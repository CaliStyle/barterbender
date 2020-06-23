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
class FeedBack_Component_Controller_Manage extends Phpfox_Component
{
	public function isValid($aVals)
	{
		$errors = "";
		if(!isset($aVals['title']) || ($aVals['title'] == ''))
		$errors .= '<div class="error_message">Title can\'t be empty. </div> <br />';
		if(empty($aVals['description']))
		$errors .= 'Description of feedback can\'t be empty.';
		return $errors;
	}

	public function process()
	{
        Phpfox::getUserParam('feedback.can_view_feedback', true);
		$login = phpfox::getLib('url')->makeURL('user.login');
		if(!phpfox::isUser())
		{
			$this->url()->send($login, null,'You can\'t access this feature. Please login now.');
		}
		$core_url = phpfox::getParam('core.path');
		$sCats = phpfox::getService('feedback')->getFeedBackCat();
		$aTypes = $sCats;
		$aStatus = Phpfox::getService('feedback')->getFeedBackStatus();
		if(isset($_POST['editfeedback']))
		{
			$aVals = $this->request()->get('val');
			$errors = $this->isValid($aVals);
			if(empty ($errors))
			{
                $oFilter = Phpfox::getLib('parse.input');
                $aVals['title'] = $oFilter->clean(strip_tags($aVals['title']), 255);
                $aVals['description'] = $oFilter->clean(strip_tags($aVals['description']));
				$isUpdate = Phpfox::getService('feedback.process')->update($aVals);
			}
			else 
			{
				Phpfox_Error::set($errors);
				$this->url()->send('feedback.detail/'.$aVals['feedback_title_url']);
			}

			if($isUpdate)
			{
				$this->url()->send('feedback.detail/'.$isUpdate, null, _p('feedback.your_feedback_title_was_updated_successfully', array('title'=>$aVals['title'])));
			}
			else
			{
				$this->url()->send('feedback.detail/'.$isUpdate, null, _p('feedback.your_feedback_title_was_updated_fail', array('title'=>$aVals['title'])));
			}
		}

		$aPages = array(1, 5, 10, 15);
		$aDisplays = array();
		foreach ($aPages as $iPageCnt)
		{
			$aDisplays[$iPageCnt] = _p('core.per_page', array('total' => $iPageCnt));
		}
		$aSorts = array(
			'fb.time_stamp' => _p('feedback.most_recent'),
                        'fb.total_vote' => _p('feedback.most_voted'),
			'fb.total_view' => _p('feedback.most_viewed'),
			'fb.total_comment' => _p('feedback.most_comment'),
                        'fb.is_featured' => _p('feedback.featured')
		);
		$aFilters = array(
			'type_cats' => array(
                'type' => 'select',
                'options' => $aTypes,
				'add_any'=>true,
				'search' =>"fb.feedback_category_id = [VALUE]"
				),
            'type_status' => array(
                'type' => 'select',
                'options' => $aStatus,
                'add_any'=>true,
				'search' =>"fb.feedback_status_id = [VALUE]"
				),
			'keyword'=>array(
			'type'=>'input:text',
			 'search' => "( fb.title LIKE '%[VALUE]%' OR fb.feedback_description LIKE '%[VALUE]%' )"
			),
            'sort' => array(
                    'type' => 'select',
                    'options' => $aSorts,
                    'default' => 'fb.time_stamp'
                    ),
           'sort_by' => array(
                    'type' => 'select',
                    'options' => array(
                            'DESC' => _p('core.descending'),
                            'ASC' => _p('core.ascending')
                    ),
                    'default' => 'DESC'
                    )
                    );

                     $oFilter = Phpfox::getLib('search')->set(array(
                'type' => 'feedback',
                'filters' => $aFilters,
                'cache' => true,
                'field' => 'fb.feedback_id',
                'search' => 'keyword'
                ));
                $oFilter->setCondition(" (fb.user_id = ".(int)Phpfox::getUserId().")");
                $isSearch = false;
                $checkSearch = $this->request()->get('search-rid');
                if ($checkSearch)
                {	
                	$isSearch = true;
                	/*$aSearch = $this->request()->getArray('search');
                	$oFilter->search('like%', array(
						'fb.title',
						'fb.feedback_description'
						), $aSearch['keyword']);
						$aSearchResults = Phpfox::getService('feedback')->getSearch($oFilter->getConditions(), $oFilter->getSort());
						$oFilter->cacheResults('sear
						ch', $aSearchResults);*/
                }
                $sFormUrl = $this->url()->makeUrl('feedback.manage');
                $iPage = $this->request()->getInt('page');
                $iPageSize = 5;
                list($iCnt, $aFeedBacks) = Phpfox::getService('feedback')->get($oFilter->getConditions(), $oFilter->getSort(), $oFilter->getPage(), $iPageSize);
                Phpfox::getService('feedback')->getExtra($aFeedBacks);
                $iCnt = $oFilter->getSearchTotal($iCnt);
                Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));
                $this->template()
                ->setHeader('cache', array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'quick_edit.js' => 'static_script',
                    'pager.css' => 'style_css',
                    'switch_legend.js' => 'static_script',
                    'switch_menu.js' => 'static_script',
                    'feedback.js' => 'module_feedback'
                    ))
                    ->setBreadcrumb('My Feedbacks')
                    ->setTitle('My Feedbacks')
                    ->assign(array(
                    'core_url'=>$core_url,
                    'core_path' =>$core_url,
                    'aMyFeedBacks' => $aFeedBacks,
                    'sFormUrl'=>$sFormUrl,
                    'isSearch' => $isSearch              
                    ));

                    if (Phpfox::getUserId())
                    {
                    	$this->template()->setEditor(array(
                'load' => 'simple'
                ));
                    }

	}
}
?>
