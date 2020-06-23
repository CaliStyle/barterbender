<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<?php if ($this->_aVars['iPageId'] == -1): ?>
<?php if (! $this->_aVars['bNeedToBeConfig']): ?>
	<p style="text-align:center; padding: 10px; background: #FFF;">
		<a href="#" onclick="showDonationIndex('<?php echo _p('donation.page_donation_title_homepage'); ?>', <?php echo $this->_aVars['iPageId']; ?>, '<?php echo $this->_aVars['sUrl']; ?>'); return false;">
			<img src='<?php echo $this->_aVars['sImg']; ?>' />
		</a>
	</p>
<?php else: ?>
	<p style="text-align:center; padding: 10px; background: #FFF;">
		<a class="no_ajax" href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.donation'); ?>">
			<img src='<?php echo $this->_aVars['sImg']; ?>' />
		</a>
	</p>
	<p style="text-align:center; padding: 10px; background: #FFF;">
		<a class="no_ajax" href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.donation'); ?>" style="text-transform:uppercase; color: #8E8E8E">
<?php echo _p('donation.donation_setting'); ?>
		</a>
	</p>
<?php endif; ?>
	
<?php else: ?>
<?php if ($this->_aVars['iDonation'] > 0 && Phpfox ::isModule('donation')): ?>
<?php if ($this->_aVars['iUserId'] == Phpfox ::getUserId() && Phpfox ::getUserParam('donation.can_add_donation_on_own_page')): ?>
			<p style="text-align:center; padding: 10px; background: #FFF;">			
				<a onclick="showDonationIndex('<?php echo $this->_aVars['sDonation']; ?>',<?php echo $this->_aVars['iPageId']; ?>,'<?php echo $this->_aVars['sUrl']; ?>'); return false;" href="#">
					<img src="<?php echo $this->_aVars['sImg']; ?>"/>			
				</a><br/>
				<a href="<?php echo $this->_aVars['urlSetting']; ?>" style="text-transform:uppercase; color: #8E8E8E"> 
<?php echo _p('donation.donation_setting'); ?>
				</a>
			</p>
<?php else: ?>
			<p style="text-align:center; padding: 10px; background: #FFF;">				
				<a onclick="showDonationIndex('<?php echo $this->_aVars['sDonation']; ?>',<?php echo $this->_aVars['iPageId']; ?>,'<?php echo $this->_aVars['sUrl']; ?>'); return false;" href="#">
					<img src="<?php echo $this->_aVars['sImg']; ?>"/>			
				</a>
			</p>
<?php endif; ?>
<?php endif; ?>

<?php endif;  echo '
<script type="text/javascript" language="javascript">
		function showDonationIndex(title, iPageId, sUrl){  
	tb_show(title,$.ajaxBox(\'donation.detail\',\'iPageId=\' + iPageId + \'&sUrl=\' + sUrl));
}
</script>
'; ?>

