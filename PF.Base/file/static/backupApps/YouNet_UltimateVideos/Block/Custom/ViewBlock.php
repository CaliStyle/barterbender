<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\YouNet_UltimateVideos\Block\Custom;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class ViewBlock extends \Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        /*custom field*/
        $iVideoId = $this->getParam('video_id');
        $iCategory = Phpfox::getService('ultimatevideo')->getParentCategoryOfVideo($iVideoId);
        $aCustomFields = Phpfox::getService('ultimatevideo')->getCustomFieldByCategoryId($iCategory);
        $keyCustomField = array();
        $aCustomData = array();
        if ($iVideoId) {
            $aCustomDataTemp = Phpfox::getService('ultimatevideo.custom')->getCustomFieldByVideoId($iVideoId);

            if (count($aCustomFields)) {
                foreach ($aCustomFields as $aField) {
                    foreach ($aCustomDataTemp as $aFieldValue) {
                        if ($aField['field_id'] == $aFieldValue['field_id']) {
                            $aCustomData[] = $aFieldValue;
                        }
                    }
                }
            }
        }

        if (count($aCustomData)) {
            $aCustomFields = $aCustomData;
        }


        $this->template()->assign(array(
            'aCustomFields' => $aCustomFields,
        ));
    }
}
