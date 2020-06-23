<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_LikeBrowse extends Phpfox_Component {

    public function process()
    {
        $aLikes = Phpfox::getService('like')->getLikes($this->request()->get('type_id'), $this->request()->getInt('item_id'));

        $sErrorMessage = '';

        if (!count($aLikes))
        {
            $sErrorMessage = _p('like.nobody_likes_this');
        }

        $this->template()->assign(array(
            'aLikes' => $aLikes,
            'sErrorMessage' => $sErrorMessage,
                )
        );

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        
    }

}

?>