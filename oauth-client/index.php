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
                                    'redirect_uri' => 'https://localhost/authSuccess-Discord',
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
            'client_secret' => 'c9f1d14c4d81c07c64068f443358d45b32caeeb0',
            'redirect_uri' => 'https://localhost/authSuccess-Github',
        ],

        'userInfo' => [
            'User-Agent' => 'request',
        ],
    ]
);
$facebookProvider = new OAuth(
    "Facebook",
    [
        'oauth' => "https://www.facebook.com/v2.10/dialog/oauth",
        'accessToken' => "https://graph.facebook.com/oauth/access_token",
        'accessUserInfo' => "https://graph.facebook.com/me?fields=id,name,email",
    ],
    [
        'oauth' => [
            'client_id' => '362381462140640',
            'scope' => 'email',
            'redirect_uri' => 'https://localhost/authSuccess-Facebook',
        ],

        'accessToken' => [
            'grant_type' => 'authorization_code',
            'client_id' => '362381462140640',
            'client_secret' => '57ebc1a1f59dd076110c21041d6e1038',
            'redirect_uri' => 'https://localhost/authSuccess-Facebook',
        ],

        'userInfo' => [
            'User-Agent' => 'request',
        ],
    ]
);

$route = strtok($_SERVER["REQUEST_URI"], "?");
switch ($route) {

    case '/login':
        echo "<h1>Veuillez choisir un provider</h1>";
        $discordProvider->getPathOAuth();
        $githubProvider->getPathOAuth();
        $facebookProvider->getPathOAuth();
        break;
    case '/authSuccess-' . $discordProvider->appName :
        $discordProvider->getAccessToken();
        break;

    case '/authSuccess-' . $githubProvider->appName:
        $githubProvider->getAccessToken();
        break;
    case '/authSuccess-' . $facebookProvider->appName:
        $facebookProvider->getAccessToken();
        break;
    case '/auth-success':
        break;
    case '/auth-cancel':
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
