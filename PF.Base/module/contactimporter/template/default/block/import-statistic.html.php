<?php
defined('PHPFOX') or die('NO DICE!');
?>
<ul class="action">
 <li>
    <ul>
    {foreach from=$aStatistics item=aStatistic}
      {if $aStatistic.total}
        <li><a>{_p var='x_y_for_social' x=$aStatistic.total y=$iTotal social=$aStatistic.title}</font></a></li>
      {/if}
    {/foreach}
    </ul>
 </li> 
</ul>