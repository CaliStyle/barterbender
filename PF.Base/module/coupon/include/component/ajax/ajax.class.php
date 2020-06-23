<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * @copyright      YouNet Company
 * @author         TienNPL, DatLV
 * @package        Module_Coupon
 * @version        3.01
 * 
 */

class Coupon_Component_Ajax_Ajax extends Phpfox_Ajax
{
    /**
     * @TODO: check to remove later
     * @return bool FALSE
     */
    public function deleteReview()
    {
        return false;
    }

    // this get email template for admincp

    public function fillEmailTemplate()
    {
        $iTypeId = $this->get('type_id');

        if (empty($iTypeId))
            $iTypeId = 0;

        $aEmail = Phpfox::getService('coupon.mail')->getEmailTemplate($iTypeId);

        $aEmail['email_template'] = str_replace('"', '\"', $aEmail['email_template']);

        $aEmail['email_subject'] = Phpfox::getLib('parse.output')->parse($aEmail['email_subject']);
        
        $this->call('$("#email_subject").val("'.$aEmail['email_subject'].'"); $("#email_template").val("'.$aEmail['email_template'].'")');
    }

    /**
     * Set featured|unfeatured a Coupon
     * @author TienNPL
     */
    public function updateFeatured()
    {
        // Get Params
        $iCouponId = (int)$this->get('iCouponId');
        $iIsFeatured = (int)$this->get('iIsFeatured');
        $iIsFeatured = (int)!$iIsFeatured;

        $oCouponProcess = Phpfox::getService('coupon.process');
        if ($iCouponId)
        {
            $oCouponProcess->feature($iCouponId, $iIsFeatured);
        }

        if ($iIsFeatured)
        {
            $sLabel = '<img src="'.Phpfox::getParam('core.path').'theme/adminpanel/default/style/default/image/misc/bullet_green.png" alt="">';
        }
        else
        {
            $sLabel = '<img src="'.Phpfox::getParam('core.path').'theme/adminpanel/default/style/default/image/misc/bullet_red.png" alt="">';
        }

        $this->html('#item_update_featured_'.$iCouponId, '<a href="javascript:void(0);" onclick="coupon.updateFeatured('.$iCouponId.','.$iIsFeatured.');"><div style="width:50px;">'.$sLabel.'</div></a>');
    }

    /**
     * publish in homepage
     * by : datlv
     */
    public function publish()
    {
        // Get Params
        $iCouponId = (int)$this->get('iCouponId');
        $iCouponStatus = (int)$this->get('iCouponStatus');
        $aCoupon = Phpfox::getService('coupon')->getCouponById($iCouponId);

        $sUrl = '';

        if ($iCouponStatus == Phpfox::getService('coupon')->getStatusCode('draft'))
        {
            $sUrl = Phpfox::getService('coupon.process')->pay($iCouponId);
            if ($sUrl === false)
            {
                // publish and/or feature
                if (isset($aCoupon['feature_coupon']))
                {
                    // publish and feature
                    Phpfox::getService('coupon.process')->publishForPaymentIsZero($iCouponId);
                    Phpfox::getService('coupon.process')->feature($iCouponId, 1);
                }
                else
                {
                    // publish
                    Phpfox::getService('coupon.process')->publishForPaymentIsZero($iCouponId);
                }

                $sUrl = '';
            }
			else {
				$sUrl = Phpfox::getLib('url')->makeUrl('coupon.payment', array($iCouponId), true);
			}
        }
        elseif ($iCouponStatus == Phpfox::getService('coupon')->getStatusCode('denied'))
        {
            $iTransactionId = Phpfox::getService('coupon.transaction')->getTransactionIdByCouponId($iCouponId);

            if (isset($iTransactionId) && (int)$iTransactionId > 0)
            {
                Phpfox::getService('coupon.process')->publish($iTransactionId);
            }
            else
            {
                Phpfox::getService('coupon.process')->publishForPaymentIsZero($iCouponId);
            }

        }

        $this->call('window.location = "'.$sUrl.'";');
    }

