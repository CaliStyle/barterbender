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
class FeedBack_Component_Controller_Admincp_StatusFeedback extends Phpfox_Component
{
	public function process()
	{
		if($aVals = $this->request()->get('val'))
		{
			$isUpdate = Phpfox::getService('feedback.process')->updateStatus($aVals);
			if($isUpdate)
			{
				$this->url()->send('admincp.feedback.statusfeedback',null,'The Feedback "'.$aVals['title'].'" Status was updated successfully.');
			}
			else
			{
				$this->url()->send('admincp.feedback.statusfeedback',null,'The Feedback "'.$aVals['title'].'" Status was updated fail.');
			}
		}
		$core_url = Phpfox::getParam('core.path');
		$sCats = phpfox::getService('feedback')->getFeedBackCat();
		$sCats['NULL']= 'Uncategorized';
		$aTypes = $sCats;
		$sStatus = Phpfox::getService('feedback')->getFeedBackStatus();
		$aStatus = $sStatus;
		$aPages = array(1, 10, 20, 30);
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
            'display' => array(
				'type' => 'select',
				'options' => $aDisplays,
				'default' => '10'
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
                    ),
           		'keyword' => array(
             		'type' => 'input:text',
            	       'search' => "( fb.title LIKE '%[VALUE]%' OR fb.feedback_description LIKE '%[VALUE]%' )"
                    )

                    );
                    $oFilter = Phpfox::getLib('search')->set(array(
                'type' => 'feedback',
                'filters' => $aFilters,
                'cache' => true,
                'field' => 'fb.feedback_id',
                'search' => 'keyword'
                )
                );
          /*      if ($oFilter->isSearch())
                {
                	$aSearch = $this->request()->getArray('search');
                	$oFilter->search('like%', array(
						'fb.title',
						'fb.feedback_description'
						), $aSearch['keyword']);
						$aSearchResults = Phpfox::getService('feedback')->getSearch($oFilter->getConditions(), $oFilter->getSort());
						$oFilter->cacheResults('search', $aSearchResults);
                }
*/
                $iPage = $this->request()->getInt('page');
                $iPageSize = 10;
                list($iCnt, $aFeedBacks) = Phpfox::getService('feedback')->getFeedBacks($oFilter->getConditions(), $oFilter->getSort(), $oFilter->getPage(), $iPageSize);
/*                foreach ($aFeedBacks as $key => $aFeedBack)
                {
                	$aFeedBacks[$key]['time_stamp'] = date("F j, Y, g:i",$aFeedBack['time_stamp']);
                }*/
                $iCnt = $oFilter->getSearchTotal($iCnt);
                Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));
                $sFormUlr = $this->url()->makeUrl('admincp.feedback.feedbacks');
                $this->template()
                ->setHeader('cache', array(
                'pager.css' => 'style_css',                  
                'feedback.js' => 'module_feedback',              
                ))
                ->setTitle('Feed Back')
                ->assign(array(
                'core_url'=>$core_url,
                'aFeedBacks' => $aFeedBacks,
                'sCats' => $sCats,
                'sStatus' => $sStatus,
                'aSorts' => $aSorts,
                'sFormUrl'=>$sFormUlr
                ));

	}
}
?>