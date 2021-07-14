<?php
//Author: Simon Beginn 2020

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
        $result->message = 'Fake API endpoint for v1.30.X active and reachable (contacted at ' . date('r') . ').';
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
        } else if(preg_match('/.*enterprise/', $body->license)) {
            $license = 'enterprise';
            $stylesheet .= file_get_contents('enterprise.css');
            //Now fix some too aggressive display strategies by appending their overrides...
            $stylesheet .= file_get_contents('enterprise_fix.css');
        } else if(preg_match('/.*ultimate/', $body->license)) {
            $license = 'enterprise_plus';
            //Load the new css file and change all invisible blocks to visible (this will show a little bit too much, but whatever...)
            $stylesheet .= file_get_contents('enterprise.css');
            $stylesheet = preg_replace('/(enterprise)/', '$1-temp-prefix', $stylesheet);
            $stylesheet = preg_replace('/(enterprise)(-temp-prefix-plus)/', '$1', $stylesheet);
            $stylesheet = preg_replace('/(enterprise)(-temp-prefix)/', '$1-plus', $stylesheet);
            $stylesheet = preg_replace('/(display:.?)none.?$/m', '$1inline-block', $stylesheet); //This WILL SHOW TOO MUCH... So we'll need a fix file...
            $stylesheet .= file_get_contents('ultimate_fix.css');
        }
        $stylesheet .= "* { color: rgb(57, 83, 120); }\n.dark * { color: rgb(200, 242, 242); }\n.navbar .navbar-brand { animation-name: pritunl-logo; animation-duration: 20s; animation-iteration-count: infinite; }\n@keyframes pritunl-logo { 0% { transform:rotate3d(1, 0, 0, 360deg); } 25% { transform:rotate3d(1, 0, 0, 0deg); } 50% { transform:rotate3d(0, 1, 0, 0deg); } 75% { transform:rotate3d(0, 1, 0, 360deg); } 100% { transform:rotate3d(0, 1, 0, 360deg); } }\n.footer-brand {visibility: hidden; }\n.footer-brand::before { visibility: visible; position: absolute; bottom: 0; right: 0; content: ''; background: url('https://" . $_SERVER['HTTP_HOST'] . "/logo.png'); background-size: cover; width: 1em; height: 1em; margin: 0.3em; }\n/* Generated for $license license */";

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
            $result->credit = 42;
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
            $result->error_msg = 'Unknown command. Use ["bad" | "canceled" | "active"] ["premium" | "enterprise" | "ultimate"].';
        }
    } else if(preg_match('/checkout.*/', $_GET['path'])) {
        $result = array();
        $result['zipCode'] = false;
        $result['allowRememberMe'] = false;
        $result['image'] = 'https://' . $_SERVER['HTTP_HOST'] . '/logo.png';
        $result['key'] = null; //Insert here a key to unlock the stripe store (is a string). And buy the subscription...
        $result['plans'] = array();
        $result['plans']['premium'] = array();
        $result['plans']['premium']['amount'] = 42;
        $result['plans']['enterprise'] = array();
        $result['plans']['enterprise']['amount'] = 42;
        $result['plans']['enterprise_plus'] = array();
        $result['plans']['enterprise_plus']['amount'] = 42;
    } else if(preg_match('/auth\/.*/', $_GET['path'])) {
        $result = array('error' => 'Sorry, but SSO is currently not supported.');
        $code = 401; //Let Pritunl fail, without 500 codes (it will show 405)
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