    /**
     *  Delete coupon in manage resume page in admin control pannel
     *  @author TienNPL
     */
    public function deleteCoupon()
    {
        $iCouponId = $this->get('iCouponId');
        Phpfox::getUserParam('admincp.has_admin_access', true);
        if ($iCouponId)
        {
            Phpfox::getService('coupon.process')->delete($iCouponId);
        }
        $this->call("window.location = window.location;");
    }

    /**
     *  Pause coupon in manage resume page in admin control pannel
     *  @author TienNPL
     */
    public function pauseCoupon()
    {
        $iCouponId = $this->get('iCouponId');
        Phpfox::getUserParam('admincp.has_admin_access', true);
        if ($iCouponId)
        {
            Phpfox::getService('coupon.process')->pause($iCouponId);
        }
        $this->call("window.location = window.location;");
    }

    /**
     *  Pause coupon in homepage page.
     * 
     */
    public function pauseOwnCoupon()
    {
        $OwnerId = Phpfox::getService("coupon")->getCouponOwnerId($this->get('item_id'));
        $canPause = ( Phpfox::getUserParam('coupon.can_pause_own_coupon')  && Phpfox::getUserId() == $OwnerId ) || Phpfox::isAdmin() ;
        
        if($canPause) {
            if (Phpfox::getService('coupon.process')->pause($this->get('item_id')))
            {
                    $this->call("$('#core_js_messages').message('"._p('coupon_pause_successfully', array('phpfox_squote' => true))."', 'valid').fadeOut(5000);");
            }
        }

        $this->call("setTimeout(function(){window.location = window.location;},1000);");

    }

    /**
     *  Resume coupon in manage resume page in admin control pannel
     *  @author TienNPL
     */
    public function resumeCoupon()
    {
        $iCouponId = $this->get('iCouponId');
        Phpfox::getUserParam('admincp.has_admin_access', true);
        if ($iCouponId)
        {
            Phpfox::getService('coupon.process')->resume($iCouponId);
        }
        $this->call("window.location = window.location;");
    }
        
    /**
     *  Resume coupon in homepage page.
     */
    public function resumeOwnCoupon()
    {
        $OwnerId = Phpfox::getService("coupon")->getCouponOwnerId($this->get('item_id'));
        $canResume = ( Phpfox::getUserParam('coupon.can_resume_own_coupon')  && Phpfox::getUserId() == $OwnerId ) || Phpfox::isAdmin() ;
        
        if($canResume) {
            if (Phpfox::getService('coupon.process')->resume($this->get('item_id')))
            {            
                 $this->call("$('#core_js_messages').message('"._p('coupon_resume_successfully', array('phpfox_squote' => true))."', 'valid').fadeOut(5000);");
            }
        }
        $this->call("setTimeout(function(){window.location = window.location;},1000);");
    }


    /**
     *  Close coupon in manage resume page in admin control pannel
     *  @author TienNPL
     */
    public function closeCoupon()
    {
        $iCouponId = $this->get('iCouponId');
        Phpfox::getUserParam('admincp.has_admin_access', true);
        if ($iCouponId)
        {
            Phpfox::getService('coupon.process')->close($iCouponId);
        }
        $this->call("window.location = window.location;");
    }


    /**
     *  Close coupon in homepage page.
     * 
     */
    public function closeOwnCoupon()
    {
        $OwnerId = Phpfox::getService("coupon")->getCouponOwnerId($this->get('item_id'));
        $canResume = ( Phpfox::getUserParam('coupon.can_close_own_coupon')  && Phpfox::getUserId() == $OwnerId ) || Phpfox::isAdmin() ;
        
        if($canResume) {
            if (Phpfox::getService('coupon.process')->close($this->get('item_id')))
            {            
                 $this->call("$('#core_js_messages').message('"._p('coupon_closed_successfully', array('phpfox_squote' => true))."', 'valid').fadeOut(5000);");
            }
        }
        $this->call("setTimeout(function(){window.location = window.location;},1000);");
    }

