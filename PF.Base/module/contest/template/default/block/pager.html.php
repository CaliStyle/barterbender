		{if !$bHideAll}
		 <div class="js_pager_view_more_link">
            <div class="pager_links_holder">
                <div class="pager_links">
                    <a class="pager_previous_link {if isset($bDisablePrev) && $bDisablePrev}pager_previous_link_not{/if}" {if isset($bDisablePrev) && $bDisablePrev}href="#" onclick="return false;"{else}href="{$sCurrentUrlPagePrev}"{/if}></a>
                    <a class="pager_next_link {if isset($bDisableNext) && $bDisableNext}pager_next_link_not{/if}" {if isset($bDisableNext) && $bDisableNext}href="#" onclick="return false;"{else}href="{$sCurrentUrlPageNext}"{/if}></a>              
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        {/if}