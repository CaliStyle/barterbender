<?php

namespace Apps\YNC_WebPush\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Database;
use Phpfox_Error;
use Phpfox_Locale;
use Phpfox_Pager;
use Phpfox_Plugin;
use Phpfox_Search;

class ManageSubscribersController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isAdmin(true);
        $bCustomSort = false;
        $aSearch = request()->get('search');
        $aAge = array();
        $iTemplateId = $this->request()->getInt('template');
        for ($i = Phpfox::getService('user')->age(Phpfox::getService('user')->buildAge(1, 1,
            Phpfox::getParam('user.date_of_birth_end'))); $i <= Phpfox::getService('user')->age(Phpfox::getService('user')->buildAge(1,
            1, Phpfox::getParam('user.date_of_birth_start'))); $i++) {
            $aAge[$i] = $i;
        }

        $iYear = date('Y');
        $aUserGroups = array();
        foreach (Phpfox::getService('user.group')->get() as $aUserGroup) {
            $aUserGroups[$aUserGroup['user_group_id']] = Phpfox_Locale::instance()->convert($aUserGroup['title']);
        }
        $sDefaultOrderName = 'u.full_name';
        $sDefaultSort = 'ASC';
        if (Phpfox::getParam('user.user_browse_default_result') == 'last_login') {
            $sDefaultOrderName = 'u.last_login';
            $sDefaultSort = 'DESC';
        }
        if (!empty($aSearch) && isset($aSearch['sort'])) {
            $aSearchSort = explode(' ', $aSearch['sort']);
            if ($aSearch['sort'] == 'u.last_login') {
                $sDefaultSort = 'DESC';
            }

            if (isset($aSearchSort[1])) {
                $sDefaultSort = $aSearchSort[1];
                $bCustomSort = true;
            }
        }

        $iDisplay = 12;
        $aPages = array(21, 31, 41, 51);
        $aDisplays = array();
        foreach ($aPages as $iPageCnt) {
            $aDisplays[$iPageCnt] = _p('per_page', array('total' => $iPageCnt));
        }
        $aSorts = array(
            'u.full_name' => _p('name'),
            'u.joined' => _p('joined'),
            'u.last_login' => _p('last_login'),
            'u.last_activity' => _p('last_activity'),
            'ug.title' => _p('groups'),
            'u.user_id' => _p('id')
        );
        $aGenders = Phpfox::getService('core')->getGenders();
        $aGenders[''] = _p('all_members');
        $aFilters = array(
            'display' => array(
                'type' => 'select',
                'options' => $aDisplays,
                'default' => $iDisplay
            ),
            'sort' => array(
                'type' => 'select',
                'options' => $aSorts,
                'default' => $sDefaultOrderName
            ),
            'sort_by' => array(
                'type' => 'select',
                'options' => array(
                    'DESC' => _p('descending'),
                    'ASC' => _p('ascending')
                ),
                'default' => $sDefaultSort
            ),
            'keyword' => array(
                'type' => 'input:text',
                'size' => 15,
                'class' => 'txt_input'
            ),
            'type' => array(
                'type' => 'select',
                'options' => array(
                    '0' => array(
                        _p('email_name'),
                        'AND ((u.full_name LIKE \'%[VALUE]%\' OR (u.email LIKE \'%[VALUE]@%\' OR u.email = \'[VALUE]\'))' . (defined('PHPFOX_IS_ADMIN_SEARCH') ? ' OR u.email LIKE \'%[VALUE]\'' : '') . ')'
                    ),
                    '1' => array(
                        _p('email'),
                        'AND ((u.email LIKE \'%[VALUE]@%\' OR u.email = \'[VALUE]\'' . (defined('PHPFOX_IS_ADMIN_SEARCH') ? ' OR u.email LIKE \'%[VALUE]%\'' : '') . '))'
                    ),
                    '2' => array(_p('name'), 'AND (u.full_name LIKE \'%[VALUE]%\')')
                ),
                'depend' => 'keyword'
            ),
            'group' => array(
                'type' => 'select',
                'options' => $aUserGroups,
                'add_any' => true,
                'search' => 'AND u.user_group_id = \'[VALUE]\''
            ),
            'gender' => array(
                'type' => 'select',
                'options' => $aGenders,
                'default_view' => '',
                'search' => 'AND u.gender = \'[VALUE]\'',
                'suffix' => '<br />'
            ),
            'from' => array(
                'type' => 'select',
                'options' => $aAge,
                'select_value' => _p('from')
            ),
            'to' => array(
                'type' => 'select',
                'options' => $aAge,
                'select_value' => _p('to')
            ),
            'country' => array(
                'type' => 'select',
                'options' => Phpfox::getService('core.country')->get(),
                'search' => 'AND u.country_iso = \'[VALUE]\'',
                'add_any' => true,
                // 'style' => 'width:150px;',
                'id' => 'country_iso'
            ),
            'country_child_id' => array(
                'type' => 'select',
                'search' => 'AND ufield.country_child_id = \'[VALUE]\'',
                'clone' => true
            ),
            'status' => array(
                'type' => 'select',
                'options' => array(
                    '2' => _p('all_members'),
                    '1' => _p('featured_members'),
                    '4' => _p('online'),
                ),
                'default_view' => '2',
            ),
            'city' => array(
                'type' => 'input:text',
                'size' => 15,
                'search' => 'AND ufield.city_location LIKE \'%[VALUE]%\''
            ),
            'zip' => array(
                'type' => 'input:text',
                'size' => 10,
                'search' => 'AND ufield.postal_code = \'[VALUE]\''
            ),
            'show' => array(
                'type' => 'select',
                'options' => array(
                    '1' => _p('name_and_photo_only'),
                    '2' => _p('name_photo_and_users_details')
                ),
                'default_view' => (Phpfox::getParam('user.user_browse_display_results_default') == 'name_photo_detail' ? '2' : '1')
            ),
            'ip' => array(
                'type' => 'input:text',
                'size' => 10
            )
        );
        $aCallback = $this->getParam('aCallback', false);
        if ($aCallback !== false) {
            if (!Phpfox::getService('group')->hasAccess($aCallback['item'], 'can_view_members')) {
                return Phpfox_Error::display(_p('members_section_is_closed'));
            }
        }
        if (!Phpfox::getUserParam('user.can_search_by_zip')) {
            unset ($aFilters['zip']);
        }
        $aSearchParams = array(
            'type' => 'browse',
            'filters' => $aFilters,
            'search' => 'keyword',
            'custom_search' => true,
            'no_session_search' => true
        );
        $oFilter = Phpfox_Search::instance()->set($aSearchParams);
        $sStatus = $oFilter->get('status');
        $sView = $this->request()->get('view');
        $aCustomSearch = $oFilter->getCustom();
        $bIsOnline = false;
        $mFeatured = false;
        $bIsGender = false;
        switch ((int)$sStatus) {
            case 1:
                $mFeatured = true;
                break;
            case 3:
                $oFilter->setCondition('AND u.status_id = 1');
                break;
            case 4:
                $bIsOnline = true;
                break;
            case 5:
                $oFilter->setCondition('AND u.view_id = 1');
                break;
            case 6:
                $oFilter->setCondition('AND u.view_id = 2');
                break;
            default:
                break;
        }
        if ($bCustomSort) {
            $oFilter->setSort($aSearchSort[0]);
        }
        if (!empty($sView)) {
            switch ($sView) {
                case 'online':
                    $bIsOnline = true;
                    break;
                case 'featured':
                    $mFeatured = true;
                    break;
                case 'spam':
                    $oFilter->setCondition('u.total_spam > ' . (int)Phpfox::getParam('core.auto_deny_items'));
                    break;
                case 'pending':
                    if (defined('PHPFOX_IS_ADMIN_SEARCH')) {
                        $oFilter->setCondition('u.view_id = 1');
                    }
                    break;
                case 'top':
                    $bExtendContent = true;
                    if (($iUserGenderTop = $this->request()->getInt('topgender'))) {
                        $oFilter->setCondition('AND u.gender = ' . (int)$iUserGenderTop);
                    }

                    $iFilterCount = 0;
                    $aFilterMenuCache = array();

                    $aFilterMenu = array(
                        _p('all_members') => '',
                        _p('male') => '1',
                        _p('female') => '2'
                    );

                    if ($sPlugin = Phpfox_Plugin::get('user.component_controller_browse_genders_top_users')) {
                        eval($sPlugin);
                    }

                    $this->template()->setTitle(_p('top_rated_members'))
                        ->setBreadCrumb(_p('top_rated_members'),
                            $this->url()->makeUrl('user.browse', array('view' => 'top')));

                    foreach ($aFilterMenu as $sMenuName => $sMenuLink) {
                        $iFilterCount++;
                        $aFilterMenuCache[] = array(
                            'name' => $sMenuName,
                            'link' => $this->url()->makeUrl('user.browse',
                                array('view' => 'top', 'topgender' => $sMenuLink)),
                            'active' => ($this->request()->get('topgender') == $sMenuLink ? true : false),
                            'last' => (count($aFilterMenu) === $iFilterCount ? true : false)
                        );

                        if ($this->request()->get('topgender') == $sMenuLink) {
                            $this->template()->setTitle($sMenuName)->setBreadCrumb($sMenuName, null, true);
                        }
                    }

                    $this->template()->assign(array(
                            'aFilterMenus' => $aFilterMenuCache
                        )
                    );

                    break;
                default:

                    break;
            }
        }

        if (($iFrom = $oFilter->get('from')) || ($iFrom = $this->request()->getInt('from'))) {
            $oFilter->setCondition('AND u.birthday_search <= \'' . Phpfox::getLib('date')->mktime(0, 0, 0, 1, 1,
                    $iYear - $iFrom) . '\'' . ' AND ufield.dob_setting IN(0,1,2)');
            $bIsGender = true;
        }
        if (($iTo = $oFilter->get('to')) || ($iTo = $this->request()->getInt('to'))) {
            $oFilter->setCondition('AND u.birthday_search >= \'' . Phpfox::getLib('date')->mktime(0, 0, 0, 1, 1,
                    $iYear - $iTo) . '\'' . ' AND ufield.dob_setting IN(0,1,2)');
            $bIsGender = true;
        }

        if (($sLocation = $this->request()->get('location'))) {
            $oFilter->setCondition('AND u.country_iso = \'' . Phpfox_Database::instance()->escape($sLocation) . '\'');
        }

        if (($sGender = $this->request()->getInt('gender'))) {
            $oFilter->setCondition('AND u.gender = \'' . Phpfox_Database::instance()->escape($sGender) . '\'');
        }

        if (($sLocationChild = $this->request()->getInt('state'))) {
            $oFilter->setCondition('AND ufield.country_child_id = \'' . Phpfox_Database::instance()->escape($sLocationChild) . '\'');
        }

        if (($sLocationCity = $this->request()->get('city-name'))) {
            $oFilter->setCondition('AND ufield.city_location = \'' . Phpfox_Database::instance()->escape(Phpfox::getLib('parse.input')->convert($sLocationCity)) . '\'');
        }

        $oFilter->setCondition('AND u.status_id = 0 AND u.view_id = 0');
        if (Phpfox::isUser()) {
            $aBlockedUserIds = Phpfox::getService('user.block')->get(null, true);
            if (!empty($aBlockedUserIds)) {
                $oFilter->setCondition('AND u.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')');
            }
        }

        if (defined('PHPFOX_IS_ADMIN_SEARCH') && ($sIp = $oFilter->get('ip'))) {
            Phpfox::getService('user.browse')->ip($sIp);
        }
        $bExtend = true;
        $iPage = $this->request()->getInt('page');
        $iPageSize = $oFilter->getDisplay();
        list($iCnt, $aUsers) = Phpfox::getService('yncwebpush')->conditions($oFilter->getConditions())
            ->callback($aCallback)
            ->sort($oFilter->getSort())
            ->page($oFilter->getPage())
            ->limit($iPageSize)
            ->online($bIsOnline)
            ->extend((isset($bExtendContent) ? true : $bExtend))
            ->featured($mFeatured)
            ->custom($aCustomSearch)
            ->gender($bIsGender)
            ->get();
        $iCnt = $oFilter->getSearchTotal($iCnt);
        Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));
        list($bFieldExist, $aCustomFields) = Phpfox::getService('custom')->getForPublic('user_profile', 0, true);
        $this->template()->setTitle(_p('manage_subscribers'))
            ->setBreadCrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('Web Push Notification'), $this->url()->makeUrl('admincp.app', ['id' => 'YNC_WebPush']))
            ->setBreadCrumb(_p('manage_subscribers'), $this->url()->makeUrl('admincp.yncwebpush.manage-subscribers'))
            ->setHeader([
                'jscript/admin.js' => 'app_ync-webpush',
                'css/admin.css' => 'app_ync-webpush',
                'country.js' => 'module_core'
            ])
            ->assign([
                'aUsers' => $aUsers,
                'aCustomFields' => $aCustomFields,
                'bShowAdvSearch' => $bFieldExist,
                'iTemplateId' => $iTemplateId
            ]);
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('yncwebpush.component_controller_admincp_manage_subscribers_clean')) ? eval($sPlugin) : false);
    }
}