<?php

/**
 * [PHPFOX_HEADER]
 */
namespace Apps\YNC_Member\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
defined('PHPFOX') or exit('NO DICE!');

class EditCustomFieldBlock extends \Phpfox_Component
{
    public function process()
    {

        $bIsEditGroup = false;
        $bAddInput = false;

        $aValidation = array(
            'group_name' => array(
                'def' => 'required',
                'title'=> _p("Group Name cannot be empty")
            ),
        );

        if ($this->getParam('iCustomFieldGroupId'))
        {
            $iEditGroupId = $this->getParam('iCustomFieldGroupId');
            if ($aGroup = Phpfox::getService('ynmember.custom.group')->getGroupForEdit($iEditGroupId))
            {
                $bAddInput = true;
                $bIsEditGroup = true;
                $this->template()->assign(array(
                    'aGroup' => $aGroup,
                    'bIsEditGroup' =>$bIsEditGroup
                ));
            }
        }

        $aLanguages = Phpfox::getService('language')->get();
        if ($bAddInput){
            $aKeys = array_keys($aGroup['group_name']);
            $aVals2 = array_values($aGroup['group_name']);
        }
        foreach ($aLanguages as $iKey => $aLanguage)
        {
            if ($bAddInput && isset($aKeys[0]))
            {
                $aLanguages[$iKey]['phrase_var_name'] = $aKeys[0];
            }
            $mPost = '';
            if($bAddInput && isset($aVals2[0])){
                foreach ($aVals2[0] as $keyaVals2 => $valueaVals2) {
                    if($aLanguages[$iKey]['language_id'] == $keyaVals2){
                        $mPost = $valueaVals2;
                    }
                }
            }
            $aLanguages[$iKey]['post_value'] = $mPost;
        }

        $this->template()->setTitle($bIsEditGroup?_p('Edit custom field groups'):_p('Add custom field groups'))
            ->setBreadcrumb($bIsEditGroup?_p('Edit custom field groups'):_p('Add custom field groups'))
            ->assign(array(
                    'sCorePath' => Phpfox::getParam('core.path_actual'),
                    'aLanguages' => $aLanguages,
                    'sUrl' => $this->url()->makeUrl('ynmember.admincp.customfield.add'),
                )
            )->setHeader(array(
                'jquery/ui.js' => 'static_script',
            ));

    }

}