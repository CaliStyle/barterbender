{if $bCanClaim}
<li>
    <a href="#?call=contact.showQuickContact&amp;height=600&amp;width=600&amp;page_id={$aPage.page_id}" class="inlinePopup" title="{_p var='claim_page'}">
        <span class="ico ico-compose-alt"></span>
        {_p var='claim_page'}
    </a>
</li>
{/if}
{if $bCanEdit}
<li>
    <a href="{url link='pages.add' id=$aPage.page_id}">
        <span class="ico ico-gear-form-o mr-1"></span>
        {_p var='manage'}
    </a>
</li>
{/if}
{if $bCanDelete}
<li class="item_delete">
    <a href="{url link='pages' delete=$aPage.page_id}" data-message="{_p var='are_you_sure'}" class="no_ajax_link sJsConfirm">
        <span class="ico ico-trash-alt-o mr-1"></span>
        {_p var='delete_this_page'}
    </a>
</li>
{/if}