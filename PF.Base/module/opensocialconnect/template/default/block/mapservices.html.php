<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form class="yncontactimporter_mapping_serive" method="post" action="{url link='admincp.opensocialconnect.providers'}" id="admincp_contactimporter_form_message">
	<h4>{phrase var='opensocialconnect.provider_to_profile_questions', provider =$titleProiver}</h4>
	<div>{phrase var='opensocialconnect.set_none_value_if_you_do_not_need_to_map_provider_fields', provider =$titleProiver}</div>
	<br />

    <h4>{phrase var='user.basic_information'}</h4>
    <input type="hidden" name="val[provider]" value="{$provider}"  />
    <!-- full name -->
    <div class="form-group">
        <label for="user_name">{phrase var='user.user_name'}</label>
        <select name="val[user_name]" id="user_name" class="form-control">
            <option value="all">{phrase var='user.select'}</option>
            {foreach from=$aProviderOptions  item=options}
            <option value="{$options.name}">{$options.label}</option>
            {/foreach}
        </select>
    </div>
    <!-- full name -->
    <div class="form-group">
        <label for="full_name">{phrase var='user.full_name'}</label>
        <select name="val[full_name]" id="full_name" class="form-control">
            <option value="all">{phrase var='user.select'}</option>
            {foreach from=$aProviderOptions  item=options}
            <option value="{$options.name}">{$options.label}</option>
            {/foreach}
        </select>
    </div>
    <!-- Gender -->
    <div class="form-group">
        <label for="gender">{phrase var='user.gender'}</label>
        <select name="val[gender]" id="gender" class="form-control">
            <option value="all">{phrase var='user.select'}</option>
            {foreach from=$aProviderOptions  item=options}
            <option value="{$options.name}">{$options.label}</option>
            {/foreach}
        </select>
    </div>
    <!-- Email -->
    <div class="form-group">
        <label for="email">{phrase var='user.email'}</label>
        <select name="val[email]" id="email" class="form-control">
            <option value="all">{phrase var='user.select'}</option>
            {foreach from=$aProviderOptions  item=options}
            <option value="{$options.name}">{$options.label}</option>
            {/foreach}
        </select>
    </div>
    <!-- Birthday -->
    <div class="form-group">
        <label for="birthday">{phrase var='user.birthday'}</label>
        <select name="val[birthday]" id="birthday" class="form-control">
            <option value="all">{phrase var='user.select'}</option>
            {foreach from=$aProviderOptions  item=options}
            <option value="{$options.name}">{$options.label}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-sm btn-primary">{phrase var='user.save_changes'}</button>
    </div>
</form>

<script type="text/javascript">
	{foreach from=$aProviderFields item=fields}
		{if (isset($fields.field))} 
				$("#{$fields.question}").val("{$fields.field}");
		{/if}
	{/foreach}
</script>
