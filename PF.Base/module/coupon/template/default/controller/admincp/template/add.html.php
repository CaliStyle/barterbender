<?php

/**
 * [PHPFOX_HEADER]
 * @copyright      YouNet Company
 * @author         AnNT
 * @package        Module_Coupon
 * @version        3.02
 */

defined('PHPFOX') or exit('NO DICE!');

?>

{literal}
<style>
.ync_table_normal {
    background: none;
}
.ync_table_normal td, .ync_table_normal th {
    border: solid 1px gray;
}
select{
    min-width: 50px;
}
</style>
{/literal}

<form method="post" action="{url link='current'}" id="ync_add_print_template_form">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='template_details'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">{phrase var='template_name'}</label>
                <input class="form-control" type="text" name="val[name]" value="{value type='input' id='name'}" id="name" size="40" maxlength="255" />
            </div>
            <div class="table-responsive flex-sortable">
                <table class="table table-bordered" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="t_center">{phrase var='item'}</th>
                            <th class="t_center">{phrase var='order_in_group'}<br />{phrase var='0_not_used'}</th>
                            <th class="t_center">{phrase var='block_position'}<br />{phrase var='0_not_used'}</th>
                            <th class="t_center">{phrase var='font_size'}<br />({phrase var='px'})</th>
                            <th class="t_center">{phrase var='text_color'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="checkRow">
                            <td>{phrase var='coupon_name'}</td>
                            <td class="t_center">&nbsp;</td>
                            <td>
                                <select name="val[coupon_name][position]">
                                    <?php for($i=0; $i<=8; $i++) : ?>
                                    <option value="<?php echo $i; ?>"<?php if ((isset($this->_aVars['aForms']['coupon_name']) && $this->_aVars['aForms']['coupon_name']['position']==$i) || (empty($this->_aVars['aForms']['coupon_name']) && $i==3)) : ?> selected="selected"<?php endif; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                            <td class="t_center">
                                <input type="text" name="val[coupon_name][size]" value="{if isset($aForms.coupon_name.size)}{$aForms.coupon_name.size}{else}16{/if}" size="5" />
                            </td>
                            <td>
                                <input type="text" name="val[coupon_name][color]" value="{if isset($aForms.coupon_name.color)}{$aForms.coupon_name.color}{else}#000000{/if}" class="color_input" />
                            </td>
                        </tr>
                        <tr class="checkRow tr">
                            <td>{phrase var='coupon_photo'}</td>
                            <td class="t_center">&nbsp;</td>
                            <td>
                                <select name="val[coupon_photo][position]">
                                    <?php for($i=0; $i<=8; $i++) : ?>
                                    <option value="<?php echo $i; ?>"<?php if ((isset($this->_aVars['aForms']['coupon_photo']) && $this->_aVars['aForms']['coupon_photo']['position']==$i) || (empty($this->_aVars['aForms']['coupon_photo']) && $i==2)) : ?> selected="selected"<?php endif; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                            <td class="t_center">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr class="checkRow">
                            <td>{phrase var='discount_value'}</td>
                            <td class="t_center">&nbsp;</td>
                            <td>
                                <select name="val[discount_value][position]">
                                    <?php for($i=0; $i<=8; $i++) : ?>
                                    <option value="<?php echo $i; ?>"<?php if ((isset($this->_aVars['aForms']['discount_value']) && $this->_aVars['aForms']['discount_value']['position']==$i) || (empty($this->_aVars['aForms']['discount_value']) && $i==4)) : ?> selected="selected"<?php endif; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                            <td class="t_center">
                                <input type="text" name="val[discount_value][size]" value="{if isset($aForms.discount_value.size)}{$aForms.discount_value.size}{else}12{/if}" size="5" />
                            </td>
                            <td>
                                <input type="text" name="val[discount_value][color]" value="{if isset($aForms.discount_value.color)}{$aForms.discount_value.color}{else}#000000{/if}" class="color_input" />
                            </td>
                        </tr>
                        <tr class="checkRow tr">
                            <td>{phrase var='coupon_code'}</td>
                            <td class="t_center">&nbsp;</td>
                            <td>
                                <select name="val[coupon_code][position]">
                                    <?php for($i=0; $i<=8; $i++) : ?>
                                    <option value="<?php echo $i; ?>"<?php if ((isset($this->_aVars['aForms']['coupon_code']) && $this->_aVars['aForms']['coupon_code']['position']==$i) || (empty($this->_aVars['aForms']['coupon_code']) && $i==7)) : ?> selected="selected"<?php endif; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                            <td class="t_center">
                                <input type="text" name="val[coupon_code][size]" value="{if isset($aForms.coupon_code.size)}{$aForms.coupon_code.size}{else}12{/if}" size="5" />
                            </td>
                            <td>
                                <input type="text" name="val[coupon_code][color]" value="{if isset($aForms.coupon_code.color)}{$aForms.coupon_code.color}{else}#000000{/if}" class="color_input" />
                            </td>
                        </tr>
                        <tr class="checkRow">
                            <td>{phrase var='site_url'}</td>
                            <td class="t_center">
                                <select name="val[site_url][order]">
                                    <?php for($i=0; $i<=4; $i++) : ?>
                                    <option value="<?php echo $i; ?>"<?php if ((isset($this->_aVars['aForms']['site_url']) && $this->_aVars['aForms']['site_url']['order']==$i) || (empty($this->_aVars['aForms']['site_url']) && $i==1)) : ?> selected="selected"<?php endif; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                            <td>
                                <select name="val[other_info][position]">
                                    <?php for($i=0; $i<=8; $i++) : ?>
                                    <option value="<?php echo $i; ?>"<?php if ((isset($this->_aVars['aForms']['other_info']) && $this->_aVars['aForms']['other_info']['position']==$i) || (empty($this->_aVars['aForms']['other_info']) && $i==5)) : ?> selected="selected"<?php endif; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                            <td class="t_center">
                                <input type="text" name="val[site_url][size]" value="{if isset($aForms.site_url.size)}{$aForms.site_url.size}{else}12{/if}" size="5" />
                            </td>
                            <td>
                                <input type="text" name="val[site_url][color]" value="{if isset($aForms.site_url.color)}{$aForms.site_url.color}{else}#000000{/if}" class="color_input" />
                            </td>
                        </tr>
                        <tr class="checkRow">
                            <td>{phrase var='expired_date'}</td>
                            <td class="t_center">
                                <select name="val[expired_date][order]">
                                    <?php for($i=0; $i<=4; $i++) : ?>
                                    <option value="<?php echo $i; ?>"<?php if ((isset($this->_aVars['aForms']['expired_date']) && $this->_aVars['aForms']['expired_date']['order']==$i) || (empty($this->_aVars['aForms']['expired_date']) && $i==2)) : ?> selected="selected"<?php endif; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                            <td>&nbsp;</td>
                            <td class="t_center">
                                <input type="text" name="val[expired_date][size]" value="{if isset($aForms.expired_date.size)}{$aForms.expired_date.size}{else}12{/if}" size="5" />
                            </td>
                            <td>
                                <input type="text" name="val[expired_date][color]" value="{if isset($aForms.expired_date.color)}{$aForms.expired_date.color}{else}#000000{/if}" class="color_input" />
                            </td>
                        </tr>
                        <tr class="checkRow">
                            <td>{phrase var='location'}</td>
                            <td class="t_center">
                                <select name="val[location][order]">
                                    <?php for($i=0; $i<=4; $i++) : ?>
                                    <option value="<?php echo $i; ?>"<?php if ((isset($this->_aVars['aForms']['location']) && $this->_aVars['aForms']['location']['order']==$i) || (empty($this->_aVars['aForms']['location']) && $i==3)) : ?> selected="selected"<?php endif; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                            <td>&nbsp;</td>
                            <td class="t_center">
                                <input type="text" name="val[location][size]" value="{if isset($aForms.location.size)}{$aForms.location.size}{else}12{/if}" size="5" />
                            </td>
                            <td>
                                <input type="text" name="val[location][color]" value="{if isset($aForms.location.color)}{$aForms.location.color}{else}#000000{/if}" class="color_input" />
                            </td>
                        </tr>
                        <tr class="checkRow">
                            <td>{phrase var='category'}</td>
                            <td class="t_center">
                                <select name="val[category][order]">
                                    <?php for($i=0; $i<=4; $i++) : ?>
                                    <option value="<?php echo $i; ?>"<?php if ((isset($this->_aVars['aForms']['category']) && $this->_aVars['aForms']['category']['order']==$i) || (empty($this->_aVars['aForms']['category']) && $i==4)) : ?> selected="selected"<?php endif; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                            <td>&nbsp;</td>
                            <td class="t_center">
                                <input type="text" name="val[category][size]" value="{if isset($aForms.category.size)}{$aForms.category.size}{else}12{/if}" size="5" />
                            </td>
                            <td>
                                <input type="text" name="val[category][color]" value="{if isset($aForms.category.color)}{$aForms.category.color}{else}#000000{/if}" class="color_input" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="form-group">
                <label for="">{phrase var='block_position'}</label>
                <table class="ync_table_normal" style="width: 250px; border-collapse: collapse; text-align: center;">
                    <tr>
                        <td colspan="2">1</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>5</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>7</td>
                    </tr>
                    <tr>
                        <td colspan="2">8</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="panel-footer">
            <input type="button" value="{phrase var='preview'}" class="btn btn-primary" onclick="tb_show('Preview', $.ajaxBox('coupon.blockPreview', 'width=450&'+$('#ync_add_print_template_form').serialize()));" />
            <input type="submit" value="{phrase var='save'}" class="btn btn-default" />
        </div>
    </div>
</form>

<script type="text/javascript">
$Behavior.initColorPicker = function() {l};
    $('.color_input').minicolors();
{
    r
}
</script>
