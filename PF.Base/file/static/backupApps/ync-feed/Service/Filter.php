<?php

namespace Apps\YNC_Feed\Service;

use Core;
use Phpfox;
use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');

class Filter extends Phpfox_Service
{
    private $_iSize;
    private $_iStringLengthFilterName;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynfeed_filter');
        $this->_iSize = 16;
        $this->_iStringLengthFilterName = 40;
    }

    /**
     * @param int $bCareActive
     * @return array|int|string
     */
    public function getForAdmin($bCareActive = 0)
    {
        $aRows = db()->select('*')
            ->from($this->_sTable, 'f')
            ->innerJoin(':module', 'm', 'm.module_id = f.module_id')
            ->where(($bCareActive ? ' is_show = 1 AND m.is_active = 1' : 'm.is_active = 1'))
            ->order('ordering ASC')
            ->execute('getSlaveRows');
        return $aRows;
    }

    /**
     * @param int $bCareActive
     * @return array|int|string
     */
    public function getForUsers($bCareActive = 1)
    {
        return $this->getForAdmin($bCareActive);
    }

    /**
     * @param $iId
     * @return array|bool|int|string
     */
    public function getForEdit($iId)
    {
        $aRow = db()->select('*')
            ->from($this->_sTable)
            ->where('filter_id = ' . (int) $iId)
            ->execute('getSlaveRow');

        if (!isset($aRow['filter_id']))
        {
            return false;
        }

        //Support legacy phrases
        if (substr($aRow['title'], 0, 7) == '{phrase' && substr($aRow['title'], -1) == '}') {
            $aRow['title'] = preg_replace('/\s+/', ' ', $aRow['title']);
            $aRow['title'] = str_replace([
                "{phrase var='",
                "{phrase var=\"",
                "'}",
                "\"}"
            ], "", $aRow['title']);
        }//End support legacy
        $aLanguages = Phpfox::getService('language')->getAll();
        foreach ($aLanguages as $aLanguage){
            $sPhraseValue = (Core\Lib::phrase()->isPhrase($aRow['title'])) ? _p($aRow['title'], [], $aLanguage['language_id']) : $aRow['title'];
            $aRow['name_' . $aLanguage['language_id']] = $sPhraseValue;
        }

        return $aRow;
    }

    /**
     * @return array
     */
    public function getModulesForAddFilter()
    {
        $aFiltersAdded = $this -> getForAdmin();
        $aModulesAdded = ['ad','core','admincp','announcement','api','attachment','ban','captcha','contact','comment','contact','custom','ecommerce','egift','error','friend','invite','language','like','link','log','mail','notification','newsletter','privacy','profile','report','request','rss','search','share','subscribe','tag','theme','track','ynfeed'];
        foreach ($aFiltersAdded as $item) {
            $aModulesAdded[] = $item['module_id'];
        }
        $sModulesAdded = join("','", $aModulesAdded);
        $aRows = db()->select('m.module_id')
            ->from(Phpfox::getT('module'), 'm')
            ->leftJoin(Phpfox::getT('product'), 'p', 'p.product_id = m.product_id')
            ->where(count($aModulesAdded)?"module_id NOT IN ('$sModulesAdded') AND m.is_active = 1":"m.is_active = 1")
            ->order('m.module_id ASC')
            ->execute('getSlaveRows');

        $aModules = [];
        foreach ($aRows as $aRow) {
            $aModules[$aRow['module_id']] = _p($aRow['module_id']);
        }

        return $aModules;
    }

    /**
     * @param $iId
     * @param $aVals
     * @return bool
     */
    public function updateFilter($iId, $aVals)
    {
        $aLanguages = Phpfox::getService('language')->getAll();
        // Update phrase
        if (Phpfox::isPhrase($aVals['name'])){
            foreach ($aLanguages as $aLanguage){
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($name = strip_tags($aVals['name_' . $aLanguage['language_id']]))){
                    Phpfox::getService('ban')->checkAutomaticBan($name);

                    Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'], $aVals['name'], $name);
                }
                else {
                    return \Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
                }

                if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthFilterName) {
                    return \Phpfox_Error::set(_p('Filter name "{{ language_name }}" name must be less than {{ limit }}', ['limit' => $this->_iStringLengthFilterName, 'language_name' => $aLanguage['title']]));
                }
            }
        } else {
            $name = $aVals['name_'.$aLanguages[0]['language_id']];
            $phrase_var_name = 'ynfeed_filter_' . md5('Adv Feed Filter'. $name . PHPFOX_TIME);

            //Validate phrases
            $aText = [];
            foreach ($aLanguages as $aLanguage){
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($name = strip_tags($aVals['name_' . $aLanguage['language_id']]))){
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                    $aText[$aLanguage['language_id']] = $name;
                }
                else {
                    return \Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
                }

                if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthFilterName) {
                    return \Phpfox_Error::set(_p('Filter name "{{ language_name }}" name must be less than {{ limit }}', ['limit' => $this->_iStringLengthFilterName, 'language_name' => $aLanguage['title']]));
                }
            }

            $aValsPhrase = [
                'var_name' => $phrase_var_name,
                'text' => $aText
            ];

            $aVals['name'] = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        }

        db()->update($this->_sTable, array('title' => $aVals['name']), 'filter_id = ' . (int) $iId);

        return true;
    }

    /**
     * @param $aVals
     * @return bool|int
     */
    public function addFilter($aVals)
    {
        if (empty($aVals['module_id'])) {
            return \Phpfox_Error::set(_p('please_choose_a_module_or_app'));
        }
        $aLanguages = Phpfox::getService('language')->getAll();
        $name = $aVals['name_'.$aLanguages[0]['language_id']];
        $phrase_var_name = 'ynfeed_filter_' . md5('Adv Feed Filter'. $name . PHPFOX_TIME);

        //Add phrases
        $aText = [];
        foreach ($aLanguages as $aLanguage){
            if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
                Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                $aText[$aLanguage['language_id']] = strip_tags($aVals['name_' . $aLanguage['language_id']]);
            }
            else {
                return \Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
            }

            if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthFilterName) {
                return \Phpfox_Error::set(_p('Filter name "{{ language_name }}" name must be less than {{ limit }}', ['limit' => $this->_iStringLengthFilterName, 'language_name' => $aLanguage['title']]));
            }
        }
        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];

        $finalPhrase = \Phpfox::getService('language.phrase.process')->add($aValsPhrase);

        $iId = db()->insert($this->_sTable, array(
                'module_id' => (!empty($aVals['module_id']) ? $aVals['module_id'] : ''),
                'type' => (!empty($aVals['module_id']) ? $aVals['module_id'] : ''),
                'is_show' => 1,
                'title' => $finalPhrase
            )
        );
        return $iId;
    }

    /**
     * @param int $iFilterId
     *
     * @return true
     */
    public function delete($iFilterId){
        $aFilter = db()->select('*')
            ->from($this->_sTable)
            ->where('filter_id=' . (int) $iFilterId)
            ->execute('getSlaveRow');
        if (isset($aFilter['name']) && Phpfox::isPhrase($aFilter['name'])){
            Phpfox::getService('language.phrase.process')->delete($aFilter['name'], true);
        }
        db()->delete($this->_sTable, 'filter_id = ' . (int) $iFilterId);
        return true;
    }

    /**
     * Active or De-active a blog category
     *
     * @param int $iFilterId
     * @param $iShow
     * @internal param int $iActive
     */
    public function toggleFilter($iFilterId, $iShow)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);
        db()->update($this->_sTable, [
            'is_show' => (int)($iShow == '1' ? 1 : 0)
        ], 'filter_id = ' . (int)$iFilterId);
    }

    public function isSavedFilterEnabled() {
        if (Phpfox::getUserBy('profile_page_id') > 0){
            return 0;
        }
        return (int) db()->select('is_show')->from($this->_sTable)->where([
            'module_id' => 'ynfeed',
            'type' => 'user_saved'
        ])->execute('getField');
    }
}