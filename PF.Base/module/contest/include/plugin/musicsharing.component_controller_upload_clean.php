<?php

if ($yncontestid = $this->request()->get('yncontestid'))
{
    $base_url = Phpfox::getLib('url')->makeUrl('musicsharing.upload', array('yncontestid' => $yncontestid));
    ?>
    <script language="javascript" type="text/javascript">
		$Behavior.initMusicSharingUpload = (function(){
            var $album = $("#album");
            var $ms_musicupload_wrapper = $("#ms-musicupload-wrapper");
			var base_url = "<?php echo $base_url; ?>";
            
            $album.change(function(evt){
                var $this = $(this);
                var $this_val = $this.val();
				
                if($this_val != -1){
					window.location = base_url + "&album=" + $this_val;
                }else{
					window.location = base_url;
                }
            });
        });
    </script>
    <?php
}

?>