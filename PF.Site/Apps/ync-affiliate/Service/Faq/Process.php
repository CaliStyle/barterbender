<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 18:24
 */

namespace Apps\YNC_Affiliate\Service\Faq;

use Phpfox;

Class Process extends \Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = \Phpfox::getT('yncaffiliate_faqs');
    }

    public function deleteFaq($iFaqId)
    {
        db()->delete($this->_sTable, 'faq_id = ' . (int)$iFaqId);

        return true;
    }

    public function updateFaq($iFaqId, $aVals)
    {
        return db()->update($this->_sTable, [
            'question' => $aVals['question'],
            'answer'   => $aVals['answer'],
        ], 'faq_id=' . (int)$iFaqId);
    }

    public function addFaq($aVals)
    {
        db()->insert($this->_sTable, [
            'question'   => $aVals['question'],
            'answer'     => $aVals['answer'],
            'time_stamp' => time(),
        ]);
    }
}