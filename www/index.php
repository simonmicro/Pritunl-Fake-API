<?php
// Author: simonmicro 2023

// Config
$minVersionNumber = 1003235044068;
$minVersionName = '1.32.3504.68';
$minVersionIgnored = false; // use this to ignore the min version check - used for the public endpoint to avoid breaking too many clients at once
$licenseCosts = 42; // insert here any price you want - "0" is a special value, which also breaks the UI ;)

header('Access-Control-Allow-Origin: *'); //Allow access from everywhere...
$code = 200; // Assuming everything is fine for now

// Check if a ".ignoreMinVersion" file exists
if(!$minVersionIgnored && file_exists('.ignoreMinVersion')) {
    $minVersionIgnored = true; // If so, we ignore the min version check
}

// Parse body (if possible)
$body = json_decode(file_get_contents('php://input'));
$clientVersion = isset($body->version) ? $body->version : null;

// Fake API
$result = null;
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    $result = array('error_msg' => 'This API only supports PHP 8 or higher.');
    $code = 500;
} else if(isset($_GET['path'])) {
    $path = trim($_GET['path'], ' /');
    $pathParts = explode('/', $path);
    if(count($pathParts) > 0 && $pathParts[0] == 'healthz') {
        $result = 'OK';
    } else if(count($pathParts) > 0 && $pathParts[0] == 'notification') {
        // Any notification/[version] will be answered here
        $msg = 'Fake API endpoint for v' . $minVersionName . ' active and reachable (contacted at ' . date('r') . ').';
        if(intval($pathParts[1]) < $minVersionNumber) {
            $msg .= ' Please update your Pritunl instance to a newer version as this endpoint may not compatible anymore.';
        }
        $result = array(
            'message' => $msg,
            'vpn' => false, // idk
            'www' => false // idk
        );
    } else if(count($pathParts) > 0 && $pathParts[0] == 'auth') {
        $result = array('error_msg' => 'Sorry, but SSO is currently not supported.');
        $code = 401; // Let Pritunl fail, without 500 codes (it will show 405)
    } else if(count($pathParts) > 0 && $pathParts[0] == 'ykwyhd') {
        // The "you-know-what-you-have-done" endpoint -> used as dummy url target
        $result = array('detail' => 'You know what you have done.');
    } else if(!$minVersionIgnored && $clientVersion != null && $clientVersion < $minVersionNumber) {
        // Check if the instance is too old for us (for now following operators)
        $result = array('error_msg' => 'This API supports v' . $minVersionName . ' (' . $minVersionNumber . ') or higher.');
        $code = 473;
    } else if(count($pathParts) > 0 && $pathParts[0] == 'subscription') {
        // The following only works with the body containing the desired license
        if(isset($body->license)) {
            $license = null;
            $user = md5(base64_encode($body->license));
            $url_key = substr($user, 0, 8);
            $input = strtolower($body->license);

            // The stylesheet determines what is shown on the dashboard (and by the plan).
            $stylesheet = '';
            if(str_contains($input, 'premium')) {
                $license = 'premium';
                $stylesheet = file_get_contents('premium.css');
                // No need to install the user license "id" into CSS class, as that file only contains custom patches
            } else if(str_contains($input, 'enterprise')) {
                $license = 'enterprise';
                $stylesheet = file_get_contents('enterprise.css');
                $stylesheet = preg_replace('/(\.enterprise)([\.\ ])/', '$1-'.$url_key.'$2', $stylesheet); // Install user license "id" into CSS class
            } else if(str_contains($input, 'ultimate')) {
                $license = 'enterprise_plus';
                $stylesheet = file_get_contents('enterprise_plus.css');
                $stylesheet = preg_replace('/(\.enterprise-plus)([\.\ ])/', '$1-'.$url_key.'$2', $stylesheet); // Install user license "id" into CSS class
            }
            $stylesheet .= "\n/* custom.css */\n";
            $stylesheet .= str_replace('BACKGROUND_IMAGE_URI', "https://" . $_SERVER['HTTP_HOST'] . "/logo.png", file_get_contents('custom.css'));
            $stylesheet .= "\n/* Generated for $license license */";

            $state = null;
            if($license) { // The following only makes sense if you selected any license
                if(str_starts_with($input, 'bad')) {
                    $state = 'Bad';
                } else if(str_starts_with($input, 'canceled')) {
                    $state = 'canceled';
                }  else if(str_starts_with($input, 'active')) {
                    $state = 'Active';
                }
            }

            if($state == 'Active') {
                $result = array(
                    'active' => true, // if the sub is not active, the css won't use the LICENSE-subscription_id pattern
                    'status' => $state,
                    'plan' => $license,
                    'url_key' => $user,
                    'quantity' => 42,
                    'amount' => $licenseCosts,
                    'credit' => 42,
                    'period_end' => false,
                    'trial_end' => false,
                    'cancel_at_period_end' => false,
                    'premium_buy_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/ykwyhd/',
                    'enterprise_buy_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/ykwyhd/',
                    'portal_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/ykwyhd/',
                    'styles' => array(
                        'etag' => null, // the resource is NOT encrypted
                        'last_modified' => time(),
                        'data' => $stylesheet
                    )
                );
            } else if($state == 'Canceled') {
                $result = array(
                    'active' => false, // Here we can savely disable any style
                    'status' => $state,
                    'plan' => $license,
                    'quantity' => 42,
                    'amount' => 42,
                    'period_end' => false,
                    'trial_end' => false,
                    'cancel_at_period_end' => false,
                    'styles' => array(
                        'etag' => null,
                        'last_modified' => null,
                        'data' => null
                    )
                );
            } else if($state == 'Bad' || $state == null) {
                $code = 470; // -> bad license
                // Do not mention "canceled" in "error_msg", as it is somewhat useless (same as bad)...
                $result = array(
                    'error' => 'license_invalid',
                    'error_msg' => $state == null ? 'Unknown command. Use ["bad" | "active"] ["premium" | "enterprise" | "ultimate"].' : 'As you wish.',
                    'active' => false,
                    'status' => null,
                    'plan' => null,
                    'quantity' => null,
                    'amount' => null,
                    'period_end' => null,
                    'trial_end' => null,
                    'cancel_at_period_end' => null,
                    'styles' => array(
                        'etag' => null,
                        'last_modified' => null,
                        'data' => null
                    )
                );
            }
        } else {
            $result = array('error_msg' => 'Missing license in body.');
            $code = 401;
        }
    } else if(count($pathParts) > 0 && $pathParts[0] == 'checkout') {
        $result = array(
            'zipCode' => false,
            'allowRememberMe' => false,
            'image' => 'https://' . $_SERVER['HTTP_HOST'] . '/logo.png',
            'key' => null, // Insert here a key to unlock the stripe store (is a string). And buy the subscription...
            'plans' => array(
                'premium' => array(
                    'amount' => $licenseCosts
                ),
                'enterprise' => array(
                    'amount' => $licenseCosts
                ),
                'enterprise_plus' => array(
                    'amount' => $licenseCosts
                )
            )
        );
    }
}

header('Content-Type: application/json');
http_response_code($code);
echo json_encode($result);

// Should we log any request? Used for the development and debugging of this API
if(false) {
    // Log request
    file_put_contents('access.log', "\n" . date('r') . ":\n" . json_encode(array('head' => getallheaders(), 'body' => file_get_contents('php://input'), 'get' => $_GET, 'post' => $_POST, 'answer_code' => $code, 'answer' => $result)) . "\n", FILE_APPEND);

    // GET operator to clear log file
    if(isset($_GET['clear']))
        file_put_contents('access.log', '');
}
?>
