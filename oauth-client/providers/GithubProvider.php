<?php 

class GithubProvider {

    public $path;

    public static $redirect_uri = "https://localhost/auth-github";
    public static $scope = "";
    public static $client_id = "";
    public static $client_secret = "";
    public static $link = "https://github.com/login/oauth/authorize";
    public static $scopes = "read:user";
    

    // path to connect
    public static function getPath()
    {
        return "<a href=" . self::$link . "?"
            . "&client_id=" . self:: $client_id
            . "&scope=" . self::$scopes
            . "&state=" . OAuth::getState()
            . "&redirect_uri=" . self::$redirect_uri . ">Se connecter avec Github</a>";
    }    

    /**
     * Send a POST request without using PHP's curl functions.
     *
     * @param string $url The URL you are sending the POST request to.
     * @param array $postVars Associative array containing POST values.
     * @return string The output response.
     * @throws Exception If the request fails.
     */
    static function post($url, $postVars = array())
    {
        //Transform vars array into a URL-encoded query string.
        $postStr = http_build_query($postVars);

        //Create an $options array that can be passed into stream_context_create.
        $options = array(
            'http' =>
            array(
                'method'  => 'POST', //We are using the POST HTTP method.
                // 'header'  => 'Content-type: application/json',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postStr //Our URL-encoded query string.
            )
        );
        //Pass our $options array into stream_context_create.
        //This will return a stream context resource.
        $streamContext  = stream_context_create($options);

        //Use PHP's file_get_contents function to carry out the request.
        //We pass the $streamContext variable in as a third parameter.
        $result = file_get_contents($url, false, $streamContext);
        
        //If $result is FALSE, then the request has failed.
        if ($result === false) {
            //If the request failed, throw an Exception containing
            //the error.
            $error = error_get_last();
            throw new Exception('POST request failed: ' . $error['message']);
        }

        //If everything went OK, return the response.
        echo "here";

        echo "<pre>";
        echo "<br>";
        $param = [];
        foreach (explode('&', $result) as $chunk) {
            $key = explode("=", $chunk)[0];
            $param[$key] = explode("=", $chunk)[1];
        }
        print_r($param);
        // die();
        self::getUser("https://api.github.com/user", $param['access_token']);
        // return $result;
    }

    static function getUser($url, $token)
    {
        //The URL that we want to GET.
        $url = $url;

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Authorization: token ' . $token,
                    'User-Agent: request'
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