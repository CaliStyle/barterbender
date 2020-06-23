{if isset($glat) && isset($glong)}
    {literal}
        <script type="text/javascript">
            $Behavior.updateGlatGlong = function()
            {
                if(ynfeIndexPage.glat > 0 && ynfeIndexPage.glong > 0){
                } else {
                    ynfeIndexPage.glat = {/literal}{$glat}{literal};
                    ynfeIndexPage.glong = {/literal}{$glong}{literal};
                }
            }
        </script>
    {/literal}
{/if}
<div id="js_ync_search_wrapper" class="" >
    <div id="js_ync_search_result" class="hide item_is_active_holder item_selection_active ync-advance-search-button">
        <a id="js_ync_enable_adv_search_btn" data-callback-js="ynfeIndexPage.getVisitorLocation();" href="javascript:void(0)" onclick="ync_core.yncEnableAdvSearch();return false;">
            <i class="ico ico-dottedmore-o"></i>
        </a>
    </div>
</div>
<div id="js_ync_adv_search_wrapper" class="ync-advance-search-form" style="display: none;">
    <form id="event_search_form" method="post" onsubmit="$Core.remakePostUrl();">
        <input type="hidden" value="{$sView}" name="view">
        <input type="hidden" value="1" name="search[submit]">
        <input type="hidden" value="{if $bIsAdvSearch}1{else}0{/if}" id="js_adv_search_value"/>
        <div class="form-group">
            <label>{_p var='fevent.advs_address'}:</label>
            <div>
                <input id="search_address" value="{$sAddress}" type="text" name="search[address]" class="form-control search_keyword">
            </div>
        </div>
        <div class="form-group">
            <label>{_p var='fevent.city'}:</label>
            <div>
                <input id="search_city" type="text" value="{if isset($sCity)}{$sCity}{/if}" name="search[city]" class="form-control search_keyword">
            </div>
        </div>
        <div class="form-group">
            <label>{_p var='fevent.zip_postal_code'}:</label>
            <div>
                <input id="search_zipcode" type="text" value="{if isset($iZipcode)}{$iZipcode}{/if}" name="search[zipcode]" class="form-control search_keyword">
            </div>
        </div>
        
        <div class="form-group">
            <label>{_p var='fevent.country'}:</label>
            <div >
                {select_location}
                {module name='core.country-child'}
            </div>
        </div>
			
        <div class="form-group">
            <label>{_p var='fevent.range'}:</label>
            <div class="form-inline">
                <div class="form-group">
                    <input id="search_range_value_from" type="text" value="{if isset($rangevaluefrom)}{$rangevaluefrom}{/if}" name="search[range_value_from]" class="form-control search_keyword">
                </div>

                <div class="form-group">
                    <select id="search_range_type" class="form-control">
                        <option value="0">{_p var='fevent.miles'}</option>
                        <option value="1" {if isset($rangetype) && $rangetype==1}selected{/if}>{_p var='fevent.km'}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div><a id="editYourCurrentLocation" href="#" onclick="ynfeIndexPage.editYourCurrentLocationClick(); return false;" style="display: none;" >{_p var='fevent.advs_edit_location'}</a></div>
        </div>

        <div class="form-group clearfix advance_search_form_button">
            <div class="pull-left">
                <span class="advance_search_dismiss" onclick="ync_core.yncEnableAdvSearch(); return false;">
                    <i class="ico ico-close"></i>
                </span>
            </div>
            <div class="pull-right">
                <a class="btn btn-default btn-sm" href="javascript:void(0);" id="js_ync_search_reset">{_p var='reset'}</a>
                <button name="search[submit]" class="btn btn-primary ml-1 btn-sm" ><i class="ico ico-search-o mr-1"></i>{_p var='fevent.submit'}</button>
            </div>
        </div>
    </form>
