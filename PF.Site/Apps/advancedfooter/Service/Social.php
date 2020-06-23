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

class Social extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('advancedfooter_category');
    }




    public function addSocial($aVals)
    {
        $iId = db()->insert(Phpfox::getT('advancedfooter_category'), [
            'time_stamp' => PHPFOX_TIME,
            'ordering' => '0',
            'icon' => $aVals['icon'],
            'link' => $aVals['link'],
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
            ->from(Phpfox::getT('advancedfooter_category'))
            ->where('category_id=' . intval($iCategoryId))
            ->execute('getSlaveRow');



        db()->delete(Phpfox::getT('advancedfooter_category'), 'category_id = ' . intval($iCategoryId));

        return true;
    }

    /**
     * @param $iId
     * @param $aVals
     * @return bool
     */
    public function updateSocial($iId, $aVals)
    {
        db()->update(Phpfox::getT('advancedfooter_category'), array(
            'time_stamp' => PHPFOX_TIME,
            'icon' => $aVals['icon'],
            'link' => $aVals['link'],
            ), 'category_id = ' . (int)$iId
        );

        return true;
    }

    public function updateSocialActivity($iId, $iType)
    {
        Phpfox::isAdmin(true);
        db()->update((Phpfox::getT('advancedfooter_category')), array('is_active' => (int)($iType == '1' ? 1 : 0)),
            'category_id = ' . (int)$iId);
        // clear cache
    }

    public function getForAdmin($isEnabled = false)
    {
        $where = '';
        if ($isEnabled) $where = 'is_active = 1';
        $aRows = db()->select('*')
            ->from($this->_sTable)
            ->where($where)
            ->order('ordering ASC')
            ->execute('getSlaveRows');

        $icons = $this->getSocial();


        if (!empty($aRows)) {
            foreach ($aRows as &$aItem) {
                $aItem['info'] =  $icons[$aItem['icon']];
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
                'phrase' => _p('Amazon'),
                'color' => '#ff9900'
            ],
            'apple' => [
                'icon' => 'apple',
                'phrase' => _p('Apple Music'),
                'color' => '#a6b1b7'
            ],
            'facebook' => [
                'icon' => 'facebook',
                'phrase' => _p('Facebook'),
                'color' => '#1877f2'
            ],
            'google' => [
                'icon' => 'google',
                'phrase' => _p('Google Play'),
                'color' => '#ea4335'
            ],
            'instagram' => [
                'icon' => 'instagram',
                'phrase' => _p('Instagram'),
                'color' => '#c32aa3'
            ],
            'linkedin' => [
                'icon' => 'linkedin',
                'phrase' => _p('Linkedin'),
                'color' => '#007bb5'
            ],
            'twitter' => [
                'icon' => 'twitter',
                'phrase' => _p('Twitter'),
                'color' => '#1da1f2'
            ],
            'snapchat' => [
                'icon' => 'snapchat',
                'phrase' => _p('Snapchat'),
                'color' => '#fffc00'
            ],
            'soundcloud' => [
                'icon' => 'soundcloud',
                'phrase' => _p('SoundCloud'),
                'color' => '#ff5500'
            ],
            'spotify' => [
                'icon' => 'spotify',
                'phrase' => _p('Spotify'),
                'color' => '#1ed760'
            ],
            'youtube' => [
                'icon' => 'youtube-play',
                'phrase' => _p('YouTube'),
                'color' => '#ff0000'
            ],
            'vimeo' => [
                'icon' => 'vimeo',
                'phrase' => _p('Vimeo'),
                'color' => '#1ab7ea'
            ],
            'pinterest' => [
                'icon' => 'pinterest',
                'phrase' => _p('Pinterest'),
                'color' => '#bd081c'
            ],
            'website' => [
                'icon' => 'globe',
                'phrase' => _p('Website'),
                'color' => '#35465d'
            ],
            'whatsapp' => [
                'icon' => 'whatsapp',
                'phrase' => _p('whatsApp'),
                'color' => '#25d366'
            ],
            'store' => [
                'icon' => 'shopping-cart',
                'phrase' => _p('Online Store'),
                'color' => '#ffb903'
            ],
            'wechat' => [
                'icon' => 'wechat',
                'phrase' => _p('WeChat'),
                'color' => '#333'
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
