<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Manage_Modules extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('directory.helper')->buildMenu();
        $iEditedBusinessId = 0;
        if ($this->request()->getInt('id')) {
            $iEditedBusinessId = $this->request()->getInt('id');
            $this->setParam('iBusinessId', $iEditedBusinessId);
        }

        if (!(int)$iEditedBusinessId) {
            $this->url()->send('directory');
        }
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iEditedBusinessId);

        // check permission 
        if (!Phpfox::getService('directory.permission')->canEditBusiness($aBusiness['user_id'], $iEditedBusinessId) ||
            !Phpfox::getService('directory.permission')->canManageModule($iEditedBusinessId)
        ) {
            $this->url()->send('subscribe');
        }

        $aModules = Phpfox::getService('directory')->getPageModuleForManage($iEditedBusinessId);
        $aAllModules = Phpfox::getService('directory')->getAllModule();

        $aModuleActions = array();
        $sModuleId = '';
        $sController = '';
        $sYnAddParamForNavigateBack = Phpfox::getService('directory.helper')->getYnAddParamForNavigateBack();

        foreach ($aAllModules as $aModule) {
            $aModuleActions[$aModule['module_name']]['view_link'] = $this->url()->makeUrl('directory.detail', array($iEditedBusinessId, $aBusiness['name'], $aModule['module_name']));
            switch ($aModule['module_name']) {
                case 'photos':
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdPhoto();
                    $sController = $sModuleId . '.add';
                    $bCanAddPhotoInBusiness = Phpfox::getService('directory.permission')->canAddPhotoInBusiness($iEditedBusinessId, $bRedirect = false);
                    $aModuleActions[$aModule['module_name']]['bCanAddPhotoInBusiness'] = $bCanAddPhotoInBusiness;
                    $aModuleActions[$aModule['module_name']]['add_link'] = Phpfox::getLib('url')->makeUrl($sController, array()) . 'module_directory/item_' . $iEditedBusinessId . '/';
                    break;
                case 'videos':
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdVideo();
                    $sController = $sModuleId . '.add';
                    $bCanAddVideoInBusiness = Phpfox::getService('directory.permission')->canAddVideoInBusiness($iEditedBusinessId, $bRedirect = false);
                    $aModuleActions[$aModule['module_name']]['bCanAddVideoInBusiness'] = $bCanAddVideoInBusiness;
                    $aModuleActions[$aModule['module_name']]['add_link'] = Phpfox::getLib('url')->makeUrl($sController, array()) . 'module_directory/item_' . $iEditedBusinessId . '/';
                    break;
                case 'musics':
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdMusic();
                    $sController = $sModuleId . '.upload';
                    $bCanAddMusicInBusiness = Phpfox::getService('directory.permission')->canAddMusicInBusiness($iEditedBusinessId, $bRedirect = false);
                    $aModuleActions[$aModule['module_name']]['bCanAddMusicInBusiness'] = $bCanAddMusicInBusiness;
                    $aModuleActions[$aModule['module_name']]['add_link'] = Phpfox::getLib('url')->makeUrl($sController, array()) . 'module_directory/item_' . $iEditedBusinessId . '/';
                    break;
                case 'blogs':
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdBlog();
                    $sController = $sModuleId . '.add';
                    $bCanAddBlogInBusiness = Phpfox::getService('directory.permission')->canAddBlogInBusiness($iEditedBusinessId, $bRedirect = false);
                    $aModuleActions[$aModule['module_name']]['bCanAddBlogInBusiness'] = $bCanAddBlogInBusiness;
                    $aModuleActions[$aModule['module_name']]['add_link'] = Phpfox::getLib('url')->makeUrl($sController, array()) . 'module_directory/item_' . $iEditedBusinessId . '/';
                    break;
                case 'ynblog':
                    $sModuleId = 'ynblog';
                    $sController = $sModuleId . '.add';
                    $bCanAddYnBlogInBusiness = Phpfox::getService('directory.permission')->canAddAdvBlogInBusiness($iEditedBusinessId, $bRedirect = false);
                    $aModuleActions[$aModule['module_name']]['bCanAddYnBlogInBusiness'] = $bCanAddYnBlogInBusiness;
                    $aModuleActions[$aModule['module_name']]['add_link'] = Phpfox::getLib('url')->makeUrl($sController, array()) . 'module_directory/item_' . $iEditedBusinessId . '/';
                    break;
                case 'polls':
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdPolls();
                    $sController = $sModuleId . '.add';
                    $bCanAddPollsInBusiness = Phpfox::getService('directory.permission')->canAddPollsInBusiness($iEditedBusinessId, $bRedirect = false);
                    $aModuleActions[$aModule['module_name']]['bCanAddPollsInBusiness'] = $bCanAddPollsInBusiness;
                    $aModuleActions[$aModule['module_name']]['add_link'] = Phpfox::getLib('url')->makeUrl($sController, array()) . 'module_directory/item_' . $iEditedBusinessId . '/';
                    break;
                case 'coupons':
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdCoupon();
                    $sController = $sModuleId . '.add';
                    $bCanAddCouponInBusiness = Phpfox::getService('directory.permission')->canAddCouponInBusiness($iEditedBusinessId, $bRedirect = false);
                    $aModuleActions[$aModule['module_name']]['bCanAddCouponInBusiness'] = $bCanAddCouponInBusiness;
                    $aModuleActions[$aModule['module_name']]['add_link'] = Phpfox::getLib('url')->makeUrl($sController, array()) . 'module_directory/item_' . $iEditedBusinessId . '/';

                    break;
                case 'events':
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdEvent();
                    $sController = $sModuleId . '.add';
                    $bCanAddEventInBusiness = Phpfox::getService('directory.permission')->canAddEventInBusiness($iEditedBusinessId, $bRedirect = false);
                    $aModuleActions[$aModule['module_name']]['bCanAddEventInBusiness'] = $bCanAddEventInBusiness;
                    $aModuleActions[$aModule['module_name']]['add_link'] = Phpfox::getLib('url')->makeUrl($sController, array()) . 'module_directory/item_' . $iEditedBusinessId . '/';
                    break;
                case 'jobs':
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdJob();
                    $sController = $sModuleId . '.add';
                    $bCanAddJobInBusiness = Phpfox::getService('directory.permission')->canAddJobInBusiness($iEditedBusinessId, $bRedirect = false);
                    $aModuleActions[$aModule['module_name']]['bCanAddJobInBusiness'] = $bCanAddJobInBusiness;
                    $aModuleActions[$aModule['module_name']]['add_link'] = Phpfox::getLib('url')->makeUrl($sController, array()) . 'module_directory/item_' . $iEditedBusinessId . '/';
                    break;
                case 'marketplace':
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdMarketplace();
                    $sController = $sModuleId . '.add';
                    $bCanAddMarketplaceInBusiness = Phpfox::getService('directory.permission')->canAddMarketplaceInBusiness($iEditedBusinessId, $bRedirect = false);
                    $aModuleActions[$aModule['module_name']]['bCanAddMarketplaceInBusiness'] = $bCanAddMarketplaceInBusiness;
                    $aModuleActions[$aModule['module_name']]['add_link'] = Phpfox::getLib('url')->makeUrl($sController, array()) . 'module_directory/item_' . $iEditedBusinessId . '/';
                    break;
                case 'ultimatevideo':
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdUltimateVideo();
                    $sController = $sModuleId . '.add';
                    $bCanAddVideoInBusiness = Phpfox::getService('directory.permission')->canAddVideoInBusiness($iEditedBusinessId, $bRedirect = false);
                    $aModuleActions[$aModule['module_name']]['bCanAddVideoInBusiness'] = $bCanAddVideoInBusiness;
                    $aModuleActions[$aModule['module_name']]['add_link'] = Phpfox::getLib('url')->makeUrl($sController, array()) . 'module_directory/item_' . $iEditedBusinessId . '/';
                    break;
                case 'v':
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdV();
                    $sController = $sModuleId . '.share';
                    $bCanAddVideoInBusiness = Phpfox::getService('directory.permission')->canAddVideoInBusiness($iEditedBusinessId, $bRedirect = false);
                    $aModuleActions[$aModule['module_name']]['bCanAddVideoInBusiness'] = $bCanAddVideoInBusiness;
                    $aModuleActions[$aModule['module_name']]['add_link'] = Phpfox::getLib('url')->makeUrl($sController, array('module' => 'directory', 'item' => $iEditedBusinessId)) . '/';
                    $aModuleActions[$aModule['module_name']]['view_link'] = $this->url()->makeUrl('directory.detail', array($iEditedBusinessId, $aBusiness['name'])) . 'v/';
                    break;
            }
        }
        $this->template()
            ->setEditor()
            ->setPhrase(array())
            ->setHeader('cache', array(
                'pager.css' => 'style_css',
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'progress.js' => 'static_script',
                'share.js' => 'module_attachment',
                'country.js' => 'module_core',
            ));
        $this->template()->assign(array(
            'aModuleActions' => $aModuleActions,
            'aModules' => $aModules,
            'iBusinessid' => $iEditedBusinessId,
            'core_path' => Phpfox::getParam('core.path'),
        ));
        $this->template()->setBreadcrumb(_p('directory.manage_modules'), $this->url()->permalink('directory.edit', 'id_' . $iEditedBusinessId));
        Phpfox::getService('directory.helper')->loadDirectoryJsCss();
    }

    public function clean()
    {

    }

}

?>