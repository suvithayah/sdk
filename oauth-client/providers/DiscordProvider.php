<?php 

class DiscordProvider {

    public $path;

    public static $CLIENT_ID = "866035109001560064";
    public static $CLIENT_SECRET = "dhzJ2sjNiWxZiGJiKfl6dmocTeYostR1";

    // path to connect
    public static function getPath()
    {
        return "<br><a href='https://discord.com/api/oauth2/authorize?response_type=code"
            . "&client_id=".self::$CLIENT_ID
            . "&scope=identify%20guilds.join"
            . "&state=".OAuth::getState()
            . "&redirect_uri=".urlencode('https://localhost/discordAuth-success')
            . "&prompt=consent'> Se connecter avec Discord</a>";
    }
    // Handle Success Discord Side
    static function handleDiscordSuccess()
    {
        $state = $_GET['state'];
        $code = $_GET['code'];
        if ($state !== OAuth::getState()) {
            throw new RuntimeException("{$state} : invalid state");
        }

        $url = "https://discord.com/api/oauth2/token?";

        $data = array(
            'client_id' => self::$CLIENT_ID,
            'client_secret' => self::$CLIENT_SECRET,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => ('https://localhost/discordAuth-success')
        );


        $options = array(
            'http' => array(
                'header' => "Content-Type: application/x-www-form-urlencoded",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            http_response_code(404);
        } else {
            $param = json_decode($result);
            self::getUser("https://discord.com/api/oauth2/@me", $param->access_token);
        }
    }


    static function getUser($url, $token)
    {
        //The URL that we want to GET.
        $url = $url;

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Authorization: Bearer ' . $token
                ]
            ]
        ]);

        //Use file_get_contents to GET the URL in question.
        $contents = file_get_contents($url, false, $context);
        echo "GetUser";

        //If $contents is not a boolean FALSE value.
        if ($contents !== false) {
            //Print out the contents.
            print_r($contents);
        }
    }
}