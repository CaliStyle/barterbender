{if !count($aUsers)}
    {if !PHPFOX_IS_AJAX}
    <div class="extra_info">
        {_p var='No members found'}
    </div>
    {/if}
{else}
    {if !PHPFOX_IS_AJAX}
        <div id="js_block_border_apps_yn_member_block_search" class="block">
            <div class="title ynmember_header">
                {$sFoundMessage}
            </div>
            <div class="ynmember_content content">
                <div class="ynmember-view-modes-block block">
                    <div class="yn-view-modes">
                        <span data-mode="list" class="yn-view-mode"><i class="fa fa-th-list" aria-hidden="true"></i></span>
                        <span data-mode="grid" class="yn-view-mode"><i class="fa fa-th" aria-hidden="true"></i></span>
                    </div>
                    <ul class="ynmember_member_entry clearfix show_list_view">
                    {/if}
                        {foreach from=$aUsers item=aUser}
                            <li class="ynmember_member_entry_item">
                                {template file='ynmember.block.entry_member'}
                            </li>
                        {/foreach}
                        {pager}
                    {if !PHPFOX_IS_AJAX}
                    </ul>
                </div>
            </div>
        </div>
    {/if}
{/if}

{literal}
    <script type="text/javascript">
        $Behavior.initMemberModview = function(){
            ynmember.initModeView('js_block_border_apps_yn_member_block_search');
        }
    </script>
{/literal}