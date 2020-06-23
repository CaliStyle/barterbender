<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/12/16
 * Time: 2:22 PM
 */
?>

<div class="yns adv-search-block" id ="ynsocialstore_adv_search" {if !$bIsAdvSearch }style="display:none;"{else}style="display:block; margin-bottom: 10px;"{/if}>
    <form onsubmit="checkOnSubmit()" id="ynsocialstore_adv_search_form" method="get" action="{$sFormUrl}">
        <input type="hidden" id="flag_advancedsearch" {if $bIsAdvSearch }value="1"{/if} name="flag_advancedsearch"/>
        <input type="hidden" id="sort" name="sort" value="{if isset($aSearch.sort)}{$aSearch.sort}{/if}">
        <input type="hidden" id="when" name="when" value="{if isset($aSearch.when)}{$aSearch.when}{/if}">
        <input type="hidden" id="show" name="show" value="{if isset($aSearch.show)}{$aSearch.show}{/if}">
        <input type="hidden" id="keywords" name="keywords" value="">
        <div class="row">
            <div class="col-sm-6 col-xs-12 ynstore-paddingright-5">
                <div class="form-group">
                    <div class="ynsocialstore-location">
                        <div class="input-group">
                            <input class="form-control" id="ynsocialstore_location" type="search" data-inputid="fulladdress" name="val[location_fulladdress]" size="30" value="{if isset($aForms.location_address)}{$aForms.location_address}{/if}" placeholder="{_p var='ynsocialstore.enter_a_location'}" autocomplete="off">
                            <input type="hidden" data-inputid="address" name="search[location_address]" value="{if isset($aForms.location_address)}{$aForms.location_address}{/if}" />
                            <input type="hidden" data-inputid="lat" name="search[location_address_lat]" value="{if isset($aForms.location_address_lat)}{$aForms.location_address_lat}{/if}" />
                            <input type="hidden" data-inputid="lng" name="search[location_address_lng]" value="{if isset($aForms.location_address_lng)}{$aForms.location_address_lng}{/if}" />
                            <a class="btn input-group-addon" onclick="ynsocialstore.getIndexCurrentPosition();"><i class="ico ico-checkin"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xs-12 ynstore-paddingleft-5">
                <div class="form-group">
                    <input type="text" name="search[radius]" value="{if isset($aForms.radius)}{$aForms.radius}{/if}" placeholder="{_p var='ynsocialstore.radius_'.$DMU}" class="form-control" id="radius" />
                </div>
            </div>
        </div>

        <div class="ynstore-btn-block">
            <a class="ynstore-btn-close" onclick="ynsocialstore.advSearchDisplay(); return false;" href="javascript:void(0)">
                <i class="ico ico-close" aria-hidden="true"></i>{_p var='ynsocialstore.close'}
            </a>

            <div class="pull-right">
                <input type="submit" id="filter_submit" name="search[submit]" value="{_p var='ynsocialstore.search'}" class="btn btn-primary"/>
                <input type="button" onclick="clearInputSearchStore()" id="filter_submit" name="search[reset]" value="{_p var='ynsocialstore.reset'}" class="btn btn-default"/>
            </div>
        </div>
    </form>
</div>
{literal}
<script type="text/javascript">
    function clearInputSearchStore() {
        $('#page_ynsocialstore_store_index #ynsocialstore_adv_search input[type=text]').val('');
        $('#page_ynsocialstore_store_index #ynsocialstore_adv_search select').val(0);
        $('#page_ynsocialstore_store_index #form_main_search input[type=search]').val('');
    }
    function checkOnSubmit()
    {
        if ($(".header_bar_search input[name='search[search]']").length > 0){
            var val = $(".header_bar_search input[name='search[search]']").val();
            val = escape(val);
            $('#ynsocialstore_adv_search_form #keywords').val(val);
        }

        return true;``
    }
    $Behavior.store_advanced_search = function() {
        if($('#form_main_search').length) {
            if(!$('#ynsocialstore_adv_search').hasClass('init')) {
                let advsearchObject = $('#ynsocialstore_adv_search');
                let parent = advsearchObject.closest('._block_content');
                advsearchObject.detach().prependTo(parent.get(0));
                $('#ynsocialstore_adv_search').addClass('init');
            }

            let parent = $('#form_main_search').find('.hidden');
            if(parent.find('input[name="flag_advancedsearch"]').length) {
                let value = $('#ynsocialstore_adv_search').find('#ynsocialstore_location').val();
                parent.find('input[type="hidden"][name="val[location_fulladdress]"]').val(value);
                parent.find('input[type="hidden"][name="search[location_address]"]').val(value);
            }
        }
    }
</script>
{/literal}
