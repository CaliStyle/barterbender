<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Block_Sign extends Phpfox_Component
{

	public function process()
	{
		$iId = $this->getParam('id');		
		$aPetition = Phpfox::getService('petition')->getPetition($iId );
         
          if($aPetition['can_sign'] == 2)
          {
            return Phpfox_Error::display('<div class="error_message">'.Phpfox::getPhrase('petition.you_have_already_signed_this_petition').'</div>');
          }
          else if($aPetition['can_sign'] != 1)
          {
            return Phpfox_Error::display('<div class="error_message">'.Phpfox::getPhrase('petition.you_are_not_allowed_to_sign_this_petition').'</div>');
          }
		$aValidation = array(
              'location' => array(
                   'def' => 'required',
                   'title' => Phpfox::getPhrase('petition.fill_in_a_location')
              ),
              'signature' => array(
                   'def' => 'required',
                   'title' => Phpfox::getPhrase('petition.please_add_a_reason')
              )
         );
                   
         $oValid = Phpfox::getLib('validator')->set(array(
                   'sFormName' => 'js_form_sign', 
                   'aParams' => $aValidation
              )
         );
	
        $this->template()->assign(array(
             'sCreateJs2' => $oValid->createJS(),
             'sGetJsForm' => $oValid->getJsForm(),
             'aPetition'	=> $aPetition,
             'sSubmitUrl' 	=> $this->url()->permalink('petition', $aPetition['petition_id'], $aPetition['title'])
        ));	
	}

	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_block_sign_clean')) ? eval($sPlugin) : false);
	}
}

?>