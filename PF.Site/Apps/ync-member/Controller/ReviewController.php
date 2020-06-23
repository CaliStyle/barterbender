<?php

namespace Apps\YNC_Member\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Pager;
use Core\Route\Controller;

class ReviewController extends Phpfox_Component
{
    public function process()
    {
        $template = $this->template();
        $search = $this->search();
        $request = $this->request();
        $url = $this->url();
        $bSingleUser = false;
        if ($iUserId = $request->get('user_id', 0)){
            $bSingleUser = true;
        }

        $template->setTitle(_p('Members Review'));

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

        if ($bSingleUser) {
            $iPage = $this->request()->getInt('page');
            $iPageSize = 10;
            $aUser = Phpfox::getService('user')->get($iUserId);
            Phpfox::getService('ynmember.member')->processUser($aUser, ['Review']);

            $sTitle = _p('review_for_full_name', ['full_name' => $aUser['full_name']]);
            $breadCrumb = $url->makeUrl('profile', array($aUser['user_name']));
            $template->setBreadCrumb($sTitle , $breadCrumb);

            if (!$aUser['is_review_written'] && Phpfox::getService('ynmember.review')->canWriteReview($iUserId)) {
                sectionMenu(_p('Create New Review'), $url->makeUrl('ynmember.writereview', ['user_id'=>$iUserId]), ['css_class'=>'popup']);
            }

            $aReviews = Phpfox::getService('ynmember.review.browse')->getReviewsOfUser($iUserId, $iPage, $iPageSize);

            $template->assign([
                'aReviews' => $aReviews,
                'bSingleUser' => true,
                'amCorePath' =>  Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-member'
            ]);

            return;
        }

        // MULTI REVIEW

        $template->setTitle(_p('Members Review'));

        $breadCrumb = $url->makeUrl('ynmember');

        $template->setBreadCrumb(_p('Members Review'), $breadCrumb);

        $aFilters = array(
            'reviewer' => array(
                'type' => 'input:text',
                'size' => 15,
                'search' => 'AND reviewer.full_name LIKE \'%[VALUE]%\'',
            ),
            'rating' => array(
                'type' => 'select',
                'options' => [
                    0 => _p('All ratings'),
                    1 => _p('1 star'),
                    2 => _p('2 stars'),
                    3 => _p('3 stars'),
                    4 => _p('4 stars'),
                    5 => _p('5 stars'),
                ],
                'default_view' => '',
                'search' => 'AND (re.rating = \'[VALUE]\' OR 0 = \'[VALUE]\')',
                'suffix' => '<br />'
            ),
        );

        $bIsProfile = false;

        $sFormAction = $url->makeUrl('ynmember.review');

        $oFilter = $search->set([
            'type' => 'ynmember_review',
            'field' => 're.review_id',
            'filters' => $aFilters,
            'ignore_blocked' => true,
            'search_tool' => [
                'table_alias' => 're',
                'search' => [
                    'action' => $sFormAction,
                    'default_value' => _p('Search...'),
                    'name' => 'search',
                    'field' => ['re.title']
                ],
                'sort' => [
                    'latest' => ['re.time_stamp', _p('Most Recent')],
                    'highest_rated' => ['re.rating', _p('Highest Rated')],
                    'least_rated' => ['re.rating', _p('Least Rated'), 'ASC'],
                    'most_helpful' => ['total_yes', _p('Most Helpful')],
                    'most_viewed' => ['re.total_view', _p('Most Viewed')],
                ],
                'show' => [6, 12, 24]
            ]
        ]);

        $bIsAdvSearch = FALSE;
        if($oFilter->get('form_flag') == 1)
        {
            $bIsAdvSearch = TRUE;
        }

        $aBrowseParams = [
            'module_id' => 'ynmember.review',
            'alias' => 're',
            'field' => 'review_id',
            'table' => Phpfox::getT('ynmember_review'),
            'hide_view' => ['pending', 'my']
        ];

        /***
         * Paging in browse need at least one condition for working
         */
        $this->search()->setCondition('AND 1=1');

        $this->search()->setContinueSearch(true);
        $this->search()->browse()->params($aBrowseParams)->execute();

        $iCnt = $this->search()->browse()->getCount();
        $aReviews = $this->search()->browse()->getRows();

        $template->assign([
            'iCnt' => $iCnt,
            'bSingleUser' => false,
            'bIsAdvSearch' => $bIsAdvSearch,
            'aReviews' => $aReviews,
            'bIsProfile' => $bIsProfile,
            'amCorePath' =>  Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-member'
        ]);
    }

    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('ynmember.component_controller_review_clean')) ? eval($sPlugin) : false);
    }
}
