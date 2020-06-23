<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class FeedBack_Component_Block_Add extends Phpfox_Component
{
    public function process()
    {
        $module = $this->getParam('module');
        $item = $this->getParam('item');
        $error = $this->getParam('errors');
        $aCats = phpfox::getLib('database')
            ->select('*')
            ->from(phpfox::getT('feedback_category'), 'fc')
            ->where(1)
            ->execute('getRows');
        foreach ($aCats as $k => $aCat) {
            $aCats[$k]['name'] = Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aCat['name']) ? _p($aCat['name']) : $aCat['name']);
        }

        $aSers = phpfox::getLib('database')
            ->select('*')
            ->from(phpfox::getT('feedback_serverity'), 'fs')
            ->where(1)
            ->execute('getRows');
        $where = " (fb.privacy = 1 AND fb.is_approved = 1)";

        foreach ($aSers as $iKey => $Sers) {
            $Sers['name'] = Phpfox::getLib('locale')->convert($Sers['name']);
            $aSers[$iKey] = $Sers;
        }

        $aAllFeedBacks = Phpfox::getLib('database')
            ->select('fb.*,fs.name as status,fs.colour as color')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->leftjoin(Phpfox::getT('feedback_status'), 'fs', 'fb.feedback_status_id=fs.status_id')
            ->where($where)
            ->order('fb.total_vote DESC')
            ->limit(0, 4)
            ->execute('getRows');

        $link = Phpfox::getLib('url')->makeURL('feedback');
        foreach ($aAllFeedBacks as $iKey => $aItem) {
            $aAllFeedBacks[$iKey] = Phpfox::getService('feedback')->getFeedBackDetailById($aAllFeedBacks[$iKey]['feedback_id']);

            $time_stamp = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aAllFeedBacks[$iKey]['time_stamp']);
            if (empty($aAllFeedBacks[$iKey]['full_name'])) {
                $visitor_title = "";
                if (strlen($aAllFeedBacks[$iKey]['visitor']) > 12) {
                    $aAllFeedBacks[$iKey]['visitor_show'] = substr($aAllFeedBacks[$iKey]['visitor'], 0, 12) . '...';
                    $visitor_title = $aAllFeedBacks[$iKey]['visitor'];
                } else {
                    $aAllFeedBacks[$iKey]['visitor_show'] = $aAllFeedBacks[$iKey]['visitor'];
                }
                $aAllFeedBacks[$iKey]['info'] = _p('feedback.posted_time_by_visitor', array('time_stamp' => $time_stamp, 'full_name' => $visitor_title, 'short_name' => $aAllFeedBacks[$iKey]['visitor_show']));
            } else {
                $aAllFeedBacks[$iKey]['full_name_show'] = $aAllFeedBacks[$iKey]['full_name'];
                if (strlen($aAllFeedBacks[$iKey]['full_name']) > 12) {
                    $aAllFeedBacks[$iKey]['full_name_show'] = mb_substr($aAllFeedBacks[$iKey]['full_name'], 0, 12, mb_detect_encoding($aAllFeedBacks[$iKey]['full_name'])) . '...';
                }
                $link = phpfox::getLib('url')->makeURL($aAllFeedBacks[$iKey]['user_name']);
                $short_name = $aAllFeedBacks[$iKey]['full_name_show'];
                $full_name = $aAllFeedBacks[$iKey]['full_name'];
                $aAllFeedBacks[$iKey]['info'] = _p('feedback.posted_time_by_user', array('time_stamp' => $time_stamp, 'link' => $link, 'full_name' => $full_name, 'short_name' => $short_name));
            }
            $bookmark_url = Phpfox::getLib('url')->makeUrl('feedback', array('detail', $aAllFeedBacks[$iKey]['title_url']));
            $aAllFeedBacks[$iKey]['bookmark_url'] = $bookmark_url;
        }

        Phpfox::getService('feedback')->getFeedbackVoted($aAllFeedBacks);

        $aUncategorizedFeedBacks = Phpfox::getLib('database')
            ->select('fb.*,fs.name as status,fs.colour as color')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->leftjoin(Phpfox::getT('feedback_status'), 'fs', 'fb.feedback_status_id=fs.status_id')
            ->where("fb.privacy = 1 AND fb.is_approved = 1 AND fb.feedback_category_id = 0")
            ->order('fb.total_vote DESC')
            ->limit(0, 4)
            ->execute('getRows');
        foreach ($aUncategorizedFeedBacks as $iKey => $aItem) {
            $time_stamp = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aUncategorizedFeedBacks[$iKey]['time_stamp']);
            $aUncategorizedFeedBacks[$iKey] = Phpfox::getService('feedback')->getFeedBackDetailById($aUncategorizedFeedBacks[$iKey]['feedback_id']);
            if (empty($aUncategorizedFeedBacks[$iKey]['full_name'])) {
                $visitor_title = "";
                if (strlen($aUncategorizedFeedBacks[$iKey]['visitor']) > 12) {
                    $aUncategorizedFeedBacks[$iKey]['visitor_show'] = substr($aUncategorizedFeedBacks[$iKey]['visitor'], 0, 12) . '...';
                    $visitor_title = $aUncategorizedFeedBacks[$iKey]['visitor'];
                } else {
                    $aUncategorizedFeedBacks[$iKey]['visitor_show'] = $aUncategorizedFeedBacks[$iKey]['visitor'];
                }
                $short_name = $aUncategorizedFeedBacks[$iKey]['visitor_show'];
                $aUncategorizedFeedBacks[$iKey]['info'] = _p('feedback.posted_time_by_visitor', array('time_stamp' => $time_stamp, 'full_name' => $visitor_title, 'short_name' => $short_name));
            } else {
                $aUncategorizedFeedBacks[$iKey]['full_name_show'] = $aUncategorizedFeedBacks[$iKey]['full_name'];
                if (strlen($aUncategorizedFeedBacks[$iKey]['full_name']) > 12) {
                    $aUncategorizedFeedBacks[$iKey]['full_name_show'] = substr($aUncategorizedFeedBacks[$iKey]['full_name'], 0, 12) . '...';
                }
                $link = phpfox::getLib('url')->makeURL($aUncategorizedFeedBacks[$iKey]['user_name']);
                $short_name = $aUncategorizedFeedBacks[$iKey]['full_name_show'];
                $full_name = $aUncategorizedFeedBacks[$iKey]['full_name'];
                $aUncategorizedFeedBacks[$iKey]['info'] = _p('feedback.posted_time_by_user', array('time_stamp' => $time_stamp, 'link' => $link, 'full_name' => $full_name, 'short_name' => $short_name));
            }
            $bookmark_url = Phpfox::getLib('url')->makeUrl('feedback', array('detail', $aUncategorizedFeedBacks[$iKey]['title_url']));
            $aUncategorizedFeedBacks[$iKey]['bookmark_url'] = $bookmark_url;
        }
        Phpfox::getService('feedback')->getFeedbackVoted($aUncategorizedFeedBacks);

        $aCategoryFeedBacks = array();
        foreach ($aCats as $iKey => $aCat) {
            $aFeedBacks = Phpfox::getLib('database')
                ->select('fb.*,fs.name as status,fs.colour as color')
                ->from(Phpfox::getT('feedback'), 'fb')
                ->leftjoin(Phpfox::getT('feedback_status'), 'fs', 'fb.feedback_status_id=fs.status_id')
                ->where("fb.privacy = 1 AND fb.is_approved = 1 AND fb.feedback_category_id = " . $aCat['category_id'])
                ->order('fb.total_vote DESC')
                ->limit(0, 4)
                ->execute('getRows');
            foreach ($aFeedBacks as $iKey => $aItem) {
                $time_stamp = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aFeedBacks[$iKey]['time_stamp']);
                $aFeedBacks[$iKey] = Phpfox::getService('feedback')->getFeedBackDetailById($aFeedBacks[$iKey]['feedback_id']);
                if (empty($aFeedBacks[$iKey]['full_name'])) {
                    $visitor_title = "";
                    if (strlen($aFeedBacks[$iKey]['visitor']) > 15) {
                        $aFeedBacks[$iKey]['visitor_show'] = substr($aFeedBacks[$iKey]['visitor'], 0, 15) . '...';
                        $visitor_title = $aFeedBacks[$iKey]['visitor'];
                    } else {
                        $aFeedBacks[$iKey]['visitor_show'] = $aFeedBacks[$iKey]['visitor'];
                    }
                    $short_name = $aFeedBacks[$iKey]['visitor_show'];
                    $aFeedBacks[$iKey]['info'] = _p('feedback.posted_time_by_visitor', array('time_stamp' => $time_stamp, 'full_name' => $visitor_title, 'short_name' => $short_name));
                } else {
                    $aFeedBacks[$iKey]['full_name_show'] = $aFeedBacks[$iKey]['full_name'];
                    if (strlen($aFeedBacks[$iKey]['full_name']) > 15) {
                        $aFeedBacks[$iKey]['full_name_show'] = substr($aFeedBacks[$iKey]['full_name'], 0, 15) . '...';
                    }
                    $link = phpfox::getLib('url')->makeURL($aFeedBacks[$iKey]['user_name']);
                    $short_name = $aFeedBacks[$iKey]['full_name_show'];
                    $full_name = $aFeedBacks[$iKey]['full_name'];
                    $aFeedBacks[$iKey]['info'] = _p('feedback.posted_time_by_user', array('time_stamp' => $time_stamp, 'link' => $link, 'full_name' => $full_name, 'short_name' => $short_name));
                }
                $bookmark_url = Phpfox::getLib('url')->makeUrl('feedback', array('detail', $aFeedBacks[$iKey]['title_url']));
                $aFeedBacks[$iKey]['bookmark_url'] = $bookmark_url;
            }
            Phpfox::getService('feedback')->getFeedbackVoted($aFeedBacks);
            $aCategoryFeedBacks[$aCat['category_id']] = $aFeedBacks;
        }
        $aFilters = array(
            'keyword' => array(
                'type' => 'input:text',
                'size' => 30,
            )
        );
        $this->search()->set(array(
                'type' => 'feedback',
                //  'filters'=>$aFilters,
                'search' => 'keyword',
                'field' => 'fb.feedback_id',
                'search_tool' => array(
                    'table_alias' => 'fb',
                    'search' => array(
                        'action' => ($this->url()->makeUrl('feedback', array('view' => $this->request()->get('view')))),
                        'default_value' => _p('feedback.search_feedback_dot'),
                        'name' => 'keyword',
                        'field' => 'fb.feedback_description'
                    ),
                    'sort' => array(
                        'latest' => array('fb.time_stamp', _p('feedback.latest')),
                        'most-viewed' => array('fb.total_view', _p('feedback.most_viewed')),
                        'most-liked' => array('fb.total_like', _p('feedback.most_like')),
                        'most-talked' => array('fb.total_comment', _p('feedback.most_discussed'))
                    ),
                    'show' => array(5, 10, 15),

                )
            )
        );
        $sFormUrl = $this->url()->makeUrl('feedback');
        $login = phpfox::getLib('url')->makeURL('login');
        $visitor = _p('feedback.post_a_feedback_visitor', array('login' => $login));
        $feedback = Phpfox::getLib('url')->makeURL('feedback');
        $this->template()->setTitle('Post Feed Back')
            ->assign(array(
                'aCats' => $aCats,
                'totalaCats' => count($aCats),
                'aSers' => $aSers,
                'aAllFeedBacks' => $aAllFeedBacks,
                'aUncategorizedFeedBacks' => $aUncategorizedFeedBacks,
                'aCategoryFeedBacks' => $aCategoryFeedBacks,
                'core_path' => Phpfox::getParam('core.path'),
                'visitor' => $visitor,
                'feedback' => $feedback,
                'errors' => $error,
                'user_id' => phpfox::getUserId(),
            ));

        $this->template()->assign(array(
                'sFormUrl' => $sFormUrl
            )
        );
        $this->template()
            ->assign(
                array(
                    'site_title' => phpfox::getParam('core.site_title'),
                    'module' => $module,
                    'item' => $item
                )
            )
            ->setHeader(array(
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'pager.css' => 'style_css',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'feedback.js' => 'module_feedback',
                'feed.js' => 'module_feed',
                'country.js' => 'module_core',
            ));
        return 'block';

    }
}

?>

