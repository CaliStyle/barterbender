<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Offer_List extends Phpfox_Component
{
    private $_sModule = false;
    private $_iItem = false;
    private function _checkIsInPageAndPagePermission() {
        //will check later

        if ($this->_sModule !== false && $this->_iItem !== false) {

            switch ($this->_sModule) {
                case 'pages':
                    $this->_aCallback = Phpfox::callback('auction.getAuctionsDetails', array('item_id' => $this->_iItem));
                    break;

                default:
                    $this->_aCallback = Phpfox::callback($this->_sModule . '.getAuctionsDetails', array('item_id' => $this->_iItem));
                    break;
            }

            if ($this->_aCallback) {
                $this->template()->setBreadcrumb($this->_aCallback['breadcrumb_title'], $this->_aCallback['breadcrumb_home']);
                $this->template()->setBreadcrumb($this->_aCallback['title'], $this->_aCallback['url_home'])
                    ->setBreadCrumb(_p('auction'),$this->_aCallback['url_home'].'auction/');
                if ($this->_sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($this->_iItem, 'auction.share_auctions')) {
                    return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings '));
                }
            }
        }
    }
	public function process()
	{
		Phpfox::getService('auction.helper')->buildMenu();
        $iProductId = 0;
        if ($this->request()->getInt('id')) {
            $iProductId = $this->request()->getInt('id');
            $this->setParam('iProductId',$iProductId);
        }

        if(!(int)$iProductId){
                   $this->url()->send('auction');
        }
        $aAuction = Phpfox::getService('auction')->getQuickAuctionByProductId($iProductId);

        if ($aAuction && $aAuction['module_id'] != 'auction') {
            $this->_sModule = $aAuction['module_id'];
            $this->_iItem = $aAuction['item_id'];
        }
        $this->_checkIsInPageAndPagePermission();
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
        $iPage = $this->request()->getInt('page') != '' ? $this->request()->getInt('page') : 1;
        $iLimit = 10;
        list($iCnt, $aOfferList) = Phpfox::getService('auction')->getOfferListOfAuction($iProductId, $aSort , $iPage, $iLimit);

        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iCnt));
        
        $this->template()->assign(array(
                'aAuction' => $aAuction,
                'aOfferList' => $aOfferList,
                'iPage' => $iPage
            ));

        $orgLink = Phpfox::getLib('url')->makeUrl('auction.offer-list',array('id'=>$iProductId,'page'=>$iPage));
        if($this->_sModule == 'auction' || $this->_sModule == false)
        {
            $this->template()
                ->setBreadcrumb(_p('module_menu'),$this->url()->makeUrl('auction'));
        }
        $this->template()
                ->setPhrase(array(
                ))
                ->assign(
                    array(
                        'orgLink' => $orgLink,
                        'sSort' => $sSortField.'_'.$sSortType,
                        )
                    )
                ->setHeader('cache', array(
                    'pager.css' => 'style_css',
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'switch_legend.js' => 'static_script',
                    'switch_menu.js' => 'static_script',
                    'quick_edit.js' => 'static_script',
                    'progress.js' => 'static_script',
                    'share.js' => 'module_attachment',
                    'country.js' => 'module_core',
                    'jquery/ui.js' => 'static_script',
                )); 

        $this->template()->setBreadcrumb($aAuction['name'], $this->url()->permalink('auction.detail', $iProductId));
        $this->template()->setBreadcrumb(_p('offer_list'), $this->url()->permalink('auction.offer-list','id_'.$iProductId),true);


        Phpfox::getService('auction.helper')->loadAuctionJsCss();


	}
}
?>