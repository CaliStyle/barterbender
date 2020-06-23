<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php
class FeedBack_Component_Controller_Admincp_Status extends Phpfox_Component
{
	public function process()
	{
		$status_id = (int)$this->request()->get('status');
		$edit_status = "";
		$iPage = $this->request()->getInt('page');
		$iPageSize = 5;
		$iCnt = 0;
		$aLanguages = Phpfox::getService('language')->getAll();
		$sStatus = phpfox::getService('feedback')->getFeedBackStatusAll($iPage, $iPageSize, $iCnt);		
		Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));
		$aVals = $this->request()->get('val');
		if(isset($aVals['submit']))
		{
			if($aVals = Phpfox::getService('language')->validateInput($aVals, 'name', false))
			{
				$oFilter = Phpfox::getLib('parse.input');
				$aVals['name'] = $oFilter->clean(strip_tags($aVals['name']), 255);
				$aStatus = Phpfox::getService('feedback.process')->addStatus($aVals);
				if($aStatus)
				{
					$iCnt = $iCnt + 1;
					$iPage = (($iCnt%$iPageSize) > 0) ? (int)($iCnt/$iPageSize) + 1 : (int)($iCnt/$iPageSize);
					$this->url()->send('admincp.feedback.status',array('page' => $iPage),_p('feedback.the_status_name_was_created_successfully', array('name'=>$aVals['name'])));
				}
				else
				{
					$iPage = (($iCnt%$iPageSize) > 0) ? (int)($iCnt/$iPageSize) + 1 : (int)($iCnt/$iPageSize);
					$this->url()->send('admincp.feedback.status',array('page' => $iPage),'Status Name "'.$aVals['name'] .'" was created fail.');

				}

			}
		}
		elseif(isset($aVals['editstatus']))
		{
                $oFilter = Phpfox::getLib('parse.input');
                $aVals['description'] = $oFilter->clean(strip_tags($aVals['description']));
				$isUpdate = Phpfox::getService('feedback.process')->updateStatusAdmin($aVals);
                //echo $this->request()->get('page');die();
                $url = phpfox::getLib('url')->makeUrl('admincp.feedback.status',array('page'=>$this->request()->get('page')?$this->request()->get('page'):1));
				if($isUpdate)
				{
					$this->url()->send($url,null, _p('feedback.status_name_was_updated_successfully', array('name'=>$aVals['name_'.$aLanguages[0]['language_id']])));
				}
				else
					$this->url()->send($url,null ,$aVals['name_'.$aLanguages[0]['language_id']] ." was updated fail.");
			
		}
		elseif(!empty ($status_id))
		{
			$edit_status = 'edit';
			$aStatus =  phpfox::getLib('phpfox.database')->select('*')
			->from(Phpfox::getT('feedback_status'))
			->where('status_id='.$status_id)
			->execute('getRow');
			$this->template()->assign([
				'sPhraseName' => $aStatus['name']
			]);
		}

		$this->template()->setHeader('cache',array(
                'feedback.js' => 'module_feedback',
        		'pager.css' => 'style_css',
            	'admin.js' => 'module_feedback',
				'colpick.js' => 'module_feedback',
				'colorpicker.css' => 'module_feedback',
				
        		));
        		$this->template()->assign(array(
                    'sStatus'=> $sStatus,
                    'core_path' => Phpfox::getParam('core.path'),
                    'edit'=> (isset ($edit_status)?$edit_status:''),
                    'aStatus'=> isset($aStatus)?$aStatus:'',
        	        'page_number' =>($iPage>0) ? $iPage:1
            ));
        $this->template()->setBreadCrumb(_p('feedback.feedback_statuses'), $this->url()->makeUrl('admincp.feedback.status'))
						->setPhrase(array('feedback.are_you_sure_you_want_to_delete_these_feedbacks', 'feedback.no_feedback_selected_to_delete', 'feedback.are_you_sure'));
	}
}
?>
