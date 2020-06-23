<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/7/16
 * Time: 3:17 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_Logo extends Phpfox_Component
{
    public function process()
    {
        $aStore = $this->getParam('aStore');

        if (empty($aStore) || $aStore['theme_id'] == 2) {
            return false;
        }

        $oInput = Phpfox::getLib('parse.input');
        if (count($aStore['address']) > 0) {
            $aStore['address'] = $aStore['address'][0];
            if (!empty($aStore['address']['location'])) {
                $aStore['address']['address'] = $aStore['address']['location'];
            }
        } else {
            $aStore['address'] = null;
        }

        // Get first location

        $sUrlAbouUs = $this->url()->makeUrl('ynsocialstore.store', [$aStore['store_id'], $oInput->cleanTitle($aStore['name']), 'aboutus'], true);

        $this->template()->assign(array(
            'aStoreLogo' => $aStore,
            'sCorePath' => Phpfox::getParam('core.path'),
            'sUrlAbouUs' => $sUrlAbouUs,
            'sHeader' => _p('logo'),
            'aFooter' => array(
                _p('view_more_abouts_us') => $sUrlAbouUs
            )
        ));

        return 'block';
    }
}