<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Entry_Entry extends Phpfox_service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('contest_entry');
    }

    private $_aEntries = array();

    private $_aEntriesForCheckingPermission = array();

    private $_aSimpleInfoEntries = array();

    public function getEntriesOfContestById($iContestId)
    {
        $aRows = $this->database()->select('en.entry_id,en.title')
        ->from($this->_sTable, 'en')
        ->where('en.contest_id = '.$iContestId)
        ->execute('getRows');
        
        return $aRows;
    }

    public function getTopEntries($sType = 'recent', $iLimit = 6)
    {
        $where = '1 = 1 AND ct.privacy = 0 AND en.status = 1 and ct.is_deleted = 0';
        $order = 'en.time_stamp desc';

        switch (trim($sType))
        {
            case 'most-vote':
                $order = 'en.total_vote desc';
                break;
            case 'recent':
                $order = 'en.time_stamp desc';
                break;
        }

        $iCnt = $this->database()->select('count(*)')
        ->from(Phpfox::getT('contest_entry'), 'en')
        ->join(Phpfox::getT('contest'), 'ct', 'ct.contest_id = en.contest_id')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = en.user_id')
        ->where($where)
        ->execute('getField');

        $aRows = $this->database()->select('*,en.image_path as image_path_parse,en.entry_id as type_entry,en.total_like as total_like_entry, en.total_view as total_view_entry, en.status as status_entry, en.server_id as server_id,u.server_id as user_server_id')
        ->from(Phpfox::getT('contest_entry'), 'en')
        ->join(Phpfox::getT('contest'), 'ct', 'ct.contest_id = en.contest_id')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = en.user_id')
        ->where($where)
        ->order($order)
        ->limit($iLimit)
        ->execute('getRows');

        $aRows = Phpfox::getService('contest.contest')->implementsContestFields($aRows);
        return array($iCnt, $aRows);
    }

    public function getTotalPendings()
    {
        $where = '1 = 1 AND ct.privacy = 0 AND en.status = 0 and ct.is_deleted = 0';
        $order = 'en.time_stamp desc';

        $iCnt = $this->database()->select('count(*)')
        ->from(Phpfox::getT('contest_entry'), 'en')
        ->join(Phpfox::getT('contest'), 'ct', 'ct.contest_id = en.contest_id')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = en.user_id')
        ->where($where)
        ->execute('getField');

        return $iCnt;
    }


    public function getEntries()
    {
        return $this->_aEntries;
    }

    public function getWinningEntriesOfContest($iContestId)
    {
        return $this->_aEntries;
    }

    public function getItemService($iItemType)
    {
        $sItemName = Phpfox::getService('contest.constant')->getContestTypeNameByTypeId($iItemType);
        return Phpfox::getService('contest.entry.item.'.$sItemName);
    }

    public function getDataOfAddEntryTemplate($iContestId,$iSourceId = 1)
    {
        $aTemplateData = array();
        $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);

        $oItemService = $this->getItemService($aContest['type']);

        $aTemplateData['sAddNewItemLink'] = $oItemService->getAddNewItemLink($aContest['contest_id'], $iSourceId);


        $iPage = Phpfox::getLib('request')->get('page');
        if (!$iPage)
        {
            $iPage = 0;
        }

        $iPageSize = Phpfox::getService('contest.constant')->getNumberOfItemsPerPageInAddEntryForm();

        list($iTotalItems, $aItems) = $oItemService->getItemsOfCurrentUser($iPageSize, $iPage,$iSourceId);

        $aTemplateData['aItems'] = Phpfox::getService('contest.adapter')->adaptDataOfItemInAddEntryList($aContest['type'], $aItems,$iSourceId);

        $aTemplateData['iTotalItems'] = $iTotalItems;
        $aTemplateData['iPage'] = $iPage;
        $aTemplateData['iPageSize'] = $iPageSize;

        $aTemplateData['iItemType'] = $aContest['type'];

        $aTemplateData['iContestId'] = $aContest['contest_id'];

        return $aTemplateData;
    }

    public function getTemplateViewPath($iItemType)
    {
        $oItemService = $this->getItemService($iItemType);

        $sPath = $oItemService->getTemplateViewPath();

        return $sPath;
    }

    public function getItemDataFromFox($iItemType, $iItemId)
    {
        $oItemService = $this->getItemService($iItemType);
        if($iItemType == 3 && Phpfox::isModule('ultimatevideo'))
        {
            $aItem = $oItemService->getItemFromFoxUltVideo($iItemId);
        }
        else{
            $aItem = $oItemService->getItemFromFox($iItemId);
        }
        return $aItem;
    }

    public function getDataFromItemToInsert($iItemType, $iItemId,$iSourceId = 1)
    {
        $oItemService = $this->getItemService($iItemType);

        $aItem = $oItemService->getDataToInsertIntoEntry($iItemId, $iSourceId);

        return $aItem;
    }

    public function getContestEntryById($iEntryId)
    {
        if (isset($this->_aEntries[$iEntryId]))
        {
            return $this->_aEntries[$iEntryId];
        }

        if (Phpfox::isModule('like'))
        {
            $this->database()->select('l.like_id AS is_liked, ')
            ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'contest_entry\' AND l.item_id = en.entry_id AND l.user_id = '.Phpfox::getUserId());
        }

        if (Phpfox::isModule('track')) {
            $this->database()->select("contest_track.item_id AS is_viewed, ")
                ->leftJoin(Phpfox::getT('track'), 'contest_track', 'contest_track.item_id = en.entry_id AND contest_track.user_id = ' . Phpfox::getUserBy('user_id') . ' AND contest_track.type_id=\'ync_contest_entry\'');
        }

        if (Phpfox::isModule('friend'))
        {
            $this->database()->select('f.friend_id AS is_friend, ')
            ->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = en.user_id AND f.friend_user_id = ".Phpfox::getUserId());
        }

        $sContestField = 'ct.privacy, ct.privacy_comment, ct.user_id as contest_user_id, ct.contest_status, ct.contest_name, ct.number_entry_max, ct.number_winning_entry_max';
        
        $aRow = $this->database()->select(Phpfox::getUserField().', '.$sContestField.', en.*, en.status as status_entry, en.server_id as server_id, w.rank, w.award')
        ->from($this->_sTable, 'en')
        ->join(Phpfox::getT('contest'), 'ct', 'ct.contest_id=en.contest_id')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id=en.user_id')
        ->leftJoin(Phpfox::getT('contest_winner'), 'w', 'w.entry_id = en.entry_id')
        ->where('en.entry_id = '.$iEntryId)
        ->execute('getSlaveRow');

        if ($aRow)
        {
            $aRow = Phpfox::getService('contest.entry')->retrieveEntryPermission($aRow);
            $aRow['is_winning'] = isset($aRow['rank']) ? true : false;
        }
        
        $this->_aEntries[$iEntryId] = $aRow;
        
        return $aRow;
    }

    public function getContestEntryBesideId($iEntryId, $iContestId, $type)
    {
        if ($type == "previous")
        {
            $where = 'en.entry_id < '.$iEntryId;
            $order = 'en.entry_id DESC';
        }
        else
        {
            $where = 'en.entry_id > '.$iEntryId;
            $order = 'en.entry_id ASC';
        }
        
        $aRow = $this->database()->select('en.entry_id, en.title')
        ->from($this->_sTable, 'en')
        ->where($where.' and en.contest_id = '.$iContestId.' and en.status=1')
        ->order($order)
        ->limit(1)
        ->execute('getSlaveRow');
        
        return $aRow;
    }

    public function getDataFromFoxComplyWithContestEntry($iItemType, $iItemId,$iSourceId = 1)
    {
        $oItemService = $this->getItemService($iItemType);
        $aItem = $oItemService->getDataFromFoxAdaptedWithContestEntryData($iItemId,$iSourceId);

        return $aItem;
    }

    public function getListVotesByEntryId($iEntryId)
    {
        $aRows = $this->database()->select('*')
        ->from(Phpfox::getT('contest_entry_vote'), 'env')
        ->join(PHpfox::getT('user'), 'u', 'u.user_id = env.user_id')
        ->where('env.entry_id = '.$iEntryId)
        ->execute('getRows');
        
        return $aRows;
    }

    public function getTotalEntriesByContestId($iContestId)
    {
        $iCnt = $this->database()->select('count(*)')
        ->from($this->_sTable, 'en')
        ->where('en.contest_id = '.$iContestId.' and en.status=1')
        ->execute('getField');

        return $iCnt;
    }

    public function get($iContestId, $iPage = 0, $iLimit)
    {
        
        $where = 'en.contest_id = '.$iContestId;
        $iCnt = $this->database()->select('count(*)')
        ->from(Phpfox::getT('contest_winner'), 'ctw')
        ->join(Phpfox::getT('contest_entry'), 'en', 'en.entry_id = ctw.entry_id')
        ->join(Phpfox::getT('contest'), 'ct', 'ct.contest_id = en.contest_id')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = ctw.user_id')
        ->where($where)
        ->execute('getSlaveField');

        $aRows = $this->database()->select(Phpfox::getUserField().', ct.contest_name, ctw.*, en.*, en.server_id as server_id, en.status as status_entry')
        ->from(Phpfox::getT('contest_winner'), 'ctw')
        ->join(Phpfox::getT('contest_entry'), 'en', 'en.entry_id = ctw.entry_id')
        ->join(Phpfox::getT('contest'), 'ct', 'ct.contest_id = en.contest_id')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = ctw.user_id')
        ->where($where)
        ->limit($iPage, $iLimit)
        ->order('ctw.rank ASC')
        ->execute('getRows');
        
        return array($iCnt, $aRows);
    }

    public function CheckExistEntryWinning($entry_id)
    {
        $iCount = $this->database()->select('count(*)')
        ->from(Phpfox::getT('contest_winner'))
        ->where('entry_id = '.$entry_id)
        ->execute('getField');
        
        if ($iCount > 0)
            return 1;
        
        return 0;
    }

    public function getNumberOfSumittedEntryInAContestOfUser($iContestId, $iUserId)
    {
        $iCount = $this->database()->select('count(*)')
        ->from(Phpfox::getT('contest_entry'))
        ->where('contest_id = '.$iContestId.' AND user_id = '.$iUserId)
        ->execute('getField');

        return $iCount;
    }

    public function getTotalWinningEntries($contest_id, $aIdEntry)
    {
        $where = 'en.contest_id = '.$contest_id;
        if (strlen(trim($aIdEntry)) > 0)
        {
            $where .= " And en.entry_id not in (".$aIdEntry.')';
        }
        
        $iCnt = $this->database()->select('count(*)')
        ->from(Phpfox::getT('contest_winner'), 'w')
        ->join(Phpfox::getT('contest_entry'), 'en', 'en.entry_id = w.entry_id')
        ->where($where)
        ->execute('getField');
        
        return $iCnt;
    }

    public function retrieveContestPermissions($aContest)
    {
        return $aContest;
    }

    public function retrieveEntryPermission($aEntry)
    {
        $aEntry['can_view_entry_detail'] = Phpfox::getService('contest.permission')->canViewEntryDetail($aEntry['entry_id'], Phpfox::getUserId());

        $aEntry['can_vote_entry'] = Phpfox::getService('contest.permission')->canVoteEntry($aEntry['entry_id'], Phpfox::getUserId());

        $aEntry['can_approve_entry'] = Phpfox::getService('contest.permission')->canApproveEntry($aEntry['entry_id'], Phpfox::getUserId());

        $aEntry['can_deny_entry'] = Phpfox::getService('contest.permission')->canDenyEntry($aEntry['entry_id'], Phpfox::getUserId());

        $aEntry['can_set_winning_entry'] = Phpfox::getService('contest.permission')->canSetWinningEntry($aEntry['entry_id'], Phpfox::getUserId());

        // $aEntry['can_delete_entry'] = Phpfox::getService('contest.permission')->canDeleteEntry($aEntry['entry_id'], Phpfox::getUserId());

        $aEntry['can_remove_entry_from_winning'] = Phpfox::getService('contest.permission')->canRemoveEntryFromWinningList($aEntry['entry_id'], Phpfox::getUserId());

        $aEntry['have_action_on_entry'] = true;

        if (!$aEntry['can_approve_entry'] && !$aEntry['can_deny_entry'] && !$aEntry['can_set_winning_entry'])
        {
            $aEntry['have_action_on_entry'] = false;
        }

        return $aEntry;
    }

    public function getEntryForCheckingPermission($iEntryId)
    {
        if (isset($this->_aEntriesForCheckingPermission[$iEntryId]))
        {
            return $this->_aEntriesForCheckingPermission[$iEntryId];
        }

        $aRow = $this->database()->select('en.*,u.*,en.status as status_entry,ct.privacy,ct.privacy_comment,ct.user_id as contest_user_id,ct.contest_status,ct.contest_name,ct.number_entry_max,ct.number_winning_entry_max,en.server_id as server_id')
        ->from($this->_sTable, 'en')
        ->join(Phpfox::getT('contest'), 'ct', 'ct.contest_id=en.contest_id')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id=en.user_id')
        ->where('en.entry_id = '.$iEntryId)
        ->execute('getSlaveRow');

        $this->_aEntriesForCheckingPermission[$iEntryId] = $aRow;

        return $aRow;
    }

    public function getSimpleEntryInfor($iEntryId)
    {
        if (isset($this->_aSimpleInfoEntries[$iEntryId]))
        {
            return $this->_aSimpleInfoEntries[$iEntryId];
        }

        $aRow = $this->database()->select('en.*')
        ->from($this->_sTable, 'en')
        ->where('en.entry_id = '.$iEntryId)
        ->execute('getSlaveRow');

        $this->_aSimpleInfoEntries[$iEntryId] = $aRow;

        return $aRow;
    }
    
    public function getTopByContestId($iContestId, $sType = 'vote', $iLimit = 4)
    {
        $sCond = 'e.contest_id = '.(int)$iContestId.' AND e.status = 1';
        
        switch ($sType)
        {
            case 'like':
                $sOrder = 'e.total_like DESC';
                break;
            case 'comment':
                $sOrder = 'e.total_comment DESC';
                break;
            case 'view':
                $sOrder = 'e.total_view DESC';
                break;
            default:
                $sOrder = 'e.total_vote DESC';
                $sCond .= ' AND e.total_vote > 0';
        }
        
        $aRows = $this->database()->select('e.*, e.server_id as image_server_id, '.Phpfox::getUserField())
        ->from($this->_sTable, 'e')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
        ->where($sCond)
        ->order($sOrder)
        ->limit($iLimit)
        ->execute('getSlaveRows');
        
        return $aRows;
    }
    
    public function getRecentByContestType($sType, $iLimit = 8)
    {
        $iType = Phpfox::getService('contest.constant')->getContestTypeIdByTypeName($sType);

        $aRows = $this->database()->select('e.*, e.server_id as image_server_id, e.status as status_entry, c.contest_name, '.Phpfox::getUserField())
        ->from($this->_sTable, 'e')
        ->join(Phpfox::getT('contest'), 'c', 'c.contest_id = e.contest_id and c.is_active = 1 AND c.contest_status = 4')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
        ->where('c.type = '.$iType.' AND c.privacy = 0 AND c.is_deleted = 0 AND e.status = 1')
        ->order('e.time_stamp DESC')
        ->limit($iLimit)
        ->execute('getSlaveRows');
        
        return $aRows;
    }
    
    public function getMostVotedByContestType($sType, $iLimit = 8)
    {
        $iType = Phpfox::getService('contest.constant')->getContestTypeIdByTypeName($sType);
        
        $aRows = $this->database()->select('e.*, e.server_id as image_server_id, e.status as status_entry, c.contest_name, '.Phpfox::getUserField())
        ->from($this->_sTable, 'e')
        ->join(Phpfox::getT('contest'), 'c', 'c.contest_id = e.contest_id AND c.is_active = 1 AND c.contest_status = 4')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
        ->where('c.type = '.$iType.' AND c.privacy = 0 AND c.is_deleted = 0 AND e.status = 1')
        ->order('e.total_vote DESC')
        ->limit($iLimit)
        ->execute('getSlaveRows');
        
        return $aRows;
    }
    
    public function getDefaultSearchType($sView = 'my_entries')
    {
        $sDefaultType = 'blog';
        $aType = array('blog', 'music', 'photo', 'video');
        
        foreach ($aType as $sType)
        {
            $iCnt = $this->countForSearch($sType, $sView);
            if ($iCnt > 0)
            {
                $sDefaultType = $sType;
                break;
            }
        }
        
        return $sDefaultType;
    }
    
    public function countForSearch($sType, $sView)
    {
        $iType = Phpfox::getService('contest.constant')->getContestTypeIdByTypeName($sType);
        $sCond = 'c.is_deleted = 0 AND c.type = '.$iType;
        switch ($sView)
        {
            case 'my_entries':
                $sCond .= ' AND e.user_id = '.Phpfox::getUserId();
                break;
            case 'pending_entries':
                $sCond .= ' AND e.status = 0';
                if(!Phpfox::isAdmin())
                {
                    $sCond .= ' AND c.user_id = '.Phpfox::getUserId();
                }
                break;
        }
        
        $iCnt = $this->database()->select('COUNT(*)')
        ->from($this->_sTable, 'e')
        ->join(Phpfox::getT('contest'), 'c', 'c.contest_id = e.contest_id')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
        ->where($sCond)
        ->execute('getSlaveField');
        
        return $iCnt;
    }
}
