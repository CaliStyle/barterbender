<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="POST" id="js_comparison_form_product" name="js_comparison_form_product">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='ynsocialstore.manage_product_comparison'}
            </div>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <p class="help-block">{_p var='ynsocialstore.which_information_about_products_you_want_to_help_your_to_members_compare'}</p>
                <div class="comparison_section" data-maxchecked="9">
                    <div class="checkbox">
                        <label><input type="checkbox" class="main_checkbox"> <i>{_p var='ynsocialstore.select_all'}</i></label>
                    </div>
                    {foreach from=$aProductFields key=iKey item=aField}
                    <div class="checkbox">
                        <label>
                            <input class="sub_checkbox" type="checkbox" name="val[comparison_field][]" value="{$aField.comparison_id}"
                                   {if $aField.enable == 1}
                                        checked="checked"
                                   {/if}
                            > {_p var=$aField.phrase|convert}
                        </label>
                    </div>
                    {/foreach}
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <button type="submit" name="val[product]" class="btn btn-primary">{_p var='ynsocialstore.submit'}</button>
        </div>
    </div>
</form>

<form method="POST" id="js_comparison_form_product" name="js_comparison_form_product">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='ynsocialstore.manage_store_comparison'}
            </div>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <p class="help-block">{_p var='ynsocialstore.which_information_about_stores_you_want_to_help_your_to_members_compare'}</p>
                <div class="comparison_section" data-maxchecked="10">
                    <div class="checkbox">
                        <label><input type="checkbox" class="main_checkbox"> <i>{_p var='ynsocialstore.select_all'}</i></label>
                    </div>
                    {foreach from=$aStoreFields item=aField}
                    <div class="checkbox">
                        <label>
                            <input class="sub_checkbox" type="checkbox" name="val[comparison_field][]" value="{$aField.comparison_id}"
                                   {if $aField.enable == 1}
                                   checked="checked"
                                   {/if}
                            > {_p var=$aField.phrase|convert}
                        </label>
                    </div>
                    {/foreach}
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <button type="submit" name="val[store]" class="btn btn-primary">{_p var='ynsocialstore.submit'}</button>
        </div>
    </div>
</form>

{literal}
<script type="text/javascript">
    $Behavior.ynsocialstore_check_all_package = function() {
        ComparisonSection.init();
    };

    var ComparisonSection = {
        wrapper_class: '.comparison_section',
        init: function () {
            $('.comparison_section')
                .on('click', '.main_checkbox', function () {
                    if ($(this).prop('checked')) {
                        $(this).parents('.comparison_section').find('.sub_checkbox').prop('checked', true);
                    } else {
                        $(this).parents('.comparison_section').find('.sub_checkbox').prop('checked', false);
                    }
                }).on('change', function () {
                    ComparisonSection.is_check_all($(this));
                }).each(function () {
                    ComparisonSection.is_check_all($(this));
                });
        },
        is_check_all: function (section) {
            var total_checked_checkboxes = $(section).find('.sub_checkbox:checked').length;
            var total_max_checked = $(section).data('maxchecked');
            if (parseInt(total_checked_checkboxes) === parseInt(total_max_checked)) {
                $(section).find('.main_checkbox').prop('checked', true);
            } else {
                $(section).find('.main_checkbox').prop('checked', false);
            }
        }
    };
</script>
{/literal}
