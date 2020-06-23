<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:24 am */ ?>
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
<?php if (isset ( $this->_aVars['glat'] ) && isset ( $this->_aVars['glong'] )): ?>
    <?php echo '
        <script type="text/javascript">
            $Behavior.updateGlatGlong = function()
            {
                if(ynfeIndexPage.glat > 0 && ynfeIndexPage.glong > 0){
                } else {
                    ynfeIndexPage.glat = ';  echo $this->_aVars['aForms']['glat'];  echo ';
                    ynfeIndexPage.glong = ';  echo $this->_aVars['aForms']['glong'];  echo ';
                }
            }
        </script>
    '; ?>

<?php endif; ?>
<div id="" class="js_p_search_wrapper" >
    <div  class=" js_p_search_result hide item_is_active_holder item_selection_active p-advance-search-button">
        <a class="js_p_enable_adv_search_btn" href="javascript:void(0)" onclick="p_core.pEnableAdvSearch();return false;">
            <i class="ico ico-dottedmore-o"></i>
        </a>
    </div>
</div>
<div class="js_p_adv_search_wrapper p-advance-search-form p-fevent-search-wrapper" style="display: none">
        <div id="core_js_messages" class="mb-3"></div>
        <input type="hidden" name="search[advsearch]" id="js_advsearch_flag" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['advsearch']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['advsearch']) : (isset($this->_aVars['aForms']['advsearch']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['advsearch']) : '')); ?>
"/>
        <input type="hidden" name="search[glat]" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['glat']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['glat']) : (isset($this->_aVars['aForms']['glat']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['glat']) : '')); ?>
" id="js_advsearch_glat">
        <input type="hidden" name="search[glong]" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['glong']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['glong']) : (isset($this->_aVars['aForms']['glong']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['glong']) : '')); ?>
" id="js_advsearch_glong">

        <div class="p-fevent-search-formgroup-wrapper dont-unbind-children">
            <div class="form-group">
                <label><?php echo _p('fevent.v_locationvenue'); ?></label>
                <div class="js-location_input_section"><input type="text" name="val[location]" class="form-control js-location_input" placeholder="<?php echo _p('type_a_location'); ?>" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['location']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['location']) : (isset($this->_aVars['aForms']['location']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['location']) : '')); ?>
" maxlength="">
		<input type="hidden" name="val[location_lat]" class="js-location_lat" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['location_lat']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['location_lat']) : (isset($this->_aVars['aForms']['location_lat']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['location_lat']) : '')); ?>
">		<input type="hidden" name="val[location_lng]" class="js-location_lng" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['location_lng']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['location_lng']) : (isset($this->_aVars['aForms']['location_lng']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['location_lng']) : '')); ?>
">		<input type="hidden" name="val[country_iso]" class="js-location_country_iso" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['country_iso']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['country_iso']) : (isset($this->_aVars['aForms']['country_iso']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['country_iso']) : '')); ?>
">		<input type="hidden" name="val[country_child_id]" class="js-location_country_child_id" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['country_child_id']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['country_child_id']) : (isset($this->_aVars['aForms']['country_child_id']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['country_child_id']) : '')); ?>
"><div class="js-location_map"></div></div>
            </div>

            <div class="form-group">
                <label><?php echo _p('fevent.range'); ?></label>
                <div class="input-group input-group-dropdown">
                    <input placeholder="0.00" id="search_range_value_from" type="text" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['rangevaluefrom']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['rangevaluefrom']) : (isset($this->_aVars['aForms']['rangevaluefrom']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['rangevaluefrom']) : '')); ?>
" name="search[rangevaluefrom]" class="form-control search_keyword">
                    <div class="input-group-btn dropdown">
                        <select class="w-auto btn dropdown-toggle" name="search[rangetype]" id="search_range_type">
                            <option value="0"><?php echo _p('fevent.miles'); ?></option>
                            <option value="1" <?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('rangetype') && in_array('rangetype', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['rangetype'])
								&& $aParams['rangetype'] == '1')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['rangetype'])
									&& !isset($aParams['rangetype'])
									&& (($this->_aVars['aForms']['rangetype'] == '1') || (is_array($this->_aVars['aForms']['rangetype']) && in_array('1', $this->_aVars['aForms']['rangetype']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
><?php echo _p('fevent.km'); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group js_core_init_selectize_form_group">
                <label><?php echo _p('fevent.country'); ?></label>
                <div >
                    <?php Phpfox::getBlock('core.country-build', array('param'=> array (
))); ?>
                </div>
            </div>
            <div class="p-daterangepicker-form-group form-group">
                <label><?php echo _p('time'); ?></label>
                <div>
                    <input type="hidden" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['start_time']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['start_time']) : (isset($this->_aVars['aForms']['start_time']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['start_time']) : '')); ?>
" id="js_p_start_time" name="search[stime]">
                    <input type="hidden" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['end_time']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['end_time']) : (isset($this->_aVars['aForms']['end_time']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['end_time']) : '')); ?>
" id="js_p_end_time" name="search[etime]">
                    <input type="hidden" id="js_time_type" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['time_type']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['time_type']) : (isset($this->_aVars['aForms']['time_type']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['time_type']) : '')); ?>
