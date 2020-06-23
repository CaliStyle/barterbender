<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class TourGuides_Component_Controller_Admincp_Manage extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
        phpfox::getLib('session')->remove('yntour_current_selected');  
        if (($iId = $this->request()->getInt('delete')))
        {   
             if (phpfox::getService('tourguides')->removeTour($iId))
            {
                $this->url()->send('admincp.tourguides',null, _p('tourguides.tour_guide_successfully_deleted'));
            }                
        }
        if($Ids = $this->request()->get('idtour'))
        {   
            foreach($Ids as $Idi) 
            {
                if (phpfox::getService('tourguides')->removeTour($Idi))
                {
                    phpfox::getService('tourguides.steps')->removeSteps($Idi);
                }    
            }
            $this->url()->send('admincp.tourguides',null, _p('tourguides.tour_guide_successfully_deleted'));
        }
        if (($iId = $this->request()->getInt('reset')))
        {   
            if (phpfox::getService('tourguides')->resetTour($iId))
            {
                $this->url()->send('admincp.tourguides',null, _p('tourguides.tour_guide_successfully_updated'));
            }                
        }
        if (($iId = $this->request()->getInt('uncomplete')))
        {   
            if (phpfox::getService('tourguides')->updateTour($iId,array('is_complete'=>1)))
            {
                $this->url()->send('admincp.tourguides',null, _p('tourguides.tour_guide_successfully_updated'));
            }                
        }
        if (($iId = $this->request()->getInt('complete')))
        {   
            if (phpfox::getService('tourguides')->updateTour($iId,array('is_complete'=>0)))
            {
                $this->url()->send('admincp.tourguides',null, _p('tourguides.tour_guide_successfully_updated'));
            }                
        }
        
		$oService = phpfox::getService('tourguides');
        $aFilters = array(
            'search' => array(
                'type' => 'input:text',
                'search' => "AND t.name LIKE '%[VALUE]%'"
            ),    
            
        );        
        
        $oSearch = Phpfox::getLib('search')->set(array(
                'type' => 'tourguides',
                'filters' => $aFilters,
                'search' => 'search'
            )
        );
        $iLimit = 15;
        $iPage = $this->request()->getInt('page');  
        $aConditions = $oSearch->getConditions();

        list($iCnt, $aTours) = $oService->get($aConditions, "", $oSearch->getPage(), $iLimit);
        
        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch->getSearchTotal($iCnt)));
        $sCorePath = Phpfox::getParam('core.path') ;
        $sCorePath = str_replace("index.php".PHPFOX_DS,"",$sCorePath);
        $sCorePath .= 'PF.Base'.PHPFOX_DS;

        $this->template()->setTitle(_p('tourguides.mange_tour_guides'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_tourguides"), $this->url()->makeUrl('admincp.app', ['id' => '__module_tourguides']))
            ->setBreadcrumb(_p('tourguides.mange_tour_guides'), $this->url()->makeUrl('admincp.tourguides.manage'))
            ->assign(array(
                    'sCoreUrl' =>$sCorePath,
                    'aTours' =>$aTours,
                    'bSearch' =>$oSearch->isSearch(),
                    'sUrlAdded' =>$this->url()->makeUrl('admincp.tourguides.add'),
                )
            );            
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('tourguides.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}

?>
