<?php

class OAuth {

    public $state;
    public $paths;
    public $appName;
    public $params;

    public function __construct($appName, $paths, $params){
        $this->paths = $paths;
        $this->appName = $appName;
        $this->params = $params;
    }

    public function getPathOAuth()
    {
        $path =  $this->paths['oauth'] . '?' . http_build_query($this->params['oauth']);
        echo "<a href='" . $path . "&state=" . $_SESSION['state'] . "'> Se connecter vers " . $this->appName . "</a><br>";
    }

    public function getAccessToken()
    {
       
        $state = $_GET['state'];
        $code = $_GET['code'];
        echo "<pre>";
        print_r($this->paths['accessToken']);
        echo "<br>";
        print_r($_SESSION['state']);
        echo "<br>";
        print_r($state);

        if ($state !== $_SESSION['state']) {
            unset($_SESSION['state']);
            throw new RuntimeException("{$state} : invalid state");
        }
        unset($_SESSION['state']);

        $url = $this->paths['accessToken'] . "?";

        $this->params['accessToken']['code'] = $code;
        $data = $this->params['accessToken'];

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
            echo "token";
            print_r("token");
            $this->getUser($this->paths['accessUserInfo'], $param->access_token);
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