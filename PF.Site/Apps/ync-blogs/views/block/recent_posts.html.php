<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 05/01/2017
 * Time: 11:31
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aItems)}
<div class="ynadvblog-view-modes-block yn-viewmode-<?php echo setting('yn_advblog_default_viewmode'); ?>">
    <div class="yn-view-modes">
        <span data-mode="big" class="yn-view-mode yn_casual"><i class="fa fa-align-left" aria-hidden="true"></i></span>
        <span data-mode="list" class="yn-view-mode"><i class="fa fa-th-list" aria-hidden="true"></i></span>
        <span data-mode="grid" class="yn-view-mode"><i class="fa fa-th" aria-hidden="true"></i></span>
    </div>
    
    <ul class="ynadvblog_items clearfix ">
        {foreach from=$aItems name=ynblog item=aItem}
            <li class="ynadvblog_item {$phpfox.iteration.ynblog|ynblog_mode_view_blog_format}">
                {template file='ynblog.block.entry'}
            </li>
        {/foreach}
    </ul>

</div>
    

{literal}
    <script type="text/javascript">
        $Behavior.initViewModeRecent = function(){
            ynadvancedblog.initModeView('js_block_border_apps_ync_blogs_block_recentposts', '{/literal}<?php echo setting('yn_advblog_default_viewmode'); ?>{literal}');
        }
    </script>
{/literal}

{else}
<div class="extra_info">{_p('no_blogs_found')}</div>
{/if}