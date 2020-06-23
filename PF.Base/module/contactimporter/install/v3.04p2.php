<?php
function contactimporter_install304p2() {
   $oDb = Phpfox::getLib('phpfox.database');
   $sWhere = "var_name = 'require_invite' AND module_id = 'contactimporter'";
   $oDb->delete(Phpfox::getT('setting'), $sWhere); 
}
contactimporter_install304p2();


