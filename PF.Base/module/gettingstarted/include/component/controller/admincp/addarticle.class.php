<?php

/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_gettingstarted
 * @version          2.01
 */
defined('PHPFOX') or exit('NO DICE!');

class gettingstarted_component_controller_admincp_addarticle extends Phpfox_Component {

    public function process() {
        $aLanguages = PhpFox::getService('language')->getAll();

        $isEdit = false;
        $aVals = array();
        $aVals['title'] = "";
        $aVals['article_category_id'] = 0;
        $aVals['description'] = "";
        $bool_test = 0;
        $art_id = '';
        $art_for_edit = array('language_id' => '',
            'title' => '',
            'article_category_id' => '',
            'description' => '',
            'is_featured' => '',
        );
        //get language and article id for update
        $art_language = $this->request()->get('req5');
        $art_id = $this->request()->get('req4');

        //do update article
        if (isset($art_id) && $art_id != '' && isset($art_language) && $art_language != '') {
            $isEdit = true;
            $art_for_edit = phpfox::getService('gettingstarted.articlecategory')->getArticleForEdit($art_id, $art_language);
            $this->template()->assign(array('aForms' => array('description' => $art_for_edit['description'])));

            if (isset($_POST['submit_addarticlecategory']) == true) {
                //validate input values
                $aVals = $this->request()->get('val');
                if ($aVals['title'] == "") {
                    Phpfox_Error::set(_p('gettingstarted.fill_in_a_title_for_your_knowledgebase_article'));
                    $bool_test = 1;
                }
				if(!isset($aVals['article_category_id']))
				{
					Phpfox_Error::set(_p('gettingstarted.error_please_provide_a_category_dot'));
                    $bool_test = 1;
				}
                if ($aVals['description'] == "") {
                    Phpfox_Error::set(_p('gettingstarted.fill_in_a_description_for_your_knowledgebase_article'));
                    $bool_test = 1;
                }
                if ($bool_test == 0) {

                    $isExist = PHPFOX::getService('gettingstarted.articlecategory')->isExistArticleByLanguage($art_id, $aVals['language_id']);
                    $aVals['article_id'] = $art_id;
                    //for edit
                    if ($isExist) {
                        
                        PHPFOX::getService('gettingstarted.articlecategory')->updateArticle($aVals);
                        PHPFOX::getService('gettingstarted.articlecategory')->updateIsFeatured($aVals['article_id'],$aVals['is_featured']);
                        $this->url()->send('admincp.gettingstarted.addarticle', array($aVals['article_id'], $aVals['language_id']), _p('gettingstarted.article_successfully_edit'));
                    }
                    //for insert article with others languages
                    else {

						$bIsAddId = true;
                        PHPFOX::getService('gettingstarted.articlecategory')->addarticle($aVals, $bIsAddId);
                        PHPFOX::getService('gettingstarted.articlecategory')->updateIsFeatured($aVals['article_id'],$aVals['is_featured']);
                        $this->url()->send('admincp.gettingstarted.addarticle', array($aVals['article_id'], $aVals['language_id']), _p('gettingstarted.knowledge_base_article_successfully_added'));
                    }
                }
            }
        }
        //Do insert articles
        else {
            if (isset($_POST['submit_addarticlecategory']) == true) {
                $aVals = $this->request()->get('val');

                if ($aVals['title'] == "") {
                    Phpfox_Error::set(_p('gettingstarted.fill_in_a_title_for_your_knowledgebase_article'));
                    $bool_test = 1;
                }
				if(!isset($aVals['article_category_id']))
				{
					Phpfox_Error::set(_p('gettingstarted.error_please_provide_a_category_dot'));
                    $bool_test = 1;
				}
                if ($aVals['description'] == "") {
                    Phpfox_Error::set(_p('gettingstarted.fill_in_a_description_for_your_knowledgebase_article'));
                    $bool_test = 1;
                }
                if ($bool_test == 0) {

                    $iArticle_Max_Id = PHPFOX::getService('gettingstarted.articlecategory')->getMaxArticleId();
                    $aVals['article_id'] = $iArticle_Max_Id + 1;
                    $iId = phpfox::getService("gettingstarted.articlecategory")->addarticle($aVals);
                    PHPFOX::getService('gettingstarted.articlecategory')->updateIsFeatured($aVals['article_id'],$aVals['is_featured']);
					if($iId > 0)
					{
						$this->url()->send('admincp.gettingstarted.addarticle', array($iId, $aVals['language_id']), _p('gettingstarted.knowledge_base_article_successfully_added'));
					}
                }
            }
        }
        
        #Get categories selection
        $oMulticat = Phpfox::getService('gettingstarted.multicat');
        $css = array('id'=>'', 'name'=>'val[article_category_id]', 'class'=>'form-control');
        if($isEdit) {
            $aCats = $oMulticat->getSelectBox($css, $art_for_edit['article_category_id'], null, $art_for_edit['language_id']);
        } else {
            $aCats = $oMulticat->getSelectBox($css, null, null, $aLanguages[0]['language_id']);
        }

        $this->template()->assign(array(
            'aScheduledMail' => $aVals,
            'aLanguages' => $aLanguages,
            'isEdit' => $isEdit,
            'art_for_edit' => $art_for_edit,
            'art_id' => $art_id,
            'aCats' => $aCats,
        ));
        if($isEdit)
        {
            $this->template()->setBreadCrumb(_p('gettingstarted.edit_article'), $this->url()->makeUrl('admincp.gettingstarted.addarticle/'.$art_id."/".$art_language));
        }
        else
        {
            $this->template()->setBreadCrumb(_p('gettingstarted.add_articles'), $this->url()->makeUrl('admincp.gettingstarted.addarticle'));
        }
        $this->template()->setEditor(array('wysiwyg' => true))
                ->setHeader(array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'switch_legend.js' => 'static_script',
                    'switch_menu.js' => 'static_script',
                    'quick_edit.js' => 'static_script',
                    'pager.css' => 'style_css',
                    'admin_editor.css' => 'module_gettingstarted'
                ));

        $this->setParam('attachment_share', array(
            'type' => 'article',
            'id' => 'my_form'));
    }

}

?>
