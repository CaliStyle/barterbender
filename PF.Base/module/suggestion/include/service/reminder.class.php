<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Suggestion_Service_Reminder extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
		$this->_sTable = Phpfox::getT('suggestion_reminder');                
    }
    

    public function addReminder($aData){ 

        $checkExist = $this->database()->select('*')
        	 ->from($this->_sTable)
             ->where(" owner_id = ".$aData['owner_id']." AND module_id = '".$aData['module_id']."' AND item_id = '".$aData['item_id']."'")
             ->execute('getSlaveRows');
			 
        if(count($checkExist) == 0){
            $this->database()->insert($this->_sTable, array(
                    'item_id' => $aData['item_id'],
                    'owner_id' =>$aData['owner_id'],
                    'module_id' =>$aData['module_id']
                )
            );
        }
    }

    public function getReminder($owner_id,$module_id,$offset,$limit){

        $aReminders = $this->database()->select('*')->from($this->_sTable)
            ->where(" owner_id = ".$owner_id." AND module_id = '".$module_id."'")
            ->limit($offset,$limit)
            ->execute('getSlaveRows');
		//    ->execute('');
		//var_dump($aReminders);
			//die();
        if(count($aReminders) > 0){
            foreach ($aReminders as &$aReminder) {
                $this->getItemDetailMore($aReminder);
            }
			
            return $aReminders;
        }
        return array();
    }	  

    public function getAllReminder($owner_id,$limit){

        $aReminders = $this->database()->select('*')->from($this->_sTable)
            ->where(" owner_id = ".$owner_id )
            ->limit(0,$limit)
            ->execute('getSlaveRows');
        if(count($aReminders) > 0)
        {
            foreach ($aReminders as &$aReminder) 
            {					
             	$this->getItemDetailMore($aReminder);
			}
			return $aReminders;
    	}
		return array();
    }   

    public function getItemDetailMore(&$aData)
    {
        switch ($aData['module_id']) {
            case 'coupon':
                    $aData['url'] = Phpfox::getLib('url')->makeUrl('coupon.detail',$aData['item_id']);

                    $aData['create'] ='';

                    
                    $object = Phpfox::getService('suggestion')->getObject($aData['item_id'], 'coupon');

                    $aData['info'] = Phpfox::getService('suggestion.url')->makeLink($aData['url'], Phpfox::getLib('phpfox.parse.output')->shorten($object['title'], 70, '...'));   
                    

                    if(isset($object['image_path']) && $object['image_path'] != '' ){
                        $aImgs = Phpfox::getService('suggestion')->parserData($object['server_id'], $object['image_path'],'core.url_pic');
                        
                        $img = Phpfox::getService('suggestion')->getObjectAvatar($aImgs);
                        $aData['avatar'] = '<a href="'.$aData['url'].'">'.$img.'</a>';
                    }   
                    else{
                        $aData['avatar'] = '<a href="'.$aData['url'].'"><img src="'.Phpfox::getParam('core.path').'/theme/frontend/default/style/default/image/noimage/item.png" alt="" height="75" width="75" ></a>';
                    }   

                    $aData['suggestion_id'] = '';
                    $aData['suggest'] = '';
                    $aData['title'] = base64_encode( urlencode($object['title']));
                    $aData['friend_user_id'] = Phpfox::getUserId();
                    $aData['friend_friend_user_id'] = $aData['item_id'];
                    $aData['message'] = '';
                    $aData['reminder'] = _p('suggestion.send_suggestion');
                    $aData['delete_reminder'] = _p('suggestion.delete');
                    $aData['total'] = Phpfox::getService('suggestion.reminder')->countReminder(Phpfox::getUserId(),'coupon');
                    $aData['header_title'] = ucfirst($aData['module_id']);
                break;

            case 'jobposting':
                    
                    $aData['url'] = Phpfox::getLib('url')->makeUrl('jobposting',$aData['item_id']);

                    $aData['create'] ='';

                    
                    $object = Phpfox::getService('suggestion')->getJobReminder($aData['item_id']);
                    $aData['info'] = Phpfox::getService('suggestion.url')->makeLink($aData['url'], Phpfox::getLib('phpfox.parse.output')->shorten($object['title'], 70, '...'));   
                    

                    if(isset($object['image_path']) && $object['image_path'] != '' ){
                        $aImgs = Phpfox::getService('suggestion')->parserData($object['server_id'], 'jobposting/'.$object['image_path'],'core.url_pic');
                        $img = Phpfox::getService('suggestion')->getObjectAvatar($aImgs);
                        $aData['avatar'] = '<a href="'.$aData['url'].'">'.$img.'</a>';
                    }   
                    else{
                        $aData['avatar'] = '<a href="'.$aData['url'].'"><img src="'.Phpfox::getParam('core.path').'/theme/frontend/default/style/default/image/noimage/item.png" alt="" height="75" width="75" ></a>';
                    }   

                    $aData['suggestion_id'] = '';
                    $aData['suggest'] = '';
                    $aData['friend_user_id'] = Phpfox::getUserId();
                    $aData['friend_friend_user_id'] = $aData['item_id'];
                    $aData['message'] = '';
                    $aData['title'] = base64_encode( urlencode($object['title']));
                    $aData['reminder'] = _p('suggestion.send_suggestion');
                    $aData['delete_reminder'] = _p('suggestion.delete');
                    $aData['total'] = Phpfox::getService('suggestion.reminder')->countReminder(Phpfox::getUserId(),'jobposting');
                    $aData['header_title'] = ucfirst($aData['module_id']);

                break;
            
            case 'contest':
                    $aData['url'] = Phpfox::getLib('url')->makeUrl('contest',$aData['item_id']);

                    $aData['create'] ='';

                    
                    $object = Phpfox::getService('suggestion')->getContestReminder($aData['item_id']);
                    
/*                    echo '<pre>';
                    print_r($object);
                    die;*/
                    $aData['info'] = Phpfox::getService('suggestion.url')->makeLink($aData['url'], Phpfox::getLib('phpfox.parse.output')->shorten($object['contest_name'], 70, '...'));   
                    

                    if(isset($object['image_path']) && $object['image_path'] != '' ){
                        $aImgs = Phpfox::getService('suggestion')->parserData($object['server_id'], 'contest/'.$object['image_path'],'core.url_pic');
                        $img = Phpfox::getService('suggestion')->getObjectAvatar($aImgs);
                        $aData['avatar'] = '<a href="'.$aData['url'].'">'.$img.'</a>';
                    }   
                    else{
                        $aData['avatar'] = '<a href="'.$aData['url'].'"><img src="'.Phpfox::getParam('core.path').'/theme/frontend/default/style/default/image/noimage/item.png" alt="" height="75" width="75" ></a>';
                    }   

                    $aData['suggestion_id'] = '';
                    $aData['suggest'] = '';
                    $aData['friend_user_id'] = Phpfox::getUserId();
                    $aData['friend_friend_user_id'] = $aData['item_id'];
                    $aData['message'] = '';
                    $aData['title'] = base64_encode( urlencode($object['contest_name']));
                    $aData['reminder'] = _p('suggestion.send_suggestion');
                    $aData['delete_reminder'] = _p('suggestion.delete');
                    $aData['total'] = Phpfox::getService('suggestion.reminder')->countReminder(Phpfox::getUserId(),'contest');
                    $aData['header_title'] = ucfirst($aData['module_id']);
                break;
            default:
                # code...
                break;
        }
    }

    public function deleteReminder($iReminderId) {
        $this->database()->delete($this->_sTable, 'reminder_id = ' . (int) $iReminderId );
    }

    public function countReminder($owner_id,$module_id){
        $iTotal = $this->database()->select('COUNT(*) as total')->from($this->_sTable)
            ->where("owner_id = ".$owner_id." AND module_id = '".$module_id."'")->execute('getSlaveRow');
        
        return $iTotal['total'];
    }

    public function countAllReminder($owner_id){
        
        $iTotal = $this->database()->select('COUNT(*) as total')->from($this->_sTable)
            ->where("owner_id = ".$owner_id." AND module_id IN ('coupon','jobposting','contest')")->execute('getSlaveRow');
        
        return $iTotal['total'];
    
    }
}
?>