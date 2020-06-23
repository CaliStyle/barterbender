<div class="charts">
    <div class="first_chart_holder">
        <div class="first_chart_title">{_p var='ecommerce.sale_symbol_by_month' symbol=$sCurrencySymbol}</div>
        <div class="first_chart_content chart-container">
            <div id="first_chart" class="chart-placeholder"></div>
        </div>
    </div>
    <div class="second_chart_holder {if $sType == 'statistic'}hide{/if}" >
        <div class="second_chart_title">{_p('Total amount charged by site')}</div>
        <div class="second_chart_content chart-container">
            <div id="second_chart" class="chart-placeholder"></div>
        </div>
    </div>
    <div class="third_chart_holder">
        <div class="third_chart_title">{_p var='ecommerce.number_of_products_sold'}</div>
        <div class="third_chart_content chart-container">
            <div id="third_chart" class="chart-placeholder"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
	var aXAxis = {$sTempXAxis};

    var aFirstChartsData = {$sFirstChartData};
    if (aFirstChartsData.length == 1) {l}
        aFirstChartsData[1] = [1, aFirstChartsData[0][1]];
    {r}

    var aPublishFeeChartData = {$sPublishFeeChartData};
    var aFeatureFeeChartData = {$sFeatureFeeChartData};
    var aCommissionFeeChartData = {$sCommissionFeeChartData};

    var aThirdChartsData = {$sThirdChartData};
    if (aThirdChartsData.length == 1) {l}
        aThirdChartsData[1] = [1, aThirdChartsData[0][1]];
    {r}
</script>

{literal}
<script type="text/javascript">
    $Behavior.initFirstChart = function() {
        if($('#first_chart').length == 0) return false;
		$.plot("#first_chart", [{data: aFirstChartsData, label: $('.first_chart_title').text()}], {
            series: {
                lines: {
                    show: true
                }
            },
			xaxis: {
				ticks: aXAxis
			},
            legend: {position: "se"}
        });
	};
    
    $Behavior.initSecondChart = function() {
        if($('#second_chart').length == 0) return false;
		var stack = 0,
			bars = true,
			lines = false,
			steps = false;

		$.plot("#second_chart", [
			{ data: aPublishFeeChartData, label: oTranslations['ecommerce.publish_fee']},
			{ data: aFeatureFeeChartData, label: oTranslations['ecommerce.featured_fee']},
			{ data: aCommissionFeeChartData, label: oTranslations['ecommerce.commission_fee']}
		], {
            series: {
                stack: stack,
                lines: {
                    show: false,
                    fill: true,
                    steps: steps
                },
                bars: {
                    show: bars,
                    barWidth: 0.6
                }
            },
			xaxis: {
				ticks: aXAxis
			}
        });
	};
    
	$Behavior.initThirdChart = function() {
        if($('#third_chart').length == 0) return false;
		$.plot("#third_chart", [{data: aThirdChartsData, label: oTranslations['ecommerce.number_of_products_sold']}], {
            xaxis: {
				ticks: aXAxis
			},
            legend: {position: "se"}
        });
	};
</script>

<style>
    .chart-container {
        box-sizing: border-box;
        width: 100%;
        height: 450px;
        padding: 20px 15px 15px 15px;
        margin: 15px auto 30px auto;
        border: 1px solid #ddd;
        background: #fff;
        background: linear-gradient(#f6f6f6 0, #fff 50px);
        background: -o-linear-gradient(#f6f6f6 0, #fff 50px);
        background: -ms-linear-gradient(#f6f6f6 0, #fff 50px);
        background: -moz-linear-gradient(#f6f6f6 0, #fff 50px);
        background: -webkit-linear-gradient(#f6f6f6 0, #fff 50px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        -o-box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        -ms-box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        -moz-box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        -webkit-box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .chart-placeholder {
        width: 100%;
        height: 100%;
        font-size: 14px;
        line-height: 1.2em;
    }
</style>
{/literal}