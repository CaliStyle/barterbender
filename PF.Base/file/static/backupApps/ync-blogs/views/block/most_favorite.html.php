<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 05/01/2017
 * Time: 13:47
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="ynadvblog-view-modes-block yn-viewmode-grid">
    <div class="yn-view-modes">
        <span data-mode="big" class="yn-view-mode yn_casual"><i class="fa fa-align-left" aria-hidden="true"></i></span>
        <span data-mode="list" class="yn-view-mode"><i class="fa fa-th-list" aria-hidden="true"></i></span>
        <span data-mode="grid" class="yn-view-mode"><i class="fa fa-th" aria-hidden="true"></i></span>
    </div>

    <ul class="ynadvblog_items">
        {foreach from=$aItems name=ynblog item=aItem}
            <li class="ynadvblog_item {$phpfox.iteration.ynblog|ynblog_mode_view_blog_format}">
                {template file='ynblog.block.entry'}
            </li>
        {/foreach}
    </ul>

</div>

{literal}
    <script type="text/javascript">
        $Behavior.initViewModeFavorite = function(){
            ynadvancedblog.initModeView('js_block_border_apps_ync_blogs_block_mostfavorite', 'grid');
        }
    </script>
{/literal}