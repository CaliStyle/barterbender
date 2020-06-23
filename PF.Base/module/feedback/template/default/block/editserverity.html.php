<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
<style type="text/css">
	.popup_serverity
	{
		margin-left:100px;
	}

	.popup_serverity_button
	{
		margin-left: 100px;
		text-align: left;
	}
</style>
{/literal}
<form method="post" ENCTYPE="multipart/form-data" action="{url link='admincp.feedback.serverity'}">
<div class="table form-group">
    <input type="hidden" name="val[serverity_id]" value="{$aSer.serverity_id}" />
    <div class="table_left">
       {_p var='category_description'}
    </div>
    <div class="table_right popup_serverity">
        <input type="text" name="val[name]" value="{$aSer.name}" class="input" />
    </div>
    <div class="clear"></div>
</div>
<div class="table form-group">
    <div class="table_left">
     {_p var='pick_a_colour'}
    </div>
    <div class="table_right popup_serverity">
        <div id="jquery-colour-picker-example">
            <select name="val[colour]">
           <option value="{$aSer.colour}" selected="selected">#{$aSer.colour}</option>
            <option value="ffffff">#ffffff</option>
            <option value="ffccc9">#ffccc9</option>
            <option value="ffce93">#ffce93</option>
            <option value="fffc9e">#fffc9e</option>
            <option value="ffffc7">#ffffc7</option>
            <option value="9aff99">#9aff99</option>
            <option value="96fffb">#96fffb</option>
            <option value="cdffff">#cdffff</option>
            <option value="cbcefb">#cbcefb</option>
            <option value="cfcfcf">#cfcfcf</option>
            <option value="fd6864">#fd6864</option>
            <option value="fe996b">#fe996b</option>
            <option value="fffe65">#fffe65</option>
            <option value="fcff2f">#fcff2f</option>
            <option value="67fd9a">#67fd9a</option>
            <option value="38fff8">#38fff8</option>
            <option value="68fdff">#68fdff</option>
            <option value="9698ed">#9698ed</option>
            <option value="c0c0c0">#c0c0c0</option>
            <option value="fe0000">#fe0000</option>
            <option value="f8a102">#f8a102</option>
            <option value="ffcc67">#ffcc67</option>
            <option value="f8ff00">#f8ff00</option>
            <option value="34ff34">#34ff34</option>
            <option value="68cbd0">#68cbd0</option>
            <option value="34cdf9">#34cdf9</option>
            <option value="6665cd">#6665cd</option>
            <option value="9b9b9b">#9b9b9b</option>
            <option value="cb0000">#cb0000</option>
            <option value="f56b00">#f56b00</option>
            <option value="ffcb2f">#ffcb2f</option>
            <option value="ffc702">#ffc702</option>
            <option value="32cb00">#32cb00</option>
            <option value="00d2cb">#00d2cb</option>
            <option value="3166ff">#3166ff</option>
            <option value="6434fc">#6434fc</option>
            <option value="656565">#656565</option>
            <option value="9a0000">#9a0000</option>
            <option value="ce6301">#ce6301</option>
            <option value="cd9934">#cd9934</option>
            <option value="999903">#999903</option>
            <option value="009901">#009901</option>
            <option value="329a9d">#329a9d</option>
            <option value="3531ff">#3531ff</option>
            <option value="6200c9">#6200c9</option>
            <option value="343434">#343434</option>
            <option value="680100">#680100</option>
            <option value="963400">#963400</option>            
            <option value="646809">#646809</option>
            <option value="036400">#036400</option>
            <option value="34696d">#34696d</option>
            <option value="00009b">#00009b</option>
            <option value="303498">#303498</option>
            <option value="000000">#000000</option>
            <option value="330001">#330001</option>
            <option value="643403">#643403</option>
            <option value="663234">#663234</option>
            <option value="343300">#343300</option>
            <option value="013300">#013300</option>
            <option value="003532">#003532</option>
            <option value="010066">#010066</option>
            <option value="340096">#340096</option>
        </select>             
        </div>
         {literal}
        <script type="text/javascript">
        jQuery('#jquery-colour-picker-example select').colourPicker({
            ico:     "{/literal}{$core_path}{literal}module/feedback/static/image/jquery.colourPicker.gif",
            title:    false
        });
        </script>
      {/literal}
    </div>  
    <div class="clear"></div>
</div>
<div class="table form-group">
    <div class="table_left">
      {_p var='category_description'}
    </div>
    <div class="table_right popup_serverity">
        <textarea type="text" name="val[description]" cols="30" rows="5" >{$aSer.description}</textarea>
    </div>
    <div class="clear"></div>
</div>
<div class="table_clear popup_serverity_button">
	<input type="hidden" name="val[page]" value = "{$page}" />
    <input type="submit" name="editserverity" value="{_p var='save_changes'}" class="button" />
</div>
</form>