" name="search[time_type]">
                    <input type="text" id="js_time_text" class="form-control" value="<?php if (! empty ( $this->_aVars['aForms']['time'] )):  $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['time']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['time']) : (isset($this->_aVars['aForms']['time']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['time']) : ''));  else:  echo _p('all');  endif; ?>" readonly >
                </div>
            </div>

            <div class="form-group js_core_init_selectize_form_group">
                <label><?php echo _p('status'); ?></label>
                <div>
                    <select class="form-control" id="search_status" name="search[status]">
                        <option value=""><?php echo _p('all'); ?></option>
<?php if (count((array)$this->_aVars['statusArray'])):  foreach ((array) $this->_aVars['statusArray'] as $this->_aVars['status_text'] => $this->_aVars['status_value']): ?>
                        <option value="<?php echo $this->_aVars['status_value']; ?>" <?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('status') && in_array('status', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['status'])
								&& $aParams['status'] == $this->_aVars['status_value'])

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['status'])
									&& !isset($aParams['status'])
									&& (($this->_aVars['aForms']['status'] == $this->_aVars['status_value']) || (is_array($this->_aVars['aForms']['status']) && in_array($this->_aVars['status_value'], $this->_aVars['aForms']['status']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
><?php echo $this->_aVars['status_text']; ?></option>
<?php endforeach; endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group clearfix advance_search_form_button p-fevent-advance-search-form-button">
            <div class="pull-right">
                <a class="btn btn-default btn-sm" href="javascript:void(0);" id="js_p_search_reset" onclick="eventAdvSearch.resetForm(); return false;"><?php echo _p('reset'); ?></a>
                <button class="btn btn-primary ml-1 btn-sm" onclick="return eventAdvSearch.submitForm(this);"><?php echo _p('search'); ?></button>
<?php if (Phpfox ::VERSION >= '4.8.0'): ?>
                <a class="btn btn-primary ml-1 btn-sm" onclick="searchOnGoogleMapView(this);" attr-href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('fevent.map', array('type' => fevent,'view' => all)); ?>" id="search_and_view_on_the_map"><?php echo _p('search_and_view_on_the_map'); ?></a>
<?php endif; ?>
            </div>
            <div class="pull-left">
                <span class="advance_search_dismiss" onclick="p_core.pEnableAdvSearch(); return false;">
                    <i class="ico ico-close"></i>
                </span>
            </div>
        </div>
</div>
<?php echo '
<script type="text/javascript">
    $Behavior.initFeventDaterangepicker = function(){
        let params = {
            parent : \'.p-daterangepicker-form-group\',
            default_range_key: \'';  echo _p('all');  echo '\',
            time_type_default: \'';  echo $this->_aVars['aForms']['time_type'];  echo '\',
            ranges: {
                \'';  echo _p('all');  echo '\' : [moment(), moment()],
                \'';  echo _p('today');  echo '\' : [moment(), moment()],
                \'';  echo _p('tomorrow');  echo '\' : [moment().add(1,\'days\'), moment().add(1,\'days\')],
                \'';  echo _p('this_week');  echo '\' : [moment().startOf(\'isoWeek\'), moment().endOf(\'isoWeek\')],
                \'';  echo _p('advevent_next_week');  echo '\' : [moment().add(1, \'weeks\').startOf(\'isoWeek\'), moment().add(1, \'weeks\').endOf(\'isoWeek\')],
                \'';  echo _p('this_month');  echo '\' : [moment().startOf(\'month\'), moment().endOf(\'month\')]
            },
            \'custom_range\' : [\'';  echo $this->_aVars['aForms']['custom_start_time'];  echo '\',\'';  echo $this->_aVars['aForms']['custom_end_time'];  echo '\'],
            \'custom_range_label\' : \'';  echo _p('advevent_choose_date_lowercase');  echo '\'
        }
        eventAdvSearch.advEventRangeTime.setDefaultParams(params);
        eventAdvSearch.advEventRangeTime.init();
        eventAdvSearch.defaultCountryIso = \'';  echo $this->_aVars['defaultCountry'];  echo '\'
        let isCoreSearch = '; ?>
parseInt(<?php echo $this->_aVars['isCoreSearch']; ?>)<?php echo ';
        if(isCoreSearch) {
            eventAdvSearch.resetForm();
        }
    }

    $Behavior.initInputListener = function(){
        $(\'#search_range_value_from\').on(\'input\',function(e){
            var range = e.target.value;
            if (range){
                $(\'#search_and_view_on_the_map\').addClass(\'disabled\');
            } else {
                $(\'#search_and_view_on_the_map\').removeClass(\'disabled\');
            }
        });
    }

    searchOnGoogleMapView = function(obj){
        var google_map_url = $(obj).attr(\'attr-href\');
        var keyword = $(\'.form-control[name="search[search]"]\').val();
        var url = google_map_url;
        url += \'&\' + encodeURIComponent(\'search[search]\') + \'=\' + encodeURIComponent(keyword);
        if (!$(\'#search_and_view_on_the_map\').hasClass(\'disabled\')){
            window.location.href = url;
        }
    }
</script>
'; ?>




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
