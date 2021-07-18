<?php

require_once('OAuth.php');

// Providers
require_once('providers/FacebookProvider.php');
require_once('providers/ServerProvider.php');
require_once('providers/DiscordProvider.php');
require_once('providers/GithubProvider.php');


/**
 * AUTH CODE WORKFLOW
 * => Generate link (/login)
 * => Get Code (/auth-success)
 * => Exchange Code <> Token (/auth-success)
 * => Exchange Token <> User info (/auth-success)
 */
$route = strtok($_SERVER["REQUEST_URI"], "?");
switch ($route) {

    case '/login':
        OAUth::handleLogin();
        break;
    case '/auth-success':
        ServerProvider::handleSuccess();
        break;
    case '/fbauth-success':
        FacebookProvider::handleFbSuccess();
        break;
    case '/discordAuth-success';
        DiscordProvider::handleDiscordSuccess();
        break;
    case '/auth-cancel':
        OAuth::handleError();
        break;

    case '/auth-github':
        $vars = array(
            'redirect_uri' => GithubProvider::$redirect_uri,
            'client_id' => GithubProvider::$client_id,
            'client_secret' => GithubProvider::$client_secret,
            'code' =>  $_GET["code"],
        );
        GithubProvider::post("https://github.com/login/oauth/access_token", $vars);
        break;
    case '/auth-bitly-success':
            $vars = array(
                'redirect_uri' => SpotifyProvider::$redirect_uri,
                'client_id' => SpotifyProvider::$client_id,
                'client_secret' => SpotifyProvider::$client_secret,
                'code' =>  $_GET["code"],
            );
            SpotifyProvider::post("https://api-ssl.bitly.com/oauth/access_token", $vars);
        break;
    case '/password':
        if ($_SERVER['REQUEST_METHOD'] === "GET") {
            echo '<form method="POST">';
            echo '<input name="username">';
            echo '<input name="password">';
            echo '<input type="submit" value="Submit">';
            echo '</form>';
        } else {
            $username = $_POST['username'];
            $password = $_POST['password'];
            ServerProvider::getUser([
                'grant_type' => "password",
                "username" => $username,
                "password" => $password
            ]);
        }
        break;
    default:
        http_response_code(404);
        break;
}
