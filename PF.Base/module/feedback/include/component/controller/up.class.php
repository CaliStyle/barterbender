<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
class Feedback_Component_Controller_Up extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		// Make sure the user is allowed to upload an image
		Phpfox::isUser(true);
		$sCats = phpfox::getService('feedback')->getFeedBackCat();

		$aTypes = $sCats;
		$aStatus = Phpfox::getService('feedback')->getFeedBackStatus();
		//    $aStatus['All'] = 'All';
		$aPages = array(1, 5, 10, 15);

		$aDisplays = array();
		foreach ($aPages as $iPageCnt)
		{
			$aDisplays[$iPageCnt] = _p('core.per_page', array('total' => $iPageCnt));
		}
		$aSorts = array(
            'fb.date_modify' => _p('feedback.most_recent'),
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
                'default' => '5'
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
                    //       'search' => "( fb.title LIKE '%[VALUE]%' OR fb.feedback_description LIKE '%[VALUE]%' )"
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
                $oFilter->setCondition("(fb.privacy = 1 OR (fb.privacy = 2 AND fb.user_id=".Phpfox::getUserId()."))");
                if ($oFilter->isSearch())
                {

                	$aSearch = $this->request()->getArray('search');
                	$oFilter->search('like%', array(
                        'fb.title',
                        'fb.feedback_description'
                        ), $aSearch['keyword']);
                        $aSearchResults = Phpfox::getService('feedback')->getSearch($oFilter->getConditions(), $oFilter->getSort());
                        $oFilter->cacheResults('search', $aSearchResults);

                }
                $iPage = $this->request()->getInt('page');
                $iPageSize = 5;
                $sFormUrl = $this->url()->makeUrl('feedback');

                $num_feedback_pictures = 0;
                $max_feedback_pictures = Phpfox::getUserParam('feedback.define_how_many_pictures_can_be_uploaded_per_feedback');
                $feedback_title = '';
                if($this->request()->get('feedback'))
                {
                	$feedback_id = $this->request()->get('feedback');
                	$feedback_title = Phpfox::getService('feedback')->getFeedBackDetailById($feedback_id);
                	$pics = Phpfox::getService('feedback')->getFeedBackPictures($feedback_id);
                	$num_feedback_pictures = count($pics);
                }
                else
                $feedback_id = 0;
                if($feedback_id == 0)
                {
                	phpfox_error::set(_p('feedback.invalid_feedback_please_try_again'));
                }
                if(($feedback_title['user_id'] != Phpfox::getUserId()) && !phpfox::isAdmin())
                {
                	$this->url()->send('subscribe');
                }
                $rest_picture = $max_feedback_pictures - $num_feedback_pictures;
                if ($rest_picture < 0 || $max_feedback_pictures <=0)
                {
                	phpfox_error::set(_p('feedback.you_have_reach_limit_uploaded_pictures_for_this_feedback'));
                }
               /* $rest_picture = 0;
                if($rest_picture == 0)
                {
                	phpfox_error::set(_p('feedback.you_have_reach_limit_uploaded_pictures_for_this_feedback'));
                }*/

                $this->template()->assign(
                array(
                  'rest_pictures'=>$rest_picture,
                	)
                );

                $this->template()->setHeader(array(
                   'upload.css' => 'module_feedback'

                ));
                if($feedback_id != 0)
                {
                	$this->template()->setBreadcrumb(_p('feedback.feedback'), phpfox::getLib('url')->makeUrl('feedback'))
                     ->setBreadCrumb($feedback_title['title'], $this->url()->permalink('feedback.detail', $feedback_title['title_url']), true);        
                	$this->template()->setBreadcrumb(_p('feedback.upload_feedback_picture'), phpfox::getLib('url')->makeUrl('feedback.up',array('feedback'=>$feedback_id)), true);
                	$this->template()->assign(array(
                	 'feedback_title'=>$feedback_title['title_url']
                	 ));
                }

                 
                $this->template()->assign(array(
                    'aFeedBack' => $feedback_title,
                //'sHeader' => _p('feedback.upload_feedback_picture'),
                'feedback_id' => $feedback_id,
                'max_feedback_picture' => $max_feedback_pictures,
                'rest_picture' => $rest_picture,            
                'core_path' =>Phpfox::getParam('core.path'),
                'sFormUrl'=>$sFormUrl,
                
                //'feedback_title'=>$feedback_title['title']
                ));
                Phpfox::getUserParam('feedback.can_upload_pictures', true);
                //using massuploader (default of phpfox to upload file)
                $aCallback = null;
                $iMaxFileSize = (Phpfox::getUserParam('feedback.picture_max_upload_size') === 0 ? null : ((Phpfox::getUserParam('feedback.picture_max_upload_size') / 1024) * 1048576));
                $this->template()->assign(
                array(
                'iMaxFileSize' => $iMaxFileSize,
                'feedback_id' =>$feedback_id,
                )
                );
                $url = $this->url()->makeUrl('feedback.detail', $feedback_title['title_url']);
                if(PHPFOX_IS_AJAX_PAGE || PHPFOX_IS_AJAX)
                {
                	echo '<script type="text/javascript">var redict_url = "'.$url.'";</script>';
                }
                $this->template()->setPhrase(array(
                            'feedback.you_can_upload_a_jpg_gif_or_png_file',
                            'core.name',
                            'core.status',
                            'core.in_queue',
                            'core.upload_failed_your_file_size_is_larger_then_our_limit_file_size',
                            'core.more_queued_than_allowed',
                )
                )
                ->setHeader(array(
                '<script type="text/javascript">
                    var redict_url = "'.$url.'";
                </script>',
                '<script type="text/javascript">$Behavior.feedbackProgressBarSettings = function(){ if ($Core.exists(\'#js_feedback_form_holder\')) { oProgressBar = {holder: \'#js_feedback_form_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_progress_uploader\', add_more: true, max_upload: ' . (int) $rest_picture . ', total: 1, frame_id: \'js_upload_frame\', file_id: \'image[]\'}; $Core.progressBarInit(); } }</script>',
                )
                );
                $this->template()->setHeader('cache', array(
					'progress.js' => 'static_script',
					'feedback.js'=>'module_feedback',

					)
					);
				
					//end////////////////////////////////////////////////

					(($sPlugin = Phpfox_Plugin::get('feedback.component_controller_upload_end')) ? eval($sPlugin) : false);
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('feedback.component_controller_upload_clean')) ? eval($sPlugin) : false);
	}
}

?>