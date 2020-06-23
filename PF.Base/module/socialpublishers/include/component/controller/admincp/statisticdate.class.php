<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class SocialPublishers_Component_Controller_Admincp_Statisticdate extends Phpfox_Component {

    public function delete()
    {
        // Delete case.
        $sDelete = $this->request()->get('delete');

        if ($sDelete != '')
        {
            if (Phpfox::getService('socialpublishers.statisticdate.process')->deleteAll())
            {
                $this->url()->send('admincp.socialpublishers.statisticdate', null, _p('socialpublishers.statistic_by_date_successfully_deleted'));
            }
            else
            {
                $this->url()->send('admincp.socialpublishers.statisticdate', null, _p('socialpublishers.delete_statistic_by_date_fail'));
            }
        }
    }
    
    public function process()
    {
        Phpfox::isUser(true);
        
        $this->delete();
        
        // Get current page.
        $iPage = $this->request()->getInt('page');

        // Get array of limit.
        $aPages = array(10, 20, 50, 100);

        $aDisplays = array();
        foreach ($aPages as $iPageCnt)
        {
            $aDisplays[$iPageCnt] = _p('core.per_page', array('total' => $iPageCnt));
        }

        // Config the sort conditions.
        $aSorts = array(
            'id' => _p('socialpublishers.id'),
            'statistic_date' => _p('socialpublishers.date'),
            'total_facebook_post' => _p('socialpublishers.facebook'),
            'total_twitter_post' => _p('socialpublishers.twitter'),
            'total_linkedin_post' => _p('socialpublishers.linkedin')
        );

        // Compose the filter.
        $aFilters = array(
            'statistic_date' => array(
                'type' => 'input:text',
                'search' => "AND d.statistic_date = '%[VALUE]%'"
            ),
            'display' => array(
                'type' => 'select',
                'options' => $aDisplays,
                'default' => '10'
            ),
            'sort' => array(
                'type' => 'select',
                'options' => $aSorts,
                'default' => 'id',
                'alias' => 'd'
            ),
            'sort_by' => array(
                'type' => 'select',
                'options' => array(
                    'DESC' => _p('core.descending'),
                    'ASC' => _p('core.ascending')
                ),
                'default' => 'DESC'
            )
        );

        $aSearchConfig = array(
            'type' => 'socialpublishers',
            'filters' => $aFilters,
            'search' => 'search'
        );
        
        // Get the limit.
        $iLimit = $this->search()->set($aSearchConfig)->getDisplay();

        list($iCount, $aItems) = Phpfox::getService('socialpublishers.statisticdate')->adminGet($this->search()->getConditions(), $this->search()->getSort(), $this->search()->getPage(), $iLimit);
        
        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $this->search()->getSearchTotal($iCount)));

        $aParams = array(
            'aItems' => $aItems,
            'iCount' => $iCount
        );

        $this->template()
                ->setBreadcrumb(_p('socialpublishers.statistic_by_date'), $this->url()->makeUrl('admincp.socialpublishers.statisticdate'))
                ->assign($aParams)
                ->setPhrase(array('socialpublishers.are_you_sure'));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('socialpublishers.component_controller_admincp_statisticdate_clean')) ? eval($sPlugin) : false);
    }

}

?>