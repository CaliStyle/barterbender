<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_DetailCoreVideosList extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aYnDirectoryDetail = $this->getParam('aYnDirectoryDetail');
        $sType = 'videos';

        $iItemPerPage = 5;
        $iPage = 1;
        $aConds = array(' 1=1 ');
        $aExtra['order'] = 'm.time_stamp DESC';

        $sModuleId = Phpfox::getService('directory.helper')->getModuleIdVideo();
        $hidden_select = '';

        if ($aVals = $this->getParam('aQueryParam')) {
            $hidden_select = $aVals['hidden_select'];
            if (isset($aVals['keyword']) && $aVals['keyword']) {
                $sKeywordParse = Phpfox::getLib('parse.input')->clean($aVals['keyword']);
                $aConds[] = 'm.title like \'%' . $sKeywordParse . '%\' ';
            }

            if (isset($aVals['filterinbusiness_when']) && $aVals['filterinbusiness_when']) {
                $iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'),
                    Phpfox::getTime('Y'));
                $field = 'm.time_stamp';
                switch ($aVals['filterinbusiness_when']) {
                    case 'today':
                        $iEndDay = Phpfox::getLib('date')->mktime(23, 59, 0, Phpfox::getTime('m'), Phpfox::getTime('d'),
                            Phpfox::getTime('Y'));
                        $aConds[] = '  (' . $field . ' >= \'' . Phpfox::getLib('date')->convertToGmt($iTimeDisplay) . '\' AND ' . $field . ' < \'' . Phpfox::getLib('date')->convertToGmt($iEndDay) . '\')';
                        break;
                    case 'this_week':
                        $aConds[] = '  ' . $field . ' >= \'' . Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekStart()) . '\'';
                        $aConds[] = '  ' . $field . ' <= \'' . Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekEnd()) . '\'';
                        break;
                    case 'this_month':
                        $aConds[] = '  ' . $field . ' >= \'' . Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getThisMonth()) . '\'';
                        $iLastDayMonth = Phpfox::getLib('date')->mktime(0, 0, 0, date('n'),
                            Phpfox::getLib('date')->lastDayOfMonth(date('n')), date('Y'));
                        $aConds[] = '  ' . $field . ' <= \'' . Phpfox::getLib('date')->convertToGmt($iLastDayMonth) . '\'';
                        break;
                    // case 'upcoming':
                    // 	break;
                    default:
                        break;
                }
            }

            if (isset($aVals['filterinbusiness_sort']) && $aVals['filterinbusiness_sort']) {
                switch ($aVals['filterinbusiness_sort']) {
                    case 'latest':
                        $aExtra['order'] = "m.video_id DESC";
                        break;
                    case 'most_viewed':
                        $aExtra['order'] = "m.total_view DESC";
                        break;
                    case 'most_liked':
                        $aExtra['order'] = "m.total_like DESC";
                        break;
                    case 'most_discussed':
                        $aExtra['order'] = "m.total_comment DESC";
                        break;
                }
            }

            if (isset($aVals['filterinbusiness_show']) && $aVals['filterinbusiness_show']) {
                $iItemPerPage = (int)$aVals['filterinbusiness_show'];
            }

            if (isset($aVals['page']) && $aVals['page']) {
                $iPage = $aVals['page'];
            }
        }

        $aExtra['limit'] = $iItemPerPage;
        $aExtra['page'] = $iPage;

        $aBusiness = $aYnDirectoryDetail['aBusiness'];
        if (isset($aBusiness['business_id']) == false) {
            $hidden_businessid = (int)$aVals['hidden_businessid'];
            $aBusiness = Phpfox::getService('directory')->getBusinessById($hidden_businessid);
        }

        list($aVideos, $iCountVideos) = Phpfox::getService('directory')->getVByBusinessId($aBusiness['business_id'], $aConds, $aExtra, true);
        foreach ($aVideos as $iKey => $aRow) {
            $aVideos[$iKey]['link'] = Phpfox::permalink('video.play', $aRow['video_id'], $aRow['title']);
            $aVideos[$iKey]['hasPermission'] = false;
            $aVideos[$iKey]['duration'] = Phpfox::getService('directory')->getDuration($aVideos[$iKey]['duration']);
            Phpfox::getService('v.video')->convertImagePath($aVideos[$iKey], 500);
        }

        $this->setParam('aPagingParams', array(
            'total_all_result' => $iCountVideos,
            'total_result' => count($aVideos),
            'page' => $iPage,
            'limit' => $iItemPerPage
        ));

        $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aBusiness['business_id'],
                $aBusiness['name']) . "{$sType}/";
        $this->template()->assign(array(
                'aYnDirectoryDetail' => $aYnDirectoryDetail,
                'aVideos' => $aVideos,
                'iCountVideos' => $iCountVideos,
                'sLink' => $sLink,
                'bShowModerator' => false,
                'hidden_select' => $hidden_select,
                'sCustomClassName' => 'ync-block'
            )
        );
    }

}
