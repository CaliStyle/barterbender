<?php

namespace Apps\YNC_Reaction\Service;

use Phpfox;
use Phpfox_Service;
use Phpfox_Url;

/**
 * Class Yncreaction
 * @package Apps\YNC_Reaction\Service
 */
class Yncreaction extends Phpfox_Service
{
    /**
     * Class constructor
     */
    private $_aDefaultIcon ;
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('yncreaction_reactions');
        $this->_aDefaultIcon = ['like.svg','love.svg','haha.svg','wow.svg','sad.svg','angry.svg'];
    }

    /**
     * @return array|bool|int|string
     */
    public function getForAdmin()
    {
        $sCacheId = $this->cache()->set('yncreaction_reactions_admin');
        $this->cache()->group('yncreaction', $sCacheId);
        if (!($aRows = $this->cache()->get($sCacheId))) {
            $aRows = db()->select('r.*')
                ->from($this->_sTable, 'r')
                ->where('r.is_deleted = 0')
                ->order('r.ordering ASC')
                ->execute('getSlaveRows');
            $this->cache()->save($sCacheId, $aRows);
        }
        foreach ($aRows as $key => $aRow) {
            $this->getReactionIcon($aRows[$key]);
        }
        return $aRows;
    }

    /**
     * @param $aReaction
     */
    public function getReactionIcon(&$aReaction)
    {
        if (!empty($aReaction['icon_path'])) {
            if ($aReaction['view_id'] > 0 && in_array($aReaction['icon_path'],$this->_aDefaultIcon)) {
                $aReaction['full_path'] = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-reaction/assets/images/' . $aReaction['icon_path'];
            } else {
                $aReaction['full_path'] = Phpfox::getLib('image.helper')->display([
                    'server_id' => $aReaction['server_id'],
                    'path' => 'core.url_pic',
                    'file' => $aReaction['icon_path'],
                    'suffix' => '_64',
                    'return_url' => true
                ]);
            }
        }
    }

    /**
     * @param $iId
     * @return array|bool|int|string
     */
    public function getForEdit($iId)
    {
        Phpfox::isAdmin(true);
        return $this->getReactionById($iId, true);
    }

    /**
     * @param $iId
     * @param bool $bGetIcon
     * @return array|bool|int|string
     */
    public function getReactionById($iId, $bGetIcon = false)
    {
        if (!$iId) {
            return false;
        }
        $aItem = db()->select('*')
            ->from($this->_sTable)
            ->where('is_deleted = 0 AND id =' . (int)$iId)
            ->execute('getRow');
        if ($aItem && $bGetIcon) {
            $this->getReactionIcon($aItem);
        }
        return $aItem;
    }

    /**
     * @param bool $bActive
     * @return array|int|string
     */
    public function countReactions($bActive = false)
    {
        return db()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('is_deleted = 0' . ($bActive ? ' AND is_active = 1' : ''))
            ->execute('getField');
    }

    public function getReactions($bActive = true)
    {
        $sCacheId = $this->cache()->set('yncreaction_list_reactions_' . $bActive);
        $this->cache()->group('yncreaction', $sCacheId);
        if (!($aRows = $this->cache()->get($sCacheId))) {
            $aRows = db()->select('r.*')
                ->from($this->_sTable, 'r')
                ->where('r.is_deleted = 0' . ($bActive ? ' AND r.is_active = 1' : ''))
                ->order('r.ordering ASC')
                ->execute('getSlaveRows');
            $this->cache()->save($sCacheId, $aRows);
        }
        foreach ($aRows as $key => $aRow) {
            $this->getReactionIcon($aRows[$key]);
        }
        return $aRows;
    }

    /**
     * @return array|int|string
     */
    public function getDefaultLike()
    {
        //Like default have view_id = 2
        $aItem = db()->select('*')
            ->from($this->_sTable)
            ->where('view_id = 2')
            ->execute('getRow');
        $this->getReactionIcon($aItem);

        return $aItem;
    }

    /**
     * @param $iItemId
     * @param $sType
     * @param $iUserId
     * @return array|int|string
     */
    public function getReactedDetail($iItemId, $sType, $iUserId)
    {
        $aRow = db()->select('r.*, l.like_id')
            ->from(':like', 'l')
            ->join($this->_sTable, 'r', 'l.ync_react_id = r.id')
            ->where('l.type_id = \'' . db()->escape($sType) . '\' AND l.item_id = ' . (int)$iItemId . ' AND l.user_id = ' . $iUserId)
            ->execute('getRow');
        if ($aRow) {
            $this->getReactionIcon($aRow);
        }
        return $aRow;
    }

    /**
     * @param $sType
     * @param $iItem
     * @param string $sPrefix
     * @return array
     */
    public function getAll($sType, $iItem, $sPrefix = '')
    {
        $aLikes = Phpfox::getService('like')->getLikes($sType, $iItem, $sPrefix);
        $aFeed = array('likes' => $aLikes);
        $aFeed['type_id'] = $sType;
        $aFeed['item_id'] = $iItem;
        $aFeed['feed_table_prefix'] = $sPrefix;
        $sLikePhrase = $this->getReactionsPhrase($aFeed);


        $aOut = array(
            'likes' => array(
                'total' => count($aLikes),
                'phrase' => $sLikePhrase,
                'most_reactions' => isset($aFeed['most_reactions']) ? $aFeed['most_reactions'] : []
            ),
        );

        return $aOut;
    }

    /**
     * @param $aFeed
     * @param bool $bForce
     * @return string
     */
    public function getReactionsPhrase(&$aFeed, $bForce = false)
    {
        $sOriginalIsLiked = ((isset($aFeed['feed_is_liked']) && $aFeed['feed_is_liked']) ? $aFeed['feed_is_liked'] : '');

        if (!isset($aFeed['feed_total_like'])) {
            $aFeed['feed_total_like'] = isset($aFeed['likes']) ? count($aFeed['likes']) : 0;
        }

        if (!isset($aFeed['like_type_id'])) {
            $aFeed['like_type_id'] = isset($aFeed['type_id']) ? $aFeed['type_id'] : null;
        }

        $sPhrase = '<span class="people-liked-feed">';
        $oParse = Phpfox::getLib('phpfox.parse.output');
        if (Phpfox::isModule('like')) {
            $oLike = Phpfox::getService('like');
        }
        $oUrl = Phpfox_Url::instance();

        if ((!isset($aFeed['likes']) && isset($oLike)) || (isset($oLike) && count($aFeed['likes']) > 2)) {
            $aFeed['likes'] = $oLike->getLikesForFeed($aFeed['type_id'], $aFeed['item_id'], false, 2, false,
                (isset($aFeed['feed_table_prefix']) ? $aFeed['feed_table_prefix'] : ''));
            $aFeed['total_likes'] = count($aFeed['likes']);
        }

        $bDidILikeIt = false;
        /* Check to see if I liked this */
        if (!isset($aFeed['feed_is_liked'])) {
            if (Phpfox::isModule('like')) {
                $aFeed['feed_is_liked'] = Phpfox::getService('like')->didILike($aFeed['type_id'], $aFeed['item_id'], [],
                    (isset($aFeed['feed_table_prefix']) ? $aFeed['feed_table_prefix'] : ''));
            }
        }

        $iCountLikes = (isset($aFeed['likes']) && !empty($aFeed['likes'])) ? count($aFeed['likes']) : 0;

        if ($aFeed['feed_total_like'] < count($aFeed['likes'])) {
            $aFeed['feed_total_like'] = count($aFeed['likes']);
        }

        $iPhraseLimiter = 2;
        if (isset($aFeed['feed_is_liked']) && $aFeed['feed_is_liked']) {
            if ($aFeed['feed_total_like'] == 1) {
                $sPhrase .= '<span class="user_profile_link_span" id="js_user_name_link_' . Phpfox::getUserBy('user_name') . '"><a href="' . $oUrl->makeUrl('profile', array(Phpfox::getUserBy('user_name'), (empty(Phpfox::getUserBy('user_name')) && !empty(Phpfox::getUserBy('profile_page_id')) ? Phpfox::getUserBy('profile_page_id') : null))) . '">' . $oParse->shorten(Phpfox::getUserBy('full_name'),
                        20) . '</a></span>';
            } elseif ($iPhraseLimiter == 1 || $iPhraseLimiter == 2) {
                if ($aFeed['feed_total_like'] == 2) {
                    $sPhrase .= _p('you_and') . '&nbsp;';
                } else {
                    $sPhrase .= _p('you');
                }
            } elseif ($aFeed['feed_total_like'] == 2) {
                $sPhrase .= _p('you_and') . '&nbsp;';
            } elseif ($iPhraseLimiter > 2) {
                $sPhrase .= _p('you_comma') . '&nbsp;';
            }
            $bDidILikeIt = true;
        } else {
            if (Phpfox::isModule('like')) {
                $sPhrase = '';
            }
        }

        if (isset($aFeed['likes']) && is_array($aFeed['likes']) && $iCountLikes > 0) {
            $iIteration = ($bDidILikeIt && ($iPhraseLimiter < $aFeed['feed_total_like']) ? 1 : 0);
            $aLikes = [];
            foreach ($aFeed['likes'] as $aLike) {
                if ($iIteration >= $iCountLikes) {
                    break;
                } else {
                    if ($aLike['user_id'] == Phpfox::getUserId()) {
                        continue;
                    }
                    if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $aLike['user_id'])) {
                        $sUserLink = '<span class="user_profile_link_span" id="js_user_name_link_' . $aLike['user_name'] . '">' . $oParse->shorten($aLike['full_name'],
                                20) . '</span>';
                    } else {
                        $sUserLink = '<span class="user_profile_link_span" id="js_user_name_link_' . $aLike['user_name'] . '"><a href="' . $oUrl->makeUrl('profile', array($aLike['user_name'], (empty($aLike['user_name']) && !empty($aLike['profile_page_id']) ? $aLike['profile_page_id'] : null))) . '">' . $oParse->shorten($aLike['full_name'],
                                20) . '</a></span>';
                    }
                    $aLikes[] = $sUserLink;
                    $iIteration++;
                }
            }

            $sTempUser = array_pop($aLikes);
            $sImplode = implode(', ', $aLikes);
            $sPhrase .= $sImplode . ' ';

            if (isset($aFeed['feed_is_liked']) && $aFeed['feed_is_liked'] && $iPhraseLimiter >= 2 && $aFeed['feed_total_like'] > $iPhraseLimiter) {
                $sPhrase = trim($sPhrase) . ', ' /*. _p('and') . ' '*/
                ;
            } else {
                if (isset($aFeed['feed_total_like']) && ($aFeed['feed_total_like'] > 2) && 2 != 1) {
                    $sPhrase = trim($sPhrase) . ', ';
                } else {
                    if (count($aLikes) > 0) {
                        $sPhrase .= _p('and') . ' ';
                    } else {
                        $sPhrase = trim($sPhrase);
                    }
                }
            }
            $sPhrase .= $sTempUser;

        }

        if (isset($aFeed['feed_total_like']) && $aFeed['feed_total_like'] > 2 && 2 != 0) {
            $sLink = '<a href="javascript:void(0)" data-action="ync_reaction_show_list_user_react_cmd" data-type_id="' . $aFeed['like_type_id'] . '"  data-item_id="' . $aFeed['item_id'] . '" data-feed_id="" data-table_prefix="' . ((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE')) ? 'pages_' : '') . '" data-react_id="0">';
            $iTotalLeftShow = ($aFeed['feed_total_like'] - 2);

            if ($iTotalLeftShow == 1) {
                $sPhrase .= '&nbsp;' . _p('and') . '&nbsp;' . $sLink . _p('1_other_person') . '&nbsp;';
            } else {
                $sPhrase .= '&nbsp;' . _p('and') . '&nbsp;' . $sLink . Phpfox::getService('core.helper')->shortNumber($iTotalLeftShow) . '&nbsp;' . _p('others') . '&nbsp;';
            }
            $sPhrase .= '</a></span>';
        } else {
            if (isset($aFeed['likes']) && count($aFeed['likes']) > 1) {
                $sPhrase .= '</span>&nbsp;';
            } else {
                if (isset($aFeed['feed_is_liked']) && $aFeed['feed_is_liked']) {

                    if (count($aFeed['likes']) == 0 || count($aFeed['likes']) == 1) {
                        $sPhrase .= '</span>&nbsp;';
                    } else {
                        if (count($aFeed['likes']) > 1) {
                            $sPhrase .= '<a href="javascript:void(0)" data-action="ync_reaction_show_list_user_react_cmd" data-type_id="' . $aFeed['like_type_id'] . '"  data-item_id="' . $aFeed['item_id'] . '" data-feed_id="" data-table_prefix="' . ((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE')) ? 'pages_' : '') . '" data-react_id="0">';
                            $sPhrase .= Phpfox::getService('core.helper')->shortNumber($aFeed['feed_total_like']) . '&nbsp;' . _p('others') . '&nbsp;';
                            $sPhrase .= '</a></span>';
                        } else {
                            $sPhrase .= '</span>';
                        }
                    }
                } else {
                    if (isset($aFeed['likes']) && count($aFeed['likes']) == 1) {
                        $sPhrase .= '</span>&nbsp;';
                    } else {
                        if (strlen($sPhrase) > 1) {
                            $sPhrase .= '</span>';
                        }
                    }
                }
            }
        }

        $aActions = [];

        if (count($aActions) > 0) {
            $aFeed['bShowEnterCommentBlock'] = true;
            $aFeed['call_displayactions'] = true;
        }
        if (strlen($sPhrase) > 1 || count($aActions) > 0) {
            $aFeed['bShowEnterCommentBlock'] = true;
        }
        $sPhrase = str_replace(["&nbsp;&nbsp;", '  ', "\n"], ['&nbsp;', ' ', ''], $sPhrase);
        $sPhrase = str_replace(['  ', " &nbsp;", "&nbsp; "], ' ', $sPhrase);

        //',&nbsp;,'
        $sPhrase = str_replace(["\r\n", "\r"], "\n", $sPhrase);

        $aFeed['feed_like_phrase'] = $sPhrase;

        if (!empty($sOriginalIsLiked) && !$bForce) {
            $aFeed['feed_is_liked'] = $sOriginalIsLiked;
        }

        if (empty($sPhrase)) {
            $aFeed['feed_is_liked'] = false;
            $aFeed['feed_total_like'] = 0;
        } else {
            list(, $aFeed['most_reactions']) = $this->getMostReaction($aFeed['like_type_id'], $aFeed['item_id'],
                (isset($aFeed['feed_table_prefix']) ? $aFeed['feed_table_prefix'] : ''));
        }

        return $sPhrase;
    }

    public function getMostReaction($sType, $iItemId, $sFeedTablePrefix)
    {
        $sWhere = '(l.type_id = \'' . $this->database()->escape(str_replace('-', '_',
                $sType)) . '\' OR l.type_id = \'' . str_replace('_', '-',
                $sType) . '\') AND l.item_id = ' . (int)$iItemId;

        if ($sType == 'app') {
            $sWhere .= " AND l.feed_table = '{$sFeedTablePrefix}feed'";
        }
        $aGroupReact = db()->select('r.*, COUNT(l.user_id) as total_reacted')
            ->from(':like', 'l')
            ->join($this->_sTable, 'r', 'l.ync_react_id = r.id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->group('l.ync_react_id')
            ->where($sWhere)
            ->order('total_reacted DESC, r.ordering ASC')
            ->execute('getSlaveRows');
        $iTotal = 0;
        if ($aGroupReact) {
            foreach ($aGroupReact as $key => $aReact) {
                $this->getReactionIcon($aGroupReact[$key]);
                $iTotal += $aReact['total_reacted'];
            }
        }
        return array($iTotal, $aGroupReact);
    }

    public function getSomeReactedUser($sType, $iItemId, $iReactId, $sPrefix = '', $iLimit = 5)
    {
        $sPrefix = $sPrefix . 'feed';
        $aUsers = db()->select(Phpfox::getUserField())
            ->from(':like', 'l')
            ->join(':user', 'u', 'u.user_id = l.user_id')
            ->where('l.ync_react_id = ' . (int)$iReactId . ' AND l.type_id = \'' . db()->escape($sType) . '\' AND l.item_id = ' . (int)$iItemId . ($sType == 'app' ? " AND l.feed_table = '{$sPrefix}'" : ''))
            ->limit($iLimit)
            ->execute('getSlaveRows');
        return $aUsers;
    }

    /**
     * @param $sType
     * @param $iItemId
     * @param $iReactId
     * @param string $sPrefix
     * @param int $iLimit
     * @param int $iPage
     * @param $iCnt
     * @return array|int|string
     */
    public function getListUserReact($sType, $iItemId, $iReactId, $sPrefix = '', $iLimit = 5, $iPage = 1, &$iCnt)
    {
        $sWhere = '1 = 1';
        if ($iReactId) {
            $sWhere .= ' AND l.ync_react_id = ' . (int)$iReactId;
        }
        $sWhere .= ' AND l.type_id = \'' . db()->escape($sType) . '\' AND l.item_id = ' . (int)$iItemId . ($sType == 'app' ? " AND l.feed_table = '{$sPrefix}'" : '');
        $iCnt = db()->select('COUNT(*)')
            ->from(':like', 'l')
            ->join(':user', 'u', 'u.user_id = l.user_id')
            ->where($sWhere)
            ->execute('getField');
        $aUsers = [];
        if ($iCnt) {
            $aUsers = db()->select(Phpfox::getUserField() . ', r.*, uf.total_friend, uf.dob_setting')
                ->from(':like', 'l')
                ->join(':user', 'u', 'u.user_id = l.user_id')
                ->join(Phpfox::getT('user_field'), 'uf', 'l.user_id = uf.user_id')
                ->join($this->_sTable, 'r', 'r.id = l.ync_react_id')
                ->where($sWhere)
                ->limit($iPage, $iLimit, $iCnt)
                ->order('l.time_stamp DESC')
                ->execute('getSlaveRows');
            foreach ($aUsers as $key => $aUser) {
                $this->getReactionIcon($aUsers[$key]);
                if (Phpfox::isModule('pages') && empty($aUser['user_name']) && !empty($aUser['profile_page_id'])) {
                    $aUsers[$key]['page'] = Phpfox::getService('yncreaction')->getPage($aUser['profile_page_id'], Phpfox::getUserId());
                }
                $aUsers[$key]['is_friend'] = Phpfox::getService('friend')->isFriend(Phpfox::getUserId(), $aUser['user_id']);
            }
        }
        return $aUsers;
    }

    public function getPage($iPageId, $iUserId)
    {
        $aPage = db()->select('p.*, l.like_id as is_liked')
                    ->from(':pages','p')
                    ->leftJoin(':like','l','l.type_id = \'pages\' AND l.item_id = p.page_id AND l.user_id ='.(int)$iUserId)
                    ->where('p.page_id = '.(int)$iPageId .' AND p.item_type = 0' )
                    ->execute('getRow');
        if ($aPage['reg_method'] == '2' && $iUserId) {
            $aPage['is_invited'] = (int)db()->select('COUNT(*)')
                ->from(':pages_invite')
                ->where('page_id = ' . (int)$aPage['page_id'] . ' AND invited_user_id = ' . (int)$iUserId)
                ->execute('getSlaveField');
            if (!$aPage['is_invited']) {
                unset($aPage['is_invited']);
            }
        }
        if (($aPage['page_type'] == '1' || $aPage['item_type'] != '0') && $aPage['reg_method'] == '1') {
            $aPage['is_reg'] = (int)db()->select('COUNT(*)')
                ->from(':pages_signup')
                ->where('page_id = ' . (int)$aPage['page_id'] . ' AND user_id = ' . (int)$iUserId)
                ->execute('getSlaveField');
        }
        return $aPage;
    }
}