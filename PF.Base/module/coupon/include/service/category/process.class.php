<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_Coupon
 * @version        3.01
 */
class Coupon_Service_Category_Process extends Phpfox_Service
{
    private $_iStringLengthCategoryName;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('coupon_category');
        $this->_iStringLengthCategoryName = 40;
    }

    public function updateOrder($aVals)
    {
        foreach ($aVals as $iId => $iOrder) {
            $this->database()->update($this->_sTable, array('ordering' => $iOrder), 'category_id = ' . (int)$iId);
        }

        $this->cache()->remove('coupon', 'substr');

        return true;
    }

    public function delete($iId)
    {
        $this->database()->delete($this->_sTable, 'category_id = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('coupon_category_data'), 'category_id = ' . (int)$iId);
        $this->cache()->remove('coupon', 'substr');
        return true;
    }

    public function add($aVals)
    {
        $aLanguages = Phpfox::getService('language')->getAll();
        $name = $aVals['name_'.$aLanguages[0]['language_id']];
        $phrase_var_name = 'coupon_category_' . md5('Coupon Category'. $name . PHPFOX_TIME);

        //Add phrases
        $aText = [];
        foreach ($aLanguages as $aLanguage){
            if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
                Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
            }
            else {
                return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
            }

            if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthCategoryName) {
                return Phpfox_Error::set(_p('Category "{{ language_name }}" name must be less than {{ limit }}', ['limit' => $this->_iStringLengthCategoryName, 'language_name' => $aLanguage['title']]));
            }
        }

        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];

        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);

        $iId = $this->database()->insert($this->_sTable, array(
                'parent_id' => (!empty($aVals['parent_id']) ? (int)$aVals['parent_id'] : 0),
                'is_active' => 1,
                'title' => $finalPhrase,
                'time_stamp' => PHPFOX_TIME
            )
        );

        $this->cache()->remove('coupon', 'substr');

        return $iId;
    }

    public function update($iId, $aVals)
    {
        $aLanguages = Phpfox::getService('language')->getAll();
        $name = $aVals['name_'.$aLanguages[0]['language_id']];
        $phrase_var_name = 'coupon_category_' . md5('Coupon Category'. $name . PHPFOX_TIME);

        //Add phrases
        $aText = [];
        foreach ($aLanguages as $aLanguage){
            if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
                Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
            }
            else {
                return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
            }

            if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthCategoryName) {
                return Phpfox_Error::set(_p('Category "{{ language_name }}" name must be less than {{ limit }}', ['limit' => $this->_iStringLengthCategoryName, 'language_name' => $aLanguage['title']]));
            }
        }

        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];

        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);

        if ($iId == $aVals['parent_id']) {
            return Phpfox_Error::set(_p('parent_category_and_child_is_the_same'));
        }

        $this->database()->update($this->_sTable, array('title' => $finalPhrase, 'parent_id' => (int)$aVals['parent_id']), 'category_id = ' . (int)$iId);

        $this->cache()->remove('coupon', 'substr');

        return true;
    }

    public function deleteMultiple($aIds)
    {
        foreach ($aIds as $iId) {
            $this->database()->delete($this->_sTable, 'category_id = ' . (int)$iId);
            $this->deleteCouponBelongToCategory($iId);
            $this->database()->delete(Phpfox::getT('coupon_category_data'), 'category_id = ' . (int)$iId);
        }
        return true;
    }

    public function deleteCouponBelongToCategory($sId)
    {
        $aItems = $this->database()->select('d.coupon_id')
            ->from(Phpfox::getT('coupon_category_data'), 'd')
            ->where("d.category_id IN(" . $sId . ")")
            ->execute('getSlaveRows');
        if (!empty($aItems)) {
            foreach ($aItems as $aItem) {
                Phpfox::getService('coupon.process')->delete($aItem['coupon_id']);
            }
        }
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('coupon.service_category_process__call')) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    public function updateActivity($iId, $iType)
    {
        Phpfox::isAdmin(true);
        $this->database()->update(($this->_sTable), array('is_active' => (int)($iType == '1' ? 1 : 0)), 'category_id' . ' = ' . (int)$iId);

        $this->cache()->remove('coupon', 'substr');
    }

}

?>