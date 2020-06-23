<?php

namespace Apps\YNC_Blogs\Controller;

use Phpfox;
use Phpfox_Component;

class FollowingController extends Phpfox_Component
{
    public function process()
    {
        $this->search()->set([
                'type' => 'blogger',
                'field' => 'u.user_id',
                'ignore_blocked' => true,
                'search_tool' => [
                    'table_alias' => 'abf',
                    'search' => [
                        'action' => $this->url()->makeUrl('ynblog.following'),
                        'default_value' => _p('search_blogger_three_dot'),
                        'name' => 'search',
                        'field' => ['u.full_name']
                    ],
                    'sort' => [
                        'latest' => ['abf.time_stamp', _p('latest')],
                        'a-z' => ['u.full_name', _p('A-Z'), 'ASC'],
                        'z-a' => ['u.full_name', _p('Z-A')],
                    ],
                    'show' => [10, 20, 30],
                ]
            ]
        );

        //Prepare params
        $iPage = $this->search()->getPage();
        $iSize = $this->search()->getDisplay();
        $sSort = $this->search()->getSort();
        $sConds = implode(' ', $this->search()->getConditions());


        $aItems = Phpfox::getService('ynblog.blog')->getFollowingBlogger($iPage, $iSize, $sConds, $sSort);


        $this->template()->assign(array(
                'aItems' => $aItems,
                'bIsInDetail' => true,
                'sSearchBlock' => _p('search_blogs_'),
                'appPath' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-blogs/',
            )
        );

        section(_p('Blogs'), url('/advanced-blog'));
        section(_p('my_following_bloggers'), url('/advanced-blog/following'));

        $aFilterMenu = Phpfox::getService('ynblog.helper')->buildFilterMenu();
        $this->template()->buildSectionMenu('ynblog', $aFilterMenu);
    }
}
