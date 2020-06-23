<?php

namespace Apps\Core_MobileApi\Service\Auth\GrantType;

use Apps\Core_MobileApi\Service\Auth\Storage;
use Core\Hash as Hash;
use Core\Model as Model;
use OAuth2\GrantType\GrantTypeInterface;
use OAuth2\RequestInterface;
use OAuth2\ResponseInterface;
use OAuth2\ResponseType\AccessTokenInterface;
use OAuth2\Storage\UserCredentialsInterface;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

/**
 * @author Brent Shaffer <bshafs at gmail dot com>
 */
class AppleAuth extends Model implements GrantTypeInterface
{
    /**
     * @var array
     */
    private $userInfo;

    /**
     * @var UserCredentialsInterface
     */
    protected $storage;

    /**
     * @param UserCredentialsInterface $storage - REQUIRED Storage class for retrieving user credentials information
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return string
     */
    public function getQueryStringIdentifier()
    {
        return 'apple';
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return bool|mixed|null
     * @throws \Exception
     */
    public function validateRequest(RequestInterface $request, ResponseInterface $response)
    {
        $appleId = $request->request('apple_id');
        $appleEmail = $request->request('apple_email');
        $appleName = $request->request('apple_name');

        if (empty($appleId)) {
            $response->setError(500, 'invalid_request', 'Missing parameters: `apple_id`, `apple_email` and `apple_name` required');
            return null;
        }

        $cached = \storage()->get('apple_users_' . $appleId);
        if (!empty($cached) && isset($cached->value->user_id)) {
            $userInfo = db()->select('user_id, user_name, email')->from(':user')
                ->where(['user_id' => $cached->value->user_id])
                ->execute('getRow');
        } else {
            if (empty($appleEmail) || empty($appleName)) {
                $response->setError(500, 'invalid_request', _p('apple_sign_in_missing_email_error'));
                return null;
            }
            $userInfo = db()->select('user_id, user_name, email')->from(':user')
                ->where(['email' => $appleEmail])
                ->execute('getRow');

            //In case match user, save cache this apple id
            if (!empty($userInfo)) {
                \storage()->set('apple_users_' . $appleId, [
                    'user_id' => $userInfo['user_id'],
                    'email' => $appleEmail
                ]);
            }

        }

        if (empty($userInfo)) {
            if (\Phpfox::getParam('user.allow_user_registration') && (!\Phpfox::getParam('user.invite_only_community') || \Phpfox::getService('invite')->isValidInvite($appleEmail))) {
                $userInfo = $this->createUser($appleId, $appleEmail, html_entity_decode($appleName, ENT_QUOTES));
            } else {
                $response->setError(505, 'invalid_grant', _p('unable_to_retrieve_user_information'));
                return null;
            }
        }

        if (!isset($userInfo['user_id'])) {
            throw new \LogicException(_p('user_has_not_found_and_cant_sign_up'));
        }

        $this->userInfo = $userInfo;

        return true;
    }

    /**
     * Get client id
     *
     * @return mixed|null
     */
    public function getClientId()
    {
        return null;
    }

    /**
     * Get user id
     *
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userInfo['user_id'];
    }

    /**
     * Get scope
     *
     * @return null|string
     */
    public function getScope()
    {
        return isset($this->userInfo['scope']) ? $this->userInfo['scope'] : null;
    }

    /**
     * Create access token
     *
     * @param AccessTokenInterface $accessToken
     * @param mixed $client_id - client identifier related to the access token.
     * @param mixed $user_id - user id associated with the access token
     * @param string $scope - scopes to be stored in space-separated string.
     *
     * @return array
     */
    public function createAccessToken(AccessTokenInterface $accessToken, $client_id, $user_id, $scope)
    {
        return $accessToken->createAccessToken($client_id, $user_id, $scope);
    }

    public function createUser($appleID, $appleEmail, $appleName)
    {
        $url = null;

        $_password = $appleID . uniqid();
        $password = (new Hash())->make($_password);
        $iGender = 0;
        $url = null;
        $blank_email = false;

        if (!$appleEmail) {
            $appleEmail = $appleID . '@apple';
            $blank_email = true;
        }

        $id = db()->insert(':user', [
            'user_group_id' => NORMAL_USER_ID,
            'email' => $appleEmail,
            'password' => $password,
            'gender' => $iGender,
            'full_name' => $appleName,
            'user_name' => 'apple-' . $appleID,
            'user_image' => '',
            'view_id' => (!defined('PHPFOX_INSTALLER') && Phpfox::getParam('user.approve_users')) ? '1' : '0',
            'joined' => PHPFOX_TIME,
            'last_activity' => PHPFOX_TIME
        ]);

        $storage = \storage();

        //Remove existed cache
        $storage->del('apple_users_' . $appleID);

        if ($blank_email) {
            $storage->set('apple_force_email_' . $id, $appleID);
        } else {
            //Set cache to show popup notify
            $storage->set('apple_user_notice_' . $id, ['email' => $appleEmail]);
        }

        $storage->set('apple_users_' . $appleID, [
            'user_id' => $id,
            'email' => $appleEmail
        ]);

        //Storage account login by Apple but use FB cache, in the first time this user change password, he/she doesn't need confirm old password.
        $storage->set('fb_new_users_' . $id, [
            'apple_id' => $appleID,
            'email' => $appleEmail
        ]);

        $aExtras = [
            'user_id' => $id
        ];

        (($sPlugin = \Phpfox_Plugin::get('user.service_process_add_extra')) ? eval($sPlugin) : false);

        $tables = [
            'user_activity',
            'user_field',
            'user_space',
            'user_count'
        ];
        foreach ($tables as $table) {
            db()->insert(':' . $table, $aExtras);
        }

        $iFriendId = (int)Phpfox::getParam('user.on_signup_new_friend');
        if ($iFriendId > 0 && Phpfox::isModule('friend')) {
            $iCheckFriend = db()->select('COUNT(*)')
                ->from(Phpfox::getT('friend'))
                ->where('user_id = ' . (int)$id . ' AND friend_user_id = ' . (int)$iFriendId)
                ->execute('getSlaveField');

            if (!$iCheckFriend) {
                db()->insert(Phpfox::getT('friend'), [
                        'list_id' => 0,
                        'user_id' => $id,
                        'friend_user_id' => $iFriendId,
                        'time_stamp' => PHPFOX_TIME
                    ]
                );

                db()->insert(Phpfox::getT('friend'), [
                        'list_id' => 0,
                        'user_id' => $iFriendId,
                        'friend_user_id' => $id,
                        'time_stamp' => PHPFOX_TIME
                    ]
                );

                if (!Phpfox::getParam('user.approve_users')) {
                    Phpfox::getService('friend.process')->updateFriendCount($id, $iFriendId);
                    Phpfox::getService('friend.process')->updateFriendCount($iFriendId, $id);
                }
            }
        }

        $iId = $id; // add for plugin use

        (($sPlugin = Phpfox_Plugin::get('user.service_process_add_end')) ? eval($sPlugin) : false);

        if (Phpfox::isAppActive('Core_Activity_Points')) {
            Phpfox::getService('activitypoint.process')->updatePoints($id, 'user_signup');
        }

        if (!defined('PHPFOX_INSTALLER') && Phpfox::isAppActive('Core_Subscriptions') && Phpfox::getParam('subscribe.enable_subscription_packages')) {
            $aPackages = Phpfox::getService('subscribe')->getPackages(true);
            if (count($aPackages)) {
                //Get first package
                $aPackage = $aPackages[0];

                $iPurchaseId = Phpfox::getService('subscribe.purchase.process')->add([
                    'package_id' => $aPackage['package_id'],
                    'currency_id' => $aPackage['default_currency_id'],
                    'price' => $aPackage['default_cost']
                ], $iId);

                $iDefaultCost = (int)str_replace('.', '', $aPackage['default_cost']);

                if ($iPurchaseId) {
                    if ($iDefaultCost > 0) {
                        define('PHPFOX_MUST_PAY_FIRST', $iPurchaseId);

                        Phpfox::getService('user.field.process')->update($iId, 'subscribe_id', $iPurchaseId);
                    } else {
                        Phpfox::getService('subscribe.purchase.process')->update($iPurchaseId, $aPackage['package_id'], 'completed', $iId, $aPackage['user_group_id'], $aPackage['fail_user_group']);
                    }
                }
            }
        }
        if (!Phpfox_Error::isPassed()) {
            throw new \Exception(implode('', Phpfox_Error::get()));
        }

        return [
            'user_id' => $id,
            'email' => $appleEmail,
            'user_name' => 'apple-' . $appleID
        ];
    }
}