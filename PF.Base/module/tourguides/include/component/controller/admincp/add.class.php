<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class TourGuides_Component_Controller_Admincp_Add extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
        phpfox::getLib('session')->remove('yntour_current_selected');   
        $oService = phpfox::getService('tourguides');
        if (($iId = $this->request()->getInt('delete')))
        {   
            
             if (phpfox::getService('tourguides.steps')->removeStep($iId))
            {
                $aTour['id'] = $this->request()->get('tour');
                $this->url()->send('admincp.tourguides.add', array('id' => $aTour['id']), _p('tourguides.tour_guide_step_successfully_deleted'));
            }                
        }
        $aRows = Phpfox::getService('admincp.block')->get();
        
        foreach ($aRows as $iKey => $aRow)
        {
            if (!Phpfox::isModule($aRow['module_id']))
            {
                continue;
            }
            $aBlocks[$aRow['m_connection']][$aRow['location']][] = $aRow;
        }
        
        ksort($aBlocks);
		$aValidation = array(
            'name' => _p('tourguides.provide_tour_guide_name'),
            'url' =>  _p('tourguides.provide_tour_guide_url'),
        );        
        
        $oValid = Phpfox::getLib('validator')->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));    
        $bIsEdit = false;
        if (($iId = $this->request()->getInt('id')))
        {
            if (($aTour = $oService->getTourById($iId)))
            {
                $bIsEdit = true;
                $this->template()->assign('aForms', $aTour);
                $aSteps = phpfox::getService('tourguides.steps')->getSteps($iId);			
                $this->template()->assign('aSteps', $aSteps);
            }
        }
        if ($aVals = $this->request()->getArray('val'))
        {			
            $aVals['user_id'] = isset($aVals['user_id'])?$aVals['user_id']:phpfox::getUserId();
            if ($oValid->isValid($aVals))
            {
                if ($bIsEdit)
                {
                    
                    if ($oService->updateTour($aTour['id'], $aVals))
                    {
                        $this->url()->send('admincp.tourguides.add', array('id' => $aTour['id']), _p('tourguides.tour_guide_successfully_updated'));
                    }                
                }
                else 
                {
                   if ($iId = $oService->addTour($aVals))
                    {
                        $this->url()->send('admincp.tourguides.add', array('id' => $iId), _p('tourguides.new_tour_guide_successfully_added'));
                    }
                }
                
            }
        }
        $sCorePath = Phpfox::getParam('core.path') ;
        $sCorePath = str_replace("index.php".PHPFOX_DS,"",$sCorePath);
        $sCorePath .= 'PF.Base'.PHPFOX_DS;
        
		 $this->template()->setTitle(_p('tourguides.add_new_tour_guide'))
             ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
             ->setBreadCrumb(_p("module_tourguides"), $this->url()->makeUrl('admincp.app', ['id' => '__module_tourguides']))
            ->setBreadcrumb(_p('tourguides.add_new_tour_guide'), $this->url()->makeUrl('admincp.tourguides.add'))
            ->setHeader('cache', array(
                    'drag.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'tourguides.ordering\'}); }</script>'
                )
            )
            ->assign(array(
                    'sCoreUrl' =>$sCorePath,
                    'sCreateJs' => $oValid->createJS(),
                    'sGetJsForm' => $oValid->getJsForm(),
                    'bIsEdit' => $bIsEdit,
                    'aBlocks' => $aBlocks				
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