    /**
     *  Deny coupon in manage resume page in admin control pannel
     *  @author TienNPL
     */
    public function denyCoupon()
    {
        $iCouponId = $this->get('iCouponId');
        Phpfox::getUserParam('admincp.has_admin_access', true);
        if ($iCouponId)
        {
            Phpfox::getService('coupon.process')->deny($iCouponId);
        }
        $this->call("window.location = window.location;");
    }
    /**
     *  Approve coupon in manage resume page in admin control pannel
     *  @author TienNPL
     */
    public function approveCoupon()
    {
        $iCouponId = $this->get('iCouponId');
        Phpfox::getUserParam('admincp.has_admin_access', true);
        if ($iCouponId)
        {
            Phpfox::getService('coupon.process')->approve($iCouponId);
        }
        $this->call("window.location = window.location;");
    }
    /**
     * Add favorite
     * @author TienNPL
     */
    public function addFavorite()
    {
        $iId = (int)$this->get('id');
        Phpfox::getBlock('coupon.add-favorite', array('iId' => $iId));
    }

    /**
     * Delete favorite
     * @author TienNPL
     */
    public function deleteFavorite()
    {
        $iItem = (int)$this->get('id');

        $iFavoriteId = phpfox::getLib('database')->select('favorite_id')->from(phpfox::getT('coupon_favorite'))->where("coupon_id = {$iItem} and user_id =".phpfox::getUserId())->execute('getSlaveField');

        if ($iFavoriteId)
        {
            Phpfox::getService('coupon.process')->deleteFavorite($iFavoriteId);
        }

        (($sPlugin = Phpfox_Plugin::get('coupon.component_ajax_deletefavorite_end')) ? eval($sPlugin) : false);

        $this->alert(_p('the_coupon_had_been_removed_from_your_favorite_list'));
    }

    /**
     * Add follow
     * @author TienNPL
     */
    public function addFollow()
    { 
        $iItem = (int)$this->get('id');
        Phpfox::getBlock('coupon.add-follow', array('iId' => $iItem));

    }

    /**
     * Delete follow
     * @author TienNPL
     */
    public function deleteFollow()
    {
        $iItem = (int)$this->get('id');

        $iFollowId = phpfox::getLib('database')->select('follow_id')->from(phpfox::getT('coupon_follow'))->where("coupon_id = {$iItem} and user_id =".phpfox::getUserId())->execute('getSlaveField');

        if ($iFollowId)
        {
            Phpfox::getService('coupon.process')->deleteFollow($iFollowId);
        }

        $this->alert(_p('the_coupon_had_been_removed_from_your_following_list'));
    }

    /**
     * Invite friends
     * @author TienNPL
     */
    public function inviteBlock()
    {
        Phpfox::getBlock('coupon.form-invite-friend', array(
            'id' => $this->get('id'),
            'url' => $this->get('url'),
            ));
        $this->setTitle(_p('sign_this_coupon'));

        $this->call('<script>$Core.loadInit();</script>');
    }

    /**
     * Get Term And Condition
     */
    public function getTermAndCondition()
    {
        $iCouponId = (int)$this->get('iCouponId');
        Phpfox::getBlock('coupon.term-and-condition', array('iId' => $iCouponId));
    }

