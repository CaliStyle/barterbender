<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" id="js_comparison_form" name="js_comparison_form" >
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='manage_comparison'}
            </div>
        </div>
        <div class="panel-body">
            <p class="help-block">{phrase var='select_the_information_you_want_to_use_to_compare_businesses'}</p>
            <div id="comparison_field">
                <div class="checkbox">
                    <label><input type="checkbox"  value="1" id='select_all_comparison_field'><i>{phrase var='select_all'}</i></label>
                </div>
                {foreach from=$aFields item=aField}
                <div class="checkbox">
                    <label><input type="checkbox" class="comparison_field_checkbox" name="val[comparison_field][]" value="{$aField.comparison_id}"
                           {if $aField.is_active == 1}
                           checked="checked"
                           {/if}
                    > {$aField.comparison_name|convert}</label>
                </div>
                {/foreach}
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" name="val[submit]" value="{phrase var='submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>

{literal}
<script type="text/javascript">
    $Behavior.directory_check_all_package = function () {
        var number_comparison_field = 11;

        function check_is_all() {
            if ($('.comparison_field_checkbox:checked').length === number_comparison_field) {
                $('#select_all_comparison_field').prop('checked', true);
            } else {
                $('#select_all_comparison_field').prop('checked', false);
            }
        }

        check_is_all();
        $('.comparison_field_checkbox').on('click', function () {
            check_is_all();
        });

        $('#select_all_comparison_field').on('click', function () {
            if ($('#select_all_comparison_field').prop('checked')) {
                $('.comparison_field_checkbox').prop('checked', true);
            }
            else {
                $('.comparison_field_checkbox').prop('checked', false);
            }
        });
    }
</script>
{/literal}
