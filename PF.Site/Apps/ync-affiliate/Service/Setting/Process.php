<?php

/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 10:44
 */

namespace Apps\YNC_Affiliate\Service\Setting;

use Phpfox;

Class Process extends \Phpfox_Service
{
    private $_productId;
    private $_moduleId;
    private $_versionId;

    public function __construct()
    {
        $this->_sTable = \Phpfox::getT('setting');
        $this->_moduleId = 'yncaffiliate';
        $this->_productId = 'YNC_Affiliate';
        $this->_versionId = '4.5.1';
    }

    public function updateSetting($aSetting)
    {
        if ($aSetting) {
            foreach ($aSetting as $key => $settingValue) {
                //Check the setting is exist
                $iCnt = Phpfox::getLib('database')->select('COUNT(*)')
                    ->from($this->_sTable)
                    ->where(['var_name' => $key])
                    ->executeField();
                if ($iCnt) {
                    //Update setting value
                    Phpfox::getLib('database')->update($this->_sTable, [
                        'value_actual'    => $settingValue,
                        'value_default'   => $settingValue
                    ], [
                        'var_name' => $key
                    ]);
                } else {
                    //Add new setting value
                    Phpfox::getLib('database')->insert($this->_sTable, [
                        'module_id'       => $this->_moduleId,
                        'product_id'      => $this->_productId,
                        'is_hidden'       => 1,
                        'version_id'      => $this->_versionId,
                        'type_id'         => 'input:text',
                        'var_name'        => $key,
                        'phrase_var_name' => $key,
                        'value_actual'    => $settingValue,
                        'value_default'   => $settingValue
                    ]);
                }
            }
            $this->cache()->remove('setting','substr');
            return true;
        }

        return false;
    }
}