    /**
     * Get Coupon Code
     */
    public function getCode()
    {
        $iCouponId = (int)$this->get('coupon_id');
        $oCouponService = Phpfox::getService('coupon');
        $oCouponProcess = Phpfox::getService('coupon.process');

        $aCoupon = $oCouponService->getCouponById($iCouponId);

        if (!$aCoupon)
        {
            return false;
        }

        if ($aCoupon['is_closed'])
        {
            $this->call('tb_remove();$(".ync_code_info").hide();alert("'._p('this_coupon_had_been_closed').'")');
            return false;
        }

        $sCode = "";
        if (isset($aCoupon['code_setting']))
        {
            $sCode = $aCoupon['code_setting'];
        }
        else
        {
            $sCode = $oCouponService->generateCode();
            $bExisted = $oCouponService->checkCode($sCode);

            while ($bExisted)
            {
                $sCode = $oCouponService->generateCode();
                $bExisted = $oCouponService->checkCode($sCode);
            }
        }

        $oCouponProcess->addClaim($aCoupon['coupon_id'], $sCode);
		
		$aCoupon = $oCouponService->getCouponById($iCouponId);
		
		$sRemain = _p("unlimited_remain");
		if($aCoupon['quantity'] > 0)
		{
			$sRemain = $aCoupon['quantity'] - $aCoupon['total_claim'];
			if($sRemain < 0)
			{
				$sRemain = 0;
			}
			
			$sRemain = $sRemain . " " . _p("claims_remain");
		}
		
		$iPercent = 100;
		if($aCoupon['quantity'] > 0)
		{
			$iPercent = $aCoupon['total_claim']/$aCoupon['quantity']*100;	
		}

        $this->html('#coupon_code_display', $sCode);
        $this->html('.ync_claim_sum .ync_value', $aCoupon['total_claim']);
		$this->html('.ync_claim .ync_claim_remain', $sRemain);
        $this->call('tb_remove();$(".ync_claim_active").css("width", "'.$iPercent.'%");$("#coupon_code_display").show();$("#coupon_code_button").hide();$("#coupon_print_button").show();');
    }

    /**
     * @by : datlv
     * @TODO: will improve in next version
     */
    public function moderation()
    {
        Phpfox::isUser(true);

        switch ($this->get('action'))
        {
            case 'approve':
                foreach ((array )$this->get('item_moderate') as $iId)
                {
                	$aCoupon = Phpfox::getService('coupon')->getCouponById($iId);
                    $canApprove =   ( Phpfox::getUserParam('coupon.can_approve_coupon')  && Phpfox::isAdmin() )  &&
		        				 (in_array($aCoupon['status'], array(	
														        		Phpfox::getService('coupon')->getStatusCode('pending')
														        		 ) ) ) ;
			         if($canApprove) {
	                    Phpfox::getService('coupon.process')->approve($iId);
	                    $this->remove('#js_coupon_entry'.$iId);
                     }
                }
                $this->updateCount();
                $sMessage = _p('coupon_s_successfully_approved');
                break;
            case 'deny':
                Phpfox::getUserParam('coupon.can_approve_coupon', true);
                foreach ((array )$this->get('item_moderate') as $iId)
                {
                	$aCoupon = Phpfox::getService('coupon')->getCouponById($iId);
                    $canDeny =   ( Phpfox::getUserParam('coupon.can_approve_coupon')  && Phpfox::isAdmin() )  &&
		        				 (in_array($aCoupon['status'], array(	
														        		Phpfox::getService('coupon')->getStatusCode('pending')
														        		 ) ) ) ;
			        if($canDeny) {
	                    Phpfox::getService('coupon.process')->deny($iId);
	                    $this->remove('#js_coupon_entry'.$iId);
                    }

                }
                $this->updateCount();
                $sMessage = _p('coupon_s_successfully_denied');
                break;
            case 'delete':
                Phpfox::getUserParam('coupon.can_delete_own_coupon', true);
                foreach ((array )$this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('coupon.process')->delete($iId);
                    $this->slideUp('#js_coupon_entry'.$iId);
                }
                $sMessage = _p('coupon_s_successfully_deleted');
                break;
            case 'pause':

				foreach ((array) $this->get('item_moderate') as $iId)
				{

            		$aCoupon = Phpfox::getService('coupon')->getCouponById($iId);
                    $canPause =  ( ( Phpfox::getUserParam('coupon.can_pause_own_coupon')  && Phpfox::getUserId() == $aCoupon['user_id'] ) ) &&
		        				 (in_array($aCoupon['status'], array(	
														        		Phpfox::getService('coupon')->getStatusCode('running'),
														        		Phpfox::getService('coupon')->getStatusCode('upcoming'),
														        		Phpfox::getService('coupon')->getStatusCode('endingsoon'),
														        		Phpfox::getService('coupon')->getStatusCode('pause')
														        		 ) ) ) ;
			         if($canPause) {
			            if (Phpfox::getService('coupon.process')->pause($iId))
			            {
			            }
			         }

				}
				$this->updateCount();				
				$sMessage = _p('coupon_pause_successfully');
				break;
			case 'resume':
				foreach ((array) $this->get('item_moderate') as $iId)
				{
            	  $aCoupon = Phpfox::getService('coupon')->getCouponById($iId);
                  $canResume = (( Phpfox::getUserParam('coupon.can_resume_own_coupon')  && Phpfox::getUserId() == $aCoupon['user_id'] ) ) &&
		        				 (in_array($aCoupon['status'], array(	Phpfox::getService('coupon')->getStatusCode('pause')
														        		 ) ) ) ;
		         if($canResume) {
		            if (Phpfox::getService('coupon.process')->resume($iId))
		            {
		            }
		         }

				}
				$this->updateCount();				
				$sMessage = _p('coupon_resume_successfully');
				break;
			case 'close':
				foreach ((array) $this->get('item_moderate') as $iId)
				{


            	  $aCoupon = Phpfox::getService('coupon')->getCouponById($iId);
                  $canClose =  ( ( Phpfox::getUserParam('coupon.can_close_own_coupon')  && Phpfox::getUserId() == $aCoupon['user_id'] ) ) &&
		        				 (in_array($aCoupon['status'], array(	
		        				 									Phpfox::getService('coupon')->getStatusCode('running'),
						        									Phpfox::getService('coupon')->getStatusCode('upcoming'),
						        									Phpfox::getService('coupon')->getStatusCode('pause'),
						        									Phpfox::getService('coupon')->getStatusCode('endingsoon'),
						        									Phpfox::getService('coupon')->getStatusCode('pending'),
						        									Phpfox::getService('coupon')->getStatusCode('closed')
														        		 ) ) ) ;
		         if($canClose) {
		            if (Phpfox::getService('coupon.process')->close($iId))
		            {

		            }
		         }

				}
				$this->updateCount();				
				$sMessage = _p('coupon_close_successfully');
				break;
        }

        $this->alert($sMessage);
        $this->call('setTimeout(function(){$Core.reloadPage();},2000);');
    }

