<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Entry_Content_Blog extends Phpfox_Component{

	public function process ()
	{
		$aEntry = $this->getParam('aYnEntry');
		$isAdvBlog = false;
		$imagePath = null;
        $dirPath = Phpfox::getParam('core.dir_pic');
		if(!empty($aEntry['image_path'])) {
		    if(preg_match('/^ynadvancedblog/', $aEntry['image_path'])) {
                $isAdvBlog = true;
                $hasImage = false;
                $suffix = '';
                if(file_exists($dirPath . sprintf($aEntry['image_path'], '_1024'))) {
                    $suffix = '_1024';
                    $hasImage = true;
                }
                elseif(file_exists($dirPath . sprintf($aEntry['image_path'], '_big'))) {
                    $suffix = '_big';
                    $hasImage = true;
                }
                elseif(file_exists($dirPath . sprintf($aEntry['image_path'], ''))) {
                    $hasImage = true;
                }

                if($hasImage) {
                    $imagePath = Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aEntry['server_id'],
                        'path' => 'core.url_pic',
                        'file' => $aEntry['image_path'],
                        'suffix' => $suffix,
                        'return_url' => true
                    ));
                }
            }
            elseif(file_exists($dirPath . sprintf($aEntry['image_path'], '_1024'))) {
                $imagePath = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aEntry['server_id'],
                    'path' => 'core.url_pic',
                    'file' => $aEntry['image_path'],
                    'suffix' => '_1024',
                    'return_url' => true
                ));
            }
        }
		$bIsPreview = $this->getParam('bIsPreview');
		$this->template()->assign(array(
				'aBlogEntry' => $aEntry,
				'bIsPreview' => $bIsPreview,
                'imagePath' => $imagePath,
                'isAdvBlog' => $isAdvBlog,
                'defaultAdvBlogPhoto' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-blogs/assets/image/blog_photo_default.png'
			)
		);	
			
	}
}