<!-- <div id="item_{$aDocument.index}_center" class="{if !$aDocument.first_item && !$aDocument.last_item}background_center{elseif $aDocument.last_item} background_align{/if}"> -->
    <div id="document_block_{$aDocument.document_id}" class="entry_document">
        <div class="document_block" >
            <!-- <div id="item_{$aDocument.index}_left" class="{if $aDocument.first_item}background-left{/if}"> -->
                <!-- <div id="item_{$aDocument.index}_right" class=" {if $aDocument.last_item}background_right{/if}"> -->
                    <!-- <div class="table_left_modified"> -->
                        <div class="document_image_block" id="document_image_{$aDocument.document_id}">
                            <div class="background_document">
                                {if Phpfox::getUserParam('document.can_approve_documents') || Phpfox::getUserParam('document.can_delete_other_document')}
                                <div class="moderation_row yndocument_mod">
                                    <label class="item-checkbox">
                                        <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aDocument.document_id}" id="check{$aDocument.document_id}" />
                                        <i class="ico ico-square-o"></i>
                                    </label>
                                </div>
                                {/if}
                                <div class="js_mp_fix_holder image_hover_holder">
                                    {if Phpfox::getUserParam('document.can_feature_documents') || ((Phpfox::getUserParam('document.can_edit_own_document') && $aDocument.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('document.can_edit_other_document'))
                                    || ((Phpfox::getUserParam('document.can_delete_own_document') && $aDocument.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('document.can_delete_other_document'))}
                                    <a href="{$aDocument.link}" class="ynd_bg_img clearfix">
                                        {if !empty($aDocument.image_url) && strpos($aDocument.image_url,'http') !== false}
                                            <img id="js_img_block_{$aDocument.document_id}" class="document_normal" onload title="{$aDocument.title}" src="{$aDocument.image_url}" onerror="this.src='{$no_image_url}';" />
                                        {else}
                                            {img server_id=$aDocument.image_server_id  path='core.url_pic' file='document/'.$aDocument.image_url suffix='_400_square' class='document_normal'}
                                        {/if}
                                    </a>
                                    <a href="#" class="image_hover_menu_link">{phrase var='link'}</a>
                                    <div class="image_hover_menu">
                                        <ul>
                                            {if Phpfox::getUserParam('document.can_approve_documents')}
                                            <li id="js_approve_{$aDocument.document_id}" {if $aDocument.is_approved}
                                                style="display:none;"{/if}>
                                            {if $sView=='pending'}
                                            <a href="#" onclick="$(this).parent().hide(); $('#js_disapprove_{$aDocument.document_id}').show(); $.ajaxCall('document.frontendUpdateApproveRemove', 'id={$aDocument.document_id}&amp;active=1'); return false;"><span>&nbsp</span>{phrase var='approve'}</a>
                                            {else}
                                            <a href="#" onclick="$(this).parent().hide(); $('#js_disapprove_{$aDocument.document_id}').show(); $.ajaxCall('document.frontendUpdateApprove', 'id={$aDocument.document_id}&amp;active=1'); return false;"><span>&nbsp</span>{phrase var='approve'}</a>
                                            {/if}


                                            </li>
                                            <li id="js_disapprove_{$aDocument.document_id}" {if !$aDocument.is_approved} style="display:none;"{/if}><a href="#" onclick="$(this).parent().hide(); $('#js_approve_{$aDocument.document_id}').show(); $.ajaxCall('document.frontendUpdateApprove', 'id={$aDocument.document_id}&amp;active=0'); return false;"><span>&nbsp</span>{phrase var='disapprove'}</a></li>
                                            {/if}
                                            
                                            {if Phpfox::getUserParam('document.can_feature_documents')}
                                            <li id="js_feature_{$aDocument.document_id}" {if $aDocument.is_featured || !$aDocument.is_approved} style="display:none;"{/if}><a href="#" onclick="$(this).parent().hide(); $('#js_unfeature_{$aDocument.document_id}').show(); $.ajaxCall('document.frontendUpdateFeature', 'id={$aDocument.document_id}&amp;active=1'); return false;"><span>&nbsp</span>{phrase var='feature'}</a> </li>
                                            <li id="js_unfeature_{$aDocument.document_id}" {if !$aDocument.is_featured || !$aDocument.is_approved} style="display:none;"{/if}><a href="#" onclick="$(this).parent().hide(); $('#js_feature_{$aDocument.document_id}').show(); $.ajaxCall('document.frontendUpdateFeature', 'id={$aDocument.document_id}&amp;active=0'); return false;"><span>&nbsp</span>{phrase var='un_feature'}</a></li>
                                            {/if}
                                            
                                            {if (Phpfox::getUserParam('document.can_edit_own_document') && $aDocument.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('document.can_edit_other_document')}
                                            <li id="item_edit_{$aDocument.document_id}"><a href="{$aDocument.edit_link}" title="{phrase var='edit'}" ><span>&nbsp</span>{phrase var='edit'}</a></li>
                                            {/if}
                                            
                                            {if (Phpfox::getUserParam('document.can_delete_own_document') && $aDocument.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('document.can_delete_other_document')}
                                            <li class="item_delete"><a href="#" title="{phrase var='delete'}" onclick="$Core.jsConfirm({l}message:'{_p('Are you sure you want to delete this document permanently?')}'{r},function(){l}$.ajaxCall('document.delete', 'document_id={$aDocument.document_id}'){r});return false;"><span>&nbsp</span>{phrase var='delete'}</a></li>
                                            {/if}
                                        </ul>
                                    </div>
                                    {else}
                                    <a href="{$aDocument.link}"  class="ynd_bg_img clearfix">
                                        {if !empty($aDocument.image_url) && strpos($aDocument.image_url,'http') !== false}
                                            <img id="js_img_block_{$aDocument.document_id}" class="document_normal" onload title="{$aDocument.title}" src="{$aDocument.image_url}" onerror="this.src='{$no_image_url}';" />
                                        {else}
                                            {img server_id=$aDocument.image_server_id path='core.url_pic' file='document/'.$aDocument.image_url suffix='_400_square' class='document_normal'}
                                        {/if}
                                    </a>
                                    {/if}
                                    
                                    {if $aDocument.page_count}<div class="document_page" >{$aDocument.page_count} p.</div> {/if}
                                </div>
                                
                                <div id="feature_new_document_{$aDocument.document_id}" class="document_featured" {if $aDocument.is_approved && $aDocument.is_featured && $aDocument.is_new}style="display:block;z-index:1000000!important"{else}style="display:none;"{/if}><img src="{$core_path}module/document/static/image/feature-new.png"/></div>
                                <div id="feature_document_{$aDocument.document_id}" class="document_featured" {if $aDocument.is_approved && $aDocument.is_featured && !$aDocument.is_new}style="display:block;z-index:1000000!important;"{else}style="display:none;"{/if}><img src="{$core_path}module/document/static/image/feature.png"/></div>
                                <div id="new_document_{$aDocument.document_id}" class="document_new" {if $aDocument.is_approved && !$aDocument.is_featured && $aDocument.is_new}style="display:block;z-index:1000000!important"{else}style="display:none;"{/if}><img src="{$core_path}module/document/static/image/new.png"/></div>
                                <div id="pending_document_{$aDocument.document_id}" class="document_pending" {if !$aDocument.is_approved}style="display:block;z-index:1000000!important;"{else}style="display:none;"{/if}><img src="{$core_path}module/document/static/image/pending.png"/></div>
                            </div>
                            
                        </div>

                    <!-- </div> -->
                <!-- </div> -->
            <!-- </div> -->

            <div class="table_right_modified">
                <div class="entry_table_right_modified_div">
                    <a class="someClass yndocument_{$iPage} entry_description_title" href="{$aDocument.link}" data-title="{$aDocument.title|clean}" data-text="{$aDocument.text_parsed|striptag|shorten:175:'...'}" data-date="{$aDocument.date}">
                    {$aDocument.title|clean}</a></div>
                <div class="document_extra_info">{phrase var='by'} <a href="{$aDocument.full_name_link}" {if $aDocument.is_long_name}title="{$aDocument.full_name}"{/if}>{$aDocument.full_name|shorten:20:'...'}</a></div>
                <div class="document_extra_info"> {if $aDocument.total_view == 1}{$aDocument.total_view} {phrase var='view'}{else}{$aDocument.total_view} {phrase var='views'}{/if} | {if $aDocument.total_like == 1}{$aDocument.total_like} {phrase var='like'}{else}{$aDocument.total_like} {phrase var='likes'}{/if}</div>
            </div>
            <div class="clear"> </div>
        </div>
    </div>
<!-- </div> -->