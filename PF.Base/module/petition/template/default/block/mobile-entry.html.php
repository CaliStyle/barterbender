   <div class="ynp-mobile-featured">
		<a target="_self" href="{permalink module='petition' id=$aPetition.petition_id title=$aPetition.title}" class="row_sub_link" title="{$aPetition.title|clean}">
		<div class="pet_img_tit">                            
				{img server_id=$aPetition.server_id path='core.url_pic' file=$aPetition.image_path suffix='_120' width=90 class='photo_holder'}                            
		</div>
		</a>		
		<div class="row_title_info">		
			<a class="link1" target="_self" href="{permalink module='petition' id=$aPetition.petition_id title=$aPetition.title}" class="row_sub_link" title="{$aPetition.title|clean}"><strong>{$aPetition.title|clean|shorten:50:'...'|split:20}</strong></a>		
			<div class="extra_info">{phrase var='petition.created_by'} {$aPetition|user} {if isset($aDirect.category)}{phrase var='petition.in'} <a href="{$aPetition.category.link}">{$aPetition.category.name}</a>{/if}
				</br> {if $aPetition.is_directsign == 1}<span class="total_sign">{$aPetition.total_sign}</span>{phrase var='petition.total_sign_signatures' total_sign=''}{else}{phrase var='petition.total_sign_signatures' total_sign=$aPetition.total_sign}{/if} - {phrase var='petition.total_like_likes' total_like=$aPetition.total_like} - {phrase var='petition.total_view_views' total_view=$aPetition.total_view}
			</div>
			<div class="item_content item_view_content">
				{$aPetition.short_description|shorten:75:'...'|split:55}
			</div>	
			<div class="clear"></div>
		</div>
	</div>