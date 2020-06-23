<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: sample.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */
class Suggestion_Component_Block_View_More extends Phpfox_Component {
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process() {

		$type = $this -> getParam('type');		
		$iPage = (int)$this -> getParam('iPage');
		
		switch ($type) {
			case 'people_you_may_knows' :
				$offset =  $this -> getParam('offset');
				$aRows['people_you_may_knows'] = Phpfox::getService('suggestion') -> getPeopleYouMayKnow(Phpfox::getUserId(), $offset);
				break;
				
			case 'event_you_may_like':
				$sEventJoin  = Phpfox::getService('suggestion')->getListEventJoin(Phpfox::getUserId());
				if(!$sEventJoin){
					//get new event//get new event
					$aParentModule = $this->getParam('aParentModule');
					$bIsPage = $aParentModule['module_id'] == 'pages' ? true : false;
		
					$pageID = $aParentModule['module_id'] == 'pages' ? $aParentModule['item_id'] : -1;
					
					list($iCnt, $aTemp) = Phpfox::getService('fevent')->getOnHomepageByType('ongoing', Phpfox::getParam('suggestion.number_item_on_other_block'), $bIsPage, false, false, $pageID);
				
				}
				else{
					//get suggest event
					$sOwner = Phpfox::getService('suggestion')->getListOwner($sEventJoin);
					$sCategory = Phpfox::getService('suggestion')->getListCategory($sEventJoin);
					
					list($iCnt, $aTemp) = Phpfox::getService('suggestion') -> getEventYouMayJoin(array('iPage' => $iPage, 'sEventJoin' => $sEventJoin, 'sOwner' => $sOwner, 'sCategory' => $sCategory));
					
					if(count($aTemp)>0)
					{
						
						$object = array();
						foreach($aTemp as &$item)
						{
							$sLink = $this->url()->makeUrl('fevent',null,null).$item['event_id'].'/'.$item['title'];
							$item['create'] ='<p>'._p('suggestion.added_by_name_on_time',array('name'=>Phpfox::getService('suggestion')->getUserLink($item['user_id']), 'time'=>Phpfox::getTime(Phpfox::getParam('core.global_update_time'),$item['time_stamp'])) ).'</p>';
							$item['info'] = "<a href='" . $sLink . "'target='_blank'>" .Phpfox::getLib('phpfox.parse.output')->shorten($item['title'], 70, '...') . "</a>"; 	
							$item['url'] = $sLink;
							if(isset($item['image_path']) && $item['image_path'] != '' ){
								$aImgs = Phpfox::getService('suggestion')->parserData($item['server_id'], $item['image_path'],'event.url_image');
								$img = Phpfox::getService('suggestion')->getObjectAvatar($aImgs);
								$item['avatar'] = '<a href="'.$sLink.'">'.$img.'</a>';
							}		
							else{
								$item['avatar'] = '<a href="'.$sLink.'"><img src="'.Phpfox::getParam('core.path').'/module/suggestion/static/image/no_photo.png" alt="" height="75" width="75" ></a>';
							}
							$item['suggest'] = '';
							$item['message'] = '';
							$item['suggestion_id'] = $item['event_id'];
							$item['accept'] = _p('suggestion.view');
							$item['friend_user_id'] = Phpfox::getUserId();
							$item['friend_friend_user_id'] = $item['event_id'];
						}
					}
					$aRows['event_you_may_like'] = $aTemp;
				}
				break;
			
			case 'pages_you_may_like' :
				$offset =  $this -> getParam('offset');
				$aRows['pages_you_may_like'] = Phpfox::getService('suggestion') -> getPageYouMayLike($offset);
				break;
			case 'jobs_you_may_like' :
				$offset =  $this -> getParam('offset');
				$aRows['jobs_you_may_like'] = Phpfox::getService('suggestion') -> getJobYouMayLike($offset);
				break;
			case 'coupon':
				$offset =  $this -> getParam('iPage') * Phpfox::getParam('suggestion.number_item_on_other_block');
				$aRows['coupon'] = Phpfox::getService('suggestion.reminder')-> getReminder(Phpfox::getUserId(),'coupon',$offset,Phpfox::getParam('suggestion.number_item_on_other_block'));
				break;
			case 'jobposting':
				$offset =  $this -> getParam('iPage') * Phpfox::getParam('suggestion.number_item_on_other_block');
				$aRows['jobposting'] = Phpfox::getService('suggestion.reminder')-> getReminder(Phpfox::getUserId(),'jobposting',$offset,Phpfox::getParam('suggestion.number_item_on_other_block'));
				break;
			case 'contest':
				$offset =  $this -> getParam('iPage') * Phpfox::getParam('suggestion.number_item_on_other_block');
				$aRows['contest'] = Phpfox::getService('suggestion.reminder')-> getReminder(Phpfox::getUserId(),'contest',$offset,Phpfox::getParam('suggestion.number_item_on_other_block'));
				break;
			default :
				$aRows = phpfox::getService("suggestion")->getAllSuggestion(array('sView' => $type, 
				'iPage' => $iPage, 
				'limit' => Phpfox::getParam('suggestion.number_item_on_other_block')));
				
				break;
		}
		
		$this -> template() 
		-> assign(array(
			'aRows' => $aRows,
			'sFullUrl' => Phpfox::getParam('core.path'),
			'iPage'=>$iPage 
		));
		
		
		return 'block';
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean() {
		(($sPlugin = Phpfox_Plugin::get('suggestion.component_block_friends_clean')) ? eval($sPlugin) : false);
	}

}
?>