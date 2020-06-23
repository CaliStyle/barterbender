<?php

namespace Apps\YNC_Member\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Pager;
use Core\Route\Controller;

class BirthdayController extends Phpfox_Component
{
    public function process()
    {
        $template = $this->template();
        $search = $this->search();
        $request = $this->request();
        $url = $this->url();

        $template->setTitle(_p('Members Birthday'));

        $template->setBreadCrumb(_p('Members'), $url->makeUrl('ynmember'))
            ->setBreadCrumb(_p('Members Birthday'), $url->makeUrl('ynmember.birthday'));

        $aFilterMenu = [
            _p('All Members') => '',
        ];

        if (Phpfox::isModule('friend')) {
        if (Phpfox::isUser() && ($iCnt = Phpfox::getService('ynmember.member')->getFriendCount(Phpfox::getUserId()))) {
            $iCnt = ($iCnt >= 100) ? '99+' : $iCnt;
            $aFilterMenu[_p('My Friends') . '<span class="pending count-item">' . $iCnt . '</span>'] = 'my';
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

        $bIsProfile = false;
        $sView = $request->get('view');

        // Check if we are on advanced search mode
        $bIsAdvSearch = FALSE;
        if($this->request()->get('formflag'))
        {
            $bIsAdvSearch = TRUE;
        }

        $sFormAction = $url->makeUrl('ynmember.birthday');

        $iLimit = 8;
        $sStart = $this->request()->get('from_date', '');
        $sEnd = $this->request()->get('to_date', '');
        $iDaysInAdvance = Phpfox::getParam('friend.days_to_check_for_birthday') >= 0 ? Phpfox::getParam('friend.days_to_check_for_birthday') : 0;

        if ($sStart) {
            $sStartSearch = str_replace('/', '', $sStart);
        } else {
            $sStartSearch = date('md', strtotime('+1 day'));
        }
        if ($sEnd) {
            $sEndSearch = str_replace('/', '', $sEnd);
        } else {
            $sEndSearch= date('md', strtotime('+'.$iDaysInAdvance.' day'));
        }
        $sTodaySearch = (string)date('md');

        $iPage = $this->request()->get('page', 1);

        $sToday = date('d F');
        list($iCntToday, $aTodayBirthdays) = Phpfox::getService('ynmember.browse')->getBirthdayInRange($sTodaySearch, $sTodaySearch);
        list($iCnt, $aUsers) = Phpfox::getService('ynmember.browse')->getBirthdayInRange($sStartSearch, $sEndSearch, $iPage, $iLimit);
        foreach ($aTodayBirthdays as $key => $aItem) {
            Phpfox::getService('ynmember.member')->processBirthdayWish($aTodayBirthdays[$key]);
        }
        foreach ($aUsers as $key => $aItem) {
            Phpfox::getService('ynmember.member')->processUser($aUsers[$key]);
            Phpfox::getService('ynmember.member')->processBirthdayWish($aUsers[$key]);
            Phpfox::getService('ynmember.member')->processBirthdate($aUsers[$key]);
        }
        foreach ($aTodayBirthdays as $key => $aTodayBirthday) {
            Phpfox::getService('ynmember.member')->processUser($aTodayBirthdays[$key]);
        }

        Phpfox::getLib('pager')->set([
            'page' => $iPage,
            'size' => $iLimit,
            'count' => $iCnt]);

        $template->assign([
            'page'  => $iPage,
            'iCntToday' => $iCntToday,
            'sToday' => $sToday,
            'sStart' => $sStart,
            'sEnd' => $sEnd,
            'aUsers' => $aUsers,
            'aTodayBirthdays' => $aTodayBirthdays,
            'amCorePath' =>  Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-member',
        ]);
    }

    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('ynmember.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}
