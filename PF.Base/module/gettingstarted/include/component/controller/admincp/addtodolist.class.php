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

class gettingstarted_component_controller_admincp_addtodolist extends Phpfox_Component {

    public function process() {
        //get all languages to list in select box
        $aLanguages = PhpFox::getService('language')->getAll();
        $sLanguage_id = $aLanguages[0]['language_id'];
        $aVals = array();
        $isEdit = false;
        $aVals['title'] = "";
        $aVals['description'] = "";
        $bool_test = 0;
 
        if($iTodolist_id = $this->request()->getInt('req4'))
        {
            $isEdit = true;
            
            $aTodolist_for_edit = phpfox::getService('gettingstarted.todolist')->getTodolistForEdit($iTodolist_id);
            
            $sLanguage_id = $aTodolist_for_edit['language_id'];
            
            $this->template()->assign(array(
                'aForms' => array(
                    'title' => $aTodolist_for_edit['title'],
                    'description' => $aTodolist_for_edit['description'],
                    'language_id' => $aTodolist_for_edit['language_id']
                )
            ));
            
            if (isset($_POST['submit_addtodolist']) == true) {
                $aVals = $this->request()->get('val');
                
                //Validate input values
                if ($aVals['title'] == "") {
                    Phpfox_Error::set(_p('gettingstarted.fill_in_a_title_for_your_todo_list'));
                    $bool_test = 1;
                }
                if ($aVals['description'] == "") {
                    Phpfox_Error::set(_p('gettingstarted.fill_in_a_description_for_your_todo_list'));
                    $bool_test = 1;
                }

                if ($bool_test == 0) {
                    if(Phpfox::getService('gettingstarted.todolist')->update($aVals, $iTodolist_id, $sLanguage_id)) {
                        $this->url()->send('admincp.gettingstarted.addtodolist', $iTodolist_id, _p('gettingstarted.todo_list_successfully_edited'));
                    }
                }
            }
        } 
        else
        { 
            if (isset($_POST['submit_addtodolist']) == true) {
                $aVals = $this->request()->get('val');

                if ($aVals['title'] == "") {
                    Phpfox_Error::set(_p('gettingstarted.fill_in_a_title_for_your_todo_list'));
                    $bool_test = 1;
                }
                if ($aVals['description'] == "") {
                    Phpfox_Error::set(_p('gettingstarted.fill_in_a_description_for_your_todo_list'));
                    $bool_test = 1;
                }

                if ($bool_test == 0) {
                    if(phpfox::getService("gettingstarted.todolist")->add($aVals)) {
                        $this->url()->send('admincp.gettingstarted.addtodolist', null, _p('gettingstarted.todo_list_successfully_added'));
                    }
                }
            }
        }

        $this->template()->assign(array(
            'aScheduledMail' => $aVals,
            'aLanguages' => $aLanguages,
            'sLanguage_id' => $sLanguage_id,
            'iTodolist_id' => $iTodolist_id,
            'isEdit' => $isEdit
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
        ));
        
        if($isEdit)
        {
           $this->template()->setBreadCrumb(_p('gettingstarted.edit_todo_list'), $this->url()->makeUrl('admincp.gettingstarted.addtodolist/'.$iTodolist_id."/".$sLanguage_id)); 
        }
        else
        {
           $this->template()->setBreadCrumb(_p('gettingstarted.add_todo_list'), $this->url()->makeUrl('admincp.gettingstarted.addtodolist'));
        }
        
    }

}

?>
