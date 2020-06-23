<?php
/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          2.01
 */

defined('PHPFOX') or exit('NO DICE!');

?>

{if count($aCategories)}

<form method="post" action="{url link='admincp.gettingstarted.managecategory'}">
        <div class="table_header">
       {phrase var='gettingstarted.manage_mail_categories'}
    </div>
	<table>
	<tr>
        <th>{phrase var='gettingstarted.module'}</th>
		<th>Description</th>
        <th>{phrase var='gettingstarted.edit'}</th>
	</tr>
	{foreach from=$aCategories key=iKey item=aCategory}
	<tr id="js_row{$aCategory.scheduledmail_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
        <td style="width: 130px;">{$aCategory.scheduledmail_name}</td>
        <td>{$aCategory.description}</td>
        <td style="width: 45px;"><a href="{url link='admincp/gettingstarted/editmanagecategory'}id_{$aCategory.scheduledmail_id}">Edit</a></td>
	</tr>
	{/foreach}
	</table>

	{else}

	{/if}
</form>

{pager}