<?php


defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
<script language="JavaScript" type="text/javascript">
	$Behavior.initTodaylisting = function() {
		$("#js_from_date_listing").datepicker({
			// dateFormat: "@", // Unix timestamp
			dateFormat: 'mm/dd/yy',
			onSelect: function(dateText, inst) {
				var $dateTo = $("#js_to_date_listing").datepicker("getDate");
				var $dateFrom = $("#js_from_date_listing").datepicker("getDate");
				if($dateTo)
				{
					$dateTo.setHours(0);
					$dateTo.setMilliseconds(0);
					$dateTo.setMinutes(0);
					$dateTo.setSeconds(0);
				}
				
				if($dateFrom)
				{
					$dateFrom.setHours(0);
					$dateFrom.setMilliseconds(0);
					$dateFrom.setMinutes(0);
					$dateFrom.setSeconds(0);
				}
				
				if($dateTo && $dateFrom && $dateTo < $dateFrom) {
					tmp = $("#js_to_date_listing").val();
					$("#js_to_date_listing").val($("#js_from_date_listing").val());
					$("#js_from_date_listing").val(tmp);
				}
				return false;
			}
		});

		$("#js_to_date_listing").datepicker({
			// dateFormat: "@", // Unix timestamp
			dateFormat: 'mm/dd/yy',
			onSelect: function(dateText, inst) {
				var $dateTo = $("#js_to_date_listing").datepicker("getDate");
				var $dateFrom = $("#js_from_date_listing").datepicker("getDate");
				
				//$dateTo = $dateTo?$dateTo:(new Date());
				//$dateFrom = $dateFrom?$dateFrom:(new Date());
				if($dateTo)
				{
					$dateTo.setHours(0);
					$dateTo.setMilliseconds(0);
					$dateTo.setMinutes(0);
					$dateTo.setSeconds(0);
				}
				
				if($dateFrom)
				{
					$dateFrom.setHours(0);
					$dateFrom.setMilliseconds(0);
					$dateFrom.setMinutes(0);
					$dateFrom.setSeconds(0);
				}
				
				if($dateTo && $dateFrom && $dateTo < $dateFrom) {
					tmp = $("#js_to_date_listing").val();
					$("#js_to_date_listing").val($("#js_from_date_listing").val());
					$("#js_from_date_listing").val(tmp);
				}
				return false;
			}
		});
			
		$("#js_from_date_listing_anchor").click(function() {
			$("#js_from_date_listing").focus();
			return false;
		});
		
		$("#js_to_date_listing_anchor").click(function() {
			$("#js_to_date_listing").focus();
			return false;
		});
        
        $(".jsaction").find("a").each(function(){
			var $this = $(this);
			
			$this.click(function(evt) {
				evt.preventDefault();
				
				var $_this = $(this);
				var path = $_this.attr("href").split("/");
				var id = path[path.length - 3]
				
				$("select").val("");
				$("#search-category").val(id);
				setTimeout(function(){
					$("#frm_submitbtn").click();
				}, 1);
				
				return false;
			});
		});
        
        $("#js_mp_category_item_{/literal}{$iCategoryId}{literal}").attr({
			"selected": "selected"
		});
	}
</script>
{/literal}
<form style="margin-bottom:10px;" method="post" action="{url link='admincp.advancedmarketplace.todaylisting'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='advancedmarketplace.listing_filter'}
            </div>
        </div>
        <div class="panel-body">
             <div class="form-group">
                <label for="">{phrase var='advancedmarketplace.listing_name'}:</label>
                {$aFilters.listing}
            </div>
            <div class="form-group">
                <label for="">{phrase var='advancedmarketplace.owner_name'}:</label>
                {$aFilters.owner}
            </div>
            <div class="form-group">
                <label for="">{phrase var='advancedmarketplace.category'}:</label>
                <select name="search[category]" class="form-control" id="search-category">
                    <option value="">{phrase var='advancedmarketplace.select'}:</option>
                    {$sCategories}
                </select>
            </div>
            <div class="form-group" style="display: flex;flex-flow: wrap;">
                <div class="date-start" style="margin-right: 20px;">
                    <label  for="">{phrase var='advancedmarketplace.from_date'}: </label>
                    {select_date prefix='from_' start_year='-1' end_year='+1' field_separator=' / ' field_order='MDY' default_all=true}
                </div>
                <div class="date-end">
                    <label  for="">{phrase var='advancedmarketplace.to_date'}: </label>
                    {select_date prefix='to_' start_year='-1' end_year='+1' field_separator=' / ' field_order='MDY' default_all=true}
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <input type="submit" id="frm_submitbtn" name="search[submit]" value="{phrase var='core.submit'}" class="btn btn-primary" />
            <input type="submit" name="search[reset]" value="{phrase var='core.reset'}" class="btn btn-primary" />
            <div class="clear"></div>
        </div>
    </div>
