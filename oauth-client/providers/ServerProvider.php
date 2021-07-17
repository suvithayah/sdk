<?php 

class ServerProvider {

    public static $CLIENT_ID = "client_60a3778e70ef02.05413444";
    public static $CLIENT_SECRET = "cd989e9a4b572963e23fe39dc14c22bbceda0e60";

    // Handle Success Server Side
    static function handleSuccess()
    {
        $state = $_GET["state"];
        $code = $_GET["code"];
        if ($state !== OAuth::getState()) {
            throw new RuntimeException("{$state} : invalid state");
        }

        self::getUser([
            'grant_type' => "authorization_code",
            "code" => $code,
        ]);
    }

    static function getPath()
    {
        return "<a href='http://localhost:8081/auth?response_type=code"
        . "&client_id=" . self::$CLIENT_ID
            . "&scope=basic"
            . "&state=" . OAuth::$STATE . "'>Se connecter avec Oauth Server</a><br>";
    }

    // Return User Server Side
    static function getUser($params)
    {
        $url = "http://oauth-server:8081/token?client_id=" . self::$CLIENT_ID . "&client_secret=" . self::$CLIENT_SECRET . "&" . http_build_query($params);
        $result = file_get_contents($url);
        $result = json_decode($result, true);
        $token = $result['access_token'];

        $apiUrl = "http://oauth-server:8081/me";
        $context = stream_context_create([
            'http' => [
                'header' => 'Authorization: Bearer ' . $token
            ]
        ]);
        echo file_get_contents($apiUrl, false, $context);
    }
}