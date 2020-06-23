<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
namespace Apps\Advancedfooter\Service;

use Core;
use Phpfox;
use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');

class Menu extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('advancedfooter_menu');
    }

    public function addMenu($aVals)
    {
        //Add phrase for category
        $aLanguages = Phpfox::getService('language')->getAll();
        $name = $aVals['name_' . $aLanguages[0]['language_id']];
        $phrase_var_name = 'advancedfooter_menu_' . md5('Advanced footer Category' . $name . PHPFOX_TIME);

        //Add phrases
        $aText = [];
        foreach ($aLanguages as $aLanguage) {
            if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
            } else {
                return \Phpfox_Error::set((_p('provide_a_language_name_name',
                    ['language_name' => $aLanguage['title']])));
            }
        }
        $aValsPhrase = [
            'product_id' => 'phpfox',
            'module' => 'advancedfooter',
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];
        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);

        $iId = db()->insert(Phpfox::getT('advancedfooter_menu'), [
            'name' => $finalPhrase,
            'time_stamp' => PHPFOX_TIME,
            'ordering' => '0',
            'link' => $aVals['link'],
            'direct_link' => $aVals['direct_link'],
            'parent_id' => (!empty($aVals['parent_id']) ? (int)$aVals['parent_id'] : 0),
            'is_active' => '1'
        ]);
        
        return $iId;
    }

    /**
     * @param $iCategoryId
     * @param array $aVals
     * @return bool
     */
    public function deleteCategory($iCategoryId, $aVals = array())
    {
        $aCategory = db()->select('*')
            ->from(Phpfox::getT('advancedfooter_menu'))
            ->where('category_id=' . intval($iCategoryId))
            ->execute('getSlaveRow');

        db()->delete(Phpfox::getT('advancedfooter_menu'), 'category_id = ' . intval($iCategoryId));

        return true;
    }

    /**
     * @param $iId
     * @param $aVals
     * @return bool
     */
    public function updateMenu($iId, $aVals)
    {
        $aLanguages = Phpfox::getService('language')->getAll();
        if (Phpfox::isPhrase($aVals['name'])) {
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']])) {
                    $name = $aVals['name_' . $aLanguage['language_id']];
                    Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'],
                        $aVals['name'], $name);
                }
            }
        } else {
            //Add new phrase if before is not phrase
            $name = $aVals['name_' . $aLanguages[0]['language_id']];
            $phrase_var_name = 'advancedfooter_category_' . md5('Advanced Footer Category' . $name . PHPFOX_TIME);
            $aText = [];
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                    $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
                } else {
                    Phpfox_Error::set((_p('provide_a_language_name_name',
                        ['language_name' => $aLanguage['title']])));
                }
            }
            $aValsPhrase = [
                'product_id' => 'phpfox',
                'module' => 'advancedfooter',
                'var_name' => $phrase_var_name,
                'text' => $aText
            ];
            $aVals['name'] = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        }

        db()->update(Phpfox::getT('advancedfooter_menu'), array(
            'parent_id' => (!empty($aVals['parent_id']) ? (int)$aVals['parent_id'] : 0),
            'name' => $aVals['name'],
            'link' => $aVals['link'],
            'direct_link' => $aVals['direct_link']
        ), 'category_id = ' . (int)$iId
        );

        return true;
    }

    public function updateActivity($iId, $iType)
    {
        Phpfox::isAdmin(true);
        db()->update((Phpfox::getT('advancedfooter_menu')), array('is_active' => (int)($iType == '1' ? 1 : 0)),
            'category_id = ' . (int)$iId);
        // clear cache
    }

    public function getForAdmin(
        $iParentId = 0,
        $bGetSub = 1,
        $bCareActive = 0,
        $notInclude = 0
    ) {
        $aRows = db()->select('*')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int)$iParentId . ($bCareActive ? ' AND is_active = 1' : '') . ' AND category_id <> ' . $notInclude)
            ->order('ordering ASC')
            ->execute('getSlaveRows');

        if ($bGetSub) {
            foreach ($aRows as $iKey => $aRow) {
                $aRows[$iKey]['sub'] = $this->getForAdmin($aRow['category_id'], 1, $bCareActive, $notInclude, 0);
            }
        }

        if (is_array($aRows)) {
            foreach ($aRows as $iKey => $aCategory) {
                $aRows[$iKey]['name'] = _p($aCategory['name']);

            }
        }

        return $aRows;
    }

    /**
     * @param int $iParentId
     * @param int $bGetSub
     * @param int $bCareActive
     * @param int $iCacheTime
     * @return array|int|string
     */
    public function getForUsers($iParentId = 0, $bGetSub = 1, $bCareActive = 1, $iCacheTime = 5)
    {
        return $this->getForAdmin($iParentId, $bGetSub, $bCareActive, 0, 1, $iCacheTime);
    }

    /**
     * @param $iId
     * @return array|bool|int|string
     */
    public function getForEdit($iId)
    {
        $aRow = db()->select('*')
            ->from($this->_sTable)
            ->where('category_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aRow['category_id'])) {
            return false;
        }

        if (!isset($aRow['category_id'])) {
            return false;
        }
        $aLanguages = Phpfox::getService('language')->getAll();
        foreach ($aLanguages as $aLanguage) {
            $sPhraseValue = (Core\Lib::phrase()->isPhrase($aRow['name'])) ? _p($aRow['name'], [],
                $aLanguage['language_id']) : $aRow['name'];
            $aRow['name_' . $aLanguage['language_id']] = $sPhraseValue;
        }


        return $aRow;
    }

    /**
     * @param $iCategoryId
     * @return array|bool|int|string
     */
    public function getCategory($iCategoryId)
    {
        $aCategory = db()->select('*')
            ->from($this->_sTable)
            ->where('category_id = ' . (int)$iCategoryId)
            ->execute('getSlaveRow');

        return (isset($aCategory['category_id']) ? $aCategory : false);
    }

    public function getSocial()
    {
        return [
            'amazon' => [
                'icon' => 'amazon',
                'phrase' => _p('Amazon')
            ],
            'apple' => [
                'icon' => 'apple',
                'phrase' => _p('Apple Music')
            ],
            'facebook' => [
                'icon' => 'facebook',
                'phrase' => _p('Facebook')
            ],
            'google' => [
                'icon' => 'google',
                'phrase' => _p('Google Play')
            ],
            'instagram' => [
                'icon' => 'instagram',
                'phrase' => _p('Instagram')
            ],
            'linkedin' => [
                'icon' => 'linkedin',
                'phrase' => _p('Linkedin')
            ],
            'twitter' => [
                'icon' => 'twitter',
                'phrase' => _p('Twitter')
            ],
            'snapchat' => [
                'icon' => 'snapchat',
                'phrase' => _p('Snapchat')
            ],
            'soundcloud' => [
                'icon' => 'soundcloud',
                'phrase' => _p('SoundCloud')
            ],
            'spotify' => [
                'icon' => 'spotify',
                'phrase' => _p('Spotify')
            ],
            'youtube' => [
                'icon' => 'youtube-play',
                'phrase' => _p('YouTube')
            ],
            'vimeo' => [
                'icon' => 'vimeo',
                'phrase' => _p('Vimeo')
            ],
            'pinterest' => [
                'icon' => 'pinterest',
                'phrase' => _p('Pinterest')
            ],
            'website' => [
                'icon' => 'globe',
                'phrase' => _p('Website')
            ],
            'store' => [
                'icon' => 'shopping-cart',
                'phrase' => _p('Online Store')
            ],
            'wechat' => [
                'icon' => 'wechat',
                'phrase' => _p('WeChat')
            ]
        ];
    }


    /**
     * @param $iCategory
     * @return mixed|string
     */
    public function getStringParentCategories($iCategory)
    {
        $aCategory = $this->getCategory($iCategory);

        if (empty($aCategory['parent_id'])) {
            return $aCategory['category_id'];
        } else {
            return ($this->getStringParentCategories($aCategory['parent_id']) . ',' . $aCategory['category_id']);
        }
    }

    /**
     * @param $iCategory
     * @return array
     */


    public function getParentCategories($iCategory)
    {
        $sCategories = $this->getStringParentCategories($iCategory);
        $aCategories = db()
            ->select('*')
            ->from($this->_sTable)
            ->where('category_id IN(' . $sCategories . ')')
            ->execute('getSlaveRows');

        return $aCategories;
    }
}
