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
 * @package        Module_Coupon
 * @version        3.01
 */
class Coupon_Component_Block_KeywordPlaceholder extends Phpfox_Component
{

        /**
         * Class process method wnich is used to execute this component.
         */
        public function process()
        {
                $aKeywordPlaceholder = Phpfox::getService('coupon.mail')->getAllReplaces();


                foreach ($aKeywordPlaceholder as &$sKeyword)
                {
                        $sKeyword = _p('keywordsub_' . $sKeyword);
                }

                $this->template()->assign(array(
                        'aKeywordPlaceholder' => $aKeywordPlaceholder
                ));
        }

}

?>