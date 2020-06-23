<?php

namespace Apps\YNC_Comment\Service;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Request;
use Phpfox_Service;
use Phpfox_Url;

class Ynccomment extends Phpfox_Service
{
    private static $_iLimitStickers = 48;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('comment');
    }

    public function getUploadParams($aParams = null)
    {
        if (isset($aParams['id'])) {
            $iTotalStickers = Phpfox::getService('ynccomment.stickers')->countStickers($aParams['id']);
            $iRemainImage = self::$_iLimitStickers - $iTotalStickers;
        } else {
            $iRemainImage = self::$_iLimitStickers;
        }
        $iMaxFileSize = null;
        $aEvents = [
            'sending' => 'ynccomment_admin.dropzoneOnSending',
            'success' => 'ynccomment_admin.dropzoneOnSuccess',
            'queuecomplete' => 'ynccomment_admin.dropzoneQueueComplete',
            'removedfile' => 'ynccomment_admin.dropzoneOnRemoveFile',
            'error' => 'ynccomment_admin.dropzoneOnError'
        ];
        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'upload_url' => Phpfox::getLib('url')->makeUrl('admincp.ynccomment.frame-upload'),
            'component_only' => true,
            'max_file' => $iRemainImage,
            'js_events' => $aEvents,
            'upload_now' => "false",
            'submit_button' => '',
            'first_description' => _p('drag_n_drop_multi_photos_here_to_upload'),
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'ynccomment/',
            'upload_path' => Phpfox::getParam('core.url_pic') . 'ynccomment/',
            'update_space' => true,
            'no_square' => true,
            'type_list' => ['jpg', 'gif', 'png'],
            'style' => '',
            'extra_description' => [
                _p('maximum_photos_you_can_upload_is_number', ['number' => $iRemainImage])
            ],
            'thumbnail_sizes' => Phpfox::getParam('ynccomment.thumbnail_sizes')
        ];
    }

    public function getUploadParamsComment($aParams = null)
    {
        $iMaxFileSize = Phpfox::getUserParam('photo.photo_max_upload_size');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize / 1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        $aEvents = [
            'success' => 'ynccomment.dropzoneOnSuccessAttach',
            'error' => 'ynccomment.dropzoneOnErrorAttach',
            'sending' => 'ynccomment.dropzoneOnSendingAttach',
            'init' => 'ynccomment.dropzoneOnInitAttach'
        ];
        $sType = 'ynccomment_comment';
        if (!empty($aParams['parent_id'])) {
            $sType = 'ynccomment_photo_parent_' . $aParams['parent_id'];
        } elseif (!empty($aParams['feed_id'])) {
            $sType = 'ynccomment_photo_' . $aParams['feed_id'];
        } elseif (!empty($aParams['edit_id'])) {
            $sType = 'ynccomment_edit_photo_' . $aParams['edit_id'];
        }
        $sPreviewTemplate = '';
        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'component_only' => true,
            'max_file' => 1,
            'js_events' => $aEvents,
            'upload_now' => true,
            'first_description' => _p('drag_n_drop_multi_photos_here_to_upload'),
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'ynccomment/',
            'upload_path' => Phpfox::getParam('core.url_pic') . 'ynccomment/',
            'update_space' => true,
            'no_square' => true,
            'keep_form' => true,
            'type_list' => ['jpg', 'gif', 'png'],
            'on_remove' => 'ynccomment.deleteAttachPhoto',
            'style' => 'mini',
            'extra_description' => [
                _p('maximum_photos_you_can_upload_is_number', ['number' => 1])
            ],
            'thumbnail_sizes' => Phpfox::getParam('ynccomment.attach_sizes'),
            'type' => $sType,
            'preview_template' => $sPreviewTemplate,
        ];
    }

    /**
     * @param int $iId
     *
     * @return array
     */
    public function getComment($iId)
    {
        list(, $aRows) = $this->get('cmt.*', ['AND cmt.comment_id = ' . $iId], 'cmt.time_stamp DESC', 0, 1, 1);

        return (isset($aRows[0]['comment_id']) ? $aRows[0] : []);
    }

    /**
     * @param string $sSelect
     * @param array $aConds
     * @param string $sSort
     * @param string $iRange
     * @param string $sLimit
     * @param null $iCnt
     * @param bool $bIncludeOwnerDetails
     *
     * @return array
     */
    public function get(
        $sSelect,
        $aConds,
        $sSort = 'cmt.time_stamp DESC',
        $iRange = '',
        $sLimit = '',
        $iCnt = null,
        $bIncludeOwnerDetails = false
    ) {
        (($sPlugin = Phpfox_Plugin::get('comment.service_comment_get__start')) ? eval($sPlugin) : false);

        $aRows = [];

        if ($iCnt === null) {
            (($sPlugin = Phpfox_Plugin::get('comment.service_comment_get_count_query')) ? eval($sPlugin) : false);

            $iCnt = $this->database()->select('COUNT(*)')
                ->from($this->_sTable, 'cmt')
                ->where($aConds)
                ->execute('getSlaveField');
        }

        if ($iCnt) {
            if (Phpfox::isUser()) {
                $this->database()->select('cr.comment_id AS has_rating, cr.rating AS actual_rating, ')
                    ->leftJoin(Phpfox::getT('comment_rating'), 'cr',
                        'cr.comment_id = cmt.comment_id AND cr.user_id = ' . (int)Phpfox::getUserId());
            }

            if ($bIncludeOwnerDetails === true) {
                $this->database()->select(Phpfox::getUserField('owner', 'owner_') . ', ')
                    ->leftJoin(Phpfox::getT('user'), 'owner', 'owner.user_id = cmt.owner_user_id');
            }

            if (Phpfox::isModule('like')) {
                $this->database()->select('l.like_id AS is_liked, ')
                    ->leftJoin(Phpfox::getT('like'), 'l',
                        'l.type_id = \'feed_mini\' AND l.item_id = cmt.comment_id AND l.user_id = ' . Phpfox::getUserId());
            }

            (($sPlugin = Phpfox_Plugin::get('comment.service_comment_get_query')) ? eval($sPlugin) : false);

            $aRows = $this->database()->select($sSelect . ", " . (Phpfox::getParam('core.allow_html') ? "comment_text.text_parsed" : "comment_text.text") . " AS text, " . Phpfox::getUserField())
                ->from($this->_sTable, 'cmt')
                ->leftJoin(Phpfox::getT('comment_text'), 'comment_text', 'comment_text.comment_id = cmt.comment_id')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = cmt.user_id')
                ->where($aConds)
                ->order($sSort)
                ->limit($iRange, $sLimit, $iCnt)
                ->execute('getSlaveRows');

        }

        $oUrl = Phpfox_Url::instance();
        $oParseOutput = Phpfox::getLib('parse.output');
        foreach ($aRows as $iKey => $aRow) {
            $aRows[$iKey]['link'] = '';
            if ($aRow['user_name']) {
                $aRows[$iKey]['link'] = $oUrl->makeUrl($aRow['user_name']);
                $aRows[$iKey]['is_guest'] = false;
            } else {
                if (Phpfox::getUserBy('profile_page_id') > 0 && Phpfox::isModule('pages')) {
                    $aRows[$iKey]['full_name'] = $oParseOutput->clean(Phpfox::getUserBy('full_name'));
                } else {
                    $aRows[$iKey]['full_name'] = $oParseOutput->clean($aRow['author']);
                }

                $aRows[$iKey]['is_guest'] = true;
                if ($aRow['author_url']) {
                    $aRows[$iKey]['link'] = $aRow['author_url'];
                }
            }
            $aRows[$iKey]['unix_time_stamp'] = $aRow['time_stamp'];
            $aRows[$iKey]['time_stamp'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'),
                $aRow['time_stamp']);
            $aRows[$iKey]['posted_on'] = _p('user_link_at_item_time_stamp', array(
                    'item_time_stamp' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'),
                        $aRow['time_stamp']),
                    'user' => $aRow
                )
            );
            $aRows[$iKey]['update_time'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'),
                $aRow['update_time']);
            $aRows[$iKey]['post_convert_time'] = Phpfox::getLib('date')->convertTime($aRow['time_stamp'],
                'core.global_update_time');
            if (Phpfox::hasCallback($aRow['type_id'], 'getCommentItemName')) {
                $aRows[$iKey]['item_name'] = Phpfox::callback($aRow['type_id'] . '.getCommentItemName');
            } else {
                $aRows[$iKey]['item_name'] = '';
            }
            $aRows[$iKey]['extra_data'] = $this->getExtraByComment($aRow['comment_id']);
        }

        (($sPlugin = Phpfox_Plugin::get('comment.service_comment_get__end')) ? eval($sPlugin) : false);

        return array($iCnt, $aRows);
    }

    public function getExtraByComment($iCommentId, $bGetDeleted = false, $sType = '')
    {
        if (!$iCommentId) {
            return false;
        }
        $aExtra = db()->select('*')
            ->from(':ynccomment_comment_extra')
            ->where('comment_id =' . (int)$iCommentId . (!$bGetDeleted ? ' AND is_deleted = 0' : '') . (!empty($sType) ? ' AND extra_type = \''.$sType.'\'' : ''))
            ->execute('getRow');
        if (count($aExtra)) {
            if ($aExtra['extra_type'] == 'preview') {
                $aExtra['params'] = json_decode($aExtra['params'], true);
                if (!empty($aExtra['params']['link'])) {
                    $aExtra['params']['actual_link'] = $aExtra['params']['link'];
                    if (Phpfox::getParam('core.warn_on_external_links')) {
                        if (!preg_match('/' . preg_quote(Phpfox::getParam('core.host')) . '/i',
                            $aExtra['params']['link'])
                        ) {
                            $aExtra['params']['actual_link'] = Phpfox_Url::instance()->makeUrl('core.redirect',
                                array('url' => Phpfox_Url::instance()->encode($aExtra['params']['link'])));
                        }
                    }
                } else {
                    $aExtra['params']['actual_link'] = '';
                }
            } elseif ($aExtra['extra_type'] == 'sticker') {
                $aSticker = Phpfox::getService('ynccomment.stickers')->getStickerById($aExtra['item_id']);
                if ($aSticker) {
                    $aExtra['image_path'] = $aSticker['image_path'];
                    $aExtra['server_id'] = $aSticker['server_id'];
                    $aExtra['full_path'] = $aSticker['full_path'];
                }
            }
        }
        return $aExtra;
    }

    /**
     * @param string $sType
     * @param int $iItemId
     * @param int $iLimit
     * @param null $mPager
     * @param null $iCommentId
     * @param null $iTimeStamp
     * @param string $sPrefix
     *
     * @return array
     */
    public function getCommentsForFeed(
        $sType,
        $iItemId,
        $iLimit = 2,
        $mPager = null,
        $iCommentId = null,
        $sPrefix = '',
        $iTimeStamp = null
    ) {
        if ($iCommentId === null) {
            if ($mPager !== null && !$iTimeStamp) {
                $this->database()->limit(Phpfox_Request::instance()->getInt('page'), $iLimit, $mPager);
            } else {
                $this->database()->limit($iLimit);
            }
        }

        if ($iCommentId !== null) {
            $sWhere = 'c.comment_id = ' . (int)$iCommentId;
        } else {
            if ($sType == 'app') {
                $sWhere = 'c.parent_id = 0 AND c.type_id = \'' . $this->database()->escape($sType) . '\' AND c.item_id = ' . (int)$iItemId . ' AND c.view_id = 0 AND c.feed_table = "' . $sPrefix . 'feed';
            } else {
                $sWhere = 'c.parent_id = 0 AND c.type_id = \'' . $this->database()->escape($sType) . '\' AND c.item_id = ' . (int)$iItemId . ' AND c.view_id = 0';
            }

        }
        if ($iTimeStamp) {
            $sWhere .= Phpfox::getParam('comment.newest_comment_on_top') ? ' AND c.time_stamp > ' . (int)$iTimeStamp : ' AND c.time_stamp < ' . (int)$iTimeStamp;
        }

        $this->database()->where($sWhere);

        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l',
                    'l.type_id = \'feed_mini\' AND l.item_id = c.comment_id AND l.user_id = ' . Phpfox::getUserId());
        }
        if (Phpfox::getParam('comment.newest_comment_on_top')) {
            Phpfox::getLib('database')->order('c.time_stamp ASC');
        } else {
            Phpfox::getLib('database')->order('c.time_stamp DESC');
        }
        $aFeedComments = $this->database()->select('c.*, ' . (Phpfox::getParam('core.allow_html') ? "ct.text_parsed" : "ct.text") . ' AS text, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('comment'), 'c')
            ->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->execute('getSlaveRows');

        $aComments = array();
        if (count($aFeedComments)) {
            foreach ($aFeedComments as $iFeedCommentKey => $aFeedComment) {
                $aFeedComments[$iFeedCommentKey]['extra_data'] = $this->getExtraByComment($aFeedComment['comment_id']);
                $aFeedComments[$iFeedCommentKey]['is_hidden'] = $this->checkHiddenComment($aFeedComment['comment_id'],
                    Phpfox::getUserId());
                $aFeedComments[$iFeedCommentKey]['total_hidden'] = 1;
                $aFeedComments[$iFeedCommentKey]['hide_ids'] = $aFeedComment['comment_id'];
                $aFeedComments[$iFeedCommentKey]['hide_this'] = $aFeedComments[$iFeedCommentKey]['is_hidden'];
                if ($aFeedComments[$iFeedCommentKey - 1]['is_hidden'] && $aFeedComments[$iFeedCommentKey]['is_hidden']) {
                    $aFeedComments[$iFeedCommentKey - 1]['hide_this'] = false;
                    $aFeedComments[$iFeedCommentKey]['hide_ids'] = $aFeedComments[$iFeedCommentKey - 1]['hide_ids'] . ',' . $aFeedComment['comment_id'];
                    $aFeedComments[$iFeedCommentKey]['total_hidden'] = $aFeedComments[$iFeedCommentKey - 1]['total_hidden'] + 1;
                }
                $aFeedComments[$iFeedCommentKey]['post_convert_time'] = Phpfox::getLib('date')->convertTime($aFeedComment['time_stamp'],
                    'core.global_update_time');

                if (Phpfox::getParam('comment.comment_is_threaded')) {
                    $aFeedComments[$iFeedCommentKey]['children'] = $aFeedComment['child_total'] > 0 ? $this->_getChildren($aFeedComment['comment_id'],
                        $sType, $iItemId, $iCommentId) : [];
                }
                if (!setting('ynccomment.ynccomment_show_replies_on_comment')) {
                    $aFeedComments[$iFeedCommentKey]['last_reply'] = Phpfox::getService('ynccomment')->getLastChild($aFeedComment['comment_id'],
                        $aFeedComment['type_id'], $aFeedComment['item_id']);
                }
            }

            $aComments = array_reverse($aFeedComments);
        }

        return $aComments;
    }

    public function checkHiddenComment($iCommentId, $iUserId)
    {
        $iHide = db()->select('hide_id')
            ->from(':ynccomment_hide')
            ->where('comment_id = ' . (int)$iCommentId . ' AND user_id =' . (int)$iUserId)
            ->execute('getField');
        if ($iHide) {
            return true;
        }
        return false;
    }

    /**
     * @param int $iParentId
     * @param string $sType
     * @param int $iItemId
     * @param null $iCommentId
     * @param int $iCnt
     *
     * @return array
     */
    private function _getChildren(
        $iParentId,
        $sType,
        $iItemId,
        $iCommentId = null,
        $iCnt = 0,
        $iTimStamp = null,
        $iMaxTime = null,
        $iLimit = null
    ) {
        if ($iLimit != null) {
            $this->database()->limit($iLimit);
        }
        $iTotalComments = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('comment'), 'c')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->where('c.parent_id = ' . (int)$iParentId . ' AND c.type_id = \'' . $this->database()->escape($sType) . '\' AND c.item_id = ' . (int)$iItemId . ' AND c.view_id = 0' . ($iTimStamp != null ? ' AND c.time_stamp > ' . (int)$iTimStamp : '') . ($iMaxTime != null ? ' AND c.time_stamp <= ' . (int)$iMaxTime : ''))
            ->execute('getSlaveField');
        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l',
                    'l.type_id = \'feed_mini\' AND l.item_id = c.comment_id AND l.user_id = ' . Phpfox::getUserId());
        }

        if ($iCommentId === null) {
            $this->database()->limit(Phpfox::getParam('comment.thread_comment_total_display'));
        } elseif ($iLimit != null) {
            $this->database()->limit($iLimit);
        }
        $aFeedComments = $this->database()->select('c.*, ' . (Phpfox::getParam('core.allow_html') ? "ct.text_parsed" : "ct.text") . ' AS text, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('comment'), 'c')
            ->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->where('c.parent_id = ' . (int)$iParentId . ' AND c.type_id = \'' . $this->database()->escape($sType) . '\' AND c.item_id = ' . (int)$iItemId . ' AND c.view_id = 0' . ($iTimStamp != null ? ' AND c.time_stamp > ' . (int)$iTimStamp : '') . ($iMaxTime != null ? ' AND c.time_stamp <= ' . (int)$iMaxTime : ''))
            ->order('c.time_stamp ASC')
            ->execute('getSlaveRows');

        $iCnt++;
        if (count($aFeedComments)) {
            foreach ($aFeedComments as $iFeedCommentKey => $aFeedComment) {
                if ($iTimStamp != null || !setting('ynccomment.ynccomment_show_replies_on_comment')) {
                    $aFeedComments[$iFeedCommentKey]['is_loaded_more'] = true;
                }
                $aFeedComments[$iFeedCommentKey]['iteration'] = $iCnt;
                $aFeedComments[$iFeedCommentKey]['extra_data'] = $this->getExtraByComment($aFeedComment['comment_id']);
                $aFeedComments[$iFeedCommentKey]['is_hidden'] = $this->checkHiddenComment($aFeedComment['comment_id'],
                    Phpfox::getUserId());
                $aFeedComments[$iFeedCommentKey]['total_hidden'] = 1;
                $aFeedComments[$iFeedCommentKey]['hide_ids'] = $aFeedComment['comment_id'];
                $aFeedComments[$iFeedCommentKey]['hide_this'] = $aFeedComments[$iFeedCommentKey]['is_hidden'];
                if ($aFeedComments[$iFeedCommentKey - 1]['is_hidden'] && $aFeedComments[$iFeedCommentKey]['is_hidden']) {
                    $aFeedComments[$iFeedCommentKey - 1]['hide_this'] = false;
                    $aFeedComments[$iFeedCommentKey]['hide_ids'] = $aFeedComments[$iFeedCommentKey - 1]['hide_ids'] . ',' . $aFeedComment['comment_id'];
                    $aFeedComments[$iFeedCommentKey]['total_hidden'] = $aFeedComments[$iFeedCommentKey - 1]['total_hidden'] + 1;
                }
                $aFeedComments[$iFeedCommentKey]['post_convert_time'] = Phpfox::getLib('date')->convertTime($aFeedComment['time_stamp'],
                    'core.global_update_time');
                $aFeedComments[$iFeedCommentKey]['children'] = $this->_getChildren($aFeedComment['comment_id'], $sType,
                    $iItemId, $iCommentId, $iCnt);
            }
        }

        return [
            'total' => (int)($iTotalComments - Phpfox::getParam('comment.thread_comment_total_display')),
            'comments' => $aFeedComments
        ];
    }

    public function getLastChild($iCommentId, $sType, $iItemId)
    {
        return $this->database()->select('c.*, ' . (Phpfox::getParam('core.allow_html') ? "ct.text_parsed" : "ct.text") . ' AS text, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('comment'), 'c')
            ->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->where('c.parent_id = ' . (int)$iCommentId . ' AND c.type_id = \'' . $this->database()->escape($sType) . '\' AND c.item_id = ' . (int)$iItemId . ' AND c.view_id = 0')
            ->order('c.time_stamp DESC')
            ->execute('getRow');
    }

    public function loadMoreChild($iParentId, $sType, $iItemId, $iTimeStamp, $iMaxTime, $iLimit)
    {
        $aChilds = $this->_getChildren($iParentId, $sType, $iItemId, $iParentId, 0, $iTimeStamp, $iMaxTime, $iLimit);
        if ($aChilds) {
            return $aChilds['comments'];
        }
        return false;

    }

    /**
     * Get a Comment for edit
     *
     * @param int $iCommentId
     *
     * @return array
     */
    public function getCommentForEdit($iCommentId)
    {
        (($sPlugin = Phpfox_Plugin::get('comment.service_comment_getcommentforedit')) ? eval($sPlugin) : false);

        $aComment = $this->database()->select('cmt.*, comment_text.text AS text')
            ->from($this->_sTable, 'cmt')
            ->join(Phpfox::getT('comment_text'), 'comment_text', 'comment_text.comment_id = cmt.comment_id')
            ->where('cmt.comment_id = ' . (int)$iCommentId)
            ->execute('getSlaveRow');
        $aComment['extra_data'] = $this->getExtraByComment($iCommentId);
        return $aComment;
    }

    public function getUsersForMention()
    {
        //Get friends
        $aFriends = Phpfox::getService('friend')->getFromCache();

        //Get groups & pages
        $aPages = [];
        if (Phpfox::isModule('pages')) {
            $aPages = $this->database()->select(Phpfox::getUserField() . ', p.item_type as page_type')
                ->from(Phpfox::getT('user'), 'u')
                ->join(Phpfox::getT('pages'), 'p', 'u.profile_page_id = p.page_id')
                ->where('u.profile_page_id > 0 AND p.item_type = 0')
                ->order('u.last_activity DESC')
                ->execute('getSlaveRows');
        }

        $aGroups = [];
        if (Phpfox::isModule('groups')) {
            $sExtraCond = 'p.item_type = 1 AND u.profile_page_id > 0';
            if (Phpfox::hasCallback(Phpfox::getService('groups.facade')->getItemType(), 'getExtraBrowseConditions')
            ) {
                $sExtraCond .= Phpfox::callback(Phpfox::getService('groups.facade')->getItemType() . '.getExtraBrowseConditions',
                    'p');
            }
            $aGroups = $this->database()->select(Phpfox::getUserField() . ', p.item_type as page_type')
                ->from(Phpfox::getT('user'), 'u')
                ->join(Phpfox::getT('pages'), 'p', 'u.profile_page_id = p.page_id')
                ->where($sExtraCond)
                ->order('u.last_activity DESC')
                ->execute('getSlaveRows');
        }
        $aPages = array_merge($aPages, $aGroups);

        $aProcessedPages = [];
        foreach ($aPages as $iKey => $aPage) {
            $aPage['full_name'] = html_entity_decode(Phpfox::getLib('parse.output')->split($aPage['full_name'], 20),
                null, 'UTF-8');
            $aPage['user_profile'] = Phpfox::getService('pages')->getUrl($aPage['profile_page_id'], '');
            $aPage['is_page'] = ($aPage['profile_page_id'] ? true : false);
            $aPage['user_image'] = Phpfox::getLib('image.helper')->display(array(
                    'user' => $aPage,
                    'suffix' => '_50_square',
                    'max_height' => 32,
                    'max_width' => 32,
                    'return_url' => true
                )
            );
            $aPage['user_image_actual'] = Phpfox::getLib('image.helper')->display(array(
                    'user' => $aPage,
                    'suffix' => '_50_square',
                    'max_height' => 32,
                    'max_width' => 32
                )
            );
            $aProcessedPages[] = $aPage;
        }
        // Current user
        $aUser = Phpfox::getUserBy();
        if(is_array($aUser) && !empty($aUser)) {
            $aUser = $this->addMoreUserInfo($aUser);
            $aUser['is_you'] = true;
            array_push($aFriends, $aUser);
        }
        return array_merge($aProcessedPages, $aFriends);
    }

    public function addMoreUserInfo($aUser)
    {
        $aUser['user_profile'] = Phpfox_Url::instance()->makeUrl($aUser['user_name']);
        $aUser['full_name'] = html_entity_decode(Phpfox::getLib('parse.output')->split($aUser['full_name'], 20), null,
            'UTF-8');
        $aUser['user_profile'] = ($aUser['profile_page_id'] ? Phpfox::getService('pages')->getUrl($aUser['profile_page_id'],
            '', $aUser['user_name']) : Phpfox_Url::instance()->makeUrl($aUser['user_name']));
        $aUser['is_page'] = ($aUser['profile_page_id'] ? true : false);
        $aUser['user_image'] = Phpfox::getLib('image.helper')->display(array(
                'user' => $aUser,
                'suffix' => '_50_square',
                'max_height' => 32,
                'max_width' => 32,
                'return_url' => true
            )
        );
        $aUser['user_image_actual'] = Phpfox::getLib('image.helper')->display(array(
                'user' => $aUser,
                'suffix' => '_50_square',
                'max_height' => 32,
                'max_width' => 32
            )
        );
        $aUser['has_image'] = isset($aUser['user_image']) && $aUser['user_image'];
        return $aUser;
    }
}