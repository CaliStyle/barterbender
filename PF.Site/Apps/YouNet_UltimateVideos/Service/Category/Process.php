<?php

/**
 * [PHPFOX_HEADER]
 */

namespace Apps\YouNet_UltimateVideos\Service\Category;

use Phpfox;
use Phpfox_Service;
use Phpfox_Pages_Category;

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_Coupon
 * @version        3.01
 */
class Process extends \Core_Service_Systems_Category_Process
{

    /**
     * Class constructor
     */
    private $_iStringLengthCategoryName;

    public function __construct()
    {
        parent::__construct();
        $this->_sTable = Phpfox::getT('ynultimatevideo_category');
        $this->_iStringLengthCategoryName = 40;
    }

    public function updateOrder($aVals)
    {
        foreach ($aVals as $iId => $iOrder) {
            $this->database()->update($this->_sTable, array('ordering' => $iOrder), 'category_id = ' . (int)$iId);
        }

        $this->cache()->remove('ynultimatevideo');

        return true;
    }

    public function delete($iId)
    {
        $aCategory = Phpfox::getService('ultimatevideo.category')->getForEdit($iId);

        $aSubCategories = $this->database()->select('category_id')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int)$iId)
            ->execute('getRows');

        if (!empty($aSubCategories)) {
            $aSubCategoryIds = array();

            foreach ($aSubCategories as $aItem) {
                $aSubCategoryIds[] = array_shift($aItem);
            }

            $sSubCategories = implode(',', $aSubCategoryIds);

            $this->database()->update($this->_sTable, array('parent_id' => $aCategory['parent_id']), 'category_id IN (' . $sSubCategories . ')');
        }
        if (isset($aCategory['title']) && \Core\Lib::phrase()->isPhrase($aCategory['title'])) {
            Phpfox::getService('language.phrase.process')->delete($aCategory['title'], true);
        }
        $this->database()->update($this->_sTable, array('parent_id' => 0), 'parent_id = ' . (int)$iId);
        //$this->database()->update(Phpfox::getT('ynultimatevideo_videos'), array('category_id' => 0), 'category_id = ' . (int)$iId);
        $this->database()->delete($this->_sTable, 'category_id = ' . (int)$iId);
        $this->cache()->remove('ynultimatevideo');
        return true;
    }

    public function add($aVals, $sName = 'name')
    {
        $aLanguages = Phpfox::getService('language')->getAll();
        $name = $aVals['name_' . $aLanguages[0]['language_id']];
        $phrase_var_name = 'ultimatevideo_category_' . md5('Ultimate Video Category' . $name . PHPFOX_TIME);

        $iLimit = 40;

        //Add phrases
        $aText = [];
        foreach ($aLanguages as $aLanguage) {
            if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                $aText[$aLanguage['language_id']] = strip_tags($aVals['name_' . $aLanguage['language_id']]);
            } else {
//                return \Phpfox_Error::set((_p('provide_a_language_name_name', ['language_name' => $aLanguage['title']])));
                $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguages[0]['language_id']];
            }

            if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthCategoryName) {
                return \Phpfox_Error::set(_p('category_language_name_name_must_beLess_than_limit', ['limit' => $iLimit, 'language_name' => $aLanguage['title']]));
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

        $this->cache()->remove('ynultimatevideo');

        return $iId;
    }

    /**
     * @param array $iId
     * @param string $aVals
     * @return bool
     * @throws \Exception
     */
    public function update($iId, $aVals)
    {
        $aLanguages = Phpfox::getService('language')->getAll();
        if (\Core\Lib::phrase()->isPhrase($aVals['name'])) {
            $finalPhrase = $aVals['name'];
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                    if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthCategoryName) {
                        return \Phpfox_Error::set(_p('category_language_name_name_must_beLess_than_limit', ['limit' => $this->_iStringLengthCategoryName, 'language_name' => $aLanguage['title']]));
                    }
                    $name = strip_tags($aVals['name_' . $aLanguage['language_id']]);
                    Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'], $aVals['name'], $name);
                } else {
                    return \Phpfox_Error::set((_p('provide_a_language_name_name', ['language_name' => $aLanguage['title']])));
                }
            }
        } else {
            $name = $aVals['name_' . $aLanguages[0]['language_id']];
            $phrase_var_name = 'ultimatevideo_category_' . md5('Ultimate Video Category' . $name . PHPFOX_TIME);
            //Add phrases
            $aText = [];
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                    $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
                } else {
                    return \Phpfox_Error::set((_p('provide_a_language_name_name', ['language_name' => $aLanguage['title']])));
                }

                if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthCategoryName) {
                    return \Phpfox_Error::set(_p('category_language_name_name_must_beLess_than_limit', ['limit' => $this->_iStringLengthCategoryName, 'language_name' => $aLanguage['title']]));
                }
            }

            $aValsPhrase = [
                'var_name' => $phrase_var_name,
                'text' => $aText
            ];

            $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);

        }
        if ($iId == $aVals['parent_id']) {
            return \Phpfox_Error::set(_p('parent_category_and_child_is_the_same'));
        }
        $this->database()->update($this->_sTable, array('title' => $finalPhrase, 'parent_id' => (int)$aVals['parent_id']), 'category_id = ' . (int)$iId);

        $this->cache()->remove('ynultimatevideo');

        return true;
    }

    public function updateActivity($iId, $iType, $iSub)
    {
        Phpfox::isAdmin(true);
        $this->database()->update(($this->_sTable), array('is_active' => (int)($iType == '1' ? 1 : 0)), 'category_id' . ' = ' . (int)$iId);

        $this->cache()->remove('ultimatevideo_category');
    }

    public function updateHot($iId, $iType, $iSub)
    {
        Phpfox::isAdmin(true);
        $this->database()->update(($this->_sTable), array('is_hot' => (int)($iType == '1' ? 1 : 0)), 'category_id' . ' = ' . (int)$iId);

        $this->cache()->remove('ultimatevideo_category');
    }

    /**
     * @param $sMethod
     * @param $aArguments
     * @return bool|mixed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = \Phpfox_Plugin::get('ynultimatevideo.service_category_process__call')) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        \Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);

        return true;
    }
}