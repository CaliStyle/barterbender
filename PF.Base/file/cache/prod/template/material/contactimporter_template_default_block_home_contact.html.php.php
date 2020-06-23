<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<?php

?>

<?php if (! isset ( $this->_aVars['sHidden'] )):  $this->assign('sHidden', '');  endif; ?>

<?php if (( isset ( $this->_aVars['sHeader'] ) && ( ! PHPFOX_IS_AJAX || isset ( $this->_aVars['bPassOverAjaxCall'] ) || isset ( $this->_aVars['bIsAjaxLoader'] ) ) ) || ( defined ( "PHPFOX_IN_DESIGN_MODE" ) && PHPFOX_IN_DESIGN_MODE )): ?>

<div class="<?php echo $this->_aVars['sHidden']; ?> block<?php if (( defined ( 'PHPFOX_IN_DESIGN_MODE' ) ) && ( ! isset ( $this->_aVars['bCanMove'] ) || ( isset ( $this->_aVars['bCanMove'] ) && $this->_aVars['bCanMove'] == true ) )): ?> js_sortable<?php endif;  if (isset ( $this->_aVars['sCustomClassName'] )): ?> <?php echo $this->_aVars['sCustomClassName'];  endif; ?>"<?php if (isset ( $this->_aVars['sBlockBorderJsId'] )): ?> id="js_block_border_<?php echo $this->_aVars['sBlockBorderJsId']; ?>"<?php endif;  if (defined ( 'PHPFOX_IN_DESIGN_MODE' ) && Phpfox_Module ::instance()->blockIsHidden('js_block_border_' . $this->_aVars['sBlockBorderJsId'] . '' )): ?> style="display:none;"<?php endif; ?> data-toggle="<?php echo $this->_aVars['sToggleWidth']; ?>">
<?php if (! empty ( $this->_aVars['sHeader'] ) || ( defined ( "PHPFOX_IN_DESIGN_MODE" ) && PHPFOX_IN_DESIGN_MODE )): ?>
		<div class="title <?php if (defined ( 'PHPFOX_IN_DESIGN_MODE' )): ?>js_sortable_header<?php endif; ?>">
<?php if (isset ( $this->_aVars['sBlockTitleBar'] )): ?>
<?php echo $this->_aVars['sBlockTitleBar']; ?>
<?php endif; ?>
<?php if (( isset ( $this->_aVars['aEditBar'] ) && Phpfox ::isUser())): ?>
			<div class="js_edit_header_bar">
				<a href="#" title="<?php echo _p('edit_this_block'); ?>" onclick="$.ajaxCall('<?php echo $this->_aVars['aEditBar']['ajax_call']; ?>', 'block_id=<?php echo $this->_aVars['sBlockBorderJsId'];  if (isset ( $this->_aVars['aEditBar']['params'] )):  echo $this->_aVars['aEditBar']['params'];  endif; ?>'); return false;">
					<span class="ico ico-pencilline-o"></span>
				</a>
			</div>
<?php endif; ?>
<?php if (empty ( $this->_aVars['sHeader'] )): ?>
<?php echo $this->_aVars['sBlockShowName']; ?>
<?php else: ?>
<?php echo $this->_aVars['sHeader']; ?>
<?php endif; ?>
		</div>
<?php endif; ?>
<?php if (isset ( $this->_aVars['aEditBar'] )): ?>
	<div id="js_edit_block_<?php echo $this->_aVars['sBlockBorderJsId']; ?>" class="edit_bar hidden"></div>
<?php endif; ?>
<?php if (isset ( $this->_aVars['aMenu'] ) && count ( $this->_aVars['aMenu'] )): ?>
<?php unset($this->_aVars['aMenu']); ?>
<?php endif; ?>
	<div class="content"<?php if (isset ( $this->_aVars['sBlockJsId'] )): ?> id="js_block_content_<?php echo $this->_aVars['sBlockJsId']; ?>"<?php endif; ?>>
