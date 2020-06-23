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

class Gettingstarted_component_controller_admincp_edittodolist extends Phpfox_Component{

	public function process()
	{
            $id = $this->request()->get("id");
            $oFilter = Phpfox::getLib('parse.input');
            $bool_test=0;
			$this->template()->setBreadCrumb(_p('gettingstarted.edit_todo_list'), $this->url()->makeUrl('admincp.gettingstarted.edittodolist.id_'.$id));
            if(isset($_POST['submit_edittodolist'])==true)
            {

                $aVals=$this->request()->get('val');
                if($aVals['title']=="")
                {
                    Phpfox_Error::set(_p('gettingstarted.fill_in_a_title_for_your_todo_list'));
                    $bool_test=1;
                }
                if($aVals['description']=="")
                {
                    Phpfox_Error::set(_p('gettingstarted.fill_in_a_description_for_your_todo_list'));
                    $bool_test=1;
                }
                
                if($bool_test==0)
                {
                    
                    $link="admincp/gettingstarted/edittodolist/id_".$aVals["todolist_id"];
                    $aVals['title']=$oFilter->clean($aVals['title']);
                    $aVals['description_parsed']=$oFilter->prepare($aVals['description']);
                    $aVals['description']=$oFilter->clean($aVals['description']);
                    Phpfox::getService("gettingstarted.todolist")->updateTodolist($aVals);
                    $this->url()->send('admincp.gettingstarted.managetodolist', null, _p('gettingstarted.todo_list_successfully_edited'));
                }

            }

            $scheduled_mail=phpfox::getService('gettingstarted.todolist')->getTodolistById($id);
            $oFilter = Phpfox::getLib('parse.input');


            $boolean_id=0;
            if(count($scheduled_mail)==0)
            {
                Phpfox_Error::set(_p('gettingstarted.id_not_found'));
                $boolean_id=1;
            }
            else
            {
                $this->template()->assign(array('aForms' => array('description' => $scheduled_mail['description'])));
            }

            $this->template()->assign(array(

               'boolean_id' => $boolean_id,
               'scheduled_mail' => $scheduled_mail,
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
