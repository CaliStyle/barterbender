<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Component_Block_Search extends Phpfox_Component {

    public function process() {
        $sSearch = $this->request()->get('search');
        $sSort = $this->request()->get('sort');
        $sWhen = $this->request()->get('when');
        $sShow = $this->request()->get('show');
        $sLocation = $this->request()->get('location');
        $sAddress = $this->request()->get('address');
        $sCity = $this->request()->get('city');
        $sZipcode = $this->request()->get('zipcode');
        $rangevalueto = $this->request()->get('rangevalueto');
        $rangevaluefrom = $this->request()->get('rangevaluefrom');
        $rangetype =  $this->request()->get('rangetype');

        $glat = floatval($this->request()->get('glat'));
        $glong = floatval($this->request()->get('glong'));
        if($glat != -1 && $glong != -1){
            $this->template()->assign(array(
                'glat' => $glat,
                'glong' => $glong
                )
            );
        }

        $aForms = array(
            'country_iso' => $this->request()->get('country')
        );
        $this->setParam(array(
			'country_child_value' => $this->request()->get('country'),
			'country_child_id' => $this->request()->get('childid')
        ));
        
        $sBaseStr = _p('number_per_page');
        $aShows = array(
            array("value" => 10, "label" => str_replace('{number}', 10, $sBaseStr)),
            array("value" => 15, "label" => str_replace('{number}', 15, $sBaseStr)),
            array("value" => 18, "label" => str_replace('{number}', 18, $sBaseStr)),
            array("value" => 21, "label" => str_replace('{number}', 21, $sBaseStr))
        );

        $this->template()->assign(array(
                'sSearch' => isset($sSearch['search']) ? urldecode($sSearch['search']) : urldecode($sSearch),
                'aShows' => $aShows,
                'sSort' => $sSort,
                'sWhen' => $sWhen,
                'sShow' => $sShow,
                'sLocation' => urldecode($sLocation),
                'sAddress' => urldecode($sAddress),
                'sCity' => urldecode($sCity),
                'iZipcode' => $sZipcode,
                'rangevalueto' => $rangevalueto,
                'rangevaluefrom' => $rangevaluefrom,
                'rangetype' => $rangetype,
                'aForms' => $aForms,
                'sCustomClassName' => 'ync-block'
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

?>