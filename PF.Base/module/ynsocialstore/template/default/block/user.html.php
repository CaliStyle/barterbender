<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/17/16
 * Time: 10:34 AM
 */
?>

{if count($aItem) > 0}
    {if $iPage == 1}
    <div class="content-wrapper ynstore-product-friendbuy-popup clearfix" id="js_content_users">
    {/if}
        <input type="hidden" id="current_page" value="{$iPage}">
        {foreach from = $aItem item = aUser}
            <div class="user_rows">
                <div class="user_rows_image">
                    {img user=$aUser suffix='_120_square'}
                </div>
                <?php echo '<span class="" id="js_user_name_link_' . $this->_aVars['aUser']['user_name'] . '"><a href="' . Phpfox::getLib('phpfox.url')->makeUrl('profile', array($this->_aVars['aUser']['user_name'], ((empty($this->_aVars['aUser']['user_name']) && isset($this->_aVars['aUser']['profile_page_id'])) ? $this->_aVars['aUser']['profile_page_id'] : null))) . '">' . Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getService('user')->getCurrentName($this->_aVars['aUser']['user_id'], $this->_aVars['aUser']['full_name']), Phpfox::getParam('user.maximum_length_for_full_name')) . '</a></span>'; ?>
            </div>
        {/foreach}
    {if $iPage == 1}
    </div>
    {/if}
    <a role="button" class="ynstore-loadmore ynstore_loadmore_users" id="js_load_more_users" onclick="onLoadMoreItem()">
        {_p var='ynsocialstore.load_more'}
    </a>
    {literal}
    <script type="text/javascript">
        function onLoadMoreItem() {
            if ($('#current_page').length > 0) {
                var page = $('#current_page').val();
                $('#current_page').remove();
                $.ajaxCall('ynsocialstore.getUsers', 'iItemId={/literal}{$iItemId}{literal}&sType={/literal}{$sType}{literal}&page=' + page);
            }
        }
    </script>
    {/literal}
{else}
    {literal}
    <script type="text/javascript">
        $('.ynstore_loadmore_users').remove();
    </script>
    {/literal}
    <span class="ynstore-loadmore-txt">
        {if $sType == 'friend-bought-this'}{_p var='ynsocialstore.no_friends_found'}{else}{_p var='ynsocialstore.no_users_found'}{/if}
    </span>
{/if}

