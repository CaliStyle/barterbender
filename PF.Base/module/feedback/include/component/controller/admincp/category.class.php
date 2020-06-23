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
class FeedBack_Component_Controller_Admincp_Category extends Phpfox_Component
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
		$iPageSize = 5;
		$aLanguages = Phpfox::getService('language')->getAll();
		$aCats = Phpfox::getService('feedback')->getFeedBackCatAll($iPage, $iPageSize, $iCnt);
		Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));
        $aVals = $this->request()->get('val');
		if (isset($aVals['submit']))
		{
            if ($aVals = $this->_validate($aVals))
			{
				$idCat = Phpfox::getService('feedback.process')->addCategory($aVals);
				if($idCat)
				{
					unset($_SESSION['cat_name']);
					unset($_SESSION['cat_description']);
					$iCnt = $iCnt + 1;
					$iPage = (($iCnt%$iPageSize) > 0) ? (int)($iCnt/$iPageSize) + 1 : (int)($iCnt/$iPageSize);
					$this->url()->send('admincp.feedback.category',array('page' => $iPage),_p('Category successfully added.'));
					
				}
				else {
					$iCnt = $iCnt + 1;
					$iPage = (($iCnt%$iPageSize) > 0) ? (int)($iCnt/$iPageSize) + 1 : (int)($iCnt/$iPageSize);
					$this->url()->send('admincp.feedback.category',array('page' => $iPage), _p('Category added failed.'));
				}
			}
		}
		elseif (isset($aVals['editcategory']))
		{
			$aVals = $this->request()->get('val');
			$isUpdate = Phpfox::getService('feedback.process')->updateCategory($aVals);
			if($isUpdate)
			{
				$this->url()->send('admincp.feedback.category.page_'.$aVals['page'], null,_p('Category successfully updated.'));
			}
			else
			$this->url()->send('admincp.feedback.category.page_'.$aVals['page'],null,_p('Category updated failed.'));
		}
		$name = (isset($_SESSION['cat_name']))?$_SESSION['cat_name']:'';
		$description = (isset($_SESSION['cat_description']))?$_SESSION['cat_description']:'';
		unset($_SESSION['cat_name']);
		unset($_SESSION['cat_description']);
		$this->template()->setHeader('cache',array(
            'feedback.js' => 'module_feedback',
			'pager.css' => 'style_css', 
		));
		$this->template()->assign(array(
            'aCats'=> $aCats,
			'pageNumber'=>($iPage>0)?$iPage:1,
			'name'=>$name,
			'description'=>$description,
			'aLanguages' => $aLanguages,
		));
        $this->template()->setBreadCrumb(_p('feedback.feedback_categories'), $this->url()->makeurl('admincp.feedback.category'))
						->setPhrase(array('feedback.are_you_sure_you_want_to_delete_these_feedbacks', 'feedback.no_feedback_selected_to_delete', 'feedback.are_you_sure'));
	}
    /**
     * validate input value
     * @param $aVals
     *
     * @return bool
     */
    private function _validate($aVals)
    {
        return Phpfox::getService('language')->validateInput($aVals, 'name', false);
    }
}
?>
