<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

defined('PHPFOX') or exit('NO DICE!');

class Gettingstarted_service_search extends Phpfox_Service
{

    public function highlight($word, $text)
    {
        if ($word != '') {
            $text = preg_replace('/(' . preg_quote($word, '/') . ')/siU', '<span class="highlight">\\1</span>', $text);
        }

        return $text;
    }

}

?>
