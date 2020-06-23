<?php
defined('PHPFOX') or exit('NO DICE!');

require_once __DIR__ . PHPFOX_DS . '..' . PHPFOX_DS . 'libs' . PHPFOX_DS . 'linkedin.php';


class Socialbridge_Service_Provider_Linkedin extends SocialBridge_Service_Provider_Abstract
{

    protected $_name = 'linkedin';

    public function getProfile()
    {
        if (null == $this->_profile) {
            $oLinkedIn = $this->getApi();

            $arr_data = $oLinkedIn->fetch('/v2/me',[
                'projection' => '(id,firstName,lastName,profilePicture(displayImage~:playableStreams))'
            ]);
            $email = $oLinkedIn->fetch('/v2/emailAddress',[
                'q' => 'members',
                'projection' => '(elements*(handle~))'
            ]);
            $me = [];

            $me['identity'] = $arr_data['id'];
            $me['first_name'] = $arr_data['firstName']['localized'][$arr_data['firstName']['preferredLocale']['language']. '_' . $arr_data['firstName']['preferredLocale']['country']];
            $me['last_name'] = $arr_data['lastName']['localized'][$arr_data['lastName']['preferredLocale']['language']. '_' . $arr_data['lastName']['preferredLocale']['country']];
            $me['full_name'] = trim($me['first_name'] . ' ' .$me['last_name']);
            $me['user_name'] = preg_replace("#(\W+)#", '', $arr_data['full_name']);
            $me['img_url']  = '';
            $pictureArray = !empty($arr_data['profilePicture']['displayImage~']['elements'][0]) ? $arr_data['profilePicture']['displayImage~']['elements'][0] : null;
            if (!empty($pictureArray)) {
                $me['img_url'] = $pictureArray['identifiers'][0]['identifier'];
            }
            $me['email'] = !empty($email['elements'][0]['handle~']['emailAddress']) ? $email['elements'][0]['handle~']['emailAddress'] : '';
            $me['service'] = 'linkedin';

            $this->_profile = $me;
        }

        return $this->_profile;
    }

    /**
     * get api object
     * @return LinkedInSBYN
     */
    public function getApi()
    {
        if (null == $this->_api) {

            $config = $this->getSetting();

            $config['appKey'] = $config['api_key'];
            $config['appSecret'] = $config['secret_key'];
            $config['callbackUrl'] = Phpfox::getService('socialbridge')->getStaticPath() . 'module/socialbridge/static/php/linkedin.php?lResponse=1';

            // $this -> _api = new LinkedInSBYN($config);
            $this->_api = new linkedin_API($config['api_key'], $config['secret_key'], $config['callbackUrl']);

            list($token, $profile) = $this->getTokenData();

            if ($token) {
                $this->_api->setAccessToken($token);
                $this->_profile = $profile;
            }
        }

        return $this->_api;
    }

    /**
     * get list of conntected twitters friends of current viewer
     * alias to get contacts
     * @TODO get a large of contact etc: 100,000 followers
     * @param int $iPage OPTIONAL DEFAULT  = 1
     * @param int $iLimit OPTIONAL DEFAULT = 50
     * @return array
     */
    public function getFriends($iPage = 1, $iLimit = 50)
    {
        return $this->getContacts($iPage, $iLimit);
    }

