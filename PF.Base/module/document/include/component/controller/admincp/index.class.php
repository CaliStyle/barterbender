<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */
class Document_Component_Controller_Admincp_Index extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bSubCategory = false;
        $aParent = [];
        if (($iId = $this->request()->getInt('sub')))
        {
            $bSubCategory = true;
            $aParent = Phpfox::getService('document.category')->getCategory($iId);
        }

        if ($iDelete = $this->request()->getInt('delete'))
        {
            if (Phpfox::getService('document.category')->getAllItemBelongToCategory($iDelete) > 0) {
                return Phpfox_Error::display(_p('you_can_not_delete_this_category_because_there_are_many_items_related_to_it'));
            }

            if (Phpfox::getService('document.category.process')->delete($iDelete))
            {
                $this->url()->send('admincp.document', null, _p('category_successfully_deleted'));
            }
        }

        $aCategories = ($bSubCategory ? Phpfox::getService('document.category')->getForAdmin($iId) : Phpfox::getService('document.category')->getForAdmin());

        $this->template()->setTitle(($bSubCategory ?  _p('manage_sub_categories') : _p('manage_categories')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(($bSubCategory ?  _p('manage_categories'). ((isset($aParent['name'])) ? ': '. _p($aParent['name']) : '') : _p('manage_categories')))
            ->setHeader(array(
                    'drag.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'document.categoryOrdering\'}); }</script>'
                )
            )
            ->assign(array(
                    'bSubCategory' => $bSubCategory,
                    'aCategories' => $aCategories
                )
            );
    }
    
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('document.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}

?>