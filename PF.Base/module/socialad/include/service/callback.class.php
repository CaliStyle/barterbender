<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */

class Socialad_Service_Callback extends Phpfox_Service
{
	public function paymentApiCallback($aParam)
	{
		Phpfox::getService('socialad.payment')->handlePaymentCallback($aParam);
	}

	public function getNotificationAd_Approve($aNotification) {
		$iAdId = $aNotification['item_id'];
		$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);
		$sMessage =  _p('the_ad_title_has_been_approved', array(
			'title' => $aAd['ad_title']
		));
		return array(
			'link' => Phpfox::getLib('url')->makeUrl('socialad.ad.detail', array('id' => $aNotification['item_id'])),
			'message' => $sMessage,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}


	public function getNotificationAd_Deny($aNotification) {
		$iAdId = $aNotification['item_id'];
		$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);
		$sMessage =  _p('the_ad_title_has_been_denied', array(
			'title' => $aAd['ad_title']
		));
		return array(
			'link' => Phpfox::getLib('url')->makeUrl('socialad.ad.detail', array('id' => $aNotification['item_id'])),
			'message' => $sMessage,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}

	public function getNotificationAd_Orderconfirm($aNotification) {
		$iAdId = $aNotification['item_id'];
		$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);
		$sMessage =  _p('the_pay_later_request_for_ad_title_has_been_confirmed', array(
			'title' => $aAd['ad_title']
		));
		return array(
			'link' => Phpfox::getLib('url')->makeUrl('socialad.ad.detail', array('id' => $aNotification['item_id'])),
			'message' => $sMessage,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}

	public function getNotificationAd_Running($aNotification) {
		$iAdId = $aNotification['item_id'];
		$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);
		$sMessage =  _p('your_ad_title_is_running', array(
			'title' => $aAd['ad_title']
		));
		return array(
			'link' => Phpfox::getLib('url')->makeUrl('socialad.ad.detail', array('id' => $aNotification['item_id'])),
			'message' => $sMessage,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}
	
	public function getNotificationAd_Delete($aNotification) {
		$iAdId = $aNotification['item_id'];
		$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);
		$sMessage =  _p('your_ad_b_title_b_is_deleted', array(
			'title' => $aAd['ad_title']
		));
		return array(
			'link' => Phpfox::getLib('url')->makeUrl('socialad.ad.detail', array('id' => $aNotification['item_id'])),
			'message' => $sMessage,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}

	public function pendingApproval()
	{
		$iPendingId = Phpfox::getService('socialad.helper')->getConst('ad.status.pending');
		return array(
			'phrase' => _p('social_ads'),
			'value' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('socialad_ad'))->where('ad_status = ' . $iPendingId)->execute('getSlaveField'),
			'link' => Phpfox::getLib('url')->makeUrl('admincp.socialad.ad.pending')
		);
	}

    /**
     * @param $iUserId
     * @return array|bool
     */
    public function getUserStatsForAdmin($iUserId)
    {
        if (!$iUserId) {
            return false;
        }

        $iTotal = db()->select('COUNT(*)')
            ->from(':socialad_ad')
            ->where('ad_user_id ='.(int)$iUserId)
            ->execute('getField');
        return [
            'total_name' => _p('social_ads'),
            'total_value' => $iTotal,
            'type' => 'item'
        ];
    }

	public function getUploadParams()
    {
        return [
            'max_size' => null,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_url' => Phpfox::getLib('url')->makeUrl('socialad.ad.image'),
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'socialad' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'socialad' . PHPFOX_DS,
            'no_square' => true,
            'js_events' => [
                    'sending' => 'ynsocialad.addForm.dropzoneOnSending',
                    'success' => 'ynsocialad.addForm.dropzoneOnComplete',
                    'error' => 'ynsocialad.addForm.dropzoneOnError',
            ],
            'extra_data' => [
                'remove-button-action' => 'ynsocialad.addForm.dropzoneRemoveCurrent',
            ],
            'upload_now' => "true",
            'label' => '',
            'max_size_description' => '',
            'type_description' => '',
            'required' => true
        ];
    }
}



