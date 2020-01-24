<?php
header("Access-Control-Allow-Origin: *"); //Allow access from everywhere...
$code = 200;

//Parse body (if possible)
$body = json_decode(file_get_contents('php://input'));

//Fake API
$result = 'UNDEFINED';
if(isset($_GET['path'])) {
    if(preg_match('/notification.*/', $_GET['path'])) {
        $result = new stdClass;
        $result->message = 'Fake API endpoint active and reachable under ' . $_SERVER['HTTP_HOST'] . ' (checked at ' . date('r') . ').';
        $result->vpn = false;
        $result->www = false;
    } else if(preg_match('/subscription.*/', $_GET['path'])) {
        $result = new stdClass;
        if(isset($body->license)) {
            //premium
            //enterprise
            //enterprise plus
            $license = null;
            $stylesheet = '* { color: rgb(20, 150, 20); } .dark * { color: rgb(40, 180, 40); }';
            if(preg_match('/.*premium/', $body->license)) {
                $license = 'premium';
            } else if(preg_match('/.*enterprise[^\w]/', $body->license)) {
                $license = 'enterprise';
                $stylesheet .= file_get_contents('enterprise.css');
            } else if(preg_match('/.*enterpriseplus/', $body->license)) {
                $license = 'enterprise_plus';
                $stylesheet .= file_get_contents('enterprise_plus.css');
                $stylesheet = preg_replace('/(.*display:.?)none.*/', '$1inline-block', $stylesheet);
            }
            $stylesheet .= '/* Generated for ' . $license . ' license */';

            $state = null;
            if($license) { //The following only makes sense if you selected a license
                if(strpos($body->license, 'bad') !== false) {
                    $state = 'Bad';
                } else if(strpos($body->license, 'canceled') !== false) {
                    $state = 'canceled';
                }  else if(strpos($body->license, 'active') !== false) {
                    $state = 'Active';
                }
            }


            if($state == 'Active') {
                $result->active = $license != 'premium'; //if true the stylesheet ↓ will be activated. This will also hide some elements, so don't use it on premium users (which will have the minimal stylesheet)...
                $result->status = $state;
                $result->plan = $license;
                $result->quantity = 42;
                $result->amount = 42;
                $result->period_end = false;
                $result->trial_end = false;
                $result->cancel_at_period_end = false;
                $result->styles = new stdClass;
                $result->styles->etag = 42;
                $result->styles->last_modified = time();
                $result->styles->data = $stylesheet;
            }
            if($state == 'Canceled') {
                $result->active = false;
                $result->status = $state;
                $result->plan = $license;
                $result->quantity = 42;
                $result->amount = 42;
                $result->period_end = false;
                $result->trial_end = false;
                $result->cancel_at_period_end = false;
                $result->styles = new stdClass;
                $result->styles->etag = 42;
                $result->styles->last_modified = time();
                $result->styles->data = $stylesheet;
            }
            if($state == 'Bad' || $state == null) {
                $code = 470; //-> bad license
                $result->error_msg = 'As you wish.';
                $result->error = 'license_invalid';
                $result->active = false;
                $result->status = false;
                $result->plan = null;
                $result->quantity = 0;
                $result->amount = 0;
                $result->period_end = true;
                $result->trial_end = true;
                $result->cancel_at_period_end = null;
                $result->styles = new stdClass;
            }
            if($state == null) {
                $result->error_msg = 'Unknown command. Use ["bad" | "canceled" | "active"] ["premium" | "enterprise" | "enterpriseplus"].';
            }
        } else {
            $result = new stdClass;
            $result->ERROR = 'BAD REQUEST';
            $code = 400;
        }
    } else if(preg_match('/checkout.*/', $_GET['path'])) {
        $result = array();
        $result['zipCode'] = false;
        $result['allowRememberMe'] = false;
        $result['image'] = 'https://objectstorage.us-ashburn-1.oraclecloud.com/n/pritunl8472/b/pritunl-static/o/logo_stripe.png';
        $result['key'] = 'pk_live_plmoOl3lS3k5dMNQViZWGfVR'; //Stolen store key from official API
        $result['plans'] = array();
        $result['plans']['premium'] = array();
        $result['plans']['premium']['amount'] = 42;
        $result['plans']['enterprise'] = array();
        $result['plans']['enterprise']['amount'] = 42;
        $result['plans']['enterprise_plus'] = array();
        $result['plans']['enterprise_plus']['amount'] = 42;
    }
}

header('Content-Type: application/json');
http_response_code($code);
echo json_encode($result);

if(false) {
    //Log request
    file_put_contents('access.log', "\n" . date('r') . ":\t" . json_encode(array('head' => getallheaders(), 'body' => file_get_contents('php://input'), 'get' => $_GET, 'post' => $_POST, 'answer_code' => $code, 'answer' => $result)) . "\n", FILE_APPEND);

    //GET operator to clear log file
    if(isset($_GET['clear']))
        file_put_contents('access.log', '');
}
?>
