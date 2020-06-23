{$sJs}
{foreach from=$aFiles item=file}
<script type="text/javascript" src="{$file}"></script>
{/foreach}
{literal}
<style>
    .feed_sort_order{
        display: none !important;
    }
    #js_main_feed_holder{
        display: none;
    }
    #js_feed_content{
        display: none;
    }
    #js_block_border_feed_display .title{
        display: none;
    }
    .ym-feed-header{
        display: none;
    }
    .yndirectory-suggested-block {
        display: none !important;
    }

</style>
<script type="text/javascript">
    $(document).ready(function() {
        yndirectory.loadAjaxMapStaticImage($('#yndirectory_detail_business_id').val());
        window.onload = function () {
            window.print();
            setTimeout(function(){window.close();}, 1);
        }
    });

</script>
{/literal}
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={$apiKey}&v=3.exp&libraries=places"></script>
<link rel="stylesheet" type="text/css" href="{$core_url}PF.Base/module/directory/static/css/default/default/main.css" />
<link rel="stylesheet" type="text/css" href="{$core_url}PF.Base/theme/frontend/default/style/default/css/font-awesome/css/font-awesome.min.css" />
<div id="yndirectory_detail" class="main_break yndirectory_detail_theme_{$aYnDirectoryDetail.aBusiness.theme_id}">
    <input type="hidden" value="{$aYnDirectoryDetail.aBusiness.business_id}" id="yndirectory_detail_business_id" name="yndirectory_detail_business_id" />
    <input type="hidden" value="detail" id="yndirectory_pagename" name="yndirectory_pagename" />
    <input type="hidden" value="data" id="yndirectory_detail_data" name="yndirectory_detail_data"
           data-sdetailurl="{$aYnDirectoryDetail.sDetailUrl}"
           data-businessname="{$aYnDirectoryDetail.aBusiness.name}"
           data-businesslocationaddress="{$aYnDirectoryDetail.aBusiness.location_address}"
           data-businessid="{$aYnDirectoryDetail.aBusiness.business_id}"
           data-sdownloadbusinessurl="{$aYnDirectoryDetail.sDownloadBusinessUrl}"
    />
    <input type="hidden" name="yndirectory_business_item_id" id="yndirectory_business_item_id" value="{$aYnDirectoryDetail.aBusiness.business_id}">
    <input type="hidden" name="yndirectory_business_isliked" id="yndirectory_business_isliked" value="{$aYnDirectoryDetail.aBusiness.isLiked}">
    <input type="hidden" name="yndirectory_displayLikeButtonTheme2" id="yndirectory_displayLikeButtonTheme2" value="{if ($aYnDirectoryDetail.aBusiness.business_status == (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}0{else}{$aYnDirectoryDetail.aBusiness.displayLikeButtonTheme2}{/if}">
    <input type="hidden" name="yndirectory_can_manage_businesss" id="yndirectory_can_manage_businesss" value="{$aYnDirectoryDetail.aBusiness.canManageDashBoard}">
    <input type="hidden" name="yndirectory_can_close_business" id="yndirectory_can_close_business" value="{$aYnDirectoryDetail.aBusiness.canCloseBusiness}">
    <input type="hidden" name="yndirectory_can_open_business" id="yndirectory_can_open_business" value="{$aYnDirectoryDetail.aBusiness.canOpenBusiness}">
    <input type="hidden" name="yndirectory_can_publish_business" id="yndirectory_can_publish_business" value="{$aYnDirectoryDetail.aBusiness.isDraft}">
    <input type="hidden" name="yndirectory_can_transfer_businesss" id="yndirectory_can_transfer_businesss" value="{$aYnDirectoryDetail.aBusiness.canTransferOwner}">
    <input type="hidden" name="yndirectory_manage_businesss_link" id="yndirectory_manage_businesss_link" value="{$aYnDirectoryDetail.aBusiness.linkBusinessDashBoard}">
    <input type="hidden" name="yndirectory_makepayment_businesss_link" id="yndirectory_makepayment_businesss_link" value="{url link='directory.manage-packages.id_'.$aYnDirectoryDetail.aBusiness.business_id}">
    <input type="hidden" name="yndirectory_is_claiming_draft" id="yndirectory_is_claiming_draft" value="{$aYnDirectoryDetail.aBusiness.isClaimingDraft}">
    <input type="hidden" name="yndirectory_is_print_page" id="yndirectory_is_print_page" value="{if isset($aYnDirectoryDetail.isPrintPage)}{$aYnDirectoryDetail.isPrintPage}{else}0{/if}">

    <div id="yndirectory_detail_header">
        <div class="yndirectory-detail-header">
            <h1>
                <a>{$aYnDirectoryDetail.aBusiness.name}</a>
            </h1>
        </div>
        {if !empty($aYnDirectoryDetail.aBusiness.location_address)}
        <div class="yndirectory-detail-header-location"><i class="fa fa-map-marker"></i>
            {$aYnDirectoryDetail.aBusiness.location_address}
        </div>
        {/if}
    </div>
    {if isset($iCustomPage) && $iCustomPage}
    {module name='directory.detailcustompage' aYnDirectoryDetail=$aYnDirectoryDetail}
    {else}
    {if 'overview' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailoverview' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'aboutus' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailaboutus' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'activities' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailactivities' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'members' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailmembers' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'followers' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailfollowers' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'reviews' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailreviews' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'photos' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailphotos' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'videos' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailvideos' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'musics' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailmusics' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'blogs' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailblogs' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'discussion' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detaildiscussion' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'coupons' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailcoupons' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'polls' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailpolls' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'events' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailevents' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'jobs' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailjobs' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'marketplace' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailmarketplace' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'contactus' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailcontactus' aYnDirectoryDetail=$aYnDirectoryDetail}

    {elseif 'faq' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailfaq' aYnDirectoryDetail=$aYnDirectoryDetail}
    {elseif 'v' == $aYnDirectoryDetail.firstpage}
    {module name='directory.detailcorevideos' aYnDirectoryDetail=$aYnDirectoryDetail}
    {elseif 1 == 0 }

    {/if}

    {/if}
</div>
