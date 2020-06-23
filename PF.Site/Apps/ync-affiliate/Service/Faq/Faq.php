<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 18:24
 */

namespace Apps\YNC_Affiliate\Service\Faq;

use Phpfox;

Class Faq extends \Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = \Phpfox::getT('yncaffiliate_faqs');
    }

    public function getFaqById($iFaqId)
    {
        return db()->select('faq.*')
            ->from($this->_sTable, 'faq')
            ->where('faq_id=' . (int)$iFaqId)
            ->execute('getSlaveRow');
    }

    public function get($aWhere = [])
    {
        $aWhere[] = '1=1';
        return db()->select('faq.*')
            ->from($this->_sTable, 'faq')
            ->where(implode(' AND ', $aWhere))
            ->order('ordering ASC')
            ->execute('getSlaveRows');
    }
    public function getByPage($iPage,$iLimit,$aWhere = [])
    {
        $aWhere[] = '1=1';
        $iCnt = db()->select('COUNT(*)')
                    ->from($this->_sTable)
                    ->execute('getSlaveField');
        $aFAQs = [];
        if($iCnt)
        {
            $aFAQs = db()->select('faq.*')
                            ->from($this->_sTable, 'faq')
                            ->where(implode(' AND ', $aWhere))
                            ->limit($iPage,$iLimit,$iCnt)
                            ->order('ordering ASC')
                            ->execute('getSlaveRows');
        }
        return [$iCnt,$aFAQs];
    }
}