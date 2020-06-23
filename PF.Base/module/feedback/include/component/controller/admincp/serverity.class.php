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
class FeedBack_Component_Controller_Admincp_Serverity extends Phpfox_Component
{
	public function checkCategoryName($name)
	{
		if(strlen($name) > 50)
		{
			return true;
		}
		return false;
	}

	public function process()
	{
		$message = '';
		$iCnt = 0;
		$iPage = $this->request()->getInt('page');
        $serverity_id = (int)$this->request()->get('serverity_id');
		$iPageSize = 5;
		$edit_servertity = "";
		$aLanguages = Phpfox::getService('language')->getAll();
		$aServerities = Phpfox::getService('feedback')->getFeedBackServerityAll($iPage, $iPageSize, $iCnt);
		Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));
		$aVals = $this->request()->get('val');
		if(isset($aVals['submit']))
		{
			$oFilter = Phpfox::getLib('parse.input');
			$aVals['description'] = $oFilter->clean(strip_tags($aVals['description']));

			$idSer = Phpfox::getService('feedback.process')->addServerity($aVals);
			if($idSer)
			{
				unset($_SESSION['ser_name']);
				unset($_SESSION['ser_description']);
				$iCnt = $iCnt + 1;
				$iPage = (($iCnt%$iPageSize) > 0) ? (int)($iCnt/$iPageSize) + 1 : (int)($iCnt/$iPageSize);
				$this->url()->send('admincp.feedback.serverity',array('page' => $iPage), _p('feedback.the_serverity_title_was_created_successfully', array('title'=>$aVals['name_'.$aLanguages[0]['language_id']])));
			}
			else
			{
				$iPage = (($iCnt%$iPageSize) > 0) ? (int)($iCnt/$iPageSize) + 1 : (int)($iCnt/$iPageSize);
				$this->url()->send('admincp.feedback.serverity',array('page' => $iPage),'Serverity Name "'.$aVals['name_'.$aLanguages[0]['language_id']] .'" was created fail.');
			}
		}
		elseif(isset($aVals['editserverity']))
		{
			$aVals = $this->request()->get('val');
			$oFilter = Phpfox::getLib('parse.input');
            $aVals['description'] = $oFilter->clean(strip_tags($aVals['description']));
			$isUpdate = Phpfox::getService('feedback.process')->updateServerity($aVals);
			if($isUpdate)
			{
				$this->url()->send('admincp.feedback.serverity.page_'.$iPage,null,'Serverity Name "'.$aVals['name_'.$aLanguages[0]['language_id']] .'" was updated successfully.');
			}
			else
				$this->url()->send('admincp.feedback.serverity.page_'.$iPage,null,'Serverity Name "'.$aVals['name_'.$aLanguages[0]['language_id']] .'" was updated fail.');
		}
        elseif(!empty ($serverity_id)) {
            $edit_servertity = 'edit';
            $aSer = phpfox::getLib('phpfox.database')->select('*')
                ->from(Phpfox::getT('feedback_serverity'))
                ->where('serverity_id=' . $serverity_id)
                ->execute('getRow');
            $this->template()->assign([
            	'sPhraseName' => $aSer['name']
			]);
        }
		$name = (isset($_SESSION['ser_name']))?$_SESSION['ser_name']:'';
		$description = (isset($_SESSION['ser_description']))?$_SESSION['ser_description']:'';
		unset($_SESSION['ser_name']);
		unset($_SESSION['ser_description']);
		$this->template()->setHeader('cache',array(
				'feedback.js' => 'module_feedback',
				'pager.css' => 'style_css',
				'admin.js' => 'module_feedback',
				'colpick.js' => 'module_feedback',
				'colorpicker.css' => 'module_feedback',
        	));
        	$this->template()->assign(array(
				'aSers'=> $aServerities,
				'aEdit' => !empty($aSer) ? $aSer : [],
				'pageNumber'=>($iPage>0) ? $iPage : 1,
				'core_path' => Phpfox::getParam('core.path'),
					'edit'=> (isset ($edit_servertity)?$edit_servertity:''),
				'name'=>$name,
				'description'=>$description,
        	));
        $this->template()->setBreadCrumb(_p('feedback.feedback_serverities'), $this->url()->makeUrl('admincp.feedback.serverity'))
						->setPhrase(array('feedback.are_you_sure_you_want_to_delete_these_feedbacks', 'feedback.no_feedback_selected_to_delete', 'feedback.are_you_sure'));
	}
}
?>
