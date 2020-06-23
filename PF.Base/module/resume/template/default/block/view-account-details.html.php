<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
?>

<ul>
	<!-- account name information -->
	<li class="resume_view_detail_row">
		<div class="resume_view_detail_label">
			{_p var='your_name'}:
		</div>
		<div class="resume_view_detail_content">
			{$aAccount.name}
		</div>
	</li>
	<!-- Company name information -->
	{if $aAccount.company_name!=""}
	<li class="resume_view_detail_row">
		<div class="resume_view_detail_label">
			{_p var='company_name'}:
		</div>
		<div class="resume_view_detail_content">
			{$aAccount.company_name}
		</div>
	</li>
	{/if}
	<!-- Website information -->
	{if $aAccount.website!=""}
	<li class="resume_view_detail_row">
		<div class="resume_view_detail_label">
			{_p var='website'}:
		</div>
		<div class="resume_view_detail_content">
			<a href="{$aAccount.website}" target="_blank">{$aAccount.website}</a>
		</div>
	</li>
	{/if}
	<!-- Email information -->
	<li class="resume_view_detail_row">
		<div class="resume_view_detail_label">
			{_p var='email'}:
		</div>
		<div class="resume_view_detail_content">
			{$aAccount.email}
	</li>
	<!-- Zip Codde Information -->
	{if $aAccount.zip_code!=""}
	<li class="resume_view_detail_row">
		<div class="resume_view_detail_label">
			{_p var='zip_code'}:
		</div>
		<div class="resume_view_detail_content">
				{$aAccount.zip_code}
		</div>
	</li>
	{/if}
	<!--Telephone Information -->
	{if $aAccount.telephone!=""}
	<li class="resume_view_detail_row">
		<div class="resume_view_detail_label">
			{_p var='telephone'}:
		</div>
		<div class="resume_view_detail_content">
			{$aAccount.telephone}
		</div>
	</li>
	{/if}
</ul>