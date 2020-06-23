<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: June 1, 2020, 8:35 pm */ ?>
<?php if (count ( $this->_aVars['aCustomFields'] )):  if (count((array)$this->_aVars['aCustomFields'])):  foreach ((array) $this->_aVars['aCustomFields'] as $this->_aVars['sGroupName'] => $this->_aVars['aFields']): ?>
	<?php 
		$this->_aVars['isDisplayGroup'] = false;
	 ?>
<?php if (count((array)$this->_aVars['aFields'])):  foreach ((array) $this->_aVars['aFields'] as $this->_aVars['aField']): ?>
<?php if (isset ( $this->_aVars['aField']['value'] ) && $this->_aVars['aField']['value'] != ""): ?>
			<?php 
				$this->_aVars['isDisplayGroup'] = true;
			 ?>
<?php endif; ?>
<?php endforeach; endif; ?>
<?php if ($this->_aVars['isDisplayGroup']): ?>
	    <div class="subsection_header"><?php echo _p($this->_aVars['sGroupName']); ?></div>
<?php if (count((array)$this->_aVars['aFields'])):  foreach ((array) $this->_aVars['aFields'] as $this->_aVars['aField']): ?>
<?php if (isset ( $this->_aVars['aField']['value'] ) && $this->_aVars['aField']['value'] != ""): ?>
			    <div class="ynauction-detail-overview-custom-item">
			        <div class="item_label">
<?php echo _p($this->_aVars['aField']['phrase_var_name']); ?>:
			        </div>
			        <div class="item_value">
<?php if ($this->_aVars['aField']['var_type'] == 'text'): ?>
<?php if (isset ( $this->_aVars['aField']['value'] )): ?> <?php echo $this->_aVars['aField']['value']; ?> <?php else: ?> <?php echo _p("auction.none"); ?>  <?php endif; ?>
			            
<?php elseif ($this->_aVars['aField']['var_type'] == 'textarea'): ?>
<?php if (isset ( $this->_aVars['aField']['value'] )): ?> <?php echo $this->_aVars['aField']['value']; ?> <?php else: ?> <?php echo _p("auction.none"); ?>  <?php endif; ?>
<?php elseif ($this->_aVars['aField']['var_type'] == 'select'): ?>
			
<?php if (isset ( $this->_aVars['aField']['value'] )): ?>
<?php if (count((array)$this->_aVars['aField']['value'])):  foreach ((array) $this->_aVars['aField']['value'] as $this->_aVars['selected'] => $this->_aVars['value_selected']): ?>
<?php echo _p($this->_aVars['value_selected']); ?><br>
<?php endforeach; endif; ?>
<?php else: ?>
<?php echo _p("auction.none"); ?>
<?php endif; ?>
			            
<?php elseif ($this->_aVars['aField']['var_type'] == 'multiselect'): ?>
<?php if (isset ( $this->_aVars['aField']['value'] )): ?>
<?php if (count((array)$this->_aVars['aField']['value'])):  foreach ((array) $this->_aVars['aField']['value'] as $this->_aVars['selected'] => $this->_aVars['value_selected']): ?>
<?php echo _p($this->_aVars['value_selected']); ?><br>
<?php endforeach; endif; ?>
<?php else: ?>
<?php echo _p("auction.none"); ?>
<?php endif; ?>
			            
<?php elseif ($this->_aVars['aField']['var_type'] == 'checkbox'): ?>
<?php if (isset ( $this->_aVars['aField']['value'] )): ?>
<?php if (count((array)$this->_aVars['aField']['value'])):  foreach ((array) $this->_aVars['aField']['value'] as $this->_aVars['checked'] => $this->_aVars['value_checked']): ?>
<?php echo _p($this->_aVars['value_checked']); ?><br>
<?php endforeach; endif; ?>
<?php else: ?>
<?php echo _p("auction.none"); ?>
<?php endif; ?>
			            
<?php elseif ($this->_aVars['aField']['var_type'] == 'radio'): ?>
<?php if (isset ( $this->_aVars['aField']['value'] )): ?>
<?php if (count((array)$this->_aVars['aField']['value'])):  foreach ((array) $this->_aVars['aField']['value'] as $this->_aVars['checked'] => $this->_aVars['value_checked']): ?>
<?php echo _p($this->_aVars['value_checked']); ?><br>
<?php endforeach; endif; ?>
<?php else: ?>
<?php echo _p("auction.none"); ?>
<?php endif; ?>
<?php endif; ?>
			        </div>
			    </div>
<?php endif; ?>
<?php endforeach; endif; ?>
<?php endif;  endforeach; endif;  endif; ?>
