<?php

namespace Apps\P_AdvMarketplace\Service\Category;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;
use Phpfox_Plugin;

class Process extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('advancedmarketplace_category');
        $this->_aLanguages = Phpfox::getService('language')->getAll();
    }

    protected function addPhrase($aVals, $sName = 'name', $bVerify = true)
    {
        $langId = current($this->_aLanguages)['language_id'];
        $aFirstLang = end($this->_aLanguages);

        //Add phrases
        $aText = [];
        //Verify name

        foreach ($this->_aLanguages as $aLanguage) {
            if (isset($aVals[$sName . '_' . $aLanguage['language_id']]) && !empty($aVals[$sName . '_' . $aLanguage['language_id']])) {
                $aText[$aLanguage['language_id']] = $aVals[$sName . '_' . $aLanguage['language_id']];
            } elseif (isset($aVals[$sName . '_' . $langId]) && !empty($aVals[$sName . '_' . $langId])) {
                $aText[$aLanguage['language_id']] = $aVals[$sName . '_' . $langId];
            } elseif ($bVerify) {
                return Phpfox_Error::set(_p('provide_a_language_name_label',
                    ['language_name' => $aLanguage['title'], 'label' => $sName]));
            } else {
                $bReturnNull = true;
            }
        }
        if (isset($bReturnNull) && $bReturnNull) {
            //If we don't verify value, phrase can't be empty. Return null for this case.
            return null;
        }
        $name = $aVals[$sName . '_' . $aFirstLang['language_id']];
        $phrase_var_name = 'marketplace_category_' . md5($name . PHPFOX_TIME);

        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];

        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        return $finalPhrase;
    }

    public function add($aVals, $sName = 'name')
    {
        $sImageName = md5(PHPFOX_TIME . 'advancedmarketplace') . '%s.jpg';
        $sAdvMarketplaceImageDir = Phpfox::getParam('advancedmarketplace.dir_pic');
        $oImage = Phpfox::getLib('image');
        $aSizes = array(120, 200, 250);
        $iFileSizes = 0;

        $aImage = Phpfox::getLib('file')->load('file', array(
            'jpg',
            'gif',
            'png'
        ), 1024);

        if ($aImage) {
            $sFileName = Phpfox::getLib('file')->upload('file', $sAdvMarketplaceImageDir, $sImageName);
            $iFileSizes += filesize(Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                    ''));

            foreach ($aSizes as $iSize) {
                $oImage->createThumbnail(Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                        ''), Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                        '_' . $iSize), $iSize, $iSize);
                $oImage->createThumbnail(Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                        ''), Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                        '_' . $iSize . '_square'), $iSize, $iSize, false);

                $iFileSizes += filesize(Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($sFileName,
                        '_' . $iSize));
            }
        }

        $oParseInput = Phpfox::getLib('parse.input');
        $finalPhrase = $this->addPhrase($aVals, $sName);
        $iCategoryId = $this->database()->insert($this->_sTable, [
            'parent_id' => (!empty($aVals['parent_id']) ? (int)$aVals['parent_id'] : 0),
            'is_active' => 1,
            'name' => $finalPhrase,
            'name_url' => $oParseInput->cleanTitle($finalPhrase),
            'image_path' => !empty($sFileName) ? $sFileName : null,
            'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
            'time_stamp' => PHPFOX_TIME
        ]);

        $this->cache()->removeGroup('advmarketplace_category');
        if (empty($aVals['parent_id'])) {
            $this->cache()->remove('advmarketplace_parent_categories');
        }
        return $iCategoryId;

    }

    public function update($iId, $aVals)
    {
        if ($aVals['parent_id'] != 0) {
            $this->deleteCategoryImage($iId);
        }

        if ($iId) {
            $imagePath = $this->database()->select('t.image_path')
                ->from(Phpfox::getT('advancedmarketplace_category'), 't')
                ->where('category_id = ' . $iId)
                ->execute('getSlaveField');
        }

        $aImage = Phpfox::getLib('file')->load('file', array(
            'jpg', 'gif', 'png'
        ), 1024);

        if ($aImage) {
            if ($imagePath) {
                $aImages = array(
                    Phpfox::getParam('core.dir_pic') . 'advancedmarketplace' . PHPFOX_DS . sprintf($imagePath, ''),
                    Phpfox::getParam('core.dir_pic') . 'advancedmarketplace' . PHPFOX_DS . sprintf($imagePath, '_120'),
                    Phpfox::getParam('core.dir_pic') . 'advancedmarketplace' . PHPFOX_DS . sprintf($imagePath, '_200'),
                    Phpfox::getParam('core.dir_pic') . 'advancedmarketplace' . PHPFOX_DS . sprintf($imagePath, '_250'),
                    Phpfox::getParam('core.dir_pic') . 'advancedmarketplace' . PHPFOX_DS . sprintf($imagePath, '_120_square'),
                    Phpfox::getParam('core.dir_pic') . 'advancedmarketplace' . PHPFOX_DS . sprintf($imagePath, '_200_square'),
                    Phpfox::getParam('core.dir_pic') . 'advancedmarketplace' . PHPFOX_DS . sprintf($imagePath, '_250_square')
                );
                foreach ($aImages as $sImage) {
                    if (file_exists($sImage)) {
                        @unlink($sImage);
                    }
                }
            }
            $sAdvMarketplaceImageDir = Phpfox::getParam('core.dir_pic') . 'advancedmarketplace' . PHPFOX_DS;

            if (!is_dir($sAdvMarketplaceImageDir)) {
                @mkdir($sAdvMarketplaceImageDir, 0777, 1);
                @chmod($sAdvMarketplaceImageDir, 0777);
            }
            $sNewFileName = Phpfox::getLib('file')->upload('imageUpload', $sAdvMarketplaceImageDir, PHPFOX_TIME);
            Phpfox::getLib('image')->createThumbnail($sAdvMarketplaceImageDir . sprintf($sNewFileName, ''), $sAdvMarketplaceImageDir . sprintf($sNewFileName, '_' . 120), 120, 120);
            Phpfox::getLib('image')->createThumbnail($sAdvMarketplaceImageDir . sprintf($sNewFileName, ''), $sAdvMarketplaceImageDir . sprintf($sNewFileName, '_' . 200), 200, 200);
            Phpfox::getLib('image')->createThumbnail($sAdvMarketplaceImageDir . sprintf($sNewFileName, ''), $sAdvMarketplaceImageDir . sprintf($sNewFileName, '_' . 250), 250, 250);
            Phpfox::getLib('image')->createThumbnail($sAdvMarketplaceImageDir . sprintf($sNewFileName, ''), $sAdvMarketplaceImageDir . sprintf($sNewFileName, '_' . 120 . '_square'), 120, 120, false);
            Phpfox::getLib('image')->createThumbnail($sAdvMarketplaceImageDir . sprintf($sNewFileName, ''), $sAdvMarketplaceImageDir . sprintf($sNewFileName, '_' . 200 . '_square'), 200, 200, false);
            Phpfox::getLib('image')->createThumbnail($sAdvMarketplaceImageDir . sprintf($sNewFileName, ''), $sAdvMarketplaceImageDir . sprintf($sNewFileName, '_' . 250 . '_square'), 250, 250, false);
        }

        $aLanguages = Phpfox::getService('language')->getAll();
        if (Phpfox::isPhrase($aVals['name'])) {
            $finalPhrase = $aVals['name'];
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                    Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'],
                        $aVals['name'], $aVals['name_' . $aLanguage['language_id']]);
                } else {
                    return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.',
                        ['language_name' => $aLanguage['title']])));
                }
                if (strlen($aVals['name_' . $aLanguage['language_id']]) > 255) {
                    return Phpfox_Error::set(_p('Category "{{ language_name }}" name must be less than {{ limit }}',
                        ['limit' => 255, 'language_name' => $aLanguage['title']]));
                }
            }
        } else {
            $name = $aVals['name_' . $aLanguages[0]['language_id']];
            $phrase_var_name = 'marketplace_category_' . md5('Marketplace Category' . $name . PHPFOX_TIME);
            $aText = [];
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                    $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
                } else {
                    return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.',
                        ['language_name' => $aLanguage['title']])));
                }
                if (strlen($aVals['name_' . $aLanguage['language_id']]) > 255) {
                    return Phpfox_Error::set(_p('Category "{{ language_name }}" name must be less than {{ limit }}',
                        ['limit' => 255, 'language_name' => $aLanguage['title']]));
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

        $iOldParentId = db()->select('parent_id')->from($this->_sTable)->where(['category_id' => $iId])->executeField();
        if ($iOldParentId != $aVals['parent_id']) {
            $sListingsInChildCategory = db()->select('listing_id')->from(':advancedmarketplace_category_data')->where(['category_id' => $iId])->execute();

            $innerJoin = db()->select('listing_id')->from(':advancedmarketplace_category_data')->where([
                'category_id' => $iOldParentId,
                'listing_id' => ['in' => $sListingsInChildCategory]
            ])->group('listing_id')->execute();

            $sUpdateFrom = Phpfox::getT('advancedmarketplace_category_data') . ' AS d1 ' . db()->innerJoin("($innerJoin)",
                    'd2', 'd1.listing_id = d2.listing_id')->execute();

            db()->update($sUpdateFrom, ['d1.category_id' => $aVals['parent_id']], ['d1.category_id' => $iOldParentId]);
        }

        $aUpdates = array('name' => $finalPhrase,
            'parent_id' => (int)$aVals['parent_id'],
        );
        if ($aImage) {
            $aUpdates['image_path'] = $sNewFileName ? $sNewFileName : $imagePath;
            $aUpdates['server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
        }
        $this->database()->update($this->_sTable, $aUpdates, 'category_id = ' . (int)$iId);

        $sTempFile = $sAdvMarketplaceImageDir . sprintf($sNewFileName, '');
        if (file_exists($sTempFile)) {
            @unlink($sTempFile);
        }

        $this->cache()->removeGroup('advmarketplace_category');

        return true;
    }

    public function delete($iId)
    {
        //Delete phrase of category
        $aCategory = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('category_id=' . (int)$iId)
            ->execute('getSlaveRow');
        if (isset($aCategory['name']) && Phpfox::isPhrase($aCategory['name'])) {
            Phpfox::getService('language.phrase.process')->delete($aCategory['name'], true);
        }

        $this->deleteCategoryImage($iId);

        $this->database()->delete($this->_sTable, 'parent_id = ' . (int)$iId);
        $this->database()->delete($this->_sTable, 'category_id = ' . (int)$iId);
        $this->cache()->removeGroup('advmarketplace_category');

        return true;
    }

    public function deleteCategoryImage($categoryId)
    {
        $iFileSizes = 0;
        $aSizes = array('', 120, 200, 250, '120_square', '200_square', '250_square');
        $aImage = db()->select('server_id, image_path')
            ->from(Phpfox::getT('advancedmarketplace_category'))
            ->where(['category_id' => $categoryId])
            ->execute('getSlaveRow');
        if (!isset($aImage['image_path']) || !$aImage['image_path']) {
            return;
        }
        // delete image using cdn
        foreach ($aSizes as $sSize) {
            $sImage = Phpfox::getParam('core.dir_pic') . "advancedmarketplace/" . sprintf($aImage['image_path'],
                    (empty($sSize) ? '' : '_') . $sSize);

            if (file_exists($sImage)) {
                $iFileSizes += filesize($sImage);

                @unlink($sImage);
            }
                if (isset($aImage) && $aImage['server_id'] > 0) {
                $advancedmarketplace_dir_image = Phpfox::getParam('advancedmarketplace.dir_pic');
                $advancedmarketplace_url_image = Phpfox::getParam('advancedmarketplace.url_pic');

                // Get the file size stored when the photo was uploaded
                $sTempUrl = Phpfox::getLib('cdn')->getUrl(str_replace($advancedmarketplace_dir_image,
                    $advancedmarketplace_url_image, $sImage));

                $aHeaders = get_headers($sTempUrl, true);
                if (preg_match('/200 OK/i', $aHeaders[0])) {
                    $iFileSizes += (int)$aHeaders["Content-Length"];
                }

                Phpfox::getLib('cdn')->remove($sImage);
            }
        }

        // update database
        db()->update(Phpfox::getT('advancedmarketplace_category'), [
            'server_id' => 0,
            'image_path' => null
        ], ['category_id' => $categoryId]);
    }

    public function updateOrder($aVals)
    {
        foreach ($aVals as $iId => $iOrder) {
            $this->database()->update($this->_sTable, array('ordering' => $iOrder), 'category_id = ' . (int)$iId);
        }

        return true;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('advancedmarketplace.service_category_process__call')) {
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
        $this->database()->update(($this->_sTable), array('is_active' => (int)($iType == '1' ? 1 : 0)),
            'category_id' . ' = ' . (int)$iId);
    }
}
