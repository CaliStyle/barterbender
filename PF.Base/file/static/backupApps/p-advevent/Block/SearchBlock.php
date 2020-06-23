<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class SearchBlock extends Phpfox_Component
{
    public function process()
    {
        $requestObject = $this->request();
        $searchParams = !empty($requestObject->get('search')) ? $requestObject->get('search') : [];
        $isCoreSearch = false;
        $sSort = $requestObject->get('sort');
        $sWhen = $requestObject->get('when');
        $sShow = $requestObject->get('show');
        if(!empty($sWhen)) {
            $isCoreSearch = true;
        }

        $vals = $requestObject->get('val');
        $myCountry = Phpfox::isUser() ? Phpfox::getUserBy('country_iso') : null;
        $countryIso = !empty($vals['country_iso']) && !$isCoreSearch ? $vals['country_iso'] : (!empty($myCountry) ? $myCountry : '');
        $countryChildId = !empty($vals['country_child_id']) && !$isCoreSearch ? $vals['country_child_id'] : '';
        $this->setParam(['country_child_value' => $countryIso, 'country_child_id' => $countryChildId ]);

        if(empty($searchParams['time_type']) || $isCoreSearch) {
            $searchParams['time_type'] = _p('all');
        }
        if(!empty($searchParams['address']) && !$isCoreSearch) {
            $searchParams['address'] = urldecode($searchParams['address']);
        }
        if(!empty($searchParams['city']) && !$isCoreSearch) {
            $searchParams['city'] = urldecode($searchParams['city']);
        }

        $assignedArray = [];

        $glat = floatval($searchParams['glat']);
        $glong = floatval($searchParams['glong']);
        if($glat != -1 && $glong != -1){
            $assignedArray['glat'] = $glat;
            $assignedArray['glong'] = $glong;
        }

        $assignedArray['time'] = !empty($searchParams['stime']) && !empty($searchParams['etime']) && !$isCoreSearch? ($searchParams['stime'] . ' - ' . $searchParams['etime']) : '';
        if(!empty($searchParams['time_type']) && in_array($searchParams['time_type'], ['Custom Range', _p('advevent_choose_date_lowercase')]) && !$isCoreSearch) {
            $assignedArray['custom_start_time'] = $searchParams['stime'];
            $assignedArray['custom_end_time'] = $searchParams['etime'];
        }

        $sBaseStr = _p('number_per_page');
        $aShows = array(
            array("value" => 10, "label" => str_replace('{number}', 10, $sBaseStr)),
            array("value" => 15, "label" => str_replace('{number}', 15, $sBaseStr)),
            array("value" => 18, "label" => str_replace('{number}', 18, $sBaseStr)),
            array("value" => 21, "label" => str_replace('{number}', 21, $sBaseStr))
        );

        $statusArray = [
            _p('upcoming') => 'upcoming',
            _p('ongoing') => 'ongoing',
            _p('s_past') => 'past',
        ];

        if($isCoreSearch) {
            $searchParams['advsearch'] = 0;
        }

        $this->template()->assign(array(
                'aShows' => $aShows,
                'sSort' => $sSort,
                'sWhen' => $sWhen,
                'sShow' => $sShow,
                'aForms' => array_merge($searchParams, $assignedArray),
                'sCustomClassName' => 'p-block',
                'statusArray' => $statusArray,
                'defaultCountry' => $myCountry,
                'isCoreSearch' => (int)$isCoreSearch
            )
        );

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean() {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_block_search_clean')) ? eval($sPlugin) : false);
    }
}