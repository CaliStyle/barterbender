{if isset($aBusiness)}
    <div class="yndirectory-bussiness-download" style="font-family: sans-serif;">
        <!-- name,location -->
        <div style="margin-bottom: 10px;">
            <h2 style="color: #3b5998;">{$aBusiness.name}</h2>
            <h4 style="border-bottom: 1px solid #ccc;">{$aBusiness.location_address}</h4>
        </div>

        <!-- category -->
        <table style="font-family: sans-serif; margin-bottom: 10px;">
            <tr>
                <td><h4 style="color: #3b5998;">{phrase var='category'}:</h4></td>
                <td style="margin-left: 50px">
                     {foreach from=$aBusiness.list_category key=list_category_key item=list_category_item}
                        <div>
                            {$list_category_item.title}
                            {foreach from=$list_category_item.list_child key=list_child_key item=list_child_item}
                                &#8250;&#8250; {$list_child_item.title}
                            {/foreach}
                        </div>
                     {/foreach}
                </td>
            </tr>
        </table>

        <!-- short description -->
        <div style="margin-bottom: 10px;">
            {$aBusiness.short_description_parsed}
        </div>

        <!-- visiting hours -->
        <div style="margin-bottom: 10px;">
            <h4 style="color: #3b5998;">{phrase var='operating_hours'}:</h4>
            <table cellpadding="3" width="100%" style="font-family: sans-serif;">                
                <tr>
                <td width="50%">
                {foreach from=$aBusiness.list_visitinghour_first key=list_visitinghour_first_key item=list_visitinghour_first_item}
                    <table cellpadding="2" style="font-family: sans-serif;">   
                    <tr>
                        <td style="color: #999">{$list_visitinghour_first_item.phrase}</td>
                        <td>{$list_visitinghour_first_item.vistinghour_starttime} - {$list_visitinghour_first_item.vistinghour_endtime}</td>
                    </tr>
                    </table>
                {/foreach}                                    
                </td>
                <td width="50%">                
                {foreach from=$aBusiness.list_visitinghour_second key=list_visitinghour_second_key item=list_visitinghour_second_item}
                    <table cellpadding="2" style="font-family: sans-serif;">   
                    <tr>
                        <td style="color: #999">{$list_visitinghour_second_item.phrase}</td>
                        <td>{$list_visitinghour_second_item.vistinghour_starttime} - {$list_visitinghour_second_item.vistinghour_endtime}</td>
                    </tr>
                    </table>
                {foreachelse}
                {/foreach}                                    
                </td>
                </tr>
            </table>
        </div>

        <!-- founders -->
        <div style="margin-bottom: 10px;">
            <h4 style="color: #3b5998;">{phrase var='founder'}:</h4>
            <div>
                {$aBusiness.founder}    
            </div>
        </div>

        <!-- contact information -->
        <div style="margin-bottom: 10px;">
            <h4 style="color: #3b5998;">{phrase var='contact_information'}:</h4>

            <table cellpadding="3" style="font-family: sans-serif;">                
                <tr>
                    <td valign="top" style="color: #999">{phrase var='call_us'}</td>
                    <td valign="top">
                        <div>
                            {foreach from=$aBusiness.list_phone_first key=list_phone_first_key item=list_phone_first_item}
                                <div>{$list_phone_first_item.phone_number}</div>
                            {/foreach}                    
                        </div>
                        <div>
                            {foreach from=$aBusiness.list_phone_second key=list_phone_second_key item=list_phone_second_item}
                                <div>{$list_phone_second_item.phone_number}</div>
                            {foreachelse}
                                &nbsp;
                            {/foreach}                    
                        </div>                    
                    </td>
                </tr>
                <tr>
                    <td valign="top" style="color: #999">{phrase var='fax'}</td>
                    <td valign="top">
                        <div>
                            {foreach from=$aBusiness.list_fax_first key=list_fax_first_key item=list_fax_first_item}
                                <div>{$list_fax_first_item.fax_number}</div>
                            {/foreach}                    
                        </div>
                        <div>
                            {foreach from=$aBusiness.list_fax_second key=list_fax_second_key item=list_fax_second_item}
                                <div>{$list_fax_second_item.fax_number}</div>
                            {foreachelse}
                                &nbsp;
                            {/foreach}                    
                        </div>                    
                    </td>
                </tr>
                <tr>
                    <td valign="top" style="color: #999">{phrase var='email'}</td>
                    <td valign="top">{$aBusiness.email}</td>
                </tr>
                <tr>
                    <td valign="top" style="color: #999">{phrase var='website'}</td>
                    <td valign="top">
                        {foreach from=$aBusiness.list_website key=list_website_key item=list_website_item}
                            <div>{$list_website_item.website_text}</div>
                        {/foreach}                                        
                    </td>
                </tr>
            </table>

        <!-- location -->
        <div style="margin-bottom: 10px;">
            <h4 style="color: #3b5998;">{phrase var='locations'}</h4>
            <div>
                <div>
                    {foreach from=$aBusiness.list_location_first key=list_location_first_key item=list_location_first_item}
                        <div>{$list_location_first_item.location_title}</div>
                        <div>{$list_location_first_item.location_address}</div>
                    {/foreach}                    
                </div>
                <div>
                    {foreach from=$aBusiness.list_location_second key=list_location_second_key item=list_location_second_item}
                        <div>{$list_location_second_item.location_title}</div>
                        <div>{$list_location_second_item.location_address}</div>
                    {foreachelse}
                        &nbsp;
                    {/foreach}                    
                </div>                    
            </div>
        </div>

        <!-- additional information -->
        <div style="margin-bottom: 10px;">
            <h4 style="color: #3b5998;">{phrase var='additional_information'}</h4>
            <div>
                {foreach from=$aBusiness.list_addinfo key=list_addinfo_key item=list_addinfo_item}
                    <div>{$list_addinfo_item.usercustomfield_title}: {$list_addinfo_item.usercustomfield_content}</div>
                {/foreach}                    
            </div>
        </div>

        <!-- description -->
        <div style="margin-bottom: 10px;">
            <h4 style="color: #3b5998;">{phrase var='description'}:</h4>
            <div>{$aBusiness.description}</div>
        </div>
    </div>
{else}
    <div>
        {phrase var='unable_to_find_the_business_you_wan_to_download'}
    </div>
{/if}
