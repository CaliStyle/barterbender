<?php

namespace Apps\YNC_Member\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Pager;
use Core\Route\Controller;
use Phpfox_Plugin;

class IndexController extends Phpfox_Component
{
    public function process()
    {
        if ($sPlugin = Phpfox_Plugin::get('user.component_controller_browse__1')){eval($sPlugin);if (isset($aPluginReturn)){return $aPluginReturn;}}

        $template = $this->template();
        $search = $this->search();
        $request = $this->request();
        $url = $this->url();

        if(defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW')){
            Controller::$name = true;
        }else{
            Controller::$name = '';
        }

        $template->setTitle(_p('Members'));

        $breadCrumb = $url->makeUrl('ynmember');

        $template->setBreadCrumb('Members', $breadCrumb);

        $aFilterMenu = [
            _p('All Members') => '',
            ];

        if (Phpfox::isModule('friend')) {
        if (Phpfox::isUser() && ($iCnt = Phpfox::getService('ynmember.member')->getFriendCount(Phpfox::getUserId()))) {
            $iCnt = ($iCnt >= 100) ? '99+' : $iCnt;
            $aFilterMenu[_p('My Friends') . '<span class="pending my count-item">' . $iCnt . '</span>'] = 'my';
        } else {
            $aFilterMenu[_p('My Friends')] = 'my';
            }
        }

        $aFilterMenu = array_merge($aFilterMenu, [
            _p('Featured Members') => 'featured',
            _p('Members Rating & Review') => 'ynmember.review',
            _p('Members Birthday') => 'ynmember.birthday',
        ]);

        $template->buildSectionMenu('ynmember', $aFilterMenu);

        $iYear = date('Y');
        $bIsProfile = false;
        $sView = $request->get('view');

        // GET PARAMS
        $formAction = $url->makeUrl('ynmember', ['view' => $sView]);
        $oFilter = Phpfox::getService('ynmember.member')->getSearchFilter($formAction);

        // Check if we are on advanced search mode
        $bIsAdvSearch = FALSE;
        if($oFilter->get('form_flag') == 1)
        {
            $bIsAdvSearch = TRUE;
        }

        $aCustomSearch = $oFilter->getCustom();
        $mFeatured = false;
        $bFriend = false;
        $bLocation = false;
        $bExtend = true;

        // PROCESS CONDITIONS
        if (!empty($sView))
        {
            switch ($sView)
            {
                case 'featured':
                    $mFeatured = true;

                    break;
                case 'my':
                    if (Phpfox::isModule('friend')) {
                    Phpfox::isUser(true);
                    $bFriend = true;
                    $oFilter->setCondition('AND friend.friend_id IS NOT NULL');
                    } else {
                        $sView = 'all';
                    }
                    break;
                default:
                    break;
            }
        }

        if ($iFrom = $oFilter->get('from'))
        {
            $oFilter->setCondition('AND u.birthday_search <= \'' . Phpfox::getLib('date')->mktime(0, 0, 0, 1, 1, $iYear - $iFrom). '\'' . ' AND ufield.dob_setting IN(0,1,2)');
        }
        if ($iTo = $oFilter->get('to'))
        {
            $oFilter->setCondition('AND u.birthday_search >= \'' . Phpfox::getLib('date')->mktime(0, 0, 0, 1, 1, $iYear - $iTo) .'\'' . ' AND ufield.dob_setting IN(0,1,2)');
        }

        if (($sLat = $oFilter->get('location_latitude')) && ($sLng = $oFilter->get('location_longitude')) && ($iRadius = $oFilter->get('within'))) {
            $bLocation = true;
            $fLat = floatval($sLat);
            $fLng = floatval($sLng);
            $iRadius = (int)$iRadius;
            $oFilter->setCondition("AND (
                        (3959 * acos(
                                cos( radians('{$fLat}')) 
                                * cos( radians( place.location_latitude ) ) 
                                * cos( radians( place.location_longitude ) - radians('{$fLng}') ) 
                                + sin( radians('{$sLat}') ) * sin( radians( place.location_latitude ) ) 
                            ) < {$iRadius} 
                        )                     
                    )");
        }

        if (!defined('PHPFOX_IS_ADMIN_SEARCH'))
        {
            $oFilter->setCondition('AND u.status_id = 0 AND u.view_id = 0');
            if (Phpfox::isUser()) {
                $aBlockedUserIds =  Phpfox::getService('user.block')->get(null, true);
                if (!empty($aBlockedUserIds)) {
                    $oFilter->setCondition('AND u.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')');
                }
            }
        }
        else
        {
            $oFilter->setCondition('AND u.profile_page_id = 0');
        }

        $iPage = $this->request()->getInt('page');
        $iPageSize = $oFilter->getDisplay();

        list($iCnt, $aUsers) = Phpfox::getService('ynmember.browse')->conditions($oFilter->getConditions())
            ->sort($oFilter->getSort())
            ->page($oFilter->getPage())
            ->limit($iPageSize)
            ->extend((isset($bExtendContent) ? true : $bExtend))
            ->featured($mFeatured)
            ->location($bLocation)
            ->friend($bFriend)
            ->custom($aCustomSearch)
            ->get();

        $iCnt = $oFilter->getSearchTotal($iCnt);

        $aNewCustomValues = array();
        if ($aCustomValues = $this->request()->get('custom'))
        {
            if (is_array($aCustomValues)) {
                foreach ($aCustomValues as $iKey => $sCustomValue) {
                    $aNewCustomValues['custom[' . $iKey . ']'] = $sCustomValue;
                }
            }
        }
        if (!(defined('PHPFOX_IS_ADMIN_SEARCH'))) {
            Phpfox_Pager::instance()->set(array(
                'page' => $iPage,
                'size' => $iPageSize,
                'count' => $iCnt,
                'ajax' => 'user.mainBrowse',
                'aParams' => $aNewCustomValues
            ));
        } else {
            Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));
        }

        // SET PAGE BACK TO URL
        $url->setParam('page', $iPage);

        // PROCESS CUSTOM FIELD
        foreach ($aUsers as $iKey => $aUser)
        {
            if (!isset($aUser['user_group_id']) || empty($aUser['user_group_id']) ||  $aUser['user_group_id'] < 1)
            {
                $aUser['user_group_id'] = $aUsers[$iKey]['user_group_id'] = 5;
                Phpfox::getService('user.process')->updateUserGroup($aUser['user_id'], 5);
                $aUsers[$iKey]['user_group_title'] = _p('user_banned');
            }
            $aBanned =  Phpfox::getService('ban')->isUserBanned($aUser);
            $aUsers[$iKey]['is_banned'] = $aBanned['is_banned'];

            // ADD MORE INFO
            Phpfox::getService('ynmember.member')->processUser($aUsers[$iKey]);
        }

        $aCustomFields = Phpfox::getService('custom')->getForPublic('user_profile');

        // mode page
        Phpfox_Pager::instance()->set(array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount()
        ));

        $this->template()
            ->setPhrase(array(
                'please_enter_only_numbers'
            ))
            ->setHeader('cache', array(
                    'country.js' => 'module_core'
                )
            )
            ->assign(array(
                    'iCnt' => $iCnt,
                    'sFoundMessage' => $this->buildFoundMessage($sView, $iCnt),
                    'aUsers' => $aUsers,
                    'bExtend' => $bExtend,
                    'bIsSearch' => $oFilter->isSearch(),
                    'bIsAdvSearch' => $bIsAdvSearch,
                    'bIsInSearchMode' => ($this->request()->getInt('search-id') ? true : false),
                    'aForms' => $aCustomSearch,
                    'aCustomFields' => $aCustomFields,
                    'sView' => $sView,
                    'amCorePath' =>  Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-member',
                )
            );
    }

    public function buildFoundMessage($sView = '', $iCnt = 0)
    {
        $sFoundMessage = '';
        switch ($sView)
        {
            case 'featured':
                if ($iCnt == 1)
                    $sFoundMessage = _p('1_featured_member');
                else
                    $sFoundMessage = _p('n_featured_members', ['number' => $iCnt]);

                break;
            case 'my':
                if ($iCnt == 1)
                    $sFoundMessage = _p('1_friend');
                else
                    $sFoundMessage = _p('n_friends', ['number' => $iCnt]);
                break;
            default:
                if ($iCnt == 1)
                    $sFoundMessage = _p('1_member_found');
                else
                    $sFoundMessage = _p('n_members_found', ['number' => $iCnt]);

                break;
        }

        return $sFoundMessage;
    }

    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('ynmember.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}
