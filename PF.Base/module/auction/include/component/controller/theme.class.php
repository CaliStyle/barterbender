<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Theme extends Phpfox_Component
{


	public function process()
	{
		Phpfox::getService('auction.helper')->buildMenu();
        $iEditedAuctionId = 0;
        if ($this->request()->getInt('id')) {

            $iProductId = $this->request()->getInt('id');
            $aAuction = Phpfox::getService('auction')->getQuickAuctionByProductId($iProductId);        
            $iEditedAuctionId = $aAuction['auction_id'];
            $this->setParam('iAuctionId',$aAuction['auction_id']);
        }

        if(!(int)$iEditedAuctionId){
                $this->url()->send('auction');
        }

        if ($aVals = $this->request()->getArray('val'))
        {
            if(isset($aVals['apply_theme'])){
                  
                if(Phpfox::getService('ecommerce.process')->updateThemeForEcommerce($aVals)){
                   $this->url()->send("auction.theme",array('id' => $iProductId),_p('manage_themes_updated_successfully'));
                }
            }
        }
        $this->template()
                ->setBreadcrumb(_p('module_menu'),$this->url()->makeUrl('auction'))
                ->setEditor()
                ->setPhrase(array(
                ))
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

        $this->template()->assign(array(
            'iAuctionId'   => $iEditedAuctionId,
            'iProductId'   => $aAuction['product_id'],
            'aAuction'        => $aAuction,
            'aForms'        => $aAuction,
            'core_path'     => Phpfox::getParam('core.path')
        ));  
        
        $this->template()->setBreadcrumb($aAuction['name'], $this->url()->permalink('auction.detail', $aAuction['product_id']));
        $this->template()->setBreadcrumb(_p('themes'), $this->url()->permalink('auction.theme','id_'.$aAuction['product_id']));


        Phpfox::getService('auction.helper')->loadauctionJsCss();

    }

    public function clean()
    {
        
    }

}
?>