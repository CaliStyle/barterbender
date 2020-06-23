<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		younet
 * @package 		Phpfox_Component
 * @see Core_Service_Country_Country
 * @version 		3.01
 */
class Resume_Component_Controller_Summary extends Phpfox_Component {

    public function process() {
        $iId = $this->request()->get("id");
        // Edit mode
        $bIsEdit = false;
        if ($iId == 0) {
            $this->url()->send("resume.add");
        }
        $iEditId = 0;
        $aValidation = array(
            'headline' => array(
                'def' => 'required',
                'title' => _p('resume.add_headline_to_your_resume')
            ),
        );
        $aLevel = Phpfox::getService("resume.level")->getLevels();
        $aLevelLabel = array();
        foreach ($aLevel as $i => $aItem) {
            $aLevelLabel[$aItem['level_id']] = $aItem['name'];
        }
        $aCountries = Phpfox::getService('core.country')->get();
        $oValid = Phpfox::getLib('validator')->set(array(
            'sFormName' => 'js_resume_summary_form',
            'aParams' => $aValidation
                )
        );

        //Checked item in category 
        $aItemData = array();

        if ($iId != 0) {
            $iEditId = $iId;
            $aRow = Phpfox::getService("resume.basic")->getBasicInfo($iId);

            $bIsEdit = true;
            if (!isset($aRow['resume_id'])) {
                $this->url()->send("resume.add");
            }
            if ($aRow['user_id'] != Phpfox::getUserId()) {
                if (!Phpfox::getUserParam('resume.can_edit_other_resume')) {
                    $this->url()->send("subscribe");
                }
            }

            //get item of category and display
            $aItemDataTmp = Phpfox::getService('resume.category')->getCategoriesData($iId);
            foreach ($aItemDataTmp as $ItemDataTmp) {
                $aItemData[] = $ItemDataTmp['category_id'];
            }

            $aRow['country_phrase'] = '';
            if (isset($aRow['country_child_id']) && $aRow['country_child_id'] > 0) {
                $aRow['country_phrase'] = Phpfox::getService('core.country')->getChild($aRow['country_child_id']) . " ";
            }

            $aRow['country_phrase'] .= Phpfox::getPhraseT(Phpfox::getService('core.country')->getCountry($aRow['country_iso']), 'country');

            $this->setParam(array(
                'country_child_value' => $aRow['country_iso'],
                'country_child_id' => $aRow['country_child_id'],
                'authorized_country_child_value' => $aRow['authorized_country_iso'],
                'authorized_country_child_id' => $aRow['authorized_country_child_id'],
                    )
            );
            $aRow['user_id'] = Phpfox::getUserId();

            if (isset($aRow["authorized"]["label_level_id"]))
                unset($aRow["authorized"]["label_level_id"]);

            if (isset($aRow["authorized"]["other_level"]))
                unset($aRow["authorized"]["other_level"]);

            if (isset($aRow["authorized"]))
            {
                for($i=0;$i<count($aRow["authorized"]);$i++)
                {
                    $item = $aRow["authorized"][$i];
                    if (isset($item["label_country_iso"]))
                    {
                        if (empty($item["label_country_iso"]))
                        {
                            unset($aRow["authorized"][$i]);
                        }
                    }
                }
            }
            $this->template()->assign(array('aForms' => $aRow));
        }


        //Process Update
        if ($aVals = $this->request()->getArray('val')) {
            $aAuthorized = array();
			if(!empty($aVals['authorized_country_iso']))
			{
                if (isset($aVals['authorized_country_iso']))
                {
                    if (is_array($aVals['authorized_country_iso'] ))
                    {

                        foreach ($aVals['authorized_country_iso'] as $iKey => $sCountryIso) {
                            $value_authorized_level_id = "";
                            if (isset($aVals['authorized_level_id'][$iKey]))
                            {
                                if (isset($aLevelLabel[$aVals['authorized_level_id'][$iKey]]))
                                {
                                    $value_authorized_level_id = $aLevelLabel[$aVals['authorized_level_id'][$iKey]];
                                }
                            }
                            $value_other_level = "";
                            if (isset($aVals['authorized_other_level'][$iKey]))
                            {
                                $value_other_level    = $aVals['authorized_other_level'][$iKey];
                            }


                            $aAuthorized[] = array(
                                'label_country_iso' => isset($aCountries[$aVals['authorized_country_iso'][$iKey]]) ? $aCountries[$aVals['authorized_country_iso'][$iKey]] : '',
                                'label_level_id' => $value_authorized_level_id,
                                'label_country_child' => isset($aVals['authorized_country_child'][$iKey]) ? Phpfox::getService('core.country')->getChild($aVals['authorized_country_child'][$iKey]) : '',
                                'country_iso' => $aVals['authorized_country_iso'][$iKey],
                                'country_child' => $aVals['authorized_country_child'][$iKey],
                                'location' => $aVals['authorized_location'][$iKey],
                                'level_id' => $aVals['authorized_level_id'][$iKey],
                                'other_level' =>$value_other_level
                            );
                        }
                    }
                }

            }
            $aVals['authorized'] = serialize($aAuthorized);
            $is_validate = true;
            if ($oValid->isValid($aVals)) {
                $is_validate = false;
                if (!isset($aVals['category'])) {
                    Phpfox_Error::set(_p('resume.add_category_to_your_resume'));
                    $is_validate = true;
                } else {
                    $aVals['resume_id'] = $iId;
                    if (Phpfox::getService("resume.summary.process")->update($aVals)) {
                        Phpfox::getService('resume')->updateStatus($iId);

                        //No ajax mode
                        $this->url()->send("resume.experience", array('id' => $iId), _p("resume.your_summary_updated_successfully"));
                    } else {
                        $is_validate = true;
                    }
                }
            }

            if ($is_validate == true) {
                $aVals['resume_id'] = $iId;
                //item of category
                if (isset($aVals['category'])) {
                    $aItemData = $aVals['category'];
                } else {
                    $aItemData = array();
                }

                // Multi-location.
                $aVals['authorized'] = $aAuthorized;

                $aVals['authorized_country_iso'] = '';
                $aVals['authorized_country_child'] = '0';
                $aVals['authorized_location'] = '';
                $aVals['authorized_level_id'] = 0;
                $aVals['authorized_other_level'] = 0;

                $this->template()->assign(array(
                    'aForms' => $aVals
                ));
            }
        }


        $iMaxCategories = Phpfox::getUserParam('resume.resume_category_numbers');

        Phpfox::getService('resume')->setPageSectionMenu($aRow);
        $this->template()->assign(array(
                    'sDobStart' => Phpfox::getParam('user.date_of_birth_start'),
                    'sDobEnd' => Phpfox::getParam('user.date_of_birth_end'),
                    'id' => $iId,
                    'bIsEdit' => $bIsEdit,
                    'aLevel' => $aLevel,
                    'aItemData' => $aItemData,
                    'iMaxCategories' => $iMaxCategories,
                    'typesession' => Phpfox::getService("resume.process")->typesesstion($iId),
                ))
                ->setHeader('cache', array(
                    'country.js' => 'module_resume',

                    'resume.js' => 'module_resume',
                ))
                ->setBreadcrumb(_p('resume.resume'), $this->url()->makeUrl('resume'))
                ->setBreadcrumb((!empty($iEditId) ? _p('resume.editing_resume') . ': ' . Phpfox::getLib('parse.output')->shorten($aRow['full_name'], Phpfox::getService('core')->getEditTitleSize(), '...') : _p('resume.create_new_resume')), ($iEditId > 0 ? $this->url()->makeUrl('resume', array('add', 'id' => $iEditId)) : $this->url()->makeUrl('resume', array('add'))), true)
                ->setPhrase(array('resume.you_reach_the_maximum_of_total_predefined'));
    }

    public function clean() {
        (($sPlugin = Phpfox_Plugin::get('resume.component_controller_index_clean')) ? eval($sPlugin) : false);
    }

}

?>