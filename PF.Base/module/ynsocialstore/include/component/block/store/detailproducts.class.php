<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/27/16
 * Time: 4:06 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_DetailProducts extends Phpfox_Component
{
    public function process()
    {
        $aStore = $this->getParam('aStore');

        if (empty($aStore)) {
            return false;
        }

        $sType = $this->request()->get('type', 'all');
        $sSort = $this->request()->get('sort', 'latest');
        $iCategory = $this->request()->get('category');
        $sActivePhrase = str_replace('-', '_', $sSort);

        $aBrowseParams = array(
            'module_id' => 'ynsocialstore.product',
            'alias' => 'ecp',
            'field' => 'product_id',
            'table' => Phpfox::getT('ecommerce_product'),
            'hide_view' => array('my')
        );

        $this->search()->set(Phpfox::getService('ynsocialstore.helper')->getParamsSearchProduct());

        $this->search()->setCondition(' AND st.status <> "deleted"');
        $this->search()->setCondition(' AND ecp.product_status = \'running\' AND ecp.item_id = '.$aStore['store_id']);

        if ($sSort == 'super-deal') {
            $aBrowseParams['select'] = 'IF(eps.discount_end_date > '. PHPFOX_TIME .', 1, 0) AS is_product_discounting, ';
            $this->search()->setCondition(' AND st.status <> "deleted" AND (eps.discount_start_date <= ' . PHPFOX_TIME . ' AND eps.discount_end_date >= ' . PHPFOX_TIME . ' OR eps.discount_timeless = 1 )');
        }

        if (!empty($iCategory) && is_numeric($iCategory))
        {
            $this->search()->setCondition(' AND ecd.category_id = '.$iCategory);
        }

        switch ($sType)
        {
            case 'physical':
                $this->search()->setCondition(' AND eps.product_type = "physical"');
                break;
            case 'digital':
                $this->search()->setCondition(' AND eps.product_type = "digital"');
                break;
            default:
                break;
        }

        $this->search()->browse()->params($aBrowseParams)->execute();
        $aProducts = $this->search()->browse()->getRows();

        $aModerationMenu = [];
        if(Phpfox::isAdmin()){
            $aModerationMenu[] = [
                'phrase' => _p('core.delete'),
                'action' => 'deleteProduct'
            ];
        }

        if(Phpfox::getUserParam('ynsocialstore.can_feature_product')){
            $aModerationMenu[] = [
                'phrase' => _p('feature'),
                'action' => 'featureProduct'
            ];
        }

        $this->setParam('global_moderation', [
            'name' => 'ynsocialstore',
            'ajax' => 'ynsocialstore.moderation',
            'menu' => $aModerationMenu
        ]);

        $this->template()->assign(array(
                'aStore'	=> $aStore,
                'sHeader' => '',
                'aForms' => array(
                    'sType' => $sType,
                    'sSort' => $sSort,
                ),
                'aItems' => $aProducts,
                'sActivePhrase' => $sActivePhrase,
                'link' => $this->url()->permalink('ynsocialstore.store', $aStore['store_id'], $aStore['name']).'products/',
            )
        );

        return 'block';
    }
}