<?php
    $id = 5;
    $tweets = select("SELECT *, (SELECT CONCAT(AVG(valencemean), ' ', AVG(vmsd), ' ', AVG(arousalmean), ' ', AVG(amsd)) FROM anew_female WHERE LOCATE(description, jt.`jt_anew`)>0) m FROM `job_tweet` jt WHERE jt_job=$id");
    
?>
<style type="text/css">
    ${demo.css}
    .highcharts-legend{
        display: none;
    }
</style>
<script type="text/javascript">
$(function () {
$('#container').highcharts({
chart: {
    type: 'scatter',
    zoomType: 'xy'
},
title: {
    text: ''
},
subtitle: {
    text: ''
},
xAxis: {
    title: {
        enabled: true,
        text: 'Height (cm)'
    },
    startOnTick: true,
    endOnTick: true,
    showLastLabel: true
},
yAxis: {
    title: {
        text: 'Weight (kg)'
    }
},
legend: {
    layout: 'vertical',
    align: 'left',
    verticalAlign: 'top',
    x: 100,
    y: 70,
    floating: true,
    backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF',
    borderWidth: 1
},
plotOptions: {
    scatter: {
        marker: {
            radius: 5,
            states: {
                hover: {
                    enabled: true,
                    lineColor: 'rgb(100,100,100)'
                }
            }
        },
        states: {
            hover: {
                marker: {
                    enabled: false
                }
            }
        },
        tooltip: {
            headerFormat: '<b>{series.name}</b><br>',
            pointFormat: '{point.x} cm, {point.y} kg'
        }
    }
},
series: [{
    name: '',
    //color: 'rgba(223, 83, 83, .5)',
    colorByPoint: true,
    data: [
        <?php
            while ($tweet = mysqli_fetch_object($tweets)) {
                $anew = explode(" ", $tweet->m);
                print "[{$anew[0]},{$anew[2]}],";
            }
        ?>
        ]
}]
});
});


</script>
<script src="js/highcharts.js"></script>
<script src="js/modules/exporting.js"></script>

<div id="container" style="min-width: 310px; height: 400px; max-width: 800px; margin: 0 auto"></div>