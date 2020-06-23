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
// echo Phpfox::getLib("locale")->getLangId();
?>

<form id="_submit-todaylisting-form" action="#" method="post">
	<input type="hidden" name="id" value="{$iId}" />
	{foreach from=$aTListing key=iKey item=tListing}
		<input type="hidden" class="ctls" id="todaylisting-item-{$tListing.time_stamp}" name="todaylistingitem[nas_{$tListing.time_stamp}]" value="{$tListing.time_stamp}"/>
	{/foreach}
</form>
<div id="todaypicker{$iId}"></div>
<br>
<input type="button" id="today-listing-btnr" class="btn btn-primary" value="{phrase var="advancedmarketplace.save_changes"}" />
<script language="javascript" type="text/javascript">
	{literal}
    $Behavior.onClickDate = function () {
        submit_todaylisting_form = $("#_submit-todaylisting-form");
        dates{/literal}{$iId}{literal} = new Array();
        function addDate{/literal}{$iId}{literal}(date) {if ($.inArray(date, dates{/literal}{$iId}{literal}) < 0) dates{/literal}{$iId}{literal}.push(date);}
        function removeDate{/literal}{$iId}{literal}(index) {dates{/literal}{$iId}{literal}.splice(index, 1);}

        // Adds a date if we don't have it yet, else remove it
        function addOrRemoveDate{/literal}{$iId}{literal}(date)
        {
            var index = $.inArray(date, dates{/literal}{$iId}{literal});
            if (index >= 0) {
                removeDate{/literal}{$iId}{literal}(index);
                remInputValue{/literal}{$iId}{literal}(date);
            } else {
                addDate{/literal}{$iId}{literal}(date);
                addInputValue{/literal}{$iId}{literal}(date);
            }
        }

        addInputValue{/literal}{$iId}{literal} = function(value){
            var input = $("<input>").attr({
                "type": "hidden",
                "id": ("todaylisting-item-" + value),
                "name": "todaylistingitem[nas_" + value + "]"
            }).val(value);

            submit_todaylisting_form.append(input);
        }

        var remInputValue{/literal}{$iId}{literal} = function(value){
            submit_todaylisting_form.find("#" + ("todaylisting-item-" + value)).remove();
        }

        $(".ctls").each(function(index){
            var $this = $(this);

            addDate{/literal}{$iId}{literal}($this.val());
        });
        let datePicker = $('#todaypicker{/literal}{$iId}{literal}');
        datePicker.datepicker({
            dateFormat: "@", // Unix timestamp
            onSelect: function(dateText, inst) {
                var offset = (new Date()).getTimezoneOffset();
                dateText = (parseInt(dateText) - offset * 60000) + '';

                addOrRemoveDate{/literal}{$iId}{literal}(dateText);

                var gotDate = $.inArray(dateText, dates{/literal}{$iId}{literal});
            },
            beforeShowDay: function (date) {
                var cdate = (date.getTime() - (new Date()).getTimezoneOffset() * 60000),
                    currentDate = new Date();

                currentDate.setHours(0);
                currentDate.setMilliseconds(0);
                currentDate.setMinutes(0);
                currentDate.setSeconds(0);

                var gotDate = $.inArray(cdate + "", dates{/literal}{$iId}{literal});
                if(date.getTime() < currentDate.getTime()) {
                    return [false,""];
                }
                if (gotDate >= 0) {
                    // Enable date so it can be deselected. Set style to be highlighted
                    return [true,"css-highlight"];
                }

                return [true,"css-highlight-off"];
            }
        });
        let nextDisplayedMonth = parseInt({/literal}{$mostRecentMonth}{literal}) - 1;
        let displayedYear = parseInt({/literal}{$mostRecentYear}{literal});
        if(nextDisplayedMonth >= 0 && nextDisplayedMonth <= 11){
            let setMonth = datePicker.datepicker('getDate');
            setMonth.setMonth(nextDisplayedMonth);
            setMonth.setFullYear(displayedYear);
            datePicker.datepicker("setDate", setMonth);
        }
    }
	{/literal}
</script>

<style type="text/css">
	{literal}

	.css-highlight {
		/* background: #E78F08 url('images/ui-bg_highlight-soft_75_ffe45c_1x100.png') 50% top repeat-x!important; */
	}
	.css-highlight a {
		border: 1px solid #E78F08!important;
		/* background: #ffffff url(images/ui-bg_glass_65_ffffff_1x400.png) 50% 50% repeat-x; */
		/* background: url("images/ui-bg_glass_100_fdf5ce_1x400.png") repeat-x scroll 50% 50% #F6F6F6!important; */
		font-weight: bold!important;
		color: #EB8F00!important;
		background-color: #FFFFFF;
	}
	.css-highlight-off {
		background: none!important;
		background-color: #FFFFFF!important;
	}
	.ui-state-highlight, .ui-widget-content .ui-state-highlight, .ui-widget-header .ui-state-highlight {
		border-color: #CCCCCC;
	}
	.css-highlight-off a {
		/* background: #ffffff url(images/ui-bg_glass_65_ffffff_1x400.png) 50% 50% repeat-x; */
		/* background: url("images/ui-bg_glass_100_fdf5ce_1x400.png") repeat-x scroll 50% 50% #F6F6F6!important; */
		color: #1C94C4!important;
		border: 1px solid #CCCCCC!important;
	}
	.ui-datepicker-today a {
		background: none!important;
	}
	body[id^=page_advancedmarketplace_admincp_] .js_box_content .ui-datepicker{
		width: auto;
		margin-bottom: 15px;
	}
	{/literal}
</style>