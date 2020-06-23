<?php

namespace Apps\PHPfox_Groups\Service;

use Phpfox;
use Phpfox_Pages_Browse;

/**
 * Class Browse
 *
 * @package Apps\PHPfox_Groups\Service
 */
class Browse extends Phpfox_Pages_Browse
{
    public function getFacade()
    {
        return Phpfox::getService('groups.facade');
    }

    public function processRows(&$aRows)
    {
        foreach ($aRows as $iKey => $aRow) {
            Phpfox::getService('groups')->getActionsPermission($aRows[$iKey], 'pending');

            if (!empty($aRow['category_id'])) {
                $aRows[$iKey]['category_link'] = Phpfox::permalink('groups.sub-category', $aRow['category_id'], $aRow['category_name']);
            }
            else {
                $aRows[$iKey]['type_link'] = Phpfox::permalink('groups.category', $aRow['type_id'], $aRow['type_name']);
            }
            $aRows[$iKey]['link'] = $this->getFacade()->getItems()->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

            list($iCnt, $aMembers) = Phpfox::getService('groups')->getMembers($aRow['page_id']);
            $aRows[$iKey]['members'] = $aMembers;
            $aRows[$iKey]['total_members'] = $iCnt;
            $aRows[$iKey]['remain_members'] = $iCnt - 3;
            $aRows[$iKey]['text_parsed'] = Phpfox::getService('groups')->getInfo($aRow['page_id'], true);
        }
    }
}
