<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: price.html.php 3533 2011-11-21 14:07:21Z Raymond_Benc $
 */
defined('PHPFOX') or exit('NO DICE!');

// var_dump($this->_aVars["aCustomFieldGroups"]);
?>
<form id="jh_yn_cusfield_submitform" method="post" action=".">
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="white_rows table table-bordered">
                <tr>
                    <td width="30%">
                        <ul id="yn_jh_customgroup_listing">
                            {foreach from=$aCustomFieldGroups key=iKey item=aGroup}
                            {module name="advancedmarketplace.admincp.customfieldgroup" lid=$aGroup.category_id sKeyVar=$aGroup.var_name sText=$aGroup.text is_active=$aGroup.is_active}
                            {/foreach}
                            <li id="yn_jh_anchor_addcf">
                                <a id="yn_jh_add_cusgroup" title="Add a group" href="javascript:void(0)"
                                   onclick="$.ajaxCall('advancedmarketplace.addCustomFieldGroup', 'lid={$iListingId}'); return false;">
                                    {_p('add_a_group')}...</a>
                            </li>
                        </ul>
                    </td>
                    <td width="70%" id="yn_jh_groupcustomfields">
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>
{literal}
<script lang="javascript" type="text/javascript">
    $Behavior.onCustomFieldAction = function() {
        $(".customrow").each(function (/* index */) {
            setGroupInterfaceActions($(this));
        });
    }

	/* aCustomFieldInfors */
	{/literal}
	var fieldInfors = new Array();
	{foreach from=$aCustomFieldInfors key=iKey item=aInfor}
		fieldInfors["{$iKey}"] = {ldelim}
			"tag": "<?php echo str_replace("\"", "\\\"", $this->_aVars["aInfor"]["tag"]); ?>",
			"sub_tags": "<?php echo str_replace("\"", "\\\"", $this->_aVars["aInfor"]["sub_tags"]); ?>"
		{rdelim};
	{/foreach}
	{literal}
</script>
<style type="text/css">
    .ajxloader {
        display: inline-block;
        display: none;
    }

    #jh_yn_cusfield_submitform #yn_jh_customgroup_listing input {
		text-decoration: underline;
		cursor: pointer;
    }

    #jh_yn_cusfield_submitform #yn_jh_customgroup_listing input.changed {
		text-decoration: none;
		cursor: text!important;
    }

    #jh_yn_cusfield_submitform #yn_jh_customgroup_listing .customrow {
        padding: 5px;
    }
    .btn1 {
        background-repeat: no-repeat;
        display: inline-block;
        height: 12px;
        text-decoration: none;
        width: 12px;
        filter:alpha(opacity=50);
        opacity: 0.5;
        background-color: transparent;
        margin: 5px;
    }

    .btn1:hover {
        filter:alpha(opacity=100);
        opacity: 1;
    }

    .edit {
        background-position: 0 0;
        background-image: url("{/literal}{param var='core.path_actual'}{literal}PF.Site/Apps/p-advmarketplace/assets/image/default/manibuton.png");
    }

    .delete {
        background-position: 0 100%;
        background-image: url("{/literal}{param var='core.path_actual'}{literal}PF.Site/Apps/p-advmarketplace/assets/image/default/manibuton.png");
    }

    .save {
        background-image: url("{/literal}{param var='core.path_actual'}{literal}PF.Site/Apps/p-advmarketplace/assets/image/default/b_save.png");
		display: none;
    }

    .down {
        background-image: url("{/literal}{param var='core.path_actual'}{literal}PF.Site/Apps/p-advmarketplace/assets/image/default/updownnav.png");
        background-position: 0 -60%;
    }

    .up {
        background-image: url("{/literal}{param var='core.path_actual'}{literal}PF.Site/Apps/p-advmarketplace/assets/image/default/updownnav.png");
        background-position: 0 120%;
    }

    .bullet {
        background-image: url("{/literal}{param var='core.path_actual'}{literal}PF.Site/Apps/p-advmarketplace/assets/image/default/bulletonoff.png");
		width: 12px;
		height: 12px;
		display: inline-block;
		text-decoration: none;
    }
	
	.on {
		background-position: 0 0;
	}
	
	.off {
		background-position: 0 100%;
	}

    #jh_yn_cusfield_submitform #yn_jh_groupcustomfields .sub_tags {
        padding-top: 10px;
		display: none;
    }

    #jh_yn_cusfield_submitform #yn_jh_groupcustomfields .anoption {
        padding-bottom: 5px;
    }

    table.white_rows {
        border-bottom: 1px solid #B3B3B3;
        border-right: 1px solid #B3B3B3;
    }

    table.white_rows td {
        border-top: 1px solid #B3B3B3;
        border-left: 1px solid #B3B3B3;
        background-color: #FFFFFF;
    }

    table.gray_rows {
        border-bottom: 1px solid #B3B3B3;
        border-right: 1px solid #B3B3B3;
    }

    table.gray_rows td,th {
        border-top: 1px solid #B3B3B3;
        border-left: 1px solid #B3B3B3;
        background-color: #FFFFFF;
    }
</style>
{/literal}