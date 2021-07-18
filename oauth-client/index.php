<?php
session_start();
if(!isset($_SESSION['state']))
    $_SESSION['state'] = bin2hex(random_bytes(8));
require_once('OAuth.php');

$discordProvider = new OAuth("Discord",
                            [
                                'oauth' => "https://discord.com/api/oauth2/authorize",
                                'accessToken' => "https://discord.com/api/oauth2/token",
                                'accessUserInfo' => "https://discord.com/api/oauth2/@me",
                            ],
                            [
                                'oauth' => [
                                    'client_id' => '866400383328321546',
                                    'scope' => 'email',
                                    'redirect_uri' => 'https://localhost/authSuccess-Discord',
                                    'response_type' => 'code',
                                ],

                                'accessToken' => [
                                    'client_id' => '866400383328321546',
                                    'client_secret' => 'qH1dGLgmQ0aAR_gEXPZVDksRY6KkvxEs',
                                    'grant_type' => 'authorization_code',
                                    'redirect_uri' => 'https://localhost/authSuccess',
                                ],
                            ]);

$githubProvider = new OAuth(
    "Github",
    [
        'oauth' => "https://github.com/login/oauth/authorize",
        'accessToken' => "https://github.com/login/oauth/access_token",
        'accessUserInfo' => "https://api.github.com/user",
    ],
    [
        'oauth' => [
            'client_id' => '320951b103100045cae5',
            'scope' => 'read:user',
            'redirect_uri' => 'https://localhost/authSuccess-Github',
        ],

        'accessToken' => [
            'client_id' => '320951b103100045cae5',
            'client_secret' => '4794afd3d597077da25a980d6aeb0f92a91494b1',
            'redirect_uri' => 'https://localhost/authSuccess',
        ],
    ]
);

/**
 * AUTH CODE WORKFLOW
 * => Generate link (/login)
 * => Get Code (/auth-success)
 * => Exchange Code <> Token (/auth-success)
 * => Exchange Token <> User info (/auth-success)
 */
/* switch ($route) {

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
} */

$route = strtok($_SERVER["REQUEST_URI"], "?");
switch ($route) {

    case '/login':
        $discordProvider->getPathOAuth();
        $githubProvider->getPathOAuth();
        break;
    case '/authSuccess-' . $discordProvider->appName :
        $discordProvider->getAccessToken();
        break;

    case '/authSuccess-' . $githubProvider->appName:
        $githubProvider->getAccessToken();
        break;
    default:
        http_response_code(404);
        break;
} 
