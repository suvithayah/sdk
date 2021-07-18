<?php

class OAuth {

    public static $STATE = "fdzefzefze";

    public static function handleLogin()
    {
        // http://.../auth?response_type=code&client_id=...&scope=...&state=...
        echo "<h1>Login with OAUTH</h1>";
        echo ServerProvider::getPath();
        echo FacebookProvider::getPath();
        echo DiscordProvider::getPath();
    }

    // Handle error general
    static function handleError()
    {
        $state = $_GET["state"];
        echo "{$state} : Request cancelled";
    }

    public static function getState()
    {
        return self::$STATE;
    }

}