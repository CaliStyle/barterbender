<?php
/**
 * 
 * 
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if Phpfox::getUserParam('document.can_approve_documents')}
<li id="js_approve_{$aDocument.document_id}" {if $aDocument.is_approved} style="display:none;"{/if}><a href="#" onclick="$(this).parent().hide(); $('#js_disapprove_{$aDocument.document_id}').show(); $.ajaxCall('document.frontendUpdateApprove', 'id={$aDocument.document_id}&amp;active=1'); return false;"><span>&nbsp</span>{phrase var='approve'}</a></li>
<li id="js_disapprove_{$aDocument.document_id}" {if !$aDocument.is_approved} style="display:none;"{/if}><a href="#" onclick="$(this).parent().hide(); $('#js_approve_{$aDocument.document_id}').show(); $.ajaxCall('document.frontendUpdateApprove', 'id={$aDocument.document_id}&amp;active=0'); return false;"><span>&nbsp</span>{phrase var='disapprove'}</a></li>
{/if}

{if Phpfox::getUserParam('document.can_feature_documents')}
<li id="js_feature_{$aDocument.document_id}" {if $aDocument.is_featured || !$aDocument.is_approved}style="display:none;"{/if}><a href="#" onclick="$(this).parent().hide(); $('#js_unfeature_{$aDocument.document_id}').show(); $.ajaxCall('document.frontendUpdateFeature', 'id={$aDocument.document_id}&amp;active=1'); return false;"><span>&nbsp</span>{phrase var='feature'}</a> </li>
<li id="js_unfeature_{$aDocument.document_id}" {if !$aDocument.is_featured || !$aDocument.is_approved}style="display:none;"{/if}><a href="#" onclick="$(this).parent().hide(); $('#js_feature_{$aDocument.document_id}').show(); $.ajaxCall('document.frontendUpdateFeature', 'id={$aDocument.document_id}&amp;active=0'); return false;"><span>&nbsp</span>{phrase var='un_feature'}</a></li>
{/if}

{if (Phpfox::getUserParam('document.can_edit_own_document') && $aDocument.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('document.can_edit_other_document')}
    <li id="item_edit_{$aDocument.document_id}"><a href="{$aDocument.edit_link}" title="{phrase var='edit'}" ><span>&nbsp</span>{phrase var='edit'}</a></li>
{/if}

{if (Phpfox::getUserParam('document.can_delete_own_document') && $aDocument.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('document.can_delete_other_document')}
    <li class="item_delete"><a class="sJsConfirm" data-message="{_p var='are_you_sure_you_want_to_delete_this_document_permanently'}" href='{url link="document.delete" id=$aDocument.document_id}' title="{phrase var='delete'}"><span>&nbsp</span>{phrase var='delete'}</a></li>
{/if}