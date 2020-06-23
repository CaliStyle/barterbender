<div class="yncaffiliate_link_tracking">
    {if !PHPFOX_IS_AJAX}
    <form id="ynaf_link_tracking_search" action="{url link='affiliate.link-tracking'}" method="GET">
        <input type="hidden" id="is_init_form" value="0">
        <div class="yncaffiliate_search_form form-inline">
            <div class="yncaffiliate_search_form_inner clearfix">
                <div class="form-group padding">
                    <label>{_p('from_date')}</label>
                    <div class="form-inline clearfix sm_padding_parent">
                        <div class="form-group yncaffiliate_datetime_picker_parent sm_padding">
                            {select_date prefix='start_time_' id='_from' start_year='-10' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true }
                        </div>
                    </div>
                </div>
                <div class="form-group padding">
                    <label>{_p('to_date')}</label>
                    <div class="form-inline clearfix sm_padding_parent">
                        <div class="form-group yncaffiliate_datetime_picker_parent sm_padding">
                            {select_date prefix='end_time_' id='_end_time' start_year='-10' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true }
                        </div>
                    </div>
                </div>
                <div class="form-group padding">
                    <label class="notext"></label>
                    <div class="form-inline text-left">
                        <button type="submit" class="btn btn-primary fw-bold">{_p var='Search'}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <strong>
        {$iCount} {_p var='url_s'}
    </strong>
    {/if}
    {if count($aTrackings)}
        {if !PHPFOX_IS_AJAX}
        <div id="tableLinkTracking" class="table-responsive">
            <table class="table table-bordered yncaffiliate_table">
                <thead>
                    <tr>
                        <th>{_p('referring_urls')}</th>
                        <th>{_p('click')}</th>
                        <th>{_p('successful_registration')}</th>
                        <th>{_p('date')}</th>
                    </tr>
                </thead>
            {else}
                <table id="page2" style="display: none" class="table table-bordered yncaffiliate_table">
            {/if}
                <tbody>
                    {foreach from=$aTrackings item=aTracking key=iKey}
                    <tr>
                        <td>{$aTracking.target_url}</td>
                        <td>{$aTracking.total_click}</td>
                        <td>{$aTracking.total_success}</td>
                        <td>{$aTracking.last_click|date:'core.extended_global_time_stamp'}</td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
                {pager}
        {if !PHPFOX_IS_AJAX}
        </div>
        {/if}
    {else}
        {if !PHPFOX_IS_AJAX}
            <div class="p_4">
                {_p var='no_tracking_found'}
            </div>
        {/if}
    {/if}
</div>

{literal}
<script language="JavaScript" type="text/javascript">
    $Behavior.loadMoreLinkTracking = function () {
        if ($('#page2').length > 0 && $('#page2 tbody').length > 0 && $('#tableLinkTracking tbody').length > 0)
        {
            $('#tableLinkTracking tbody').append($('#page2 tbody').html());
            $('#page2').remove();
        }
    }
</script>
{/literal}