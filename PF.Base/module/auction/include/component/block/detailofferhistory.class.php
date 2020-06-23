<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_DetailOfferHistory extends Phpfox_Component {

    public function process()
    {
        $aYnAuctionDetail = $this->getParam('aYnAuctionDetail');
        
        $aAuction = $aYnAuctionDetail['aAuction'];
        
        $this->template()->assign(array(
            'aAuction' => $aAuction,
            )
        );

        $iProductId = $aAuction['product_id'];

        if(Phpfox::getUserId() != $aAuction['user_id']){
            return $this->url()->send('auction.detail',array($aAuction['product_id'] ));
        }
      /*sort table*/
        if($this->request()->get('sortfield') !='' ){
            $sSortField = $this->request()->get('sortfield'); 
            Phpfox::getLib('session')->set('ynauction_offerlist_sortfield',$sSortField);  
        }
        $sSortField = Phpfox::getLib('session')->get('ynauction_offerlist_sortfield');
        if(empty($sSortField)){
            $sSortField = ($this->request()->get('sortfield') !='' )?$this->request()->get('sortfield'):'time'; 
            Phpfox::getLib('session')->set('ynauction_offerlist_sortfield',$sSortField);  
        }


        if($this->request()->get('sorttype') !='' ){
            $sSortType = $this->request()->get('sorttype'); 
            Phpfox::getLib('session')->set('ynauction_offerlist_sorttype',$sSortType);  
        }
        $sSortType = Phpfox::getLib('session')->get('ynauction_offerlist_sorttype');
        if(empty($sSortType)){
            $sSortType = ($this->request()->get('sorttype') !='' )?$this->request()->get('sorttype'):'asc'; 
            Phpfox::getLib('session')->set('ynauction_offerlist_sorttype',$sSortType);  
        }

        switch ($sSortField) {
            case 'name':
                $sSortFieldDB = 'u.full_name';
                break;
            case 'amount':
                $sSortFieldDB = 'eao.auctionoffer_price';
                break;
            case 'time':
                $sSortFieldDB = 'eao.auctionoffer_creation_datetime';
                break;
            default:
                
                break;
        }
        $aSort = array('field' => $sSortFieldDB,'type' => $sSortType);
        $aSort = implode(" ", $aSort);
        /*
        Phpfox::getLib('session')->set('ynauction_offerlist_sortfield',$sSortField);
        Phpfox::getLib('session')->set('ynauction_offerlist_sorttype',$sSortType);
        */
        $iPage = $this->request()->getInt('page') != '' ? $this->request()->getInt('page') : 1;
        $iLimit = 10;
        list($iCnt, $aOfferList) = Phpfox::getService('auction')->getOfferListOfAuction($iProductId, $aSort , $iPage, $iLimit);

        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iCnt));
        
        $this->template()->assign(array(
                'aAuction' => $aAuction,
                'aOfferList' => $aOfferList,
            ));

        $sTitle = $aAuction['name'];
        if (!empty($sTitle))
        {
            if (preg_match('/\{phrase var\=(.*)\}/i', $sTitle, $aMatches) && isset($aMatches[1]))
            {
                $sTitle = str_replace(array("'", '"', '&#039;'), '', $aMatches[1]);
                $sTitle = _p($sTitle);
            }
            
            $sTitle = Phpfox::getLib('url')->cleanTitle($sTitle);
        }

        $orgLink  = Phpfox::getLib('url')->makeUrl('auction.detail'.'.'.$iProductId.'.'.$sTitle.'.offerhistory'); 

        $this->template()->assign(
                array(
                    'orgLink' => $orgLink,
                    'sSort' => $sSortField.'_'.$sSortType,
                    'iPage' => $iPage
                    )
                );
    }
}

?>
