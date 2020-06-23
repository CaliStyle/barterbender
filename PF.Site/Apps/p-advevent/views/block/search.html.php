{if isset($glat) && isset($glong)}
    {literal}
        <script type="text/javascript">
            $Behavior.updateGlatGlong = function()
            {
                if(ynfeIndexPage.glat > 0 && ynfeIndexPage.glong > 0){
                } else {
                    ynfeIndexPage.glat = {/literal}{$aForms.glat}{literal};
                    ynfeIndexPage.glong = {/literal}{$aForms.glong}{literal};
                }
            }
        </script>
    {/literal}
{/if}
<div id="" class="js_p_search_wrapper" >
    <div  class=" js_p_search_result hide item_is_active_holder item_selection_active p-advance-search-button">
        <a class="js_p_enable_adv_search_btn" href="javascript:void(0)" onclick="p_core.pEnableAdvSearch();return false;">
            <i class="ico ico-dottedmore-o"></i>
        </a>
    </div>
</div>
<div class="js_p_adv_search_wrapper p-advance-search-form p-fevent-search-wrapper" style="display: none">
        <div id="core_js_messages" class="mb-3"></div>
        <input type="hidden" name="search[advsearch]" id="js_advsearch_flag" value="{value type='input' id='advsearch'}"/>
        <input type="hidden" name="search[glat]" value="{value type='input' id='glat'}" id="js_advsearch_glat">
        <input type="hidden" name="search[glong]" value="{value type='input' id='glong'}" id="js_advsearch_glong">

        <div class="p-fevent-search-formgroup-wrapper dont-unbind-children">
            <div class="form-group">
                <label>{_p var='fevent.v_locationvenue'}</label>
                {location_input}
            </div>

            <div class="form-group">
                <label>{_p var='fevent.range'}</label>
                <div class="input-group input-group-dropdown">
                    <input placeholder="0.00" id="search_range_value_from" type="text" value="{value type='input' id='rangevaluefrom'}" name="search[rangevaluefrom]" class="form-control search_keyword">
                    <div class="input-group-btn dropdown">
                        <select class="w-auto btn dropdown-toggle" name="search[rangetype]" id="search_range_type">
                            <option value="0">{_p var='fevent.miles'}</option>
                            <option value="1" {value type='select' id='rangetype' default=1}>{_p var='fevent.km'}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group js_core_init_selectize_form_group">
                <label>{_p var='fevent.country'}</label>
                <div >
                    {select_location}
                </div>
            </div>
            <div class="p-daterangepicker-form-group form-group">
                <label>{_p var='time'}</label>
                <div>
                    <input type="hidden" value="{value type='input' id='start_time'}" id="js_p_start_time" name="search[stime]">
                    <input type="hidden" value="{value type='input' id='end_time'}" id="js_p_end_time" name="search[etime]">
                    <input type="hidden" id="js_time_type" value="{value type='input' id='time_type'}" name="search[time_type]">
                    <input type="text" id="js_time_text" class="form-control" value="{if !empty($aForms.time)}{value type='input' id='time'}{else}{_p var='all'}{/if}" readonly >
                </div>
            </div>

            <div class="form-group js_core_init_selectize_form_group">
                <label>{_p var='status'}</label>
                <div>
                    <select class="form-control" id="search_status" name="search[status]">
                        <option value="">{_p var='all'}</option>
                        {foreach from=$statusArray key=status_text item=status_value}
                        <option value="{$status_value}" {value type='select' id='status' default=$status_value}>{$status_text}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group clearfix advance_search_form_button p-fevent-advance-search-form-button">
            <div class="pull-right">
                <a class="btn btn-default btn-sm" href="javascript:void(0);" id="js_p_search_reset" onclick="eventAdvSearch.resetForm(); return false;">{_p var='reset'}</a>
                <button class="btn btn-primary ml-1 btn-sm" onclick="return eventAdvSearch.submitForm(this);">{_p var='search'}</button>
                {if Phpfox::VERSION >= '4.8.0'}
                <a class="btn btn-primary ml-1 btn-sm" onclick="searchOnGoogleMapView(this);" attr-href="{url link='fevent.map' type=fevent view=all}" id="search_and_view_on_the_map">{_p var='search_and_view_on_the_map'}</a>
                {/if}
            </div>
            <div class="pull-left">
                <span class="advance_search_dismiss" onclick="p_core.pEnableAdvSearch(); return false;">
                    <i class="ico ico-close"></i>
                </span>
            </div>
        </div>
</div>
{literal}
<script type="text/javascript">
    $Behavior.initFeventDaterangepicker = function(){
        let params = {
            parent : '.p-daterangepicker-form-group',
            default_range_key: '{/literal}{_p var='all'}{literal}',
            time_type_default: '{/literal}{$aForms.time_type}{literal}',
            ranges: {
                '{/literal}{_p var='all'}{literal}' : [moment(), moment()],
                '{/literal}{_p var='today'}{literal}' : [moment(), moment()],
                '{/literal}{_p var='tomorrow'}{literal}' : [moment().add(1,'days'), moment().add(1,'days')],
                '{/literal}{_p var='this_week'}{literal}' : [moment().startOf('isoWeek'), moment().endOf('isoWeek')],
                '{/literal}{_p var='advevent_next_week'}{literal}' : [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks').endOf('isoWeek')],
                '{/literal}{_p var='this_month'}{literal}' : [moment().startOf('month'), moment().endOf('month')]
            },
            'custom_range' : ['{/literal}{$aForms.custom_start_time}{literal}','{/literal}{$aForms.custom_end_time}{literal}'],
            'custom_range_label' : '{/literal}{_p var='advevent_choose_date_lowercase'}{literal}'
        }
        eventAdvSearch.advEventRangeTime.setDefaultParams(params);
        eventAdvSearch.advEventRangeTime.init();
        eventAdvSearch.defaultCountryIso = '{/literal}{$defaultCountry}{literal}'
        let isCoreSearch = {/literal}parseInt({$isCoreSearch}){literal};
        if(isCoreSearch) {
            eventAdvSearch.resetForm();
        }
    }

    $Behavior.initInputListener = function(){
        $('#search_range_value_from').on('input',function(e){
            var range = e.target.value;
            if (range){
                $('#search_and_view_on_the_map').addClass('disabled');
            } else {
                $('#search_and_view_on_the_map').removeClass('disabled');
            }
        });
    }

    searchOnGoogleMapView = function(obj){
        var google_map_url = $(obj).attr('attr-href');
        var keyword = $('.form-control[name="search[search]"]').val();
        var url = google_map_url;
        url += '&' + encodeURIComponent('search[search]') + '=' + encodeURIComponent(keyword);
        if (!$('#search_and_view_on_the_map').hasClass('disabled')){
            window.location.href = url;
        }
    }
</script>
{/literal}