</form>
{pager}

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='advancedmarketplace.today_listings'}
        </div>
    </div>

    {if count($aListings) > 0}
        <script lang="javascript" type="text/javascript">
            /* {literal} */
            $Behavior.advmarket_todaylistingaction = function(){
                $(".yn_popup___calendar_listing").click(function(evt){
                    evt.preventDefault();
                    $("#_submit-todaylisting-form").empty();
                    tb_show("{/literal}{phrase var="advancedmarketplace.today_listing" phpfox_squote=true}{literal}", $.ajaxBox('advancedmarketplace.todaylistingPopup', 'height=230&width=auto&id=' + $(this).parent().find(".yn_lid").val()));

                    return false;
                });
                $("#checkmeall").removeAttr("checked");
                $(".X_checkbox").removeAttr("checked");
                $("#checkmeall").click(function(evt) {
                    if($(this).is(":checked")){
                        $("#js_control").find("input[type=checkbox]").prop("checked","checked");
                        $("#deletebtn").removeClass("disabled");
                    } else {
                        $("#js_control").find("input[type=checkbox]").removeAttr("checked");
                        $("#deletebtn").addClass("disabled");
                    }
                });

                $(".X_checkbox").click(function() {
                    if($(".X_checkbox:checked").size() <=0 ) {
                        $("#checkmeall").removeAttr("checked");
                        $("#deletebtn").addClass("disabled");
                    } else {
                        $("#deletebtn").removeClass("disabled");
                    }
                });
                $("#deletebtn").click(function(evt) {
                    evt.preventDefault();

                    var $this = $(this);
                    if($this.hasClass("disabled")) return false;

                    $Core.jsConfirm({}, function() {
                      var $form = $("<form>");
                      $form.append($(".X_checkbox").clone());

                      $form.ajaxCall("advancedmarketplace.deleteTodayListings");
                    }, function() {});

                    return false;
                });

            }
            // $Core.init();
            /* {/literal} */
        </script>
    <div class="panel-body">
    <div class="table-responsive flex-sortable">
        <table class="table table-bordered js_drag_drop_" id="js_control">
            <thead>
            <tr>
                <th class="t_center">
                    <input type="checkbox" id="checkmeall" value=""/>
                </th>
                <th></th>
                <th>
                    {phrase var="advancedmarketplace.listing_name"}
                </th>
                <th>
                    {phrase var='user.user'}
                </th>
                <th>
                    {phrase var='advancedmarketplace.category'}
                </th>
                <th class="t_center">{_p var='advancedmarketplace_displayed_date'}</th>
                <th class="t_center">{_p var='advancedmarketplace_no_of_displayed_times'}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$aListings key=iKey item=aBlock}
            <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                <td class="t_center">
                    <input name="deleteitem[]" class="X_checkbox" type="checkbox" value="{$aBlock.listing_id}"/>
                </td>
                <td align="center">
                    <a href="#" class="js_drop_down_link" title="{phrase var='ad.manage'}"></a>
                    <div class="link_menu">
                        <ul>
                            <li><a href="{url link='admincp.advancedmarketplace.todaylisting' delete=$aBlock.listing_id}" class="sJsConfirm">{phrase var='delete'}</a></li>
                        </ul>
                    </div>
                </td>
                <td>
                    <a href="{url link='advancedmarketplace.detail.'.{$aBlock.listing_id}{$aBlock.title}">{$aBlock.title}</a>
                </td>
                <td>{$aBlock.full_name}</td>
                <td class="jsaction">{$aBlock.categories|category_display}</td>
                <td class="t_center">{$aBlock.most_recent_date}</td>
                <td class="t_center">{$aBlock.total_dates}</td>
                <td>
                    <a href="javascript:void(0);" class="yn_popup___calendar_listing">
                        <input type="hidden" value="{$aBlock.listing_id}" name="yn_lid" class="yn_lid" />
                        <img src="<?php echo Phpfox::getLib('template')->getStyle('image', 'jquery/calendar.gif'); ?>" />
                    </a>
                </td>

            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    {else}
        {if $bIsSearch}
        <div class="form-group t_center">
            <div class="extra_info">{phrase var='advancedmarketplace.no_listings_found'}</div>
        </div>
        {else}
        <div class="form-group t_center">
            <div class="extra_info">{phrase var='advancedmarketplace.no_listings_have_been_created'}</div>
        </div>
        {/if}
    {/if}
    {pager}
    </div>
    <div class="panel-footer">
        <input id="deletebtn" type="submit" class="btn btn-primary disabled" value="{phrase var='advancedmarketplace.delete_selected'}">
    </div>
</div>