<?php endif; ?>
		<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Blog
 * @version 		$Id: top.html.php 1318 2009-12-14 22:34:04Z Raymond_Benc $
 */
 
 

?>
<div style="display:none; margin-left:250px; background:url(<?php echo $this->_aVars['core_url']; ?>module/contactimporter/static/image/loading_small.gif) no-repeat;width:320px;height:100px;" id="loading">
	<div style="text-align:left;padding-top:50px;padding-left:-20px; "><?php echo _p('sending_request'); ?></div>
</div>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_aVars['core_url']; ?>module/contactimporter/static/css/default/default/jquery.autocomplete.css" />
<script  type="text/javascript" src="<?php echo $this->_aVars['core_url']; ?>module/contactimporter/static/jscript/jquery.autocomplete.js" /></script>

<?php echo '
<style type="text/css">
    #homecontact .logoContact {
		float:left;
		height:';  echo $this->_aVars['icon_size'];  echo 'px;
        width:';  echo $this->_aVars['icon_size']; ?> + 10<?php echo 'px;
		padding:0px 12px 4px 0px;
    }
    #homecontact .logoContact img,#homecontact .logoContact a {
		display:block;
        height:';  echo $this->_aVars['icon_size'];  echo 'px;
        width:';  echo $this->_aVars['icon_size']; ?> + 10<?php echo 'px;
    }
</style>

