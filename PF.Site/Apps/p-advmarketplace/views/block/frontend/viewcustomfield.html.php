{foreach from=$aCustomFields key=sKey item=aCustomField}
    <div class="listing_detail mb-1 mt-1">
        <div class="ynmarketplace_detail-listing-item">
            <p class="ynmarketplace_detail-title mb-2 text-gray-dark text-uppercase">{phrase var=$sKey}</p>
            <div class="short_description_content">
                <div class="ync-detail-custom-fields-container">
                    {foreach from=$aCustomField key=iKey item=aField}
                        {if $aField.view_id}
                            {module name="advancedmarketplace.frontend.view.".($aField.view_id) aField=$aField cfInfors = $cfInfors}
                        {/if}
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
{/foreach}