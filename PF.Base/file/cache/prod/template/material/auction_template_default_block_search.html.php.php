<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:46 pm */ ?>
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
 */


/**
 *
 *
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>

<?php echo '
<script type="text/javascript">

    $Core.remakePostUrl = function(){

        var keyword = $("#search_keyword").val();
        var category = $("select.js_mp_category_list option:selected");
        var sort = $("#search_sort").val();
        var categoryUrl = [];
        $(category).each(function(index){
            if($(this).val() != \'\'){
                categoryUrl.push($(this).val());
            }
        });

        categoryUrl = categoryUrl.join(",");
        var url = window.location.href;

        if(url.match(/\/advsearch_.*?\//g))
        {

        }
        else
        {
'; ?>

<?php if ($this->_aVars['sFullControllerName'] == 'auction.index'): ?>
            url = url.replace(/\/auction\//g, '/auction/advsearch_true/');
<?php endif; ?>
<?php if ($this->_aVars['sFullControllerName'] == 'auction.my-bids'): ?>
            url = url.replace(/\/my-bids\//g, '/my-bids/advsearch_true/');
<?php endif; ?>
<?php if ($this->_aVars['sFullControllerName'] == 'auction.my-offers'): ?>
            url = url.replace(/\/my-offers\//g, '/my-offers/advsearch_true/');
<?php endif; ?>
<?php if ($this->_aVars['sFullControllerName'] == 'auction.didnt-win'): ?>
            url = url.replace(/\/didnt-win\//g, '/didnt-win/advsearch_true/');
<?php endif; ?>
<?php if ($this->_aVars['sFullControllerName'] == 'auction.my-won-bids'): ?>
            url = url.replace(/\/my-won-bids\//g, '/my-won-bids/advsearch_true/');
    <?php endif;  echo '
        }

        if(url.match(/\/keyword_.*?\//g))
        {
            url = url.replace(/\/keyword_.*?\//g, \'/keyword_\'+keyword+\'/\');
        }
        else
        {
            url += \'keyword_\'+keyword+\'/\';
        }


        if(url.match(/\/category_.*?\//g))
        {
            url = url.replace(/\/category_.*?\//g, \'/category_\'+categoryUrl+\'/\');
        }
        else
        {
            url += \'category_\'+categoryUrl+\'/\';
        }

        if(url.match(/\/sort_.*?\//g))
        {
            url = url.replace(/\/sort_.*?\//g, \'/sort_\'+sort+\'/\');
        }
        else
        {
            url += \'sort_\'+sort+\'/\';
        }

        $("#ynauction_advsearch").attr(\'action\', url);
    }

</script>
'; ?>


<div class="ynfe adv-search-block clear-bg-padding" id ="ynauction_adv_search" <?php if (! isset ( $this->_aVars['aForms']['advancedsearch'] )): ?> style="display: none;" <?php endif; ?>>
    <form id="ynauction_advsearch" method="post" onsubmit="$Core.remakePostUrl(); if($('#search_keywords').val()=='<?php echo _p('keywords'); ?>...'){$('#search_keywords').val('');}">
        <div class="form-group content">
            <input type="hidden" value="1" name="search[submit]">
            <input type="hidden" name="search[advsearch]" value="1" />
            <input type="hidden" id="form_flag" name="search[form_flag]" value="0">

            <div class="form-group">
                <div class="lb-keyword"><label for="search_category"><?php echo _p('keyword'); ?>:</label></div>
                <div class="cw-keyword">
                    <input id="search_keyword" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['keyword']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['keyword']) : (isset($this->_aVars['aForms']['keyword']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['keyword']) : '')); ?>
" type="text" name="search[keyword]" class="search_keyword form-control">
                </div>
            </div>
            <div class="form-group">
                    <div class="lb-category">
                        <label for="search_category"><?php echo _p('category'); ?>:</label>
                    </div>
                    <div class="cw-category">
<?php echo $this->_aVars['sCategories']; ?>
                    </div>
            </div>
            <div class="form-group">
                   <div class="lb-sort"><label for="search_sort"><?php echo _p('sort'); ?>:</label></div>
                   <div class="cw-sort">
                       <select id="search_sort" name="search[sort]" class="form-control">
                           <option value=""><?php echo _p('select'); ?>:</option>
                           <option value="top-orders" <?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('sort') && in_array('sort', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['sort'])
								&& $aParams['sort'] == 'top-orders')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['sort'])
									&& !isset($aParams['sort'])
									&& (($this->_aVars['aForms']['sort'] == 'top-orders') || (is_array($this->_aVars['aForms']['sort']) && in_array('top-orders', $this->_aVars['aForms']['sort']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
><?php echo _p('top_orders'); ?></option>
                           <option value="newest" <?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('sort') && in_array('sort', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['sort'])
								&& $aParams['sort'] == 'newest')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['sort'])
									&& !isset($aParams['sort'])
									&& (($this->_aVars['aForms']['sort'] == 'newest') || (is_array($this->_aVars['aForms']['sort']) && in_array('newest', $this->_aVars['aForms']['sort']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
><?php echo _p('newest'); ?></option>
                           <option value="oldest" <?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('sort') && in_array('sort', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['sort'])
								&& $aParams['sort'] == 'oldest')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['sort'])
									&& !isset($aParams['sort'])
									&& (($this->_aVars['aForms']['sort'] == 'oldest') || (is_array($this->_aVars['aForms']['sort']) && in_array('oldest', $this->_aVars['aForms']['sort']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
><?php echo _p('oldest'); ?></option>
                           <option value="a-z" <?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('sort') && in_array('sort', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['sort'])
								&& $aParams['sort'] == 'a-z')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['sort'])
									&& !isset($aParams['sort'])
									&& (($this->_aVars['aForms']['sort'] == 'a-z') || (is_array($this->_aVars['aForms']['sort']) && in_array('a-z', $this->_aVars['aForms']['sort']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
><?php echo _p('a_z'); ?></option>
                           <option value="z-a" <?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('sort') && in_array('sort', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['sort'])
								&& $aParams['sort'] == 'z-a')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['sort'])
									&& !isset($aParams['sort'])
									&& (($this->_aVars['aForms']['sort'] == 'z-a') || (is_array($this->_aVars['aForms']['sort']) && in_array('z-a', $this->_aVars['aForms']['sort']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
><?php echo _p('z_a'); ?></option>
                           <option value="most-liked" <?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('sort') && in_array('sort', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['sort'])
								&& $aParams['sort'] == 'most-liked')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['sort'])
									&& !isset($aParams['sort'])
									&& (($this->_aVars['aForms']['sort'] == 'most-liked') || (is_array($this->_aVars['aForms']['sort']) && in_array('most-liked', $this->_aVars['aForms']['sort']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
><?php echo _p('most_liked'); ?></option>
                       </select>
                   </div>
            </div>
            <div class="form-group">
                <div class="cw-cmd">
                    <button name="search[submit]" class="btn btn-sm btn-primary" type="submit"><?php echo _p('search'); ?></button>
<?php if ($this->_aVars['sFullControllerName'] == 'auction.index'): ?>
                    <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('auction'); ?>" class="btn btn-sm btn-default"><?php echo _p('reset'); ?></a>
<?php else: ?>
                    <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl($this->_aVars['sFullControllerName']); ?>" class="btn btn-sm btn-default"><?php echo _p('reset'); ?></a>
<?php endif; ?>
                </div>
            </div>
        </div>
    
</form>

</div> 



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
