{if !empty($aInfo.view) && $aItem.total_view}
    <span class="item-total-view item-statistic">
        {$aItem.total_view|short_number} <span
                class="p-text-lowercase">{if $aItem.total_view == 1}{_p('view')}{else}{_p('views')}{/if}</span>
    </span>
{/if}
{if !empty($aInfo.like) && $aItem.total_like}
    <span class="item-total-like item-statistic">
        {$aItem.total_like|short_number} <span
                class="p-text-lowercase">{if $aItem.total_like == 1}{_p('like')}{else}{_p('likes')}{/if}</span>
    </span>
{/if}
{if !empty($aInfo.comment) && $aItem.total_comment}
    <span class="item-total-comment item-statistic">
        {$aItem.total_comment|short_number} <span
                class="p-text-lowercase">{if $aItem.total_comment == 1}{_p('comment')}{else}{_p('comments')}{/if}</span>
    </span>
{/if}