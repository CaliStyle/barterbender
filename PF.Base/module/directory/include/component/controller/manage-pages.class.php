<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Manage_Pages extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('directory.helper')->buildMenu();
        $iEditedBusinessId = 0;
        if ($this->request()->getInt('id')) {
            $iEditedBusinessId = $this->request()->getInt('id');
        }

        if (!(int)$iEditedBusinessId) {
            $this->url()->send('directory');
        }
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iEditedBusinessId);

        // check permission 
        if (!Phpfox::getService('directory.permission')->canEditBusiness($aBusiness['user_id'], $iEditedBusinessId) ||
            !Phpfox::getService('directory.permission')->canManagePagesDashBoard($iEditedBusinessId)
        ) {
            $this->url()->send('subscribe');
        }
        $aBusiness['setting_support'] = Phpfox::getService('directory.permission')->getSettingSupportInBusiness($aBusiness['business_id'], $aBusiness);

        $sView = 'maincontent';
        $oValid = array();

        if ($this->request()->get('view')) {
            $sView = $this->request()->get('view');
        }


        $aValidation = array();

        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => '',
                'aParams' => $aValidation
            )
        );

        if ($sView == 'maincontent') {

            $aPagesModule = Phpfox::getService('directory')->getModuleInBusiness($iEditedBusinessId);

            $aModules = Phpfox::getService('directory')->getPageModuleForManage($iEditedBusinessId);

            $aModuleView = array();
            foreach ($aModules[0] as $iModuleId => $aModule) {
                $aItem = Phpfox::getService('directory')->getPageByBusinessModuleId($iEditedBusinessId, $iModuleId);
                if (isset($aItem['module_name'])) {
                    $aModuleView[$aItem['module_name']] = $aItem;
                }
            }

        } elseif ($sView == 'addnewcontent') {

            $aValidation = array(
                'page_title' => array(
                    'def' => 'required',
                    'title' => _p('directory.fill_in_a_title_for_your_page')
                ),
                'contentpage' => array(
                    'def' => 'required',
                    'title' => _p('directory.add_some_content_to_your_page')
                )
            );

            $oValid = Phpfox::getLib('validator')->set(array(
                    'sFormName' => 'js_add_new_page',
                    'aParams' => $aValidation
                )
            );
        } elseif ($sView == 'editaboutus') {

            $aAboutUs = Phpfox::getService('directory')->getPageAboutUsByBusinessId($iEditedBusinessId);

            $aValidation = array(
                'contentpage' => array(
                    'def' => 'required',
                    'title' => _p('directory.add_some_content_to_your_page')
                )
            );

            $oValid = Phpfox::getLib('validator')->set(array(
                    'sFormName' => 'js_edit_about_us',
                    'aParams' => $aValidation
                )
            );
        } elseif ($sView == 'editcustompage') {
            $iCustomPageId = $this->request()->getInt('idcustom');
            $aCustomPage = Phpfox::getService('directory')->getPageBusinessByDataId($iCustomPageId);
            $aValidation = array(
                'page_title' => array(
                    'def' => 'required',
                    'title' => _p('directory.add_some_content_to_your_page')
                ),
                'contentpage' => array(
                    'def' => 'required',
                    'title' => _p('directory.add_some_content_to_your_page')
                )
            );

            $oValid = Phpfox::getLib('validator')->set(array(
                    'sFormName' => 'js_edit_custom_page',
                    'aParams' => $aValidation
                )
            );
        } elseif ($sView == 'editfaq') {

            $aFAQs = Phpfox::getService('directory')->getFAQsByBusinessId($iEditedBusinessId);
        } elseif ($sView == 'editcontactus') {
            if ($aBusiness['setting_support']['allow_business_owner_to_edit_contact_form'] == false) {
                $this->url()->send("directory.manage-pages", array('id' => $iEditedBusinessId), _p('directory.you_do_not_have_permission_for_this_action_please_contact_administrator_for_more_details'));
            }

            $aContactUsCustomfield = array();
            $aContactUs = Phpfox::getService('directory')->getPageContactUsByBusinessId($iEditedBusinessId);
            if (count($aContactUs)) {
                $aContactUsCustomfield = Phpfox::getService('directory.customcontactus.custom')->getCustomFieldByContactUsId($aContactUs['contactus_id']);
            }
            if (count($aContactUs['receiver_data'])) {
                $aContactUs['receiver_data'] = json_decode($aContactUs['receiver_data'], 1);
            }
        }

        if ($aVals = $this->request()->getArray('val')) {
            $iEditedBusinessId = $aVals['business_id'];
            if (isset($aVals['manage_page'])) {

                list($message, $check) = Phpfox::getService('directory.process')->updateExtraInfoBusinessPage($aVals, $iEditedBusinessId);

                if ($check) {

                    $this->url()->send("directory.manage-pages", array('id' => $iEditedBusinessId), _p('directory.manage_pages_updated_successfully'));

                } else {
                    if ($message == 'invalid_landing') {
                        $this->url()->send("directory.manage-pages", array('id' => $iEditedBusinessId), _p('directory.the_page_you_choose_to_be_landing_page_is_hidden'));
                    }

                    $this->url()->send("directory.manage-pages", array('id' => $iEditedBusinessId), _p('directory.manage_pages_updated_successfully'));

                }
            }


            if (isset($aVals['add_new_page']) && $oValid->isValid($aVals)) {

                if (Phpfox::getService('directory.process')->addNewPage($aVals, $iEditedBusinessId)) {
                    $this->url()->send("directory.manage-pages", array('id' => $iEditedBusinessId), _p('directory.add_page_successfully'));
                }
            }


            if (isset($aVals['edit_about_us']) && $oValid->isValid($aVals)) {

                if (Phpfox::getService('directory.process')->editAboutUs($aVals, $iEditedBusinessId)) {
                    $this->url()->send("directory.manage-pages", array('id' => $iEditedBusinessId), _p('directory.edit_about_us_successfully'));
                }

            }

            if (isset($aVals['edit_custom_page']) && $oValid->isValid($aVals)) {

                if (Phpfox::getService('directory.process')->editCustomPage($aVals, $aVals['custompage_id'])) {
                    $this->url()->send("directory.manage-pages", array('id' => $iEditedBusinessId), _p('directory.edit_custom_page_successfully'));
                }

            }

            if (isset($aVals['edit_faq'])) {

                if (Phpfox::getService('directory.process')->saveFAQForBusiness($aVals, $iEditedBusinessId)) {
                    $this->url()->send("directory.manage-pages", array('id' => $iEditedBusinessId), _p('directory.edit_faqs_successfully'));
                }

            }

            if (isset($aVals['edit_contact_us'])) {

                if (Phpfox::getService('directory.process')->editContactUs($aVals, $iEditedBusinessId)) {
                    Phpfox::getService('directory.customcontactus.process')->updateActiveCustomField($aVals['contact_us_id'], $aVals['cf_active']);
                    $this->url()->send("directory.manage-pages", array('id' => $iEditedBusinessId, 'view' => 'editcontactus'), _p('directory.edit_contact_us_successfully'));
                }

            }

        }

        if ($sView == 'maincontent') {
            $this->template()->assign(array(
                'aPagesModule' => $aPagesModule,
                'aModuleView' => $aModuleView,
            ));
        } else
            if ($sView == 'addnewcontent') {

            } else
                if ($sView == 'editaboutus') {
                    $this->template()->assign(array(
                        'aForms' => $aAboutUs,
                    ));
                } else
                    if ($sView == 'editcustompage') {
                        $this->template()->assign(array(
                            'aForms' => $aCustomPage,
                        ));
                    } else
                        if ($sView == 'editfaq') {
                            $this->template()->assign(array(
                                'aFAQs' => $aFAQs,
                            ));
                        }
        if ($sView == 'editcontactus') {
            $this->template()->assign(array(
                'aContactUs' => $aContactUs,
                'aContactUsCustomfield' => $aContactUsCustomfield,
            ));
        }
        $this->template()
            ->setEditor()
            ->assign(array(
                'sView' => $sView,
                'aBusiness' => $aBusiness,
                'iEditedBusinessId' => $iEditedBusinessId ? $iEditedBusinessId : $this->request()->getInt('id'),
                'iBusinessid' => $iEditedBusinessId,
                'sCreateJs' => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(),
                'corepath' => phpfox::getParam('core.path'),
            ))
            ->setPhrase(array())
            ->setHeader('cache', array(
                'pager.css' => 'style_css',
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'progress.js' => 'static_script',
                'share.js' => 'module_attachment',
                'country.js' => 'module_core'
            ))
            ->setPhrase(array(
                'directory.edit_title',
                'directory.add_new_page',
            ));
        $this->template()->setBreadcrumb(_p('directory.manage_pages'), $this->url()->permalink('directory.edit', 'id_' . $iEditedBusinessId));

        Phpfox::getService('directory.helper')->loadDirectoryJsCss();

    }

    public function clean()
    {

    }

}

?>