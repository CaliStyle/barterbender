<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[YouNet_COPYRIGHT]
 * @author  		YouNet Company
 * @package 		Module_GettingStarted
 * @version 		3.02p5
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="panel panel-default">
    <div id="js_menu_drop_down" style="display:none;">
        <div class="link_menu dropContent" style="display:block;">
            <ul>
                <li><a href="#" onclick="return $Core.gettingstarted.action(this, 'edit');">{phrase var='gettingstarted.edit'}</a></li>
                <li><a href="#" onclick="return $Core.gettingstarted.action(this, 'delete');">{phrase var='gettingstarted.delete'}</a></li>
            </ul>
        </div>
    </div>
    <div class="panel-heading">
        {phrase var='gettingstarted.categories'}
    </div>
    <form method="post" action="{url link='current'}">
        <div class="panel-body">
            <div class="form-group">
                <label>
                    {phrase var='gettingstarted.language'}:
                </label>
                <select class="form-control" id="language_id" onchange="window.location.href = '{$sUrlManageCategory}' + 'lang_' + this.value + '/';" >
                    {foreach from=$aLanguages item=aLanguage}
                    <option value="{$aLanguage.language_id}"{if $aLanguage.language_id==$lang_id} selected="selected"{/if}>{$aLanguage.title}</option>
                    {/foreach}
                </select>
                <span id="loading"></span>
            </div>
            <div class="form-group">
                <div class="sortable">
                    {if count($sCategories)}
                    {$sCategories}
                    {else}
                    {phrase var='gettingstarted.do_not_have_any_category_in_this_language'}
                    {/if}
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <input type="submit" value="{phrase var='gettingstarted.update_order'}" class="btn btn-primary" />
        </div>
    </form>
</div>
{literal}
<script type="text/javascript">
    $Behavior.initManageCategory = function() {
        $(function()
        {
            $('.sortable ul').sortable({
                axis: 'y',
                update: function(element, ui)
                {
                    var iCnt = 0;
                    $('.js_mp_order').each(function()
                    {
                        iCnt++;
                        this.value = iCnt;
                    });
                },
                opacity: 0.4
            });

            $('.js_drop_down').click(function()
            {
                eleOffset = $(this).offset();
                aParams = $.getParams(this.href);
                $('#js_cache_menu').remove();
                $('body').prepend('<div id="js_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:100; background:red;">' + $('#js_menu_drop_down').html() + '</div>');
                $('#js_cache_menu .link_menu li a').each(function()
                {
                    this.href = '#?id=' + aParams['id'];
                });
                $('.dropContent').show();

                $('.dropContent').mouseover(function()
                {
                    $('.dropContent').show();
                    return false;
                });

                $('.dropContent').mouseout(function()
                {
                    $('.dropContent').hide();
                    $('.sJsDropMenu').removeClass('is_already_open');
                });
                return false;
            });

        });
    }
</script>
{/literal}