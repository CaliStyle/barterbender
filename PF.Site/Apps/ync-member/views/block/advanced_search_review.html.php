<div id="ynmember_adv_search" {if !$bIsAdvSearch }style="display:none;"{else}style="display:block;"{/if}>
<form id="ynmember_search_form" method="GET" action="{url link='current' isAdvSearch=true}">
    <input type="hidden" value="1" name="search[submit]">
    <input type="hidden" id="form_flag" name="search[form_flag]" {if !$bIsAdvSearch }value="0"{else}value="1"{/if}>

    <div class="form-group">
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                {filter key='reviewer'}
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
                {filter key='rating'}
            </div>
        </div>
    </div>

    <div class="form-group space-bottom">
        <div class="text-right">
            <button name="search[submit]" value="{phrase var='Submit'}" class="btn btn-sm btn-primary" type="submit">{_p var="Search"}</button>
            <button name="search[reset]" value="{phrase var='Reset'}" class="btn btn-sm btn-default" type="reset" onclick="window.location.href='{url link=\'ynmember.review\'}'">{_p var="Reset"}</button>
        </div>
    </div>
</form>
</div>