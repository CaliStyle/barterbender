<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div class="gettingstarted_info_box">
	<div class="gettingstarted_info_box_content">
		<div class="gettingstarted_info_view">{if $dsarticle.total_view == 0}1{else}{$dsarticle.total_view|number_format}{/if}</div>	
		<ul class="gettingstarted_info_box_list">
			<li class="full_name first">
				<span id="js_user_name_link_admin" class="user_profile_link_span">
					<a href="{$path_user}">{$username}</a>
				</span>
			</li>
			<li>{$time_stamp}</li>
			<li>{$total_comments} {phrase var='gettingstarted.comment_s'}</li>
		</ul>
		</ul>
		</ul>

		<div class="gettingstarted_info_box_extra">	
			<div class="table form-group">
				<div class="table_left">
					{phrase var='gettingstarted.category'}: <a href="{$dsarticle.url}" onclick="window.location=this.href; return false;">{$dsarticle.article_category_name|convert|clean}</a>
				</div>
			</div>
		</div>
	</div>
</div>
<div id='gettingstarted_addthis'>
     {if $bShowAddThisSection}
     <div class="addthis_share">
         <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid={$public_id_addthis}" data-title="{$dsarticle.title|clean}"></script>
         {addthis url=$dsarticle.bookmark title=$dsarticle.title description=$dsarticle.description}
     </div>
     {/if}
</div>
        
        