    /**
     * get list of facebook friends of current viewer
     * @param int $iPage OPTIONAL DEFAULT  = 1
     * @param int $iLimit OPTIONAL DEFAULT = 50
     * @return array
     */
    public function getContacts($iPage = 1, $iLimit = 50)
    {
        $iCnt = 0;
        $iCountInvited = 0;
        $aRows = $aMails = array();
        $aInviteList = $aJoineds = $aInvalids = $aErrors = array();

        $this->getProfile();

        $aContacts = array();

        $oLinkedIn = $this->getApi();

        // RETURN THE CONTENTS OF THE CALL
        // $response = $oLinkedIn -> connections();
        $response = $oLinkedIn->fetch("/v1/people/~/connections");

        /**
         * @TODO process when failed.
         */
//        if ($response['success'] == false) {
//
//        }

        $data = json_decode(json_encode(simplexml_load_string($response['linkedin'])), 1);

        if ($data['@attributes']['total'] <= 0) {

        } else {
            $aInviteds = Phpfox::getService('contactimporter')->getInviteds();

            unset($_SESSION['yncontactimporter']['linkedin']);

            if (isset($data['person']['id'])) {
                $item = $data['person'];

                if (!Phpfox::getService('contactimporter')->checkInviteIdExist($item['id'], Phpfox::getUserId(),
                    'linkedin')) {
                    $_SESSION['yncontactimporter']['linkedin'][] = $item['id'];
                    $aContacts[] = array(
                        'id' => $item['id'],
                        'name' => sprintf('%s %s', $item['first-name'], $item['last-name']),
                        'pic' => isset($item['picture-url']) ? $item['picture-url'] : '',
                        'headline' => isset($item['headline']) ? $item['headline'] : '',
                    );
                }
            } else {
                foreach ($data['person'] as $item) {
                    if (!Phpfox::getService('contactimporter')->checkInviteIdExist($item['id'], Phpfox::getUserId(),
                        'linkedin')) {
                        $_SESSION['yncontactimporter']['linkedin'][] = $item['id'];
                        $aContacts[] = array(
                            'id' => $item['id'],
                            'name' => sprintf('%s %s', $item['first-name'], $item['last-name']),
                            'pic' => isset($item['picture-url']) ? $item['picture-url'] : '',
                            'headline' => isset($item['headline']) ? $item['headline'] : '',
                        );
                    }
                }
            }
        }
        $_SESSION['contactimporter']['linkedin'] = $aContacts;

        unset($_SESSION['yncontactimporter_fullValue']['linkedin']);
        $_SESSION['yncontactimporter_fullValue']['linkedin'] = $aContacts;


        $iCnt = count($aContacts);
        $iOffset = ($iPage - 1) * $iLimit;
        $aContacts = array_slice($aContacts, $iOffset, $iLimit);
        $aIds = array();
        $aInviteList = array();
        if (count($aContacts) <= 0) {
            $aErrors['contacts'] = _p('contactimporter.there_is_not_contact_in_your_account');

            if (count($data['person']) > 0 || isset($data['person']['id'])) {
                $aErrors['contacts'] = _p('contactimporter.you_have_sent_the_invitations_to_all_of_your_friends');
            }
        }
        foreach ($aContacts as $aContact) {
            $aInviteList[] = $aContact;
            $aIds[] = $aContact['id'];
        }
        $aJoineds = Phpfox::getService('contactimporter')->checkSocialJoined($aIds);
        $aInviteList = Phpfox::getService('contactimporter')->processSocialRows($aInviteList);

        return array(
            'iInvited' => $iCountInvited,
            'iCnt' => $iCnt,
            'aInviteLists' => $aInviteList,
            'aJoineds' => $aJoineds,
            'aInvalids' => $aInvalids,
            'sLinkNext' => '',
            'sLinkPrev' => '',
            'aErrors' => $aErrors,
        );
    }

    /**
     * @param int $iUserId
     * @param string $sRecipient
     * @param string $sSubject
     * @param string $sMessage
     * @param string $sLink
     * @return true|false
     * @throws Exception
     */
    public function sendInvitation($iUserId, $sRecipient, $sSubject, $sMessage, $sLink)
    {
        /**
         * be care if this network does not install contact importer
         */

        $sMessage = str_replace("{full_user_name}", Phpfox::getUserBy('full_name'), $sMessage);

        if (!Phpfox::isModule('contactimporter')) {
            return false;
        }

        $aProfile = $this->getProfile();

        $sHost = Phpfox::getParam('core.host');

        $sSubject = $aProfile['full_name'] . ' ' . _p('contactimporter.is_inviting_you_to') . ' ' . Phpfox::getParam('core.site_title');

        $sBody = $aProfile['full_name'] . ' ' . _p('contactimporter.is_inviting_you_to') . ' ' . Phpfox::getParam('core.site_title') . "\n\r " . _p('contactimporter.to_join_please_follow_the_link') . "\n\r " . $sLink . "\n\r " . _p('contactimporter.message') . ": " . $sMessage;

        $api = $this->getApi();

        try {
            $result = $api->message(array($sRecipient), $sSubject, $sBody);

            return $this->generateResult($result, $result['success']);
        } catch (Exception $e) {
            return $this->generateResult($e->getMessage(), false);
        }
        // return $api -> message(array($sRecipient), $sSubject, $sBody);
    }

    public function post($aVals)
    {
        $oLinkedIn = $this->getApi();

        $submittedUrl = Phpfox::getService('socialbridge')->getStaticPath() . 'module/socialbridge/static/php/redirect.php?url=' . base64_encode($aVals['url']);

        $aVals['status'] = preg_replace(array('/\[x=\d+\]/', '/\[\/x\]/'), '', $aVals['status']);
        $aVals['content'] = preg_replace(array('/\[x=\d+\]/', '/\[\/x\]/'), '', $aVals['content']);

        $aContent = array(
            'title' => html_entity_decode($aVals['status'], ENT_COMPAT, "UTF-8"),
            'submitted-url' => $submittedUrl,
            'description' => html_entity_decode($aVals['content'], ENT_COMPAT, "UTF-8")
        );

        if (strlen($aContent['title']) >= 200) {
            $aContent['title'] = substr($aContent['title'], 0, 180) . '...';
        }
        if (isset($aVals['img']) && !empty($aVals['img'])) {
            $aContent['submitted-image-url'] = $aVals['img'];
        }

        $aResponse = $oLinkedIn->share($aContent, false);
        $aResponse['apipublisher'] = 'linkedin';

            return $aResponse;
    }

