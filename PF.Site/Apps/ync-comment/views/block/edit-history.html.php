<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="ync-comment-edit-history-container-fix">
<div class="ync-comment-edit-history-container">
    {foreach from=$aEditHistory item=aEdit}
        <div class="ync-comment-item ync-comment-item-edit">
            <div class="item-outer">
                <div class="item-media">
                    {img user=$aEdit suffix='_50_square' max_width=40 max_height=40}
                </div>
                <div class="item-inner">
                    <div class="item-name">
                        {$aEdit|user:'':'':30}
                    </div>
                    <div class="item-comment-content">
                        <div class="content-text">
                            {$aEdit.text|ynccomment_parse|shorten:'300':'comment.view_more':true|split:30|max_line}
                        </div>
                    </div>
                    {if !empty($aEdit.attachment_text)}
                        <div class="item-attachment-info">
                            {_p var=$aEdit.attachment_text}
                        </div>
                    {/if}
                    <div class="item-time">
                        {$aEdit.time_update|convert_time:'core.global_update_time'}
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
</div>
</div>
<div class="help-block">
    {_p var='edits_to_comments_are_visible_to_everyone_who_can_see_this_comment'}
</div>