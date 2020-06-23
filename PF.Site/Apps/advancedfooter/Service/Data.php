<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
namespace Apps\Advancedfooter\Service;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');

class Data extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('landing');
    }

    public function getUsers($limit = 9)
    {
        $aRows = [];
        switch (Phpfox::getParam('advancedfooter.userwidgetlogic')) {
            case "recent":
                $aRows =  $this->database()->select('u.*,uf.total_view,uf.total_friend')
                    ->from(Phpfox::getT('user_field'), 'uf')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = uf.user_id')
                    ->order('u.user_id DESC')
                    ->where('u.status_id = 0 AND u.view_id = 0')
                    ->limit($limit)
                    ->execute('getSlaveRows');
                break;
            case "featured":
                $aRows =  $this->database()->select('u.*,uf.total_view,uf.total_friend')
                    ->from(Phpfox::getT('user_field'), 'uf')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = uf.user_id')
                    ->join(Phpfox::getT('user_featured'), 'f', 'f.user_id = u.user_id')
                    ->order('RAND()')
                    ->limit($limit)
                    ->execute('getSlaveRows');
                break;
            case "popular":
                $aRows =  $this->database()->select('u.*,uf.total_view,uf.total_friend')
                    ->from(Phpfox::getT('user_field'), 'uf')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = uf.user_id')
                    ->order('uf.total_view DESC')
                    ->where('u.status_id = 0 AND u.view_id = 0')
                    ->limit($limit)
                    ->execute('getSlaveRows');
                break;
        }

        return $aRows;
    }
}
