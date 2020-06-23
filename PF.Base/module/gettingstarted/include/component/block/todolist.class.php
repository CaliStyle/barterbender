<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class gettingstarted_component_block_todolist extends Phpfox_Component {

    public function process() {

        $position = 0;
        $aRowPosition = Phpfox::getService("gettingstarted.todolist")->getPosiontodolist(Phpfox::getUserId());
        if (count($aRowPosition) > 0) {
            if ($aRowPosition['active'] == 0) {

                print_r(_p('gettingstarted.you_can_not_see_it_again'));
                exit;
            }
            $position = $aRowPosition['item_id'];
        }
        if ($position == 0) {
            $currentTodoList = Phpfox::getService('gettingstarted.todolist')->getFirstLinetodolist($position);
        } else {
            $currentTodoList = Phpfox::getService('gettingstarted.todolist')->getCurrentPositionOfUser(Phpfox::getUserId());
        }

        $showbuttonNext = 0;
        $showbuttonPre = 0;
        $showbuttonDone = 0;
        $showbuttonClose = 1;
        if (count($currentTodoList) > 0) {
            $nextTodoList = Phpfox::getService('gettingstarted.todolist')->getFirstLinetodolist($currentTodoList['ordering']);
            $preTodoList = Phpfox::getService('gettingstarted.todolist')->getPreTodolist($currentTodoList['ordering']);
            if (count($nextTodoList) > 0) {
                $showbuttonNext = 1;
            } else {
                $showbuttonClose = 0;
                $showbuttonDone = 1;
            }
            if (count($preTodoList) > 0) {
                $showbuttonPre = 1;
            }
            $aVals = array();
            $aVals['item_id'] = $currentTodoList['ordering'];
            if (count($aRowPosition) == 0) {
                Phpfox::getService('gettingstarted.todolist')->addpositiontodolist($aVals);
            } else {
                $aVals['user_id'] = phpfox::getUserId();
                Phpfox::getService('gettingstarted.todolist')->updatepositiontodolist($aVals);
            }
        } else {


            $aNextPosition = PHPFOX::getService('gettingstarted.todolist')->getFirstLinetodolist($position);
            if (!empty($aNextPosition)) {
                $positionUpdate['item_id'] = $aNextPosition['ordering'];
                $currentTodoList = $aNextPosition;
            } else {
                $aPrePosition = PHPFOX::getService('gettingstarted.todolist')->getPreTodolist($position);
                if (!empty($aPrePosition)) {
                    $positionUpdate['item_id'] = $aPrePosition['ordering'];
                    $currentTodoList = $aPrePosition;
                } else {
                    PHPFOX::getLib('database')->delete(phpfox::getT('gettingstarted_position'), 'item_id = ' . $position);
                    print_r(_p('gettingstarted.you_can_not_see_it_again'));
                    exit;
                }
            }
            $nextTodoList = Phpfox::getService('gettingstarted.todolist')->getFirstLinetodolist($currentTodoList['ordering']);
            $preTodoList = Phpfox::getService('gettingstarted.todolist')->getPreTodolist($currentTodoList['ordering']);
            if (count($nextTodoList) > 0) {
                $showbuttonNext = 1;
            } else {
                $showbuttonClose = 0;
                $showbuttonDone = 1;
            }
            if (count($preTodoList) > 0) {
                $showbuttonPre = 1;
            }
            if (isset($positionUpdate['item_id'])) {
                PHPFOX::getLib('database')->update(phpfox::getT('gettingstarted_position'), $positionUpdate, 'item_id = ' . $position);
            }
        }
        if (empty($currentTodoList)) {
            print_r(_p('gettingstarted.you_can_not_see_it_again'));
            exit;
        }

        $this->template()->assign(array(
            'FirstTodoList' => $currentTodoList,
            'showbuttonNext' => $showbuttonNext,
            'showbuttonPre' => $showbuttonPre,
            'showbuttonDone' => $showbuttonDone,
            'showbuttonClose' => $showbuttonClose
        ));
        return 'block';
    }

}

?>
