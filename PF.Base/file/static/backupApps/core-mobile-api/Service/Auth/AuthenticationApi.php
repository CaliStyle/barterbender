<?php
/**
 * Created by PhpStorm.
 * User: pro
 * Date: 22/5/18
 * Time: 11:30 AM
 */

namespace Apps\Core_MobileApi\Service\Auth;

use Apps\Core_MobileApi\Api\Exception\ErrorException;
use Apps\Core_MobileApi\Api\Exception\NotFoundErrorException;
use Apps\Core_MobileApi\Api\Exception\PaymentRequiredErrorException;
use Apps\Core_MobileApi\Api\Exception\PermissionErrorException;
use Apps\Core_MobileApi\Api\Exception\UnauthorizedErrorException;
use Apps\Core_MobileApi\Api\Exception\UnknownErrorException;
use Apps\Core_MobileApi\Api\Exception\ValidationErrorException;
use Apps\Core_MobileApi\Api\Resource\UserResource;
use Apps\Core_MobileApi\Service\AbstractApi;
use Apps\Core_MobileApi\Service\Auth\GrantType\AppleAuth;
use Apps\Core_MobileApi\Service\Auth\GrantType\FacebookAuth;
use Apps\Core_MobileApi\Service\Auth\GrantType\UserPasswordAuth;
use Phpfox;


class AuthenticationApi extends AbstractApi
{

    public function __naming()
    {
        return [
            'verify-token' => [
                'get' => 'verifyToken'
            ]
        ];
    }

    public function verifyToken($params = [])
    {
        $user = Phpfox::getService("user")->get(Phpfox::getUserId());
        if (!empty($user)) {
            return $this->success(UserResource::populate($user)->toArray());
        } else {
            return $this->notFoundError();
        }
    }

    public function setUserFromToken($token)
    {
        $token = \Phpfox::getService('mobile.auth.storage')->getAccessToken($token);
        if (!empty($token) && !empty($token['user_id']) && ($user = Phpfox::getService("user")->get($token['user_id']))) {
            /** @var \User_Service_Auth $auth */
            $auth = Phpfox::getService("user.auth");
            $auth->setUser($user);
            //Set cookie
            $sPasswordHash = Phpfox::getLib('hash')->setRandomHash(Phpfox::getLib('hash')->setHash($user['password'], $user['password_salt']));
            $iTime = 0;
            $cookieUserId = 'user_id';
            $cookieUserHash = 'user_hash';
            if (Phpfox::getParam('core.use_custom_cookie_names')) {
                $cookieUserId = md5(Phpfox::getParam('core.custom_cookie_names_hash') . $cookieUserId);
                $cookieUserHash = md5(Phpfox::getParam('core.custom_cookie_names_hash') . $cookieUserHash);
            }
            Phpfox::setCookie($cookieUserId, $user['user_id'], $iTime, (Phpfox::getParam('core.force_https_secure_pages') ? true : false));
            Phpfox::setCookie($cookieUserHash, $sPasswordHash, $iTime, (Phpfox::getParam('core.force_https_secure_pages') ? true : false));

        }
    }

    public function handleTokenRequest()
    {
        try {
            \OAuth2\Autoloader::register();

            $storage = \Phpfox::getService('mobile.auth.storage');
            $server = new \OAuth2\Server($storage, [
                'allow_implicit'  => true,
                'access_lifetime' => 86400 * 30,
                'enforce_state'   => false
            ]);

            $server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));
            $server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));
            $server->addGrantType(new UserPasswordAuth($storage));
            $server->addGrantType(new FacebookAuth($storage));
            $server->addGrantType(new AppleAuth($storage));
            $server->addGrantType(new \OAuth2\GrantType\RefreshToken($storage, [
                'always_issue_new_refresh_token' => true
            ]));

            $request = \OAuth2\Request::createFromGlobals();
            $response = $server->handleTokenRequest($request);


            if (!($error = $response->getParameter('error')) && ($token = $response->getParameter('access_token'))) {
                return $this->response([
                    'access_token'  => $token,
                    'expires_in'    => 86400 * 30,
                    'token_type'    => $response->getParameter('token_type'),
                    'scope'         => $response->getParameter('scope'),
                    'refresh_token' => $response->getParameter('refresh_token')
                ]);
            } else {
                return $this->response([
                    'error' => $this->error(
                        $response->getParameter('error_description'),
                        false,
                        $response->getStatusCode())
                ]);
            }
        } catch (\Exception $exception) {
            return $this->response(['error' => $this->error($exception->getMessage())]);
        }

    }


    public function response($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    public function error($error = "", $ignoredLast = false, $code = 102)
    {
        $error = $this->getErrorException($error, $code);
        return $error->getResponse();
    }

    public function getErrorException($error, $code)
    {
        switch ($code) {
            case ErrorException::RESOURCE_NOT_FOUND:
                $error = new NotFoundErrorException($error, $code);
                break;
            case ErrorException::PERMISSION_DENIED:
                $error = new PermissionErrorException($error, $code);
                break;
            case ErrorException::UNKNOWN_ERROR:
                $error = new UnknownErrorException($error, $code);
                break;
            case ErrorException::INVALID_REQUEST_PARAMETERS:
                $error = new ValidationErrorException($error, $code);
                break;
            case ErrorException::PAYMENT_REQUIRED:
                $error = new PaymentRequiredErrorException($error, $code);
                break;
            case ErrorException::UNAUTHORIZED:
                $error = new UnauthorizedErrorException($error, $code);
                break;
            default:
                $error = new ErrorException($error, $code);
                break;
        }
        return $error;
    }
}