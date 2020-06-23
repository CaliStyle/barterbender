<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Coupon
 * @version        3.01
 *
 */
 ?>
{literal}
<style type="text/css">
	
	._block_content{
		background: #FFF;
		padding-bottom: 15px;
	}
	#left {
		width: 0px;
		display: none;
	}
	#right {
		width: 0px;
		display: none;
	}
	.content4 {
		width: 100%;
	}
	.ync-review-print {
		border: solid 1px gray;
		width: 500px;
		margin-left: 250px;
		padding: 0px 10px 10px 10px;
	}

	.preview-bar {
		width: 100%;
		background-color: #DFDFDF;
		color: #0099FF;
		text-align: center;
		padding: 10px;
		font-size: 16px;
		font-weight: bold;
		margin-bottom: 20px;
		box-sizing: border-box;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
	}
	.preview-bar a {
		text-decoration: none;
		text-transform: uppercase;
	}
    .ync-style {
        margin: 0 auto;
    }
</style>
<style media="print">
	.preview-bar {
		display: none;
	}
	#feedback-bt {
		display: none;
	}
	#breadcrumb_holder {
		display: none;
	}
	#header {
		display: none;
	}
	#tm_top_bar {
		display: none;
	}
	#main_footer_holder {
		display: none;
	}
	#js_main_debug_holder {
		display: none;
	}
	#im_footer_wrapper {
		display: none;
	}
	#tourcontrols {
		display: none;
	}
</style>
{/literal}

<div class="preview-bar">
	<a href="#" onclick="$('#tm_top_bar').hide(); window.print(); window.close(); return false;">{phrase var="print"}</a>
</div>

{if isset($sHtml)}
    {$sHtml}
{else}
    {if empty($aCoupon.print_option.style) || (isset($aCoupon.print_option.style) && $aCoupon.print_option.style=='1')}
        {template file='coupon.block.print.style1'}
    {elseif $aCoupon.print_option.style=='2'}
        {template file='coupon.block.print.style2'}
    {elseif $aCoupon.print_option.style=='3'}
        {template file='coupon.block.print.style3'}
    {elseif $aCoupon.print_option.style=='4'}
        {template file='coupon.block.print.style4'}
    {elseif $aCoupon.print_option.style=='5'}
        {template file='coupon.block.print.style5'}
    {/if}
{/if}
