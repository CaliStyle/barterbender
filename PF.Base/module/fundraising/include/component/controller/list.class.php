<?php

    /**
     * [PHPFOX_HEADER]
     */
    defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Controller_List extends Phpfox_Component {

    /**
     * Class process method which is used to execute this component.
     */
    public function process() {
    	Phpfox::getService('fundraising')->buildMenu();
        $iCampaignId = $this->request()->getInt('req3');
		if(!Phpfox::getService('fundraising.permission')->canViewStatisticCampaign($iCampaignId, Phpfox::getUserId()))
		{
			$this->url()->send('fundraising.error', array('status' => Phpfox::getService('fundraising')->getErrorStatusNumber('invalid_permission')));
		}

        $iPage = $this->request()->getInt('page');
        $iLimit = 10;
        $iTotal = 0;

        $aFilters = array(
            'keyword' => array(
                'type' => 'input:text',
                'search' => '(fd.full_name LIKE "%[VALUE]%" OR u.full_name LIKE "%[VALUE]%" OR fd.email_address LIKE "%[VALUE]%" OR u.email LIKE "%[VALUE]%" OR ft.paypal_transaction_id LIKE "%[VALUE]%" OR ft.amount = "[VALUE]")',
                'size' => 45,
            ),
        );

        $oSearch = Phpfox::getLib('search')->set(array(
                'type' => 'fundraising',
                'filters' => $aFilters,
                'search' => 'search'
            )
        );

        $oSearch->setCondition('AND ft.campaign_id = ' . $iCampaignId);

        $sKeyword = $oSearch->get('keyword');

        $sFromDate = $sToDate = '';
        if ($aValsDate = $this->request()->getArray('val')) {
            if(isset($aValsDate['from_month'])) {
                $sFromDate = strtotime($aValsDate['from_month'] . '/' . $aValsDate['from_day'] . '/' . $aValsDate['from_year']);
            }
            if(isset($aValsDate['to_month'])) {
                $sToDate = strtotime($aValsDate['to_month'] . '/' . $aValsDate['to_day'] . '/' . $aValsDate['to_year']);
            }
            $this->template()->assign('aForms', $aValsDate);
        }

        if($sFromDate && $sToDate)
        {
            $iStartTime = Phpfox::getLib('date')->mktime( 0, 0, 0, date('m',$sFromDate), date('d',$sFromDate), date('Y',$sFromDate));
            $iEndTime = Phpfox::getLib('date')->mktime( 23, 59, 59, date('m',$sToDate), date('d',$sToDate), date('Y',$sToDate));

            if ($iStartTime > $iEndTime)
            {
                $iTemp = $iStartTime;
                $iStartTime = $iEndTime;
                $iEndTime = $iTemp;
            }

            $oSearch->setCondition('AND ft.time_stamp > ' . $iStartTime . ' AND ft.time_stamp < ' . $iEndTime);
        }

        $aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignById($iCampaignId);

        list($iTotal, $aTransactions) = Phpfox::getService('fundraising.transaction')->getTransactionByCampaignId($oSearch->getConditions(), $iPage, $iLimit);

        $this->setParam('aTransactions', $aTransactions);

        $this->setParam('aPager', array( 'iPage' => $iPage, 'iLimit' => $iLimit, 'iTotal' => $iTotal));

        $sView = '';$iTransaction = 0;$aDetail = null;
        $sView = $this->request()->get('view');
        if($sView == 'detail') {
            $iTransaction = $this->request()->getInt('transaction');
            $aTransaction = Phpfox::getService('fundraising.transaction')->getTransactionById($iTransaction);
            $aDonor = Phpfox::getService('fundraising.user')->getDonorNameById($aTransaction['donor_id']);
			if(!$aDonor)
			{
				$aInvoice = unserialize($aTransaction['invoice']);
				$aDonor = $aInvoice;
				if($aInvoice['is_guest'])
				{
					$aDonor['guest_full_name'] = $aInvoice['full_name'];
				}
				else
				{
					$aDonor['full_name'] = Phpfox::getService('fundraising.user')->getFullNameOfUser($aInvoice['user_id']);
				}
			}

            $aDetail = array(
                'donor_name' => (isset($aDonor['is_guest']) && $aDonor['is_guest']) ? $aDonor['guest_full_name'] : $aDonor['full_name'],
                'campaign_name' => $aCampaign['title'],
                'donate_date' => $aTransaction['time_stamp'],
                'description' => $aCampaign['short_description'],
                'donation_amount' => $aTransaction['amount'],
                'transaction_id' => $aTransaction['paypal_transaction_id'],
                'sBackUrl' => 'fundraising.list.' . $aCampaign['campaign_id'],
            );
        }

		if ($aCampaign['module_id'] != 'fundraising' &&($aCallback = Phpfox::callback('fundraising.getFundraisingDetails', array('item_id' => $aCampaign['item_id'])))) {
			$this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
			$this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
		}

        $sFromDate = $sFromDate ? date('m/d/Y', $sFromDate) : date('m/d/Y');
        $sToDate = $sToDate ? date('m/d/Y', $sToDate) : date('m/d/Y');

        $this->template()->setBreadCrumb(_p('fundraisings'), $aCampaign['module_id'] == 'fundraising' ? $this->url()->makeUrl('fundraising') : $this->url()->permalink('pages', $aCampaign['item_id'], 'fundraising') )
            ->setBreadCrumb(_p('view_funds'),$this->url()->permalink('fundraising.list', $aCampaign['campaign_id']))
            ->setBreadCrumb($aCampaign['title'], $this->url()->permalink('fundraising', $aCampaign['campaign_id'], $aCampaign['title']), true )
        ->setMeta('description', $aCampaign['title'] . '.')
        ->setMeta('keywords', $this->template()->getKeywords($aCampaign['title']))
        ->setHeader('cache', array(
            'jquery/plugin/jquery.highlightFade.js' => 'static_script',
            'jquery/plugin/jquery.scrollTo.js' => 'static_script',
            'pager.css' => 'style_css',
            'feed.js' => 'module_feed',
        ))
        ->assign(array(
            'sViewFr' => $sView,
            'aTransaction' => $aDetail,
            'sFromDate' => $sFromDate,
            'sToDate' => $sToDate,
        ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean() {
        (($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_list_clean')) ? eval($sPlugin) : false);
    }
}