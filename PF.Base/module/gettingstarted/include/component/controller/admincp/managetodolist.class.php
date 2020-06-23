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

class Gettingstarted_component_controller_admincp_managetodolist extends Phpfox_Component {

    public function process() {
        
        $aLanguages = PHPFOX::getService('language')->getAll();
        $sLanguage_id = $aLanguages[0]['language_id'];
        if($this->request()->get('lang') != '') {
            $sLanguage_id = $this->request()->get('lang');
        }
        
        if($iId = $this->request()->get('delete')) {
            if(Phpfox::getService('gettingstarted.todolist')->delete($iId)) {
                $this->url()->send('admincp.gettingstarted.managetodolist'.'/lang_'.$sLanguage_id, null, _p('gettingstarted.todo_item_has_been_deleted'));
            }
        }

        if($iId = $this->request()->get('delete_all')) {
            if(Phpfox::getService('gettingstarted.todolist')->deleteAllByLanguage($sLanguage_id)) {
                $this->url()->send('admincp.gettingstarted.managetodolist'.'/lang_'.$sLanguage_id, null, _p('gettingstarted.todo_list_has_been_deleted'));
            }
        }

        $aTodoItems = Phpfox::getService('gettingstarted.todolist')->getTodoItemsForManage($sLanguage_id);

        $this->template()
                ->assign(array(
                    'aTodoItems' => $aTodoItems,
                    'corepath' => phpfox::getParam("core.path"),
                    'aLanguages' => $aLanguages,
                    'sLanguage_id' => $sLanguage_id,
                ))
                ->setHeader('cache', array(
                    'quick_edit.js' => 'static_script',
                    'drag.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.initCoreDrag = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'gettingstarted.setOrder\'}); }</script>',
                ));
        
        $this->template()->setBreadCrumb(_p('gettingstarted.manage_todo_lists'), $this->url()->makeUrl('admincp.gettingstarted.managetodolist'));
    }

}
?>

