<div id="js_ync_search_wrapper" class="" >
    <div id="js_ync_search_result" class="hide item_is_active_holder item_selection_active ync-advance-search-button">
        <a id="js_ync_enable_adv_search_btn" href="javascript:void(0)" onclick="ync_core.yncEnableAdvSearch();return false;">
            <i class="ico ico-dottedmore-o"></i>
        </a>
    </div>
</div>
<div id="js_ync_adv_search_wrapper" class="ync-advance-search-form" style="display: none;">
    <form method="get" action="" onsubmit="return checkOnSearchSubmitForm();" id="yndirectory_advsearch">
        <input type="hidden" value="{$sView}" name="view">
        <input type="hidden" name="search[search]" value="{if isset($sSearch)}{$sSearch}{/if}" id="directory_input_core_search">
        <div class="form-group">
            <label>{phrase var='category'}:</label>
            {$sCategories}
        </div>
        <div class="form-group yndirectory-form-location">
            <label>{phrase var='location'}:</label>
            <div class="input-group">
                <input class="form-control" type="text" name="search[searchblock_location]"  id="yndirectory_searchblock_location" value="{value type='input' id='searchblock_location'}" />
                <span id="yndirectory_checkin" onclick="yndirectory.getCurrentPositionForBlock('search');" class="input-group-addon"><i class="ico ico-checkin-o"></i></span>
            </div>
            <input type="hidden" data-inputid="location_address" id="location_address" name="search[location_address]" value="{value type='input' id='location_address'}">
            <input type="hidden" data-inputid="location_address_lat" id="location_address_lat" name="search[location_address_lat]" value="{value type='input' id='location_address_lat'}">
            <input type="hidden" data-inputid="location_address_lng" id="location_address_lng" name="search[location_address_lng]" value="{value type='input' id='location_address_lng'}">
        </div>

        <div class="form-group">
            <label>{phrase var='radius_mile'}:</label>
            <input class="form-control" type="text" name="search[radius]" value="{value type='input' id='radius'}" id="radius" />
        </div>

        <div class="form-group clearfix advance_search_form_button">
            <div class="pull-left">
                <span class="advance_search_dismiss" onclick="ync_core.yncEnableAdvSearch(); return false;">
                    <i class="ico ico-close"></i>
                </span>
            </div>
            <div class="pull-right">
                <a class="btn btn-default btn-sm" href="javascript:void(0);" id="js_ync_search_reset">{_p var='reset'}</a>
                <button id="yndirectory_searchblock_submit" name="search[submit]" class="btn btn-primary ml-1 btn-sm" ><i class="ico ico-search-o mr-1"></i>{_p var='directory.submit'}</button>
            </div>
        </div>
    </form>
</div>

{literal}
<script type="text/javascript">
    $Ready(function(){
        yndirectory.initSearchBlock();
        $('#js_ync_search_reset').click(function(){
            $("input[name='search[search]']").val('');
            $(".category").val('');
            $("#location_address").val('');
            $("#yndirectory_searchblock_location").val('');
            $('#radius').val('');
        });
    });

    function checkOnSearchSubmitForm() {
        var search_input = $("#form_main_search input[name='search[search]']");
        if (parseInt(search_input.length) > 0){
            var val = search_input.val();
            $('#yndirectory_advsearch #directory_input_core_search').val(val);
        }
        return true;
    }
</script>
{/literal}