<?php

defined('PHPFOX') or exit('NO DICE!');

define('ONLINE_TIMEOUT', 30);
define('MAX_SIZE_OF_USER_IMAGE', '_50_square');
define('CHECK_ALIVE_CONNECTION_DB_INTERVAL', 300);   // 5 * 60


if(!function_exists('get_mime_of_file')) {
    function get_mime_of_file($filename) {
        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}


class Ynchat_Service_Ynchat extends Phpfox_Service
{
    private $_iTotalOnlineFriends = 0;
    protected function database()
    {
        $oYndatabase = new Ynchat_Service_Database_Yndatabase();
        return $oYndatabase -> getInstance();
    }

	public function getTotalOnlineFriends()
	{
		return $this->_iTotalOnlineFriends;
	}

	public function checkBan($sTxt, $sType = 'word')
	{
        $aFilters = $this->getBanFilters($sType);
        switch($sType){
            case 'word':
                foreach($aFilters as $aItem){
                    $sTxt = str_replace($aItem['org_find_value'], $aItem['org_replacement'], $sTxt);
                }

                break;
        }

		return $sTxt;
	}

	public function getBanFilters($sType)
	{
        $aFilters = $this->database()->select('b.*, ' . Phpfox::getUserField())
                        ->from(('ynchat_ban'), 'b')
                        ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
                        ->where('b.type_id = \'' . $this->database()->escape($sType) . '\'')
                        ->execute('getRows');

        foreach ($aFilters as $iKey => $aFilter)
        {
            if (!empty($aFilter['user_groups_affected']))
            {
                $aUserGroups = unserialize($aFilter['user_groups_affected']);
                $aFilters[$iKey]['user_groups_affected'] = array();

                $sWhere = '';
                foreach ($aUserGroups as $iUserGroup)
                {
                    $sWhere .= 'user_group_id = ' . $iUserGroup . ' OR ';
                }
                $sWhere = rtrim($sWhere, ' OR ');
                $aFilters[$iKey]['user_groups_affected'] = Phpfox::getService('user.group')->get($sWhere);
            }
        }


        if(is_array($aFilters) == false){
            $aFilters = array();
        }
		return $aFilters;
	}

	public function addBan($aVals, &$aBanFilter = null)
	{
		Phpfox::isAdmin(true);

		$aForm = array(
			'type_id' => array(
				'type' => 'string:required'
			),
			'find_value' => array(
				'type' => 'string:required',
				'message' => Phpfox::getPhrase('ynchat.filter_value_is_required')
			),
			'reason' => array(
				'type' => 'string'
			),
			'days_banned' => array(
				'type' => 'int'
			),
			'return_user_group' => array(
				'type' => 'int'
			),
			'bShow' => array(
				'type' => 'string'
				), // just to allow the input
			'user_groups_affected' => array(
				'type' => 'array'
			)
		);

		if ($aBanFilter !== null && isset($aBanFilter['replace']))
		{
			$aForm['replacement'] = array(
				'type' => 'string:required',
				'message' => Phpfox::getPhrase('ynchat.filter_replacement_is_required')
			);
		}

		$aVals = $this->validator()->process($aForm, $aVals);

		if (!Phpfox_Error::isPassed())
		{
			return false;
		}
		if ($aVals['find_value'] == Phpfox::getIp())
		{
			return Phpfox_Error::set(Phpfox::getPhrase('ynchat.you_cannot_ban_yourself'));
		}

		$aVals['org_find_value'] = $aVals['find_value'];
		$aVals['org_replacement'] = $aVals['replacement'];
		$aVals['user_id'] = Phpfox::getUserId();
		$aVals['time_stamp'] = PHPFOX_TIME;
		$aVals['find_value'] = $this->preParse()->convert($aVals['find_value']);
		if ( (isset($aVals['bShow']) && $aVals['bShow'] == '0') || !isset($aVals['bShow']))
		{
			unset($aVals['reason']);
			unset($aVals['days_banned']);
			unset($aVals['return_user_group']);
		}
		else
		{
			$aVals['reason'] = !Phpfox::getLib('locale')->isPhrase($aVals['reason']) ? Phpfox::getLib('parse.input')->clean($aVals['reason']) : $aVals['reason'];
			$aVals['days_banned'] = (int)$aVals['days_banned'];
			$aVals['return_user_group'] = (int)$aVals['return_user_group'];
			if (!isset($aVals['user_groups_affected']))
			{
				$aVals['user_groups_affected'] = array();
			}
			$aVals['user_groups_affected'] = serialize($aVals['user_groups_affected']);
		}
		unset($aVals['bShow']);
		if (isset($aVals['replacement']))
		{
			$aVals['replacement'] = $this->preParse()->convert($aVals['replacement']);
		}
		if (empty($aVals['user_groups_affected']))
		{
			$aVals['user_groups_affected'] = '';
		}
		$this->database()->insert(('ynchat_ban'), $aVals);

		$this->cache()->remove('ynchat_ban', 'substr');

		return true;
	}

	public function deleteBan($iDeleteid)
	{
		Phpfox::isAdmin(true);
		$this->database()->delete(('ynchat_ban'), 'ban_id = ' . (int) $iDeleteid);
		$this->cache()->remove('ynchat_ban', 'substr');
		return true;
	}

    // alias call from mfox
    public function alias_getFriendList($aData){
        
        $iUserId = Phpfox::getUserId();

        return $this->getFriendsList($iUserId);
    }
    public function getFriendsList($userid, $time = null, $search = '')
    {
        // init
        if((int)$userid <= 0){
            return array();
        }

        $result = array();
        if(null == $time){
            $time = $this->getTimeStamp();
        }
		$hideOffline = 0;
        $iLimit = (int)Phpfox::getParam('ynchat.number_of_friend_list');
        if($iLimit < 1){
            $iLimit = 1000;
        }

        // process
        // get user setting
        $aUserSetting = $this->getUserSettingsByUserId($userid);
        if(isset($aUserSetting['iUserId']) == false){
            return array();
        }

        //// show ONLY friends of user
        $sWhere = '';
        if (strlen(trim($search)) > 0)
        {
            $sWhere .= ' AND user.full_name LIKE "%'. Phpfox::getLib('parse.input')->clean($search) .'%" ';
            $iLimit = 20;
        }

        $sSql = '';
        $sSql .= 'SELECT DISTINCT
                      user.user_id user_id,
                      user.full_name full_name,
                      user.ynchat_lastactivity ynchat_lastactivity,
                      user.user_image user_image,
                      user.user_name user_name,
                      user.status message,
                      user.server_id user_server_id,
                      yus.is_goonline,
                      yus.turnonoff,
                      IFNULL(ys.status, \'offline\')  AS `status`, 
                      IFNULL(ys.agent, \'\')  AS `agent`
            ';
        $sSql .= ' FROM ' . Phpfox::getT('user') . ' AS `user` ';

        $sSql .= ' LEFT JOIN ' . ('ynchat_usersetting') . ' AS `yus` ';
        $sSql .= ' ON yus.user_id = user.user_id  ';

        $sSql .= ' LEFT JOIN ' . ('ynchat_status') . ' AS `ys` ';
        $sSql .= ' ON user.user_id = ys.user_id  ';

        // NOT get pages
        $sSql .= ' WHERE user.profile_page_id = 0   ' . $sWhere;

        $sSql .= ' AND user.user_id NOT IN   ';
        $sSql .= ' (   ';
        $sSql .= ' SELECT ub1.block_user_id   ';
        $sSql .= ' FROM ' . Phpfox::getT('user_blocked') . ' AS `ub1`     ';
        $sSql .= ' WHERE ub1.user_id = ' . (int)$userid;
        $sSql .= ' UNION  ';
        $sSql .= ' SELECT ub2.user_id   ';
        $sSql .= ' FROM ' . Phpfox::getT('user_blocked') . ' AS `ub2`     ';
        $sSql .= ' WHERE ub2.block_user_id = ' . (int)$userid;
        $sSql .= ' )   ';

        $sSql .= ' AND user.user_id IN   ';
        $sSql .= ' (   ';
        $sSql .= ' SELECT fr1.friend_user_id    ';
        $sSql .= ' FROM ' . Phpfox::getT('friend') . ' AS `fr1`     ';
        $sSql .= ' WHERE fr1.user_id = ' . (int)$userid;
        $sSql .= ' )   ';

        $sSql .= ' AND user.user_id IN    ';
        $sSql .= ' (   ';
        $sSql .= ' SELECT fr2.user_id     ';
        $sSql .= ' FROM ' . Phpfox::getT('friend') . ' AS `fr2`     ';
        $sSql .= ' WHERE fr2.friend_user_id = ' . (int)$userid;
        $sSql .= ' )   ';

        $sSql .= ' ORDER BY `status` ASC, user_id ASC';

        $result = $this->database()->getSlaveRows($sSql);
        $blockAList = $this->getBlockListBySide($userid, 'to');
		$blockAUserId = array();
		foreach ($blockAList as $key => $item) {
			$blockAUserId[] = $item['user_id'];
		}
        $blockXList = $this->getBlockListBySide($userid, 'from');
		$blockXUserId = array();
		foreach ($blockXList as $key => $item) {
			$blockXUserId[] = $item['user_id'];
		}
		$allowAList = $this->getAllowListBySide($userid, 'to');
		$allowAUserId = array();
		foreach ($allowAList as $key => $item) {
			$allowAUserId[] = $item['user_id'];
		}
		$allowXList = $this->getAllowListBySide($userid, 'from');
		$allowXUserId = array();
		foreach ($allowXList as $key => $item) {
			$allowXUserId[] = $item['user_id'];
		}

		$buddyList = array();
        $count = 0;
        $countOnline = 0;
		foreach ($result as $key => $item) {
            if ($item['turnonoff'] == null) {
                $item['turnonoff'] = 'onall';
            }
            $bAdd = null;
            switch($aUserSetting['sTurnOnOff']){
                case 'onall':
                    $bAdd = 1;
                    break;
                case 'offall':
                    $bAdd = 0;
                    break;
                case 'onsome':
                    if (in_array($item['user_id'], $allowAUserId)) {
                        $bAdd = 1;
                    } else {
                        $bAdd = 0;
                    }
                    break;
                case 'offsome':
                    if (!in_array($item['user_id'], $blockAUserId)) {
                        $bAdd = 1;
                    } else {
                        $bAdd = 0;
                    }
                    break;
            }
            if($bAdd){
                switch($item['turnonoff']){
                    case 'offall':
                        $bAdd = -1;
                        break;
                    case 'offsome':
                        if (in_array($item['user_id'], $blockXUserId)) {
                            $bAdd = -1;
                        }
                        break;
                    case 'onsome':
                        if (!in_array($item['user_id'], $allowXUserId)) {
                            $bAdd = -1;
                        }
                        break;
                }
            }
            // -1 : can display with offline status
            // 0 : NOT display
            // 1 : can display with online status
            if($bAdd == -1){
                $item['status'] = 'offline';
            }
            if($bAdd == 1 || $bAdd == -1){
                if(empty($item['status'])){
                    $item['status'] = 'offline';
                }

				if ($item['message'] == null) {
					$item['message'] = '';
				}
                if ($item['is_goonline'] == null) {
                    $item['is_goonline'] = 1;
                }
                if (!$item['is_goonline']) {
                    $item['status'] = 'offline';
                }

				$item['link'] = $this->getLink($item['user_name']);
				$item['avatar'] = $this->getAvatar($item);

				if (empty($item['grp'])) {
					$item['grp'] = '';
				}

				if (!empty($item['user_name']) && ($hideOffline == 0 || ($hideOffline == 1 && $item['status'] != 'offline'))) {
					$buddyList[] = $item;
                    $count ++;
                    if($item['status'] != 'offline'){
                        $countOnline ++;
                    }
				}
            }

            if($count == $iLimit){
                break;
            }
		}

        // end
        $this->_iTotalOnlineFriends = $countOnline;
        return $buddyList;
    }

    public function getFriendsListNotRestriction($userid, $time = null, $search = '', $type = '')
    {
        // init
        if((int)$userid <= 0){
            return array();
        }

        // check cache data
        $result = array();
        if(null == $time){
            $time = $this->getTimeStamp();
        }
        $iLimit = (int)Phpfox::getParam('ynchat.number_of_friend_list');
        if($iLimit < 1){
            $iLimit = 1000;
        }

        // process
        $sWhere = '';
        if (strlen(trim($search)) > 0)
        {
            $sWhere .= ' AND user.full_name LIKE "%'. Phpfox::getLib('parse.input')->clean($search) .'%" ';
            $iLimit = 20;

            if($type == 'friend_setting'){
                $iLimit = (int)Phpfox::getParam('ynchat.number_of_friend_when_searching_friend_setting');
            }
        }

        $sSql = '';
        $sSql .= 'SELECT DISTINCT
                      user.user_id user_id,
                      user.full_name full_name,
                      user.ynchat_lastactivity ynchat_lastactivity,
                      user.user_image user_image,
                      user.user_name user_name,
                      user.status message,
                      user.server_id user_server_id,
                      yus.is_goonline,
                      yus.turnonoff,
                      IFNULL(ys.status, \'offline\')  AS `status`, 
                      IFNULL(ys.agent, \'\')  AS `agent`
            ';
        $sSql .= ' FROM ' . Phpfox::getT('user') . ' AS `user` ';

        $sSql .= ' LEFT JOIN ' . ('ynchat_usersetting') . ' AS `yus` ';
        $sSql .= ' ON yus.user_id = user.user_id  ';

        $sSql .= ' LEFT JOIN ' . ('ynchat_status') . ' AS `ys` ';
        $sSql .= ' ON user.user_id = ys.user_id  ';

        // NOT get pages
        $sSql .= ' WHERE user.profile_page_id = 0   ' . $sWhere;

        $sSql .= ' AND user.user_id IN   ';
        $sSql .= ' (   ';
        $sSql .= ' SELECT fr1.friend_user_id    ';
        $sSql .= ' FROM ' . Phpfox::getT('friend') . ' AS `fr1`     ';
        $sSql .= ' WHERE fr1.user_id = ' . (int)$userid;
        $sSql .= ' )   ';

        $sSql .= ' AND user.user_id IN    ';
        $sSql .= ' (   ';
        $sSql .= ' SELECT fr2.user_id     ';
        $sSql .= ' FROM ' . Phpfox::getT('friend') . ' AS `fr2`     ';
        $sSql .= ' WHERE fr2.friend_user_id = ' . (int)$userid;
        $sSql .= ' )   ';

        $sSql .= ' ORDER BY user_id ASC    ';
        $sSql .= ' LIMIT 0 , ' . $iLimit;

        $result = $this->database()->getSlaveRows($sSql);
        $buddyList = array();
		foreach ($result as $key => $item) {
            if(empty($item['status'])){
                $item['status'] = 'offline';
            }

            if ($item['message'] == null) {
                $item['message'] = '';
            }
            if ($item['is_goonline'] == null) {
                $item['is_goonline'] = 1;
            }
            if (!$item['is_goonline']) {
                $item['status'] = 'offline';
            }

            if (empty($item['grp'])) {
                $item['grp'] = '';
            }

            $item['link'] = $this->getLink($item['user_name']);
            $item['avatar'] = $this->getAvatar($item);

            $buddyList[] = $item;
		}

        // end
        return $buddyList;
    }

    public function getAllFriends($userid)
    {
        // init
        if((int)$userid <= 0){
            return array();
        }

        // check cache data
        $sCacheId = $this->cache()->set('ynchat_allfriendlist_' . $userid);
        if (($buddyList = $this->cache()->get($sCacheId))){
            return $buddyList;
        }

        // process
        $sSql = 'SELECT DISTINCT
                      user.user_id user_id,
            ';
        $sSql .= ' FROM ' . Phpfox::getT('user') . ' AS `user` ';

        // NOT get pages
        $sSql .= ' WHERE user.profile_page_id = 0 ';

        $sSql .= ' AND user.user_id IN   ';
        $sSql .= ' (   ';
        $sSql .= ' SELECT fr1.friend_user_id    ';
        $sSql .= ' FROM ' . Phpfox::getT('friend') . ' AS `fr1`     ';
        $sSql .= ' WHERE fr1.user_id = ' . (int)$userid;
        $sSql .= ' )   ';

        $sSql .= ' AND user.user_id IN    ';
        $sSql .= ' (   ';
        $sSql .= ' SELECT fr2.user_id     ';
        $sSql .= ' FROM ' . Phpfox::getT('friend') . ' AS `fr2`     ';
        $sSql .= ' WHERE fr2.friend_user_id = ' . (int)$userid;
        $sSql .= ' )   ';

        $sSql .= ' ORDER BY user_id ASC    ';

        $result = $this->database()->getSlaveRows($sSql);

        // end
        $this->cache()->save($sCacheId, $buddyList);
        return $result;
    }

    /**
     * @param $aMainUser array example array(array('user_id' => 1), array('user_id' => 2))
     * @param $aSubUser array example array(1,2,3,4)
     */
    public function getDifferentUserIdList($aMainUser, $aSubUser){
        $result = array();
        foreach($aMainUser as $aItem){
            if(!in_array($aItem['user_id'], $aSubUser)){
                $result[] = $aItem['user_id'];
            }
        }

        return $result;
    }

    public function getBlockList($userid){
        $sCacheId = $this->cache()->set('ynchat_getblocklist_' . $userid);
        if (($aRows = $this->cache()->get($sCacheId))){
            if(true === $aRows){
                return array();
            } else {
                return $aRows;
            }
        }

        $sSql = ' (SELECT ynb1.to_id as user_id';
        $sSql .= ' FROM ' . ('ynchat_block') . ' AS `ynb1`';
        $sSql .= ' WHERE ynb1.from_id = ' . (int)$userid;
        $sSql .= ' ) UNION ( ';
        $sSql .= ' SELECT ynb2.from_id as user_id   ';
        $sSql .= ' FROM ' . ('ynchat_block') . ' AS `ynb2`';
        $sSql .= ' WHERE ynb2.to_id = ' . (int)$userid;  
		$sSql .= ' ) ';  		

        $aRows = $this->database()->getSlaveRows($sSql);
        $this->cache()->save($sCacheId, $aRows);

        return $aRows;
    }

    public function getBlockListBySide($userid, $side = 'from'){
        $sCacheId = $this->cache()->set('ynchat_getblocklistbyside_' . $side . '_' . $userid);
        if (($aRows = $this->cache()->get($sCacheId))){
            if(true === $aRows){
                return array();
            } else {
                return $aRows;
            }
        }

        $sSql = '';
        $aRows = array();
        switch($side){
            case 'from':
                $sSql .= ' SELECT ynb1.to_id as user_id ';
                $sSql .= ' FROM ' . ('ynchat_block') . ' AS `ynb1`';
                $sSql .= ' WHERE ynb1.from_id = ' . (int)$userid;
                break;
            case 'to':
                $sSql .= ' SELECT ynb2.from_id as user_id';
                $sSql .= ' FROM ' . ('ynchat_block') . ' AS `ynb2`';
                $sSql .= ' WHERE ynb2.to_id = ' . (int)$userid;
                break;
        }
        if(strlen($sSql) > 0){
            $aRows = $this->database()->getSlaveRows($sSql);
        }

        $this->cache()->save($sCacheId, $aRows);
        return $aRows;
    }

    public function getBlockListBySideMoreInfo($userid, $side = 'from'){
        $sCacheId = $this->cache()->set('ynchat_getblocklistbysidemoreinfo_' . $side . '_' . $userid);
        if (($aRows = $this->cache()->get($sCacheId))){
            if(true === $aRows){
                return array();
            } else {
                return $aRows;
            }
        }

        $sSql = '';
        $aRows = array();
        switch($side){
            case 'from':
                $sSql .= ' SELECT
                              user.user_id user_id,
                              user.full_name full_name,
                              user.user_image user_image,
                              user.user_name user_name,
                              user.server_id user_server_id
                        ';
                $sSql .= ' FROM ' . ('ynchat_block') . ' AS `ynb1`';
                $sSql .= ' JOIN ' . Phpfox::getT('user') . ' AS `user`';
                $sSql .= ' ON ynb1.to_id = user.user_id  ';
                $sSql .= ' WHERE ynb1.from_id = ' . (int)$userid;
                break;
            case 'to':
                $sSql .= ' SELECT
                              user.user_id user_id,
                              user.full_name full_name,
                              user.user_image user_image,
                              user.user_name user_name,
                              user.server_id user_server_id
                        ';
                $sSql .= ' FROM ' . ('ynchat_block') . ' AS `ynb2`';
                $sSql .= ' JOIN ' . Phpfox::getT('user') . ' AS `user`';
                $sSql .= ' ON ynb2.from_id = user.user_id  ';
                $sSql .= ' WHERE ynb2.to_id = ' . (int)$userid;
                break;
        }
        if(strlen($sSql) > 0){
            $aRows = $this->database()->getSlaveRows($sSql);
        }

        $this->cache()->save($sCacheId, $aRows);
        return $aRows;
    }

    public function getAllowList($userid){
        $sCacheId = $this->cache()->set('ynchat_getallowlist_' . $userid);
        if (($aRows = $this->cache()->get($sCacheId))){
            if(true === $aRows){
                return array();
            } else {
                return $aRows;
            }
        }

        $sSql = '';
        $sSql .= ' (SELECT ynb1.to_id as user_id';
        $sSql .= ' FROM ' . ('ynchat_allow') . ' AS `ynb1`';
        $sSql .= ' WHERE ynb1.from_id = ' . (int)$userid;
        $sSql .= ' ) UNION ( ';
        $sSql .= ' SELECT ynb2.from_id as user_id';
        $sSql .= ' FROM ' . ('ynchat_allow') . ' AS `ynb2`';
        $sSql .= ' WHERE ynb2.to_id = ' . (int)$userid;
		$sSql .= ' ) ';


        $aRows = $this->database()->getSlaveRows($sSql);
        $this->cache()->save($sCacheId, $aRows);

        return $aRows;
    }

    public function getAllowListBySide($userid, $side = 'from'){
        $sCacheId = $this->cache()->set('ynchat_getallowlistbyside_' . $side . '_' . $userid);
        if (($aRows = $this->cache()->get($sCacheId))){
            if(true === $aRows){
                return array();
            } else {
                return $aRows;
            }
        }

        $sSql = '';
        $aRows = array();
        switch($side){
            case 'from':
                $sSql .= ' SELECT ynb1.to_id as user_id';
                $sSql .= ' FROM ' . ('ynchat_allow') . ' AS `ynb1`';
                $sSql .= ' WHERE ynb1.from_id = ' . (int)$userid;
                break;
            case 'to':
                $sSql .= ' SELECT ynb2.from_id as user_id';
                $sSql .= ' FROM ' . ('ynchat_allow') . ' AS `ynb2`';
                $sSql .= ' WHERE ynb2.to_id = ' . (int)$userid;
                break;
        }
        if(strlen($sSql) > 0){
            $aRows = $this->database()->getSlaveRows($sSql);
        }

        $this->cache()->save($sCacheId, $aRows);
        return $aRows;
    }

    public function getAllowListBySideMoreInfo($userid, $side = 'from'){
        $sCacheId = $this->cache()->set('ynchat_getallowlistbysidemoreinfo_' . $side . '_' . $userid);
        if (($aRows = $this->cache()->get($sCacheId))){
            if(true === $aRows){
                return array();
            } else {
                return $aRows;
            }
        }

        $sSql = '';
        $aRows = array();
        switch($side){
            case 'from':
                $sSql .= ' SELECT
                              user.user_id user_id,
                              user.full_name full_name,
                              user.user_image user_image,
                              user.user_name user_name,
                              user.server_id user_server_id
                        ';
                $sSql .= ' FROM ' . ('ynchat_allow') . ' AS `ynb1`';
                $sSql .= ' JOIN ' . Phpfox::getT('user') . ' AS `user`';
                $sSql .= ' ON ynb1.to_id = user.user_id ';
                $sSql .= ' WHERE ynb1.from_id = ' . (int)$userid;
                break;
            case 'to':
                $sSql .= ' SELECT
                              user.user_id user_id,
                              user.full_name full_name,
                              user.user_image user_image,
                              user.user_name user_name,
                              user.server_id user_server_id
                        ';
                $sSql .= ' FROM ' . ('ynchat_allow') . ' AS `ynb2`';
                $sSql .= ' JOIN ' . Phpfox::getT('user') . ' AS `user`';
                $sSql .= ' ON ynb2.from_id = user.user_id ';
                $sSql .= ' WHERE ynb2.to_id = ' . (int)$userid;
                break;
        }
        if(strlen($sSql) > 0){
            $aRows = $this->database()->getSlaveRows($sSql);
        }

        $this->cache()->save($sCacheId, $aRows);
        return $aRows;
    }

    public function removeCache(){
        $this->cache()->remove('ynchat_allfriendlist_' . Phpfox::getUserId());
        $this->cache()->remove('ynchat_getblocklist_' . Phpfox::getUserId());
        $this->cache()->remove('ynchat_getallowlist_' . Phpfox::getUserId());
        $this->cache()->remove('ynchat', 'substr');
    }

    public function getTimeStamp(){
        $time = 0;
        // Default time to GMT
        if (function_exists('date_default_timezone_set'))
        {
            $time = time();
        }
        else
        {
            $time = strtotime(gmdate("M d Y H:i:s", time()));
        }

        return $time;
    }
	
	public function processTime($time) {
		return $time;
	}
	
	public function getLink($user_name){
		return Phpfox::getParam('core.path') . 'index.php?do='. PHPFOX_DS . $user_name . PHPFOX_DS;
	}	
	
	public function getAvatar($aItem) {
        if($aItem['user_image'] != ''){
            return Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aItem['user_server_id'],
                'path' => 'core.url_user',
                'file' => $aItem['user_image'],
                'suffix' => MAX_SIZE_OF_USER_IMAGE,
                'return_url' => true
                    )
            );      
        }
        else{

            return Phpfox::getParam('core.path').'module/ynchat/static/image/profile_50.png';
        
        }
	}

    public function getSiteLink(){
        $site_link = '//' . Phpfox::getParam('core.host') . Phpfox::getParam('core.folder');
        return $site_link;
    }

    public function isUser(){
        return Phpfox::isUser();
    }

    public function isModule($sModule){
        return Phpfox::isModule('ynchat');
    }

    public function getHost(){
        return Phpfox::getParam('core.host');
    }

    // simplier get lang and configure
    // return config & userseting.
    // this method call from mfox module
    public function alias_mfox_getConfig($aData) {

        $result =  $this->getLangAndConfig();

        unset($result['lang']);

        $scheme = 'http';

        // prepare sApiUrl, sSiteLink missing full protocol name
        // because mfox use file:// by default.

        $result['config']['sApiUrl'] = $scheme  .':'.  $result['config']['sApiUrl'];
        $result['config']['sSiteLink'] = $scheme . ':'. $result['config']['sSiteLink'];

        return $result;
    }

    public function getLangAndConfig(){
        $iPort = (int)Phpfox::getParam('ynchat.web_socket_port');
        $sAction = 'chat';
        $iIntervalUpdateFriendList = 10 * 1000;
        $iTimeOut = 30 * 1000;
        $iImageSizeLimit = 1024 * 1024;
        $iNumberOfFriendList = Phpfox::getParam('ynchat.number_of_friend_list');
        if((int)$iNumberOfFriendList <= 0){
            $iNumberOfFriendList = 1000;
        }

        $aEmoticon = $this->getAllEmoticon(true);
        $aSticker = $this->getAllSticker(true);
        $sPicUrl = Phpfox::getParam('core.url_pic');
        $aBanWord = $this->getBanFilters('word');
        $sSiteLink = Phpfox::getParam('core.path_file');
        $sApiUrl = $sSiteLink.'module/ynchat/api.php/';

        return array(
            'lang' => array(
                'your_browser_does_not_support_javascript' => html_entity_decode(Phpfox::getPhrase('ynchat.your_browser_does_not_support_javascript')),
                'nothing_friend_s_found' => html_entity_decode(Phpfox::getPhrase('ynchat.nothing_friend_s_found')),
                'connection_timed_out_please_try_again' => html_entity_decode(Phpfox::getPhrase('ynchat.connection_timed_out_please_try_again')),
                'search' => html_entity_decode(Phpfox::getPhrase('ynchat.search')),
                'too_big_image_file' => html_entity_decode(Phpfox::getPhrase('ynchat.too_big_image_file')),
                'please_try_another_image' => html_entity_decode(Phpfox::getPhrase('ynchat.please_try_another_image')),
                'upload' => html_entity_decode(Phpfox::getPhrase('ynchat.upload')),
                'add' => html_entity_decode(Phpfox::getPhrase('ynchat.add')),
                'view_old_conversation' => html_entity_decode(Phpfox::getPhrase('ynchat.view_old_conversation')),
                'yesterday' => html_entity_decode(Phpfox::getPhrase('ynchat.yesterday')),
                'days' => html_entity_decode(Phpfox::getPhrase('ynchat.days')),
                'month' => html_entity_decode(Phpfox::getPhrase('ynchat.month')),
                'show_message_from' => html_entity_decode(Phpfox::getPhrase('ynchat.show_message_from')),
                'advanced_settings' => html_entity_decode(Phpfox::getPhrase('ynchat.advanced_settings')),
                'close_all_chat_tabs' => html_entity_decode(Phpfox::getPhrase('ynchat.close_all_chat_tabs')),
                'go_offline' => html_entity_decode(Phpfox::getPhrase('ynchat.go_offline')),
                'go_online' => html_entity_decode(Phpfox::getPhrase('ynchat.go_online')),
                'play_sound_on_new_message' => html_entity_decode(Phpfox::getPhrase('ynchat.play_sound_on_new_message')),
                'yes' => html_entity_decode(Phpfox::getPhrase('ynchat.yes')),
                'no' => html_entity_decode(Phpfox::getPhrase('ynchat.no')),
                'enter_name' => html_entity_decode(Phpfox::getPhrase('ynchat.enter_name')),
                'searching' => html_entity_decode(Phpfox::getPhrase('ynchat.searching')),
                'turn_on_chat_except' => html_entity_decode(Phpfox::getPhrase('ynchat.turn_on_chat_except')),
                'turn_on_chat_for_only_some_friends' => html_entity_decode(Phpfox::getPhrase('ynchat.turn_on_chat_for_only_some_friends')),
                'save' => html_entity_decode(Phpfox::getPhrase('ynchat.save')),
                'chat' => html_entity_decode(Phpfox::getPhrase('ynchat.chat')),
                'mobile' => html_entity_decode(Phpfox::getPhrase('ynchat.mobile')),
                'web' => html_entity_decode(Phpfox::getPhrase('ynchat.web')),
                'message_history' => html_entity_decode(Phpfox::getPhrase('ynchat.message_history')),
                'video' => html_entity_decode(Phpfox::getPhrase('ynchat.video')),
                'photo' => html_entity_decode(Phpfox::getPhrase('ynchat.photo')),
                'add_files' => html_entity_decode(Phpfox::getPhrase('ynchat.add_files')),
                'upload_photo' => html_entity_decode(Phpfox::getPhrase('ynchat.upload_photo')),
                'choose_a_sticker_or_emoticon' => html_entity_decode(Phpfox::getPhrase('ynchat.choose_a_sticker_or_emoticon')),
                'please_try_again_make_sure_you_are_uploading_a_valid_photo' => html_entity_decode(Phpfox::getPhrase('ynchat.please_try_again_make_sure_you_are_uploading_a_valid_photo')),
                'unable_to_connect_to_chat_check_your_internet_connection' => html_entity_decode(Phpfox::getPhrase('ynchat.unable_to_connect_to_chat_check_your_internet_connection')),
            ),
            'config' => array(
                'sServerUrl' => trim(Phpfox::getParam('core.host'), '/'),
                'sSiteLink' => $sSiteLink,
                'sApiUrl' => $sApiUrl,
                'iTimeOut' => $iTimeOut,
                'iPort' => $iPort,
                'iStunnelPort' => Phpfox::getParam('ynchat.stunnel_port'),
                'sIpPublic' => Phpfox::getParam('ynchat.ynchat_ip_public'),
                'sAction' => $sAction,
                'sUserIdHash' => $this->encryptUserId(Phpfox::getUserId()),
                'iIntervalUpdateFriendList' => $iIntervalUpdateFriendList,
                'iNumberOfFriendList' => $iNumberOfFriendList,
                'iImageSizeLimit' => $iImageSizeLimit,
                'bIsEnableVideoAction' => $this->isEnableVideoAction(),
                'bIsEnablePhotoAction' => $this->isEnablePhotoAction(),
                'bIsEnableLinkAction' => $this->isEnableLinkAction(),
                'bIsEnableEmoticonStickerAction' => $this->isEnableEmoticonStickerAction(),
                'aEmoticon' => $aEmoticon,
                'aSticker' => $aSticker,
                'sPicUrl' => $sPicUrl,
                'aBanWord' => $aBanWord,
                'bEnableSSL' => 0,
                'sApiKeyEmbedly' => Phpfox::getParam('ynchat.api_embedly'),
                'iPlacementOfChatFrame' => Phpfox::getParam('ynchat.placement_of_chat_frame'),
            ),
            'usersettings' => $this->getUserSettingsByUserId(Phpfox::getUserId()),
        );
    }

    public function isEnableVideoAction(){
        if(Phpfox::isModule('video')){
            return 1;
        }
        return 0;
    }

    public function isEnablePhotoAction(){
        if(Phpfox::isModule('photo') && Phpfox::getUserParam('photo.can_upload_photos')){
            return 1;
        }
        return 0;
    }

    public function isEnableLinkAction(){
        if(Phpfox::isModule('link')){
            return 1;
        }
        return 0;
    }

    public function isEnableEmoticonStickerAction(){
        return 1;
    }

    public function getUserSettingsByUserId($iUserId){
        if((int)$iUserId <= 0){
            return array();
        }

        $aRow = $this->database()->select('yus.*')
            ->from(('ynchat_usersetting'), 'yus')
            ->where('yus.user_id = ' . $iUserId)
            ->execute('getSlaveRow');

        if(isset($aRow['user_id']) == false){
            // insert if not exist
            $this->database()->insert(('ynchat_usersetting'), array(
                    'user_id' => $iUserId,
                    'is_notifysound' => 1,
                    'is_goonline' => 1,
                    'turnonoff' => 'onall',
                )
            );

            $aRow = array(
                'is_notifysound' => 1,
                'is_goonline' => 1,
                'turnonoff' => 'onall',
            );
        }

        $result = array(
            'iUserId' => (int)$iUserId,
            'iIsNotifySound' => (int)$aRow['is_notifysound'],
            'iIsGoOnline' => (int)$aRow['is_goonline'],
            'sTurnOnOff' => $aRow['turnonoff'],
            'sUserName' => Phpfox::getUserBy('user_name'),
            'sFullName' => Phpfox::getUserBy('full_name'),
            'sLink' => Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name')),
            'sAvatar' => $this->getAvatar(array('user_image' => Phpfox::getUserBy('user_image'), 'user_server_id' => Phpfox::getUserBy('server_id'))),
            'aData' => (is_null($aRow['data']) ? array() : unserialize($aRow['data'])) ,
        );

        return $result;
    }

    public function getPhrase($sKey){
        return _p($sKey);
    }

    public function getUserId(){
        return Phpfox::getUserId();
    }

    public function encryptUserId($iUserId){
        $salt = $this->getSalt($iUserId);
        return Phpfox::getService('ynchat.helper')->encrypt($iUserId, $salt);
    }

    public function decryptUserId($sHash){
        $salt = $this->getSalt();
        return Phpfox::getService('ynchat.helper')->decrypt($sHash, $salt);
    }

    public function getSalt(){
        $cryptKey  = Phpfox::getParam('core.id_hash_salt');
        return $cryptKey;
    }

    public function setUserId($iUserId){
        Phpfox::getService('user.auth')->setUserId($iUserId);
    }

    public function setUser($iUserId){
        Phpfox::getService('user.auth')->setUserId($iUserId);
        $aUser = Phpfox::getService('user')->get($iUserId);
        Phpfox::getService('user.auth')->setUser($aUser);
    }

    public function validateUser($sUserIdHash){
        // validate user
        $iUserId = (int)$this->decryptUserId($sUserIdHash);
        $this->reconnectDatabase();
        $aUser = $this->getUser($iUserId);

        // login if be valid
        if(isset($aUser['user_id'])){
            $this->reconnectDatabase();
            $oAuth = Phpfox::getService('user.auth') ;
            $oAuth -> setUserId($iUserId);
            $oAuth->setUser($aUser);

            return $iUserId;
        }

        if(defined('YNCHAT_DEBUG') && YNCHAT_DEBUG){
            $this->log_message(PHPFOX_DIR . 'ynchat/log/', "debug", 'validateUser -- $sUserIdHash -- ' . print_r($sUserIdHash, true));
            $this->log_message(PHPFOX_DIR . 'ynchat/log/', "debug", 'validateUser -- $iUserId -- ' . print_r($iUserId, true));
            $this->log_message(PHPFOX_DIR . 'ynchat/log/', "debug", 'validateUser -- $aUser -- ' . print_r($aUser, true));
        }
        return false;
    }

    /**
     * We accept status in {'available','away','busy','invisible','offline'}
     * @param string $sStatus
     */
    public function updateUserStatus($iUserId, $sStatus = 'available'){

        if((int)$iUserId > 0){
            $this->reconnectDatabase();
            // remove old status
            $this->database()->delete(('ynchat_status'), 'user_id = ' . (int) $iUserId);

            $this->reconnectDatabase();
            // insert new status
            $id = $this->database()->insert(('ynchat_status'), array(
                    'user_id' => $iUserId,
                    'status' => $sStatus,
                )
            );
            return true;
        }

        return false;
    }

    public function getUpdateFriendList($aData){
        $oAjax = Phpfox::getLib("ajax");

        $buddyList = $this->getFriendsList(Phpfox::getUserId());
        Phpfox::getBlock('ynchat.friendlist', array('buddyList' => $buddyList));
        $friendlist = $oAjax->getContent(false);

        return array(
            'error_message' => '',
            'error_code' => 0,
            'friendlist' => $friendlist,
            'result' => 1
        );
    }

    public function updateLastActivity($iUserId = null){
        $this->reconnectDatabase();        
        if(null == $iUserId){
            $iUserId = Phpfox::getUserId();
        }
        $this->database()->update(Phpfox::getT('user')
            , array('ynchat_lastactivity' => $this->getTimeStamp())
            , 'user_id = ' . (int) $iUserId);
    }

    public function addMessage($aVals = array()){
        // init
        // process
        $aInsert = array(
            '`from`' => (int) $aVals['from'],
            '`to`' => (int) $aVals['to'],
            '`message`' => $aVals['message'],
            '`sent`' => $this->getTimeStamp(),
            '`read`' => (int)$aVals['read'],
            '`direction`' => 0,
        );
        if(isset($aVals['message_type'])){
            $aInsert['message_type'] = $aVals['message_type'];
        } else {
            $aInsert['message_type'] = 'text';
        }
        if($aInsert['message_type'] == 'text'){
            $aInsert['`message`'] = $this->checkBan($aInsert['`message`'], 'word');
            $aInsert['`message`'] = htmlspecialchars($aInsert['`message`']);
            $aInsert['`message`'] = nl2br($aInsert['`message`']);
            $aInsert['`message`'] = $this->parseEmoticon($aInsert['`message`']);
        } else if($aInsert['message_type'] == 'sticker'){
            $sText = $this->parseSticker($aVals['sticker_id']);
            $aInsert['`message`'] = $sText;
        }
        if(isset($aVals['data'])){
            $aInsert['data'] = $aVals['data'];
        }

        $this->reconnectDatabase();
        $iMessageId = $this->database()->insert(('ynchat_message'), $aInsert);

        if((int)$iMessageId > 0){
            $aMessage = $this->__generateMessageData(array(
                'message_id' => $iMessageId,
                'from' => (int) $aVals['from'],
                'to' => (int) $aVals['to'],
                'message' => $aInsert['`message`'],
                'sent' => $this->getTimeStamp(),
                'read' => (int)$aVals['read'],
                'direction' => 0,
                'message_type' => $aInsert['message_type'],
				'data' => (isset($aVals['data'])) ? $aVals['data'] : '',
            ), 'large');
        } else {
            $aMessage = array();
        }

        (($sPlugin = Phpfox_Plugin::get('ynchat.service_ynchat_add_message__end')) ? eval($sPlugin) : false);

        // end
        return $aMessage;
    }

    public function updateReadMessageByMessageId($iMessageId, $iRead = 0){
        if((int)$iMessageId){
            $this->database()->update(('ynchat_message')
                , array('`read`' => (int)$iRead)
                , 'message_id = ' . $iMessageId
            );

            return true;
        }

        return false;
    }

    public function getMessageByFriendId($aData){
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }
        $iFriendId = isset($aData['iFriendId']) ? (int)$aData['iFriendId'] : 0;

        $aRows = $this->database()->select('ynm.*')
            ->from(('ynchat_message'), 'ynm')
            ->where(' (ynm.from = ' . Phpfox::getUserId() . ' AND ynm.to = ' . $iFriendId
                . ') OR (ynm.from = ' . $iFriendId . ' AND ynm.to = ' . Phpfox::getUserId() . ') ')
            ->execute('getSlaveRows');

        return array(
            'aMessages' => $aRows,
            'error_message' => '',
            'error_code' => 0
        );
    }

    public function getInfoFriend($aData){
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }

        $iFriendId = isset($aData['iFriendId']) ? (int)$aData['iFriendId'] : 0;
        $iReceiver = isset($aData['iReceiver']) ? (int)$aData['iReceiver'] : 0;
        $aRow = $this->database()->select('
                      user.user_id user_id,
                      user.full_name full_name,
                      user.ynchat_lastactivity ynchat_lastactivity,
                      user.user_image user_image,
                      user.user_name user_name,
                      user.status message,
                      user.server_id user_server_id,
                      IFNULL(ys.status, \'offline\')  AS `status`,
                      IFNULL(ys.agent, \'\')  AS `agent`
                    ')
            ->from(Phpfox::getT('user'), 'user')
            ->leftJoin(('ynchat_status'), 'ys', 'ys.user_id = user.user_id')
            ->where('user.user_id = ' . $iFriendId)
            ->execute('getSlaveRow');
        if(isset($aRow['user_id']) == false){
            $aRow = array();
        } else {
            if(empty($aRow['status'])){
                $aRow['status'] = 'offline';
            }

            if ($aRow['message'] == null) {
                $aRow['message'] = '';
            }

            $aRow['link'] = $this->getLink($aRow['user_name']);
            $aRow['avatar'] = $this->getAvatar($aRow);

            if (empty($aRow['grp'])) {
                $aRow['grp'] = '';
            }
        }

        return array(
            'aUser' => $aRow,
            'iReceiver' => $iReceiver,
            'error_message' => '',
            'error_code' => 0
        );
    }

    public function threadInfo($aData){
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }

        $iFriendId = isset($aData['iFriendId']) ? (int)$aData['iFriendId'] : 0;
        $iReceiver = isset($aData['iReceiver']) ? (int)$aData['iReceiver'] : 0;
        $iMessageId = isset($aData['iMessageId']) ? (int)$aData['iMessageId'] : 0;
        $iNew = isset($aData['iNew']) ? (int)$aData['iNew'] : 0;

        if($iNew){
            // get ONE message
            $aFriend = $this->__getInfoFriend($iFriendId);
            $aMessage = $this->__getMessageByMessageId($iMessageId);
            if(isset($aMessage['message_id'])){
                $aMessage = $this->__generateMessageData($aMessage, 'large');
            }
            return array(
                'aFriend' => $aFriend,
                'aMessage' => $aMessage,
                'iReceiver' => $iReceiver,
                'error_message' => '',
                'error_code' => 0
            );
        } else {
            // get old messages (compare with iMessageId)
            $aMoreMessages = array();
            $aRows = $this->__getMessagesByFriendId($iFriendId, $iMessageId);
            if(count($aRows) > 0){
                foreach($aRows as $aItem){
                    $aMoreMessages[] = $this->__generateMessageData($aItem, 'large');
                }
            }
            // add separator
            $aMoreMessages = array_reverse($aMoreMessages);
            $aMoreMessages = $this->addSeparatorMessage($aMoreMessages, Phpfox::getUserId(), $iFriendId);

            return array(
                'aMoreMessages' => $aMoreMessages,
                'iFriendId' => $iFriendId,
                'iReceiver' => $iReceiver,
                'error_message' => '',
                'error_code' => 0
            );
        }
    }

    public function addSeparatorMessage($aMessages, $iSenderId, $iReceiverId){
        $result = array();
        if(count($aMessages) > 0){
            $oHelper = Phpfox::getService('ynchat.helper');
            $now = $this->getTimeStamp();
            $nowOfViewer = $oHelper->convertToUserTimeZone($now);
            $y = $oHelper->convertTime($nowOfViewer, 'Y-m-d');
            $oneOfViewer = $oHelper->convertToUserTimeZone($aMessages[0]['iTimeStamp']);
            $x = $oHelper->convertTime($oneOfViewer, 'Y-m-d');
            $z = null;
            $separator = null;
            $limit = 5 * 60 * 60;

            // first check
            if($x == $y){
                $separator = Phpfox::getLib('date')->convertTime($aMessages[0]['iTimeStamp']);
                $z = $aMessages[0]['iTimeStamp'];
            } else {
                $separator = $oHelper->convertTime($oneOfViewer, 'F j, Y');
            }
            $result[] = array(
                'iMessageId' => 0,
                'iSenderId' => $iSenderId,
                'iReceiverId' => $iReceiverId,
                'sText' => '<div><span>' . $separator . '</span></div>',
                'iTimeStamp' => $aMessages[0]['iTimeStamp'] - 1,
                'bRead' => true,
                'iDirection' => 0,
                'sType' => 'separator'
            );

            // check all
            $separator = null;
            foreach($aMessages as $aItem){
                $dateOfViewer = $oHelper->convertToUserTimeZone($aItem['iTimeStamp']);
                $date = $oHelper->convertTime($dateOfViewer, 'Y-m-d');
                if($date != $x){
                    $x = $date;
                    $separator = $oHelper->convertTime($dateOfViewer, 'F j, Y');
                }
                if($x == $y){
                    if($aItem['iTimeStamp'] - $z > $limit || null == $z){
                        $z = $aItem['iTimeStamp'];
                        $separator = Phpfox::getLib('date')->convertTime($aItem['iTimeStamp']);
                    }
                }

                if(null != $separator){
                    $result[] = array(
                        'iMessageId' => 0,
                        'iSenderId' => $iSenderId,
                        'iReceiverId' => $iReceiverId,
                        'sText' => '<div><span>' . $separator . '</span></div>',
                        'iTimeStamp' => $aItem['iTimeStamp'] - 1,
                        'bRead' => true,
                        'iDirection' => 0,
                        'sType' => 'separator'
                    );

                    $separator = null;
                }

                $result[] = $aItem;
            }
        }

        return $result;
    }

    private function __getMessageByMessageId($iMessageId){
        $aRow = $this->database()->select('ynm.*')
            ->from(('ynchat_message'), 'ynm')
            ->where('ynm.message_id = ' . (int)$iMessageId)
            ->execute('getSlaveRow');

        if(isset($aRow['message_id']) == false){
            $aRow = array();
        }

        return $aRow;
    }

    public function getMessageByMessageId($iMessageId){
        $this->reconnectDatabase();
        $result = $this->__getMessageByMessageId($iMessageId);
        if(isset($result['message_id'])){
            $result = $this->__generateMessageData($result);
        }

        return $result;
    }

    private function __getMessagesByFriendId($iFriendId, $iMessageId = null, $bLoadAll = false, $sOrder = null, $iStartTime = null, $iEndTime = null){
        $sWhere = '';
        if((int)$iMessageId > 0){
            $sWhere .= ' AND ynm.message_id < ' . (int)$iMessageId;
        }
        if((int)$iStartTime > 0 && (int)$iEndTime > 0){
            $sWhere .= ' AND ynm.sent >= ' . (int)$iStartTime . ' AND ynm.sent <= ' . (int)$iEndTime;
        }
        if(!$sOrder)
            $sOrder = 'ynm.message_id DESC';

        if($bLoadAll == false){
            $iLimit = (int)Phpfox::getParam('ynchat.number_of_old_message');
            if($iLimit <= 0){
                $iLimit = 10;
            }

            $aRows = $this->database()->select('ynm.*')
                ->from(('ynchat_message'), 'ynm')
                ->where(' ( (ynm.from = ' . Phpfox::getUserId() . ' AND ynm.to = ' . $iFriendId
                    . ') OR (ynm.from = ' . $iFriendId . ' AND ynm.to = ' . Phpfox::getUserId() . ') ) '
                    . $sWhere
                )
                ->limit($iLimit)
                ->order($sOrder)
                ->execute('getSlaveRows');
        }
        else {
            $aRows = $this->database()->select('ynm.*')
                ->from(('ynchat_message'), 'ynm')
                ->where(' ( (ynm.from = ' . Phpfox::getUserId() . ' AND ynm.to = ' . $iFriendId
                    . ') OR (ynm.from = ' . $iFriendId . ' AND ynm.to = ' . Phpfox::getUserId() . ') ) '
                    . $sWhere
                )
                ->order($sOrder)
                ->execute('getSlaveRows');
        }
        return $aRows;
    }

    public function getAvatarByUserId($user_id){
        $aUser = $this->getUser($user_id);
        if(isset($aUser['user_id']) == false){
            return "";
        }
        $aUser['user_server_id'] = $aUser['server_id'];

        return $this->getAvatar($aUser);
    }

    private function __generateMessageData($aItem, $sMoreInfo = 'large'){
        $oParseOutput = Phpfox::getLib('parse.output');
        $timeViewer = Phpfox::getService('ynchat.helper')->convertToUserTimeZone($aItem['sent']);
        $sDate = Phpfox::getService('ynchat.helper')->convertTime($timeViewer, 'F j, Y');
        $sTime = Phpfox::getService('ynchat.helper')->convertTime($timeViewer, 'g:i a');
        $sText = $oParseOutput->parse($aItem['message']);
        $sText = htmlspecialchars_decode($sText);
        $sText = strip_tags($sText, '<img>');
        $result = array(
            'iMessageId' => $aItem['message_id'],
            'iSenderId' => $aItem['from'],
            'iReceiverId' => $aItem['to'],
            'sText' => $sText,
            'iTimeStamp' => $aItem['sent'],
            'sTime' => $timeViewer,
            'bRead' => ($aItem['read'] == 1 ? true : false),
            'iDirection' => $aItem['direction'],
			
			'type' => $aItem['message_type'],
			'data' => $aItem['data'],
            'avatar'=> $this->getAvatarByUserId($aItem['from']),

            'sDate' => $sDate,
            'sTime' => $sTime,
        );
        switch ($sMoreInfo) {
            case 'large':
            case 'medium':
            case 'small':
                return $result;
                break;
        }
    }

    private function __getInfoFriend($iFriendId){
        $aRow = $this->database()->select('
                      user.user_id user_id,
                      user.full_name full_name,
                      user.ynchat_lastactivity ynchat_lastactivity,
                      user.user_image user_image,
                      user.user_name user_name,
                      user.status message,
                      user.server_id user_server_id,
                      IFNULL(ys.status, \'offline\')  AS `status`,
                      IFNULL(ys.agent, \'\')  AS `agent`
                    ')
            ->from(Phpfox::getT('user'), 'user')
            ->leftJoin(('ynchat_status'), 'ys', 'ys.user_id = user.user_id')
            ->where('user.user_id = ' . $iFriendId)
            ->execute('getSlaveRow');
        if(isset($aRow['user_id']) == false){
            $aRow = array();
        } else {
            if(empty($aRow['status'])){
                $aRow['status'] = 'offline';
            }

            if ($aRow['message'] == null) {
                $aRow['message'] = '';
            }

            $aRow['link'] = $this->getLink($aRow['user_name']);
            $aRow['avatar'] = $this->getAvatar($aRow);

            if (empty($aRow['grp'])) {
                $aRow['grp'] = '';
            }
        }

        return $aRow;
    }

    /**
     * Get unread and opening box 
     */ 
    public function getUnreadBox($aData){
        // init
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }
        $aUnreadBox = array();

        // process
        // get old messages (compare with iMessageId)
        $listOfUserId = array();
        $aUnreadSenders = $this->getAllSenderIdOfReceiverId(Phpfox::getUserId(), 0);
        foreach ($aUnreadSenders as $key => $value) {
            $listOfUserId[$value['from']] = 1;    // check can send message 
        }
        $usersettings = $this->getUserSettingsByUserId(Phpfox::getUserId());
        $aData = $usersettings['aData'];
        if(is_array($aData)){
            $open = isset($aData['open']) ? $aData['open'] : array();
            if(count($open) > 0){
                foreach ($open as $key => $value) {
                    $listOfUserId[$key] = 0;    // NOT check can send message 
                }
            }
        }

        foreach($listOfUserId as $userid => $check){
            if($check && $this->canSendMessage($userid, Phpfox::getUserId()) == false){
                continue;
            }
            if($check){
                // get unread box
                $aUnreadMessages = $this->getMessageWithSenderIdAndReceiverId($userid, Phpfox::getUserId(), 0);
                $aUnreadMessages['is_unread'] = 1;
            } else {
                // get opening box 
                $aUnreadMessages = $this->getMessageWithSenderIdAndReceiverId($userid, Phpfox::getUserId(), null);
                $aUnreadMessages['is_unread'] = 0;
            }
            
            // if(count($aUnreadMessages) > 0){
                // get sender's info
                $aSenderInfo = $this->__getInfoFriend($userid);
                if(isset($aSenderInfo['user_id']) == false || $aSenderInfo['user_id'] == Phpfox::getUserId()){
                    continue;
                }
                // get old messages (compare with iMessageId)
                $aOldMessages = array();
                if($check){
                    $aOldMessages = $this->__getMessagesByFriendId($userid, $aUnreadMessages[0]['message_id'], false, 'ynm.message_id DESC');
                }
                $aMergeMessages = array_merge($aOldMessages, $aUnreadMessages);
                $aMessages = array();
                foreach($aMergeMessages as $aItem){
                    if(!is_array($aItem)) continue;
                    $aMessages[] = $this->__generateMessageData($aItem, 'large');
                }
                $aUnreadBox[] = array(
                    'aSenderInfo' => $aSenderInfo,
                    'aMessages' => $aMessages,
                    'iUnread' => $aUnreadMessages['is_unread']
                );
            // }
        }

        // end
        return array(
            'aUnreadBox' => $aUnreadBox,
            'error_message' => '',
            'error_code' => 0
        );
    }

    public function getAllSenderIdOfReceiverId($iReceiverId, $iRead = null){
        // init
        if((int)$iReceiverId <= 0){
            return array();
        }

        // process
        $sWhere = '';
        if(null !== $iRead){
            $sWhere .= ' AND ynm.read = ' . (int)$iRead;
        }
        $aRows = $this->database()->select('DISTINCT (ynm.from)')
            ->from(('ynchat_message'), 'ynm')
            ->where('ynm.to = ' . $iReceiverId
                . $sWhere
            )
            ->order('ynm.from ASC')
            ->execute('getSlaveRows');
        // end
        return $aRows;
    }

    public function getMessageWithSenderIdAndReceiverId($iSenderId, $iReceiverId, $iRead = null){
        // init
        if((int)$iReceiverId <= 0 || (int)$iSenderId <= 0){
            return array();
        }

        // process
        $sWhere = '';
        $iLimit = (int)Phpfox::getParam('ynchat.number_of_old_message');
        if(null !== $iRead){
            $sWhere .= ' AND ynm.read = ' . (int)$iRead;
            $aRows = $this->database()->select('ynm.*')
                ->from(('ynchat_message'), 'ynm')
                ->where('ynm.from = ' . $iSenderId . ' AND ynm.to = ' . $iReceiverId
                    . $sWhere
                )
                ->order('ynm.message_id DESC')
                ->execute('getSlaveRows');
        } else {
            $aRows = $this->database()->select('ynm.*')
                ->from(('ynchat_message'), 'ynm')
                ->where(' ( ( ynm.from = ' . $iSenderId . ' AND ynm.to = ' . $iReceiverId . ' ) OR ( ynm.from = ' . $iReceiverId . ' AND ynm.to = ' . $iSenderId . ') ) '
                    . $sWhere
                )
                ->group('ynm.message_id') 
                ->order('ynm.message_id DESC')
                ->limit($iLimit)
                ->execute('getSlaveRows');
        }
        // end
        return $aRows;
    }

    public function updateStatusMessage($aData){
        // init
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }
        $iFriendId = isset($aData['iFriendId']) ? (int)$aData['iFriendId'] : 0;

        // process
        $this->updateStatusMessageBySenderId($iFriendId, 1);

        // end
        return array(
            'error_message' => '',
            'error_code' => 0
        );
    }

    public function updateStatusMessageBySenderId($iSenderId, $iRead = 1){
        if((int)$iSenderId <= 0){
            return false;
        }

        $sWhere = '';
        if($iRead == 0){
            $sWhere .= ' AND `read` = 1';
        } else if($iRead == 1){
            $sWhere .= ' AND `read` = 0';
        }
        $this->database()->update(('ynchat_message')
            , array('`read`' => (int)$iRead)
            , '`from` = ' . (int) $iSenderId . $sWhere
        );

        return true;
    }

    public function searchFriend($aData)
    {
        // init
        $results = array();
        $sSearch = '';
        $type = '';
        if(isset($aData['input'])){
            $type = 'friend_chat';
            $sSearch = trim($aData['input']);
        } else if(isset($aData['q'])){
            $type = 'friend_setting';
            $sSearch = trim($aData['q']);
        }
        if(strlen($sSearch) > 0){
            switch($type){
                case 'friend_chat';
                    $aRows = $this->getFriendsListNotRestriction(Phpfox::getUserId(), null, $sSearch,$type);
                    foreach($aRows as $key => $val){
                        $results[] = array_merge($val, array(
                            'id' => $val['user_id'],
                            'value' => $val['full_name'],
                            'info' => $val['user_name'],
                        ));
                    }
                    return array('results' => $results);
                    break;
                case 'friend_setting';
                    $aRows = $this->getFriendsListNotRestriction(Phpfox::getUserId(), null, $sSearch,$type);
                    foreach($aRows as $key => $val){
                        $results[] = array(
                            'id' => $val['user_id'],
                            'name' => $val['full_name'],
                            'user_id' => $val['user_id'],
                            'full_name' => $val['full_name'],
                            'user_name' => $val['user_name'],
                            'avatar' => ($val['avatar']),
                        );
                    }
                    return $results;
                    break;
            }
        }
    }

    public function uploadPhoto($aData){
        if($this->isEnablePhotoAction() && isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            // check $_FILES['ImageFile'] not empty
            if(!isset($_FILES['image']) || !is_uploaded_file($_FILES['image']['tmp_name'])){
                    die('Image file is Missing!'); // output error when above checks fail.
            }

            $oFile = Phpfox::getLib('file');
            $oImage = Phpfox::getLib('image');
            $aVals = array(
                'privacy' => 0,
                'privacy_comment' => 0,
                'album_id' => '',
                'is_cover_photo' => 0,
                'status_info' => '',
                'user_status' => '',
                'destination' => '',
                'iframe' => 1,
                'method' => 'simple',
                'video_inline' => 1,
                'video_title' => '',
                'action' => '',
                'page_id' => 0,
            );

            if (!is_array($aVals))
            {
                $aVals = array();
            }

            $bIsInline = false;
            if (isset($aVals['action']) && $aVals['action'] == 'upload_photo_via_share')
            {
                $bIsInline = true;
            }

            $oServicePhotoProcess = Phpfox::getService('photo.process');
            $aImages = array();
            $iFileSizes = 0;
            $iCnt = 0;
            $orgSize = null;
            $orgExt = null;

            if ($_FILES['image']['error']  == UPLOAD_ERR_OK)
            {
                $iKey = 0;
                $iLimitUpload = null;
                if ($aImage = $oFile->load('image', array('jpg', 'gif', 'png'), $iLimitUpload))
                {
                    $aVals['description'] = ($aVals['is_cover_photo']) ? null : $aVals['status_info'];
                    $aVals['type_id'] = ($aVals['is_cover_photo']) ? '2' : '1';

                    if ($iId = $oServicePhotoProcess->add(Phpfox::getUserId(), array_merge($aVals, $aImage)))
                    {
                        $iCnt++;
                        $aPhoto = Phpfox::getService('photo')->getForProcess($iId);

                        // Move the uploaded image and return the full path to that image.
                        $sFileName = $oFile->upload('image[' . $iKey . ']', Phpfox::getParam('photo.dir_photo'), (Phpfox::getParam('photo.rename_uploaded_photo_names') ? Phpfox::getUserBy('user_name') . '-' . $aPhoto['title'] : $iId), (Phpfox::getParam('photo.rename_uploaded_photo_names') ? array() : true));

                        // Get the original image file size.
                        $iFileSizes += filesize(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));

                        // Get the current image width/height
                        $aSize = getimagesize(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                        $orgSize = $aSize;
                        $orgExt = $aImage['ext'];

                        // Update the image with the full path to where it is located.
                        $oServicePhotoProcess->update(Phpfox::getUserId(), $iId, array(
                            'destination' => $sFileName,
                            'width' => $aSize[0],
                            'height' => $aSize[1],
                            'description' => $aVals['description'],
                            'server_id' => 0,
                            'allow_rate' => (empty($aVals['album_id']) ? '1' : '0')
                                )
                        );

                        // Assign vars for the template.
                        $aImages[] = array(
                            'photo_id' => $iId,
                            'server_id' => 0,
                            'destination' => $sFileName,
                            'description' => $aVals['description'],
                            'name' => $aImage['name'],
                            'ext' => $aImage['ext'],
                            'size' => $aImage['size'],
                            'width' => $aSize[0],
                            'height' => $aSize[1],
                            'completed' => 'false'
                        );
                    }
                }
            }

        $iFeedId = 0;

        // Make sure we were able to upload some images
        if (count($aImages))
        {
            if (defined('PHPFOX_IS_HOSTED_SCRIPT'))
            {
                unlink(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
            }

            (!empty($aVals['callback_module']) ? (Phpfox::hasCallback($aVals['callback_module'], 'addPhoto') ? Phpfox::callback($aVals['callback_module'] . '.addPhoto', $aVals['callback_item_id']) : null) : null);
            $sAction = (isset($aVals['action']) ? $aVals['action'] : 'view_photo');

            // Update the user space usage
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'photo', $iFileSizes);
            $sCallbackModule = null;
            $iCallbackItemId = null;
            $bIsCoverPhoto = $aVals['is_cover_photo'];

            foreach ($aImages as $iKey => $aImage)
            {
                if ($aImage['completed'] == 'false')
                {
                    $aPhoto = Phpfox::getService('photo')->getForProcess($aImage['photo_id']);
                    if (isset($aPhoto['photo_id']))
                    {
                        if (Phpfox::getParam('core.allow_cdn'))
                        {
                            Phpfox::getLib('cdn')->setServerId($aPhoto['server_id']);
                        }

                        if ($aPhoto['group_id'] > 0)
                        {
                            $iGroupId = $aPhoto['group_id'];
                        }

                        $sFileName = $aPhoto['destination'];

                        // fix rotate bug
                        if(Phpfox::isMobile() && $orgExt !== null){
                            if (($orgExt == 'jpg' || $orgExt == 'jpeg') && function_exists('exif_read_data'))
                            {
                                $exif = exif_read_data(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                                if(!empty($exif['Orientation'])){
                                    switch($exif['Orientation'])
                                    {
                                        case 1:
                                        case 2:
                                            break;
                                        case 3:
                                        case 4:
                                            // 90 degrees
                                            $oImage->rotate(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''), 'right');
                                            // 180 degrees
                                            $oImage->rotate(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''), 'right');
                                            break;
                                        case 5:
                                        case 6:
                                            // 90 degrees right
                                            $oImage->rotate(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''), 'right');
                                            break;
                                        case 7:
                                        case 8:
                                            // 90 degrees left
                                            $oImage->rotate(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''), 'left');
                                            break;
                                        default:
                                            break;
                                    }
                                }
                            }
                        }

                        foreach (Phpfox::getParam('photo.photo_pic_sizes') as $iSize)
                        {
                            // Create the thumbnail
                            if ($oImage->createThumbnail(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''), Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize, true, ((Phpfox::getParam('photo.enabled_watermark_on_photos') && Phpfox::getParam('core.watermark_option') != 'none') ? (Phpfox::getParam('core.watermark_option') == 'image' ? 'force_skip' : true) : false)) === false)
                            {
                                continue;
                            }

                            if (Phpfox::getParam('photo.enabled_watermark_on_photos'))
                            {
                                $oImage->addMark(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize));
                            }

                            // Add the new file size to the total file size variable
                            $iFileSizes += filesize(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize));

                            if (defined('PHPFOX_IS_HOSTED_SCRIPT'))
                            {
                                unlink(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize));
                            }
                        }

                        // Get is_page variable.
                        $bIsPage = ((isset($aVals['page_id']) && !empty($aVals['page_id'])) ? 1 : 0);

                        if (Phpfox::getParam('photo.delete_original_after_resize') && $bIsPage != 1)
                        {
                            Phpfox::getLib('file')->unlink(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                        }
                        else if (Phpfox::getParam('photo.enabled_watermark_on_photos'))
                        {
                            $oImage->addMark(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                        }

                        $aImages[$iKey]['completed'] = 'true';

                        break;
                    }
                }
            }

            // Update the user space usage
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'photo', $iFileSizes);

            $iNotCompleted = 0;
            foreach ($aImages as $iKey => $aImage)
            {
                if ($aImage['completed'] == 'false')
                {
                    $iNotCompleted++;
                }
            }

            if ($iNotCompleted === 0)
            {
                ($sCallbackModule ? (Phpfox::hasCallback($sCallbackModule, 'addPhoto') ? Phpfox::callback($sCallbackModule . '.addPhoto', $iCallbackItemId) : null) : null);
                $iFeedId = 0;

                if (!Phpfox::getUserParam('photo.photo_must_be_approved') && !$bIsCoverPhoto)
                {
                    if(isset($aData['isPostStatus']) && $aData['isPostStatus']) {
                        // create feed only for post status
                        // with other upload photo, we are using photo/postfeed after uploading done
                        if (count($aImages) && !$sCallbackModule)
                        {
                            foreach ($aImages as $aImage)
                            {
                                if ($aImage['photo_id'] == $aPhoto['photo_id'])
                                {
                                    continue;
                                }

                                Phpfox::getLib('database')->insert(Phpfox::getT('photo_feed'), array(
                                    'feed_id' => $iFeedId,
                                    'photo_id' => $aImage['photo_id']
                                        )
                                );
                            }
                        }
                    }
                }

                // this next if is the one you will have to bypass if they come from sharing a photo in the activity feed.
                if ($sAction == 'upload_photo_via_share')
                {
                    if ($bIsCoverPhoto)
                    {
                        Phpfox::getService('user.process')->updateCoverPhoto($aImage['photo_id']);
                    }
                    else
                    {
                        $aFeeds = Phpfox::getService('feed')->get(Phpfox::getUserId(), $iFeedId);

                        if (!isset($aFeeds[0]))
                        {
                            Phpfox::addMessage(Phpfox::getPhrase('feed.this_item_has_successfully_been_submitted'));
                        }
                    }

                    Phpfox::addMessage(Phpfox::getPhrase('photo.photo_successfully_uploaded'));
                }
                else
                {
                    // Only display the photo block if the user plans to upload more pictures
                    if ($sAction == 'view_photo')
                    {
                        Phpfox::addMessage((count($aImages) == 1 ? Phpfox::getPhrase('photo.photo_successfully_uploaded') : Phpfox::getPhrase('photo.photos_successfully_uploaded')));
                    }
                    elseif ($sAction == 'view_album' && isset($aImages[0]['album']))
                    {
                        Phpfox::addMessage((count($aImages) == 1 ? Phpfox::getPhrase('photo.photo_successfully_uploaded') : Phpfox::getPhrase('photo.photos_successfully_uploaded')));
                    }
                    else
                    {
                        Phpfox::addMessage((count($aImages) == 1 ? Phpfox::getPhrase('photo.photo_successfully_uploaded') : Phpfox::getPhrase('photo.photos_successfully_uploaded')));
                    }
                }

                // add message photo
                $photoUrl = Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aPhoto['server_id'],
                        'path' => 'photo.url_photo',
                        'file' => $aPhoto['destination'],
                        'suffix' => '_500',
                        'return_url' => true
                            )
                    );
                $message = '';
                $message .= '<a target="_blank" href="' . Phpfox::getLib('url')->makeUrl('photo') . $aPhoto['photo_id'] . '/' . $aPhoto['title'] . '/" class="">';
                $message .= '<img src="' . $photoUrl . '" alt="" />';
                $message .= '</a>';
                $aVals = array(
                    'from' => Phpfox::getUserId(),
                    'to' => (int) $aData['iUserId'],
                    'message' => $message,
                    'read' => 0,
                    'direction' => 0,
                    'message_type' => 'photo',
                );
                $aMessage = $this->addMessage($aVals);

                return array(
                    'result' => true,
                    'aMessage' => $aMessage,
                    'error_message' => '',
                    'error_code' => 0,
                    'sType' => 'photo',
                );
            }
        }
      }
    }

    public function attachVideo($aData){
        if(!$this->isEnableVideoAction()){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.cannot_attach_video_link'),
                'error_code' => 1
            );
        }
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }
        $iUserId = isset($aData['iUserId']) ? (int)$aData['iUserId'] : 0;
        $sUrl = isset($aData['sUrl']) ? $aData['sUrl'] : '';
        if(empty($sUrl)){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_input_valid_link'),
                'error_code' => 1
            );
        }
        $sUrl = base64_decode($sUrl);
        if (Phpfox::getService('video.grab')->get($sUrl))
        {
            if (($sEmbed = Phpfox::getService('video.grab')->embed()))
            {
                $sUrlParse = parse_url($sUrl, PHP_URL_QUERY);
                $aUrlParts = explode('&', $sUrlParse);
                $sUrlDb = '';
                foreach ($aUrlParts as $sPart)
                {
                    if (strpos($sPart, 'v=') !== false)
                    {
                        $sUrlDb = str_replace('v=', '', $sPart);
                        break;
                    }
                }

                $message = '';
                $message .= '<iframe width="560" height="315" src="//www.youtube.com/embed/' . $sUrlDb . '?rel=0" frameborder="0" allowfullscreen></iframe>';
                $aVals = array(
                    'from' => Phpfox::getUserId(),
                    'to' => (int) $iUserId,
                    'message' => $message,
                    'read' => 0,
                    'direction' => 0,
                    'message_type' => 'video',
                );
                $aMessage = $this->addMessage($aVals);

                return array(
                    'result' => true,
                    'iUserId' => $iUserId,
                    'aMessage' => $aMessage,
                    'error_message' => '',
                    'error_code' => 0
                );
            }
        }

        return array(
            'result' => FALSE,
            'error_message' => Phpfox::getPhrase('ynchat.cannot_attach_video_link'),
            'error_code' => 1
        );
    }

    public function attachLink($aData){
        if(!$this->isEnableLinkAction()){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.cannot_attach_link'),
                'error_code' => 1
            );
        }
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }
        $iUserId = isset($aData['iUserId']) ? (int)$aData['iUserId'] : 0;
        $sUrl = isset($aData['sUrl']) ? $aData['sUrl'] : '';
        if(empty($sUrl)){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.cannot_attach_link'),
                'error_code' => 1
            );
        }
        $sUrl = base64_decode($sUrl);
        if (!($aLink = Phpfox::getService('link')->getLink($sUrl)))
        {
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.cannot_attach_link'),
                'error_code' => 1
            );
        } else {
            $message = '';
            $message .= '<div>';
                $message .= '<div>';
                    $message .= '<a href="' . $aLink['link'] . '" target="_blank" >' . $sUrl . '</a>';
                $message .= '</div>';
                $message .= '<div>';
                    $message .= '<div>';
                        $message .= '<div>';
                            $message .= '<a target="_blank" href="' . $aLink['link'] . '" >';
                                $message .= '<img src="' . $aLink['default_image'] . '" alt="">';
                            $message .= '</a>';
                        $message .= '</div>';
                        $message .= '<div>';
                            $message .= '<div>';
                                $message .= '<a href="' . $aLink['link'] . '" target="_blank" >' . $aLink['title'] . '</a>';
                            $message .= '</div>';
                            $message .= '<div>';
                                $message .= $sUrl;
                            $message .= '</div>';
                        $message .= '</div>';
                    $message .= '</div>';
                $message .= '</div>';
            $message .= '</div>';
            $aVals = array(
                'from' => Phpfox::getUserId(),
                'to' => (int) $iUserId,
                'message' => $message,
                'read' => 0,
                'direction' => 0,
                'message_type' => 'link',
            );
            $aMessage = $this->addMessage($aVals);

            return array(
                'result' => true,
                'iUserId' => $iUserId,
                'aMessage' => $aMessage,
                'error_message' => '',
                'error_code' => 0
            );
        }
    }

    public function getAllEmoticon($format = false){
        $aRows = $this->database()->select('yne.*')
            ->from(('ynchat_emoticon'), 'yne')
            ->where('1=1')
            ->execute('getSlaveRows');
        if($format){
            $result = array();
            foreach($aRows as $aItem){
                $result[] = $this->__generateEmoticonData($aItem, 'large');
            }
            return $result;
        }

        return $aRows;
    }

    public function getAllSticker($format = false){
        $aRows = $this->database()->select('yns.*')
            ->from(('ynchat_sticker'), 'yns')
            ->where('1=1')
            ->execute('getSlaveRows');

        if($format){
            $result = array();
            foreach($aRows as $aItem){
                $result[] = $this->__generateStickerData($aItem, 'large');
            }
            return $result;
        }

        return $aRows;
    }

    private function __generateStickerData($aItem, $sMoreInfo = 'large'){
        $result = array(
            'iStickerId' => $aItem['sticker_id'],
            'sTitle' => $aItem['title'],
            'sImage' => $aItem['image'],
            'iOrdering' => $aItem['ordering'],
        );

        switch ($sMoreInfo) {
            case 'large':
            case 'medium':
            case 'small':
                return $result;
                break;
        }
    }

    private function __generateEmoticonData($aItem, $sMoreInfo = 'large'){
        $result = array(
            'iEmoticonId' => $aItem['emoticon_id'],
            'sTitle' => $aItem['title'],
            'sText' => $aItem['text'],
            'sImage' => $aItem['image'],
            'iOrdering' => $aItem['ordering'],
        );

        switch ($sMoreInfo) {
            case 'large':
            case 'medium':
            case 'small':
                return $result;
                break;
        }
    }

	public function parseEmoticon($sTxt)
	{
        $sPicUrl = Phpfox::getParam('core.url_pic');
        $path = $sPicUrl . 'ynchat_emoticon' . PHPFOX_DS;

        $sCacheId = $this->cache()->set('ynchat_emoticon_parse');
        if (!($aEmoticons = $this->cache()->get($sCacheId)))
        {
            $aRows = $this->getAllEmoticon(false);
            foreach ($aRows as $aItem)
            {
                $aEmoticons[$aItem['text']] = $aItem;
            }

            $this->cache()->save($sCacheId, $aEmoticons);
        }

		foreach ($aEmoticons as $sKey => $aEmoticon)
		{
			$sTxt = str_replace($sKey, '<img src="' . $path . $aEmoticon['image'] . '" alt="' . $aEmoticon['title'] . '" title="' . $aEmoticon['title'] . '" class="v_middle" />', $sTxt);
			$sTxt = str_replace(str_replace('&lt;', '<', $sKey), '<img src="' . $path . $aEmoticon['image'] . '" alt="' . $aEmoticon['title'] . '" title="' . $aEmoticon['title'] . '" class="v_middle" />', $sTxt);
			$sTxt = str_replace(str_replace('>', '&gt;', $sKey), '<img src="' . $path . $aEmoticon['image'] . '" alt="' . $aEmoticon['title'] . '" title="' . $aEmoticon['title'] . '" class="v_middle" />', $sTxt);
        }

		return $sTxt;
	}

    public function getStickerById($iStickerId){
        $aRow = $this->database()->select('yns.*')
            ->from(('ynchat_sticker'), 'yns')
            ->where('yns.sticker_id = ' . (int)$iStickerId)
            ->execute('getSlaveRow');

        return $aRow;
    }

    public function parseSticker($iStickerId){
        $aRow = $this->getStickerById($iStickerId);
        $sText = '';
        if(isset($aRow['sticker_id'])){


            $sPicUrl = Phpfox::getParam('core.url_pic');
            $path = $sPicUrl . 'ynchat_sticker' . PHPFOX_DS;
            $sText = '<img src="' . $path . $aRow['image'] .'" alt="' . $aRow['title'] . '" title="' . $aRow['title'] . '" />';
        }

        return $sText;
    }

    public function getOldConversation($aData){
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }
        $iUserId = isset($aData['iUserId']) ? (int)$aData['iUserId'] : 0;
        // yesterday/week/month/quarter
        $sType = isset($aData['sType']) ? $aData['sType'] : 'yesterday';
        $now = $this->getTimeStamp();
        $iStartTime = 0;
        $iEndTime = $now;

        // process
        switch($sType){
            case 'yesterday':
                $yesterday = date("Y-m-d", $now - (24 * 60 * 60) );
                $yesterday = explode('-', $yesterday);
                $iStartTime = mktime(0, 0, 0, $yesterday[1], $yesterday[2], $yesterday[0]);
                break;
            case 'week':
                $iStartTime = $now - (7 * 24 * 60 * 60);
                break;
            case 'month':
                $iStartTime = $now - (30 * 24 * 60 * 60);
                break;
            case 'quarter':
                $iStartTime = $now - (90 * 24 * 60 * 60);
                break;
        }

        $aMessages = $this->__getMessagesByFriendId($iUserId, null, true, null, $iStartTime, $iEndTime);
        $result = array();
        foreach($aMessages as $aItem){
            $result[] = $this->__generateMessageData($aItem, 'large');
        }

        // end
        return array(
            'result' => true,
            'error_message' => '',
            'aMessages' => $result,
            'iUserId' => $iUserId,
            'sType' => $sType,
            'error_code' => 0
        );
    }

    public function updateStatusPlaySound($aData){
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }
        $iStatus = isset($aData['iStatus']) ? (int)$aData['iStatus'] : 1;

        $this->database()->update(('ynchat_usersetting')
            , array('is_notifysound' => $iStatus)
            , 'user_id = ' . (int) Phpfox::getUserId());

        return array(
            'result' => true,
            'error_message' => '',
            'error_code' => 0
        );
    }

    public function updateStatusGoOnline($aData){
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }
        $iStatus = isset($aData['iStatus']) ? (int)$aData['iStatus'] : 1;

        $this->database()->update(('ynchat_usersetting')
            , array('is_goonline' => $iStatus)
            , 'user_id = ' . (int) Phpfox::getUserId());

        $this->removeCache();
        return array(
            'result' => true,
            'error_message' => '',
            'error_code' => 0
        );
    }

    public function canSendMessage($iSenderId, $iReceiverId){
        $this->reconnectDatabase();
        $receiverUserSetting = $this->getUserSettingsByUserId($iReceiverId);
        $result = true;
        if($receiverUserSetting['iIsGoOnline'] == 0){
            $result = true;
        } else {
            $this->reconnectDatabase();
            $blockRList = $this->getBlockListBySide($iReceiverId, 'to');
            $blockRUserId = array();
            foreach ($blockRList as $key => $item) {
                $blockRUserId[] = $item['user_id'];
            }
            $this->reconnectDatabase();
            $allowRList = $this->getAllowListBySide($iReceiverId, 'to');
            $allowRUserId = array();
            foreach ($allowRList as $key => $item) {
                $allowRUserId[] = $item['user_id'];
            }
            switch($receiverUserSetting['sTurnOnOff']){
                case 'offall':
                    $result = false;
                    break;
                case 'offsome':
                    if (in_array($iSenderId, $blockRUserId)) {
                        $result = false;
                    }
                    break;
                case 'onsome':
                    if (!in_array($iSenderId, $allowRUserId)) {
                        $result = false;
                    }
                    break;
            }
        }

        return $result;
    }

    public function getAdvancedSetting($aData){
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }
        $blockList = $this->getBlockListBySideMoreInfo(Phpfox::getUserId(), 'to');
        $aBlockList = array();
        foreach($blockList as $key => $val){
            $aBlockList[] = array(
                'id' => $val['user_id'],
                'name' => $val['full_name'],
                'user_id' => $val['user_id'],
                'full_name' => $val['full_name'],
                'user_name' => $val['user_name'],
                'avatar' => '',
            );
        }

        $allowList = $this->getAllowListBySideMoreInfo(Phpfox::getUserId(), 'to');
        $aAllowList = array();
        foreach($allowList as $key => $val){
            $aAllowList[] = array(
                'id' => $val['user_id'],
                'name' => $val['full_name'],
                'user_id' => $val['user_id'],
                'full_name' => $val['full_name'],
                'user_name' => $val['user_name'],
                'avatar' => '',
            );
        }

        return array(
            'result' => true,
            'error_message' => '',
            'aBlockList' => $aBlockList,
            'aAllowList' => $aAllowList,
            'error_code' => 0
        );
    }

    public function saveAdvancedSetting($aData){
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }

        $sTurn = isset($aData['sTurn']) ? $aData['sTurn'] : '';
        $sBlockList = isset($aData['sBlockList']) ? $aData['sBlockList'] : '';
        $sAllowList = isset($aData['sAllowList']) ? $aData['sAllowList'] : '';

        if(strlen($sTurn) > 0){
            if(strlen(trim($sBlockList)) == 0){
                $aBlockUserId = array();
            } else {
                $aBlockUserId = explode(',', $sBlockList);
            }
            if(strlen(trim($sAllowList)) == 0){
                $aAllowUserId = array();
            } else {
                $aAllowUserId = explode(',', $sAllowList);
            }

            $sTurnonoff = '';
            switch($sTurn){
                case 'turnon':
                    $sTurnonoff = 'onsome';
                    // remove duplicate in block list
                    foreach($aBlockUserId as $key => $val){
                        if(in_array($val, $aAllowUserId)){
                            unset($aBlockUserId[$key]);
                        }
                    }
                    if(count($aAllowUserId) == 0){
                        $sTurnonoff = 'onall';
                    }
                    break;
                case 'turnoff':
                    $sTurnonoff = 'offsome';
                    // remove duplicate in allow list
                    foreach($aAllowUserId as $key => $val){
                        if(in_array($val, $aBlockUserId)){
                            unset($aAllowUserId[$key]);
                        }
                    }
                    if(count($aBlockUserId) == 0){
                        $sTurnonoff = 'onall';
                    }
                    break;
            }

            // remove old settings
            $this->database()->delete(('ynchat_block'), 'to_id = ' . (int) Phpfox::getUserId());
            $this->database()->delete(('ynchat_allow'), 'to_id = ' . (int) Phpfox::getUserId());

            // update
            $this->database()->update(('ynchat_usersetting')
                , array('turnonoff' => $sTurnonoff)
                , 'user_id = ' . (int) Phpfox::getUserId());
            // add new setting
            foreach($aBlockUserId as $val){
                $this->database()->insert(('ynchat_block'), array(
                    'from_id' => $val,
                    'to_id' => (int) Phpfox::getUserId(),
                ));
            }
            foreach($aAllowUserId as $val){
                $this->database()->insert(('ynchat_allow'), array(
                    'from_id' => $val,
                    'to_id' => (int) Phpfox::getUserId(),
                ));
            }
        }

        $this->removeCache();
        return array(
            'result' => true,
            'error_message' => '',
            'sTurnonoff' => $sTurnonoff,
            'error_code' => 0
        );
    }

    public function getSocketConfig(){
        $iPort = (int)Phpfox::getParam('ynchat.web_socket_port');
        $sAction = 'chat';
        $sIpListenServer = '0.0.0.0';
        $iMaxClient = 999999;
        $bCheckOrigin = true;
        $iMaxConnectPerIp = 100;
        $iMaxRequestPerMinute = 2000;

        $sSiteLink = Phpfox::getParam('core.path_file');
        $sApiUrl = $sSiteLink.'module/ynchat/api.php/';

        return array(
                'sServerUrl' => trim(Phpfox::getParam('core.host'), '/'),
                'sSiteLink' => $sSiteLink,
                'sApiUrl' => $sApiUrl,
                'iPort' => $iPort,
                'iStunnelPort' => Phpfox::getParam('ynchat.stunnel_port'),
                'sIpPublic' => Phpfox::getParam('ynchat.ynchat_ip_public'),
                'sAction' => $sAction,
                'sIpListenServer' => $sIpListenServer,
                'iMaxClient' => $iMaxClient,
                'bCheckOrigin' => $bCheckOrigin,
                'iMaxConnectPerIp' => $iMaxConnectPerIp,
                'iMaxRequestPerMinute' => $iMaxRequestPerMinute,
                'bEnableSSL' => 0,
        );
    }

    public function startWebSocketServer(){
       shell_exec('bash ' . PHPFOX_DIR . 'ynchat/runcheck.sh start ' . PHPFOX_DIR . 'ynchat/');
    }

    public function sendMessageByAjax($aData){
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }

        $iUserId = isset($aData['iUserId']) ? $aData['iUserId'] : '';
        $text = isset($aData['text']) ? $aData['text'] : '';
        $title = isset($aData['title']) ? $aData['title'] : '';
        $url = isset($aData['url']) ? $aData['url'] : '';
        $imageUrl = isset($aData['imageUrl']) ? $aData['imageUrl'] : '';
        $iframe = isset($aData['iframe']) ? $aData['iframe'] : '';
        $widthIframe = isset($aData['widthIframe']) ? (int)$aData['widthIframe'] : 0;
        $heightIframe = isset($aData['heightIframe']) ? (int)$aData['heightIframe'] : 0;

        $type = 'link';
        if(strlen($iframe) > 0){
            $type = 'video';
        }
        $dataField = array(
            'type' => $type, 
            'iframe' => $iframe, 
            'widthIframe' => $widthIframe, 
            'heightIframe' => $heightIframe, 
            'url' => $url, 
            'imageUrl' => $imageUrl, 
            'title' => ($title), 
        );
        // process
        $text = $this->checkBan($text, 'word');
        $text = htmlspecialchars($text);
        $text = nl2br($text);
        $text = $this->parseEmoticon($text);

        $message = '';
        $message .= $text;
        $aVals = array(
            'from' => Phpfox::getUserId(),
            'to' => (int) $iUserId,
            'message' => $message,
            'read' => 0,
            'direction' => 0,
            'message_type' => $type,
            'data' => json_encode($dataField),
        );
        $aMessage = $this->addMessage($aVals);

        return array(
            'result' => true,
            'iUserId' => $iUserId,
            'aMessage' => $aMessage,
            'error_message' => '',
            'error_code' => 0
        );
    }

    public function initLangAndConfig($aData){
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }

        $iUserId = Phpfox::getUserId();
        $data = $this->getLangAndConfig();
        $friendList = $this->getFriendsList(Phpfox::getUserId());
        $countOnine = $this->getTotalOnlineFriends();
        return array(
            'result' => true,
            'iUserId' => $iUserId,
            'lang' => $data['lang'],
            'config' => $data['config'],
            'usersettings' => $data['usersettings'],
            'friendList' => $friendList,
            'countOnine' => $countOnine,
            'error_message' => '',
            'error_code' => 0
        );
    }    

    public function alias_mfox_updateAgent($aData){
        if(!Phpfox::getUserId()){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }

        $type = 'mobile';
        
        $this->database()->update(('ynchat_status')
            , array('agent' => $type)
            , 'user_id = ' . Phpfox::getUserId());

        return array(
            'error_code'=>0,
            'message'=>'Update status success',
        );    
    }
    public function updateAgent($aData){
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }

        $type = 'web';
        if(Phpfox::getService('ynchat.helper')->isMobile()){
            $type = 'mobile';
        }        

        $this->database()->update(('ynchat_status')
            , array('agent' => $type)
            , 'user_id = ' . Phpfox::getUserId());

        return array(
            'error_code'=>0,
            'message'=>'Update agent success',
        );
    }

    public function updateUserBoxSetting($aData){
        if(Phpfox::isUser() == false){
            return array(
                'result' => FALSE,
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }

        $iUserId = isset($aData['iUserId']) ? (int)$aData['iUserId'] : 0;
        $sType = isset($aData['sType']) ? $aData['sType'] : '';
        switch ($sType) {
            case 'open':
                $usersettings = $this->getUserSettingsByUserId(Phpfox::getUserId());
                $aData = $usersettings['aData'];
                if(is_array($aData)){
                    $open = isset($aData['open']) ? $aData['open'] : array();
                    if(isset($open[$iUserId]) == false){
                        $open[$iUserId] = '';
                        $aData['open'] = $open;
                        $this->database()->update(('ynchat_usersetting')
                            , array('data' => serialize($aData))
                            , 'user_id = ' . (int) Phpfox::getUserId());
                    }
                }
                break;
            case 'close':
                $usersettings = $this->getUserSettingsByUserId(Phpfox::getUserId());
                $aData = $usersettings['aData'];
                if(is_array($aData)){
                    $open = isset($aData['open']) ? $aData['open'] : array();
                    if(isset($open[$iUserId]) == true){
                        unset($open[$iUserId]);
                        $aData['open'] = $open;
                        $this->database()->update(('ynchat_usersetting')
                            , array('data' => serialize($aData))
                            , 'user_id = ' . (int) Phpfox::getUserId());
                    }
                }
                break;
            case 'removeall':
                $this->database()->update(('ynchat_usersetting')
                    , array('data' => null)
                    , 'user_id = ' . (int) Phpfox::getUserId());
                break;
        }
        return array('result' => true);
    }

    public function addFile($userId, $receiverId, $title, $name, $type, $server_id = 0) {
        $file = array(
            'user_id' => $userId,
            'receiver_id' => $receiverId,
            'creation_date' => $this->getTimeStamp(),
            'title' => $title,
            'file_name' => $name,
            'type' => $type, 
            'server_id' => $server_id, 
        );
        return $this->database()->insert(('ynchat_file'), $file);
    }
    
    public function upload($params) {
        if(Phpfox::isUser() == false){
            return array(
                'error_message' => Phpfox::getPhrase('ynchat.please_login_and_try_again'),
                'error_code' => 1
            );
        }
        $sender = Phpfox::getUserId();
        $receiver = $params['iReceiverId'];
        if (!$sender || !$receiver) {
            return array(
                'error_message' => Phpfox::getPhrase('ynchat.invalid_receiver'),
                'error_code' => 1
            );
        }
        
        $file_name = $params['fileName'];
        $file_data = $params['fileData'];

        $time = $this->getTimeStamp();
        $sHTML5TempFile = PHPFOX_DIR_CACHE . 'file_' . md5(PHPFOX_DIR_CACHE . $file_name . uniqid());
        list($type, $file_data) = explode(';', $file_data);
        list(, $file_data)      = explode(',', $file_data);
        $file_data = str_replace(' ', '+', $file_data);
        $data = base64_decode($file_data);
        file_put_contents(
            $sHTML5TempFile,
            $data
        );
        $type = get_mime_of_file($file_name);
        $_FILES['object'] = array(
            'name' => $file_name,
            'type' => $type,
            'tmp_name' => $sHTML5TempFile,
            'error' => 0,
            'size' => filesize($sHTML5TempFile), 
        );

        define('PHPFOX_APP_USER_ID', $sender);
        $oFile = Phpfox::getLib('file');
        $oImage = Phpfox::getLib('image');
        $aSupported = array('jpg', 'gif', 'png', 'zip', 'mp3', 'doc', 'docx', 'ppt', 'pptx', 'pps', 'xls', 'xlsx', 'pdf', 'ps', 'odt', 'odp', 'sxw', 'sxi', 'txt', 'rtf', 'mpg' , 'mpeg', 'wmv' , 'avi' , 'mov' , 'flv');
        $aObject = $oFile->load('object', $aSupported, ( (10 * 1000) / 1024));
        if (Phpfox_Error::isPassed() == false){
            $errors = Phpfox_Error::get();
            return array(
                'error_message' => isset($errors[0]) ? $errors[0] : '',
                'error_code' => 1,
            );            
        }
        if ($aObject === false)
        {
            return array(
                'error_message' => 'Please select file to upload',
                'error_code' => 1,
            );            
        }

        $sFileName = $oFile->upload('object', Phpfox::getParam('core.dir_file') . 'ynchat/', PHPFOX_TIME);
        $type = get_mime_of_file($file_name);
        $sFilepath = Phpfox::getParam('core.dir_file') . 'ynchat/' . sprintf($sFileName, '');
        // fix rotate bug
        if (preg_match('/image*/', $type)) {
            $orgExt = str_replace('image/', '', $type);
            if (($orgExt == 'jpg' || $orgExt == 'jpeg') && function_exists('exif_read_data'))
            {
                $exif = exif_read_data($sFilepath);
                if(!empty($exif['Orientation'])){
                    switch($exif['Orientation'])
                    {
                        case 1:
                        case 2:
                            break;
                        case 3:
                        case 4:
                            // 90 degrees
                            $oImage->rotate($sFilepath, 'right');
                            // 180 degrees
                            $oImage->rotate($sFilepath, 'right');
                            break;
                        case 5:
                        case 6:
                            // 90 degrees right
                            $oImage->rotate($sFilepath, 'right');
                            break;
                        case 7:
                        case 8:
                            // 90 degrees left
                            $oImage->rotate($sFilepath, 'left');
                            break;
                        default:
                            break;
                    }
                }
            }

            // resize
            $iSize = 720;
            $oImage->createThumbnail($sFilepath, $sFilepath, $iSize, $iSize);           
        }

        $id = $this->addFile($sender, $receiver, $file_name, $sFileName, $type, Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'));
        return array(
            'error_message' => '',
            'error_code' => 0,
            'id' => $id, 
            'name' => $file_name, 
            'type' => $type
        );
    }
    public function alias_mfox_download($aData = null) {
        $id = $aData['id'];

        $aRows = $this->database()->select('yne.*')
            ->from(('ynchat_file'), 'yne')
            ->where('file_id='.$id)
            ->execute('getSlaveRows');
            
        if (!count($aRows)) {
            return array(
                'error_code'=>1,
                'error_debug'=> "file id [{$id}]",
                'error_message'=>'Could not find file entry.',
            );
        }
        $file = $aRows[0];
       
        $file_link = $this->getFilePath($file['file_name'], $file['server_id']);
        header('Location: '.$file_link);
    }
    
    public function download() {
        $id = $_GET['id'];
        if (!$id) {
            return false;
        }
        
        $aRows = $this->database()->select('yne.*')
            ->from(('ynchat_file'), 'yne')
            ->where('file_id='.$id)
            ->execute('getSlaveRows');
            
        if (!count($aRows)) {
            return false;
        }
        $file = $aRows[0];
        $viewer = Phpfox::getUserId();
        if ($viewer != $file['user_id'] && $viewer != $file['receiver_id']) {
            return false;
        }

        $file_link = $this->getFilePath($file['file_name'], $file['server_id']);
        header('Location: '.$file_link);
    }

    public function getFilePath($sFile, $iServerId = null)
    {
        $sFile = Phpfox::getParam('core.url_file') . 'ynchat/' . sprintf($sFile, '');   
        
        if (Phpfox::getParam('core.allow_cdn') && !empty($iServerId))
        {
            $sTempSong = Phpfox::getLib('cdn')->getUrl($sFile, $iServerId);
            if (!empty($sTempSong))
            {
                $sFile = $sTempSong;
            }
        }
        
        return $sFile;
    }    

    public function isMobile(){
        return Phpfox::isMobile();
    }    

    public function log_message($path, $level = 'error', $msg)
    {
        $_date_fmt = 'Y-m-d H:i:s';
        $filepath = $path . 'log-' . date('Y-m-d') . '.php';

        $message = '';

        if (!file_exists($filepath))
        {
                $message .= "<" . "?php  ; ?" . ">\n\n";
        }

        if (!$fp = @fopen($filepath, 'ab'))
        {
                return FALSE;
        }

        $message .= $level . ' ' . (($level == 'INFO') ? ' -' : '-') . ' ' . date($_date_fmt) . ' --> ' . $msg . "\n";

        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);

        @chmod($filepath, 0666);
        return TRUE;
    }

    public function writeCheckAlive($path)
    {
        $filepath = $path . 'checkalive' . '.log';
        $message = '';
        if (!file_exists($filepath))
        {
            $message .= "";
        }

        if (!$fp = @fopen($filepath, 'w'))
        {
            return FALSE;
        }

        $message .= $this->getTimeStamp();

        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);

        @chmod($filepath, 0666);
        return TRUE;
    }    

    public function readCheckAlive($path)
    {
        $filepath = $path . 'checkalive' . '.log';
        if (!file_exists($filepath))
        {
            return 0;
        }
        $number = trim(file_get_contents($filepath));

        return (int)$number;
    }

    public function reconnectDatabase(){ 
        // check ONLY action from server socket 
        if(defined('YNCHAT_DIR')){
            $newCheck = $this->getTimeStamp();
            $oldCheck = $this->readCheckAlive(PHPFOX_DIR . 'ynchat/log/');
            if(($newCheck - $oldCheck) > CHECK_ALIVE_CONNECTION_DB_INTERVAL){
                $this->database()->reconnect();
                if(defined('YNCHAT_DEBUG') && YNCHAT_DEBUG){
                    $this->log_message(PHPFOX_DIR . 'ynchat/log/', "debug", 'reconnectDatabase --  -- ' . print_r('end', true));
                }
                $this->writeCheckAlive(PHPFOX_DIR . 'ynchat/log/');
            }
        }
    }

    public function getUser($mName = null, $bUseId = true){
        $aRow = $this->database()->select('u.*')
            ->from(Phpfox::getT('user'), 'u')
            ->where(($bUseId ? "u.user_id = " . (int) $mName . "" : "u.user_name = '" . $this->database()->escape($mName) . "'"))
            ->execute('getSlaveRow');
        return $aRow;
    }

}
?>