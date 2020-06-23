<?php
class FeedBack_Component_Block_Feedback_Detail_Status extends Phpfox_Component
{
	public function process()
	{
		$fb_id = $this->request()->get('feedback');
		$sTitle = $this->request()->get('req3');
		$core_url = Phpfox::getParam('core.path');
		$sFeedBacks = array();
		if(!empty($fb_id))
		{
			$sFeedBacks = Phpfox::getService('feedback')->getFeedBackDetailById($fb_id);
		}
		else
		{
			$sFeedBacks = Phpfox::getService('feedback')->getFeedBackDetailByAlias($sTitle);
		}
		if(empty($sFeedBacks))
		{
			return false;
		}
		$sFeedBackPics = Phpfox::getService('feedback')->getFeedBackPictures($sFeedBacks['feedback_id']);
		$aFirstPic = array();
		if(!empty($sFeedBackPics))
		{
			$aFirstPic = $sFeedBackPics[0];
				
			foreach($sFeedBackPics as $iKey => $aPic)
			{
				$url = PHPFOX_DIR_FILE.'pic/feedback/'.$aPic['picture_path'];
				list($iWidth, $iHeight, $sType, $sAttr) = @getimagesize($url);


				if($iHeight >= 600)
				{
					$iWidth = 600 * $iWidth / $iHeight;
					$iHeight = 600;
				}
				if($iWidth >= 600)
				{
					$iHeight = 600 * $iHeight / $iWidth;
					$iWidth = 600;
				}
				$iWidth += 30;
				$iHeight += 80;
				$sFeedBackPics[$iKey]['width'] = $iWidth;
				$sFeedBackPics[$iKey]['height'] = $iHeight;

				$aFileInfo = pathinfo($aPic['picture_path']);
                $sFeedBackPics[$iKey]['thumb_url_temp'] = $aFileInfo['dirname'].PHPFOX_DS.$aFileInfo['filename'].'_240.'.$aFileInfo['extension'];
			}
		}
                
		$this->template()->assign(array('feedback_id'=> $sFeedBacks['feedback_id'],
										'aFeedBackPics' => $sFeedBackPics,
										'aFirstPic' =>$aFirstPic,						
						))->setPhrase(array('feedback.feedback_photo'));
                return 'block';
	}

}
?>