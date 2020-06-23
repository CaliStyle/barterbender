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

class FeedBack_Component_Controller_Upload extends Phpfox_Component
{
    public function process()
    { 
        $redirectUrl = phpfox::getLib("url")->makeUrl('feedback.up',array('feedback'=>$this->request()->get('feedback')));
        $this->url()->send($redirectUrl);
    	$sCats = phpfox::getService('feedback')->getFeedBackCat();

		$aTypes = $sCats;
		$aStatus = Phpfox::getService('feedback')->getFeedBackStatus();
		//	$aStatus['All'] = 'All';
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
        $max_feedback_pictures = 5;
        $feedback_title = '';
        if($this->request()->get('feedback'))
        {
           $feedback_id = $this->request()->get('feedback');
           $feedback_title = Phpfox::getService('feedback')->getFeedbackTitle($feedback_id);
        } 
       else 
       		$feedback_id = 0;
        $rest_picture = $max_feedback_pictures - $num_feedback_pictures;
        
        $settings['max_file_size_upload_mb'] = 2;
        if ($rest_picture < 0 || $max_feedback_pictures <=0)
            $rest_picture = 0;
        $this->template()->assign(
                        array(                            
                            'rest_pictures'=>$rest_picture,
                            )
                        );
        
        if(isset($_FILES['uploadfile']))
        {            
            $file = $_FILES['uploadfile'];
            $currentDate = date("Y-m-d H:i:s");
            $fullname = $file['name'];
            $arrayName = explode(".",$fullname);
            $ext = $arrayName[sizeof($arrayName)-1];
            $lengExt = strlen($ext);
            $name_picture = substr($fullname,0,strlen($fullname)- ($lengExt + 1));
            $filesize = $file['size'];
            phpfox::getLib('file')->load('uploadfile',array('jpg','png','gif','jpeg'));                        
            $p = PHPFOX_DIR_FILE.'pic'.PHPFOX_DS.'feedback'.PHPFOX_DS;          
            if (!is_dir($p))
            {
                if(!@mkdir($p,0777,1))
                {
                     
                }
            }

            $target_path = phpfox::getLib('file')->upload('uploadfile', $p, $file['name']);
            $picture = array();
            $picture['file_name']= $name_picture;
            $picture['feedback_id']= $feedback_id;
            $picture['filesize']= $filesize;
            $picture['picture_path']= str_replace('%s','',$target_path);   
            $type = substr($picture['picture_path'],  strrpos($picture['picture_path'], '.'));
            $thumb_url = substr($picture['picture_path'],0,strrpos($picture['picture_path'],'.')).'_thumb'.$type;
            $thumb_url = str_replace(phpfox::getParam('core.path').'file'.'/',PHPFOX_DIR_FILE, $thumb_url);
            $url = str_replace('_thumb','',$thumb_url);          
            $oImage = Phpfox::getLib('image');
           
            $oImage->createThumbnail(PHPFOX_DIR_FILE.'pic/feedback/'.$url, PHPFOX_DIR_FILE.'pic/feedback/'.$thumb_url, 120, 120);
            $thumb_url = str_replace(PHPFOX_DIR_FILE,phpfox::getParam('core.path').'file'.'/',$thumb_url);          
            $picture['thumb_url'] = $thumb_url;
            $idPic = Phpfox::getService('feedback.process')->uploadPicture($picture);
            Phpfox::getService('feedback.process')->updateTotalPicture($feedback_id);

        }
         $this->template()->setHeader(array(
            'jquery-1.3.2.js' => 'module_feedback' ,
            'swfupload.js' => 'module_feedback' ,
            'jquery.swfupload.js' => 'module_feedback',                                                                                
            'upload.css' => 'module_feedback'
       ));
        $this->template()->assign(array(
            'sHeader' => 'Upload Feedback Picture',
            'feedback_id' => $feedback_id,
            'max_feedback_picture' => $max_feedback_pictures,
            'rest_picture' => $rest_picture,            
            'core_path' =>Phpfox::getParam('core.path'),
        	'sFormUrl'=>$sFormUrl,
        	'feedback_title'=>$feedback_title
        ));
    }
}

?>
