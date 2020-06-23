<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL, TrucPTM
 * @package        Module_Resume
 * @version        3.01
 *
 */?>
<!-- Basic information layout here -->
{if !$aOptions.no_resume }
<div class="resume_header_link">
	{if $aOptions.can_favorite}
		<a class="yns-item yns-fav" href="javascript:void(0);" onclick="FavoriteAction('favorite',{$aResume.resume_id},'detail');return false;" id="js_favorite_link_like_{$aResume.resume_id}" {if $aOptions.favorited }style="display:none;"{/if}>{_p var='favorite'}</a>
		<a class="yns-item yns-un-fav" href="javascript:void(0);" onclick="FavoriteAction('unfavorite',{$aResume.resume_id},'detail');return false;" id="js_favorite_link_unlike_{$aResume.resume_id}" {if !$aOptions.favorited }style="display:none;"{/if}>{_p var='unfavorite'}</a>
	{/if}
	{if $aOptions.can_note}
		<a class="yns-item yns-note" href="javascript:void(0);" onclick="NoteAction('note',{$aResume.resume_id});return false;" id="js_favorite_link_note_{$aResume.resume_id}" {if $aOptions.noted} style="display:none;"{/if}>{_p var='note'}</a>
		<a class="yns-item yns-note" href="javascript:void(0);" onclick="NoteAction('unnote',{$aResume.resume_id});return false;" id="js_favorite_link_unnote_{$aResume.resume_id}" {if !$aOptions.noted} style="display:none;"{/if}>{_p var='unnote'}</a>
	{/if}
	{if $aOptions.can_send_message}
		<a class="yns-item yns-mail" href="javascript:void(0);" onclick="$Core.box('resume.sendMessagePupUp',400,'user_id={$aResume.user_id}&resume_id={$aResume.resume_id}&type=2');">{_p var='send_email'}</a>
	{/if}
	{if $aOptions.can_export_resume}
		 <a class="yns-item  yns-item-pdf no_ajax"
		   href="{$linkdownloadpdf}">{_p var='pdf_printer'}</a>
	{/if}
	{if $aResume.user_id == Phpfox::getUserId() }
		{if $aResume.is_completed}
			<!-- not published yet -->
			{if !$aResume.is_published}
				<!-- has a resume is being approved or not -->
				{if !$bIsApproving and $aResume.status != 'denied'}
					<a class="yns-item sJsConfirm" href="{url link='resume.publish' id=$aResume.resume_id isdetail = true}" data-message="{_p var='are_you_sure_you_want_to_publish_this_resume' phpfox_squote=true}">
						<i class="fa fa-globe" style="color: #666; font-size: 14px;"></i> {phrase var="resume.publish"}
					</a>
				{/if}
			<!-- published -->
			{else}
				<!-- the current is being approved or not -->
				{if $aResume.status == 'approved'}
					<a class="yns-item sJsConfirm" href="{url link='resume.private' id=$aResume.resume_id isdetail = true}" data-message="{_p var='are_you_sure_you_want_to_set_private_this_resume' phpfox_squote=true}">
						<i class="fa fa-lock" style="color: #666; font-size: 14px;"></i> {phrase var="resume.private"}
					</a>
				{/if}
			{/if}
		{/if}
	{/if}
