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

class FeedBack_Component_Controller_Admincp_FeedBacks extends Phpfox_Component
{
    public function isValid($aVals)
    {
        $errors = array();
        if (empty($aVals['title'])) {
            $errors['title'] = _p('feedback.title_cannot_be_empty') . '.';
        }
        if (empty($aVals['description'])) {
            $errors['description'] = _p('feedback.description_of_feedback_cannot_be_empty') . '.';
        }
        return $errors;
    }

    public function process()
    {
        if (isset($_POST['updatestatus'])) {
            $aVals = $this->request()->get('val');
            $isUpdate = Phpfox::getService('feedback.process')->updateStatus($aVals);
            if ($isUpdate) {
                $this->url()->send('admincp.feedback.feedbacks', null,
                    _p('feedback.the_feedback_title_was_update_successfully',
                        array('title' => $aVals['title'])));
            } else {
                $this->url()->send('admincp.feedback.feedbacks', null,
                    'The Feedback "' . $aVals['title'] . '" was updated fail.');
            }

        }
        if (isset($_POST['editfeedbackforadmin'])) {
            $aVals = $this->request()->get('val');

            $errors = $this->isValid($aVals);
            if (empty ($errors)) {
                $oFilter = Phpfox::getLib('parse.input');
                $aVals['title'] = $oFilter->clean(strip_tags($aVals['title']), 255);
                $aVals['description'] = $oFilter->clean(strip_tags($aVals['description']));

                $isUpdate = Phpfox::getService('feedback.process')->update($aVals);
                if ($isUpdate && isset($aVals['send_mail_to_none_user'])) {
                    $isSendMail = (int)$aVals['send_mail_to_none_user'];
                    Phpfox::getService('feedback')->sendMailToNoneUser($aVals['feedback_id'], $isSendMail, $isUpdate);
                }
            } else {
                Phpfox_Error::setDisplay($errors);
            }

            if ($isUpdate) {
                $this->url()->send('admincp.feedback.feedbacks', null,
                    _p('feedback.the_feedback_title_was_update_successfully',
                        array('title' => $aVals['title'])));
            } else {
                $this->url()->send('admincp.feedback.feedbacks', null,
                    'The Feedback "' . $aVals['title'] . '" was updated fail.');
            }
        }
        if ($this->request()->get('deleteselect')) {
            $arr_select = $this->request()->get('arr_selected');
            $arr_select = substr($arr_select, 1);
            if (!empty($arr_select)) {
                $aDeleteItem = explode(',', $arr_select);
                foreach ($aDeleteItem as $iKey) {
                    Phpfox::getService('feedback.process')->delete($iKey);
                }
                /*$isDelete = Phpfox::getLib('database')->delete(Phpfox::getT('feedback'),'feedback_id IN ('.$arr_select.')');
                if($isDelete)
                {*/
                $this->url()->send('admincp.feedback.feedbacks', null,
                    _p('feedback.the_feedback_was_deleted_successfully'));
                //}
            }
        }
        $core_url = Phpfox::getParam('core.path');
        $sCats = phpfox::getService('feedback')->getFeedBackCat();
        $sCats['NULL'] = _p('feedback.uncategorized');
        $aTypes = $sCats;
        $sStatus = Phpfox::getService('feedback')->getFeedBackStatus();
        $sStatus['NULL'] = _p('feedback.unstatus');
        $aStatus = $sStatus;
        $aPages = array(1, 10, 20, 30);
        $aDisplays = array();
        foreach ($aPages as $iPageCnt) {
            $aDisplays[$iPageCnt] = _p('core.per_page', array('total' => $iPageCnt));
        }

        $aSorts = array(
            'fb.time_stamp' => _p('feedback.most_recent'),
            'fb.total_vote' => _p('feedback.most_voted'),
            'fb.total_view' => _p('feedback.most_viewed'),
            'fb.total_comment' => _p('feedback.most_comment'),
            'fb.is_featured' => _p('feedback.featured')
        );

        $aFilters = array(
            'type_cats' => array(
                'type' => 'select',
                'options' => $aTypes,
                'add_any' => true,
                'search' => "fb.feedback_category_id = [VALUE]"
            ),
            'type_status' => array(
                'type' => 'select',
                'options' => $aStatus,
                'add_any' => true,
                'search' => "fb.feedback_status_id = [VALUE]"
            ),
            'display' => array(
                'type' => 'select',
                'options' => $aDisplays,
                'default' => '10'
            ),
            'sort' => array(
                'type' => 'select',
                'options' => $aSorts,
                'default' => 'fb.time_stamp'
            ),
            'sort_by' => array(
                'type' => 'select',
                'options' => array(
                    'DESC' => _p('core.descending'),
                    'ASC' => _p('core.ascending')
                ),
                'default' => 'DESC'
            ),
            'keyword' => array(
                'type' => 'input:text',
                'search' => "( fb.title LIKE '%[VALUE]%' OR fb.feedback_description LIKE '%[VALUE]%' )"
            )

        );
        $oFilter = Phpfox::getLib('search')->set(array(
                'type' => 'feedback',
                'filters' => $aFilters,
                'cache' => true,
                'field' => 'fb.feedback_id',
                'search' => 'search'
            )
        );
        $sCon = $oFilter->getConditions();
        $aSearch = $this->request()->getArray('search');

        /*if ($oFilter->isSearch())
        {

        $oFilter->search('like%', array(
        'fb.title',
        'fb.feedback_description'
        ), $aSearch['keyword']);

        $aSearchResults = Phpfox::getService('feedback')->getSearch($oFilter->getConditions(), $oFilter->getSort());
        $oFilter->cacheResults('search', $aSearchResults);
        } */

        $iPage = $this->request()->getInt('page');
        $iPageSize = 10;
        list($iCnt, $aFeedBacks) = Phpfox::getService('feedback')->getFeedBacks($oFilter->getConditions(),
            $oFilter->getSort(), $oFilter->getPage(), $iPageSize);

        /*foreach ($aFeedBacks as $key => $aFeedBack)
        {
        $aFeedBacks[$key]['time_stamp'] = Phpfox::getLib('date')->convertTime($aFeedBack['time_stamp'], 'F j, Y, g:i a');
        }*/
        $iCnt = $oFilter->getSearchTotal($iCnt);

        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));
        $sFormUlr = $this->url()->makeUrl('admincp.feedback.feedbacks');
        $this->template()
            ->setHeader('cache', array(
//                'backend.css' => 'module_feedback',
//                'pager.css' => 'style_css',
                'feedback.js' => 'module_feedback',
            ))
            ->setTitle('FeedBack')
            ->assign(array(
                'core_url' => $core_url,
                'aFeedBacks' => $aFeedBacks,
                'sCats' => $sCats,
                'sStatus' => $sStatus,
                'aSorts' => $aSorts,
                'sFormUrl' => $sFormUlr,

            ));
        if ($this->request()->get('search-rid') != "" || $this->request()->get('search-id') != "" || $oFilter->isSearch() == true) {
            $this->template()->assign(
                array(
                    'is_search' => 1,
                )
            );
        }
        $this->template()->setBreadCrumb(_p('feedback.manage_feedbacks'),
            $this->url()->makeUrl('admincp.feedback.feedbacks'))
            ->setPhrase(array(
                'feedback.are_you_sure_you_want_to_delete_these_feedbacks',
                'feedback.no_feedback_selected_to_delete',
                'feedback.are_you_sure'
            ));
    }

}

?>
