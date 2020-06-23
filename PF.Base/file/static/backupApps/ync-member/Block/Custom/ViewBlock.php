<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\YNC_Member\Block\Custom;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
defined('PHPFOX') or exit('NO DICE!');
class ViewBlock extends \Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        /*custom field*/
        $iReviewId = $this->getParam('review_id');
        $aCustomFields = Phpfox::getService('ynmember.custom')->getAllCustomField();
        $aCustomData = array();
        if($iReviewId){
            $aCustomDataTemp = Phpfox::getService('ynmember.custom')->getCustomFieldByReviewId($iReviewId);

            if(count($aCustomFields)){
                foreach ($aCustomFields as $aField) {
                    foreach ($aCustomDataTemp as $aFieldValue) {
                        if($aField['field_id'] == $aFieldValue['field_id']){
                            $aCustomData[] = $aFieldValue;
                        }
                    }
                }
            }
        }

        if(count($aCustomData)){
            $aCustomFields  = $aCustomData;
        }

        $this->template()->assign(array(
            'aCustomFields' => $aCustomFields,
        ));
    }
}
