<?php
namespace Apps\YNC_Feed\Service;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Request;
use Core;
use Phpfox_Ajax;
use Phpfox_Url;
use Phpfox_Template;
use Phpfox_Error;
use Phpfox_Database;

defined('PHPFOX') or exit('NO DICE!');

class Emoticon extends \Phpfox_Service
{
    protected $_sTable;
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynfeed_emoticon');
    }

    public function getAll() {
        return get_from_cache(['ynfeed.emoticons'],function() {
            return db()->select('*')->from($this->_sTable)->execute('getSlaveRows');
        }, 1);
    }
}