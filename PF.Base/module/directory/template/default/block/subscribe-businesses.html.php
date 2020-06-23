<form method="post" action="" id="yndirectory_subscribe" class="dont-unbind-children">
    <input type="hidden" name="val[subscribe]" value="1" />

    <div class="form-group">
        <div class="dropdown subscribe-categories" id="subscribe_categories">
            <span class="dropdown-toggle d-block form-control cursor-point" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><p class="subscribe-categories__text">{_p var='directory.select_category'}</p> <i class="ico ico-caret-down subscribe-categories__icon"></i></span>
            <div class="dropdown-menu dropdown-menu-right yn-dropdown-not-hide">
                {foreach from=$aCategories item=aCategory }
                    {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                        <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
                    {else}
                        {assign var='value_name' value=$aCategory.title|convert}
                    {/if}
                    <li class="subscribe-categories__item ml--2 mr--2 pl-2 pr-2">
                        <label class="mb-0 d-block fw-normal cursor-point pt-1 pb-1 subscribe-categories__label">
                            <input type="checkbox" name="val[subscribe_categories]" class="category_checkbox" value="{$aCategory.category_id}">
                            <i class="ico ico-square-o"></i><span>{$value_name}</span>
                        </label>
                    </li>
                    {if isset($aCategory.sub) && count($aCategory.sub)}
                        {foreach from=$aCategory.sub item=aSubCategory }
                        {if Phpfox::isPhrase($this->_aVars['aSubCategory']['title'])}
                            <?php $this->_aVars['value_name'] = _p($this->_aVars['aSubCategory']['title']) ?>
                        {else}
                            {assign var='value_name' value=$aSubCategory.title|convert}
                        {/if}
                        <li class="subscribe-categories__item ml--2 mr--2 pl-2 pr-2 sub">
                            <label class="mb-0 d-block fw-normal cursor-point pt-1 pb-1 subscribe-categories__label">
                                <input type="checkbox" name="val[subscribe_categories]" class="category_checkbox" value="{$aSubCategory.category_id}">
                                <i class="ico ico-square-o"></i><span>{$value_name}</span>
                            </label>
                        </li>
                        {/foreach}
                    {/if}
                {/foreach}
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="input-group">
            <input class="form-control" type="text" name="val[subscribe_location]"  id="yndirectory_subscribeblock_location" value="{value type='input' id='yndirectory_subscribeblock_location'}" />
            <span id="yndirectory_checkin" class="input-group-addon" onclick="yndirectory.getCurrentPositionForBlock('subscribe');"><i class="ico ico-checkin-o"></i></span>
        </div>
        <input type="hidden" data-inputid="subscribe_location_address" id="subscribe_location_address" name="val[subscribe_location_address]" value="{value type='input' id='subscribe_location_address'}">
        <input type="hidden" data-inputid="subscribe_location_address_lat" id="subscribe_location_address_lat" name="val[subscribe_location_address_lat]" value="{value type='input' id='subscribe_location_address_lat'}">
        <input type="hidden" data-inputid="subscribe_location_address_lng" id="subscribe_location_address_lng" name="val[subscribe_location_address_lng]" value="{value type='input' id='subscribe_location_address_lng'}">            
    </div>

    <div class="form-group">
        <input class="form-control" type="text" name="val[subscribe_radius]"  id="subscribe_radius" placeholder="{phrase var='radius_mile'}" />
    </div>

    <div class="form-group">
        <input class="form-control" type="text" name="val[email]"  id="subscribe_email" value="{if isset($aEmail)}{$aEmail}{/if}" placeholder="{phrase var='email'}"/>
    </div>

    <div class="main_search_browse_button">
        <button type="button" value="{phrase var='subscribe'}" class="btn btn-sm btn-primary" id="yndirectory_subscribeblock_submit" onclick="return submitSubscribeForm();">{phrase var='subscribe'}</button>
    </div>
</form>

{literal}
    <script type="text/javascript">
        $Behavior.readyYnDirectorySubscribeBlock = function() 
        {
            if(!$('#yndirectory_subscribe').hasClass('init')) {
                yndirectory.initSubscribeBlock();
                $('#yndirectory_subscribe').addClass('init');
            }
        };
        var submitSubscribeForm = function(){
            var categories = [];
            $.each($('.category_checkbox:checked'), function(key, el) {
                categories.push($(el).val());
            })
            $.ajaxCall('directory.subscribeBusiness', 'email=' +$('#subscribe_email').val() +
                                                      '&categories=' +categories.join(',') +
                                                      '&location_lat=' +$('#subscribe_location_address_lat').val() +
                                                      '&location_lng=' +$('#subscribe_location_address_lng').val() +
                                                      '&radius=' +$('#subscribe_radius').val()
                                                      );
            return false;
        };
    </script>
{/literal}