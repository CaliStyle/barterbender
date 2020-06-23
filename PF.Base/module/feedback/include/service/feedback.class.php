<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php

class FeedBack_Service_FeedBack extends Phpfox_Service
{

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('feedback');
    }

    public function getFeedbackPermissions(&$feedback) {
        $feedback['canAddPicture'] = (Phpfox::getUserParam('feedback.can_upload_pictures') && Phpfox::getUserId() == $feedback['user_id']) || phpfox::isAdmin();
        $feedback['canEdit'] = ((Phpfox::getUserParam('feedback.edit_own_feedback') && Phpfox::getUserId() == $feedback['user_id']) || Phpfox::getUserParam('feedback.edit_user_feedback'));
        $feedback['canDelete'] = (Phpfox::getUserParam('feedback.delete_own_feedback') && Phpfox::getUserId() == $feedback['user_id']) || Phpfox::getUserParam('feedback.delete_user_feedback');
        $feedback['canApprove'] = (Phpfox::getUserParam('feedback.can_approve_feedbacks') || Phpfox::getUserParam('feedback.delete_user_feedback')) && $feedback['is_approved'] == 0;
        $feedback['canDoAction'] = $feedback['canAddPicture'] || $feedback['canEdit'] || $feedback['canDelete'] || $feedback['canApprove'];
    }

    public function prepareTitle($sTitle, $bCleanOnly = false)
    {
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_feedback_preparetitle__start')) ? eval($sPlugin) : false);
        return Phpfox::getLib('parse.input')->prepareTitle('feedback', $sTitle, 'title_url', Phpfox::getUserId(), Phpfox::getT('feedback'), null, $bCleanOnly);
    }

    function prepareTitle1($sTitle, $bCleanOnly = false)
    {
        $sTitle = phpfox::getLib('url')->cleanTitle($sTitle);
        $sTitle = trim($sTitle);
        $sTitle = html_entity_decode($sTitle);
        $sTitle = preg_replace('~ ~', '-', $sTitle);
        $sTitle = preg_replace('~-+~', '-', $sTitle);
        $sTitle = strtolower($sTitle);
        return $sTitle;
    }

    public function getCount()
    {
        $count = $this->database()->select('count(*) as number')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->where('fb.is_approved = 0')
            ->execute('getField');
        return $count;
    }

    public function getFeedBacks($aConds = array(), $sSort = 'fb.time_stamp DESC', $iPage = '', $sLimit = '', $bCount = true)
    {
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_feedbacks_get__start')) ? eval($sPlugin) : false);
        $iCnt = ($bCount ? 0 : 1);
        $aRows = array();
        $con = array();

        if (isset($aConds) && !empty ($aConds)) {
            foreach ($aConds as $key => $c) {
                // $c = html_entity_decode( $c, ENT_QUOTES, "utf-8" );
                $c = str_replace('&#92;', '', $c);
                if ($c == "fb.feedback_category_id = NULL") {
                    $c = "fb.feedback_category_id = 0";
                    if (count($con) == 0)
                        $con[] = $c;
                    else
                        $con[] = ' AND ' . $c;
                }
                if ($c == "fb.feedback_status_id = NULL") {
                    $c = "fb.feedback_status_id = 0";
                    if (count($con) == 0)
                        $con[] = $c;
                    else
                        $con[] = ' AND ' . $c;
                }
                if (!strpos($c, 'All') && $c != 'All') {
                    if ($pos = strpos($c, 'LIKE')) {
                        if (count($con) == 0)
                            $con[] = $c;
                        else
                            $con[] = ' AND ' . $c;

                    } elseif (strpos($c, '=')) {
                        if (count($con) == 0)
                            $con[] = $c;
                        else
                            $con[] = ' AND ' . $c;
                    } elseif (strpos($c, '>') || strpos($c, '<')) {
                        if (count($con) == 0)
                            $con[] = $c;
                        else
                            $con[] = ' AND ' . $c;
                    } elseif (strpos($c, 'IN')) {
                        if (count($con) == 0)
                            $con[] = $c;
                        else
                            $con[] = ' AND ' . $c;
                    }
                }
            }
        }

        if ($bCount) {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('feedback'), 'fb')
                ->where($con)
                ->execute('getSlaveField');


        }
        if ($iCnt) {
            (($sPlugin = Phpfox_Plugin::get('feedback.service_feedback_get')) ? eval($sPlugin) : false);
            $aRows = $this->database()
                ->select('fb.feedback_id,fb.total_vote,fb.title,fb.title_url,fb.feedback_description,fb.feedback_status,fb.time_stamp,fb.is_featured,fb.total_comment,fb.full_name as visitor,fb.privacy,fc.name as category,fu.full_name,fser.name as serverity, fser.colour as feedback_serverity_color, fsta.name as status,fsta.colour as color,fb.votable')
                ->from(Phpfox::getT('feedback'), 'fb')
                ->leftjoin(Phpfox::getT('feedback_status'), 'fsta', 'fb.feedback_status_id=fsta.status_id')
                ->leftjoin(Phpfox::getT('feedback_category'), 'fc', 'fb.feedback_category_id = fc.category_id')
                ->leftjoin(Phpfox::getT('user'), 'fu', 'fb.user_id = fu.user_id')
                ->leftjoin(Phpfox::getT('feedback_serverity'), 'fser', 'fb.feedback_serverity_id = fser.serverity_id')
                ->where($con)
                ->order($sSort)
                ->limit($iPage, $sLimit, $iCnt)
                ->execute('getSlaveRows');


            foreach ($aRows as $k => $aRow) {
                $aRows[$k]['category'] = Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aRow['category']) ? _p($aRow['category']) : $aRow['category']);
                $aRows[$k]['serverity'] = Phpfox::getLib('locale')->convert($aRow['serverity']);
                $aRows[$k]['status'] = Phpfox::getLib('locale')->convert($aRow['status']);
            }
        }
        if (!$bCount) {
            return $aRows;
        }
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_feedback_get__end')) ? eval($sPlugin) : false);
        return array($iCnt, $aRows);

    }

    public function getPendingTotal()
    {
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_feedback_getpendingtotal')) ? eval($sPlugin) : false);

        return (int)$this->database()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('is_approved = 0')
            ->execute('getSlaveField');
    }


    public function get($aConds = array(), $sSort = 'fb.time_stamp DESC', $iPage = '', $sLimit = '', $bCount = true, $bNoQueryFriend = false)
    {

        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_feedbacks_get__start')) ? eval($sPlugin) : false);

        $iCnt = ($bCount ? 0 : 1);
        $aRows = array();
        $con = array();
        if (isset($aConds) && !empty ($aConds)) {
            foreach ($aConds as $key => $c) {
                $c = str_replace('&#92;', '', $c);
                //$c = $this->database()->escape($c);
                // $c = html_entity_decode( $c, ENT_QUOTES, "utf-8" );
                if ($c == "fb.feedback_category_id = NULL") {
                    $c = "fb.feedback_category_id = 0";
                    if (count($con) == 0)
                        $con[] = $c;
                    else
                        $con[] = ' AND ' . $c;
                }
                if ($c == "fb.feedback_status_id = NULL") {
                    $c = "fb.feedback_status_id = 0";
                    if (count($con) == 0)
                        $con[] = $c;
                    else
                        $con[] = ' AND ' . $c;
                }
                if (!strpos($c, 'All') && $c != 'All') {
                    if ($pos = strpos($c, 'LIKE')) {
                        if (count($con) == 0)
                            $con[] = $c;
                        else
                            $con[] = ' AND ' . $c;

                    } elseif (strpos($c, '=')) {
                        if (count($con) == 0)
                            $con[] = $c;
                        else
                            $con[] = ' AND ' . $c;
                    } elseif (strpos($c, '>') || strpos($c, '<')) {
                        if (count($con) == 0)
                            $con[] = $c;
                        else
                            $con[] = ' AND ' . $c;
                    } elseif (strpos($c, 'IN')) {
                        if (count($con) == 0)
                            $con[] = $c;
                        else
                            $con[] = ' AND ' . $c;
                    }
                }
            }
        }
        if ($sSort == "fb.is_featured DESC") {
            $sSort = "fb.time_stamp DESC";
        }
        if ($bCount) {
            if ($bNoQueryFriend) {
                $iCnt = $this->database()->select('COUNT(*)')
                    ->from(Phpfox::getT('feedback'), 'fb')
                    ->where($con)
                    ->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = fb.user_id AND friends.friend_user_id = ' . Phpfox::getUserId())
                    ->execute('getSlaveField');
            } else {
                $iCnt = $this->database()->select('COUNT(*)')
                    ->from(Phpfox::getT('feedback'), 'fb')
                    ->where($con)
                    ->execute('getSlaveField');
            }
        }
        if ($iCnt) {

            (($sPlugin = Phpfox_Plugin::get('feedback.service_feedback_get')) ? eval($sPlugin) : false);
            if ($bNoQueryFriend) {
                $aRows = $this->database()
                    ->select('l.like_id AS is_liked, fb.feedback_id,fb.title,fb.feedback_description,fb.title_url, fb.total_like, fb.feedback_status,fb.time_stamp,fb.total_attachment,fb.total_vote,fb.total_comment,fb.total_view,fb.is_featured,fb.full_name as visitor,fb.privacy,fc.name as category_name,fc.name_url as category_url,fu.user_id,fu.full_name,fu.user_name,fs.name as status,fs.colour as color')
                    ->from(Phpfox::getT('feedback'), 'fb')
                    ->leftjoin(Phpfox::getT('feedback_category'), 'fc', 'fb.feedback_category_id = fc.category_id')
                    ->leftjoin(Phpfox::getT('user'), 'fu', 'fb.user_id = fu.user_id')
                    ->leftjoin(Phpfox::getT('feedback_status'), 'fs', 'fb.feedback_status_id = fs.status_id')
                    ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feedback\' AND l.item_id = fb.feedback_id AND l.user_id = ' . Phpfox::getUserId())
                    ->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = fb.user_id AND friends.friend_user_id = ' . Phpfox::getUserId())
                    ->where($con)
                    ->order($sSort)
                    ->limit($iPage, $sLimit, $iCnt)
                    ->execute('getSlaveRows');
            } else {
                $aRows = $this->database()
                    ->select('l.like_id AS is_liked, fb.feedback_id,fb.title,fb.feedback_description,fb.title_url, fb.total_like, fb.feedback_status,fb.time_stamp,fb.total_attachment,fb.total_vote,fb.total_comment,fb.total_view,fb.is_featured,fb.full_name as visitor,fb.privacy,fc.name as category_name,fc.name_url as category_url,fu.user_id,fu.full_name,fu.user_name,fs.name as status,fs.colour as color')
                    ->from(Phpfox::getT('feedback'), 'fb')
                    ->leftjoin(Phpfox::getT('feedback_category'), 'fc', 'fb.feedback_category_id = fc.category_id')
                    ->leftjoin(Phpfox::getT('user'), 'fu', 'fb.user_id = fu.user_id')
                    ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feedback\' AND l.item_id = fb.feedback_id AND l.user_id = ' . Phpfox::getUserId())
                    ->leftjoin(Phpfox::getT('feedback_status'), 'fs', 'fb.feedback_status_id = fs.status_id')
                    ->where($con)
                    ->order($sSort)
                    ->limit($iPage, $sLimit, $iCnt)
                    ->execute('getSlaveRows');
            }

        }

        if (!$bCount) {
            return $aRows;
        }
        return array($iCnt, $aRows);
    }


    public function getSearch($aConds = array(), $sSort = 'fb.time_stamp DESC')
    {
        if (isset($aConds) && !empty ($aConds)) {
            foreach ($aConds as $key => $c) {
                if ($c == "fb.feedback_category_id = NULL") {
                    if ($key == 0) {
                        $aConds[$key] = "fb.feedback_category_id = 0";
                        if (isset($aConds[1])) {
                            if (!strpos($aConds[1], 'LIKE')) {
                                $aConds[$key] = "fb.feedback_category_id = 0 AND ";
                            }
                        }
                    } else {
                        $aConds[$key] = "fb.feedback_category_id = 0";
                        if (isset($aConds[$key + 1])) {
                            $aConds[$key] = "fb.feedback_category_id = 0 AND ";
                        }
                    }
                } else if (!strpos($aConds[1], 'LIKE')) {
                    if ($key == 0) {
                        if (isset($aConds[1])) {
                            if (!strpos($aConds[1], 'LIKE')) {
                                $aConds[$key] = $c . " AND ";
                            }
                        }
                    } else {
                        if (isset($aConds[$key + 1])) {
                            if (!strpos($aConds[$key + 1], 'LIKE')) {
                                $aConds[$key] = $c . " AND ";
                            }
                        }
                    }
                }
            }
        }
        $aRows = $this->database()->select('fb.feedback_id')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->where($aConds)
            ->order($sSort)
            ->execute('getSlaveRows');

        $aSearchIds = array();
        foreach ($aRows as $aRow) {
            $aSearchIds[] = $aRow['feedback_id'];
        }

        return $aSearchIds;
    }

    public function getExtra(&$sFeedBacks)
    {
        $oFilterOutput = Phpfox::getLib('parse.output');
        foreach ($sFeedBacks as $iKey => $aFeedBack) {
            $sFeedBacks[$iKey]['feedback_servertity_name'] = Phpfox::getLib("locale")->convert($aFeedBack['feedback_servertity_name']);

            if (isset($aFeedBack['category_name'])) {
                $sCategory = ' <a href="' . Phpfox::getLib('url')->makeUrl('feedback', array('category', $aFeedBack['feedback_category_id'], $aFeedBack['category_name'])) . '">' . Phpfox::getLib('locale')->convert($oFilterOutput->clean(\Core\Lib::phrase()->isPhrase($aFeedBack['category_name']) ? _p($aFeedBack['category_name']) : $aFeedBack['category_name'])) . '</a>';
                $sFeedBacks[$iKey]['category_url'] = $sCategory;
            }
            if (!empty($aFeedBack['user_name'])) {
                $link = phpfox::getLib('url')->makeURL($aFeedBack['user_name']);
            } elseif (Phpfox::isModule('pages')) {
                //login as page
                $aUser = Phpfox::getService('user')->getUser($aFeedBack['user_id']);
                $link = Phpfox::getService('pages')->getUrl($aUser['profile_page_id']);
            }

            if (empty($aFeedBack['full_name'])) {
                $info = _p('feedback.visitor_posted_feedback', array('visitor' => $aFeedBack['visitor']));
            } else {
                $info = _p('feedback.posted_by_link_full_name', array('link' => $link, 'full_name' => $aFeedBack['full_name']));
            }
            $isVoted = $this->database()->select('fv.*')
                ->from(Phpfox::getT('feedback_vote'), 'fv')
                ->where('fv.user_id = ' . Phpfox::getUserId() . ' and fv.feedback_id = ' . $aFeedBack['feedback_id'])
                ->execute('getRow');
            if (empty($isVoted)) {
                $sFeedBacks[$iKey]['isVoted'] = false;
            } else {
                $sFeedBacks[$iKey]['isVoted'] = true;
            }
            $sFeedBacks[$iKey]['info'] = $info;
            $aFeedBack['privacy'] = 0;
            $bookmark_url = Phpfox::getLib('url')->makeUrl('feedback', array('detail', $aFeedBack['title_url']));
            $sFeedBacks[$iKey]['bookmark_url'] = $bookmark_url;
            $sFeedBacks[$iKey]['aFeed'] = array(
                'feed_display' => 'mini',
                'comment_type_id' => 'feedback',
                'privacy' => $aFeedBack['privacy'],
                'comment_privacy' => 0,
                'like_type_id' => 'feedback',
                'feed_is_liked' => (isset($aFeedBack['is_liked']) ? $aFeedBack['is_liked'] : false),
                'feed_is_friend' => (isset($aFeedBack['is_friend']) ? $aFeedBack['is_friend'] : false),
                'item_id' => $aFeedBack['feedback_id'],
                'user_id' => $aFeedBack['user_id'],
                'total_comment' => $aFeedBack['total_comment'],
                'feed_total_like' => $aFeedBack['total_like'],
                'total_like' => $aFeedBack['total_like'],
                'feed_link' => $sFeedBacks[$iKey]['bookmark_url'],
                'feed_title' => $aFeedBack['title'],
                'time_stamp' => $aFeedBack['time_stamp'],
                'type_id' => 'feedback'
            );
            (($sPlugin = Phpfox_Plugin::get('feedback.component_service_feedback_getextra__end')) ? eval($sPlugin) : false);
        }

    }

    public function getFeedbackVoted(&$sFeedBacks)
    {
        foreach ($sFeedBacks as $iKey => $aFeedBack) {
            $isVoted = $this->database()->select('fv.*')
                ->from(Phpfox::getT('feedback_vote'), 'fv')
                ->where('fv.user_id = ' . Phpfox::getUserId() . ' and fv.feedback_id = ' . $aFeedBack['feedback_id'])
                ->execute('getRow');
            if (empty($isVoted)) {
                $sFeedBacks[$iKey]['isVoted'] = false;
            } else {
                $sFeedBacks[$iKey]['isVoted'] = true;
            }
        }
    }

    public function getFeedBackCat()
    {
        $sCats = phpfox::getLib('phpfox.database')->select('*')
            ->from(Phpfox::getT('feedback_category'))
            ->where(1)
            ->execute('getRows');
        $aCats = array();
        foreach ($sCats as $sCat) {
            $aCats[$sCat['category_id']] = Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($sCat['name']) ? _p($sCat['name']) : $sCat['name']);
        }
        return $aCats;
    }

    public function getAllFeedBackCats()
    {
        $sCats = phpfox::getLib('database')->select('*')
            ->from(Phpfox::getT('feedback_category'))
            ->where(1)
            ->execute('getRows');
        foreach ($sCats as $key => $sCat) {
            $sCats[$key]['name'] = \Core\Lib::phrase()->isPhrase($sCat['name']) ? _p($sCat['name']) : $sCat['name'];
        }
        return $sCats;
    }

    public function getFeedBackCatForEdit($category_id)
    {
        $aRow = $this->database()->select('*')->from(Phpfox::getT('feedback_category'))->where('category_id = ' . (int)$category_id)->execute('getRow');

        if (!isset($aRow['category_id'])) {
            return false;
        }

        //Support legacy phrases
        if (substr($aRow['name'], 0, 7) == '{phrase' && substr($aRow['name'], -1) == '}') {
            $aRow['name'] = preg_replace('/\s+/', ' ', $aRow['name']);
            $aRow['name'] = str_replace([
                "{phrase var='",
                "{phrase var=\"",
                "'}",
                "\"}"
            ], "", $aRow['title']);
        }//End support legacy
        $aLanguages = Phpfox::getService('language')->getAll();
        foreach ($aLanguages as $aLanguage) {
            $sPhraseValue = (Core\Lib::phrase()->isPhrase($aRow['name'])) ? _p($aRow['name'], [], $aLanguage['language_id']) : $aRow['name'];
            $aRow['name_' . $aLanguage['language_id']] = $sPhraseValue;
        }

        return $aRow;
    }

    public function getFeedBackForEdit($feedback_id)
    {
        $aFeedBack = phpfox::getLib('phpfox.database')
            ->select('*')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->where('fb.feedback_id=' . $feedback_id)
            ->execute('getRow');
        return $aFeedBack;
    }

    public function getFeedBackByCategoryId($category_id)
    {
        $aFeedBacks = phpfox::getLib('phpfox.database')->select('*')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->leftjoin(Phpfox::getT('feedback_category'), 'fc', 'fb.feedback_category_id = fc.category_id')
            ->where('fc.category_id=' . $category_id)
            ->execute('getRows');
        return $aFeedBacks;
    }


    public function getFeedBackCatAll($iPage, $iPageSize, &$iCnt)
    {
        $iCnt = phpfox::getLib('phpfox.database')->select('count(*)')
            ->from(Phpfox::getT('feedback_category'), 'fc')
            ->execute('getSlaveField');
        $aCats = phpfox::getLib('phpfox.database')->select('fc.*,Count(fb.feedback_category_id) as numbers')
            ->from(Phpfox::getT('feedback_category'), 'fc')
            ->leftjoin(Phpfox::getT('feedback'), 'fb', 'fc.category_id=fb.feedback_category_id')
            ->group('fc.category_id')
            ->where(1)
            ->limit($iPage, $iPageSize, $iCnt)
            ->execute('getRows');
        foreach ($aCats as $k => $aCat) {
            $aCats[$k]['name'] = Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aCat['name']) ? _p($aCat['name']) : $aCat['name']);
        }
        return $aCats;
    }

    public function getFeedBackServerityAll($iPage, $iPageSize, &$iCnt)
    {
        $iCnt = phpfox::getLib('phpfox.database')->select('count(*)')
            ->from(Phpfox::getT('feedback_serverity'), 'fs')
            ->execute('getSlaveField');

        $sSers = phpfox::getLib('phpfox.database')->select('fs.*,Count(fb.feedback_serverity_id) as numbers')
            ->from(Phpfox::getT('feedback_serverity'), 'fs')
            ->leftjoin(Phpfox::getT('feedback'), 'fb', 'fs.serverity_id=fb.feedback_serverity_id')
            ->group('fs.serverity_id')
            ->where(1)
            ->limit($iPage, $iPageSize, $iCnt)
            ->execute('getRows');

        foreach ($sSers as $iKey => $Sers) {
            $Sers['name'] = Phpfox::getLib('locale')->convert($Sers['name']);
            $sSers[$iKey] = $Sers;
        }
        return $sSers;
    }

    public function getFeedBackStatusAll($iPage, $iPageSize, &$iCnt)
    {
        $iCnt = phpfox::getLib('phpfox.database')->select('count(*)')
            ->from(Phpfox::getT('feedback_status'), 'fs')
            ->execute('getSlaveField');
        $sStatus = phpfox::getLib('phpfox.database')->select('fs.*,Count(fb.feedback_status_id) as numbers')
            ->from(Phpfox::getT('feedback_status'), 'fs')
            ->leftjoin(Phpfox::getT('feedback'), 'fb', 'fs.status_id=fb.feedback_status_id')
            ->group('fs.status_id')
            ->where(1)
            ->limit($iPage, $iPageSize, $iCnt)
            ->execute('getRows');

        foreach ($sStatus as $iKey => $Status) {
            $Status['name'] = Phpfox::getLib('locale')->convert($Status['name']);
            $sStatus[$iKey] = $Status;
        }

        return $sStatus;
    }


    public function getFeedBackCatIdByAlias($sTitle)
    {
        $iCat = Phpfox::getLib('database')
            ->select('category_id')
            ->from(phpfox::getT('feedback_category'))
            ->where("name_url = '" . $this->database()->escape($sTitle) . "'")
            ->execute('getSlaveField');
        return $iCat;
    }

    public function getFeedBackStatus()
    {
        $sStatus = phpfox::getLib('phpfox.database')->select('*')
            ->from(Phpfox::getT('feedback_status'))
            ->where(1)
            ->execute('getRows');
        $aStatus = array();
        foreach ($sStatus as $aValue) {
            $aStatus[$aValue['status_id']] = Phpfox::getLib('locale')->convert($aValue['name']);
            //$aStatus[$aValue['colour']] = $aValue['colour'];
        }

        foreach ($sStatus as $iKey => $Status) {
            $Status['name'] = Phpfox::getLib('locale')->convert($Status['name']);
            $sStatus[$iKey] = $Status;
        }

        return $aStatus;
    }

    public function getStatusIndex()
    {
        $sStatus = phpfox::getLib('phpfox.database')->select('*')
            ->from(Phpfox::getT('feedback_status'))
            ->where(1)
            ->execute('getRows');
        return $sStatus;
    }

    public function getFeedBackDetailByAlias($sTitle)
    {
        (($sPlugin = Phpfox_Plugin::get('FeedBack.component_service_FeedBack_getfeedbackdetail__start')) ? eval($sPlugin) : false);
        (($sPlugin = Phpfox_Plugin::get('FeedBack.service_FeedBack_getfeedbackdetail')) ? eval($sPlugin) : false);
        if (Phpfox::isModule('track')) {
            $this->database()->select("feedback_track.item_id AS is_viewed, ")->leftJoin(Phpfox::getT('feedback_track'), 'feedback_track', 'feedback_track.item_id = fb.feedback_id AND feedback_track.user_id = ' . Phpfox::getUserBy('user_id'));
        }
        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feedback\' AND l.item_id = fb.feedback_id AND l.user_id = ' . Phpfox::getUserId());
        }
        if (Phpfox::isModule('friend')) {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = fb.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }
        $aRow = $this->database()
            ->select('fb.privacy, fb.is_approved, fb.feedback_id, fb.feedback_category_id, fb.title, fb.feedback_description, fb.title_url,fb.time_stamp,fb.total_attachment,fb.total_vote,fb.total_comment,fb.feedback_status,fb.is_featured,fb.full_name as visitor,fs.name as status,fs.colour as color,fu.user_id,fb.total_view, fb.total_like, fc.name as category_name,fc.name_url as category_url,fu.full_name,fu.user_name, fser.name as feedback_servertity_name, fser.colour as feedback_serverity_color,fb.votable ')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->leftjoin(Phpfox::getT('feedback_category'), 'fc', 'fb.feedback_category_id = fc.category_id')
            ->leftjoin(Phpfox::getT('feedback_status'), 'fs', 'fb.feedback_status_id = fs.status_id')
            ->leftjoin(Phpfox::getT('user'), 'fu', 'fb.user_id = fu.user_id')
            ->leftjoin(Phpfox::getT('feedback_serverity'), 'fser', 'fser.serverity_id = fb.feedback_serverity_id')
            ->where("fb.title_url = '" . $this->database()->escape($sTitle) . "'")
            ->execute('getSlaveRow');
        if (isset($aRow['category_name']))
            $aRow['category_name'] = Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aRow['category_name']) ? _p($aRow['category_name']) : $aRow['category_name']);
        if (isset($aRow['feedback_servertity_name']))
            $aRow['feedback_servertity_name'] = Phpfox::getLib('locale')->convert($aRow['feedback_servertity_name']);
        if (isset($aRow['status']))
            $aRow['status'] = Phpfox::getLib('locale')->convert($aRow['status']);

        (($sPlugin = Phpfox_Plugin::get('FeedBack.component_service_FeedBack_getfeedbackdetail__end')) ? eval($sPlugin) : false);
        return $aRow;
    }

    public function checkFeedBackExistByTitle($sTitle)
    {
        $sTitle = $this->prepareTitle1($sTitle);
        $aRow = $this->database()
            ->select('fb.feedback_id')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->where("fb.title_url = '" . $this->database()->escape($sTitle) . "'")
            ->execute('getSlaveRow');
        return $aRow;
    }

    public function getFeedbackTitle($feedback_id)
    {
        $aRow = $this->database()
            ->select('fb.title')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->where('fb.feedback_id =' . (int)$feedback_id)
            ->execute('getSlaveField');
        return $aRow;
    }

    public function getFeedBackDetailById($feedback_id)
    {
        (($sPlugin = Phpfox_Plugin::get('FeedBack.component_service_FeedBack_getfeedbackdetail__start')) ? eval($sPlugin) : false);
        (($sPlugin = Phpfox_Plugin::get('FeedBack.service_FeedBack_getfeedbackdetail')) ? eval($sPlugin) : false);
        if (Phpfox::isModule('track')) {
            $this->database()->select("feedback_track.item_id AS is_viewed, ")->leftJoin(Phpfox::getT('feedback_track'), 'feedback_track', 'feedback_track.item_id = fb.feedback_id AND feedback_track.user_id = ' . Phpfox::getUserBy('user_id'));
        }

        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feedback\' AND l.item_id = fb.feedback_id AND l.user_id = ' . Phpfox::getUserId());
        }

        if (Phpfox::isModule('friend')) {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = fb.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }
        $aRow = $this->database()
            ->select('fb.privacy, fb.is_approved, fb.feedback_category_id, fb.feedback_id, fb.title, fb.feedback_description, fb.title_url,fb.time_stamp,fb.total_attachment,fb.total_vote,fb.total_comment,fb.feedback_status,fb.is_featured,fb.full_name as visitor,fs.name as status,fs.colour as color,fu.user_id,fb.total_view, fb.total_like, fc.name as category_name,fc.name_url as category_url,fu.full_name,fu.user_name, fser.name as feedback_servertity_name, fser.colour as feedback_serverity_color,fb.votable ')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->leftjoin(Phpfox::getT('feedback_category'), 'fc', 'fb.feedback_category_id = fc.category_id')
            ->leftjoin(Phpfox::getT('feedback_status'), 'fs', 'fb.feedback_status_id = fs.status_id')
            ->leftjoin(Phpfox::getT('user'), 'fu', 'fb.user_id = fu.user_id')
            ->leftjoin(Phpfox::getT('feedback_serverity'), 'fser', 'fser.serverity_id = fb.feedback_serverity_id')
            ->where("fb.feedback_id = " . $feedback_id)
            ->execute('getSlaveRow');

        $aRow['category_name'] = Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aRow['category_name']) ? _p($aRow['category_name']) : $aRow['category_name']);
        if (isset($aRow['feedback_servertity_name']))
            $aRow['feedback_servertity_name'] = Phpfox::getLib('locale')->convert($aRow['feedback_servertity_name']);
        if (isset($aRow['status']))
            $aRow['status'] = Phpfox::getLib('locale')->convert($aRow['status']);

        (($sPlugin = Phpfox_Plugin::get('FeedBack.component_service_FeedBack_getfeedbackdetail__end')) ? eval($sPlugin) : false);
        return $aRow;
    }

    public function getFeedBackById($feedback_id)
    {
        (($sPlugin = Phpfox_Plugin::get('FeedBack.component_service_FeedBack_getfeedbackbyid__start')) ? eval($sPlugin) : false);
        (($sPlugin = Phpfox_Plugin::get('FeedBack.service_FeedBack_getfeedbackbyid')) ? eval($sPlugin) : false);
        if (Phpfox::isModule('track')) {
            //$this->database()->select("video_track.item_id AS video_is_viewed, ")->leftJoin(Phpfox::getT('video_track'), 'video_track', 'video_track.item_id = v.video_id AND video_track.ip_address = \'' . $this->database()->escape(Phpfox::getIp(true)) . '\'');
        }

        if (Phpfox::isModule('friend')) {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = fb.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }

        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'video\' AND l.item_id = fb.feedback_id AND l.user_id = ' . Phpfox::getUserId());
        }
        $aRow = $this->database()
            ->select('fb.*')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->where("fb.feedback_id = " . (int)$feedback_id)
            ->execute('getSlaveRow');

        (($sPlugin = Phpfox_Plugin::get('FeedBack.component_service_FeedBack_getfeedbackbyid__end')) ? eval($sPlugin) : false);
        return $aRow;
    }

    public function getFeedBackPictures($feedback_id)
    {
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_FeedBack_getfeedbackpictures__start')) ? eval($sPlugin) : false);
        (($sPlugin = Phpfox_Plugin::get('feedback.service_FeedBack_getfeedbackpictures')) ? eval($sPlugin) : false);
        $aPics = $this->database()
            ->select('fp.picture_id,fp.picture_path,fp.thumb_url,fp.server_id')
            ->from(Phpfox::getT('feedback_picture'), 'fp')
            ->where("fp.feedback_id = " . (int)$feedback_id)
            ->execute('getRows');
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_FeedBack_getfeedbackpictures__end')) ? eval($sPlugin) : false);
        return $aPics;
    }

    public function getPicture($iPictureId)
    {
        return db()
            ->select('*')
            ->from(Phpfox::getT('feedback_picture'), 'fp')
            ->where("fp.picture_id = " . (int)$iPictureId)
            ->execute('getRow');
    }

    public function delete($feedback_id)
    {
        $aRow = Phpfox::getLib('database')
            ->select('*')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->where('fb.feedback_id=' . $feedback_id)
            ->execute('getRow');
        if (count($aRow) > 0) {
            phpfox::getService('feedback.process')->delete($feedback_id);
            return $aRow['title'];
        }
        return false;
    }

    public function deletePic($picture_id, $feedback_id)
    {
        $aFeedback = $this->getFeedBackById($feedback_id);
        if (isset($picture_id) && isset($aFeedback)) {
            phpfox::getLib('database')->delete(phpfox::getT('feedback_picture'), 'picture_id=' . $picture_id);
            Phpfox::getService('feedback.process')->updateTotalPictureDel($feedback_id);
            return $aFeedback['title_url'];
        }

        return false;
    }

    public function getSettings()
    {
        $is_allowed_post = Phpfox::getLib('database')->select('param_values')
            ->from(Phpfox::getT('feedback_settings'))
            ->where('settings_type="is_allowed"')
            ->execute('getRow');
        return $is_allowed_post['param_values'];
    }

    public function getSettingsEmail()
    {
        $is_send_email = Phpfox::getLib('database')->select('param_values')
            ->from(Phpfox::getT('feedback_settings'))
            ->where('settings_type="is_email"')
            ->execute('getRow');
        return $is_send_email['param_values'];
    }

    function isEmail($email)
    {
        if (!preg_match("/^[0-9a-zA-Z]([\-.\w]*[0-9a-zA-Z]?)*@([0-9a-zA-Z][\-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,}$/", $email)) {
            return false;
        } else {
            return true;
        }
    }

    public function sendMailAdmin($feedback_id)
    {
        $sLink = Phpfox::getLib('url')->makeURL('feedback.detail', array('feedback' => $feedback_id));
        $adminMail = Phpfox::getLib('database')
            ->select('u.email')
            ->from(Phpfox::getT('user'), 'u')
            ->where('u.user_group_id=1')
            ->execute('getRows');

        foreach ($adminMail as $sMail) {
            $sMail = trim($sMail['email']);
            try {
                Phpfox::getLib('mail')->to($sMail)
                    ->subject(array('feedback.full_name_post_a_feedback_to_your_site', array('full_name' => Phpfox::getUserBy('full_name'), 'site_title' => Phpfox::getParam('core.site_title'))))
                    ->message(array('feedback.full_name_post_a_feedback_to_your_site_link', array('full_name' => Phpfox::getUserBy('full_name'), 'site_title' => Phpfox::getParam('core.site_title') . '<br/>' . $message . '<br/>', 'link' => $sLink)))
                    ->send();

            } catch (Exception $ex) {
                $errors[] = $sMail . " can't send !";
            }
        }
    }

    public function sendMailToNoneUser($feedback_id, $isSendMail, $isUpdate)
    {
        if ($isSendMail == 1) {
            $sLink = Phpfox::getLib('url')->makeURL('feedback.detail', array('feedback' => $feedback_id));
            $eUser = $this->database()->select('fb.email')
                ->from(Phpfox::getT('feedback'), 'fb')
                ->where('fb.feedback_id = ' . (int)$feedback_id)
                ->execute('getSlaveField');
            $sMail = trim($eUser);
            try {
                Phpfox::getLib('mail')->to($sMail)
                    ->subject('Admin approve your feedback on ' . Phpfox::getParam('core.site_title'))
                    ->message(array('feedback.admin_approve_none_user_feedback', array('link' => $sLink)))
                    ->send();

            } catch (Exception $ex) {
                $error = $sMail . " can't send !";
                return $error;
            }
            return $sMail;
        }

    }

    public function getNew($iLimit = 3)
    {
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_feedback_getnew__start')) ? eval($sPlugin) : false);
        $aRows = $this->database()->select('fb.feedback_id, fb.full_name as name, fb.time_stamp, fb.title, fb.title_url, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'fb')
            ->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = fb.user_id')
            ->where('fb.privacy = 1 OR (fb.privacy=2 AND fb.user_id=' . Phpfox::getUserId() . ')')
            ->limit($iLimit)
            ->order('fb.time_stamp DESC')
            ->execute('getSlaveRows');

        foreach ($aRows as $iKey => $aRow) {
            if ($aRow['user_id'] != null) {
                $aRows[$iKey]['posted_on'] = _p('feedback.posted_on_post_time_by_user_link', array(
                        'post_time' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_stamp']),
                        'user' => $aRow

                    )
                );
            } else {
                $aRows[$iKey]['posted_on'] = _p('feedback.posted_on_post_time_by_visitor', array(
                    'post_time' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_stamp']),
                    'visitor' => $aRow['name']));
            }
        }
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_feedback_getnew__end')) ? eval($sPlugin) : false);
        return $aRows;
    }

    public function checkVoteOfUser($user_id)
    {
        $iCntVote = phpfox::getLib('database')->select("count('*')")
            ->from(phpfox::getT('feedback_vote'), 'fv')
            ->where('fv.user_id = ' . $user_id)
            ->group('fv.user_id')
            ->execute('getField');
        $iLimitVote = Phpfox::getUserParam('feedback.limit_vote_feedback');
        if ($iCntVote >= $iLimitVote) {
            return false;
        }
        return true;
    }

    public function execute($param)
    {
        $this->_aParams = $param;
        $this->_aParams['service'] = 'feedback.browse';
        $aActualConditions = (array)phpfox::getLib('search')->getConditions();
        list($sModule, $sService) = explode('.', $this->_aParams['service']);
        if (Phpfox::isModule('input') && (isset($_SESSION[Phpfox::getParam('core.session_prefix')]['search'][$sModule][Phpfox_Request::instance()->get('search-id')]['input']))) {
            $aInputs = ($_SESSION[Phpfox::getParam('core.session_prefix')]['search'][$sModule][Phpfox_Request::instance()->get('search-id')]['input']);
            $aInputsToSearch = array();
            foreach ($aInputs as $iInputId => $mValue) {
                if (!is_numeric($iInputId) || $iInputId < 1) continue;
                $aInputsToSearch[$iInputId] = $mValue;
            }
            $aJoins = Phpfox::getService('input')->getJoinsForSearch($aInputsToSearch, $sModule);
            if (empty($aJoins)) {
                $aJoins = array(0);
            }
            $aActualConditions[] = 'AND (' . $this->_aParams['alias'] . '.' . $this->_aParams['field'] . ' IN (' . implode(',', $aJoins) . '))';
        }

        $this->_aConditions = array();
        $this->_sView = Phpfox::getLib('request')->get('view');
        foreach ($aActualConditions as $sCond) {
            switch ($this->_sView) {
                case 'friend':
                    $this->_aConditions[] = str_replace('%PRIVACY%', '0,1,2', $sCond);
                    break;
                case 'my':
                    $this->_aConditions[] = str_replace('%PRIVACY%', '0,1,2,3,4', $sCond);
                    break;
                case 'pages_member':
                    $this->_aConditions[] = str_replace('%PRIVACY%', '0,1', $sCond);
                    break;
                case 'pages_admin':
                    $this->_aConditions[] = str_replace('%PRIVACY%', '0,1,2', $sCond);
                    break;
                default:
                    $this->_aConditions[] = str_replace('%PRIVACY%', '0', $sCond);
                    break;
            }
        }

        if (Phpfox::getParam('core.section_privacy_item_browsing')
            && (isset($this->_aParams['hide_view']) && !in_array($this->_sView, $this->_aParams['hide_view']))) {
            Phpfox::getService('privacy')->buildPrivacy($this->_aParams);

            $this->database()->unionFrom($this->_aParams['alias']);
        } else {
            phpfox::getService('feedback.browse')->getQueryJoins(true);

            $this->database()->from($this->_aParams['table'], $this->_aParams['alias'])->where($this->_aConditions);
        }

        phpfox::getService('feedback.browse')->query();
        $this->_aRows = $this->database()->select($this->_aParams['alias'] . '.*, ' . (isset($this->_aParams['select']) ? $this->_aParams['select'] : '') . Phpfox::getUserField())
            ->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = ' . $this->_aParams['alias'] . '.user_id')
            ->order($this->search()->getSort())
            ->limit($this->search()->getPage(), $this->search()->getDisplay(), false, false, false)
            ->execute('getSlaveRows');
        return (array)$this->_aRows;
    }

    function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
    }

    public function hasAccess($iId, $sUserPerm, $sGlobalPerm)
    {
        (($sPlugin = Phpfox_Plugin::get('feedback.service_feedback_hasaccess_start')) ? eval($sPlugin) : false);

        $aRow = $this->database()->select('feedback.user_id')
            ->from($this->_sTable, 'feedback')
            ->where('feedback.feedback_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        (($sPlugin = Phpfox_Plugin::get('feedback.service_feedback_hasaccess_end')) ? eval($sPlugin) : false);
        if (!isset($aRow['user_id'])) {
            return false;
        }

        if ((Phpfox::getUserId() == $aRow['user_id'] && Phpfox::getUserParam('feedback.' . $sUserPerm)) || Phpfox::getUserParam('feedback.' . $sGlobalPerm)) {
            return $aRow['user_id'];
        }
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_feedback_getfeedback__end')) ? eval($sPlugin) : false);
        return false;
    }

    public function getInfoForAction($aItem)
    {

        if (is_numeric($aItem)) {
            $aItem = array('item_id' => $aItem);
        } else {
            $aRow = $this->database()->select('fb.feedback_id, fb.title, fb.title_url , fb.user_id, u.gender, u.full_name')
                ->from(Phpfox::getT('feedback'), 'fb')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = fb.user_id')
                ->where('fb.feedback_id = ' . (int)$aItem['item_id'])
                ->execute('getSlaveRow');
        }

        if (empty($aRow)) {
            d($aRow);
            d($aItem);
        }

        $aRow['link'] = Phpfox::getLib('url')->permalink('feedback.detail', $aRow['title_url']);

        return $aRow;
    }

    public function getTotalAttachment($id)
    {
        return $this->database()->select('fb.total_attachment,fb.title')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->where('fb.feedback_id =' . (int)$id)
            ->execute('getSlaveRow');
    }

    public function getMyFeedbacksTotal()
    {
        return $this->database()->select('COUNT(*)')->from(Phpfox::getT('feedback'))->where('user_id = ' . (int)Phpfox::getUserId())->execute('getSlaveField');
    }

    public function getLastTempFile()
    {
        return $this->database()->select('file_id')->from(Phpfox::getT('temp_file'))->order('file_id  DESC')->execute('getSlaveRow');
    }

    public function getUploadParams($aParams)
    {
        $aAttach = $this->getTotalAttachment($aParams['feedback_id']);
        $iMaxUploadFiles = (int)Phpfox::getUserParam('feedback.define_how_many_pictures_can_be_uploaded_per_feedback');
        if (isset($aParams['feedback_id'])) {
            $iTotalImage = $aAttach['total_attachment'];
            $iRemainImage = $iMaxUploadFiles - $iTotalImage;
        } else {
            $iRemainImage = $iMaxUploadFiles;
        }
        $iMaxFileSize = Phpfox::getUserParam('feedback.picture_max_upload_size');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize / 1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        $aEvents = [
            'success' => 'feedbackUploadPhoto.dropzoneOnSuccess',
            'queuecomplete' => 'feedbackUploadPhoto.dropzoneQueueComplete',
        ];
        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'upload_url' => Phpfox::getLib('url')->makeUrl('feedback.frame') . '?id=' . $aParams['feedback_id'],
            'component_only' => true,
            'max_file' => $iRemainImage,
            'upload_now' => "true",
            'submit_button' => '',
            'first_description' => _p('drag_n_drop_multi_photos_here_to_upload'),
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'feedback' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'feedback/',
            'update_space' => false,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'on_remove' => 'feedback.deletePhotoOnUploadForm',
            'style' => '',
            'thumbnail_sizes' => array(50, 240, 400, 500, 70),
            'no_square' => true,
            'label' => _p('display_photo'),
            'js_events' => $aEvents,
        ];
    }

}

?>