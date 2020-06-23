<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/8/17
 * Time: 15:37
 */
?>
<div class="page_section_menu" data-example-id="togglable-tabs">
    <ul class="nav nav-tabs nav-justified" role="tablist">
        <li role="presentation" class="active">
            <a href="#statistics_line" role="tab" data-toggle="tab" aria-controls="statistics_line" aria-expanded="true">{_p var='line_chart'}</a>
        </li>
        <li role="presentation" class="">
            <a href="#statistics_pie" role="tab" data-toggle="tab" aria-controls="statistics_pie" aria-expanded="true">{_p var='pie_chart'}</a>
        </li>
    </ul>
</div>
<div class="tab-content chart-container">
    <div class="t_center"><b>{$sChartName}</b></div>
    <div role="tabpanel" class="tab-pane fade in active chart-placeholder" id="statistics_line">
    </div>
    <div role="tabpanel" class="tab-pane fade chart-placeholder" id="statistics_pie">
    </div>
</div>
<script type="text/javascript">
    var line_aXAxis = {$sChartTicks},
        line_data = {$aLineChartFinalData},
        line_yAxesName = '{$yAxesName}',
        pie_data = {$aPieChartFinalData};
</script>
{literal}
<script type="text/javascript">
    $Behavior.initLineChart = function(){
        if (typeof($.plot) == 'undefined') {
            var script = document.createElement('script');
            script.src = '{/literal}{param var='core.path_actual'}{literal}PF.Site/Apps/ync-affiliate/assets/jscript/jquery.flot.js';
            script.onload = loadChart;
            document.getElementsByTagName("head")[0].appendChild(script);
        } else {
            loadChart();
        }
    }
    var loadChart = function()
        {
            var script2 = document.createElement('script');
            script2.src = '{/literal}{param var='core.path_actual'}{literal}PF.Site/Apps/ync-affiliate/assets/jscript/jquery.flot.axislabels.js';
            document.getElementsByTagName("head")[0].appendChild(script2);
            var script3 = document.createElement('script');
            script3.src = '{/literal}{param var='core.path_actual'}{literal}PF.Site/Apps/ync-affiliate/assets/jscript/jquery.flot.pie.js';
            script3.onload = loadPieChart;
            document.getElementsByTagName("head")[0].appendChild(script3);
            if($('#statistics_line').length){
                $.plot('#statistics_line',line_data,{
                    series: {
                        stack: 0,
                        lines: {
                            show: true,
                        },
                    },
                    points: {
                        radius: 3,
                        fill: true,
                        show: true
                    },
                    xaxis: {
                        ticks: (line_aXAxis.length > 10) ? [] : line_aXAxis,
                    },
                    grid: {
                        hoverable: true,
                        clickable: true,
                        borderWidth: 1,
                        show: true
                    },
                    yaxes: {
                        axisLabel:line_yAxesName,
                        axisLabelUseCanvas: true,
                        axisLabelFontSizePixels: 12,
                        axisLabelFontFamily: 'Verdana, Arial',
                        axisLabelPadding: 3,

                    }
                });
            }
            var previousPoint = null, previousLabel = null;
            $("#statistics_line").bind("plothover", function (event, pos, item) {
                // axis coordinates for other axes, if present, are in pos.x2, pos.x3, ...
                // if you need global screen coordinates, they are pos.pageX, pos.pageY

                if (item) {
                    if ((previousLabel != item.series.label) || (previousPoint != item.dataIndex)) {
                        previousPoint = item.dataIndex;
                        previousLabel = item.series.label;
                        $("#tooltip").remove();

                        var x = item.datapoint[0];
                        var y = item.datapoint[1];

                        var color = item.series.color;

                        showTooltip(item.pageX,
                            item.pageY,
                            color,
                            line_aXAxis[item.dataIndex][1] + "<br/><strong>" + item.series.label + "</strong>: <strong>" + y + "</strong>");

                    }
                    else {
                        $("#tooltip").remove();
                        previousPoint = null;
                        previousLabel = null;
                    }
                }
                else{
                    $("#tooltip").remove();
                }
            });

        }
    var loadPieChart = function(){
        if($('#statistics_pie').length)
        {
            console.log('init');
            $.plot('#statistics_pie',pie_data,{
                series: {
                    pie: {
                        show: true,
                        radius: 0.8,
                        offset:{
                            left: -30
                        },
                        label: {
                            formatter: function (label, series) {
                                return '';
                            },
                            show: true,
                            radius: 3/4,
                            background: {
                                opacity: 0.5
                            }
                        }
                    }
                },
                grid: {
                    hoverable: (pie_data.length) ? true : false,
                    clickable: (pie_data.length) ? true : false
                },

            });
        }
        var previousPieLabel = null;
        $("#statistics_pie").bind("plothover", function (event, pos, item) {
            if (item) {
                if(previousPieLabel != item.series.label){
                    $(".pie_tooltip").remove();
                    previousPieLabel = item.series.label;
                    var color = item.series.color;

                    showTooltip2(
                        color,
                        "<strong>" + item.series.label + "</strong>: <strong>" + item.series.percent + "%</strong>");
                }

            }
            else{
                $(".pie_tooltip").remove();
                previousPieLabel = null;
            }
        });
    }
    function showTooltip(x, y, color, contents) {
        $('<div id="tooltip">' + contents + '</div>').css({
            position: 'absolute',
            display: 'none',
            top: y - 50,
            left: x - 50,
            border: '2px solid ' + color,
            padding: '3px',
            'font-size': '12px',
            'border-radius': '5px',
            'background-color': '#fff',
            'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
            opacity: 0.9
        }).appendTo("body").fadeIn(200);
    }
    function showTooltip2(color, contents) {
        $('<div id="tooltip" class="pie_tooltip">' + contents + '</div>').css({
            position: 'absolute',
            display: 'none',
            top: 40,
            left: 20,
            border: '2px solid ' + color,
            padding: '3px',
            'font-size': '12px',
            'border-radius': '5px',
            'background-color': '#fff',
            'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
            opacity: 0.9
        }).appendTo($(".chart-placeholder")).fadeIn(200);
    }
</script>
<style>
    .chart-container {
        box-sizing: border-box;
        width: 100%;
        height: 500px;
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
        position: relative;
    }
    .chart-placeholder {
        width: 100%;
        height: 450px;
        font-size: 14px;
        line-height: 1.2em;
    }
    .chart-placeholder canvas{
        width: 100% !important;
    }
    .legendColorBox{
        padding-right:5px;
    }
</style>
{/literal}
