var Affiliate = {};

(function(affiliate, $){

})(Affiliate)

$Ready(function()
{
    var ele = $('#yncaffiliate_register_affiliate_form');
    if(!ele.length) return;
    $Core.loadStaticFile(ele.data('validjs'));
    $Core.loadStaticFile(ele.data('js'));
});