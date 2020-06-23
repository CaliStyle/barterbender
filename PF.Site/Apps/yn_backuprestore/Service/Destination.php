<?php
/**
 * User: huydnt
 * Date: 04/01/2017
 * Time: 17:49
 */

namespace Apps\yn_backuprestore\Service;


use Phpfox;
use Phpfox_Service;

class Destination extends Phpfox_Service
{
    protected $_destinationIdCol = 'destination_id';
    protected $_titleCol = 'title';
    protected $_typeIdCol = 'type_id';
    protected $_paramsCol = 'params';

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynbackuprestore_destinations');
    }

    /**
     * Get all destinations
     * @return array|int|string
     */
    public function getAllDestinations()
    {
        $aDestinations = $this->database()->select('dest.*, type.title as destination_type')
            ->from($this->_sTable, 'dest')
            ->leftJoin(Phpfox::getT('ynbackuprestore_destination_types'), 'type', "type.type_id = dest.type_id")
            ->execute('getrows');
        $aDestinations = $this->getDestinationLocation($aDestinations);

        return $aDestinations;
    }

    /**
     * Get Destination Location
     * @param $aDests
     * @return mixed
     */
    public function getDestinationLocation($aDests)
    {
        foreach ($aDests as &$aDestination) {
            $aParams = json_decode($aDestination['params'], true);
            switch ($aDestination['type_id']) {
                case 2:
                    $aDestination['destination_location'] = $aParams['email_address'];
                    break;
                case 3:
                    $aDestination['destination_location'] = $aParams['ftp_remote'];
                    break;
                case 4:
                    $aDestination['destination_location'] = $aParams['sftp_directory'];
                    break;
                case 5:
                    $aDestination['destination_location'] = $aParams['mysql_dbname'];
                    break;
                case 6:
                    $aDestination['destination_location'] = $aParams['s3_bucket'];
                    break;
                case 7:
                    $aDestination['destination_location'] = $aParams['dropbox_store'];
                    break;
                case 8:
                    $aDestination['destination_location'] = $aParams['onedrive_directory'];
                    break;
                case 9:
                    $aDestination['destination_location'] = $aParams['google_folder'];
                    break;
                default:
                    break;
            }
        }

        return $aDests;
    }

    /**
     * Add new destination
     * @param $aVal
     * @return int
     */
    public function addDestination($aVal)
    {
        return $this->database()->insert($this->_sTable, $this->prepareValues($aVal));
    }

    /**
     * Prepare values for add and update
     * @param $aVal
     * @return array
     */
    protected function prepareValues($aVal)
    {
        $sTitle = $aVal['title'];
        $sTypeId = $aVal['type_id'];
        unset($aVal['title']);
        unset($aVal['type_id']);
        $sParams = json_encode($aVal);

        return [
            $this->_titleCol  => $sTitle,
            $this->_typeIdCol => $sTypeId,
            $this->_paramsCol => $sParams
        ];
    }

    /**
     * Update destination
     * @param $iId
     * @param $aVal
     * @return bool|resource
     */
    public function updateDestination($iId, $aVal)
    {
        $aDest = $this->getDestinationById($iId);
        $aParams = json_decode($aDest['params'], true);
        if (array_key_exists('access_token', $aParams)) {
            $aVal['access_token'] = $aParams['access_token'];
        }
        return $this->database()->update($this->_sTable, $this->prepareValues($aVal),
            "$this->_destinationIdCol = $iId");
    }

    /**
     * Get destination by id
     * @param $iId
     * @return array|int|string
     */
    public function getDestinationById($iId)
    {
        return $this->database()->select('*')->from($this->_sTable)->where("$this->_destinationIdCol = $iId")->execute('getslaverow');
    }

    /**
     * Update destination params
     * @param $iId
     * @param $aParams
     * @return bool|resource
     */
    public function updateParams($iId, $aParams)
    {
        return $this->database()->update($this->_sTable, ['params' => json_encode($aParams)], "destination_id = $iId");
    }

    /**
     * Delete destinations
     * @param $aIds
     * @return bool
     */
    public function deleteDestinations($aIds)
    {
        foreach ($aIds as $iId) {
            if (!$this->deleteDestination($iId)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Delete destination
     * @param $iId
     * @return bool
     */
    public function deleteDestination($iId)
    {
        return $this->database()->delete($this->_sTable, "$this->_destinationIdCol = $iId");
    }

    /**
     * @param $iId
     * @return int
     */
    public function isTypeGoogleDrive($iId)
    {
        return count($this->getDestinations(['destination_id' => $iId]));
    }

    /**
     * Get destination by conditions
     * @param array $aVal
     * @param int $iPage
     * @param null $iLimit
     * @return array|int|string
     */
    public function getDestinations($aVal = array(), $iPage = 1, $iLimit = null)
    {
        $select = $this->database()->select('dest.*, type.title as destination_type')
            ->from($this->_sTable, 'dest')
            ->leftJoin(Phpfox::getT('ynbackuprestore_destination_types'), 'type', "type.type_id = dest.type_id");
        $where = array();
        if (isset($aVal['destination_id']) && $aVal['destination_id']) {
            $where[] = "dest.destination_id = $aVal[destination_id]";
        }
        if (isset($aVal['title']) && $aVal['title']) {
            $where[] = "dest.$this->_titleCol LIKE '%$aVal[title]%'";
        }
        if (isset($aVal['type_id']) && $aVal['type_id']) {
            $where[] = "dest.$this->_typeIdCol = $aVal[type_id]";
        }

        if (count($where)) {
            $select->where(implode($where, " AND "));
        }

        if ($iLimit) {
            $select->limit($iPage, $iLimit);
        }
        $aDestinations = $select->execute('getslaverows');
        $aDestinations = $this->getDestinationLocation($aDestinations);

        return $aDestinations;
    }

    public function getQuantity($aVal)
    {
        $select = $this->database()->select('count(*)')
            ->from($this->_sTable, 'dest');
        $where = array();
        if (isset($aVal['destination_id']) && $aVal['destination_id']) {
            $where[] = "dest.destination_id = $aVal[destination_id]";
        }
        if (isset($aVal['title']) && $aVal['title']) {
            $where[] = "dest.$this->_titleCol LIKE '%$aVal[title]%'";
        }
        if (isset($aVal['type_id']) && $aVal['type_id']) {
            $where[] = "dest.$this->_typeIdCol = $aVal[type_id]";
        }

        if (count($where)) {
            $select->where(implode($where, " AND "));
        }

        return $select->executeField();
    }

    /**
     * Get destinations id by backup id
     * @param $iId
     * @return array
     */
    public function getDestinationsByBackupId($iId)
    {
        return $this->database()->select('destination_id')->from(":ynbackuprestore_destination_maps")->where("parent_id = $iId AND parent_type = 'backup'")->executeRows();
    }

    /**
     * Get destinations id by schedule id
     * @param $iId
     * @return array
     */
    public function getDestinationIdsByScheduleId($iId)
    {
        $destinationIds = $this->database()->select('destination_id')->from(":ynbackuprestore_destination_maps")->where("parent_id = $iId AND parent_type = 'schedule'")->executeRows();
        foreach ($destinationIds as &$item) {
            $item = $item['destination_id'];
        }

        return $destinationIds;
    }

    public function getDestinationsByScheduleId($iId)
    {
        $destinations = $this->database()->select('*')->from(":ynbackuprestore_destination_maps", 'maps')
            ->leftJoin($this->_sTable, 'dest', 'maps.destination_id=dest.destination_id')
            ->where("parent_id=$iId AND parent_type='schedule'")->executeRows();
        return $this->getDestinationLocation($destinations);
    }
}