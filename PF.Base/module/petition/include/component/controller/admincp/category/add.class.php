<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Controller_Admincp_Category_Add extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bIsEdit = false;
        $aLanguages = Language_Service_Language::instance()->getAll();
        if ($iEditId = $this->request()->getInt('id')) {
            if ($aCategory = Phpfox::getService('petition.category')->getForEdit($iEditId)) {
                $bIsEdit = true;
                $this->template()->assign('aForms', $aCategory);
            }
        }
        if ($aVals = $this->request()->getArray('val')) {
            if ($bIsEdit) {
                if (Phpfox::getService('petition.category.process')->update($aCategory['category_id'], $aVals)) {
                    $this->url()->send('admincp.petition.category.add', array('id' => $aCategory['category_id']), Phpfox::getPhrase('petition.category_successfully_updated'));
                }
            }
            else {
                if (Phpfox::getService('petition.category.process')->add($aVals, '0')) {
                    $this->url()->send('admincp.petition.category.add', null, Phpfox::getPhrase('petition.category_successfully_added'));
                }
            }
        }

        $this->template()->setTitle(Phpfox::getPhrase('petition.add_category'))
            ->setBreadCrumb(Phpfox::getPhrase('petition.add_category'), $this->url()->makeUrl('admincp.petition.category.add'))
            ->assign(array(
                    'bIsEdit' => $bIsEdit,
                    'aLanguages' => $aLanguages
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('petition.component_controller_admincp_category_add_clean')) ? eval($sPlugin) : false);
    }
}

?>