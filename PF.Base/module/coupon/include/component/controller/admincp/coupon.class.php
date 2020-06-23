<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Coupon
 * @version        3.01
 * 
 */
class Coupon_Component_Controller_Admincp_Coupon extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
    		// Service Coupon
			$oCoupon = Phpfox::getService("coupon");
            
			// Page Number & Limit Per Page
            $iPage = $this->request()->getInt('page');
			$iPageSize = 10;
			
			// Variables
			$aVals = array();
			$aConds = array("c.is_removed = 0");
			$aCountries = $oCoupon->getCountries();
			$aCategoryOptions = Phpfox::getService('coupon.category')->display('option')->get();
			
			// Search Filter
            $oSearch 	= Phpfox::getLib('search')->set(array(
					'type' 	 => 'request',
					'search' => 'search',
				  ));
				  
			$aVals['title'] 		= $oSearch->get('title');	  
			$aVals['username'] 		= $oSearch->get('username');
			$aVals['country_iso'] 	= $oSearch->get('country_iso');
			$aVals['category_id'] 	= $oSearch->get('category_id');
			$aVals['status'] 		= $oSearch->get('status');
			$aVals['feature'] 		= $oSearch->get('feature');
			$aVals['submit'] 		= $oSearch->get('submit');
			$aVals['reset'] 		= $oSearch->get('reset');
			
			if(empty($aVals['feature']))
			{
				$aVals['feature'] = "all";
			}
            if($aVals['reset'])
			{
				$this->url()->send('admincp.coupon.coupon');
			}
			if($aVals['title'])
			{
				$aConds[] = "AND c.title like '%{$aVals['title']}%'";
			}
			if($aVals['username'])
			{
				$aConds[] = "AND u.full_name like '%{$aVals['username']}%'";
			}
			if($aVals['category_id'])
			{
				$aConds[] = "AND c.category_id = {$aVals['category_id']}";
			}
			if($aVals['country_iso'])
			{
				$aConds[] = "AND c.country_iso = '{$aVals['country_iso']}'";
			}
			if($aVals['status'])
			{
				$iMode = $oCoupon->getStatusCode($aVals['status']);
				$aConds[] = "AND c.status = {$iMode}";
			}
			if($aVals['feature'] == "1")
			{
				$aConds[] = "AND c.is_featured = 1";
			}
			elseif($aVals['feature'] == "2")
			{
				$aConds[] = "AND c.is_featured = 0";
			}
			
			// Delete selected resumes
			$aAction = $this->request()->get('val');
		 	if($aAction)
			{
				if(isset($aAction['delete_selected']) && $aAction['delete_selected'] == _p('delete_selected'))
				{
					foreach($this->request()->getArray('coupon_row') as $iId){
						Phpfox::getService('coupon.process')->delete($iId);
					}
					Phpfox::getLib('url')->send('admincp.coupon.coupon',array(),_p("the_selected_coupons_had_been_deleted_successfully"));
				}
				if(isset($aAction['approve_selected']) && $aAction['approve_selected'] == _p('approve_selected'))
				{
					foreach($this->request()->getArray('coupon_row') as $iId){
						Phpfox::getService('coupon.process')->approve($iId, FALSE);
					}
					Phpfox::getLib('url')->send('admincp.coupon.coupon',array(),_p("the_selected_coupons_had_been_approved"));
				}
			}
		
			// Set pager
			$iCount = $oCoupon->getItemCountForManage($aConds);
				
			phpFox::getLib('pager')->set(array(
						'page'  => $iPage, 
						'size'  => $iPageSize, 
						'count' => $iCount
			));
			
			// Get Coupons list
			$aCoupons = $oCoupon->getCouponsForManage($aConds, 'c.time_stamp DESC', $iPage, $iPageSize, $iCount);
            foreach($aCoupons as $k=>$aCoupon)
            {
                if($aCoupon['module_id'] != 'pages')
                {
                    $aCoupons[$k]['edit_url'] = $this->url()->makeUrl('coupon.add', array('id' => $aCoupon['coupon_id'], 'title' => Phpfox::getLib('parse.input')->cleanTitle($aCoupon['title'])));
                }
                else
                {
                    $aCoupons[$k]['edit_url'] = $this->url()->makeUrl('coupon.add', array('id' => $aCoupon['coupon_id'], 'module' => 'pages', 'item' => $aCoupon['item_id']));
                }
            }
			
			// Set title, breadcrumb and assign variable
			$this->template()->setTitle(_p('manage_coupons'));
			
			$this->template()
                ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
                ->setBreadCrumb(_p('module_coupon'), $this->url()->makeUrl('admincp.app').'?id=__module_coupon')
                ->setBreadcrumb(_p('manage_coupons'), $this->url()->makeUrl('admincp.coupon.coupon'));
			
            $this->template()->assign(array(
                        'aForms'			=> $aVals,
                        'aCountries'		=> $aCountries,
                        'aCategoryOptions'  => $aCategoryOptions,
                        'aCoupons'			=> $aCoupons
                    ));
			
			$iSelectedCatId = $aVals['category_id']?$aVals['category_id']:0;
            $this->template()->setHeader(array(
            	'coupon_backend.css' => 'module_coupon',
            	'manage.js'			 => 'module_coupon',
				'<script type="text/javascript">$Behavior.searchCouponByCategory = function(){var selectedId ='. $iSelectedCatId .';if(selectedId > 0){$(\'#js_mp_category_item_\' + selectedId).attr(\'selected\',true);}};</script>'
            ));
			
			$this->template()->setPhrase(array(
				'coupon.are_you_sure_you_want_to_delete_this_coupon',
				'coupon.are_you_sure_you_want_to_pause_this_coupon',
				'coupon.are_you_sure_you_want_to_resume_this_coupon',
				'coupon.are_you_sure_you_want_to_approve_this_coupon',
				'coupon.are_you_sure_you_want_to_deny_this_coupon',
				'coupon.are_you_sure_you_want_to_close_this_coupon'				
			));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
            (($sPlugin = Phpfox_Plugin::get('coupon.component_controller_admincp_coupon_clean')) ? eval($sPlugin) : false);
    }
}

?>