    /**
     * feature/un feature in front_end , detail ....
     * by : datlv
     */
    public function feature()
    {
        $iCouponId = $this->get('iCouponId');
        $iFeatured = $this->get('iFeatured') ? 1 : 0;

        Phpfox::getService('coupon.process')->feature($iCouponId, $iFeatured);

        if (!$iFeatured)
            $this->call('$("#js_coupon_feature_'.$iCouponId.'").show(); $("#js_coupon_unfeature_'.$iCouponId.'").hide();');
        else {
            $this->call('$("#js_coupon_unfeature_'.$iCouponId.'").show(); $("#js_coupon_feature_'.$iCouponId.'").hide();');
        }
        $this->call("$('#core_js_messages').message('".($iFeatured ? _p('coupon_successfully_featured') : _p('coupon_successfully_un_featured'))."', 'valid').fadeOut(5000);");
        $this->call("setTimeout(function(){window.location = window.location;},1000);");
    }

    /**
     * pay for feature
     */
    public function payFeature()
    {
        $iCouponId = $this->get('iCouponId');
		

        $Url = Phpfox::getService('coupon.process')->pay($iCouponId, 2);
		
        if ($Url === false)
        {
            Phpfox::getService('coupon.process')->feature($iCouponId, 1);
        }
		else {
			$Url = Phpfox::getLib('url')->makeUrl('coupon.payment', array($iCouponId, '2'), true);
		}
		
        $this->call("window.location = '".$Url."'");
    }

