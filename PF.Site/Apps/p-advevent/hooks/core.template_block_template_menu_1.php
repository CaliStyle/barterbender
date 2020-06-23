<?php
if(Phpfox::isAdminPanel())
{
    ?>
    <script type="text/javascript">
    $Behavior.fixFeventMenu = function(){
        $("div.main_sub_menu_holder_header").each(function(i,e){
            if(e.innerHTML == 'Fevent'){
                e.innerHTML = 'Advanced Event';
                return;
            }
        });
    }
    </script>
    <?php
}
