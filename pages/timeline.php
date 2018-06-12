<?php
    $start_end_time = select("SELECT MIN(jt_time) starttime, MAX(jt_time) endtime FROM job_tweet WHERE jt_job=$id $filter");
    $start_end_time = mysqli_fetch_object($start_end_time);
    $duration = select("SELECT TIMESTAMPDIFF(HOUR, '$start_end_time->starttime', '$start_end_time->endtime') minutes,
                 TIMESTAMPDIFF(MINUTE, '$start_end_time->starttime', '$start_end_time->endtime') hours,
                TIMESTAMPDIFF(DAY, '$start_end_time->starttime', '$start_end_time->endtime') days,
                TIMESTAMPDIFF(MONTH, '$start_end_time->starttime', '$start_end_time->endtime') months");
    $duration = mysqli_fetch_object($duration);
    $interval = "hourly";
    $groupby = "";
    //hourly
    if($duration->hours <= 12)
        {
            $interval = "hourly";
            $groupby = "HOUR(jt_time), DAY(jt_time), MONTH(jt_time), YEAR(jt_time)";
        }
    //4 - hourly
    elseif($duration->hours <= 72)
        {
            $interval = "4hourly";
            $groupby = "HOUR(jt_time), DAY(jt_time), MONTH(jt_time), YEAR(jt_time)";
        }
    //daily
    elseif($duration->days <= 12)
        {
            $interval = "daily";
            $groupby = "DAY(jt_time), MONTH(jt_time), YEAR(jt_time)";
        }
    //weekly
    elseif($duration->days <= 84)
        {
            $interval = "weekly";
            $groupby = "DAY(jt_time), MONTH(jt_time), YEAR(jt_time)";
        }
    //fortnightly
    elseif($duration->months <= 6)
        {
            $interval = "fortnightly";
            $groupby = "MONTH(jt_time), YEAR(jt_time)";
        }
    //monthly
    else
        {
            $interval = "monthly";
            $groupby = "MONTH(jt_time), YEAR(jt_time)";
        }
    //print $interval;
    if($interval=='hourly' || $interval=='4hourly'){
        $tweets = select("SELECT MIN(jt_time) starttime, MAX(jt_time) endtime, SUM(IF(vm>=5,IF(am>=5,1,0),0)) happy, SUM(IF(vm<5,IF(am>=5,1,0),0)) upset, SUM(IF(vm<5,IF(am<5,1,0),0)) unhappy, SUM(IF(vm>=5,IF(am<5,1,0),0)) relaxed FROM `job_tweet` WHERE jt_job=7 $filter GROUP BY $groupby");
    } else{
        $tweets = select("SELECT MIN(DATE(jt_time)) starttime, MAX(DATE(jt_time)) endtime, SUM(IF(vm>=5,IF(am>=5,1,0),0)) happy, SUM(IF(vm<5,IF(am>=5,1,0),0)) upset, SUM(IF(vm<5,IF(am<5,1,0),0)) unhappy, SUM(IF(vm>=5,IF(am<5,1,0),0)) relaxed FROM `job_tweet` WHERE jt_job=7 $filter GROUP BY $groupby");
    }

    $categories = $relaxed = $happy = $unhappy = $upset = "";
    while ($tweet = mysqli_fetch_object($tweets)) {
        $categories .= ($categories!=""?",":"")."'$tweet->starttime'";
        $relaxed .= ($relaxed!=""?",":"").$tweet->relaxed;
        $happy .= ($happy!=""?",":"").$tweet->happy;
        $unhappy .= ($unhappy!=""?",":"").$tweet->unhappy;
        $upset .= ($upset!=""?",":"").$tweet->upset;
    }
    
$content = "<br clear='all' /><br clear='all' />";
$content .= "
<script type='text/javascript'>
\$(function () {
    \$('#container').highcharts({
        colors: ['#09093b', '#1a1a59', '#801515', '#550000'],
        chart: {
            type: 'column'
        },

        title: {
            text: 'Timeline'
        },

        xAxis: {
            categories: [$categories]
        },

        yAxis: {
            allowDecimals: false,
            min: 0,
            title: {
                text: 'Number of Tweets'
            }
        },

        tooltip: {
            formatter: function () {
                return '<b>' + this.x + '</b><br/>' +
                    this.series.name + ': ' + this.y + '<br/>' +
                    'Total: ' + this.point.stackTotal;
            }
        },

        plotOptions: {
            column: {
                stacking: 'normal'
            }
        },

        series: [{
            name: 'Relaxed',
            data: [$relaxed],
            stack: 'col1'
        }, {
            name: 'Happy',
            data: [$happy],
            stack: 'col1'
        }, {
            name: 'Unhappy',
            data: [$unhappy],
            stack: 'col2'
        }, {
            name: 'Upset',
            data: [$upset],
            stack: 'col2'
        }]
    });
});
</script>

<script src='js/highcharts.js'></script>
<script src='js/modules/exporting.js'></script>

<div id='container' style='min-width: 600px; height: 400px; margin: 0 auto'></div>";

print $content;
