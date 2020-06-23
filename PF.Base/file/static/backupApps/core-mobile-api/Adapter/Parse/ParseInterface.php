<?php

namespace Apps\Core_MobileApi\Adapter\Parse;


interface ParseInterface
{
    /**
     * @param      $text
     * @param bool $parseNewLine
     *
     * @return mixed
     */
    function parseOutput($text, $parseNewLine = true);

    /**
     * @param      $text
     * @param bool $htmlChar
     *
     * @return mixed
     */
    function cleanOutput($text, $htmlChar = true);

    /**
     * @param      $text
     * @param null $shorten
     *
     * @return mixed
     */
    function cleanInput($text, $shorten = null);

    /**
     * @param $text
     *
     * @return mixed
     */
    function feedStrip($text);

    /**
     * @param $text
     *
     * @return mixed
     */
    function parseTwaEmoji($text);
}