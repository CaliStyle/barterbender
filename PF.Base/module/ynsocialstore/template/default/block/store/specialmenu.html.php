<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: index.html.php 5083 2012-12-20 11:00:06Z Miguel_Espinoza $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if $bCanAddPhotoInStore}
<div id="ynsocialstore_menu_button" class="clearfix">
	<a class="btn btn-default pull-right" href="{$sUrlAddPhoto}" id="ynsocialstore_add_new_item">{_p var='ynsocialstore.upload_a_new_image'}</a>
</div>
{literal}
<script type="text/javascript">
	;$Behavior.init_ynsocialstore_store_detail_module_photo = function(){
		ynsocialstore.addAjaxForCreateNewItem({/literal}{$iStoreId}{literal}, 'photos');
	};
</script>
{/literal}
{/if}

<div class="page_section_menu page_section_menu_header">
	<div>
		<ul class="nav nav-tabs nav-justified">
			<li {if $bShowPhotos} class="active"{/if}>
				<a href="{$sLinkPhotos}">
					{_p var='photo.photos'}
				</a>
			</li>

			<li {if !$bShowPhotos} class="active"{/if}>
			<a href="{$sLinkAlbums}">
				{_p var='photo.albums'}
			</a>
			</li>
		</ul>
	</div>
	<div class="clear"></div>
</div>
