<div class="checkbox p-checkbox-custom">
    <label for="custom_field_{$aField.field_id}">
        <input type="checkbox" name="customfield[{$aField.field_id}]" id="custom_field_{$aField.field_id}"
                {if $aField.data == 'on'} checked="checked"{/if}>
        <i class="ico ico-square-o mr-1"></i> {phrase var=$aField.phrase_var_name}
    </label>
</div>