<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 *
 */
class Resume_Service_Category_Process extends Phpfox_Service
{
    /**
     * Class constructor
     */
    private $_iStringLengthCategoryName;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('resume_category');
        $this->_iStringLengthCategoryName = 100;
    }

    /**
     * Add new category into database
     * @param array $aVals - array of category input information
     * @return integer $iId - the id of the  inserted category
     */
    public function add($aVals)
    {
        $aLanguages = Phpfox::getService('language')->getAll();
        $name = $aVals['name_' . $aLanguages[0]['language_id']];
        $phrase_var_name = 'resume_category_' . md5('Resume Category' . $name . PHPFOX_TIME);

        $iLimit = 40;

        //Add phrases
        $aText = [];
        foreach ($aLanguages as $aLanguage) {
            if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
            } else {
                return Phpfox_Error::set((_p('provide_a_language_name_name', ['language_name' => $aLanguage['title']])));
            }

            if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthCategoryName) {
                return Phpfox_Error::set(_p('category_language_name_name_must_be_less_than_limit', ['limit' => $iLimit, 'language_name' => $aLanguage['title']]));
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
                'name' => $finalPhrase,
                'time_stamp' => PHPFOX_TIME
            )
        );

        $this->cache()->remove();

        return $iId;
    }

    /**
     * Update order of category list
     * @param array $aVals - array of category order
     * @return true
     */
    public function updateOrder($aVals)
    {
        foreach ($aVals as $iId => $iOrder) {
            $this->database()->update($this->_sTable, array('ordering' => $iOrder), 'category_id = ' . (int)$iId);
        }

        $this->cache()->remove();

        return true;
    }

    /**
     * Update category data
     */
    public function update($iId, $aVals)
    {
        $aLanguages = Phpfox::getService('language')->getAll();
        if (Phpfox::isPhrase($aVals['name'])) {
            $finalPhrase = $aVals['name'];
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                    if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthCategoryName) {
                        return Phpfox_Error::set(_p('category_language_name_name_must_be_less_than_limit', ['limit' => $this->_iStringLengthCategoryName, 'language_name' => $aLanguage['title']]));
                    }
                    $name = $aVals['name_' . $aLanguage['language_id']];
                    Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'], $aVals['name'], $name);
                } else {
                    return Phpfox_Error::set((_p('provide_a_language_name_name', ['language_name' => $aLanguage['title']])));
                }
            }
        } else {
            $name = $aVals['name_' . $aLanguages[0]['language_id']];
            $phrase_var_name = 'resume_category_' . md5('Resume Category' . $name . PHPFOX_TIME);
            //Add phrases
            $aText = [];
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                    $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
                } else {
                    return Phpfox_Error::set((_p('provide_a_language_name_name', ['language_name' => $aLanguage['title']])));
                }

                if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthCategoryName) {
                    return Phpfox_Error::set(_p('category_language_name_name_must_be_less_than_limit', ['limit' => $this->_iStringLengthCategoryName, 'language_name' => $aLanguage['title']]));
                }
            }

            $aValsPhrase = [
                'var_name' => $phrase_var_name,
                'text' => $aText
            ];

            $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);

        }
        if ($iId == $aVals['parent_id']) {
            return Phpfox_Error::set(_p('parent_category_and_child_is_the_same'));
        }
        $this->database()->update($this->_sTable, array('name' => $finalPhrase, 'parent_id' => (int)$aVals['parent_id']), 'category_id = ' . (int)$iId);

        $this->cache()->remove();

        return true;
    }

    /**
     * Delete a category
     * @param integer $iId - the id of the category needed to be deleted
     * @return true
     */
    public function delete($iId)
    {
        $aCategory = Phpfox::getService('resume.category')->getForEdit($iId);

        $aSubCategories = $this->database()->select('category_id')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int) $iId)
            ->execute('getRows');

        if (!empty($aSubCategories))
        {
            $aSubCategoryIds = array();

            foreach ($aSubCategories as $aItem)
            {
                $aSubCategoryIds[] = array_shift($aItem);
            }

            $sSubCategories = implode(',', $aSubCategoryIds);

            $this->database()->update($this->_sTable, array('parent_id' => $aCategory['parent_id']), 'category_id IN ('.$sSubCategories.')');
        }
        if (isset($aCategory['name']) && Phpfox::isPhrase($aCategory['name'])){
            Phpfox::getService('language.phrase.process')->delete($aCategory['name'], true);
        }
        $this->database()->delete($this->_sTable, 'category_id = ' . (int) $iId);
        $this->cache()->remove();
        return true;
    }

    /**
     * Add data for category
     * @param $resum_id : id of resume which you are editing or creating
     * @param $category_id : id of category is added.
     */
    public function addCategorydata($resume_id, $category_id)
    {
        $this->database()->insert(Phpfox::getT('resume_category_data'), array(
            'resume_id' => $resume_id,
            'category_id' => $category_id
        ));
    }

    /**
     * Delete all Category data if have resume id in params
     */
    public function deleteAllCategorydata($resume_id)
    {
        return $this->database()->delete(Phpfox::getT('resume_category_data'), 'resume_id=' . $resume_id);
    }

    /**
     * increase or except one unit on category table
     */
    public function updateUsedCategory($cagegory_id, $unit)
    {
        $sSql = 'update ' . Phpfox::getT('resume_category') . ' set used=used+' . $unit . ' where category_id=' . $cagegory_id;
        Phpfox::getLib("database")->query($sSql);
    }

    public function updateActivity($iId, $iType, $iSub)
    {
        Phpfox::isAdmin(true);
        $this->database()->update(($this->_sTable), array('is_active' => (int)($iType == '1' ? 1 : 0)), 'category_id' . ' = ' . (int)$iId);

        $this->cache()->remove();
    }
}


?>