<div id="js_ajax_compose_error_message"></div>
<div>
    <form method="post" action="#" id="ynmember_js_place_form" onsubmit="return false" enctype="multipart/form-data">
        {if $bIsEdit}
            <div><input type="hidden" name="val[place_id]" value="{$aForms.place_id}" /></div>
        {/if}
        {if $bRequireTitle}
        <div class="table form-group">
            <div class="table_left">
                <label for="title">{required}{_p('Name')}:</label>
            </div>
            <div class="table_right">
                <input class="form-control" type="text" name="val[location_title]" value="{value type='input' id='location_title'}" id="location_title" size="40" />
            </div>
        </div>
        {/if}

        <div class="table form-group">
            <div class="table_right input-group">
                <input class="form-control" type="text" name="val[location_address]" value="{value type='input' id='location_address'}" id="location_address" size="40" placeholder="{_p var='Enter a location'}"/>
                <div id="yndirectory_checkin" onclick="ynmember.getCurrentPosition('addplace');" class="input-group-addon">
                    <i class="fa fa-location-arrow" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <input type="hidden" name="val[location_latitude]" id="location_latitude" value="{if $bIsEdit}{$aForms.location_latitude}{/if}" >
        <input type="hidden" name="val[location_longitude]" id="location_longitude" value="{if $bIsEdit}{$aForms.location_longitude}{/if}" />

        {if $bRequireTitle}
        <div class="table form-group">
            <div>
                <input type="checkbox" name="val[current]" id="current" size="40" placeholder="" value="1"{if $bIsEdit && $aForms.current} checked{/if} />
                <label for="current">{$sCurrentMessage}</label>
            </div>
        </div>
        {/if}

        <input type="hidden" name="val[type]" id="type" value="{$sType}" />

        <div class="table_clear">
            <ul class="table_clear_button">
                <li><button type="cancel" class="button btn-default" onclick="tb_remove()">{_p var='Cancel'}</button></li>
                <li><input type="submit" name="val[{if $bIsEdit}update{else}submit{/if}]" value="{_p var='Save'}" class="button btn-primary" onclick="ynmemberAddPlace()"/></li>
            </ul>
            <div class="clear"></div>
        </div>
    </form>
</div>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={$apiKey}&v=3.exp&libraries=places&callback=initPlace"></script>

{literal}
<script>
    var requireTitle = {/literal}{$bRequireTitle}{literal};
    var ynmemberAddPlace = function(){
        var form = document.getElementById('ynmember_js_place_form'),
            message = '';

        if (requireTitle && !form.location_title.value) {
            message = '{/literal}{_p var="Please enter place title"}{literal}';
        }

        if (!form.location_address.value) {
            message = '{/literal}{_p var="Please enter location of your place"}{literal}';
        }

        if (message) {
            if ($("#js_ajax_compose_error_message"))
                $("#js_ajax_compose_error_message").html("<div class='error_message'>" + message + "</div>");
            return false;
        }

        $(form).ajaxCall('ynmember.submitPlace');
        return false;
    };

    var initPlace = function(){
        ynmember.initEditPlace('addplace');
    };
</script>
{/literal}
