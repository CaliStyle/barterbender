<?php
/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          2.01
 */

defined('PHPFOX') or exit('NO DICE!');

class Gettingstarted_component_controller_admincp_editarticle extends Phpfox_Component{

	public function process()
	{
            $id=$this->request()->get("id");
            $oFilter = Phpfox::getLib('parse.input');
            $bool_test=0;
            $this->template()->setBreadCrumb(_p('gettingstarted.edit_knowledge_base_article'), $this->url()->makeUrl('admincp.gettingstarted.editarticle.id_'.$id));
            if(isset($_POST['submit_editarticle'])==true)
            {

                $aVals = $this->request()->get('val');
                if($aVals['title']=="")
                {
                    Phpfox_Error::set(_p('gettingstarted.fill_in_a_title_for_your_knowledgebase_article'));
                    $bool_test=1;
                }
                if($aVals['description']=="")
                {
                    Phpfox_Error::set(_p('gettingstarted.fill_in_a_description_for_your_knowledgebase_article'));
                    $bool_test=1;
                }

                if($bool_test==0)
                {
                    $aVals['title'] = $oFilter->clean($aVals['title']);
                    $aVals['description_parsed'] = $oFilter->prepare($aVals['description']);
                    $aVals['description'] = $oFilter->clean($aVals['description']);
                    Phpfox::getService("gettingstarted.articlecategory")->updateArticle($aVals);
                    $val_temp = ($this->request()->get('val'));
                    $page = $val_temp['iPage'];
                    $this->url()->send('admincp.gettingstarted.managearticle/page_'.$page, null, 'Knowledge Base Article successfully edited.');
                }
                
            }

            $scheduled_mail = phpfox::getService('gettingstarted.articlecategory')->getArticleById($id);
            $oFilter = Phpfox::getLib('parse.input');      
            $scheduled_category=phpfox::getService('gettingstarted.articlecategory')->getArticleCategory();
			if(isset($scheduled_category[count($scheduled_category) -1]) && ($scheduled_category[count($scheduled_category) -1]['article_category_id'] == -1))
			{
				unset($scheduled_category[count($scheduled_category) -1]);
			}
            $boolean_id=0;
            if(count($scheduled_mail)==0)
            {
                Phpfox_Error::set(_p('gettingstarted.id_not_found'));
                $boolean_id = 1;
            }
            else
            {
                $this->template()->assign(array('aForms' => array('description' => $scheduled_mail['description'])));
            }
            
            $iPage = $this->request()->get('page');
            $this->template()->assign(array(
               'scheduled_category' => $scheduled_category,
               'boolean_id' => $boolean_id,
               'scheduled_mail' => $scheduled_mail,
               'iPage' => $iPage
            ));    

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
				'type' => 'blog',
				'id' => 'core_js_blog_form'
				)
				);
        }
}
?>
