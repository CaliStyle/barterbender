<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/6/16
 * Time: 12:16 PM
 */
?>
<div id="ynsocialstore_detail" class="ynstore-store-about">
    <div class="ynstore-store-info-map-block">
        <div class="ynstore-info">
            <div class="ynstore-title">{_p var='ynsocialstore.contact_detail'}</div>

            <ul class="ynstore-info-detail">
                <li class="ynstore-multiple-address">
                    <ul>
                        {foreach from=$aAboutUs.address item=aAddress}
                        <li>
                            <label><i class="ico ico-home-alt"></i>{$aAddress.title}</label>
                            {$aAddress.address}
                        </li>
                        {/foreach}

                        {if count($aAboutUs.address) > 3}
                        <span class="ynstore-address-viewmore" onclick="ynsocialstore.showFullAddress(this); return false;">
                            view more <i class="ico ico-angle-down"></i>
                        </span>
                        {/if}
                    </ul>
                </li>

                {if isset($aAboutUs.phone)}
                <li>
                    <i class="ico ico-telephone"></i>
                    {_p var='ynsocialstore.phone'}:
                    {foreach from = $aAboutUs.phone key=iKey item = aPhone}
                        {if $iKey > 0}&nbsp-&nbsp{/if}{$aPhone}
                    {/foreach}
                </li>
                {/if}

                {if isset($aAboutUs.fax)}
                <li>
                    <i class="ico ico-printer"></i>
                    {_p var='ynsocialstore.fax'}:
                    {foreach from = $aAboutUs.fax key=iKey item = aFax}
                    {if $iKey > 0}&nbsp-&nbsp{/if}{$aFax}
                    {/foreach}
                </li>
                {/if}

                {if !empty($aAboutUs.website)}
                <li>
                    <i class="ico ico-globe"></i>
                    {_p var='ynsocialstore.website'}:
                    {foreach from = $aAboutUs.website key=iKey item = aWebsite}
                        {if $iKey > 0}&nbsp-&nbsp{/if} <a href="{if (strpos($aWebsite, 'http://') === false) && (strpos($aWebsite, 'https://') === false)}http://{/if}{$aWebsite}" target="_blank">{$aWebsite}</a>
                    {/foreach}
                </li>
                {/if}

                <li>
                    <i class="ico ico-envelope"></i>
                    {_p var='ynsocialstore.email'}:
                    <a href="mailto:{$aAboutUs.email}">{$aAboutUs.email}</a>
                </li>

                {if !empty($aAboutUs.city)}
                <li>
                    <i class="ico ico-home-alt"></i>
                    {_p var='ynsocialstore.city'}:
                    &nbsp{$aAboutUs.city}
                </li>
                {/if}

                {if !empty($aAboutUs.store_country_iso)}
                <?php $this->_aVars['country_name'] = Phpfox::getService('core.country')->getCountry($this->_aVars['aAboutUs']['store_country_iso']) ?>
                <?php $this->_aVars['province'] = Phpfox::getService('core.country')->getChild($this->_aVars['aAboutUs']['country_child_id']); ?>
                {if !empty($province)}
                <li>
                    <i class="ico ico-home-alt"></i>
                    {_p var='ynsocialstore.province'}:
                    &nbsp{$province}
                </li>
                {/if}
                <li>
                    <i class="ico ico-home-alt"></i>
                    {_p var='ynsocialstore.country'}:
                    &nbsp{$country_name}
                </li>
                {/if}
            </ul>

        </div>
        {if !empty($aAboutUs.main_address) && !empty($aAboutUs.main_address.longitude) && !empty($aAboutUs.main_address.latitude)}
        <div class="ynstore-map-block">
            <div class="ynstore-map">
                <div id="ynsocialstore_detail_mapview_wrapper" style="height: 240px;position: relative;overflow: hidden;width: 100%;">
                    <div id="ynsocialstore_detail_mapview" style="width: 100%; height: 100%;" class="mapping"></div>
                </div>
            </div>
            <a href="https://maps.google.com/maps?daddr={$aAboutUs.main_address.latitude},{$aAboutUs.main_address.longitude}" class="ynstore-viewmap btn btn-default" target="_blank">
                <i class="ico ico-map"></i>
                {_p var='ynsocialstore.view_full_map_direction'}
            </a>
        </div>
        {/if}
    </div>

    <div class="ynstore-store-about-txt">
        <div class="ynstore-title">
            {_p var='ynsocialstore.information'}
        </div>
        <div class="item_content">
            {if Phpfox::getParam('core.allow_html')}
                {$aAboutUs.description_parse|parse}
            {else}
                {$aAboutUs.description|parse}
            {/if}

            <ul class="ynstore-additional">
                {if isset($aAboutUs.addinfo)}
                {foreach from = $aAboutUs.addinfo item = aAddInfo}
                <li>
                    <label>{$aAddInfo.question}</label>
                    <div class="extra-info">{$aAddInfo.answer}</div>
                </li>
                {/foreach}
                {/if}
            </ul>
        </div>
    </div>
</div>

<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={$apiKey}&v=3.exp&libraries=places"></script>
