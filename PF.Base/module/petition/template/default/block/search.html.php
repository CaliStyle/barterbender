<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
<style type="text/css">
    /*** Search Block***/
    table.search_table tr td{
        padding: 2px 0;
    }
</style>
<style type="text/css">
    .petition_date_picker{
        background: url({/literal}{$corepath}{literal}module/petition/static/image/calendar.gif) no-repeat top left;
    }
</style>
{/literal}
<div class="petition_date_picker"></div>
<form id="petition_search_form"
      method="post"
      action="{$core_path}petition/?bIsAdvSearch=1"
    >
    <input type="hidden" value="1" name="search[flag_advancedsearch]"/>
    <table class="search_table" border="0" cellpadding="0" cellspacing="5">
        <tr>
            <td>{phrase var='petition.keywords'}:</td>
        </tr>
        <tr>
            <td>
                <input id="search_keywords" type="text"
                       name="search[keywords]"
                       class="search_keyword form-control"
                       value="{if isset($aSearchTool.search.actual_value)}{$aSearchTool.search.actual_value|clean}{else}{$aSearchTool.search.default_value}{/if}"
                       onfocus="if($('#search_keywords').val()=='{phrase var='petition.search_petition_dot'}'){l}$('#search_keywords').val('');{r}"
                       onblur="if($('#search_keywords').val()==''){l}$('#search_keywords').val('{phrase var='petition.search_petition_dot'}');{r}">
            </td>
        </tr>

        <tr>
            <td>{phrase var='petition.category'}:</td>
        </tr>
        <tr>
            <td>
                <select class="form-control" id="search_category"
                        name="search[category][search_0]"
                    >
                <option {if $iCategoryPetitionView==0}selected{/if} value="0">{phrase var='petition.all'}</option>
                {foreach from=$aCategories item=aCategory}
                    <option {if $iCategoryPetitionView==$aCategory.category_id}selected{/if} value="{$aCategory.category_id}">{$aCategory.name}</option>
                {/foreach}
                </select>
            </td>
        </tr>
        {if $sView != 'pending'}
        <tr>
            <td>{phrase var='petition.petition_status'}:</td>
        </tr>
        <tr>
            <td>
                <select class="form-control" id="search_status"
                        name="search[status]"
                    >
                <option {if $iStatus==0}selected{/if} value="0">{phrase var='petition.all'}</option>
                <option {if $iStatus==1}selected{/if} value="1">{phrase var='petition.closed'}</option>
                <option {if $iStatus==2}selected{/if} value="2">{phrase var='petition.on_going'}</option>
                <option {if $iStatus==3}selected{/if} value="3">{phrase var='petition.victory'}</option>
                </select>
            </td>
        </tr>
        {/if}
        <tr>
            <td>
                <input
                       name="search[search_by_date]"
                       id="chk_search_by_date"
                       type="checkbox"
                       onclick="$('.search_by_date').toggle();"/> {phrase var='petition.search_by_date'}.
            </td>
        </tr>
        <tr>
        <tr class="search_by_date">
            <td>{phrase var='petition.from'}:</td>
        </tr>
        <tr class="search_by_date">
            <td>
                <div style="position: relative;">


                    {select_date
                    prefix='start_'
                    id='_start'
                    start_year='current_year'
                    end_year='+1'
                    field_separator=' / ' field_order='MDY'
                    default_all=true
                    time_separator='event.time_separator'}


                    <!-- <div class="js_datepicker_image" id="start_time_picker" style="cursor: pointer; margin-left: 5px;"></div> -->
                </div>
            </td>
        </tr>

        <tr class="search_by_date">
            <td>{phrase var='petition.to'}:</td>
        </tr>
        <tr class="search_by_date">
            <td>
                <div style="position: relative;">
                    {select_date
                    prefix='end_'
                    id='_end'
                    start_year='current_year'
                    end_year='+1'
                    field_separator=' / ' field_order='MDY'
                    default_all=true
                    time_separator='event.time_separator'}
                    <!-- <div class="js_datepicker_image" id="end_time_picker" style="cursor: pointer; margin-left: 5px;"></div> -->
                </div>
            </td>
        </tr>
    </table>

    <div class="p_top_8">
        <input name="search[submit]" value="{phrase var='petition.submit'}"
               class="btn btn-primary btn-sm" type="submit" />
    </div>
</form>
{literal}
<script type="text/javascript">
    $Core.remakePostUrl = (function(){

    });
   $Behavior.setDateTimePicker = (function(){
      $('#end_time_search').datepicker({
          dateFormat: "mm/dd/yy"
      });
      $('#end_time_picker').click(function(){$('#end_time_search').focus();});
      $('#start_time_search').datepicker({
          dateFormat: "mm/dd/yy"
      });
      $('#start_time_picker').click(function(){$('#start_time_search').focus();});

      if('{/literal}{$iChecked}{literal}' == 'true')
      {
          $('#chk_search_by_date').attr('checked','checked');
          $('.search_by_date').show();
      }
      else{
          $('.search_by_date').hide();
      }
  });
</script>
{/literal}