<script type="text/javascript">
	window.fbAsyncInit = function()
	{
		FB.init({
			appId : \'';  echo $this->_aVars['fbAIP'];  echo '\',
			xfbml : true,
			version : \'v2.0\'
		});
		open_facebook_invite_dialog();
	};

	(function(d, s, id)
	{
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) {
			return;
		}
		js = d.createElement(s);
		js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	}
	(document, \'script\', \'facebook-jssdk\'));

	function open_facebook_invite_dialog() {
		if ( typeof FB == \'undefined\')
			return;
	}

    function invite_facebook_open() {
        var date = new Date();
        var timestamp = date.getTime();
        FB.ui({
            method: \'send\',
            link: \'';  echo $this->_aVars['facebookInviteLink'];  echo '\',
        }, function (res) {
            if (res.success) {
                $.ajaxCall(\'contactimporter.fbInviteSuccessfull\');
            }
        });
    }

</script>
'; ?>

<center>
<div id="homecontact">
<table width="100%" border="0"><tr><td align="center">
<?php if (count((array)$this->_aVars['top_5_email'])):  foreach ((array) $this->_aVars['top_5_email'] as $this->_aVars['email']):  if ($this->_aVars['email']['logo'] != ''): ?>
<?php if ($this->_aVars['email']['name'] == 'yahoo'): ?>
		<div class="logoContact">
		   <a id="yahoo" href="#?call=contactimporter.callYahoo&amp;height=80&amp;width=270"  class=" inlinePopup usingapi"  title="<?php echo _p('yahoo_contacts'); ?>">
				<img alt="<?php echo $this->_aVars['email']['title']; ?>" title="<?php echo $this->_aVars['email']['title']; ?>" src="<?php echo $this->_aVars['core_url']; ?>module/contactimporter/static/image/<?php echo $this->_aVars['email']['logo']; ?>_status_up.png" />
		   </a>
		</div>
<?php elseif ($this->_aVars['email']['name'] == 'gmail'): ?>
		<div class="logoContact">
			<a id="gmail" href="#?call=contactimporter.callGmail&amp;height=80&amp;width=270" class="inlinePopup usingapi" title="<?php echo _p('gmail_authorization'); ?>">
				<img alt="<?php echo $this->_aVars['email']['title']; ?>" title="<?php echo $this->_aVars['email']['title']; ?>" src="<?php echo $this->_aVars['core_url']; ?>module/contactimporter/static/image/<?php echo $this->_aVars['email']['logo']; ?>_status_up.png"/>
			</a>
		</div>
<?php elseif ($this->_aVars['email']['name'] == 'hotmail'): ?>
		<div class="logoContact">
			<a id="hotmail" href="#?call=contactimporter.callHotmail&amp;height=80&amp;width=270" class="inlinePopup usingapi" title="<?php echo _p('hotmail_authorization'); ?>">
				<img alt="<?php echo $this->_aVars['email']['title']; ?>" title="<?php echo $this->_aVars['email']['title']; ?>" src="<?php echo $this->_aVars['core_url']; ?>module/contactimporter/static/image/<?php echo $this->_aVars['email']['logo']; ?>_status_up.png"/>
			</a>
		</div>
<?php elseif ($this->_aVars['email']['name'] == 'linkedin'): ?>
		<div class="logoContact">
		   <a id="linkedinA" href="#?call=contactimporter.callLinkedIn&amp;height=80&amp;width=270" class="inlinePopup usingapi" title="<?php echo _p('linkedin_authorization'); ?>">
			 <img alt="<?php echo $this->_aVars['email']['title']; ?>" title="<?php echo $this->_aVars['email']['title']; ?>"  src="<?php echo $this->_aVars['core_url']; ?>module/contactimporter/static/image/<?php echo $this->_aVars['email']['logo']; ?>_status_up.png" />
			</a>
		</div>
<?php elseif ($this->_aVars['email']['name'] == 'twitter'): ?>
	<div class="logoContact">
		<a id="twitterA" href="#?call=contactimporter.callTwitter&amp;height=80&amp;width=270" class="inlinePopup usingapi" title="<?php echo _p('twitter_authorization'); ?>">
			<img alt="<?php echo $this->_aVars['email']['title']; ?>" title="Twitter"  src="<?php echo $this->_aVars['core_url']; ?>module/contactimporter/static/image/<?php echo $this->_aVars['email']['logo']; ?>_status_up.png"/>
		</a>
	</div>
<?php elseif ($this->_aVars['email']['name'] == 'facebook_'): ?>
<?php if ($this->_aVars['fbAIP']): ?>
		<div class="logoContact">
			<a id="fbApi" href="javascript: invite_facebook_open()" class="inlinePopup usingapi" title="<?php echo _p('facebook_authorization'); ?>">
				<img alt="<?php echo $this->_aVars['email']['title']; ?>" title="Facebook"  src="<?php echo $this->_aVars['core_url']; ?>module/contactimporter/static/image/<?php echo $this->_aVars['email']['logo']; ?>_status_up.png"/>
			</a>
		</div>
<?php endif; ?>
<?php else: ?>
	<div class="logoContact">
		<a title="<?php echo _p('import_your_contacts'); ?>" href="#?call=contactimporter.callImporterForm&amp;height=150&amp;width=400&amp;provider_type=<?php echo $this->_aVars['email']['type']; ?>&amp;default_domain=<?php echo $this->_aVars['email']['default_domain']; ?>&amp;provider_box=<?php echo $this->_aVars['email']['name']; ?>" class="inlinePopup">
			<img alt="<?php echo $this->_aVars['email']['title']; ?>" title="<?php echo $this->_aVars['email']['title']; ?>"  src="<?php echo $this->_aVars['core_url']; ?>module/contactimporter/static/image/<?php echo $this->_aVars['email']['logo']; ?>_status_up.png">
		</a>
	</div>
<?php endif;  endif;  endforeach; endif; ?>
</td></tr></table>
<div style="clear:both;width:100%;display:block"></div>
<span style="display:block;text-align: right;margin-top: 10px;"><a alt="<?php echo _p('view_all_of_providers'); ?>" title="<?php echo _p('view_all_of_providers'); ?>" href="<?php echo $this->_aVars['more_path']; ?>"><?php echo _p('view_more'); ?> &raquo;</a></span>
</div>
<div style="clear:both;width:100%;display:block"></div>
</center>



<?php if (( isset ( $this->_aVars['sHeader'] ) && ( ! PHPFOX_IS_AJAX || isset ( $this->_aVars['bPassOverAjaxCall'] ) || isset ( $this->_aVars['bIsAjaxLoader'] ) ) ) || ( defined ( "PHPFOX_IN_DESIGN_MODE" ) && PHPFOX_IN_DESIGN_MODE )): ?>
	</div>
<?php if (isset ( $this->_aVars['aFooter'] ) && count ( $this->_aVars['aFooter'] )): ?>
	<div class="bottom">
<?php if (count ( $this->_aVars['aFooter'] ) == 1): ?>
<?php if (count((array)$this->_aVars['aFooter'])):  $this->_aPhpfoxVars['iteration']['block'] = 0;  foreach ((array) $this->_aVars['aFooter'] as $this->_aVars['sPhrase'] => $this->_aVars['sLink']):  $this->_aPhpfoxVars['iteration']['block']++; ?>

<?php if ($this->_aVars['sLink'] == '#'): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'ajax/add.gif','class' => 'ajax_image')); ?>
<?php endif; ?>
<?php if (is_array ( $this->_aVars['sLink'] )): ?>
            <a class="btn btn-block <?php if (! empty ( $this->_aVars['sLink']['class'] )): ?> <?php echo $this->_aVars['sLink']['class'];  endif; ?>" href="<?php if (! empty ( $this->_aVars['sLink']['link'] )):  echo $this->_aVars['sLink']['link'];  else: ?>#<?php endif; ?>" <?php if (! empty ( $this->_aVars['sLink']['attr'] )):  echo $this->_aVars['sLink']['attr'];  endif; ?> id="js_block_bottom_link_<?php echo $this->_aPhpfoxVars['iteration']['block']; ?>"><?php echo $this->_aVars['sPhrase']; ?></a>
<?php else: ?>
            <a class="btn btn-block" href="<?php echo $this->_aVars['sLink']; ?>" id="js_block_bottom_link_<?php echo $this->_aPhpfoxVars['iteration']['block']; ?>"><?php echo $this->_aVars['sPhrase']; ?></a>
<?php endif; ?>
<?php endforeach; endif; ?>
<?php else: ?>
		<ul>
<?php if (count((array)$this->_aVars['aFooter'])):  $this->_aPhpfoxVars['iteration']['block'] = 0;  foreach ((array) $this->_aVars['aFooter'] as $this->_aVars['sPhrase'] => $this->_aVars['sLink']):  $this->_aPhpfoxVars['iteration']['block']++; ?>

				<li id="js_block_bottom_<?php echo $this->_aPhpfoxVars['iteration']['block']; ?>"<?php if ($this->_aPhpfoxVars['iteration']['block'] == 1): ?> class="first"<?php endif; ?>>
<?php if ($this->_aVars['sLink'] == '#'): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'ajax/add.gif','class' => 'ajax_image')); ?>
<?php endif; ?>
					<a href="<?php echo $this->_aVars['sLink']; ?>" id="js_block_bottom_link_<?php echo $this->_aPhpfoxVars['iteration']['block']; ?>"><?php echo $this->_aVars['sPhrase']; ?></a>
				</li>
<?php endforeach; endif; ?>
		</ul>
<?php endif; ?>
	</div>
<?php endif; ?>
</div>
<?php endif;  unset($this->_aVars['sHeader'], $this->_aVars['sComponent'], $this->_aVars['aFooter'], $this->_aVars['sBlockBorderJsId'], $this->_aVars['bBlockDisableSort'], $this->_aVars['bBlockCanMove'], $this->_aVars['aEditBar'], $this->_aVars['sDeleteBlock'], $this->_aVars['sBlockTitleBar'], $this->_aVars['sBlockJsId'], $this->_aVars['sCustomClassName'], $this->_aVars['aMenu']); ?>
