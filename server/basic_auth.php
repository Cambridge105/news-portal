<?php
require_once("auth_users.php");

if (!isset($_SERVER['PHP_AUTH_USER'])) 
{
    header('WWW-Authenticate: Basic realm="C105 LocalNews"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'You are not permitted to view this page.';
    exit;
}

elseif (!array_key_exists($_SERVER['PHP_AUTH_USER'],$auth_users) || $auth_users[$_SERVER['PHP_AUTH_USER']] != $_SERVER['PHP_AUTH_PW'])
{
	header('WWW-Authenticate: Basic realm="C105 LocalNews"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'You are not permitted to view this page.';
    exit;
}
?>