    public function getFeeds($iLasFeedTimestamp, $iLimit, $iPage, $sIdentity, $iUserId = null, $type = 'friend_me')
    {
        if ((int)$iLasFeedTimestamp > 0) {
            // https://developer.linkedin.com/documents/get-network-updates-and-statistics-api
            $iLasFeedTimestamp = $iLasFeedTimestamp + 100000;
        }

        // init
        $result = array();
        $oLinkedIn = $this->getApi();

        $sCount = '';
        if ((int)$iLimit > 0) {
            $sCount = '&count=' . (int)$iLimit;
        }

        // process
        if ($type != 'me') {
            //Connection Updates
            $fields = '?type=CONN' . $sCount;
            if ((int)$iLasFeedTimestamp > 0) {
                $fields .= '&after=' . ((int)$iLasFeedTimestamp + 1);
            }
            $response = $oLinkedIn->updates($fields);
            if ($response['success']) {
                $activities = new SimpleXMLElement($response['linkedin']);
                $activities = (array)$activities;
                if ($activities['@attributes']['total'] == 0) {
                    $updates = array();
                } else {
                    if ((int)$iLimit == 1 || $activities['@attributes']['total'] == 1) {
                        $updates = array('0' => (array)$activities['update']);
                    } else {
                        $updates = (array)@$activities['update'];
                    }
                }

                foreach ($updates as $update) {
                    $update = (array)$update;
                    $result[$update['timestamp']] = $update;
                }
            }

            //Joined a Group
            $fields = '?type=JGRP' . $sCount;
            if ((int)$iLasFeedTimestamp > 0) {
                $fields .= '&after=' . ((int)$iLasFeedTimestamp + 1);
            }
            $response = $oLinkedIn->updates($fields);
            if ($response['success']) {
                $activities = new SimpleXMLElement($response['linkedin']);
                $activities = (array)$activities;
                if ($activities['@attributes']['total'] == 0) {
                    $updates = array();
                } else {
                    if ((int)$iLimit == 1 || $activities['@attributes']['total'] == 1) {
                        $updates = array('0' => (array)$activities['update']);
                    } else {
                        $updates = (array)@$activities['update'];
                    }
                }

                foreach ($updates as $update) {
                    $update = (array)$update;
                    $result[$update['timestamp']] = $update;
                }
            }

            //Shared item
            $fields = '?type=SHAR' . $sCount;
            if ((int)$iLasFeedTimestamp > 0) {
                $fields .= '&after=' . ((int)$iLasFeedTimestamp + 1);
            }
            $response = $oLinkedIn->updates($fields);
            if ($response['success']) {
                $activities = new SimpleXMLElement($response['linkedin']);
                $activities = (array)$activities;
                if ($activities['@attributes']['total'] == 0) {
                    $updates = array();
                } else {
                    if ((int)$iLimit == 1 || $activities['@attributes']['total'] == 1) {
                        $updates = array('0' => (array)$activities['update']);
                    } else {
                        $updates = (array)@$activities['update'];
                    }
                }

                foreach ($updates as $update) {
                    $update = (array)$update;
                    $result[$update['timestamp']] = $update;
                }
            }

            //All updates
            $fields = '';
            if ((int)$iLasFeedTimestamp > 0) {
                $fields .= '?after=' . (int)$iLasFeedTimestamp;
            }
            $fields .= $sCount;
            $response = $oLinkedIn->updates($fields);
            if ($response['success']) {
                $activities = new SimpleXMLElement($response['linkedin']);
                $activities = (array)$activities;
                if ($activities['@attributes']['total'] == 0) {
                    $updates = array();
                } else {
                    if ((int)$iLimit == 1 || $activities['@attributes']['total'] == 1) {
                        $updates = array('0' => (array)$activities['update']);
                    } else {
                        $updates = (array)@$activities['update'];
                    }
                }
                foreach ($updates as $update) {
                    $update = (array)$update;
                    $result[$update['timestamp']] = $update;
                }
            }
        }

        if ($type != 'friend') {
            //get self
            $fields = '?scope=self' . $sCount;
            if ((int)$iLasFeedTimestamp > 0) {
                $fields .= '&after=' . ((int)$iLasFeedTimestamp + 1);
            }
            $response = $oLinkedIn->updates($fields);
            if ($response['success']) {
                $activities = new SimpleXMLElement($response['linkedin']);
                $activities = (array)$activities;
                if ($activities['@attributes']['total'] == 0) {
                    $updates = array();
                } else {
                    if ((int)$iLimit == 1 || $activities['@attributes']['total'] == 1) {
                        $updates = array('0' => (array)$activities['update']);
                    } else {
                        $updates = (array)@$activities['update'];
                    }
                }

                foreach ($updates as $update) {
                    $update = (array)$update;
                    $result[$update['timestamp']] = $update;
                }
            }
        }

        if ((int)$iLasFeedTimestamp > 0) {
            ksort($result);
            $result = array_slice($result, 0, $iLimit);
        } else {
            krsort($result);
            $result = array_slice($result, 0, $iLimit);
            krsort($result);
        }

        // end
        return $result;
    }
}
