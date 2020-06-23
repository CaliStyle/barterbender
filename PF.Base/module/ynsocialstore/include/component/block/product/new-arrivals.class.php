<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/28/16
 * Time: 14:59
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Product_New_Arrivals extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $hideBlock = $this->getParam('hideBlock', false);

        if($hideBlock) {
            return false;
        }

        $iLimit = $this->getParam('limit', 4);
        if (!$iLimit) {
            return false;
        }
        $iStoreId = 0;
        $sHeader = _p('new_arrivals');
        $bIsDetail = false;
        if ($this->request()->getInt('req3') > 0 && $this->request()->get('req2') == 'store') {
            $iStoreId = $this->request()->getInt('req3');
            $iLimit = $this->getParam('limit', 6);
            $sHeader = _p('new_products');
            $bIsDetail = true;
        }

        $sSelect = Phpfox::getService('ynsocialstore.helper')->getNormalSelectQuery() . ', eps.total_rating, eps.rating, ecp.total_review';
        $aCond[] = "AND ecp.product_status = 'running'";
        if ($iStoreId) {
            $aCond[] = "AND st.store_id = {$iStoreId}";
        } else {
            $aCond[] = "AND st.module_id = 'ynsocialstore'";
        }
        $iCount = 0;

        $aNewProducts = Phpfox::getService('ynsocialstore.product')->getProducts($sSelect, $iLimit, null, $iCount, $aCond, 'ecp.product_creation_datetime DESC', true);

        if(!$bIsDetail && empty($aNewProducts))
            return false;

        $this->template()->assign(array(
              'sHeader' => $iCount ? $sHeader : '',
              'aNewProducts' => $aNewProducts,
              'sCorePath' => Phpfox::getParam('core.path_actual').'PF.Base/',
              'bIsNoModerate' => true,
          ));

        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('ynsocialstore_new_arrivals_limit'),
                'description' => _p('ynsocialstore_new_arrivals_limit_description'),
                'value' => 4,
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
                'title' => _p('ynsocialstore_new_arrivals_limit_validation')
            ]
        ];
    }
}