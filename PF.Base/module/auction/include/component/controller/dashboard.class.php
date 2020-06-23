<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Dashboard extends Phpfox_Component
{


	public function process()
	{
            /*redirect to appropriate controller*/
            $id = $this->request()->getInt('id');
            Phpfox::getService('auction.helper')->buildMenu();
            if(!(int)$id){
	               $this->url()->send('auction');
            }
            // check permission 
	     /*   if( 
	        	!Phpfox::getService('auction.permission')->canEditBusiness($aAuction['user_id'],$id)
	          ){
	                $this->url()->send('subscribe');
	          }*/

			$this->url()->send("auction.edit",array('id' => $id),'');
	  

    }

    public function clean()
    {
        
    }

}
?>