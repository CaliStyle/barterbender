<?php
namespace Apps\Core_Subscriptions\Service;

use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');

class Helper extends Phpfox_Service
{
    /** generate a random transaction id
     * @param int $size (default is 17,which is length of paypal transaction
     * @return string
     */
    public function generateTransactionId($size = 17) {
        $sAlpha = '';
        $keys = range('A', 'Z');

        for ($i = 0; $i < 2; $i++) {
            $sAlpha .= $keys[array_rand($keys)];
        }

        $length = $size - 2;

        $sNumber = '';
        $keys = range(0, 9);

        for ($i = 0; $i < $length; $i++) {
            $sNumber .= $keys[array_rand($keys)];
        }

        return $sAlpha.$sNumber;
    }
}