<?php
/**
 * @copyright   [YouNet_COPYRIGHT]
 * @author      YouNet Company
 * @package     Module_GettingStarted
 * @version     3.02p5
 */
defined('PHPFOX') or exit('NO DICE!');


class Gettingstarted_component_controller_admincp_addarticlecategory extends Phpfox_Component {

    public function process() {      
        
        $aLanguages = PhpFox::getService('language')->getAll();
        $bIsEdit = false;
        $aCategory_id = $this->request()->getInt('id');
        
        #Check is edit
        if (isset($aCategory_id) && $aCategory_id) {
            $bIsEdit = true;
            
            $aForms = phpfox::getService('gettingstarted.articlecategory')->getArticleCategoryforEdit($aCategory_id);
            $this->template()->assign(array(
                'aForms' => $aForms,
            ));
        }
        
        #Validation
        $aValidation = array(
            'article_category_name' => _p('gettingstarted.please_input_article_category_name'),
        );
        $oValid = Phpfox::getLib('validator')->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));
        
        #Get input value, do update or add
        if ($aVals = $this->request()->get('val')) {
            if($oValid->isValid($aVals)) {
                if($bIsEdit) {
                    $iEdit = Phpfox::getService('gettingstarted.articlecategory')->updateArticleCategory($aVals, $aCategory_id);
                    if($iEdit) {
                        $this->url()->send('current', null, _p('gettingstarted.knowledge_base_category_successfully_edited'));
                    } else {
                        $this->url()->send('current', null, _p('gettingstarted.update_article_category_fail'));
                    }
                } else {
                    $iAdd = Phpfox::getService('gettingstarted.articlecategory')->addArticleCategory($aVals);
                    if($iAdd) {
                        $this->url()->send('current', null, _p('gettingstarted.knowledge_base_category_successfully_added'));
                    } else {
                        $this->url()->send('current', null, _p('gettingstarted.article_category_name_is_already_exists'));
                    }
                }
            }
        }
        
        $oMulticat = Phpfox::getService('gettingstarted.multicat');
        $css = array('id'=>'', 'name'=>'val[parent_id]', 'class'=>'form-control');
        $aCats = array();
        if($bIsEdit)
        {
             $this->template()->setBreadCrumb(_p('gettingstarted.edit_articles_categories'), $this->url()->makeUrl('admincp.gettingstarted.addarticlecategory/id_'.$aCategory_id));
             $aCats = $oMulticat->getSelectBox($css, $aForms['parent_id'], $aCategory_id, $aForms['language_id']);
        }
        else
        {
             $this->template()->setBreadCrumb(_p('gettingstarted.add_articles_categories'), $this->url()->makeUrl('admincp.gettingstarted.addarticlecategory'));
             $aCats = $oMulticat->getSelectBox($css, null, null, $aLanguages[0]['language_id']);
        }

        $this->template()->assign(array(
            'aLanguages' => $aLanguages,
            'sCreateJs' => $oValid->createJS(),
            'sGetJsForm' => $oValid->getJsForm(),
            'bIsEdit' => $bIsEdit,
            'aCats' => $aCats,
        ));
    }
}

?>
