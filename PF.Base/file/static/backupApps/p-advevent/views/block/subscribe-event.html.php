<form method="post" action="" id="fevent_subscribe">
    <input type="hidden" name="val[subscribe]" value="1" />
    <div class="p-fevent-subscribe-container">
        <div class="item-subscribe-outer">
            <div class="item-search-list">
    <div class="form-group">
        <div class="dropdown subscribe-categories" id="subscribe_categories">
            <span class="dropdown-toggle d-block form-control cursor-point" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><p class="subscribe-categories__text">{_p var='fevent.select_categories'}</p> <i class="ico ico-caret-down subscribe-categories__icon"></i></span>
            <div class="dropdown-menu dropdown-menu-right yn-dropdown-not-hide">
                {foreach from=$aCategories item=aCategory }
                    <li class="subscribe-categories__item ml--2 mr--2 pl-2 pr-2">
                        <label class="mb-0 d-block fw-normal cursor-point pt-1 pb-1 subscribe-categories__label">
                            <input type="checkbox" name="val[subscribe_categories]" class="category_checkbox" value="{$aCategory.category_id}">
                            <i class="ico ico-square-o"></i><span>{$aCategory.name|convert|clean}</span>
                        </label>
                    </li>
                    {if isset($aCategory.sub) && count($aCategory.sub)}
                        {foreach from=$aCategory.sub item=aSubCategory }
                        <li class="subscribe-categories__item ml--2 mr--2 pl-2 pr-2 sub">
                            <label class="mb-0 d-block fw-normal cursor-point pt-1 pb-1 subscribe-categories__label">
                                <input type="checkbox" name="val[subscribe_categories]" class="category_checkbox" value="{$aSubCategory.category_id}">
                                <i class="ico ico-square-o"></i><span>{$aSubCategory.name|convert|clean}</span>
                            </label>
                        </li>
                        {/foreach}
                    {/if}
                {/foreach}
            </div>
        </div>
    </div>

    <div class="form-group ynfevent-form-location">
        <div class="input-group input-group">
            <input type="text" name="val[subscribe_location]"  id="fevent_subscribeblock_location" value="{value type='input' id='ynfevent_subscribeblock_location'}" class="form-control" aria-describedby="fevent_checkin" placeholder="{_p var='fevent.location'}" />
            <span class="input-group-addon" id="fevent_checkin" onclick="fevent.getCurrentPositionForBlock('subscribe');"><i class="ico ico-checkin-o"></i></span>
        </div>

        <input type="hidden" data-inputid="subscribe_location_address" id="subscribe_location_address" name="val[subscribe_location_address]" value="{value type='input' id='subscribe_location_address'}">
        <input type="hidden" data-inputid="subscribe_location_address_lat" id="subscribe_location_address_lat" name="val[subscribe_location_address_lat]" value="{value type='input' id='subscribe_location_address_lat'}">
        <input type="hidden" data-inputid="subscribe_location_address_lng" id="subscribe_location_address_lng" name="val[subscribe_location_address_lng]" value="{value type='input' id='subscribe_location_address_lng'}">
    </div>

    <div class="form-group">
        <input type="text" name="val[subscribe_radius]"  id="subscribe_radius" class="form-control" placeholder="{_p var='fevent.radius_mile'}" />
    </div>

    <div class="form-group">
        <input type="text" name="val[email]" id="subscribe_email" value="{if isset($aEmail)}{$aEmail}{/if}" class="form-control" placeholder="{_p var='fevent.email'}" />
    </div>
                </div>
                <div class="form-group item-action">
    <button type="button" value="{_p var='fevent.subscribe'}" class="btn btn-primary " id="fevent_subscribeblock_submit">{_p var='fevent.subscribe'}</button>
                </div>
        </div>
    </div>
</form>
{literal}
    <script type="text/javascript">
        $Behavior.readyYnfeventSubscribeBlock = function() {
            fevent.initSubscribeBlock();
            $('#fevent_subscribeblock_submit').click(function(){
                var categories = [];
                $.each($('.category_checkbox:checked'), function(key, el) {
                    categories.push($(el).val());
                })

                $.ajaxCall('fevent.subscribeEvent', 'email=' +$('#subscribe_email').val() +
                                                          '&categories=' +categories.join(',') +
                                                          '&location_lat=' +$('#subscribe_location_address_lat').val() +
                                                          '&location_lng=' +$('#subscribe_location_address_lng').val() +
                                                          '&address=' +$('#subscribe_location_address').val() +
                                                          '&radius=' +$('#subscribe_radius').val()
                                                          );
                return false;
            });
        };
    </script>
{/literal}