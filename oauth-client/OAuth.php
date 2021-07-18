<?php

class OAuth {

    public $state;
    public $paths;
    public $appName;
    public $params;
    public $userInfo;

    public function __construct($appName, $paths, $params, $userInfo = null){
        $this->paths = $paths;
        $this->appName = $appName;
        $this->params = $params;
        $this->userInfo = $userInfo;
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

        if ($state !== $_SESSION['state']) {
            unset($_SESSION['state']);
            throw new RuntimeException("{$state} : invalid state");
        }

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
            unset($_SESSION['state']);

        } else {

            if(json_decode($result)){
                $param = json_decode($result);
                $token = $param->access_token;
            }
            else{
                $param = [];
                foreach (explode('&', $result) as $chunk) {
                    $key = explode("=", $chunk)[0];
                    $param[$key] = explode("=", $chunk)[1];
                }
                $token = $param['access_token'];
            }
            $this->getUser($this->paths['accessUserInfo'], $token);
            unset($_SESSION['state']);
        }

    }

    function getUser($url, $token)
    {
        //The URL that we want to GET.
        $url = $url;

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Authorization: Bearer ' . $token,
                    'User-Agent: request'

                ]
            ]
        ]);

        //Use file_get_contents to GET the URL in question.
        $contents = file_get_contents($url, false, $context);

        //If $contents is not a boolean FALSE value.
        if ($contents !== false) {
            //Print out the contents.
            print_r($contents);
        }
    }
 
}