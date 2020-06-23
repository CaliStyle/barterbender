<?php

/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 11:05
 */

namespace Apps\YNC_Affiliate\Ajax;

use Phpfox;
use Phpfox_Ajax;

class Ajax extends Phpfox_Ajax
{
    public function deleteAffiliate()
    {
        $iAffiliateId = (int)$this->get('iAffiliateId');
        if ($iAffiliateId) {
            if (Phpfox::getService('yncaffiliate.affiliate.process')->deleteAffiliate($iAffiliateId)) {
                $this->alert(_p('Affiliate successfully deleted.'));
                $this->call('{setTimeout(function(){window.location.href=window.location.href},2000);}');
            } else {
                $this->alert(_p('You do not have permission to delete this affiliate.'));
            }
        }
    }

    public function actionMultiSelectAffiliate()
    {
        $aVals = $this->get('affiliate_row');
        $aType = $this->get('val');
        if (!count($aVals)) {
            $this->alert(_p('No Affiliate Selected'));
            return false;
        }

        $oProcess = Phpfox::getService('yncaffiliate.affiliate.process');

        if ($aType['selected']) {
            switch ($aType['selected']) {
                case '1':
                    $success = false;
                    foreach ($aVals as $key => $affiliateId) {
                        $sResult = $oProcess->deleteAffiliate($affiliateId);
                        if (!$sResult) {
                            $success = false;
                            $this->alert(_p('You do not have permission to detele affiliate.'));
                            continue;
                        } else {
                            $success = true;
                        }
                    }
                    if ($success) {
                        $this->alert(_p('Affiliate(s) successfully delete.'));
                        $this->call('{setTimeout(function(){window.location.href=window.location.href},2000);}');
                    } else {
                        $this->alert(_p('Delete failed.'));
                    }
                    break;
                case '2':
                    foreach ($aVals as $key => $affiliateId) {
                        $sResult = $oProcess->approvedAffiliate($affiliateId, 1);
                        if(!$sResult)
                        {
                            $this->alert(_p('You do not have permission to approve affiliate.'));
                            continue;
                        }
                        else
                        {
                            $this->alert(_p('Affiliate(s) successfully approve.'));
                            $this->call('{setTimeout(function(){window.location.href=window.location.href},2000);}');
                        }
                    }
                    break;
                case '3':
                    foreach ($aVals as $key => $affiliateId) {
                        $sResult = $oProcess->approvedAffiliate($affiliateId, 3);
                        if(!$sResult)
                        {
                            $this->alert(_p('You do not have permission to deny affiliate.'));
                            continue;
                        }
                        else
                        {
                            $this->alert(_p('Affiliate(s) successfully deny.'));
                            $this->call('{setTimeout(function(){window.location.href=window.location.href},2000);}');
                        }
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }
    }

    public function updateRuleStatus()
    {
        if (Phpfox::getService('yncaffiliate.commissionrule.process')->updateRuleStatus($this->get('id'), $this->get('active'))) {

        }
    }

    public function getCommissionRuleByUserGroup()
    {
        $iUserGroupId = $this->get('iUserGroupId');

        Phpfox::getComponent('yncaffiliate.admincp.commission-rule', array('iUserGroupId' => $iUserGroupId), 'controller');
        $this->html('#commission-rule-content', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }
    public function getTermAndService()
    {
        $sTerm  = setting('yncaffiliate.ynaf_term_of_service_title','');
        $sContent = setting('yncaffiliate.ynaf_term_of_service_content','');
        if(!$sTerm && !$sContent)
        {
            echo _p('no_terms_of_service_found');
            return false;
        }
        $sHtml = '<div class="table form-group">';
        $sHtml .= '<label>'.$sTerm.'</label><br/>';
        $sHtml .= '<textarea class="form-control" cols="50" rows="20" readonly>'.$sContent.'</textarea>';
        $sHtml .= '</div>';
        echo $sHtml;

    }
    public function getAffiliateLink()
    {
        $sLink = $this->get('link');
        $sCorePath = Phpfox::getParam('core.path');
        $sAffLink = '';
        if(strpos($sLink,$sCorePath) !== false)
        {
            $sAffLink = Phpfox::getService('yncaffiliate.link')->getAffiliateUrl(Phpfox::getUserId(),$sLink,true);
        }
        echo $sAffLink;
        return true;
    }
    public function updateMaterialStatus()
    {
        if (Phpfox::getService('yncaffiliate.materials.process')->updateStatus($this->get('id'), $this->get('active'))) {

        }
    }
    public function loadMoreClient()
    {
        $aVal = $this->getAll();
        $aClients = Phpfox::getService('yncaffiliate.affiliate.affiliate')->getClient($aVal['iUserId'],$aVal['iLevel'] - 1,$aVal['iLastAssocId']);

        $sHtml = Phpfox::getService('yncaffiliate.affiliate.affiliate')->getTree($aClients,$aVal['iMaxLevel'],$aVal['iTotalDirect'],$aVal['iSearchUserId'],$aVal['iUserId'],$aVal['iLastAssocId'],$aVal['iLevel'],$aVal['iLoadedClient'] + count($aClients));
        if($aVal['iOverWriteLayout'])
        {
            $this->call('$(\'#ynaf_client_container\').html(\''.$sHtml.'\');');
        }
        else{
            $this->call('$(\'#ynaf_loadmore_'.$aVal['iLastUserId'].'\').closest(\'ul\').append(\''.$sHtml.'\');');
            $this->call('$(\'#ynaf_loadmore_'.$aVal['iLastUserId'].'\').closest(\'li\').remove();');
        }

        $this->call('initAffiliateExplain();');
    }
    public function searchClient()
    {
        $data = [];
        $sText = $this->get('text');
        if(!empty($sText)){
            $aResults = Phpfox::getService('yncaffiliate.affiliate.affiliate')->searchUser($sText);
            if($aResults)
            {
                foreach ($aResults as $aResult)
                {
                    $data[] = [
                        'id' => $aResult['user_id'],
                        'label' => $aResult['full_name'],
                        'icon' => Phpfox::getLib('phpfox.image.helper')->display(['user'=> $aResult,'suffix' => '_20_square','no_link' => true]),
                    ];
                }
            }
        }
        echo json_encode($data);
        return;

    }
    public function searchClientTree()
    {
        $iSearchUserId = $this->get('iUserId');
        if(!$iSearchUserId)
        {
            return false;
        }
        $aClients = Phpfox::getService('yncaffiliate.affiliate.affiliate')->getClient(null,0,0,$iSearchUserId,1);
        $iMaxLevel = setting('ynaf_number_commission_levels');
        $sHtml = Phpfox::getService('yncaffiliate.affiliate.affiliate')->getTree($aClients,$iMaxLevel,0,$iSearchUserId,Phpfox::getUserId(),0,0,count($aClients));
        $this->call('$(\'#ynaf_client_container\').html(\''.$sHtml.'\');');
        $this->call('$(\'.yncaffiliate_level_item\').addClass(\'yncaffiliate_item_more_explain\');$(\'.yncaffiliate_level_items_more\').css(\'display\',\'block\');');
        $this->call('initAffiliateExplain();');
    }
    public function getRequestMoneyForm()
    {
        Phpfox::isUser(true);

        $this->setTitle(_p('request_money'));

        Phpfox::getBlock('yncaffiliate.request-money-form');
    }
    public function addRequestMoney()
    {
        Phpfox::isUser(true);

        $aVals = $this->get('val');

        $fMax = (float) $aVals['maximum'];
        $fMin = (float) setting('ynaf_minimum_request_points','1');
        if($fMin <= 0)
        {
            $fMin = 1;
        }
        $sDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
        if (!isset($aVals['amount']) || !is_numeric($aVals['amount']))
        {
            \Phpfox_Error::set(_p('your_amount_is_not_valid'));
        }

        if (isset($aVals['amount']) && ($aVals['amount'] < $fMin || $aVals['amount'] > $fMax))
        {
            $sCurrencySymbol = Phpfox::getService('core.currency')->getSymbol($sDefaultCurrency);

            \Phpfox_Error::set(_p('your_request_has_to_be_between_maximum_and_minimum', array('maximum' => $fMax, 'minimum' => $fMin)));
        }

        if (!isset($aVals['reason']) || Phpfox::getLib('parse.format')->isEmpty($aVals['reason']))
        {
            \Phpfox_Error::set(_p('please_fill_your_message'));
        }
        $aVals['currency'] = $sDefaultCurrency;
        if (!\Phpfox_Error::isPassed())
        {
            $this->call('$("#request_money_submit").prop("disabled", false);');
            $this->call('$(".add_request_loading").hide();');
            $this->errorSet('.request_error_message');
            return;
        }
        if($iRequest = Phpfox::getService('yncaffiliate.request.process')->add($aVals))
        {
            $this->call('setTimeout(function(){window.location.href = window.location.href}, 100);');
        }
        else{
            $this->alert(_p('something_went_wrong_please_try_again'));
            $this->call('$(\'#request_money_submit\').removeProp("disabled");');
        }
    }
    public function getCharts()
    {
        $aVals = $this->get('val');
        $iUserId = isset($aVals['user_id']) ? $aVals['user_id'] : 0;
        $sLabel = $aVals['labeling'];
        $sStatus = $aVals['status'];
        $sData = $aVals['data'];
        $sGroupBy = $aVals['groupby'];
        $sFromDatePicker = $this->get('js_from__datepicker');
        $sToDatePicker = $this->get('js_to__datepicker');
        if (empty($sFromDatePicker))
        {
            $this->call("$('#ynaff_loading').hide();");
            $this->call("$('#yncaffiliate-chart-holder').show();");
            \Phpfox_Error::set(_p('from_date_is_not_valid'));
        }
        if (empty($sToDatePicker))
        {
            $this->call("$('#ynaff_loading').hide();");
            $this->call("$('#yncaffiliate-chart-holder').show();");
            \Phpfox_Error::set(_p('to_date_is_not_valid'));
        }
        $iFromTimestamp = 0;
        $iToTimestamp = 0;

        if ($aVals && !empty($sFromDatePicker) && !empty($sToDatePicker))
        {
            $sFromDate = $aVals['from_day'] . '-' . $aVals['from_month'] . '-' . $aVals['from_year'];
            $sToDate = $aVals['to_day'] . '-' . $aVals['to_month'] . '-' . $aVals['to_year'];

            $iFromTimestamp = strtotime($sFromDate);
            $iToTimestamp = strtotime($sToDate)+23*60*60+59*60+59;
            if ($iFromTimestamp > $iToTimestamp)
            {
                $this->call("$('#ynaff_loading').hide();");
                $this->call("$('#yncaffiliate-chart-holder').show();");
                \Phpfox_Error::set(_p('from_date_must_be_less_than_to_date'));
            }
        }
        if (\Phpfox_Error::isPassed())
        {
            Phpfox::getBlock('yncaffiliate.statistic-chart', array('iFromTimestamp' => $iFromTimestamp, 'iToTimestamp' => $iToTimestamp,'sLabel' => $sLabel,'sStatus' => $sStatus,'sData' => $sData,'sGroupBy' => $sGroupBy,'iUserId' => $iUserId));
            $this->html('#yncaffiliate-chart-holder', $this->getContent(false));
            $this->call("$('#ynaff_loading').hide();");
            $this->call("$('#yncaffiliate-chart-holder').show();");
            $this->call('$Core.loadInit();');
        }
    }
    public function reviewCode()
    {
        $sImage = $this->get('image');
        $sWidth = $this->get('width');
        echo '<div style="width: 100%;text-align: center;"><img src="'.$sImage.'" style="width:'.$sWidth.'px;"></div>';
    }
    public function loadCommissionRule(){
        $iGroupId = $this->get('group_id');
        if(!(int)$iGroupId){
            return false;
        }
        Phpfox::getBlock('yncaffiliate.commission-rule',['group' => $iGroupId]);
        $this->call('$(\'#ynaf_commisison_rule\').html(\''.$this->getContent().'\');');
        $this->call('$(\'#ynaff_loading\').hide();$(\'#ynaf_commisison_rule\').show();');
        $this->call('$Core.loadInit();');
    }
    public function showAccountDetail()
    {
        $iUserId = $this->get('user_id');
        if(!(int)$iUserId)
        {
            return false;
        }
        $aDetail = Phpfox::getService('yncaffiliate.affiliate.affiliate')->getDetail($iUserId);
        $sHtml = '<div class="yncaffiliate_contact_detail">';
        if(count($aDetail))
        {
            $sHtml .= '<div><strong>'._p('contact_name').':</strong> '.$aDetail['contact_name'].'</div>';
            $sHtml .= '<div><strong>'._p('contact_email').':</strong> '.$aDetail['contact_email'].'</div>';
            $sHtml .= '<div><strong>'._p('contact_address').':</strong> '.$aDetail['contact_address'].'</div>';
            $sHtml .= '<div><strong>'._p('contact_phone').':</strong> '.$aDetail['contact_phone'].'</div>';
        }
        $sHtml .='</div>';
        echo $sHtml;
        return false;
    }
    public function editContactForm()
    {
        Phpfox::isUser(true);

        $this->setTitle(_p('edit_information'));

        $iUserId = $this->get('user_id');
        if(!(int)$iUserId)
        {
            echo _p('can_not_find_contact_detail');
            return false;
        }
        Phpfox::getBlock('yncaffiliate.edit-contact-form',['user_id' => $iUserId]);
    }
    public function editContactInformation()
    {
        Phpfox::isUser(true);

        $aVals = $this->get('val');
        $bIsError = false;
        if(empty($aVals['name']) || empty($aVals['email']) || empty($aVals['phone']) || empty($aVals['address']))
        {
            \Phpfox_Error::set(_p('all_fields_are_required'));
            $bIsError = true;
        }
        if (!filter_var($aVals['email'], FILTER_VALIDATE_EMAIL)) {
            \Phpfox_Error::set(_p('invalid_email_format'));
            $bIsError = true;
        }

        if($bIsError)
        {
            $this->call('$("#edit_contact_submit").show();$(".edit_contact_loading").hide();');
        }
        else{
            if(Phpfox::getService('yncaffiliate.affiliate.process')->updateAffiliate($aVals))
            {
                Phpfox::addMessage(_p('account_updated_successfully'));
                $this->call('window.location.reload();');
            }
        }
        return true;
    }
}