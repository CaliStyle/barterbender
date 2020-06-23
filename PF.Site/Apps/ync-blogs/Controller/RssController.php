<?php

namespace Apps\YNC_Blogs\Controller;

use Phpfox;
use Phpfox_Component;

class RssController extends Phpfox_Component
{
    public function process()
    {
        //Check permission
        user('yn_advblog_view', null, null, true);

        if(!Phpfox::getParam('ynblog.yn_advblog_on_off_rss')){
            return \Phpfox_Module::instance()->setController('error.404');
        }

        $sCond = '';
        $iLimit = 100;
        $sLink = Phpfox::getLib('url')->makeUrl('ynblog');

        if ($this->request()->get('req3') == 'category') {
            if ($aBlogCategory = Phpfox::getService('ynblog.category')->getCategory($this->request()->getInt('req4'))) {
                if ($aBlogCategory['is_active']) {
                    $sCond .= ' AND ac.category_id = ' . $this->request()->getInt('req4');
                    $sLink .= 'category/' . $aBlogCategory['category_id'] . '/' . Phpfox::getLib('parse.input')->cleanTitle(_p($aBlogCategory['name']));
                } else {
                    return Phpfox::getLib('error')->display(_p('feed_not_found'));
                }
            } else {
                return Phpfox::getLib('error')->display(_p('feed_not_found'));
            }
        } elseif ($this->request()->get('req3') == 'author') {
            $sCond .= ' AND ab.user_id = ' . $this->request()->getInt('req4');
            $aUser = Phpfox::getService('user')->getUser($this->request()->getInt('req4'), 'u.user_id, u.user_name, u.full_name');
            $sLink .= 'author/' . $aUser['user_id'] . '/' . Phpfox::getLib('parse.input')->cleanTitle($aUser['user_name']);
        }

        $aItems = Phpfox::getService('ynblog.blog')->getRSS($iLimit, 'ab.time_stamp DESC', $sCond);

        $this->template()->assign(
            array(
                'sLink' => $sLink,
                'aItems' => $aItems,
            )
        );
        Phpfox::getLib('module')->getControllerTemplate();
        header('Content-type: application/xml');
        die;
    }
}