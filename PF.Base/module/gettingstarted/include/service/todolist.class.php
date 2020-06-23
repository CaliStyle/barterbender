<?php

/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_Gettingstarted
 * @version          2.01
 */
defined('PHPFOX') or exit('NO DICE!');

class Gettingstarted_service_todolist extends Phpfox_Service {

    /************************************************************************************/
    /* ================================ version 3.02p5 ================================ */
    /************************************************************************************/
    
    /**
	 * Class constructor
	 */
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('gettingstarted_todolist');
	}
	
    public function getTodoItemsForManage($sLanguage_id) {
        $aRows = $this->database()->select('todo.*')
                ->from($this->_sTable, 'todo')
                ->where("language_id='".$sLanguage_id."'")
				->order('ordering ASC')
                ->execute('getSlaveRows');
        return $aRows;
    }

	public function setOrder($aVals) {		
		$iCnt = 0;
		foreach ($aVals as $mKey => $mOrdering) {
			$iCnt++;
			$this->database()->update($this->_sTable, array('ordering' => $iCnt), 'todolist_id='.$this->database()->escape($mKey));
		}
	}
    
    public function delete($iId) {
        $this->database()->delete($this->_sTable, 'todolist_id='.(int)$iId);
        return true;
    }
    
    public function deleteAllByLanguage($language_id) {
        $this->database()->delete($this->_sTable, "language_id='".$language_id."'");
        return true;
    }

    public function add($aVals) {
        $aInsert = array();
        $oFilter = phpfox::getLib('parse.input');

        $aInsert['title'] = $oFilter->clean($aVals['title']);
        $aInsert['description'] = $oFilter->clean($aVals['description']);
        $aInsert['description_parsed'] = $oFilter->prepare($aVals['description']);
        $aInsert['time_stamp'] = PHPFOX_TIME;
        $aInsert['language_id'] = $oFilter->clean($aVals['language_id']);
        $aInsert['ordering'] = $this->getMaxOrder($oFilter->clean($aVals['language_id'])) + 1;
        
        return $this->database()->insert($this->_sTable, $aInsert);
    }
    
    protected function getMaxOrder($sLanguage_id) {
        $max = $this->database()->select('MAX(ordering)')
            ->from($this->_sTable)
            ->where("language_id='".$sLanguage_id."'")
            ->execute('getField');
        return $max;
    }

    public function update($aVals, $iId, $sLanguage_id) {
        $aUpdates = array();
        $oFilter = Phpfox::getLib('parse.input');
        
        $aUpdates['title'] =$oFilter->clean($aVals['title']);
        $aUpdates['description'] =$oFilter->clean($aVals['description']);
        $aUpdates['description_parsed'] =$oFilter->prepare($aVals['description']);
        $aUpdates['time_stamp'] = PHPFOX_TIME;
        $aUpdates['language_id'] = $aVals['language_id'];
        
        if($aVals['language_id']!=$sLanguage_id) {
            $aUpdates['ordering'] = $this->getMaxOrder($oFilter->clean($aVals['language_id'])) + 1;
        }
        
        return $this->database()->update($this->_sTable, $aUpdates, 'todolist_id='.(int)$iId);
    }
    
    #get Next step of todo list
    public function getFirstLinetodolist($order) {
        $aLanguage = Phpfox::getLib('locale')->getLang();
        $sLanguage_id = $aLanguage['language_id'];
        
        $aRow = $this->database()->select('*')
            ->from($this->_sTable, 'todolist')
            ->where('todolist.ordering > ' . $order . " AND todolist.language_id='" . $sLanguage_id . "'")
            ->order('todolist.ordering ASC')
            ->limit(1)
            ->execute('getSlaveRow');
        return $aRow;
    }

    #get Previous step of todo list
    public function getPreTodolist($order) {
        $aLanguage = Phpfox::getLib('locale')->getLang();
        $sLanguage_id = $aLanguage['language_id'];
        
        $aRow = $this->database()->select('*')
            ->from($this->_sTable, 'todolist')
            ->where('todolist.ordering < ' . $order . " AND todolist.language_id='" . $sLanguage_id . "'")
            ->order('todolist.ordering DESC')
            ->limit(1)
            ->execute('getSlaveRow');
        return $aRow;
    }
    
    #get current position of user
    public function getCurrentPositionOfUser($user_id) {
        $aLanguage = Phpfox::getLib('locale')->getLang();
        $sLanguage_id = $aLanguage['language_id'];
        
        $aRow = $this->database()->select('t.*, p.item_id, p.active')
            ->from($this->_sTable, 't')
            ->join(Phpfox::getT('gettingstarted_position'), 'p', 't.ordering = p.item_id')
            ->where('p.user_id = ' . $user_id . " AND language_id='" . $sLanguage_id . "'")
            ->limit(1)
            ->execute('getSlaveRow');
        return $aRow;
    }

    #update position of todolist by user
    public function updateTodoListofUser($order) {
        $aNextPosition = $this->getFirstLinetodolist($order);
        if (!empty($aNextPosition)) {
            $positionUpdate['item_id'] = $aNextPosition['ordering'];
        } else {
            $aPrePosition = $this->getPreTodolist($order);
            if (!empty($aPrePosition)) {
                $positionUpdate['item_id'] = $aPrePosition['ordering'];
            } else {
                $this->database()->delete(Phpfox::getT('gettingstarted_position'), 'item_id = ' . $order);
            }
        }
        if (isset($positionUpdate['item_id'])) {
            $this->database()->update(Phpfox::getT('gettingstarted_position'), $positionUpdate, 'item_id = ' . $order);
        }
    }

    
    /************************************************************************************/
    /* =========================== versions earlier 3.02p5 ============================ */
    /************************************************************************************/
    
    public function getTodolist($iPage, $iLimit, $iCnt) {
        $aRows = $this->database()->select('todo.*,l.title as language_title')
                ->from(phpfox::getT('gettingstarted_todolist'), 'todo')
                ->join(PHPFOX::getT('language'), 'l', 'todo.language_id=l.language_id')
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getSlaveRows');
        return $aRows;
    }

    public function getTodolistForManage($sLanguage_id, $iPage, $iLimit, $iCnt) {
        $aRows = $this->database()->select('todo.*,l.title as language_title')
                ->from(phpfox::getT('gettingstarted_todolist'), 'todo')
                ->join(PHPFOX::getT('language'), 'l', 'todo.language_id=l.language_id')
                ->where("todo.language_id='" . $sLanguage_id . "'")
				->order("todo.todolist_id DESC, todo.time_stamp")
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getSlaveRows');
        return $aRows;
    }

    public function getCountTodolistForManage($sLanguage_id) {

        $iCount = (int) $this->database()->select('count(*)')
                        ->from(phpfox::getT('gettingstarted_todolist'))
                        ->where("language_id='" . $sLanguage_id . "'")
                        ->execute('getSlaveField');
        return $iCount;
    }

    public function getCountTodolist() {
        $iCount = (int) $this->database()->select('count(*)')
                        ->from(phpfox::getT('gettingstarted_todolist'))
                        ->execute('getSlaveField');
        return $iCount;
    }

    public function getTodolistById($iId) {
        $aRow = $this->database()->select('*')
                ->from(phpfox::getT('gettingstarted_todolist'))
                ->where('todolist_id=' . $iId)
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function addpositiontodolist($aVals) {
        $aInsert = array();
        $aInsert['user_id'] = Phpfox::getUserId();
        $aInsert['time_stamp'] = PHPFOX_TIME;
        $aInsert['item_id'] = $aVals['item_id'];
        $aInsert['active'] = '1';
        $this->database()->insert(phpfox::getT('gettingstarted_position'), $aInsert);
    }

    /*
     *  Get step of todolist of one user having user_id
     */

    public function getPosiontodolist($user_id) {

        $aRow = $this->database()->select('*')
                ->from(phpfox::getT('gettingstarted_position'))
                ->where('user_id=' . $user_id)
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function deletepostiontodolist($iId) {
        $this->database()->delete(phpfox::getT('gettingstarted_position'), 'position_id=' . $iId);
    }

    public function updatepositiontodolist($aVals) {
        $aUpdate = array();
        $aUpdate['item_id'] = $aVals['item_id'];
        $aUpdate['time_stamp'] = PHPFOX_TIME;
        $this->database()->update(phpfox::getT('gettingstarted_position'), $aUpdate, 'user_id=' . $aVals['user_id']);
    }

    public function updatepositionactive($acvite, $user_id) {
        $aUpdate = array();
        $aUpdate['active'] = $acvite;
        $aUpdate['time_stamp'] = PHPFOX_TIME;
        $this->database()->update(phpfox::getT('gettingstarted_position'), $aUpdate, 'user_id=' . $user_id);
    }

    public function showTodoList() {
        $todolist = "<script type='text/javascript'>
        function viewGettingStart()
        {
            $.ajaxCall('gettingstarted.getToDoListAlert');
            //tb_show('To do list',$.ajaxBox('gettingstarted.viewTodoList'),'height=360&width=550');
        }
        var first_login_gettingstart = false;
        \$Behavior.viewGettingStart = function() {
		        \$(document).ready(function(\$) {
		        if(first_login_gettingstart == false){
		            viewGettingStart();
		            first_login_gettingstart = true;
		            }
		        });
        }
        
        </script>";
        echo $todolist;
    }

    //Todolist  with multiple languages --------------------------
    //Get maximum id to know the id of the next todolist, support for insert new todolist
    public function getMaxTodolistId() {
        $aRow = $this->database()->select('MAX(todolist_id) as todolist_id')
                ->from(PHPFOX::getT('gettingstarted_todolist'))
                ->execute('getSlaveRow');
        if (count($aRow) > 0) {
            $iMax_id = $aRow['todolist_id'];
        } else {
            $iMax_id = 0;
        }
        return $iMax_id;
    }

    //Get information of todolist for edit
    public function getTodolistForEdit($iTodolist_id) {
        $aRow = $this->database()->select('*')
                ->from(PHPFOX::getT('gettingstarted_todolist'))
                ->where('todolist_id=' . $iTodolist_id)
                ->execute('getSlaveRow');
        return $aRow;
    }

    //Check todolist has been existed or not
    public function isExistedTodolist($iTodolist_id, $sLanguage_id) {
        $aRow = $this->database()->select('todolist_id')
                ->from(PHPFOX::getT('gettingstarted_todolist'))
                ->where('todolist_id=' . $iTodolist_id . " AND language_id='" . $sLanguage_id . "'")
                ->execute('getSlaveRow');
        if (count($aRow) > 0) {
            return true;
        } else {
            return false;
        }
    }
}

?>
