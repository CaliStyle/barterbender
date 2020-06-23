<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Entry_Content_Photo extends Phpfox_Component
{
    public function process()
    {
        $aEntry = $this->getParam('aYnEntry');
        $sFullTmp = Phpfox::getParam('core.dir_pic').$aEntry['image_path'];
		if (file_exists(sprintf($sFullTmp, '')))
        {
            $sSuffix = '';
        }
        else
        {
            $sSuffix = '_1024';
        }

        $bIsPreview = $this->getParam('bIsPreview');

        $this->template()->assign(array(
            'aPhotoEntry' => $aEntry,
            'sSuffix' => $sSuffix,
            'bIsPreview' => $bIsPreview,
            'sStaticLink' => Phpfox::getParam('core.path_file')
        ));
    }
}

?>