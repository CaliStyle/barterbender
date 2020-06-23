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
class Socialad_Service_FAQ_FAQ extends Phpfox_Service
{

    private $_sDisplay = 'select';
    private $_iCnt = 0;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('socialad_faq');
    }

    public function getForEdit($iId)
    {
        return $this->database()->select('*')
            ->from($this->_sTable)
            ->where('faq_id = ' . (int)$iId)
            ->execute('getRow');
    }

    public function display($sDisplay)
    {
        $this->_sDisplay = $sDisplay;

        return $this;
    }

    public function get()
    {
        $sCacheId = $this->cache()->set('socialad_faq_display_' . $this->_sDisplay . '_' . Phpfox::getLib('locale')->getLangId());

        if ($this->_sDisplay == 'admincp') {
            $sOutput = $this->_get(0, 1);

            return $sOutput;
        } else {
            if (!($this->_sOutput = $this->cache()->get($sCacheId))) {
                $this->_get(0, 1);

                $this->cache()->save($sCacheId, $this->_sOutput);
            }

            return $this->_sOutput;
        }
    }

    private function _get($iParentId, $iActive = null)
    {

        $aFAQs = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int)$iParentId . ' AND is_active = ' . (int)$iActive . '')
            ->order('ordering ASC, faq_id DESC')
            ->execute('getRows');

        if (count($aFAQs)) {
            $aCache = array();

            if ($iParentId != 0) {
                $this->_iCnt++;
            }

            if ($this->_sDisplay == 'option') {

            } elseif ($this->_sDisplay == 'admincp') {
                $sOutput = '<ul class="dont-unbind">';
            } else {
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_' . $iParentId . '" ' . ($iParentId > 0 ? ' style="display:none; padding:5px 0px 0px 0px;"' : '') . '>';
                $this->_sOutput .= '<select name="val[faq][' . $iParentId . ']" class="js_mp_faq_list ynfr required" id="js_mp_id_' . $iParentId . '">' . "\n";
                $this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('select') : _p('select_a_sub_faq')) . ':</option>' . "\n";
            }

            foreach ($aFAQs as $iKey => $aFaq) {
                $aCache[] = $aFaq['faq_id'];

                if ($this->_sDisplay == 'option') {
                    $this->_sOutput .= '<option value="' . $aFaq['faq_id'] . '" id="js_mp_faq_item_' . $aFaq['faq_id'] . '">' . ($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt * 2)) . ' ' : '') . Phpfox::getLib('locale')->convert($aFaq['question']) . '</option>' . "\n";
                } elseif ($this->_sDisplay == 'admincp') {
                    $sOutput .= '<li><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png') . '" alt="" /> <input type="hidden" name="order[' . $aFaq['faq_id'] . ']" value="' . $aFaq['ordering'] . '" class="form-control js_mp_order" /><a href="#?id=' . $aFaq['faq_id'] . '" class="js_drop_down">' . strip_tags(Phpfox::getLib('parse.input')->prepare($aFaq['question'])) . '</a>' . $this->_get($aFaq['faq_id'], $iActive) . '</li>' . "\n";
                } else {
                    $this->_sOutput .= '<option value="' . $aFaq['faq_id'] . '" id="js_mp_faq_item_' . $aFaq['faq_id'] . '">' . Phpfox::getLib('locale')->convert($aFaq['question']) . '</option>' . "\n";
                }
            }

            if ($this->_sDisplay == 'option') {

            } elseif ($this->_sDisplay == 'admincp') {
                $sOutput .= '</ul>';

                return $sOutput;
            } else {
                $this->_sOutput .= '</select>' . "\n";
                $this->_sOutput .= '</div>';

                foreach ($aCache as $iFaqId) {
                    $this->_get($iFaqId, $iActive);
                }
            }

            $this->_iCnt = 0;
        }
    }

    public function get_frontend()
    {
        return $this->database()->select('faq_id, question_parsed as question, answer_parsed as answer')
            ->from($this->_sTable)
            ->order('ordering ASC')
            ->execute('getRows');
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
        if ($sPlugin = Phpfox_Plugin::get('socialad.service_faq_faq__call')) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

}

?>
