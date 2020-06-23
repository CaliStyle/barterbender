<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Detailcheckinlist extends Phpfox_Component {

    public function process()
    {
        if (!Phpfox::isUser())
        {
            return false;
        }
        $aYnAuctionDetail = $this->getParam('aYnAuctionDetail');
        $aAuction = $aYnAuctionDetail['aAuction'];
		
        $aAuction['linkAuction'] = urlencode($aAuction['linkAuction']);
        $aAuction['titleAuction'] = urlencode($aAuction['name']);

        $canManageAuction = $aAuction['canManageDashBoard'];
        if (empty($aAuction['logo_path'])) {
            $aAuction['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
        }
        $this->template()->assign(array(
            'aAuction' => $aAuction,
            'canManageBusiness' => $canManageAuction,
                )
        );
        return 'block';
    }

}

?>
