<?php
defined('PHPFOX') or exit('NO DICE!');
	
	class FeedBack_Component_Block_Feedback_Image extends Phpfox_Component
	{
		public function process()
		{
			$iPicId = $this->request()->get('link');
            $fb_id = $this->request()->get('fb_id');
			$aPicx = Phpfox::getLib('database')->select('*')
					->from(Phpfox::getT('feedback_picture'), 'fp')
					->where('fp.picture_id = '.$iPicId)
					->execute('getSlaveRow');
			$path_img = Phpfox::getParam('core.path').'file/pic/feedback/';
            $sFeedBackPics = Phpfox::getService('feedback')->getFeedBackPictures($fb_id);
            $nextPicID = -1;
            $prevPicID = -1;
            if(!empty($sFeedBackPics))
            {
                foreach($sFeedBackPics as $iKey => $aPic)
                {
                    if($aPic['picture_id'] == $iPicId){
                        if($iKey != count($sFeedBackPics) - 1) {
                            $nextPicID = $sFeedBackPics[$iKey + 1]['picture_id'];
                        }
                        if($iKey != 0) {
                            $prevPicID = $sFeedBackPics[$iKey - 1]['picture_id'];
                        }
                    }
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
                }
            }
			$this->template()->assign(array('aPic'=>$aPicx,
											'path'=>$path_img,
                                            'feedback_id' =>$fb_id,
                                            'picture_id' =>$iPicId,
                                            'nextPicId' => $nextPicID,
                                            'prePicId' => $prevPicID,
			));
			return 'block';
		}
	}
?>