<?php
;
if(Phpfox::isModule('directory')){
		$aUserRoleDirectory = $_SESSION['aUserRoleDirectory'];
		
		$iUser = $aUserRoleDirectory['user_id'];

		$aRoleMembers = Phpfox::getService('directory')->getAllUserRoleOfMember($iUser);


		$title = _p('directory.working_at');

		$iCountBusiness = 0;

		if(count($aRoleMembers)){
			foreach ($aRoleMembers as $key => $aRole) {
					$aBusiness = PHpfox::getService('directory')->getBusinessForEdit($aRole['business_id'], true);
					if(isset($aBusiness['business_status'])){

						if(  
							$aBusiness['business_status'] != Phpfox::getService('directory.helper')->getConst('business.status.draft')
						&&  $aBusiness['business_status'] != Phpfox::getService('directory.helper')->getConst('business.status.pending')
							)
						 {
						 	$iCountBusiness++;
						 }
					}
			}
		}
		if($iCountBusiness){

?>
		<div class="item">
			<div class="item-label">
				<?php echo $title;?>:
			</div>
            <div class="item-value">
<?php
		foreach ($aRoleMembers as $key => $aRole) {
				$aBusiness = PHpfox::getService('directory')->getBusinessForEdit($aRole['business_id'], true);
				if(isset($aBusiness['business_status'])){

				if(  
					$aBusiness['business_status'] != Phpfox::getService('directory.helper')->getConst('business.status.draft')
				&&  $aBusiness['business_status'] != Phpfox::getService('directory.helper')->getConst('business.status.pending')
					)
				 {
	?>
		

				<?php
					$link = Phpfox::permalink('directory.detail', $aRole['business_id'], $aRole['name']);
				?>
					<div>
						<span><?php echo $aRole['role_title'];?></span> at <span><a href="<?php echo $link;?>">
						<?php echo Phpfox::getLib('parse.output')->shorten($aRole['name'],50, '...');?></a></span>
					</div>

<?php
			}
		 }
	  }
	  ?>
            </div>
		</div>
	  <?php
	  }
}
;
?>