</div> 
{literal}
<script type="text/javascript">
    $Ready(function(){
        if($("#js_adv_search_value").val() == 1) {
            $("input[name='search[search]']").val('{/literal}{$sSearch}{literal}');
        }
        $('#js_ync_search_reset').click(function(){
            $("input[name='search[search]']").val('');
            $("#search_address").val('');
            $("#search_city").val('');
            $("#search_zipcode").val('');
            $("#search_range_value_from").val('');
            $('#country_iso').val('');
            $('#js_country_child_id_value').val('');
        });
    });

    $Core.remakePostUrl = function(){
        var core_search = $("input[name='search[search]']").val();
        var address = $("#search_address").val();
        var city = $("#search_city").val();
        var zipcode = $("#search_zipcode").val();
        var rangevaluefrom = $("#search_range_value_from").val();
        var rangetype = $("#search_range_type").val();
        var country = $('#country_iso').val();
        var childid = $('#js_country_child_id_value').val();
        var formflag = $("#js_adv_search_value").val();

        if(country==null)
        {
            country = '';
        }
        if(childid==null)
        {
            childid = 0;
        }
       
        var url = window.location.href;
        if(url.match(/pagecalendar\/\?view=pagecalendar/g)){
            url = url.replace(/pagecalendar\/\?view=pagecalendar/g, '?');
        }
        url = url.split("?")[0] + '/';

        url = url.replace(/\/page_.*?\//g, '/');
        url = url.replace(/\/date_.*?\//g, '/');
        url = url.replace(/\/date_.*?\/.*/g, '/');
        url = url.replace(/\/\?date=.*?\/.*/g, '/');
        if(url.match(/\/search_.*?\//g))
        {
            url = url.replace(/\/search_.*?\//g, '/search_'+core_search+'/');
        }
        else
        {
            url += 'search_'+core_search+'/';
        }
        if(url.match(/\/address_.*?\//g))
        {
            url = url.replace(/\/address_.*?\//g, '/address_'+address+'/');
        }
        else
        {
            url += 'address_'+address+'/';
        }
        if(url.match(/\/city_.*?\//g))
        {
            url = url.replace(/\/city_.*?\//g, '/city_'+city+'/');
        }
        else
        {
            url += 'city_'+city+'/';
        }
        if(url.match(/\/zipcode_.*?\//g))
        {
            url = url.replace(/\/zipcode_.*?\//g, '/zipcode_'+zipcode+'/');
        }
        else
        {
            url += 'zipcode_'+zipcode+'/';
        }
        if(url.match(/\/rangevaluefrom_.*?\//g))
        {
            url = url.replace(/\/rangevaluefrom_.*?\//g, '/rangevaluefrom_'+rangevaluefrom+'/');
            if(undefined != ynfeIndexPage.glat && null != ynfeIndexPage.glat
                && undefined != ynfeIndexPage.glong && null != ynfeIndexPage.glong
                )
            {
                var glat = ynfeIndexPage.ynfe_base64_encode(ynfeIndexPage.glat.toString());
                var glong = ynfeIndexPage.ynfe_base64_encode(ynfeIndexPage.glong.toString());

                url = url.replace(/\/glat_.*?\//g, '/glat_'+glat+'/');
                url = url.replace(/\/glong_.*?\//g, '/glong_'+glong+'/');
            }
        }
        else
        {
            url += 'rangevaluefrom_'+rangevaluefrom+'/';
            if(undefined != ynfeIndexPage.glat && null != ynfeIndexPage.glat
                && undefined != ynfeIndexPage.glong && null != ynfeIndexPage.glong
                )
            {
                var glat = ynfeIndexPage.ynfe_base64_encode(ynfeIndexPage.glat.toString());
                var glong = ynfeIndexPage.ynfe_base64_encode(ynfeIndexPage.glong.toString());

                url += 'glat_'+glat+'/';
                url += 'glong_'+glong+'/';        
            }
        }

         if(url.match(/\/rangetype_.*?\//g))
        {
            url = url.replace(/\/rangetype_.*?\//g, '/rangetype_'+rangetype+'/');
        }
        else
        {
            url += 'rangetype_'+rangetype+'/';
        }
        if(url.match(/\/country_.*?\//g))
        {
            url = url.replace(/\/country_.*?\//g, '/country_'+country+'/');
        }
        else
        {
            url += 'country_'+country+'/';
        }
        if(url.match(/\/childid_.*?\//g))
        {
            url = url.replace(/\/childid_.*?\//g, '/childid_'+childid+'/');
        }
        else
        {
            url += 'childid_'+childid+'/';
        }

        url += 'formflag_' + formflag + '/' + 'search-id_1/';
        $("#event_search_form").attr('action', url);
    }
</script>
{/literal}