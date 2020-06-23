<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/20/16
 * Time: 11:04 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Manage_Store extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('ynsocialstore.can_view_store', true);

        $this->template()
            ->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore.store'))
            ->setBreadcrumb(_p('seller_section'), $this->url()->permalink('ynsocialstore.statistic',null))
            ->setBreadCrumb(_p('manage_stores'), $this->url()->makeUrl('ynsocialstore.manage-store'))
        ;

        $this->search()->set(Phpfox::getService('ynsocialstore.helper')->getParamsSearchStore());
        $this->setParam('is_seller', true);
        $aBrowseParams = array(
            'module_id' => 'ynsocialstore',
            'alias' => 'st',
            'field' => 'store_id',
            'table' => Phpfox::getT('ynstore_store'),
            'hide_view' => array('pending', 'my')
        );

        $this->search()->setCondition('AND st.status !="deleted" AND st.user_id = '.Phpfox::getUserId());

        $this->search()->browse()->params($aBrowseParams)->execute();
        $aStore = $this->search()->browse()->getRows();

        Phpfox::getLib('pager') -> set(
            array(
                'page' => $this->search()->getPage(),
                'size' => $this->search()->getDisplay(),
                'count' => $this->search()->getCount(),
            )
        );

        $aModerationMenu = [];
        $aModerationMenu[] = [
            'phrase' => _p('reopen'),
            'action' => 'reopenStore'
        ];

        $aModerationMenu[] = [
            'phrase' => _p('close'),
            'action' => 'closeStore'
        ];

        if(Phpfox::getUserParam('ynsocialstore.can_delete_own_store')){
            $aModerationMenu[] = [
                'phrase' => _p('core.delete'),
                'action' => 'delete'
            ];
        }

        $this->setParam('global_moderation', [
            'name' => 'ynsocialstore',
            'ajax' => 'ynsocialstore.moderation',
            'menu' => $aModerationMenu
        ]);

        $this->template()->assign(array(
            'aItems' => $aStore,
            'is_manage' => true,
            'sCorePath' => Phpfox::getParam('core.path_actual').'PF.Base/',
            'bShowModeration' => true,
        ));

        $this->setParam(array(
            'is_manage' => true,
        ));

        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        Phpfox::getService('ynsocialstore.helper')->buildMenu();

        if(!defined('PHPFOX_IS_PAGES_VIEW') && !defined('PHPFOX_PAGES_ITEM_TYPE') && !defined('PHPFOX_IS_USER_PROFILE')){
            $canCreateProduct = Phpfox::getService('ynsocialstore')->checkUserStores();
            if($canCreateProduct) {
                sectionMenu(_p('ynsocialstore_sell_new_product'), 'social-store/add');
            }
            sectionMenu(_p('ynsocialstore_open_new_store'), 'social-store/store/storetype');
        }
    }
}