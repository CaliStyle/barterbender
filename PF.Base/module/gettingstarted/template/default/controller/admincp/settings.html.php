<?php
/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          2.01
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" onsubmit="return checkValidate_AdSearch();" action="{url link='admincp.gettingstarted.settings'}" id="admincp_advancesearch_form_message">
    <input type="hidden" name="action" value="add"/>
    <div class="table_header">
        Global Settings
    </div>

    <div class="table">
        <div class="row_left">
            {phrase var='gettingstarted.define_the_limit_of_how_many_articles_for_each_category'}
        </div>
        <div class="row_right">
            <input type="text" name="val[number_of_article_category]" id="number_of_article_category" value="{$number_of_article_category}"/>
        </div>
        <div class="clear"></div>
    </div>

    <div class="table">
        <div class="row_left">
            {phrase var='gettingstarted.define_the_limit_of_how_many_articles_can_be_displayed_on'}
        </div>
        <div class="row_right">
            <input type="text" name="val[number_of_article]" id="number_of_article" value="{$number_of_article}"/>
        </div>
        <div class="clear"></div>
    </div>

    <div class="table">
        <div class="row_left">
            {phrase var='gettingstarted.define_the_limit_of_how_many_letters_will_be_sent_in_one_time'}
        </div>
        <div class="row_right">
            <input type="text" name="val[number_of_letters]" id="number_of_letters" value="{$number_of_letters}"/>
        </div>
        <div class="clear"></div>
    </div>  
    <div class="table">
        <div class="row_left">
            {phrase var='gettingstarted.active_getting_started'}
        </div>
        <div class="row_right">
            <select name="val[active_getting_started]">
                <option value="1" {if $active_getting_started == 1}selected{/if}>True</option>
                <option value="0" {if $active_getting_started == 0}selected{/if}>False</option>
            </select>
        </div>
        <div class="clear"></div>
    </div>
    <div class="table">
        <div class="row_left">
            {phrase var='gettingstarted.active_mail_remainder'}
        </div>
        <div class="row_right">
            <select name="val[active_email_remainder]">
                <option value="1" {if $active_email_remainder == 1}selected{/if}>True</option>
                <option value="0" {if $active_email_remainder == 0}selected{/if}>False</option>
            </select>
        </div>
        <div class="clear"></div>
    </div>
    <div class="table">
        <div class="row_left">
            {phrase var='gettingstarted.active_base_knowledge'}
        </div>
        <div class="row_right">
            <select name="val[active_base_knowledge]">
                <option value="1" {if $active_base_knowledge == 1}selected{/if}>True</option>
                <option value="0" {if $active_base_knowledge == 0}selected{/if}>False</option>
            </select>
        </div>
        <div class="clear"></div>
    </div>

    <div class="table">
        <div class="row_left">
            {phrase var='gettingstarted.define_the_limit_of_how_many_most_view_articles_in_block'}
        </div>
        <div class="row_right">
            <input type="text" name="val[number_of_most_view_article]" id="number_of_most_view_article" value="{$number_of_most_view_article}"/>
        </div>
        <div class="clear"></div>
    </div>
    
    <div class="table_clear">
        <input type="submit" value="Save Changes" class="button" name="save_change_global_setings"/>
    </div>
</form>

<script type="text/javascript">
    {literal}
    function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
    function checkValidate_AdSearch()
    {
        var number_of_article_category = $('#number_of_article_category').val();
        if(!isNumber(number_of_article_category) || number_of_article_category<=0)
        {
            alert('Number of article on each categories is invalid');
            return false;
        }
        var number_of_article = $('#number_of_article').val();
        if(!isNumber(number_of_article) || number_of_article<=0)
        {
            alert('Number of article is invalid');
            return false;
        }
        var number_of_letters = $('#number_of_letters').val();
        if(!isNumber(number_of_letters) || number_of_letters<=0)
        {
            alert('Number of letters is invalid');
            return false;
        }
        return true;
    }
    {/literal}
</script>