<?php 

class FacebookProvider {

    public $path;

    public static $CLIENT_FBID = "3648086378647793";
    public static $CLIENT_FBSECRET = "1b5d764e7a527c2b816259f575a59942";

    // path to connect
    public static function getPath()
    {
        return "<a href='https://www.facebook.com/v2.10/dialog/oauth?response_type=code"
            . "&client_id=" . self::$CLIENT_FBID
            . "&scope=email"
            . "&state=" . OAuth::getState()
            . "&redirect_uri=https://localhost/fbauth-success'>Se connecter avec Facebook</a>";
    }

    // Handle Success Facebook Side
    static function handleFbSuccess()
    {
        $state = $_GET["state"];
        $code = $_GET["code"];
        if ($state !== OAuth::getState()) {
            throw new RuntimeException("{$state} : invalid state");
        }
        // https://auth-server/token?grant_type=authorization_code&code=...&client_id=..&client_secret=...
        $url = "https://graph.facebook.com/oauth/access_token?grant_type=authorization_code&code={$code}&client_id=" . self::$CLIENT_FBID . "&client_secret=" .  self::$CLIENT_FBSECRET . "&redirect_uri=https://localhost/fbauth-success";
        $result = file_get_contents($url);
        $resultDecoded = json_decode($result, true);
        $token = $resultDecoded["access_token"];
        $userUrl = "https://graph.facebook.com/me?fields=id,name,email";
        $context = stream_context_create([
            'http' => [
                'header' => 'Authorization: Bearer ' . $token
            ]
        ]);
        echo file_get_contents($userUrl, false, $context);
    }
}