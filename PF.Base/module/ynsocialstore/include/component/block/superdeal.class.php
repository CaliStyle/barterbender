<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/17/16
 * Time: 10:51 AM
 */
class Ynsocialstore_Component_Block_SuperDeal extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $iStoreId = $this->getParam('iStoreId');
        $bInDetail = false;

        if ($iStoreId) {
            $bInDetail = true;
            $aStore = $this->getParam('aStore');
            $iLimit = $this->getParam('limit', 4);
            if (empty($aStore)) {
                return false;
            }
            $this->template()->assign(array(
                    'iStoreId' => $iStoreId,
                    'sStoreName' => $aStore['name'],
                )
            );
        } else {
            $iLimit = $this->getParam('limit', 3);
            if (!$iLimit) {
                return false;
            }
        }

        $sSelect = Phpfox::getService('ynsocialstore.helper')->getNormalSelectQuery() . ' AND eps.discount_percentage';
        $aCond[] = "AND ecp.product_status = 'running' AND (eps.discount_start_date <= " . PHPFOX_TIME . " AND eps.discount_end_date >= " . PHPFOX_TIME . " OR eps.discount_timeless = 1 )";
        if ($iStoreId) {
            $aCond[] = "AND st.store_id = {$iStoreId}";
        } else {
            $aCond[] = "AND st.module_id = 'ynsocialstore'";
        }
        $iCount = 0;

        $aProducts = Phpfox::getService('ynsocialstore.product')->getProducts($sSelect, $iLimit, null, $iCount, $aCond,
            'eps.discount_percentage DESC', true);

        if (!$iCount) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('super_deal'),
                'aItems' => $aProducts,
                'iCount' => count($aProducts),
                'sCorePath' => Phpfox::getParam('core.path_actual') . 'PF.Base/',
                'bInDetail' => $bInDetail,
            )
        );
        if (empty($bInDetail)) {
            $this->template()->assign(array(
                'aFooter' => array(
                        _p('view_more') => $this->url()->makeurl('ynsocialstore') . '?sort=super-deal'
                    )
            ));
        }

        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Super Deal Products Limit'),
                'description' => _p('Define the limit of how many super deal products can be displayed when viewing the social store section. Set 0 will hide this block.'),
                'value' => 3,
                'type' => 'integer',
                'var_name' => 'limit',
            ]
        ];
    }

    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => '"Super Deal Products Limit" must be greater than or equal to 0'
            ]
        ];
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        // Lets clear it from memory
        $this->template()->clean(array(
                'aItems',
                'sHeader',
                'sTypeBlock',
                'limit',
                'bIsNoModerate'
            )
        );

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_block_product_super_deal_clean')) ? eval($sPlugin) : false);
    }
}