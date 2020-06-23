<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 15:18
 */

namespace Apps\YNC_Affiliate\Controller\Admin;

use Admincp_Component_Controller_App_Index;
use Core\Request\Exception;
use Core_Service_Currency_Currency;
use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class ConversionRateController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();

        $aCurrencies    = Phpfox::getService('core.currency')->get();
        $aConverionRate = setting('yncaffiliate.ynaf_points_conversion_rate');
        if ($aValues = $this->request()->getArray('val'))
        {

            $aConversion['ynaf_points_conversion_rate'] = json_encode($aValues);
            if (Phpfox::getService('yncaffiliate.setting.process')->updateSetting($aConversion)) {
                $this->url()->send('admincp.yncaffiliate.conversion-rate', _p('Successfully update affiliate points conversion rate'));
            }
        }
        $this->template()->setSectionTitle(_p('Conversion Rate'))
            ->setBreadCrumb(_p('Conversion Rate'))
            ->setTitle(_p('Conversion Rate'))
            ->assign([
                'aCurrencies'    => $aCurrencies,
                'aConverionRate' => json_decode($aConverionRate,true),
            ]);

    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('yncaffiliate.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}