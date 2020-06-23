<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_Socialad
 * @version        3.01
 */
class Socialad_Service_FAQ_Process extends Phpfox_Service
{

        /**
         * Class constructor
         */
        public function __construct()
        {
                $this->_sTable = Phpfox::getT('socialad_faq');
        }

        public function updateOrder($aVals)
        {
                foreach ($aVals as $iId => $iOrder)
                {
                        $this->database()->update($this->_sTable, array('ordering' => $iOrder), 'faq_id = ' . (int) $iId);
                }

                $this->cache()->remove('socialad', 'substr');

                return true;
        }

        public function delete($iId)
        {
                $this->database()->delete($this->_sTable, 'faq_id = ' . (int) $iId);
                $this->cache()->remove('socialad', 'substr');
                return true;
        }

		public function addInFrontEnd($aVals)
		{
                if (empty($aVals['question']))
                {
                        return false;
                }
                Phpfox::getService('ban')->checkAutomaticBan($aVals['question']);
                $oParseInput = Phpfox::getLib('parse.input');

                $iId = $this->database()->insert($this->_sTable, array(
                        'parent_id' => (!empty($aVals['parent_id']) ? (int) $aVals['parent_id'] : 0),
                        'is_active' => 1,
                        'question' => $oParseInput->clean($aVals['question']),
                        'question_parsed' => $oParseInput->prepare($aVals['question']),
                        'answer' => "",
                        'ordering' => 0,
						'user_id' => Phpfox::getUserId(),
                        'time_stamp' => PHPFOX_TIME
                        )
                );

                $this->cache()->remove('socialad', 'substr');

                return $iId;
		}
		
        public function add($aVals)
        {
                if (empty($aVals['question']))
                {
                        return Phpfox_Error::set(_p('provide_question_content'));
                }
                if (empty($aVals['answer']))
                {
                        return Phpfox_Error::set(_p('provide_answer_content'));
                }
                Phpfox::getService('ban')->checkAutomaticBan($aVals['question']);
                Phpfox::getService('ban')->checkAutomaticBan($aVals['answer']);
                $oParseInput = Phpfox::getLib('parse.input');

                $iId = $this->database()->insert($this->_sTable, array(
                        'parent_id' => (!empty($aVals['parent_id']) ? (int) $aVals['parent_id'] : 0),
                        'is_active' => 1,
                        'question' => $oParseInput->clean($aVals['question']),
                        'question_parsed' => $oParseInput->prepare($aVals['question']),
                        'answer' => $oParseInput->clean($aVals['answer']),
                        'answer_parsed' => $oParseInput->prepare($aVals['answer']),
                        'ordering' => 0,
						'user_id' => Phpfox::getUserId(),
                        'time_stamp' => PHPFOX_TIME
                        )
                );

                $this->cache()->remove('socialad', 'substr');

                return $iId;
        }

        public function update($iId, $aVals)
        {
                if (empty($aVals['question']))
                {
                        return Phpfox_Error::set(_p('provide_question_content'));
                }
                if (empty($aVals['answer']))
                {
                        return Phpfox_Error::set(_p('provide_answer_content'));
                }
				$oParseInput = Phpfox::getLib('parse.input');
				
                $this->database()->update($this->_sTable
                        , array(
                        'parent_id' => (!empty($aVals['parent_id']) ? (int) $aVals['parent_id'] : 0),
                        'question' => $oParseInput->clean($aVals['question']),
                        'question_parsed' => $oParseInput->prepare($aVals['question']),
                        'answer' => $oParseInput->clean($aVals['answer']),
                        'answer_parsed' => $oParseInput->prepare($aVals['answer']),
                        'time_stamp' => PHPFOX_TIME
                        )
                        , 'faq_id = ' . (int) $iId);

                $this->cache()->remove('socialad', 'substr');

                return true;
        }

        public function deleteMultiple($aIds)
        {
                foreach ($aIds as $iId)
                {
                        $this->database()->delete($this->_sTable, 'faq_id = ' . (int) $iId);
                }
                return true;
        }

        /**
         * If a call is made to an unknown method attempt to connect
         * it to a specific plug-in with the same name thus allowing 
         * plug-in developers the ability to extend classes.
         *
         * @param string $sMethod is the name of the method
         * @param array $aArguments is the array of arguments of being passed
         */
        public function __call($sMethod, $aArguments)
        {
                /**
                 * Check if such a plug-in exists and if it does call it.
                 */
                if ($sPlugin = Phpfox_Plugin::get('socialad.service_faq_process__call'))
                {
                        return eval($sPlugin);
                }

                /**
                 * No method or plug-in found we must throw a error.
                 */
                Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
        }

}

?>