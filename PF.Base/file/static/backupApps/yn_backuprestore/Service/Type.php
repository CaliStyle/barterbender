<?php
/**
 * User: huydnt
 * Date: 05/01/2017
 * Time: 08:46
 */

namespace Apps\yn_backuprestore\Service;


use Phpfox;
use Phpfox_Service;

class Type extends Phpfox_Service
{
    protected $_typeIdCol = 'type_id';
    protected $_titleCol = 'title';

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynbackuprestore_destination_types');
    }

    /**
     * get all destination type
     * @return array|int|string
     */
    public function getAllTypes()
    {
        return $this->database()->select('*')->from($this->_sTable)->execute('getslaverows');
    }
}