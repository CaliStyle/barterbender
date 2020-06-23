<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 1594 2010-05-22 22:49:41Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !count($aInvoices)}
<div class="extra_info">
	{phrase var='advancedmarketplace.you_do_not_have_any_invoices'}
</div>
{else}
<table class="default_table" cellpadding="0" cellspacing="0">
	<tr>
		<th>{phrase var='advancedmarketplace.id'}</th>
		<th>{phrase var='advancedmarketplace.status'}</th>	
		<th>{phrase var='advancedmarketplace.price'}</th>
		<th>{phrase var='advancedmarketplace.date'}</th>				
	</tr>
	{foreach from=$aInvoices item=aInvoice}
	<tr>
		<td class="t_center">{$aInvoice.invoice_id}</td>
		<td>{$aInvoice.status_phrase}{if $aInvoice.status === null || $aInvoice.status == 'pending'} (<a href="{url link='advancedmarketplace.purchase' invoice=$aInvoice.invoice_id}">{phrase var='advancedmarketplace.pay_now'}</a>){/if}</td>
		<td>{$aInvoice.price|currency:$aInvoice.currency_id}</td>
		<td>{$aInvoice.time_stamp|date}</td>
	</tr>
	{/foreach}
</table>
{/if}