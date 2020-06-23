<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Detail extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iId = $this->getParam('id');
		$iPage = $this->getParam('page') ? $this->getParam('page') : 0;
        $aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignById($iId);
		$sType = $this->getParam('sType');

        if(!empty($aCampaign['gmap']) && Phpfox::getLib('parse.format')->isSerialized($aCampaign['gmap']))
        {
            $gmap = unserialize($aCampaign['gmap']);
            $aCampaign['latitude'] = $gmap['latitude'];
            $aCampaign['longitude'] = $gmap['longitude'];
        }

		if($sType == 'donations')
		{
            $iTotal = Phpfox::getService('fundraising.user')->getTotalDonorsOfCampaign($aCampaign['campaign_id']);
			$aDonations = Phpfox::getService('fundraising.user')->getDonorsOfCampaign($aCampaign['campaign_id'], $iTotal, $iPage, $iTotal);
			foreach($aDonations as &$aDonor)
			{
				$aDonor['amount_text'] = Phpfox::getService('fundraising')->getCurrencyText($aDonor['amount'], $aDonor['currency']);
			}
			$aCampaign['donations'] = $aDonations;
		}
		else if($sType == 'news')
		{			
			$aValidation = array(
				'news_headline' => array(
					'def' => 'required',
					'title' => 'Please fill in news headline'
				),
				'news_content' => array(
					'def' => 'required',
					'title' => 'Please fill in news content'
				)
			);
					
			$oValid = Phpfox::getLib('validator')->set(array(
					'sFormName' => 'js_form_news', 
					'aParams' => $aValidation
				)
			);
			
			list($iTotal, $aNews) = Phpfox::getService('fundraising')->getNews($iId);
            $aCampaign['news'] = $aNews;

            $sCheckFormNewsLink = "<script type=\"text/javascript\">
                function checkFormNewsLink()
                {
                    var sLink = $('#news_link').val();
                    if($.trim(sLink).length == 0)
                        return true;
                    if ($.trim(sLink).search(/(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/) == -1)
                    {
                        $('#js_form_news_msg').message('" . _p('please_provide_a_valid_url') . "', 'error');
                        $('#news_link').addClass('alert_input');
                        return false;
                    }
                    return true;
                }
                </script>";
                                       
			$this->template()->assign(array(
				'sCreateJs' => $oValid->createJS(),
				'sGetJsForm' => $oValid->getJsForm(),
                        'sCheckFormNewsLink' => $sCheckFormNewsLink
			));			
		}

		$this->template()->assign(array(
			'aCampaign' 	=> $aCampaign,
			'sType'		=> $sType
		));
		
		if(empty($aCampaign['contact_about_me']))
        {
            $aMenus = array(
                _p('description')=> '#fundraising.displayDetail?sType=description&amp;id='.$iId,
                _p('donors_upper')=>'#fundraising.displayDetail?sType=donations&amp;id='.$iId,
                _p('news')=>'#fundraising.displayDetail?sType=news&amp;id='.$iId
            );
        }
        else
        {
            $aMenus = array(
                _p('description')=> '#fundraising.displayDetail?sType=description&amp;id='.$iId,
                _p('donors_upper')=>'#fundraising.displayDetail?sType=donations&amp;id='.$iId,
                _p('news')=>'#fundraising.displayDetail?sType=news&amp;id='.$iId,
                _p('about_us')=>'#fundraising.displayDetail?sType=about&amp;id='.$iId,
            );
        }
		$this->template()->assign(array(
			'corepath'=>phpfox::getParam('core.path'),
			'aMenus' => $aMenus,
			'sHeader' => '',
			'googleApiKey' => Phpfox::getParam('core.google_api_key')
		       ));
		
		return 'block';
	}
	
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_block_detail_clean')) ? eval($sPlugin) : false);
	}
}

?>