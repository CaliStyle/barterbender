<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Entry_Content_Music extends Phpfox_Component
{
    public function process()
    {
        $aEntry = $this->getParam('aYnEntry');
        $aEntry['song_path'] = Phpfox::getService('contest.entry.item.music')->getSongPath($aEntry['song_path'], $aEntry['song_server_id']);
        
        $bIsPreview = $this->getParam('bIsPreview');
        
        $this->template()->assign(array(
            'aMusicEntry' => $aEntry, 
            'bIsPreview' => $bIsPreview
        ));
    }
}

?>