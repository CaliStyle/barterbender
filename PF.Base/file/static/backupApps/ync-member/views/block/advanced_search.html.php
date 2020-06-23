<div id="ynmember_adv_search" style="display:{if !$bIsAdvSearch }none;{else}block;{/if}">
<form id="ynmember_search_form" method="GET" action="{url link='current' isAdvSearch=true}">
    <input type="hidden" value="1" name="search[submit]">
    <input type="hidden" id="form_flag" name="search[form_flag]" {if !$bIsAdvSearch }value="0"{else}value="1"{/if}>
    <input type="hidden" id="view" name="view" value="{$sView}">

{if Phpfox::getUserParam('user.can_search_user_gender')}
    <div class="form-group">
        <label>{phrase var='Gender'}:</label>
        <div >
            {filter key='gender'}
        </div>
    </div>
{/if}

{if Phpfox::getUserParam('user.can_search_user_age')}
    <div class="form-group">
        <label>{phrase var='between_ages'}:</label>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                {filter key='from'}
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
                {filter key='to'}
            </div>
        </div>
    </div>
{/if}

    <div class="form-group">
        <label>{phrase var='Location'}:</label>
        <div class="input-group">
            <input class="form-control" type="text" name="search[location]" value="" placeholder="{_p var='Enter a location'}">
            <div id="ynmember_checkin" class="input-group-addon" onclick="ynmember.getCurrentPosition('search');">
                <i class="fa fa-location-arrow" aria-hidden="true"></i>
            </div>
        </div>
    </div>
    <div class="form-group hidden">
        <div>
            {filter key='location_latitude'}
        </div>
    </div>
    <div class="form-group hidden">
        <div>
            {filter key='location_longitude'}
        </div>
    </div>

    <div class="form-group">
        <label>{phrase var='Radius (mile)'}:</label>
        <div class="row">
            <div class="col-xs-12 first">
                {filter key='within'}
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>{phrase var='City'}:</label>
        <div>
            {filter key='city'}
        </div>
    </div>

{if Phpfox::getUserParam('user.can_search_by_zip')}
    <div class="form-group">
        <label>{phrase var='Zip/Postal Code'}:</label>
        <div>
            {filter key='zip'}
        </div>
    </div>
{/if}

    <div class="form-group">
        <label>{phrase var='Country'}:</label>
        <div >
            {filter key='country'}
            {module name='core.country-child' country_child_filter=true country_child_type='browse'}
        </div>
    </div>

    <div id="js_ynmember_browse_advanced">
        <div>
            <div>
                {foreach from=$aCustomFields name=customfield item=aCustomField}
                {if isset($aCustomField.fields)}
                {template file='custom.block.foreachcustom'}
                {/if}
                {/foreach}
            </div>
        </div>
    </div>
    <div class="form-group space-bottom">
        <div class="text-right">
            <button name="search[submit]" value="{phrase var='Submit'}" class="btn btn-primary" type="submit">{_p var="Search"}</button>
            <button name="search[reset]" value="{phrase var='Reset'}" class="btn btn-default" type="reset" onclick="window.location.href='{url link=\'ynmember\' view=$sView}'">{_p var="Reset"}</button>
        </div>
    </div>
    {if isset($sCountryISO)}
    <script type="text/javascript">
        $Behavior.loadStatesAfterBrowse = function()
        {l}
        sCountryISO = "{$sCountryISO}";
        if(sCountryISO != "")
        {l}
        sCountryChildId = "{$sCountryChildId}";
        $.ajaxCall('core.getChildren', 'country_child_filter=true&country_child_type=browse&country_iso=' + sCountryISO + '&country_child_id=' + sCountryChildId);
        {r}
        {r}
    </script>
    {/if}
</form>
</div>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={$apiKey}&v=3.exp&libraries=places"></script>
{literal}
<script type="text/javascript">
    $Behavior.ynmemberInitSearch = function() {
        ynmember.initEditPlace('search');
    }
</script>
{/literal}