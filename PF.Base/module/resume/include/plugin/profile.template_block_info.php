<?php
$position_display = 1;
$aUserResume = $_SESSION['aUserResume'];

   $aResume = Phpfox::getLib("database")
                    ->select('*')
                    ->from(Phpfox::getT('resume_basicinfo'))
                    ->where('status = "approved" and is_published = 1 and is_show_in_profile = 1 AND user_id = ' .  $aUserResume['user_id'])
                    ->execute('getRow');
  $allPermission = Phpfox::getService('resume.setting')->getAllPermissions();
  if(isset($allPermission['position']))
  {
      $position_display = $allPermission['position'];
  }
  if($aResume && isset($allPermission['display_resume_in_profile_info']) && $allPermission['display_resume_in_profile_info']==1){
    $aCats = Phpfox::getService('resume.category')->getCatNameList($aResume['resume_id']);
    $aPrevious_job = Phpfox::getService('resume.experience')->getLastWork($aResume['resume_id']);
    $aResume['previous_job'] = _p('resume.n_a');
    if($aPrevious_job){
        if($aPrevious_job['level_id']>0)
        {
            $aResume['previous_job'] = Phpfox::getService('resume.level')->getLevelById($aPrevious_job['level_id']);
        }
    }
    $highest_education = Phpfox::getService('resume.education')->getLastEducation($aResume['resume_id']);
    $aResume['highest_education'] = _p('resume.n_a');
    if($highest_education){
        $aResume['highest_education'] = $highest_education['school_name'];
    }
    $sClassItem = 'info';
    $sClassTitle = 'info_left';
    $sClassValue = 'info_right';
    if (flavor()->active->id == 'material') {
        $sClassItem = 'item';
        $sClassTitle = 'item-label';
        $sClassValue = 'item-value';
    }
?>
<?php if($position_display==1){?>
<div id="workresume" class="hide">
<?php } ?>
    <div class="<?php echo $sClassItem ?>" style="padding-top:8px">
        <div class="<?php echo $sClassTitle ?>">
            <?php echo _p('resume.resume_name'); ?>:
        </div>
        <div class="<?php echo $sClassValue ?>">
            <?php echo $aResume['headline'];?>
        </div>
    </div>

    <div class="<?php echo $sClassItem ?>">
        <div class="<?php echo $sClassTitle ?>">
            <?php echo _p('resume.category'); ?>:
        </div>
        <div class="<?php echo $sClassValue ?>">

                    <?php if(count($aCats)>0){
                        foreach($aCats as $key=>$aCat)
                            if($key == 0){
                        ?>
                        <a href="<?php echo Phpfox::getLib("url")->makeUrl('resume.category').$aCat['category_id']."/".$aCat['name_url']."/"; ?>"><?php echo Phpfox::getLib('locale')->convert($aCat['name']); ?></a>
                            <?php }else{ ?>
                                | <a href="<?php echo Phpfox::getLib("url")->makeUrl('resume.category').$aCat['category_id']."/".$aCat['name_url'];?>"><?php echo Phpfox::getLib('locale')->convert($aCat['name'])."/"; ?></a>
                      <?php }} ?>
        </div>
    </div>

    <div class="<?php echo $sClassItem ?>">
        <div class="<?php echo $sClassTitle ?>">
            <?php echo _p('resume.previous'); ?>:
        </div>
        <div class="<?php echo $sClassValue ?>">
            <?php echo $aResume['previous_job'];?>
        </div>
    </div>

    <div class="<?php echo $sClassItem ?>" <?php if($position_display==1){ echo 'style="width:100%;"'; }?>>
        <div class="<?php echo $sClassTitle ?>">
            <?php echo _p('resume.education'); ?>:
        </div>
        <div class="<?php echo $sClassValue ?>">
            <?php echo $aResume['highest_education'];?>
        </div>
    </div>

    <div class="<?php echo $sClassItem ?>" style="width: 100%;">
        <div class="<?php echo $sClassTitle ?>">
            <?php echo _p('resume.summary'); ?>:
        </div>
        <div class="<?php echo $sClassValue ?>">
            <?php echo $aResume['summary_parsed'];?>
        </div>
    </div>
    <?php if($position_display==1){?>
    <div id="resume_viewmore">
        <?php } ?>
        <div class="<?php echo $sClassItem ?>" style="width: 100%; <?php if(flavor()->active->id != 'material'){?>padding-left:20px;padding-top:8px<?php } ?> ">
            <?php echo _p('resume.to_view_full_resume_visit_a_href_link_here_a', array(
                'link' => Phpfox::getLib("url")->makeUrl('resume.view').$aResume['resume_id']."/",
            )); ?>
        </div>
        <?php if($position_display==1){?>
    </div>
<?php } ?>
<?php if($position_display==1){?>
</div>
<script>
    $Behavior.moveBlock = function(){
        $('document').ready(function(){
            $('#js_basic_info_data').prepend($('#workresume').html());
            $('#workresume').html('');
            $('#js_basic_info_data').append($('#resume_viewmore').html());
            $('#resume_viewmore').html('');
        });
    }
</script>

<?php
  }}
?>