{if $bInHomepage > 1 }

{foreach from=$aDocuments name=documents item=aDocument}
{template file='document.block.entry'}
{/foreach}
{pager}
{elseif count($aDocuments)}

{literal}
<style type="text/css">
ul.item_menu li a,
ul.item_menu li a:hover
{
    background:url("{/literal}{$core_path}{literal}theme/frontend/default/style/default/image/layout/button.png") repeat-x center bottom #666;
    color: #fff;
    vertical-align: middle;
}
.table_left_modified
{
	background:url("{/literal}{$core_path}{literal}module/document/static/image/shell-mid.png") repeat-x bottom;
}
.background-left
{
	background:url("{/literal}{$core_path}{literal}module/document/static/image/shell-l.png") no-repeat left bottom;
	padding-left:25px;
}
.background_right
{
	background:url("{/literal}{$core_path}{literal}module/document/static/image/shell-r.png") no-repeat right bottom;
	padding-right:25px;
}
.image_hover_active,
.image_hover_menu_link
{
	background: url("{/literal}{$core_path}{literal}module/document/static/image/fdoc-action.png") no-repeat center transparent;
	line-height:11px;
	width:14px;
	padding:3px;
	border: 1px solid #ccc;
	background-color:#fff;
}
.image_hover_menu
{
    display: none;
    right:0;
    left: inherit;
	top: 133px;
    margin: 5px 0 0;
    padding: 8px 0 0;
    position: absolute;
    z-index: 2000000;
	min-width:100px;
}

.image_hover_menu ul li a span
{
	
	padding-left: 9px; 
}

</style>
{/literal}


	<div class="yndocument-list-document">
	<div id="document_message" class="public_message" style="display:none"></div>
	<div>
<!-- 	<div class="background_left"></div>
	<div class="background_right"></div> -->
	<div class="document_list">
	{foreach from=$aDocuments name=documents item=aDocument}
        {template file='document.block.entry'}
	{/foreach}
		{pager}
	</div>
	<div class="clear"></div>
		{if Phpfox::getUserParam('document.can_approve_documents') || Phpfox::getUserParam('document.can_delete_other_document')}
		{moderation}
		{/if}
		<div class="t_right">

		</div>
	</div>
	</div>
{else}
	<div style="width:510px">
	{phrase var='no_documents_found'}
	</div>
{/if}


<!--Init tip tip-->
{literal}
<script type="text/javascript">
    $Behavior.fdocIndex = function() {
        var page = {/literal}{$iPage}{literal};
        var elename = ".someClass.yndocument_" + page;
        console.log(elename);
        showTip($(elename));
    }
</script>
{/literal}
<!--End Init-->