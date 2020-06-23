<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Controller_View extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getLib('setting')->setParam('comment.load_delayed_comments_items',false);
        #View type
        $sView = $this->_getView();
        $this->setParam('sView', $sView);

        #Get contest
        $aCallback = $this->getParam('aCallback', false);
        $iContestId = $this->request()->getInt(($aCallback !== false ? $aCallback['request'] : 'req2'));
        Phpfox::getService('contest.contest.process')->checkAndUpdateStatusOfAContest($iContestId);

        $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId, $bIsCache = false);

        if (!$aContest['can_view_browse_contest'] || $aContest['is_deleted'] == 1)
        {
            $this->url()->send('privacy.invalid', array());
        }
        $this->template()->setHeader([
                         'magnific-popup.css' => 'module_contest',
                         'jquery.magnific-popup.js' => 'module_contest',
                                     ]);
        $this->_deleteNotification();

        $aContest = Phpfox::getService('contest.contest')->implementsContestFields($aContest);

        // Increment the view counter
        $bUpdateCounter = false;
        if (Phpfox::isModule('track'))
        {
            if(!$aContest['is_viewed']) {
                $bUpdateCounter = true;
                Phpfox::getService('track.process')->add('contest', $aContest['contest_id']);
            }
            else {
                if (!Phpfox::getParam('track.unique_viewers_counter')){
                    $bUpdateCounter = true;
                    Phpfox::getService('track.process')->add('contest', $aContest['contest_id']);
                }
                else
                    Phpfox::getService('track.process')->update('contest', $aContest['contest_id']);
            }
        }
        else {
            $bUpdateCounter = true;
        }

        if($bUpdateCounter)
            $aContest['total_view'] = Phpfox::getService('contest.contest.process')->viewContest($aContest['contest_id'], $aContest['total_view']);

        $this->setParam("aContest", $aContest);
        
        $is_hidden_action = Phpfox::getService('contest.permission')->canHideAction($aContest);

        $sTypeName = Phpfox::getService('contest.constant')->getContestTypeNameByTypeId($aContest['type']);

        //to make facebook know the image
        $sImageUrl = sprintf(Phpfox::getParam('core.url_pic') . 'contest/' . $aContest['image_path'], '');
        $sImageUrl = str_replace(PHPFOX_DIR, Phpfox::getParam('core.path'), $sImageUrl);
        $sImageUrl = Phpfox::getLib('cdn')->getUrl($sImageUrl, $aContest['server_id']);

        #Switch type of view
        #View entry detail
        if ($sView == 'entry')
        {
            $iEntryId = $this->request()->get('entry');

            $aEntry = Phpfox::getService('contest.entry')->getContestEntryById($iEntryId);

            if (!$aEntry)
            {
                Phpfox::getLib('url')->send('subscribe');
            }

            $this->_entryDeleteNotification();

            if (!$aEntry['can_view_entry_detail'])
            {
                Phpfox::getLib('url')->send('subscribe');
            }

            // Increment the view counter
            $bUpdateCounterEntry = false;
            if (Phpfox::isModule('track'))
            {
                if(!$aEntry['is_viewed']) {
                    $bUpdateCounterEntry = true;
                    Phpfox::getService('track.process')->add('contest_entry', $aEntry['entry_id']);
                }
                else {
                    if (!Phpfox::getParam('track.unique_viewers_counter')){
                        $bUpdateCounterEntry = true;
                        Phpfox::getService('track.process')->add('contest_entry', $aEntry['entry_id']);
                    }
                    else
                        Phpfox::getService('track.process')->update('contest_entry', $aEntry['entry_id']);
                }
            }
            else {
                $bUpdateCounterEntry = true;
            }

            if($bUpdateCounterEntry)
                $aEntry['total_view'] = Phpfox::getService('contest.entry.process')->viewEntry($aEntry['entry_id'], $aEntry['total_view']);

            if ($aEntry['image_path'] && $aContest['type'] != 1) {
                $sImageUrl = Phpfox::getLib('image.helper')->display(array('server_id' => $aEntry['server_id']
                , 'path' => 'core.url_pic'
                , 'file' => $aEntry['image_path']
                , 'suffix' => ''
                , 'return_url' => true));

                if (!file_exists($sImageUrl)) {
                    $sImageUrl = Phpfox::getLib('image.helper')->display(array('server_id' => $aEntry['server_id']
                    , 'path' => 'core.url_pic'
                    , 'file' => $aEntry['image_path']
                    , 'suffix' => '_200'
                    , 'return_url' => true));
                }

                $this->template()->assign('sImageUrl', $sImageUrl);
            }

            $this->setParam('aEntry', $aEntry);

            $aEntry = $this->_entryImplementFields($aEntry);

            $sTemplateViewPath = Phpfox::getService('contest.entry')->getTemplateViewPath($aEntry['type']);

            //display box comment,like and share
            $this->_entryBoxComment($aEntry);

            $this->template()->setBreadCrumb(Phpfox::getLib('parse.output')->shorten($aEntry['contest_name'], 40, '...'), $this->url()->permalink('contest', $aContest['contest_id'], $aContest['contest_name']), true)->setBreadCrumb('', '', true)->setEditor(array('load' => 'simple'))->assign(array(
                'aEntry' => $aEntry,
                'sTemplateViewPath' => $sTemplateViewPath,
                'core_path' => Phpfox::getParam('core.path')));

            $this->template()->setMeta(array(
                'keywords' => $this->template()->getKeywords($aEntry['title']),
                'description' => $aEntry['summary'],
                'og:description' => $aEntry['summary'],
                'og:title' => $aEntry['title'],
                'og:url' => $aEntry['bookmark_url']));
            $this->template()->setHeader('<script type="text/javascript">$Behavior.ynContestRemoveBlockH1 = function () { $("#content ._block_h1").remove();} </script>');
        }
        else
        {
            #Default view of contest
            if ($sView == 'default')
            {
                $aSearchNumber = array(
                    12,
                    24,
                    36,
                    48);
                $sActionUrl = $this->url()->makeUrl('contest/'.$iContestId, array('view' => $this->request()->get('view')));

                // remove search in view page                    
                $this->search()->set(array(
                    'type' => 'entry',
                    'field' => 'en.entry_id',
                    'search' => 'search',
                    'search_tool' => array(
                        'table_alias' => 'en',
                        'search' => array(
                            'action' => $sActionUrl,
                            'default_value' => _p('contest.search_entries'),
                            'name' => 'search',
                            'field' => 'en.title'),
                        'sort' => array(
                            'latest' => array('en.time_stamp', _p('contest.lastest')),
                            'most-viewed' => array('en.total_view', _p('contest.most_viewed')),
                            'most-vote' => array('en.total_vote', _p('contest.most_voted')),
                            'most-liked' => array('en.total_like', _p('contest.most_liked')),
                            ),
                        'show' => $aSearchNumber)));

                $this->search()->setCondition('AND en.contest_id = '.$aContest['contest_id']);

                if ($is_hidden_action)
                {
                    $this->search()->setCondition('AND en.status = 1');
                }

                $aBrowseParams = array(
                    'module_id' => 'contest',
                    'alias' => 'en',
                    'field' => 'entry_id',
                    'table' => Phpfox::getT('contest_entry'),
                    'hide_view' => array('my'));

                $iSize = $this->search()->getDisplay();

                $iPage =  $this->search()->getPage();
                $iPage = ($iPage == 0 || $iPage == 1)?1:($iPage);

                list($iPagePrev,$iPageNext,$bDisablePrev,$bDisableNext,$bHideAll) = Phpfox::getService('contest.helper')->buildPaging($iPage,$iSize,0,true,$aBrowseParams);
                
                /*set up paging*/
                $sCurrentUrl = Phpfox::getLib('url')->makeUrl('current');
                $sCurrentUrl = preg_replace("/&page=[0-9]+/", "", $sCurrentUrl);
                $sCurrentUrl = preg_replace("/\?page=[0-9]+/", "", $sCurrentUrl);
        
                if($this->request()->get('s') != ''){
                    $sCurrentUrlPagePrev = $sCurrentUrl.'&page='.$iPagePrev;
                    $sCurrentUrlPageNext = $sCurrentUrl.'&page='.$iPageNext;
                }
                else{
                    $sCurrentUrlPagePrev = $sCurrentUrl.'page_'.$iPagePrev;
                    $sCurrentUrlPageNext = $sCurrentUrl.'page_'.$iPageNext;
                }

                 $this->template()->assign(array(
                    'iPage' => $this->search()->getPage(),
                    'sCurrentUrl' => $sCurrentUrl,
                    'sCurrentUrlPagePrev' => $sCurrentUrlPagePrev,
                    'sCurrentUrlPageNext' => $sCurrentUrlPageNext,
                    'bDisablePrev' => $bDisablePrev,
                    'bDisableNext' => $bDisableNext,
                    'bHideAll' => $bHideAll,
                     'sUrlNoImagePhoto'	=> Phpfox::getParam('core.path_file').'module/contest/static/image/no_photo_small.png'
                ));
                /*set up paging*/


                $this->search()->browse()->params($aBrowseParams)->execute();
                $aEntries = $this->search()->browse()->getRows();

                Phpfox::getLib('pager')->set(array(
                    'page' => $this->search()->getPage(),
                    'size' => $this->search()->getDisplay(),
                    'count' => $this->search()->browse()->getCount()));

               
                foreach ($aEntries as $key => $aEntry)
                {
										$aEntry = array_merge($aContest, $aEntry);
										$aEntry['contest_user_id'] = $aContest['user_id'];

                    $aEntry['status_entry'] = $aEntry['status'];
                    $aEntry['approve'] = ($aEntry['status'] == 1) ? 0 : 1;
                    $aEntry['deny'] = ($aEntry['status'] == 2) ? 0 : 1;
                    $is_entry_winning = Phpfox::getService("contest.entry")->CheckExistEntryWinning($aEntry['entry_id']);
                    $aEntry['winning'] = ($aEntry['contest_status'] == 5 && $is_entry_winning == 0) ? 1 : 0;
                    $aEntry['offaction'] = 0;
                    if ($aEntry['contest_user_id'] != Phpfox::getUserId() && !PHpfox::isAdmin())
                    {
                        $aEntry['offaction'] = 1;
                    }

                    $aEntry = Phpfox::getService('contest.entry')->retrieveEntryPermission($aEntry);

                    $aEntries[$key] = $aEntry;
                }

                $this->template()->assign(array('aEntries' => $aEntries, 'corepath' => phpfox::getParam('core.path')));

                $global_moderation = array(
                    'name' => 'contestentry',
                    'ajax' => 'contest.moderateEntry',
                    'menu' => array(
                        array('phrase' => _p('contest.approve'), 'action' => 'approve'),
                        array('phrase' => _p('contest.deny'), 'action' => 'deny'),
                        ));

                if ($aContest['contest_status'] == 5)
                {
                    $global_moderation['menu'][] = array('phrase' => _p('contest.set_as_winning_entries'), 'action' => 'set_as_winning_entries');
                }

                $this->setParam('global_moderation', $global_moderation);
                  $this->template()->setHeader(array(
                            '<script type="text/javascript">$Behavior.clearModerationDetailContest = function() {$Core.moderationLinkClear();}</script>'
                  ));
            }

            #View winning entries
            if ($sView == 'winning')
            {
                $global_moderation = array(
                    'name' => 'contestentry',
                    'ajax' => 'contest.moderateEntry',
                    'menu' => array(array('phrase' => 'Delete from the list', 'action' => 'delete'), ));

                $this->setParam('global_moderation', $global_moderation);
            }

            #For announcement
            $aValidation = array('headline' => array('def' => 'required', 'title' => _p('contest.fill_headline_for_announcement')), 'content' => array('def' => 'required', 'title' => _p('contest.add_content_to_announcement')));
            $oValid = Phpfox::getLib('validator')->set(array('sFormName' => 'core_js_contest_form', 'aParams' => $aValidation));

            if ($aVals = $this->request()->getArray('val'))
            {
                if ($oValid->isValid($aVals))
                {
                    $aVals['user_id'] = Phpfox::getUserId();
                    $aVals['contest_id'] = $aContest['contest_id'];
                    $iId = Phpfox::getService('contest.announcement.process')->add($aVals);
                }
            }

            $announcement = 0;
            if ($iId = $this->request()->get('announcement'))
            {
                $announcement = $iId;
            }

            #Comment container
            $aContest['bookmark_url'] = Phpfox::permalink('contest', $aContest['contest_id'], $aContest['contest_name']);
            $this->_boxComment($aContest);

            $this->template()->assign(array('announcement' => $announcement, 'sContestWarningMessage' => $this->_getContestWarningMessage($aContest)));

            $aValidatorPhrases = Phpfox::getService('contest.helper')->getPhrasesForValidator();
            $this->template()->setPhrase($aValidatorPhrases);
            $this->template()->setPhrase(['are_you_sure_info','yes','no']);

            $this->template()->setMeta(array(
                'keywords' => $this->template()->getKeywords($aContest['contest_name']),
                'description' => $aContest['short_description'],
                'og:description' => $aContest['short_description'],
                'og:title' => $aContest['contest_name'],
                'og:url' => $aContest['bookmark_url']));
        }

        $aContest['is_show_ending_soon_label'] = Phpfox::getService('contest.contest')->isShowContestEndingSoonLabel($aContest['contest_id']);

        $bIsShowRegisterService = false;
        if ($this->request()->get('registerservice') && Phpfox::getService('contest.permission')->canRegisterService($aContest['contest_id'], Phpfox::getUserId()))
        {
            $bIsShowRegisterService = true;
        }

        if ($sTypeName == 'music')
        {
            /*$this->template()->setHeader(array(
                'mediaelementplayer.min.css' => 'module_contest',
                'mejs-audio-skins.css' => 'module_contest',
                'mediaelement-and-player.min.js' => 'module_contest',
                'controller_player.js' => 'module_contest'
                ));*/
        }
        if ($sImageUrl) {
            $image_size = getimagesize($sImageUrl);
        } else {
            $image_size = array();
        }

        $this->template()
            ->setMeta('description', $aContest['contest_name'] . '.')
            ->setMeta('description', $aContest['short_description'] . '.')
            ->setMeta('description', $aContest['description'] . '.')
            ->setMeta('keywords', $this->template()->getKeywords($aContest['contest_name']))
            ->setMeta('og:image', $sImageUrl)
            ->setMeta('og:width', @$image_size[0])
            ->setMeta('og:height', @$image_size[1])
            ->setMeta('keywords', Phpfox::getParam('contest.contest_meta_keywords'))
            ->setMeta('description', Phpfox::getParam('contest.contest_meta_description'))
            ->assign(array(
            'aContest' => $aContest,
            'sView' => $sView,
            'is_hidden_action' => $is_hidden_action,
            'showaction' => true,
            'aContestStatus' => Phpfox::getService('contest.constant')->getAllContestStatus(),
            'bIsShowRegisterService' => $bIsShowRegisterService))->setMeta(['og:type' => 'website'])->setHeader(array(
            'yncontest.js' => 'module_contest',
            'block.css' => 'module_contest',
            'jquery.validate.js' => 'module_contest',
            'jquery/plugin/jquery.highlightFade.js' => 'static_script',
            'jquery/plugin/jquery.scrollTo.js' => 'static_script',
            'quick_edit.js' => 'static_script',
            'pager.css' => 'style_css',
            'feed.js' => 'module_feed',
            'sUrlNoImagePhoto'	=> Phpfox::getParam('core.path_file').'module/contest/static/image/no_photo_small.png',
        ));

        $this->template()->assign(array(
                'sAddThisKey' => Phpfox::getParam('core.addthis_pub_id'),
                'sAddThisBtn' => Phpfox::getParam('core.addthis_share_button')
            )
        );
        Phpfox::getService('contest.helper')->buildMenu();
            
    }

    private function _boxComment($aContest)
    {
        $this->setParam('aFeed', array(
            'comment_type_id' => 'contest',
            'privacy' => $aContest['privacy'],
            'comment_privacy' => $aContest['privacy_comment'],
            'like_type_id' => 'contest',
            'feed_is_liked' => isset($aContest['is_liked']) ? $aContest['is_liked'] : false,
            'feed_is_friend' => $aContest['is_friend'],
            'item_id' => $aContest['contest_id'],
            'user_id' => $aContest['user_id'],
            'total_comment' => $aContest['total_comment'],
            'total_like' => $aContest['total_like'],
            'feed_link' => $aContest['bookmark_url'],
            'feed_title' => $aContest['contest_name'],
            'feed_display' => 'view',
            'feed_total_like' => $aContest['total_like'],
            'report_module' => 'contest',
            'report_phrase' => _p('contest.report_this_contest_entry'),
            'time_stamp' => $aContest['time_stamp']));
    }

    private function _deleteNotification()
    {
        if (Phpfox::isUser() && Phpfox::isModule('notification'))
        {
            Phpfox::getService('contest.participant')->removeAllFavoriteByContestId($this->request()->getInt('req2'));
            Phpfox::getService('notification.process')->delete('comment_contest', $this->request()->getInt('req2'), Phpfox::getUserId());
            Phpfox::getService('notification.process')->delete('contest_notice_follower', $this->request()->getInt('req2'), Phpfox::getUserId());
            Phpfox::getService('notification.process')->delete('contest_notice_join', $this->request()->getInt('req2'), Phpfox::getUserId());
            Phpfox::getService('notification.process')->delete('contest_invited', $this->request()->getInt('req2'), Phpfox::getUserId());
            Phpfox::getService('notification.process')->delete('contest_like', $this->request()->getInt('req2'), Phpfox::getUserId());
            Phpfox::getService('notification.process')->delete('contest_notice_close', $this->request()->getInt('req2'), Phpfox::getUserId());
        }
    }

    private function _getContestWarningMessage($aContest)
    {
        $sStatus = Phpfox::getService('contest.constant')->getContestStatusNameByStatusId($aContest['contest_status']);

        if ($sStatus == 'pending')
        {
            return _p('contest.this_contest_is_pending_an_admins_approval');
        }

        if ($sStatus == 'denied')
        {
            return _p('contest.this_contest_was_denied');
        }

        if ($sStatus == 'closed')
        {
            return _p('contest.this_contest_is_closed');
        }

        if ($sStatus == 'on_going')
        {
            // contest has not started
            if ($aContest['begin_time'] > PHPFOX_TIME)
            {
                return _p('contest.this_contest_will_start_on_begin_time_please_revisit_later', array('begin_time' => $aContest['begin_time_parsed']));
            }

            // user can submit entry or not
            if ($aContest['start_time'] <= PHPFOX_TIME && $aContest['stop_time'] >= PHPFOX_TIME)
            {
                // 0 is unlimited, no need to warn
                if ($aContest['number_entry_max'] != 0 && Phpfox::getService('contest.participant')->isJoinedContest(Phpfox::getUserId(), $aContest['contest_id']))
                {
                    $iNumberOfRemainingSubmitTime = $aContest['number_entry_max'] - Phpfox::getService('contest.entry')->getNumberOfSumittedEntryInAContestOfUser($aContest['contest_id'], Phpfox::getUserId());

                    if ($iNumberOfRemainingSubmitTime <= 0)
                    {
                        return _p('contest.you_can_not_submit_you_have_reached_maximum_number_of_submitted_entry_number', array('number' => $aContest['number_entry_max']));
                    }
                    else
                    {
                        return _p('contest.you_can_submit_number_entry_more', array('number' => $iNumberOfRemainingSubmitTime));
                    }
                }
            }
        }

        return '';
    }

    private function _entryImplementFields($aEntry)
    {
        $format_datetime = 'M j, Y g:i a';
        $aEntry['bookmark_url'] = Phpfox::permalink('contest', $aEntry['contest_id'], $aEntry['contest_name']).'entry_'.$aEntry['entry_id'].'/';
        $aEntry['bitlyUrl'] = Phpfox::getService('contest.entry.process')->getShortBitlyUrl($aEntry['bookmark_url']);
        $aEntry['is_voted'] = Phpfox::getService('contest.entry.process')->isVoted(Phpfox::getUserId(), $aEntry['entry_id']);
        $aEntry['submit_date'] = Phpfox::getTime($format_datetime, $aEntry['time_stamp']);
        $aEntry['approve_date'] = Phpfox::getTime($format_datetime, $aEntry['approve_stamp']);
        $aEntry['previous'] = Phpfox::getService("contest.entry")->getContestEntryBesideId($aEntry['entry_id'], $aEntry['contest_id'], 'previous');
        $aEntry['next'] = Phpfox::getService("contest.entry")->getContestEntryBesideId($aEntry['entry_id'], $aEntry['contest_id'], 'next');
        $aEntry['approve'] = $aEntry['status_entry'] == 1 ? 0 : 1;
        $aEntry['deny'] = $aEntry['status_entry'] == 2 ? 0 : 1;
        $is_entry_winning = Phpfox::getService("contest.entry")->CheckExistEntryWinning($aEntry['entry_id']);
        $aEntry['winning'] = ($aEntry['contest_status'] == 5 && $is_entry_winning == 0) ? 1 : 0;
        $aEntry['offaction'] = 0;
        if ($aEntry['contest_user_id'] != Phpfox::getUserId() && !PHpfox::isAdmin())
        {
            $aEntry['offaction'] = 1;
        }
        if (!$aEntry['bitlyUrl'])
        {
            $aEntry['bitlyUrl'] = $aEntry['bookmark_url'];
        }
        if($aEntry['type'] == 1){
            $aEntry['blog_content'] = Phpfox::getLib('parse.output')->parse($aEntry['blog_content']);
        }

        return $aEntry;
    }

    private function _entryBoxComment($aEntry)
    {
        $this->setParam('aFeed', array(
            'comment_type_id' => 'contest_entry',
            'privacy' => $aEntry['privacy'],
            'comment_privacy' => $aEntry['privacy_comment'],
            'like_type_id' => 'contest_entry',
            'feed_is_liked' => isset($aEntry['is_liked']) ? $aEntry['is_liked'] : false,
            'feed_is_friend' => $aEntry['is_friend'],
            'item_id' => $aEntry['entry_id'],
            'user_id' => $aEntry['user_id'],
            'total_comment' => $aEntry['total_comment'],
            'total_like' => $aEntry['total_like'],
            'feed_link' => $aEntry['bookmark_url'],
            'feed_title' => $aEntry['title'],
            'feed_display' => 'view',
            'feed_total_like' => $aEntry['total_like'],
            'report_module' => 'contest',
            'report_phrase' => _p('contest.report_this_contest_entry'),
            'time_stamp' => $aEntry['time_stamp'],
            'type_id' => 'contest_entry'));
    }

    private function _entryDeleteNotification()
    {
        if (Phpfox::isUser() && Phpfox::isModule('notification'))
        {
            Phpfox::getService('notification.process')->delete('contest_entry_vote', $this->request()->getInt('req3'), Phpfox::getUserId());
            Phpfox::getService('notification.process')->delete('contest_entry_invited', $this->request()->getInt('req3'), Phpfox::getUserId());
            Phpfox::getService('notification.process')->delete('contest_entry_like', $this->request()->getInt('req3'), Phpfox::getUserId());
            Phpfox::getService('notification.process')->delete('comment_contest_entry', $this->request()->getInt('req3'), Phpfox::getUserId());
            Phpfox::getService('notification.process')->delete('contest_notice_approveentry', $this->request()->getInt('req3'), Phpfox::getUserId());
        }
    }

    private function _getView()
    {
        $sView = 'default';

        $stmpView = $this->request()->get('view');
        if ($stmpView == 'participants' || $stmpView == 'winning')
        {
            $sView = $stmpView;
        }

        $sAction = $this->request()->get('action');
        $iItemId = $this->request()->get('itemid');
        if ($sAction == 'add' || $iItemId)
        {
            $sView = 'add';
        }

        $iEntryId = $this->request()->get('entry');
        if ($iEntryId)
        {
            $sView = 'entry';
        }

        return $sView;
    }
}
