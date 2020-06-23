<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Block_Menu_Add extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $module_name = "petition";
		
		
		
        if(!$id = $this->request()->get('id'))
        {
            return false;
        }
		 
			
        $id = $id = $this->request()->get('id');
        if (!$aCampaign = Phpfox::getService($module_name.'.petition')->getPetitionForEdit($id))
        {
            return false;
        }
        $aMenus = array(
            'detail' => Phpfox::getPhrase($module_name.'.main_info'),
            'photos' => Phpfox::getPhrase($module_name.'.photos'),
            'invite' => Phpfox::getPhrase($module_name.'.invite_friends'),
            'letter' => Phpfox::getPhrase($module_name.'.petition_letter')
        );
        $sView = Phpfox::getPhrase($module_name.'.view_this_petition');

        $sLink = $this->url()->permalink('petition', $id, $aCampaign['title']);

        $this->template()->assign(array(
                'sView' => $sView,
                'aMenus' => $aMenus,
                'sLink' => $sLink,
                'module_name'=>$module_name
            )
        );
        return 'block';

        /*


        $bAddNew = $this->request()->get('req4');


        $iAuctionId = $this->request()->get('id');
        if ($iAuctionId > 0)
        {
            $bAddNew  = false;
        }else{
            $bAddNew = true;
        }


        $this->template()->assign(array(
                'bAddNew' => $bAddNew
            )
        );

        return 'block';
        */
    }

}

?>