var $bUserToolTipIsHover = false;
var $bUserActualToolTipIsHover = false;
var $iUserToolTipWaitTime = 900;
var $oUserToolTipObject = null;
var $sHoveringOn = null;
var aHideUsers = new Array();
var bUserInfoLogDebug = false;

$Core.userInfoLog = function(sLog){
}

$Core.loadUserToolTip = function($sUserName)
{	   
}

$Core.closeUserToolTip = function(sUser)
{	

}

$Core.showUserToolTip = function(sUser)
{

}

$Behavior.userHoverToolTip = function()
{	
        $(document).ready(function()
        {
                if(ynfbpp !== undefined && ynfbpp !== null && ynfbpp)
                {
                        ynfbpp.init();        
                        ynfbpp.boot();        
                        ynfbpp.clearCached();        
                }
        });

}