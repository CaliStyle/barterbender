<?php

/**
 * [PHPFOX_HEADER]
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		PhuongNV
 * @package  		yn_member
 */

namespace Apps\YNC_Member\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;

class WriteReviewController extends Phpfox_Component
{

    public function process()
    {
        // require user and redirect to permission missing page
        Phpfox::isUser(true);
        $iUserId = $this->request()->get('user_id');
        $bIsEdit = false;

        $iRating = 0;
        $bAllow = Phpfox::getUserId() == $iUserId ? user('ynmember_add_review_self') : user('ynmember_add_review_others');;

        if ($iReviewId = $this->request()->get('review_id')) {

            $bIsEdit = true;
            $aRow = Phpfox::getService('ynmember.review')->getForEdit($iReviewId);
            if ($aRow) {
                $this->template()->assign(array(
                        'aForms' => $aRow
                    )
                );
                // init rating
                $iRating = $aRow['rating'];
                $bIsEdit = true;
                $bAllow = Phpfox::getUserId() == $aRow['user_id'] ? user('ynmember_edit_review_self') : user('ynmember_edit_review_others');
            }
        }

        if (!$bAllow) {
            Phpfox_Error::display(_p("You don't have permission to do this action."));
        }
        $aUser = Phpfox::getService('user')->get($iUserId);
        $sTitle = $bIsEdit ? _p('edit_review_for_fullname', ['full_name' => $aUser['full_name']]) : _p('add_review_for_fullname', ['full_name' => $aUser['full_name']]);
        $this->template()->setTitle($sTitle)
            ->setBreadCrumb($sTitle, '', true)
        ;

        // validating stuff
        $aValidationParams = $this->getValidatorParams();
        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'ynmember_js_review_form',
                'aParams' => $aValidationParams
            )
        );

        // Custom field
        $aCustomFields = Phpfox::getService('ynmember.custom')->getAllCustomField();
        $aCustomData = array();
        if($bIsEdit)
        {
            $aCustomDataTemp = Phpfox::getService('ynmember.custom')->getCustomFieldByReviewId($aRow['review_id']);
            if(count($aCustomFields)) {
                foreach ($aCustomFields as $aField) {
                    foreach ($aCustomDataTemp as $aFieldValue) {
                        if($aField['field_id'] == $aFieldValue['field_id']) {
                            $aCustomData[] = $aFieldValue;
                        }
                    }
                }
            }
        }

        if(count($aCustomData)) {
            $aCustomFields  = $aCustomData;
        }

        $this->template()->setBreadCrumb($sTitle, '', false)
            ->assign([
                'sCreateJs' => $oValid->createJs(),
                'sGetJsForm' => $oValid->getJsForm(),
                'bIsEdit' => $bIsEdit,
                'iRating' => $iRating,
                'iUserId' => $iUserId,
                'aCustomFields' => $aCustomFields
            ]);
    }

    /**
     * redefine validation params here
     *
     * @return array
     */
    private function getValidatorParams()
    {
        $aValidationParams = [
            'title' => [
                'def' => 'required',
                'title' => _p('Review title is required')
            ],
            'text' => [
                'def' => 'required',
                'title' => _p('Please enter message for you review')
            ],
        ];

        return $aValidationParams;
    }
}