    //delete in homepage
    public function inlineDelete()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('coupon.process')->delete($this->get('item_id')))
        {
            if (Phpfox::getLib('module')->getFullControllerName() == "core.detail")
                $this->call('window.location = "'.Phpfox::getLib('url')->makeUrl('coupon').'"');
            else
                $this->call("$('#js_coupon_entry".$this->get('item_id')."').hide('slow'); $('#core_js_messages').message('"._p('coupon_deleted', array('phpfox_squote' => true))."', 'valid').fadeOut(5000);");
        }

    }
    
    public function blockThemes()
    {
        $id = $this->get('id');
        Phpfox::getBlock('coupon.themes', array('id' => $id));
    }
    
    public function blockPreview()
    {
        $iId = $this->get('id');
        $aVals = $this->get('val');
        
        if (!empty($iId))
        {
            Phpfox::getBlock('coupon.preview', array('id' => $iId));
        }
        elseif (($sMsg = Phpfox::getService('coupon.template.process')->verify($aVals)) !== true)
        {
            echo $sMsg;
        }
        else
        {
            Phpfox::getBlock('coupon.preview', array('val' => $aVals));
        }
    }

    public function AdminAddCustomFieldBackEnd()
    {
        Phpfox::getComponent('coupon.admincp.customfield.add', array(), 'controller');
    }

    public function addField()
    {
        $aVals = $this->get('val');

        if (isset($aVals['option']))
        {
            // $sKey == the language phrase
            foreach ($aVals['option'] as $sKey => $aPhrases)
            {
                foreach ($aPhrases as $sLang => $aValue)
                {
                    if (!empty($aPhrases['en']['text']) && empty($aValue['text'])) {
                        $this->call("$('#js_add_field_loading').hide();");
                        $this->call("$('#js_add_field_button').attr('disabled', false);");

                        return $this->alert((_p('Provide a "{{ language_name }}" name.', ['language_name' => $sLang])));
                    }
                }
            }
        }
        
        list($iFieldId, $aOptions) = Phpfox::getService('coupon.custom.process')->add($aVals);
        if(!empty($iFieldId))
        {
            $aFields = Phpfox::getService('coupon.custom')->getCustomField();
            $this->call('tb_remove();');
        }
        
        $this->call("$('#js_add_field_loading').hide();");
        $this->call("$('#js_add_field_button').attr('disabled', false);");

        $this->call("window.location.href = window.location.href");
    }


    public function updateField()
    {
        $aVals = $this->get('val');

        if (isset($aVals['current']))
        {
            // $sKey == the language phrase
            foreach ($aVals['current'] as $sKey => $aPhrases)
            {
                if (strpos($sKey,'.') === false)
                {
                    continue;
                }
                foreach ($aPhrases as $sLang => $aValue)
                {
                    if (!empty($aPhrases['en']['text']) && empty($aValue['text'])) {
                        $this->call("$('#js_add_field_loading').hide();");
                        $this->call("$('#js_add_field_button').attr('disabled', false);");

                        return $this->alert((_p('Provide a "{{ language_name }}" name.', ['language_name' => $sLang])));
                    }
                }
            }
        }

        if(Phpfox::getService('coupon.custom.process')->update($aVals['id'], $aVals))
        {
            $aFields = Phpfox::getService('coupon.custom')->getCustomField();
            $this->call('tb_remove();');
        }
        
        $this->call("$('#js_add_field_loading').hide();");
        $this->call("$('#js_add_field_button').attr('disabled', false);");

        $this->call("window.location.href = window.location.href");
    }

    public function deleteField()
    {
        $id = $this->get('id');
        if (Phpfox::getService('coupon.custom.process')->delete($id))
        {
            $this->remove('#js_custom_field_'.$id);
        }
    }
    
    public function deleteOption()
    {
        $id = $this->get('id');

        if (Phpfox::getService('coupon.custom.process')->deleteOption($id))
        {
            $aFields = Phpfox::getService('coupon.custom')->getCustomField();
            $this->remove('#js_current_value_'.$id);
        }
        else
        {
            $this->alert(_p('could_not_delete'));
        }
    }
    public function updateActivity()
    {
        if (Phpfox::getService('coupon.category.process')->updateActivity($this->get('id'), $this->get('active')))
        {

        }
    }

    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'coupon_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove('coupon_category', 'substr');
    }


}

?>