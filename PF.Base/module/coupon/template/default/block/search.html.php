<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
<script type="text/javascript">

    $Behavior.initCouponSearch = function() {
        var parent = '';
        if ($("body[id^='page_route_coupon']").length)
            parent = "body[id^='page_route_coupon']";
        else if ($("body[id^='page_coupon_index']").length)
            parent = "body[id^='page_coupon_index']";

        if (parent && $('#form_main_search') && $(parent).length && $('#js_coupon_search_wrapper') && $('#form_main_search').find('#js_coupon_search_wrapper').length == 0) {
            if ($('.header_bar_menu').find('.header_filter_holder').length == 0) {
                $("#js_coupon_search_wrapper").detach().appendTo(parent + ' .header-filter-holder');
                $("#js_coupon_search_wrapper").addClass("filter-options").show();
            }
            else {
                $("#js_coupon_search_wrapper").detach().appendTo(parent + ' .header_filter_holder');
                $("#js_coupon_search_wrapper").addClass("inline-block").show();
            }

            $("#yncp_advsearch").insertBefore('.location_2').removeClass('hide');
            if ($('#js_adv_search_value').val() == '1') {
                $("#yncp_advsearch").show();
                $('#js_forum_search_result').find('i').removeClass('fa fa-caret-down').addClass('fa fa-caret-up');
            } else {
                $("#yncp_advsearch").hide();
                $('#js_forum_search_result').find('i').removeClass('fa fa-caret-up').addClass('fa fa-caret-down');
            }
        }
    };

    function couponEnableAdvSearch(obj) {
        if ($('#js_adv_search_value').val() == '0' || $('#js_adv_search_value').val() == '') {
            $('#js_adv_search_value').val(1);
            $("#yncp_advsearch").slideDown();
            $('#js_forum_search_result').find('i').removeClass('fa fa-caret-down').addClass('fa fa-caret-up');
        }
        else {
            $("#yncp_advsearch").slideUp();
            $('#js_adv_search_value').val(0);
            $('#js_forum_search_result').find('i').removeClass('fa fa-caret-up').addClass('fa fa-caret-down');
        }
    }

    function checkSearchProductSubmit()
    {
        if ($("#form_main_search input[name='search[search]']").length > 0){
            var val = $("#form_main_search input[name='search[search]']").val();
            $('#js_coupon_adv_search_wrapper #coupon_input_core_search').val(val);
        }

        return true;
    }
</script>
{/literal}
<div id="js_coupon_search_wrapper" style="display: none">
    <input type="hidden" value="{if !empty($aForms) && count($aForms) > 1}1{/if}" id="js_adv_search_value" name="search[adv_search]"/>
    <a href="javascript:void(0)" class="btn btn-default dropdown-toggle" onclick="couponEnableAdvSearch(this);return false;">
        {_p var='advanced_search'} <span class="ico ico-caret-down"></span>
    </a>
</div>
<form method="post" action="{url link='coupon.search' search-id=1}" id="yncp_advsearch" onsubmit="return checkSearchProductSubmit()" class="colapse hide">
    <input type="hidden" name="search[advsearch]" value="1" />
        <input type="hidden" name="search[search]" id="coupon_input_core_search" value="{value type='input' id='search'}">

    <div class="form-group">
        <label for="">{phrase var='key_word'}:</label>
        <input type="text" class="form-control" name="search[keyword]" value="{value type='input' id='keyword'}" id="keyword" />
    </div>

    <div class="form-group">
        <label for="">{phrase var='category'}:</label>
        {$sCategories}
    </div>

    <div class="form-group">
        <label for="">{phrase var='city'}:</label>
        <input type="text" class="form-control" name="search[city]" value="{value type='input' id='city'}" id="city" />
    </div>

    <div class="form-group search_list">
        <label for="">{phrase var='country'}:</label>
        { $sCountries }
        { module name='core.country-child'}
    </div>

    <div class="t_right">
        <input type="submit" value="{phrase var='search'}" class="btn btn-primary" />
        <button type="reset" class="btn btn-default" onclick="couponEnableAdvSearch(this)" >{_p var="Cancel"}</button>
    </div>
</form>
