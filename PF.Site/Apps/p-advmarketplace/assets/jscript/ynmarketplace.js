
$Behavior.ynadvmarketplace_chart_statistic = function () {
    if($('.js_advmarketplace-chart-success').length > 0){
        $('.js_advmarketplace-chart-success').each(function(){
            var chartTotal = $(this).data('chart-total'),
                chartSuccess = $(this).data('chart-success');
            var chartPercent =  (chartSuccess/chartTotal)*100;
            $(this).css('width',chartPercent + '%');
        });
    }
}