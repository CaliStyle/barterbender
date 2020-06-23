<?php
/**
 * @author  phpFox LLC
 * @license phpfox.com
 */

namespace Apps\Core_MobileApi\Service\Helper;

use Phpfox;
use Phpfox_Database;


class SearchHelper extends \Phpfox_Service
{
    /**
     * SQL conditions.
     *
     * @var array
     */
    private $_aConds = [];

    /**
     * Total amount of items this search returned
     *
     * @var int
     */
    private $_iTotalCount = 0;

    /**
     * Custom search date
     *
     * @var bool
     */
    private $_bIsCustomSearchDate = false;

    /**
     * Check to see if the form is being reset
     *
     * @var bool
     */
    private $_bIsReset = false;

    /**
     * SQL order by.
     *
     * @var array
     */
    private $_sSort = '';

    /**
     * Limit number of Item
     * @var int
     */
    private $_iLimit = 10;

    /**
     * Pagination param
     * @var int
     */
    private $_iPage = 1;

    /**
     * Check if a search has been ignored items belong to blocked users.
     *
     * @var bool
     */
    private $_bIsIgnoredBlocked = false;

    /**
     * search_tool when
     *
     * @var array
     */
    private $_aSearchTool = [];
    private $_aParams = [];
    private $_aSearchTools = [];
    private $_aConditions = [];

    /**
     * @return $this
     */
    public static function instance()
    {
        return Phpfox::getService('mobile.helper.search');
    }

    /**
     * Check if we submitted the search form.
     *
     * @return bool TRUE if form submitted, FALSE if not.
     */
    public function isSearch()
    {
        if ($this->_request()->get('search')) {
            return true;
        }
        return false;
    }

    /**
     * Set an SQL condition.
     *
     * @param string $sValue
     */
    public function setCondition($sValue)
    {
        $this->_aConds[] = $sValue;
    }

    public function clearConditions()
    {
        $this->_aConds = [];
    }

    /**
     * Get all SQL conditions.
     *
     * @return array
     */
    public function getConditions()
    {
        static $aConds = null;

        if ($this->_bIsReset) {
            $aConds = null;
            $this->_bIsReset = false;
        }

        if ($aConds !== null) {
            return $aConds;
        }

        if (!empty($this->_aSearchTool) && ($this->_request()->get('when') || $this->_bIsCustomSearchDate)) {
            $iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));

            $sWhenField = (isset($this->_aSearchTool['when_field']) ? $this->_aSearchTool['when_field'] : 'time_stamp');
            $sSwitch = ($this->_request()->get('when') ? $this->_request()->get('when') : $this->_bIsCustomSearchDate);

            switch ($sSwitch) {
                case 'today':
                    $iEndDay = Phpfox::getLib('date')->mktime(23, 59, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));

                    $this->_aConds[] = ' AND (' . $this->_aSearchTool['table_alias'] . '.' . $sWhenField . ' >= \'' . Phpfox::getLib('date')->convertToGmt($iTimeDisplay) . '\' AND '
                        . $this->_aSearchTool['table_alias'] . '.' . $sWhenField . ' < \'' . Phpfox::getLib('date')->convertToGmt($iEndDay) . '\')';
                    break;
                case 'this-week':
                    $this->_aConds[] = ' AND ' . $this->_aSearchTool['table_alias'] . '.' . $sWhenField . ' >= \'' . Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekStart())
                        . '\'';
                    $this->_aConds[] = ' AND ' . $this->_aSearchTool['table_alias'] . '.' . $sWhenField . ' <= \'' . Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekEnd()) . '\'';
                    break;
                case 'this-month':
                    $this->_aConds[] = ' AND ' . $this->_aSearchTool['table_alias'] . '.' . $sWhenField . ' >= \'' . Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getThisMonth())
                        . '\'';
                    $iLastDayMonth = Phpfox::getLib('date')->mktime(0, 0, 0, date('n'), Phpfox::getLib('date')->lastDayOfMonth(date('n')), date('Y'));
                    $this->_aConds[] = ' AND ' . $this->_aSearchTool['table_alias'] . '.' . $sWhenField . ' <= \'' . Phpfox::getLib('date')->convertToGmt($iLastDayMonth) . '\'';
                    break;
                case 'upcoming':
                    $this->_aConds[] = ' AND ' . $this->_aSearchTool['table_alias'] . '.' . $sWhenField . ' >= \'' . Phpfox::getLib('date')->convertToGmt($iTimeDisplay) . '\'';
                    break;
                default:

                    break;
            }
        }

        if (!count($this->_aConds)) {
            return [];
        }

        $oDb = Phpfox_Database::instance();
        $aConds = [];
        foreach ($this->_aConds as $mKey => $mValue) {
            $aConds[] = (is_numeric($mKey) ? $mValue : str_replace('[VALUE]', Phpfox::getLib('parse.input')->clean($oDb->escape($mValue)), $mKey));
        }

        return $aConds;
    }

    /**
     * Set the total number of items this search returned.
     *
     * @param int $iTotalCount
     */
    public function setCount($iTotalCount)
    {
        $this->_iTotalCount = $iTotalCount;
    }

    /**
     * Get the total of items this search returned.
     *
     * @see self::setCount()
     * @return int
     */
    public function getCount()
    {
        return $this->_iTotalCount;
    }

    /**
     * Reset the search
     *
     */
    public function reset()
    {
        $this->_aConditions = [];
        $this->_aParams = [];
        $this->_aSearchTools = [];
        $this->_aConds = [];
        $this->_bIsReset = true;
    }

    public function getLimit()
    {
        return $this->_iLimit;
    }

    public function setLimit($limit)
    {
        $this->_iLimit = $limit;
        return $this;
    }

    public function setPage($page)
    {
        $this->_iPage = $page;
        return $this;
    }

    public function setSearchTool($aTool = [])
    {
        $this->_aSearchTool = $aTool;
        return $this;
    }

    public function setSort($sSort)
    {
        $this->_sSort = $sSort;
        return $this;
    }

    public function getSort()
    {
        return $this->_sSort;
    }

    public function getPage()
    {
        if ($this->_iPage !== 1) {
            return $this->_iPage;
        }
        return $this->_request()->getInt('page', 1);
    }

    public function getDisplay()
    {
        return $this->getLimit();
    }

    /**
     * @return BrowseHelper
     */
    public function browse()
    {
        return \Phpfox::getService('mobile.helper.search.browse');
    }

    /**
     * @return RequestHelper
     */
    private function _request()
    {
        return \Phpfox::getService('mobile.helper.request');
    }

    /**
     * @return bool
     */
    public function isBIsIgnoredBlocked()
    {
        return $this->_bIsIgnoredBlocked;
    }

    /**
     * @param bool $bIsIgnoredBlocked
     */
    public function setBIsIgnoredBlocked($bIsIgnoredBlocked)
    {
        $this->_bIsIgnoredBlocked = $bIsIgnoredBlocked;
    }


}