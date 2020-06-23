<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Menu_Seller extends Phpfox_Component
{

    public function process()
    {
        $sFullControllerName = Phpfox::getLib('module')->getFullControllerName();

        // This block is hardly embed to block 1 in module e-commerce. So if we don't in module auction, do not show this block.
        if ($this->request()->get('req1') != 'auction')
        {
            return false;
        }

        $this->template()->assign(
            array(
                'sHeader' => _p('menu_seller'),
                'sFullControllerName' => $sFullControllerName
            )
        );
        
        return 'block';
    }

}

?>