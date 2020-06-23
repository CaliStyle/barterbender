<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<form method="post" action="{url link='admincp.profilecompleteness.settings'}" id="js_profile_compeleness_settings">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='profilecompleteness.profile_address'}
            </div>
        </div>
        <div class="panel-body">
            <div>
                <input type="hidden" name="val[user_image]" value="{$aForms.user_image}"/>
            </div>
            <div class="form-group dont-unbind-children w220" style="position: relative">
                <label for="gaugecolor">{phrase var='profilecompleteness.enter_gauge_color_in_hex'}</label>
                <input readonly class="_colorpicker form-control" data-old="{if isset($aForms.gaugecolor)}{$aForms.gaugecolor}{/if}"
                       autocomplete="off" type="text" name="val[gaugecolor]"
                       style="padding: 17px"
                       value="{if isset($aForms.gaugecolor)}{$aForms.gaugecolor}{/if}" id="gaugecolor">
                <div class="_colorpicker_holder"></div>
            </div>
            <div class="form-group">
                <label>{phrase var='profilecompleteness.whether_show_widget_or_not_when_100_completed'}</label>
                <div class="checkbox">
                    <label><input name="val[check_complete]" type="checkbox" {value type='checkbox' id='check_complete' default='1'}>{phrase var='profilecompleteness.not_show_the_widget_when_100_completed'}</label>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">{phrase var='profilecompleteness.save_changes'}</button>
        </div>
    </div>
</form>

{literal}
<script>
    $Behavior.profile_completeness_init_colorpicker = function () {
        var form_id = '#js_profile_compeleness_settings';
        // Colorpicker
        $(form_id + ' ._colorpicker:not(.c_built)').each(function () {
            var t = $(this),
                h = t.parent().find('._colorpicker_holder');

            t.addClass('c_built');
            h.css('background-color', t.val());

            h.colpick({
                layout: 'hex',
                submit: false,
                onChange: function (hsb, hex, rgb, el, bySetColor) {
                    t.val('#' + hex);
                    h.css('background-color', '#' + hex);
                    t.trigger('change');
                },
                onHide: function () {
                    t.trigger('change');
                }
            });

            var cal_of_h = $('div.colpick#' + h.data('colpickId'));
            if (cal_of_h.hasClass('dont-unbind-children') === false) {
                cal_of_h.addClass('dont-unbind-children');
            }
        });
    }
</script>
{/literal}
