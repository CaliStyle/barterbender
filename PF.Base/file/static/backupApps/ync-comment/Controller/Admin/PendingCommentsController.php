<?php
namespace Apps\YNC_Comment\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Pager;
use Phpfox_Search;

class PendingCommentsController extends Phpfox_Component
{
    public function process()
    {
        //remove this feature
        Phpfox::getUserParam('comment.can_moderate_comments', true);

        $aVals = $this->request()->getArray('val');
        if ($aIds = $this->request()->getArray('ids')) {
            if (!empty($aVals['approve_selected'])) {
                foreach ($aIds as $iId) {
                    Phpfox::getService('ynccomment.process')->moderate($iId, 'approve', true);
                }
                $this->url()->send('admincp.ynccomment.pending-comments', _p('comment_s_approved_successfully'));
            } elseif (!empty($aVals['deny_selected'])) {
                foreach ($aIds as $iId) {
                    Phpfox::getService('ynccomment.process')->moderate($iId, 'deny', true);
                }
                $this->url()->send('admincp.ynccomment.pending-comments', _p('comment_s_denied_successfully'));
            }
        }
        $iPage = $this->request()->getInt('page');

        $aPages = array(20, 30, 40, 50);
        $aDisplays = array();
        foreach ($aPages as $iPageCnt) {
            $aDisplays[$iPageCnt] = _p('per_page', array('total' => $iPageCnt));
        }

        $aFilters = array(
            'search' => array(
                'type' => 'input:text',
                'search' => "AND ls.name LIKE '%[VALUE]%'"
            ),
            'display' => array(
                'type' => 'select',
                'options' => $aDisplays,
                'default' => '10'
            ),
            'sort' => array(
                'type' => 'select',
                'options' => array(
                    'time_stamp' => _p('last_activity'),
                    'rating ' => _p('rating')
                ),
                'default' => 'time_stamp',
                'alias' => 'cmt'
            ),
            'sort_by' => array(
                'type' => 'select',
                'options' => array(
                    'DESC' => _p('descending'),
                    'ASC' => _p('ascending')
                ),
                'default' => 'DESC'
            )
        );

        $oSearch = Phpfox_Search::instance()->set(array(
                'type' => 'comments',
                'filters' => $aFilters,
                'search' => 'search'
            )
        );

        $oSearch->setCondition('AND cmt.view_id = 1');

        list($iCnt, $aComments) = Phpfox::getService('ynccomment')->get('cmt.*', $oSearch->getConditions(),
            $oSearch->getSort(), $oSearch->getPage(), $oSearch->getDisplay(), null, true);

        foreach ($aComments as $iKey => $aComment) {
            if (Phpfox::hasCallback($aComment['type_id'], 'getItemName')) {
                $aComments[$iKey]['item_name'] = Phpfox::callback($aComment['type_id'] . '.getItemName',
                    $aComment['comment_id'], $aComment['owner_full_name']);
            }
        }

        Phpfox_Pager::instance()->set(array(
            'page' => $iPage,
            'size' => $oSearch->getDisplay(),
            'count' => $oSearch->getSearchTotal($iCnt)
        ));

        $this->template()->setTitle(_p('pending_comments'))
            ->setBreadCrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("Advanced Comment"), $this->url()->makeUrl('admincp.app', ['id' => 'YNC_Comment']))
            ->setBreadCrumb(_p('pending_comments'), null, true)
            ->setHeader('cache', array(
                    'comment.css' => 'style_css',
                    'pager.css' => 'style_css',
                )
            )
            ->assign(array(
                    'aComments' => $aComments,
                    'bIsCommentAdminPanel' => true
                )
            );
    }
}