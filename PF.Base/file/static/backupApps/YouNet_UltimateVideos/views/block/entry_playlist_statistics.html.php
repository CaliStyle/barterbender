{if !empty($aInfo.view) && $aPitem.total_view}
    <div class="ultimatevideo-item-view p-seperate-dot-item hidden-on-featured">
    <span class="item-total-view">
        {$aPitem.total_view|short_number} <span
                class="p-text-lowercase">{if $aPitem.total_view == 1}{_p('view')}{else}{_p('views')}{/if}</span>
    </span>
    </div>
{/if}
{if !empty($aInfo.like) && $aPitem.total_like}
    <div class="ultimatevideo-item-view p-seperate-dot-item hidden-on-featured">
    <span class="item-total-like">
        {$aPitem.total_like|short_number} <span
                class="p-text-lowercase">{if $aPitem.total_like == 1}{_p('like')}{else}{_p('likes')}{/if}</span>
    </span>
    </div>
{/if}
{if !empty($aInfo.comment) && $aPitem.total_comment}
    <div class="ultimatevideo-item-view p-seperate-dot-item hidden-on-featured">
    <span class="item-total-comment">
        {$aPitem.total_comment|short_number} <span
                class="p-text-lowercase">{if $aPitem.total_comment == 1}{_p('comment')}{else}{_p('comments')}{/if}</span>
    </span>
    </div>
{/if}