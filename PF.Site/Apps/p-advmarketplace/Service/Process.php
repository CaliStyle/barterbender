<?php

namespace Apps\P_AdvMarketplace\Service;

defined('PHPFOX') or exit('NO DICE!');

use Core\Request\Exception;
use Phpfox;
use Phpfox_Service;
use Phpfox_Error;
use Phpfox_Plugin;

class Process extends Phpfox_Service
{
    private $_bHasImage = false;

    private $_aCategories = array();

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('advancedmarketplace');
    }

    /**
     * Process wishlist
     * @param $listingId
     * @param $userId
     * @param int $wishlist
     * @return bool
     */
    public function processWishlist($listingId, $userId, $wishlist = 1)
    {
        if (empty($listingId)) {
            return false;
        }
        $userId = !empty($userId) ? $userId : Phpfox::getUserId();
        $table = Phpfox::getT('advancedmarketplace_wishlist');

        $count = db()->select('COUNT(*)')
            ->from($table)
            ->where('listing_id = ' . (int)$listingId . ' AND user_id = ' . (int)$userId)
            ->execute('getSlaveField');
        if ($count) {
            db()->update($table, ['is_wishlist' => $wishlist], 'listing_id = ' . (int)$listingId . ' AND user_id = ' . (int)$userId);
        } else {
            db()->insert($table, [
                'listing_id' => (int)$listingId,
                'user_id' => (int)$userId,
                'is_wishlist' => (int)$wishlist
            ], 'listing_id = ' . (int)$listingId . ' AND user_id = ' . (int)$userId);
        }
        return true;
    }

    public function add($aVals)
    {
        // Plugin call
        if ($sPlugin = Phpfox_Plugin::get('advancedmarketplace.service_process_add__start')) {
            eval($sPlugin);
        }

        if (!isset($aVals['post_status'])) {
            $aVals['post_status'] = 1;
        }
        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }
        Phpfox::getService('ban')->checkAutomaticBan($aVals);

        $oParseInput = Phpfox::getLib('parse.input');
        $iItemId = (isset($aVals['item_id']) ? $aVals['item_id'] : 0);

        $aSql = array(
            'view_id' => (Phpfox::getUserParam('advancedmarketplace.listing_approve') ? '1' : '0'),
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'group_id' => 0,
            'user_id' => empty($aVals['user_id']) ? Phpfox::getUserId() : $aVals['user_id'],
            'title' => $oParseInput->clean($aVals['title'], 255),
            'currency_id' => $aVals['currency_id'],
            'price' => $this->_price($aVals['price']),
            'country_iso' => $aVals['country_iso'],
            'country_child_id' => (isset($aVals['country_child_id']) ? (int)$aVals['country_child_id'] : 0),
            'postal_code' => (empty($aVals['postal_code']) ? null : Phpfox::getLib('parse.input')->clean($aVals['postal_code'],
                20)),
            'city' => (empty($aVals['city']) ? null : $oParseInput->clean($aVals['city'], 255)),
            'time_stamp' => PHPFOX_TIME,
            'is_sell' => (isset($aVals['is_sell']) ? (int)$aVals['is_sell'] : 0),
            'auto_sell' => (isset($aVals['auto_sell']) ? (int)$aVals['auto_sell'] : 0),
            'post_status' => (isset($aVals['post_status']) ? $aVals['post_status'] : '1'),
            'address' => (isset($aVals['address']) ? $aVals['address'] : null),
            'location' => (isset($aVals['location']) ? $aVals['location'] : null),
            'module_id' => (isset($aVals['module_id']) ? $aVals['module_id'] : 'advancedmarketplace'),
            'item_id' => $iItemId,
            'payment_methods' => serialize($aVals['payment_methods']),
            'has_expiry' => empty($aVals['has_expiry']) ? 0 : 1,
            'image_path' => $aVals['image_path'],
            'server_id' => $aVals['server_id'],
        );

        if (empty($aVals['has_expiry'])) {
            $aSql['expiry_date'] = 0;
        } else {
            $aSql['expiry_date'] = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['expiry_month'], $aVals['expiry_day'], $aVals['expiry_year']);
        }

        if (isset($aVals['gmap']) && is_array($aVals['gmap']) && isset($aVals['gmap']['latitude']) && isset($aVals['gmap']['longitude'])) {
            $aSql['gmap'] = serialize($aVals['gmap']);
            $aSql['lat'] = $aVals['gmap']['latitude'];
            $aSql['lng'] = $aVals['gmap']['longitude'];
        }

        $sFullAddress = $aSql["location"] . " " . $aSql["address"] . " " . $aSql["city"] . " " . $aSql["country_iso"];
        list($aCoordinates, $sGmapAddress) = $this->address2coordinates($sFullAddress);
        if (!empty($aCoordinates[1])) {
            $aSql['lat'] = $aCoordinates[1];
            $aSql['lng'] = $aCoordinates[0];
            $aSql['gmap_address'] = $oParseInput->prepare($sGmapAddress);
        }
        $iId = $this->database()->insert($this->_sTable, $aSql);

        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.service_process_add')) ? eval($sPlugin) : false);

        if (!$iId) {
            return false;
        }

        $this->database()->insert(':advancedmarketplace_image', array(
            'listing_id' => $iId,
            'image_path' => $aSql['image_path'],
            'server_id' => $aSql['server_id']
        ));

        if ($aVals['post_status'] != 2) {
            $this->sendNotificationToFollower(Phpfox::getUserId(), $iId);
        }

        $this->database()->insert(Phpfox::getT('advancedmarketplace_text'), array(
                'listing_id' => $iId,
                'description' => (empty($aVals['description']) ? null : $oParseInput->clean($aVals['description'])),
                'description_parsed' => (empty($aVals['description']) ? null : $oParseInput->prepare($aVals['description'])),
                'short_description' => (empty($aVals['short_description']) ? null : $oParseInput->clean($aVals['short_description'])),
                'short_description_parsed' => (empty($aVals['short_description']) ? null : $oParseInput->clean($aVals['short_description']))
            )
        );

        $this->database()->insert(Phpfox::getT('advancedmarketplace_category_data'),
            array('listing_id' => $iId, 'category_id' => $aVals['category']));

        if (Phpfox::isModule('feed') && !Phpfox::getUserParam('advancedmarketplace.listing_approve')
            && ($aVals['post_status'] == 1)
        ) {
            /**
             * add feed when info complete if there are no socialpublisher module was installed
             */
            if (!empty($aVals['module_id']) && $aVals['module_id'] != 'advancedmarketplace'
                && Phpfox::isModule($aVals['module_id']) && Phpfox::hasCallback($aVals['module_id'], 'getFeedDetails')
            ) {
                $aFeedDetails = Phpfox::callback($aVals['module_id'] . '.getFeedDetails', $aVals['item_id']);
                Phpfox::getService('feed.process')->callback($aFeedDetails)->add('advancedmarketplace', $iId,
                    $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0),
                    $iItemId);
            } else {
                Phpfox::getService('feed.process')->add('advancedmarketplace', $iId,
                    $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0));
            }
            Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'advancedmarketplace');
        }

        if ($aVals['privacy'] == '4') {
            Phpfox::getService('privacy.process')->add('advancedmarketplace', $iId,
                (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }

        if (Phpfox::isModule('tag')) {
            if (Phpfox::getParam('tag.enable_hashtag_support')) {
                if (!empty($aVals['description'])) {
                    Phpfox::getService('tag.process')->add('advancedmarketplace', $iId, Phpfox::getUserId(),
                        $aVals['description'], true);
                }
                if (!empty($aVals['short_description'])) {
                    Phpfox::getService('tag.process')->add('advancedmarketplace', $iId, Phpfox::getUserId(),
                        $aVals['short_description'], true);
                }
            }

            if (!empty($aVals['tag_list'])) {
                Phpfox::getService('tag.process')->add('advancedmarketplace', $iId, Phpfox::getUserId(),
                    $aVals['tag_list']);
            }
        }

        //Plugin call
        if ($sPlugin = Phpfox_Plugin::get('advancedmarketplace.service_process_add__end')) {
            eval($sPlugin);
        }

        return $iId;
    }

    public function uploadImages($iId, $aVals)
    {

        if (isset($_FILES['Filedata'])) {
            $oImage = Phpfox::getLib('image');
            $oFile = Phpfox::getLib('file');

            $aSizes = array(50, 120, 200, 250, 300, 400, 600);

            $iFileSizes = 0;
            if ($_FILES['Filedata']['error'] == UPLOAD_ERR_OK) {
                if ($aImage = $oFile->load('Filedata', array(
                    'jpg',
                    'gif',
                    'png'
                ),
                    (Phpfox::getUserParam('advancedmarketplace.max_upload_size_listing') === 0 ? null : (Phpfox::getUserParam('advancedmarketplace.max_upload_size_listing') / 1024))
                )
                ) {
                    $sFileName = Phpfox::getLib('file')->upload('Filedata',
                        Phpfox::getParam('core.dir_pic') . "advancedmarketplace/", $iId);

                    $iFileSizes += filesize(Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                            ''));

                    $this->database()->insert(Phpfox::getT('advancedmarketplace_image'), array(
                        'listing_id' => $iId,
                        'image_path' => $sFileName,
                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
                    ));

                    foreach ($aSizes as $iSize) {
                        $oImage->createThumbnail(Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                                ''), Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                                '_' . $iSize), $iSize, $iSize);
                        $oImage->createThumbnail(Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                                ''), Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                                '_' . $iSize . '_square'), $iSize, $iSize, false);

                        $iFileSizes += filesize(Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                                '_' . $iSize));
                    }
                }
            }

            if ($iFileSizes === 0) {
                return false;
            }

            $this->database()->update($this->_sTable, array(
                'image_path' => $sFileName,
                'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
            ), 'listing_id = ' . $iId);

            // Update user space usage
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'advancedmarketplace', $iFileSizes);
        }

    }

    public function update($iId, $aVals, $iUserId2, &$aRow = null)
    {
        $oParseInput = Phpfox::getLib('parse.input');
        Phpfox::getService('ban')->checkAutomaticBan($aVals['title'] . ' ' . $aVals['description']);

        if (empty($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }
        if (empty($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        $aSql = array(
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'title' => $oParseInput->clean($aVals['title'], 255),
            'currency_id' => $aVals['currency_id'],
            'price' => $this->_price($aVals['price']),
            'country_iso' => $aVals['country_iso'],
            'country_child_id' => (isset($aVals['country_child_id']) ? (int)$aVals['country_child_id'] : 0),
            'postal_code' => (empty($aVals['postal_code']) ? null : Phpfox::getLib('parse.input')->clean($aVals['postal_code'],
                20)),
            'city' => (empty($aVals['city']) ? null : $oParseInput->clean($aVals['city'], 255)),
            'is_sell' => (isset($aVals['is_sell']) ? (int)$aVals['is_sell'] : 0),
            'auto_sell' => (isset($aVals['auto_sell']) ? (int)$aVals['auto_sell'] : 0),
            'post_status' => (isset($aVals['post_status']) ? $aVals['post_status'] : '1'),
            'update_timestamp' => PHPFOX_TIME,
            'address' => (isset($aVals['address']) ? $aVals['address'] : null),
            'location' => (isset($aVals['location']) ? $aVals['location'] : null),
            'payment_methods' => serialize($aVals['payment_methods']),
            'has_expiry' => empty($aVals['has_expiry']) ? 0 : 1,
        );

        if (empty($aVals['has_expiry'])) {
            $aSql['expiry_date'] = 0;
        } else {
            $aSql['expiry_date'] = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['expiry_month'], $aVals['expiry_day'], $aVals['expiry_year']);
        }

        $aListing = Phpfox::getService('advancedmarketplace')->getForEdit($iId, true);
        if (isset($aVals['view_id'])) {
            if (($aListing['view_id'] == 0 || $aListing['view_id'] == 2) && $aVals['view_id'] == 2) {
                $aSql['view_id'] = 2;
            }
        } else {
            if ($aListing['view_id'] == 0 || $aListing['view_id'] == 2) {
                $aSql['view_id'] = 0;
            }
        }

        if (isset($aVals['gmap']) && is_array($aVals['gmap']) && isset($aVals['gmap']['latitude']) && isset($aVals['gmap']['longitude'])) {
            $aSql['gmap'] = serialize($aVals['gmap']);
            $aSql['lat'] = $aVals['gmap']['latitude'];
            $aSql['lng'] = $aVals['gmap']['longitude'];
        }
        $sFullAddress = $aSql["location"] . " " . $aSql["address"] . " " . $aSql["city"] . " " . $aSql["country_iso"];
        list($aCoordinates, $sGmapAddress) = $this->address2coordinates($sFullAddress);
        if (count($aCoordinates) > 1) {
            $aSql['lat'] = $aCoordinates[1];
            $aSql['lng'] = $aCoordinates[0];
            $aSql['gmap_address'] = $oParseInput->prepare($sGmapAddress);
        }
        $this->database()->update($this->_sTable, $aSql, 'listing_id = ' . (int)$iId);

        $this->database()->update(Phpfox::getT('advancedmarketplace_text'), array(
            'description' => (empty($aVals['description']) ? null : $oParseInput->clean($aVals['description'])),
            'description_parsed' => (empty($aVals['description']) ? null : $oParseInput->prepare($aVals['description'])),
            'short_description' => (empty($aVals['short_description']) ? null : $oParseInput->clean($aVals['short_description'])),
            'short_description_parsed' => (empty($aVals['short_description']) ? null : $oParseInput->prepare($aVals['short_description']))
        ), 'listing_id = ' . (int)$iId
        );

        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.service_process_update')) ? eval($sPlugin) : false);

        $this->database()->delete(Phpfox::getT('advancedmarketplace_category_data'), 'listing_id = ' . (int)$iId);

        if (!empty($aVals['category'])) {
            $this->database()->insert(Phpfox::getT('advancedmarketplace_category_data'),
                array('listing_id' => $iId, 'category_id' => $aVals['category']));
        }

        $aListing = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('listing_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if ($this->_bHasImage) {
            $oImage = Phpfox::getLib('image');
            $oFile = Phpfox::getLib('file');

            $aSizes = array(50, 120, 200, 400);

            $iFileSizes = 0;
            foreach ($_FILES['image']['error'] as $iKey => $sError) {
                if ($sError == UPLOAD_ERR_OK) {
                    if ($aImage = $oFile->load('image[' . $iKey . ']', array(
                        'jpg',
                        'gif',
                        'png'
                    ),
                        (Phpfox::getUserParam('advancedmarketplace.max_upload_size_listing') === 0 ? null : (Phpfox::getUserParam('advancedmarketplace.max_upload_size_listing') / 1024))
                    )
                    ) {
                        $sFileName = Phpfox::getLib('file')->upload('image[' . $iKey . ']',
                            Phpfox::getParam('core.dir_pic') . "advancedmarketplace/", $iId);

                        $iFileSizes += filesize(Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                                ''));

                        $this->database()->insert(Phpfox::getT('advancedmarketplace_image'), array(
                            'listing_id' => $iId,
                            'image_path' => $sFileName,
                            'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
                        ));

                        foreach ($aSizes as $iSize) {
                            $oImage->createThumbnail(Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                                    ''), Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                                    '_' . $iSize), $iSize, $iSize);
                            $oImage->createThumbnail(Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                                    ''), Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                                    '_' . $iSize . '_square'), $iSize, $iSize, false);

                            $iFileSizes += filesize(Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                                    '_' . $iSize));
                        }
                    }
                }
            }

            if ($iFileSizes === 0) {
                return false;
            }

            $this->database()->update($this->_sTable, array(
                'image_path' => $sFileName,
                'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
            ), 'listing_id = ' . $iId);

            // Update user space usage
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'advancedmarketplace', $iFileSizes);
        }

        if (isset($aVals['emails']) || isset($aVals['invite'])) {

            $aInvites = $this->database()->select('invited_user_id, invited_email')
                ->from(Phpfox::getT('advancedmarketplace_invite'))
                ->where('listing_id = ' . (int)$iId)
                ->execute('getRows');
            $aInvited = array();
            foreach ($aInvites as $aInvite) {
                $aInvited[(empty($aInvite['invited_email']) ? 'user' : 'email')][(empty($aInvite['invited_email']) ? $aInvite['invited_user_id'] : $aInvite['invited_email'])] = true;
            }
        }


        if (isset($aVals['emails'])) {

            // if (strpos($aVals['emails'], ','))
            {
                $aEmails = explode(',', $aVals['emails']);
                $aCachedEmails = array();

                foreach ($aEmails as $sEmail) {
                    $sEmail = trim($sEmail);
                    if (!Phpfox::getLib('mail')->checkEmail($sEmail)) {
                        continue;
                    }

                    if (isset($aInvited['email'][$sEmail])) {
                        continue;
                    }

                    $sLink = Phpfox::getLib('url')->permalink('advancedmarketplace.detail', $aListing['listing_id'],
                        $aListing['title']);
                    $sMessage = _p('advancedmarketplace.full_name_invited_you_to_view_the_advancedmarketplace_listing_title',
                        array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => $oParseInput->clean($aVals['title'], 255),
                            'link' => $sLink
                        )
                    );
                    if (!empty($aVals['personal_message'])) {
                        $sMessage .= "\n\n" . _p('advancedmarketplace.full_name_added_the_following_personal_message',
                                array('full_name' => Phpfox::getUserBy('full_name'))) . ":\n";
                        $sMessage .= $aVals['personal_message'];
                    }
                    //var_dump($aVals['invite_from']); die();
                    $oMail = Phpfox::getLib('mail');
                    if (isset($aVals['invite_from']) && $aVals['invite_from'] == 1) {
                        $oMail->fromEmail(Phpfox::getUserBy('email'))
                            ->fromName(Phpfox::getUserBy('full_name'));
                    }
                    $bSent = $oMail->to($sEmail)
                        ->subject(array(
                            'advancedmarketplace.full_name_invited_you_to_view_the_listing_title',
                            array(
                                'full_name' => Phpfox::getUserBy('full_name'),
                                'title' => $oParseInput->clean($aVals['title'], 255)
                            )
                        ))
                        ->message($sMessage)
                        ->send();

                    if ($bSent) {
                        $this->_aInvited[] = array('email' => $sEmail);

                        $aCachedEmails[$sEmail] = true;

                        $this->database()->insert(Phpfox::getT('advancedmarketplace_invite'), array(
                                'listing_id' => $iId,
                                'type_id' => 1,
                                'user_id' => Phpfox::getUserId(),
                                'invited_email' => $sEmail,
                                'time_stamp' => PHPFOX_TIME
                            )
                        );
                    }
                }
            }
        }
        if (isset($aVals['invite']) && is_array($aVals['invite'])) {
            $sUserIds = '';
            foreach ($aVals['invite'] as $iUserId) {
                if (!is_numeric($iUserId)) {
                    continue;
                }
                $sUserIds .= $iUserId . ',';
            }
            $sUserIds = rtrim($sUserIds, ',');

            $aUsers = $this->database()->select('user_id, email, language_id, full_name')
                ->from(Phpfox::getT('user'))
                ->where('user_id IN(' . $sUserIds . ')')
                ->execute('getSlaveRows');

            foreach ($aUsers as $aUser) {
                if (isset($aCachedEmails[$aUser['email']])) {
                    continue;
                }

                if (isset($aInvited['user'][$aUser['user_id']])) {
                    continue;
                }

                $sLink = Phpfox::getLib('url')->permalink('advancedmarketplace.detail', $aListing['listing_id'],
                    $aListing['title']);
                $sMessage = _p('advancedmarketplace.full_name_invited_you_to_view_the_advancedmarketplace_listing_title',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'title' => $oParseInput->clean($aVals['title'], 255),
                        'link' => $sLink
                    ), $aUser['language_id']
                );
                if (!empty($aVals['personal_message'])) {
                    $sMessage .= "\n\n" . _p('advancedmarketplace.full_name_added_the_following_personal_message',
                            array('full_name' => Phpfox::getUserBy('full_name')), $aUser['language_id']);
                    $sMessage .= $aVals['personal_message'];
                }

                $bSent = Phpfox::getLib('mail')->to($aUser['user_id'])
                    ->subject(array(
                        'advancedmarketplace.full_name_invited_you_to_view_the_listing_title',
                        array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => $oParseInput->clean($aVals['title'], 255)
                        )
                    ))
                    ->message($sMessage)
                    ->notification('advancedmarketplace.new_invite')
                    ->send();

                if ($aVals['post_status'] == '1') {
                    $this->_aInvited[] = array('user' => $aUser['full_name']);

                    $this->database()->insert(Phpfox::getT('advancedmarketplace_invite'), array(
                            'listing_id' => $iId,
                            'user_id' => Phpfox::getUserId(),
                            'invited_user_id' => $aUser['user_id'],
                            'time_stamp' => PHPFOX_TIME
                        )
                    );

                    (Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('advancedmarketplace_invite',
                        $iId, $aUser['user_id']) : null);
                }
            }
        }

        if ($aRow !== null && $aRow['post_status'] == '2' && $aVals['post_status'] == '1') {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('advancedmarketplace', $iId,
                $aVals['privacy'], $aVals['privacy_comment'], 0, $aListing['user_id']) : null);
            $this->sendNotificationToFollower($aListing['user_id'], $aListing['listing_id']);
            // Update user activity
            Phpfox::getService('user.activity')->update($aListing['user_id'], 'advancedmarketplace');
        } else {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('advancedmarketplace', $iId,
                $aVals['privacy'], $aVals['privacy_comment']) : null);
        }

        if (Phpfox::isModule('privacy')) {
            if ($aVals['privacy'] == '4') {
                Phpfox::getService('privacy.process')->update('advancedmarketplace', $iId,
                    (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
            } else {
                Phpfox::getService('privacy.process')->delete('advancedmarketplace', $iId);
            }
        }

        if (Phpfox::isModule('tag')) {
            if (Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['description'])) {
                Phpfox::getService('tag.process')->update('advancedmarketplace', $iId, $iUserId2, $aVals['description'],
                    true);
            }

            if (isset($aVals['tag_list'])) {
                Phpfox::getService('tag.process')->update('advancedmarketplace', $iId, $iUserId2, $aVals['tag_list']);
            }
        }

        return true;
    }

    public function delete($iId, &$aListing = null)
    {
        if ($aListing === null) {
            $aListing = $this->database()->select('user_id, image_path')
                ->from($this->_sTable)
                ->where('listing_id = ' . (int)$iId)
                ->execute('getRow');

            if (!isset($aListing['user_id'])) {
                return Phpfox_Error::set(_p('advancedmarketplace.unable_to_find_the_listing_you_want_to_delete'));
            }

            if (!Phpfox::getService('user.auth')->hasAccess('listing', 'listing_id', $iId,
                'advancedmarketplace.can_delete_own_listing', 'advancedmarketplace.can_delete_other_listings',
                $aListing['user_id'])) {
                return Phpfox_Error::set(_p('advancedmarketplace.you_do_not_have_sufficient_permission_to_delete_this_listing'));
            }
        }

        $iFileSizes = 0;
        $aImages = $this->database()->select('image_id, image_path, server_id')
            ->from(Phpfox::getT('advancedmarketplace_image'))
            ->where('listing_id = ' . $iId)
            ->execute('getRows');
        foreach ($aImages as $aImage) {
            $aSizes = array('', 50, 120, 200, 400);
            foreach ($aSizes as $iSize) {
                $sImage = Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($aListing['image_path'],
                        (empty($iSize) ? '' : '_') . $iSize);
                if (file_exists($sImage)) {
                    $iFileSizes += filesize($sImage);

                    @unlink($sImage);
                }

                if ($aImage['server_id'] > 0) {
                    $advancedmarketplace_dir_image = Phpfox::getParam('advancedmarketplace.dir_pic');
                    $advancedmarketplace_url_image = Phpfox::getParam('advancedmarketplace.url_pic');

                    // Get the file size stored when the photo was uploaded
                    $sTempUrl = Phpfox::getLib('cdn')->getUrl(str_replace($advancedmarketplace_dir_image,
                        $advancedmarketplace_url_image, $sImage));

                    $aHeaders = get_headers($sTempUrl, true);
                    if (preg_match('/200 OK/i', $aHeaders[0])) {
                        $iFileSizes += (int)$aHeaders["Content-Length"];
                    }

                    Phpfox::getLib('cdn')->remove($sImage);
                }
            }

            $this->database()->delete(Phpfox::getT('advancedmarketplace_image'), 'image_id = ' . $aImage['image_id']);
        }

        if ($iFileSizes > 0) {
            Phpfox::getService('user.space')->update($aListing['user_id'], 'advancedmarketplace', $iFileSizes, '-');
        }

        (Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem(null, $iId,
            'advancedmarketplace') : null);
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('advancedmarketplace', $iId) : null);
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('comment_advancedmarketplace',
            $iId) : null);

        Phpfox::getService('notification.process')->delete('advancedmarketplace_follow', $iId, Phpfox::getUserId());
        Phpfox::getService('notification.process')->delete('advancedmarketplace_approved', $iId, Phpfox::getUserId());
        Phpfox::getService('notification.process')->delete('comment_advancedmarketplace', $iId, Phpfox::getUserId());
        Phpfox::getService('notification.process')->delete('advancedmarketplace_invite', $iId, Phpfox::getUserId());

        Phpfox::getService('comment.process')->deleteForItem(Phpfox::getUserId(), $iId, 'advancedmarketplace');

        $this->database()->delete($this->_sTable, 'listing_id = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('advancedmarketplace_text'), 'listing_id = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('advancedmarketplace_category_data'), 'listing_id = ' . (int)$iId);
        phpfox::getLib('database')->delete(phpfox::getT('advancedmarketplace_rate'), 'listing_id = ' . (int)$iId);
        phpfox::getLib('database')->delete(phpfox::getT('advancedmarketplace_recent_view'),
            'listing_id = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('tag'),
            'item_id = ' . (int)$iId . ' and category_id = "advancedmarketplace"');
        Phpfox::getService('user.activity')->update($aListing['user_id'], 'advancedmarketplace', '-');

        $this->cache()->remove('advancedmarketplace_sponsored');

        return true;
    }

    public function setDefault($iImageId)
    {
        $aListing = $this->database()->select('mi.image_path, mi.server_id, m.user_id, m.listing_id')
            ->from(Phpfox::getT('advancedmarketplace_image'), 'mi')
            ->join($this->_sTable, 'm', 'm.listing_id = mi.listing_id')
            ->where('mi.image_id = ' . (int)$iImageId)
            ->execute('getSlaveRow');

        if (!isset($aListing['user_id'])) {
            return Phpfox_Error::set('Unable to find the image.');
        }

        if (!Phpfox::getService('user.auth')->hasAccess('listing', 'listing_id', $aListing['listing_id'],
            'advancedmarketplace.can_delete_own_listing', 'advancedmarketplace.can_delete_other_listings',
            $aListing['user_id'])) {
            return Phpfox_Error::set(_p('advancedmarketplace.you_do_not_have_sufficient_permission_to_modify_this_listing'));
        }

        $this->database()->update($this->_sTable,
            array('image_path' => $aListing['image_path'], 'server_id' => $aListing['server_id']),
            'listing_id = ' . $aListing['listing_id']);

        return true;
    }

    public function deleteImage($iImageId)
    {
        $aListing = $this->database()->select('mi.image_id, mi.image_path, mi.server_id, m.user_id, m.listing_id, m.image_path AS default_image_path')
            ->from(Phpfox::getT('advancedmarketplace_image'), 'mi')
            ->join($this->_sTable, 'm', 'm.listing_id = mi.listing_id')
            ->where('mi.image_id = ' . (int)$iImageId)
            ->execute('getSlaveRow');

        if (!isset($aListing['user_id'])) {
            return Phpfox_Error::set('Unable to find the image.');
        }

        if (!Phpfox::getService('user.auth')->hasAccess('listing', 'listing_id', $aListing['listing_id'],
            'advancedmarketplace.can_delete_own_listing', 'advancedmarketplace.can_delete_other_listings',
            $aListing['user_id'])) {
            return Phpfox_Error::set(_p('advancedmarketplace.you_do_not_have_sufficient_permission_to_modify_this_listing'));
        }

        if ($aListing['default_image_path'] == $aListing['image_path']) {
            $aImage = $this->database()->select('image_path, server_id')
                ->from(Phpfox::getT('advancedmarketplace_image'))
                ->where('image_id != ' . ((int)$iImageId) . ' AND listing_id = ' . $aListing['listing_id'])
                ->execute('getSlaveRow');

            $this->database()->update($this->_sTable, array(
                'image_path' => (isset($aImage['image_path']) ? $aImage['image_path'] : null),
                'server_id' => (isset($aImage['server_id']) ? $aImage['server_id'] : 0)
            ),
                'listing_id = ' . $aListing['listing_id']);
        }

        //check if no image default -> set to "no image" image...
        $aImageListingCount = $this->database()->select('count(*)')
            ->from(Phpfox::getT('advancedmarketplace_image'), 'mi')
            ->join($this->_sTable, 'm', 'm.listing_id = mi.listing_id')
            ->where('mi.listing_id = ' . $aListing["listing_id"])
            ->execute('getSlaveField');
        if ($aImageListingCount === "1") {
            $this->database()->update($this->_sTable, array('image_path' => null),
                'listing_id = ' . $aListing['listing_id']);
        }
        //end check if no image default -> set to "no image" image...

        $iFileSizes = 0;
        $aSizes = array('', 50, 120, 200, 400);
        $aImages = $this->database()->select('image_path, server_id')
            ->from(Phpfox::getT('advancedmarketplace_image'))
            ->where('image_id = ' . ((int)$iImageId) . ' AND listing_id = ' . $aListing['listing_id'])
            ->execute('getSlaveRow');
        foreach ($aSizes as $iSize) {
            $sImage = Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($aListing['image_path'],
                    (empty($iSize) ? '' : '_') . $iSize);
            if (file_exists($sImage)) {
                $iFileSizes += filesize($sImage);

                @unlink($sImage);
            }

            if (isset($aImages) && $aImages['server_id'] > 0) {
                $advancedmarketplace_dir_image = Phpfox::getParam('advancedmarketplace.dir_pic');
                $advancedmarketplace_url_image = Phpfox::getParam('advancedmarketplace.url_pic');

                // Get the file size stored when the photo was uploaded
                $sTempUrl = Phpfox::getLib('cdn')->getUrl(str_replace($advancedmarketplace_dir_image,
                    $advancedmarketplace_url_image, $sImage));

                $aHeaders = get_headers($sTempUrl, true);
                if (preg_match('/200 OK/i', $aHeaders[0])) {
                    $iFileSizes += (int)$aHeaders["Content-Length"];
                }

                Phpfox::getLib('cdn')->remove($sImage);
            }

        }

        if ($iFileSizes > 0) {
            Phpfox::getService('user.space')->update($aListing['user_id'], 'advancedmarketplace', $iFileSizes, '-');
        }

        $this->database()->delete(Phpfox::getT('advancedmarketplace_image'), 'image_id = ' . $aListing['image_id']);

        return true;
    }

    public function setVisit($iId, $iUserId)
    {
        $this->database()->update(Phpfox::getT('advancedmarketplace_invite'), array('visited_id' => 1),
            'listing_id = ' . (int)$iId . ' AND invited_user_id = ' . (int)$iUserId);
        (Phpfox::isModule('request') ? Phpfox::getService('request.process')->delete('advancedmarketplace_invite', $iId,
            $iUserId) : null);
    }

    public function feature($iId, $iType)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('advancedmarketplace.can_feature_listings', true);
        $aListing = Phpfox::getService('advancedmarketplace')->getListing($iId);
        if ($aListing["post_status"] == 2) {
            return false;
        }

        $this->database()->update($this->_sTable, array('is_featured' => ($iType ? '1' : '0')),
            'listing_id = ' . (int)$iId);

        $this->cache()->remove('advancedmarketplace_featured');

        return true;
    }

    public function sponsor($iId, $iType)
    {
        if (!Phpfox::getUserParam('advancedmarketplace.can_sponsor_advancedmarketplace') && !Phpfox::getUserParam('advancedmarketplace.can_purchase_sponsor') && !defined('PHPFOX_API_CALLBACK')) {
            return Phpfox_Error::set('Hack attempt?');
        }
        $aListing = Phpfox::getService('advancedmarketplace')->getListing($iId);
        if ($aListing["post_status"] == 2) {

            return false;
        }
        $iType = (int)$iType;
        $iId = (int)$iId;
        if ($iType != 0 && $iType != 1) {
            return false;
        }
        $this->database()->update($this->_sTable, array('is_sponsor' => $iType),
            'listing_id = ' . $iId
        );

        if ($sPlugin = Phpfox_Plugin::get('advancedmarketplace.service_sponsor__end')) {
            eval($sPlugin);
        }
        $this->cache()->remove('advancedmarketplace_sponsored');

        return true;
    }

    public function approve($iId)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('advancedmarketplace.can_approve_listings', true);

        $aListing = $this->database()->select('v.*, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.listing_id = ' . (int)$iId)
            ->execute('getRow');

        if (!isset($aListing['listing_id'])) {
            return Phpfox_Error::set(_p('advancedmarketplace.unable_to_find_the_listing_you_want_to_approve'));
        }

        $this->database()->update($this->_sTable, array('view_id' => '0', 'time_stamp' => PHPFOX_TIME),
            'listing_id = ' . $aListing['listing_id']);

        if (Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('advancedmarketplace_approved', $aListing['listing_id'],
                $aListing['user_id']);

            $this->sendNotificationToFollower($aListing['user_id'], $aListing['listing_id']);
        }
        if ($aListing['post_status'] != 2) {
            Phpfox::getService('user.activity')->update($aListing['user_id'], 'advancedmarketplace');
        }

        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.service_process_approve__1')) ? eval($sPlugin) : false);

        // Send the user an email
        $sLink = Phpfox::getLib('url')->permalink('advancedmarketplace.detail', $aListing['listing_id'],
            $aListing['title']);
        Phpfox::getLib('mail')->to($aListing['user_id'])
            ->subject(array(
                'advancedmarketplace.your_listing_has_been_approved_on_site_title',
                array('site_title' => Phpfox::getParam('core.site_title'))
            ))
            ->message(array(
                'advancedmarketplace.your_listing_has_been_approved_on_site_title_message',
                array('site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)
            ))
            ->notification('advancedmarketplace.listing_is_approved')
            ->send();

        // $this->sendNotificationToFollower($aListing['user_id'], $aListing['listing_id']);
        if (isset($aListing['module_id']) && $aListing['module_id'] != 'advancedmarketplace' && Phpfox::isModule($aListing['module_id']) && Phpfox::hasCallback($aListing['module_id'],
                'getFeedDetails')) {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($aListing['module_id'] . '.getFeedDetails',
                $aListing['item_id']))->add('advancedmarketplace', $iId, $aListing['privacy'],
                (isset($aListing['privacy_comment']) ? (int)$aListing['privacy_comment'] : 0), 0,
                $aListing['user_id']) : null);
        } else {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('advancedmarketplace', $iId,
                $aListing['privacy'], (isset($aListing['privacy_comment']) ? (int)$aListing['privacy_comment'] : 0), 0,
                $aListing['user_id']) : null);
        }

        return true;
    }

    public function addInvoice($iId, $sCurrency, $sCost)
    {
        $iInvoiceId = $this->database()->insert(Phpfox::getT('advancedmarketplace_invoice'), array(
                'listing_id' => $iId,
                'user_id' => Phpfox::getUserId(),
                'currency_id' => $sCurrency,
                'price' => $sCost,
                'time_stamp' => PHPFOX_TIME
            )
        );

        return $iInvoiceId;
    }

    private function _price($sPrice)
    {
        if (empty($sPrice)) {
            return '0.00';
        }

        $sPrice = str_replace(array(' ', ','), '', $sPrice);
        $aParts = explode('.', $sPrice);
        if (count($aParts) > 2) {
            $iCnt = 0;
            $sPrice = '';
            foreach ($aParts as $sPart) {
                $iCnt++;
                $sPrice .= (count($aParts) == $iCnt ? '.' : '') . $sPart;
            }
        }

        return $sPrice;
    }

    private function _verify(&$aVals)
    {
        if (!isset($aVals['category'])) {
            return Phpfox_Error::set(_p('advancedmarketplace.provide_a_category_this_listing_will_belong_to'));
        }

        foreach ($aVals['category'] as $iCategory) {
            if (empty($iCategory)) {
                continue;
            }

            if (!is_numeric($iCategory)) {
                continue;
            }

            $this->_aCategories[] = $iCategory;
        }

        if (!count($this->_aCategories)) {
            return Phpfox_Error::set(_p('advancedmarketplace.provide_a_category_this_listing_will_belong_to'));
        }

        if (isset($_FILES['image'])) {
            foreach ($_FILES['image']['error'] as $iKey => $sError) {
                if ($sError == UPLOAD_ERR_OK) {
                    $aImage = Phpfox::getLib('file')->load('image[' . $iKey . ']', array(
                            'jpg',
                            'gif',
                            'png'
                        )
                    );

                    if ($aImage === false) {
                        continue;
                    }

                    $this->_bHasImage = true;
                }
            }
        }

        return true;
    }

    public function rate($iListingId, $iRate, $sComment)
    {
        $aItem = phpfox::getLib('database')->select('listing_id')
            ->from(phpfox::getT('advancedmarketplace'))
            ->where('listing_id = ' . $iListingId)
            ->execute('getField');

        if (empty($aItem)) {
            return false;
        }

        $aReviewedInfor = phpfox::getLib('database')->select('*')
            ->from(phpfox::getT('advancedmarketplace_rate'))
            ->where('user_id = ' . phpfox::getUserId() . ' and listing_id = ' . $iListingId)
            ->execute('getRow');

        if (count($aReviewedInfor) > 0) {
            return false;
        } else {
            $oDatabase = Phpfox::getLib("database");
            $iId = $oDatabase->insert(Phpfox::getT("advancedmarketplace_rate"), array(
                "content" => Phpfox::getLib('parse.input')->prepare($sComment),
                "rating" => $iRate,
                "timestamp" => time(),
                "user_id" => Phpfox::getUserId(),
                "listing_id" => $iListingId,
            ));

            if ($iId) {
                $aAverage = $this->database()->select('COUNT(*) AS count, AVG(rating) AS average_rating')
                    ->from(Phpfox::getT('advancedmarketplace_rate'))
                    ->where('listing_id = ' . (int)$iListingId)
                    ->execute('getRow');

                phpfox::getLib('database')->update(phpfox::getT('advancedmarketplace'),
                    array(
                        'total_score' => round($aAverage['average_rating']),
                        'total_rate' => $aAverage['count']
                    ),
                    'listing_id = ' . $iListingId);

            } else {
                return false;
            }

            return $iId;
        }
    }

    public function todaylisting($iListingId, $aDates)
    {
        if (empty($iListingId)) {
            return false;
        }

        $oDatabase = Phpfox::getLib("database");
        $aInsert = array();

        foreach ($aDates as $date) {
            $iDate = $date / 1000; //cheat javascript
            $aInsert[] = array($iListingId, $iDate);
        }
        $oDatabase->delete(Phpfox::getT("advancedmarketplace_today_listing"), sprintf("listing_id=%d", $iListingId));
        if (!empty($aInsert)) {
            $oDatabase->multiInsert(
                Phpfox::getT("advancedmarketplace_today_listing"), array("listing_id", "time_stamp"), $aInsert
            );
        }

        return true;
    }

    public function updateViewCounter($iListingId)
    {
        $this->database()->updateCounter('advancedmarketplace', 'total_view', 'listing_id', $iListingId);
    }

    /**
     *
     */
    public function updateRecentView($iListingId)
    {
        if (!phpfox::isUser()) {
            return false;
        }
        $iUserId = phpfox::getLib('database')->select('user_id')
            ->from(phpfox::getT('advancedmarketplace'))
            ->where('listing_id = ' . $iListingId)
            ->execute('getSlaveField');
        if ($iUserId == phpfox::getUserId()) {
            return false;
        }
        $aRows = Phpfox::getLib("database")
            ->select("*")
            ->from(phpfox::getT('advancedmarketplace_recent_view'))
            ->where(sprintf("user_id = %d AND listing_id = %d", Phpfox::getUserId(), $iListingId))
            ->execute("getRow");
        if (empty($aRows)) {
            Phpfox::getLib("database")->insert(Phpfox::getT('advancedmarketplace_recent_view'), array(
                "user_id" => Phpfox::getUserId(),
                "listing_id" => $iListingId,
                "timestamp" => PHPFOX_TIME,
            ));
        } else {
            phpfox::getLib('database')->update(phpfox::getT('advancedmarketplace_recent_view'),
                array('timestamp' => PHPFOX_TIME),
                'listing_id = ' . $iListingId . ' and user_id = ' . phpfox::getUserId());
        }

    }

    public function getRecentViewListings($iUserId = 0)
    {
//        if($iUserId === 0) {
//            $iUserId = Phpfox::getUserId();
//        }
//
//        return Phpfox::getLib("database")
//                ->select()
    }

    /**
     * @param $iUserId : the person will be follwed
     *          $iFollwerId: the person will follow
     */

    public function addFollow($iUserId, $iFollowerId)
    {
        if (phpfox::getParam('advancedmarketplace.can_follow_listings')) {
            $aInsert = array('user_id' => $iUserId, 'user_follow_id' => $iFollowerId);
            phpfox::getLib('database')->insert(phpfox::getT('advancedmarketplace_follow'), $aInsert);
        }

    }

    public function removeFollow($iUserId, $iFollowerId)
    {
        return phpfox::getLib('database')->delete(phpfox::getT('advancedmarketplace_follow'),
            'user_id = ' . $iUserId . ' and user_follow_id = ' . $iFollowerId);
    }

    public function sendNotificationToFollower($iUserId, $iItemId)
    {
        $aItem = phpfox::getLib('database')->select('l.*, ' . phpfox::getUserField())
            ->from(phpfox::getT('advancedmarketplace'), 'l')
            ->join(phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->where('l.listing_id = ' . $iItemId)
            ->execute('getSlaveRow');

        if (!empty($aItem)) {

            if ($aItem['post_status'] != 1 || $aItem['privacy'] != 0 || $aItem['view_id'] != 0) {
                return false;
            }

            $aFollowers = phpfox::getLib('database')->select('*')
                ->from(phpfox::getT('advancedmarketplace_follow'))
                ->where('user_id = ' . $iUserId)
                ->execute('getSlaveRows');
            // var_dump($aFollowers);
            foreach ($aFollowers as $iFollower) {
                if (Phpfox::isModule('notification')) {
                    Phpfox::getService('notification.process')->add('advancedmarketplace_follow', $aItem['listing_id'],
                        $iFollower['user_follow_id'], $aItem['user_id']);
                    $sLink = Phpfox::getLib('url')->permalink('advancedmarketplace.detail', $iItemId, $aItem['title']);
                    $sTitle = _p('advancedmarketplace.user_name_has_created_listing_on_site_title',
                        array('user_name' => $aItem['full_name'], 'site_title' => Phpfox::getParam('core.site_title')));
                    $sMessage = _p('advancedmarketplace.user_name_has_created_listing_on_site_title_message',
                        array(
                            'user_name' => $aItem['full_name'],
                            'site_title' => Phpfox::getParam('core.site_title'),
                            'title' => $aItem['title'],
                            'link' => $sLink
                        ));
                    Phpfox::getLib('mail')->to($iFollower['user_follow_id'])
                        ->subject($sTitle)
                        ->message($sMessage)
                        ->send();
                }
            }
        }
    }

    public function deleteTodayListing($iItemId)
    {
        $aItem = phpfox::getLib('database')->select('l.listing_id')
            ->from(phpfox::getT('advancedmarketplace'), 'l')
            ->where('l.listing_id = ' . $iItemId)
            ->execute('getField');
        if (empty($aItem)) {
            return;
        }
        phpfox::getLib('database')->delete(phpfox::getT('advancedmarketplace_today_listing'),
            'listing_id = ' . $iItemId);
    }

    public function deleteReviewsOfListing($iItemId)
    {
        $aItem = phpfox::getLib('database')->select('l.listing_id')
            ->from(phpfox::getT('advancedmarketplace'), 'l')
            ->where('l.listing_id = ' . $iItemId)
            ->execute('getField');
        if (empty($aItem)) {
            return;
        }
        phpfox::getLib('database')->delete(phpfox::getT('advancedmarketplace_rate'), 'listing_id = ' . $iItemId);
    }

    public function migrateMarketplaceData()
    {
        $oParseInput = phpfox::getLib('parse.input');
        $aListings = phpfox::getLib('database')->select('l.*, cd.*, mt.*')
            ->from(phpfox::getT('marketplace'), 'l')
            ->join(phpfox::getT('marketplace_category_data'), 'cd', 'cd.listing_id = l.listing_id')
            ->join(phpfox::getT('marketplace_text'), 'mt', 'mt.listing_id = l.listing_id')
            ->execute('getRows');
        if (empty($aListings)) {
            return false;
        }

        $source = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'PF.Base' . DIRECTORY_SEPARATOR .'file' . DIRECTORY_SEPARATOR . 'pic' . DIRECTORY_SEPARATOR . 'marketplace';
        $dest = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'PF.Base' . DIRECTORY_SEPARATOR . 'file' . DIRECTORY_SEPARATOR . 'pic' . DIRECTORY_SEPARATOR . 'advancedmarketplace';

        $this->full_copy($source, $dest);
        $name = 'Others';
        $phrase_var_name = 'marketplace_category_' . md5('Marketplace Category' . $name);
        $aCategory = phpfox::getLib('database')->select('*')
            ->from(phpfox::getT('advancedmarketplace_category'))
            ->where("name = '" . $phrase_var_name . "'")
            ->execute('getSlaveRow');

        if (empty($aCategory)) {
            $aLanguages = Phpfox::getService('language')->getAll();
            $aText = [];
            foreach ($aLanguages as $aLanguage) {
                Phpfox::getService('ban')->checkAutomaticBan($name);
                $aText[$aLanguage['language_id']] = $name;
            }
            $aValsPhrase = [
                'var_name' => $phrase_var_name,
                'text' => $aText
            ];

            $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
            $aInsert = array(
                'parent_id' => 0,
                'is_active' => 1,
                'name' => $finalPhrase,
                'name_url' => $oParseInput->cleanTitle('Others'),
                'time_stamp' => PHPFOX_TIME
            );
            $iCategoryId = phpfox::getLib('database')->insert(phpfox::getT('advancedmarketplace_category'), $aInsert);
        } else {
            $iCategoryId = $aCategory['category_id'];
        }

        foreach ($aListings as $aListing) {
            if (!empty($aListing)) {
                $iId = $this->_insertListingData($aListing, $iCategoryId);
                $this->_insertListingImage($iId, $aListing['listing_id']);
                $this->_insertListingInvite($iId, $aListing['listing_id']);
                $this->_insertListingInvoice($iId, $aListing['listing_id']);
            }
        }

        phpfox::getLib('url')->send('admincp.advancedmarketplace.migration', null,
            _p('advancedmarketplace.migration_succeed'));
    }

    private function _isExistingCategory($sCategoryName)
    {
        $aCategory = phpfox::getLib('database')->select('ac.name')
            ->from(phpfox::getT('advancedmarketplace_category'), 'ac')
            ->where('ac.name = "' . $sCategoryName . '"')
            ->execute('getRow');
        if (empty($aCategory)) {
            return false;
        }

        return true;

    }

    private function _insertCategory($aVals)
    {
        if (empty($aVals)) {
            return false;
        }
        phpfox::getLib('database')->query('ALTER TABLE `' . phpfox::getT('advancedmarketplace_category') . '` AUTO_INCREMENT = 1');
        $aInsert = array(
            'category_id' => $aVals['category_id'],
            'parent_id' => $aVals['parent_id'],
            'is_active' => $aVals['is_active'],
            'name' => $aVals['name'],
            'name_url' => $aVals['name_url'],
            'time_stamp' => $aVals['time_stamp'],
            'used' => $aVals['used'],
            'ordering' => $aVals['ordering']
        );
        phpfox::getLib('database')->insert(phpfox::getT('advancedmarketplace_category'), $aInsert);
    }


    private function _getListingDetails($iCategoryId)
    {
        $aListings = phpfox::getLib('database')->select('l.*, cd.*, mt.*')
            ->from(phpfox::getT('marketplace'), 'l')
            ->join(phpfox::getT('marketplace_category_data'), 'cd', 'cd.listing_id = l.listing_id')
            ->join(phpfox::getT('marketplace_text'), 'mt', 'mt.listing_id = l.listing_id')
            ->where('cd.category_id = ' . $iCategoryId)
            ->execute('getRows');
        if (empty($aListings)) {
            return false;
        }

        return $aListings;
    }

    private function _insertListingData($aVals, $iCategoryId = 0)
    {
        $oParseInput = Phpfox::getLib('parse.input');
        $aSql = array(
            'view_id' => $aVals['view_id'],
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'group_id' => 0,
            'user_id' => (isset($aVals['user_id']) ? $aVals['user_id'] : phpfox::getUserId()),
            'title' => $oParseInput->clean($aVals['title'], 255),
            'currency_id' => $aVals['currency_id'],
            'price' => $this->_price($aVals['price']),
            'country_iso' => $aVals['country_iso'],
            'country_child_id' => (isset($aVals['country_child_id']) ? (int)$aVals['country_child_id'] : 0),
            'postal_code' => (empty($aVals['postal_code']) ? null : Phpfox::getLib('parse.input')->clean($aVals['postal_code'],
                20)),
            'city' => (empty($aVals['city']) ? null : $oParseInput->clean($aVals['city'], 255)),
            'time_stamp' => $aVals['time_stamp'],
            'update_timestamp' => PHPFOX_TIME,
            'is_sell' => (isset($aVals['is_sell']) ? (int)$aVals['is_sell'] : 0),
            'auto_sell' => (isset($aVals['auto_sell']) ? (int)$aVals['auto_sell'] : 0),
            'post_status' => (isset($aVals['post_status']) ? $aVals['post_status'] : '1'),
            'image_path' => (isset($aVals['image_path']) ? $aVals['image_path'] : null),
            'is_closed' => (isset($aVals['is_closed']) ? (int)$aVals['is_closed'] : 0),
            'is_featured' => (isset($aVals['is_featured']) ? (int)$aVals['is_featured'] : 0),
            'is_sponsor' => (isset($aVals['is_sponsor']) ? (int)$aVals['is_sponsor'] : 0),
            'lat' => (isset($aVals['lat']) ? (int)$aVals['lat'] : 0),
            'lng' => (isset($aVals['lng']) ? (int)$aVals['lng'] : 0)
        );
        $iId = $this->database()->insert($this->_sTable, $aSql);
        if (!$iId) {
            return false;
        }
        if ($iCategoryId) {
            $this->database()->insert(Phpfox::getT('advancedmarketplace_category_data'),
                array('listing_id' => $iId, 'category_id' => $iCategoryId));
        } else {
            $this->database()->insert(Phpfox::getT('advancedmarketplace_category_data'),
                array('listing_id' => $iId, 'category_id' => $aVals['category_id']));
        }

        $this->database()->insert(Phpfox::getT('advancedmarketplace_text'), array(
                'listing_id' => $iId,
                'description' => (empty($aVals['description']) ? null : $aVals['description']),
                'description_parsed' => (empty($aVals['description_parsed']) ? null : $aVals['description_parsed']),
                'short_description' => (empty($aVals['mini_description']) ? null : $oParseInput->clean($aVals['mini_description'])),
                'short_description_parsed' => (empty($aVals['mini_description']) ? null : $oParseInput->prepare($aVals['mini_description']))
            )
        );
        $iTotalListings = phpfox::getLib('database')->select('COUNT(*)')
            ->from(phpfox::getT('advancedmarketplace'), 'm')
            ->where('m.user_id = ' . $aVals['user_id'] . ' and m.view_id = 0')
            ->execute('getField');
        if ($iTotalListings > 0) {
            $this->database()->update(Phpfox::getT('user_field'),
                array('total_advlisting' => $iTotalListings),
                'user_id = ' . (int)$aVals['user_id']);
        }

        return $iId;
    }

    private function _insertListingImage($iId, $iOldId)
    {
        if (!$iId) {
            return false;
        }
        $aImages = phpfox::getLib('database')->select('mi.*')
            ->from(phpfox::getT('marketplace_image'), 'mi')
            ->where('mi.listing_id = ' . $iOldId)
            ->execute('getRows');
        if (empty($aImages)) {
            return false;
        }
        foreach ($aImages as $aImage) {
            if (!empty($aImage)) {
                $aInsert = array(
                    'listing_id' => $iId,
                    'image_path' => $aImage['image_path'],
                    'server_id' => $aImage['server_id'],
                    'ordering' => $aImage['ordering'],
                    'is_primary' => (isset($aImage['is_primary'])) ? $aImage['is_primary'] : 0
                );
                phpfox::getLib('database')->insert(phpfox::getT('advancedmarketplace_image'), $aInsert);
            }

        }
    }

    private function _insertListingInvite($iId, $iOldId)
    {
        if (!$iId) {
            return false;
        }
        $aInvites = phpfox::getLib('database')->select('mi.*')
            ->from(phpfox::getT('marketplace_invite'), 'mi')
            ->where('mi.listing_id = ' . $iOldId)
            ->execute('getRows');
        foreach ($aInvites as $aInvite) {
            if (!empty($aInvite)) {
                $aInsert = array(
                    'listing_id' => $iId,
                    'type_id' => $aInvite['type_id'],
                    'visited_id' => $aInvite['visited_id'],
                    'user_id' => $aInvite['user_id'],
                    'invited_user_id' => $aInvite['invited_user_id'],
                    'invited_email' => $aInvite['invited_email'],
                    'time_stamp' => $aInvite['time_stamp']
                );
                phpfox::getLib('database')->insert(phpfox::getT('advancedmarketplace_invite'), $aInsert);
            }

        }
    }

    private function _insertListingInvoice($iId, $iOldId)
    {
        if (!$iId) {
            return false;
        }
        $aInvoices = phpfox::getLib('database')->select('mi.*')
            ->from(phpfox::getT('marketplace_invoice'), 'mi')
            ->where('mi.listing_id = ' . $iOldId)
            ->execute('getRows');
        foreach ($aInvoices as $aInvoice) {
            if (!empty($aInvoice)) {
                $aInsert = array(
                    'listing_id' => $iId,
                    'currency_id' => $aInvoice['currency_id'],
                    'price' => $aInvoice['price'],
                    'user_id' => $aInvoice['user_id'],
                    'status' => $aInvoice['status'],
                    'time_stamp_paid' => $aInvoice['time_stamp_paid'],
                    'time_stamp' => $aInvoice['time_stamp']
                );
                phpfox::getLib('database')->insert(phpfox::getT('advancedmarketplace_invoice'), $aInsert);
            }

        }
    }

    private function full_copy($source, $target)
    {
        if (is_dir($source)) {
            @mkdir($target);
            $d = dir($source);
            while (false !== ($entry = $d->read())) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                $Entry = $source . DIRECTORY_SEPARATOR . $entry;
                if (is_dir($Entry)) {
                    $this->full_copy($Entry, $target . DIRECTORY_SEPARATOR . $entry);
                    continue;
                }
                copy($Entry, $target . DIRECTORY_SEPARATOR . $entry);
            }

            $d->close();
        } else {
            copy($source, $target);
        }
    }

    public function address2coordinates($sAddress)
    {
        $apiaddress = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($sAddress) . "&sensor=true&key=" . Phpfox::getParam('core.google_api_key');
        $aResponse = json_decode(Phpfox::getLib('request')->send($apiaddress, array(), 'GET',
            $_SERVER['HTTP_USER_AGENT']), true);
        if ($aResponse['results']) {
            $tmpaCoordinates = $aResponse['results'][0]['geometry']['location'];
            $aCoordinates[1] = $tmpaCoordinates['lat'];
            $aCoordinates[0] = $tmpaCoordinates['lng'];
            $sGmapAddress = $aResponse['results'][0]['formatted_address'];
        } else {
            $aCoordinates[1] = 0;
            $aCoordinates[0] = 0;
            $sGmapAddress = '';
        }

        return array($aCoordinates, $sGmapAddress);
    }

    public function updateSetting($aVals)
    {
        $iCount = phpfox::getLib('database')->select('count(*)')
            ->from(phpfox::getT('advancedmarketplace_setting'))
            ->execute('getField');
        if ($iCount == 0) {
            $iId = phpfox::getLib('database')->insert(phpfox::getT('advancedmarketplace_setting'),
                array('var_name' => 'location_setting', 'value' => $aVals['location_setting']));

            return $iId;
        }
        foreach ($aVals as $iKey => $aItem) {
            if (!empty($aVals[$iKey])) {
                phpfox::getLib('database')->update(phpfox::getT('advancedmarketplace_setting'),
                    array('value' => $aItem),
                    'var_name = "' . $iKey . '"');
            }
        }
    }

    public function sendExpireNotifications()
    {
        $iExpireDaysInSeconds = (Phpfox::getParam('advancedmarketplace.advmarketplace_days_to_notify_expire') * 86400);
        // Get the listings to notify
        $aNotify = $this->database()->select('m.listing_id, m.title, u.full_name, u.email, m.user_id, m.expiry_date')
            ->from(Phpfox::getT('advancedmarketplace'), 'm')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
            ->where('(m.is_notified = 0) AND (m.has_expiry = 1 AND m.expiry_date <= ' . (PHPFOX_TIME + $iExpireDaysInSeconds) . ' AND m.expiry_date > ' . PHPFOX_TIME . ')')
            ->execute('getSlaveRows');

        if (!empty($aNotify)) {
            $aUpdate = array();
            foreach ($aNotify as $aRow) {
                Phpfox::getLib('mail')
                    ->to($aRow['user_id'])
                    ->sendToSelf(true)
                    ->subject(array(
                        'advancedmarketplace.listing_expiring_subject',
                        array(
                            'title' => $aRow['title'],
                            'site_title' => Phpfox::getParam('core.site_title'),
                            'days' => round(((int)$aRow['expiry_date'] - PHPFOX_TIME) / 86400)
                        )
                    ))
                    ->message(array(
                        'advancedmarketplace.listing_expiring_message',
                        array(
                            'title' => $aRow['title'],
                            'site_title' => Phpfox::getParam('core.site_title'),
                            'link' => Phpfox::getLib('url')->permalink('advancedmarketplace.detail',
                                $aRow['listing_id'], $aRow['title']),
                            'days' => round(((int)$aRow['expiry_date'] - PHPFOX_TIME) / 86400)
                        )
                    ))
                    ->send();

                $aUpdate[] = $aRow['listing_id'];
            }

            $this->database()->update(Phpfox::getT('advancedmarketplace'), array('is_notified' => 1),
                'listing_id IN (' . implode(',', $aUpdate) . ')');
        }
    }

    public function getListingPaymentMethods($listingId = 0)
    {
        $paymentMethods = false;
        if ($listingId) {
            $listing = Phpfox::getService('advancedmarketplace')->getForEdit($listingId, true);
            if (!empty($listing['payment_methods'])) {
                try {
                    $paymentMethods = unserialize($listing['payment_methods']);
                } catch (Exception $e) {
                }
            }
        }

        return $paymentMethods;
    }


}