</div>
<div class="yns resume_basic {if !$aOptions.can_edit}no-res-complete{/if}">
	{if $aOptions.noted}
	<div class="basic_note" id="note_resume_{$aResume.resume_id}">
		<span class="extra_info">(*) {phrase var="resume.note"}: {$aOptions.noted|parse}</span>
	</div>
	<div class="clear"></div>
	{/if}
	<div class="basic_info_content">
		<div class="yns-bg">
			<!-- resume image -->
			<div class="resume_image">
				{if $aResume.image_path!=""}
					{img server_id=$aResume.server_id path='core.url_pic' file='resume/'.$aResume.image_path suffix='_200' max_width='120' max_height='120'}
					{if $aOptions.can_edit }
						<p><b><a class="button btn btn-success btn-sm" href="{url link='resume.add.id_'.$aResume.resume_id}">{_p var='edit_photo'}</a></b></p>
					{/if}
				{else}
					<img class="default_resume_image" src="{$sCorePath}module/resume/static/image/profile.png" />
					{if $aOptions.can_edit }
						<p><b><a href="{url link='resume.add.id_'.$aResume.resume_id}">+ {_p var='add_a_photo'}</a></b></p>
					{/if}
				{/if}
			</div>
			<div class="basic_info">
				<div>
				<!-- full name - birthday - gender - marital status -->
					<p>
						<span class="name">{$aResume.full_name}</span>
                        <div class="yns-detail-action">
                            {if $aOptions.can_edit}
                                <a href="{url link='resume.add.id_'.$aResume.resume_id}" class="edit"><i class="fa fa-edit"></i> {phrase var="resume.edit"}</a>
                            {/if}
                            {if $aOptions.can_delete }
                                <a class="sJsConfirm edit" data-message="{_p('are_you_sure_you_want_to_delete_this_resume')}"  href="{url link='resume.delete.id_'.$aResume.resume_id}"><i class="fa fa-trash"></i> {_p var='delete'}</a>
                            {/if}
                        </div>
					</p>
					<p class="extra_info">
						{if !empty($aResume.birthday_parsed) && $aResume.display_date_of_birth}
						  {$aResume.birthday_parsed}
						{/if}
						{if !empty($aResume.gender_parsed) && $aResume.display_gender}
						  {if !empty($aResume.birthday_parsed)} | {/if}
						  {$aResume.gender_parsed}
						{/if}
						{if !empty($aResume.marital_status) && $aResume.display_marital_status}
						  {if !empty($aResume.birthday_parsed) or !empty($aResume.gender_parsed)}	| {/if}
						  {phrase var="resume.".$aResume.marital_status}
						{/if}
					</p>
					<!-- Current position -->
					<p>
					{if $aCurrentWork}
						{$aCurrentWork.title} {phrase var="resume.at"} {$aCurrentWork.company_name}
					{elseif $aOptions.can_edit}
						<a href="{url link='resume.experience.id_'$aResume.resume_id}" class="add"><b>+ {_p var='add_current_work'}</b></a>
					{/if}
					</p>
					<!-- Country + City -->
					<p class="extra_info">
						{$aResume.country_iso|location}
						{if !empty($aResume.location_child_id) }, {$aResume.country_child_id|location_child}{/if}
						{if !empty($aResume.city) }
							{if !empty($aResume.country_iso) } > {/if}
							{$aResume.city}
                        {/if}
					</p>
				</div>
				<div class="person-info">
					{ if count($aCats) > 0 or $aOptions.can_edit }
					<div class="info">
					<!-- Category list -->
						<div class="info_left">{_p var='categories'}:</div>
						<div class="info_right">
							{if count($aCats) > 0}
								{foreach from = $aCats key = iKey item = aCat}
									{if $iKey == 0}
										<a href="{permalink module='resume.category' id=$aCat.category_id title=$aCat.name_url}">{$aCat.name|convert}</a>
									{else}
										| <a href="{permalink module='resume.category' id=$aCat.category_id title=$aCat.name_url}">{$aCat.name|convert}</a>
									{/if}

								{/foreach}
							{else}
								<a href="{url link='resume.summary.id_'$aResume.resume_id}" class="add"><b>+ {_p var='add'}</b></a>
							{/if}
						</div>
					</div>
					{/if}
					<!-- Year of experience -->
					{if $aResume.year_exp != "0" or $aOptions.can_edit }
					<div class="info">
						<div class="info_left">{phrase var="resume.years_of_experience"}:</div>
						<div class="info_right">
							{if $aResume.year_exp > 1 }
								{$aResume.year_exp} {phrase var="resume.years"}
							{elseif $aResume.year_exp == 1}
								{$aResume.year_exp} {phrase var="resume.lowercase_year"}
							{else}
								<a href="{url link='resume.summary.id_'$aResume.resume_id}" class="add"><b>+ {_p var='add'}</b></a>
							{/if}
						</div>
					</div>
					{/if}
					<!-- Highest level -->
					{if $aResume.level_id > 0 or $aOptions.can_edit}
					<div class="info">
						<div class="info_left">{phrase var="resume.highest_level"}: </div>
						<div class="info_right">
							{if $aResume.level_id > 0 }
								{$aResume.level_name}
							{else}
								<a href="{url link='resume.summary.id_'$aResume.resume_id}" class="add"><b>+ {_p var='add'}</b></a>
							{/if}
						</div>
					</div>
					{/if}
					<!-- Education -->
					{if $aLatestEducation or $aOptions.can_edit}
					<div class="info">
						<div class="info_left">{_p var='education'}: </div>
						<div class="info_right">
							{if $aLatestEducation}
								{$aLatestEducation.degree}, {$aLatestEducation.field} {phrase var="resume.at"} {$aLatestEducation.school_name}
							{else}
								<a href="{url link='resume.education.id_'$aResume.resume_id}" class="add"><b>+ {_p var='add'}</b></a>
							{/if}
						</div>
					</div>
					{/if}
					<!-- Authorized to work on -->
					{if $aResume.authorized}
                        <div class="info">
                            <div>
                                <strong>{phrase var="resume.authorized_to_work_in"}</strong>
                            </div>
                        </div>

                        {foreach from=$aResume.authorized key=i item=aItem}
                            <div class="info">
                                {if $aItem.label_country_iso}
                                    <div class="info_left">{_p var='country'}: </div>
                                    <div class="info_right">
                                        {$aItem.country_iso|location}
                                        {if !empty($aItem.country_child) }, {$aItem.country_child|location_child}{/if}
                                    </div>
                                {/if}
                                {if $aItem.location}
                                    <div class="info_left">{_p var='location'}: </div>
                                    <div class="info_right">
                                        {$aItem.location}
                                    </div>
                                {/if}
                                {if $aItem.other_level }
                                    <div class="info_left">{_p var='position'}: </div>
                                    <div class="info_right">
                                        {$aItem.other_level}
                                    </div>
                                {elseif $aItem.level_id >0 }
                                    <div class="info_left">{_p var='position'}: </div>
                                    <div class="info_right">
                                        {$aItem.label_level_id}
                                    </div>
                                {/if}
                            </div>
                        {/foreach}
					{/if}

					{if $turnonFields}
					<div class="info">
						<div>
							<strong>{_p var='custom_fields'}</strong>
						</div>
						<div class="mt-1">
							{foreach from = $aViewCustomFields item=aCustomField}
							{if $aCustomField.value!=""}
								<div class="info_left"> {phrase var=$aCustomField.phrase_var_name}: </div>
								<div class="info_right">
									{$aCustomField.value}
								</div>
							{/if}
							{/foreach}
						</div>
					</div>
					{/if}
				</div>
			</div>
		</div>
		{if $aOptions.can_edit}
			<div class="res-complete">
				<div class="res-percent">
					<span>{$percentfinish}%</span>{_p var='of_resume_complete'}
				</div>
				<div class="meter-wrap-l">
					<div class="meter-wrap-r">
						<div class="meter-wrap">
							<div class="meter-value" style="width: {$percentfinish}%">
								{$percentfinish}%
							</div>
						</div>
					</div>
	            </div>
	            {if $percentfinish!=100}
				<div class="res-tip">
					<p class="tip-title">{_p var='resume_completion_tips'}</p>
					{foreach from=$aUncomplete item=Uncomplete}
						{$Uncomplete}
					{/foreach}
				</div>
				{/if}
			</div>
		{/if}
	</div>
	<div class="clear"></div>
	{if $aOptions.can_edit }
	<!-- <div class="yns contact-info new-section extra_info">
		{_p var='add_sections_to_refelct_archivement_and_experience_on_your_profile'}
		<a href="#" class="add-new"><b>+ {_p var='add_section'}</b></a>
	</div> -->
	{/if}
    {if $aResume.status == 'approving'}
        {template file='core.block.pending-item-action'}
    {/if}
	<!-- Contact Information -->
	<div class="yns contact-info extra_info">
			<h3>
				{phrase var="resume.contact_info"}
				{ if $aOptions.can_edit }
					<a href="{url link='resume.add.id_'.$aResume.resume_id}" class="add-new">{phrase var="resume.edit"}</a>
				{/if}
			</h3>
			<!-- Phone -->
			{if !empty($aResume.phone)}
			<div class="info">
				<div class="info_left">{phrase var="resume.phone_number"}:</div>
				<div class="info_right">
					{foreach from=$aResume.phone item=aPhone}
					<p>{$aPhone.text} ({phrase var="resume.".$aPhone.type})</p>
					{/foreach}
				</div>
			</div>
			{/if}
			<!-- IM -->
			{if !empty($aResume.imessage)}
			<div class="info">
				<div class="info_left">{phrase var="resume.im"}:</div>
				<div class="info_right">
					{foreach from=$aResume.imessage item=aImessage}
					<p>{$aImessage.text} ({phrase var="resume.".$aImessage.type})</p>
					{/foreach}
				</div>
			</div>
			{/if}
			<!-- Email -->
			{if !empty($aResume.email)}
			<div class="info">
				<div class="info_left">{phrase var="resume.email"}:</div>
				<div>
					{foreach from=$aResume.email item=aEmail}
					<p>{$aEmail}</p>
					{/foreach}
				</div>
			</div>
			{/if}
	</div>

	<!-- Summary -->
	{if $aOptions.can_edit or $aResume.summary_parsed}
	<div class="yns contact-info summary_info extra_info">

		<h3>{phrase var="resume.summary"}
			{if $aOptions.can_edit}
			<a href="{url link='resume.summary.id_'.$aResume.resume_id}" class="add-new">{_p var='edit'}</a>
			{/if}
		</h3>
		<p>{$aResume.summary_parsed}</p>

	</div>
	{/if}
</div>
{/if}
