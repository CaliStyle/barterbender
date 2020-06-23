<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class TourGuides_Component_Controller_Admincp_Position extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aVal = $this->request()->get('val');
        if(isset($aVal['position_right'], $aVal['position_top'])) {
            storage()->del('yntour_position');
            storage()->set('yntour_position', [
                'right' => $aVal['position_right'],
                'top' => $aVal['position_top']
            ]);
        }
        $aYnTourPosition = storage()->get('yntour_position');
        $this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_tourguides"), $this->url()->makeUrl('admincp.app', ['id' => '__module_tourguides']))
            ->setBreadCrumb(_p('make_a_tour_position_setting_title'))
            ->setHeader([
            'tourguides.css' => 'module_tourguides',
            'admin.js' => 'module_tourguides'
        ])->assign([
            'fRight' => (isset($aYnTourPosition->value->right) ? $aYnTourPosition->value->right : '0.04'),
            'fTop' => (isset($aYnTourPosition->value->top) ? $aYnTourPosition->value->top : '0.06'),
        ]);
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('tourguides.component_controller_admincp_position_clean')) ? eval($sPlugin) : false);
    }
}

?>
