<?php
header("Access-Control-Allow-Origin: *"); //Allow access from everywhere...
$code = 200;

//Parse body (if possible)
$body = json_decode(file_get_contents('php://input'));

//Fake API
$result = null;
if(isset($_GET['path'])) {
    //Any notification/[version] will be answered here
    if(preg_match('/notification.*/', $_GET['path'])) {
        $result = new stdClass;
        $result->message = 'Fake API endpoint active and reachable (contacted at ' . date('r') . ').';
        $result->vpn = false; //Idk
        $result->www = false; //Idk
    } else if(isset($body->license) && preg_match('/subscription.*/', $_GET['path'])) {
        //The following only works with the body containing the desired license
        $result = new stdClass;
        $license = null;
        //The stylesheet determines what is shown on the dashboard (and by the plan). As default we change the colors of any text.
        $stylesheet = '';
        if(preg_match('/.*premium/', $body->license)) {
            $license = 'premium';
        } else if(preg_match('/.*enterprisenormal/', $body->license)) {
            $license = 'enterprise';
            $stylesheet .= file_get_contents('enterprise.css');
            //Now fix some too aggressive display strategies by appending their overrides...
            $stylesheet .= file_get_contents('enterprise_fix.css');
        } else if(preg_match('/.*enterpriseplus/', $body->license)) {
            $license = 'enterprise_plus';
            //Load the new css file and change all invisible blocks to visible (this will show a little bit too much, but whatever...)
            $stylesheet .= file_get_contents('enterprise.css');
            $stylesheet = preg_replace('/(.*display:.?)none.*/', '$1inline-block', $stylesheet);
        }
        $stylesheet .= "* { color: rgb(57, 83, 120); }\n.dark * { color: rgb(200, 242, 242); }\n.navbar .navbar-brand { transform:rotateZ(180deg); }\n.footer-brand { opacity: 0; }\n/* Generated for $license license */";

        $state = null;
        if($license) { //The following only makes sense if you selected any license
            if(strpos($body->license, 'bad') !== false) {
                $state = 'Bad';
            } else if(strpos($body->license, 'canceled') !== false) {
                $state = 'canceled';
            }  else if(strpos($body->license, 'active') !== false) {
                $state = 'Active';
            }
        }

        if($state == 'Active') {
            $result->active = $license != 'premium'; //If true the stylesheet from below will be activated. This will also hide some elements, so don't use it on premium users (which will have the minimal stylesheet and so the subscription info will be empty)...
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
            $result->active = false; //Here we can savely disable any styles
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
            $result->error_msg = 'Unknown command. Use ["bad" | "canceled" | "active"] ["premium" | "enterprisenormal" | "enterpriseplus"].';
        }
    } else if(preg_match('/checkout.*/', $_GET['path'])) {
        $result = array();
        $result['zipCode'] = false;
        $result['allowRememberMe'] = false;
        $result['image'] = $_SERVER['HTTP_HOST'] . '/logo.png';
        $result['key'] = 'pk_live_*****l3lS******QViZ*****'; //Insert here a key to unlock the store. And buy the subsciption...
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

//Should we log any request? Used for the development and debugging of this API
if(false) {
    //Log request
    file_put_contents('access.log', "\n" . date('r') . ":\t" . json_encode(array('head' => getallheaders(), 'body' => file_get_contents('php://input'), 'get' => $_GET, 'post' => $_POST, 'answer_code' => $code, 'answer' => $result)) . "\n", FILE_APPEND);

    //GET operator to clear log file
    if(isset($_GET['clear']))
        file_put_contents('access.log', '');
}
?>
