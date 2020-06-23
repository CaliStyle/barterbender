{if $aDocument.doc_id>0}
<script type="text/javascript">
$Behavior.fdocView = function() {l}
var oniPaperReady = function(e) {l}{r};
if ($('#page_document_view').length)
{l}
var scribd_doc = scribd.Document.getDoc({$aDocument.doc_id}, '{$aDocument.access_key}');
scribd_doc.addParam('jsapi_version', 2);
scribd_doc.addParam('height', {$document_height});
scribd_doc.addParam('width', {$document_width});
scribd_doc.addParam('default_embed_format', 'html5');
console.log("khoi tao scrd")
scribd_doc.addEventListener('docReady', oniPaperReady);
scribd_doc.write('embedded_flash');
{r}
{r}
</script>
{/if}

<div class="item_view">
    <div class="item_info">
    </div>
    {if $aDocument.is_approved == 0}
        {template file='core.block.pending-item-action'}
    {/if}
    {if Phpfox::getUserParam('document.can_feature_documents') || ((Phpfox::getUserParam('document.can_edit_own_document') && $aDocument.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('document.can_edit_other_document'))
    || ((Phpfox::getUserParam('document.can_delete_own_document') && $aDocument.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('document.can_delete_other_document'))}
    <div class="item_bar">
        <div class="item_bar_action_holder">
            <a role="button" data-toggle="dropdown" href="#" class="item_bar_action"><i class="ico ico-gear-o"></i><span>{phrase var='actions'}</span></a>
            <ul class="dropdown-menu dropdown-menu-right">
                {template file='document.block.links'}
            </ul>
        </div>
    </div>
    {/if}
    {if $aDocument.doc_id>0}
    <div>{$aDocument.scribd_display} </div>
    {else}
    <div class="table-responsive">{$aDocument.google_display}</div>
    {/if}

    <div class="ynd_detail_infomation">
    {if isset($aDocument.license_id)}
    
        
    
    <div class="mb-1"> <img src="{$aDocument.license_image_url}" /></div>
    <div class=""> {phrase var='this_document_is_licensed_under'} <a href="{$aDocument.reference_url}" target="_blank">{$aDocument.license_name}</a></div>
    {else}

    <div class="">{phrase var='unspecified_no_licensing_information_is_associated'}</div>
    {/if}
    {if $aDocument.user_id == Phpfox::getUserId() || $aDocument.allow_download || $aDocument.allow_attach}
    <div class="table form-group">
        <div class="document_share_box t_center">
            {if $aDocument.user_id == Phpfox::getUserId() || $aDocument.allow_download}
                <a href="javascript:void(0);" onclick="window.location.href='{$aDocument.download_link}';return false;" >
                    <i class="fa fa-download"></i>
                    {phrase var='download'}
                </a> 
            {/if}

            {if $aDocument.user_id == Phpfox::getUserId() || $aDocument.allow_attach}
            <a href="#" onclick="tb_show('{phrase var='email_as_attachment'}', $.ajaxBox('document.popup', 'height=400&width=600&id={$aDocument.document_id}')); return false;">
                <i class="fa fa-paperclip"></i>
                {phrase var='email_as_attachment'}
            </a>
            {/if}
        </div>
    </div>
    {/if}
    {module name='document.detail'}
    {if $aDocument.allow_rating == '1'}
    {if Phpfox::isModule('rate')}
    <div class="document_rate_body">
        <div class="document_rate_display">
            {module name='rate.display'}
        </div>
        <a href="#" class="document_view_embed">{phrase var='embed'}</a>
        <div class="document_view_embed_holder">
            <textarea name="#" onclick="this.select();" cols=100 rows=5>{$aDocument.bookmark} </textarea>
        </div>
    </div>
    {/if}
    {/if}
    </div>
    {addthis url=$aDocument.bookmark_url title=$aDocument.title}
    <div class="item_view">
        <div class="item-detail-feedcomment">
            {module name='feed.comment'}
        </div>
    </div>
</div>
