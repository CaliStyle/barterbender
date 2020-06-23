<?php
/**
 * [ContactImporter]
 * [ContactImporter]
 *
 * @copyright		[Younetco]

 * @package 		Phpfox
 * @version 		1.0
 */

defined('PHPFOX') or exit('NO DICE!');
?>

{literal}
<script type="text/javascript">
$Behavior.loadJsCookie = function(){
;(function($,document,undefined){var pluses=/\+/g;function raw(s){return s;}
function decoded(s){return decodeURIComponent(s.replace(pluses,' '));}
var config=$.cookie=function(key,value,options){if(value!==undefined){options=$.extend({},config.defaults,options);if(value===null){options.expires=-1;}
if(typeof options.expires==='number'){var days=options.expires,t=options.expires=new Date();t.setDate(t.getDate()+days);}
value=config.json?JSON.stringify(value):String(value);return(document.cookie=[encodeURIComponent(key),'=',config.raw?value:encodeURIComponent(value),options.expires?'; expires='+options.expires.toUTCString():'',options.path?'; path='+options.path:'',options.domain?'; domain='+options.domain:'',options.secure?'; secure':''].join(''));}
var decode=config.raw?raw:decoded;var cookies=document.cookie.split('; ');for(var i=0,parts;(parts=cookies[i]&&cookies[i].split('='));i++){if(decode(parts.shift())===key){var cookie=decode(parts.join('='));return config.json?JSON.parse(cookie):cookie;}}
return null;};config.defaults={};$.removeCookie=function(key,options){if($.cookie(key)!==null){$.cookie(key,null,options);return true;}
return false;};})(jQuery,document);
;
};
</script>
{/literal}
 
{literal}

<script type="text/javascript">
$Behavior.loadJsIndexController = function(){
$.cookie('contactimporter.selected', null);
if (opener)
{
    if( opener.location.href == self.location.href ){
    
    }else{            
    }
};
 function setWaiting()
 {

     ele = document.getElementById('div_list_view').style.backgroundImage = "{/literal}{$core_url}{literal}module/contactimporter/static/image/loading.gif";
 }
  function unsetWaiting()
 {

     ele = document.getElementById('div_list_view').style.backgroundImage = "";
 }
}
 </script>
  {/literal}
  {literal}
<script type="text/javascript">
</script>
 {/literal}
  <script type="text/javascript" src="{$core_url}module/contactimporter/static/jscript/contactimporter.js"></script>
{if isset($is_linkedAPI) != 'is_linkedAPI'}
	<div style="display:none; margin:0 auto; text-align:center; background:url({$core_url}module/contactimporter/static/image/loading.gif) no-repeat;width:320px;height:320px; padding-top:180px;" id="loading">
		<div style="text-align:center; ">{_p var='sending_request'}</div>
	</div>
{literal}
<script type="text/javascript">
    var newwindow;
</script>
{/literal}
{if ($step !='get_invite' and $step !='add_contact') or (count($errors) > 0 and  $errors != '')}
	<!--Global variable JS -->
 <div id="import_form">
    <!-- Imort email contact list !-->
    {template file='contactimporter.block.import_contact'}                 
    <!-- Imort email contact list !-->
 </div>
 {/if}
 <?php Phpfox::clearMessage(); ?>
 {if $step == 'add_contact'}
  	{template file='contactimporter.block.get_contact'}
 {/if}
 {if $step == 'get_invite'}
  	{if $plugType == 'social'}
  		{template file='contactimporter.controller.social'}
  	{/if}
  	{if $plugType == 'email'}
  		{template file='contactimporter.controller.email'}
  	{/if}
  	{if $plugType == 'openinviter'}
  		{template file='contactimporter.block.get_contact'}
  	{/if}
  	{if $plugType == ''}
  		{template file='contactimporter.block.get_invite'}
  	{/if}
{/if}
{else}
    {template file='contactimporter.block.get_contact_api'}
{/if}