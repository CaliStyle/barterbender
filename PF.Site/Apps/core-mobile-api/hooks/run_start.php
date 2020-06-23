<?php
/**
 * Auto login user
 */

if (isset($_SERVER['x-access-token']) || isset($_SERVER['HTTP_X_ACCESS_TOKEN'])) {
    $token = (isset($_SERVER['x-access-token']) ? $_SERVER['x-access-token'] : $_SERVER['HTTP_X_ACCESS_TOKEN']);
    Phpfox::getService('mobile.auth_api')->setUserFromToken($token);
}