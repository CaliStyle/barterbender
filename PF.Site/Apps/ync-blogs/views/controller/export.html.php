<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 13/02/2017
 * Time: 22:36
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{if $sTemplate == 'export_wordpress'}
{template file='ynblog.block.export_wordpress'}
{elseif $sTemplate == 'export_tumblr'}
{template file='ynblog.block.export_tumblr'}
{elseif $sTemplate == 'export_blogger'}
{template file='ynblog.block.export_blogger'}
{/if}
