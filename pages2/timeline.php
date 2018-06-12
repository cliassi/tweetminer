<?php
    $start_end_time = select("SELECT MIN(CreateAt) starttime, MAX(CreateAt) endtime FROM `job_tweet` jt, `tweet` t WHERE jt_job=$id AND jt_tweet=t.id");
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
            $groupby = "HOUR(CreateAt), DAY(CreateAt), MONTH(CreateAt), YEAR(CreateAt)";
        } 
    //4 - hourly
    elseif($duration->hours <= 72)
        {
            $interval = "4hourly";
            $groupby = "HOUR(CreateAt), DAY(CreateAt), MONTH(CreateAt), YEAR(CreateAt)";
        } 
    //daily
    elseif($duration->days <= 12)
        {
            $interval = "daily";
            $groupby = "DAY(CreateAt), MONTH(CreateAt), YEAR(CreateAt)";
        } 
    //weekly
    elseif($duration->days <= 84)
        {
            $interval = "weekly";
            $groupby = "DAY(CreateAt), MONTH(CreateAt), YEAR(CreateAt)";
        } 
    //fortnightly
    elseif($duration->months <= 6)
        {
            $interval = "fortnightly";
            $groupby = "MONTH(CreateAt), YEAR(CreateAt)";
        } 
    //monthly
    else
        {
            $interval = "monthly";
            $groupby = "MONTH(CreateAt), YEAR(CreateAt)";
        } 
    //print $interval;
    if($interval=='hourly' || $interval=='4hourly'){
        $tweets = select("SELECT MIN(CreateAt) starttime, MAX(CreateAt) endtime, SUM(IF(vm>=5,IF(am>=5,1,0),0)) happy, SUM(IF(vm<5,IF(am>=5,1,0),0)) upset, SUM(IF(vm<5,IF(am<5,1,0),0)) unhappy, SUM(IF(vm>=5,IF(am<5,1,0),0)) relaxed FROM `job_tweet` jt, `tweet` t WHERE jt_job=7 AND jt_tweet=t.id GROUP BY $groupby");
    } else{
        $tweets = select("SELECT MIN(DATE(CreateAt)) starttime, MAX(DATE(CreateAt)) endtime, SUM(IF(vm>=5,IF(am>=5,1,0),0)) happy, SUM(IF(vm<5,IF(am>=5,1,0),0)) upset, SUM(IF(vm<5,IF(am<5,1,0),0)) unhappy, SUM(IF(vm>=5,IF(am<5,1,0),0)) relaxed FROM `job_tweet` jt, `tweet` t WHERE jt_job=7 AND jt_tweet=t.id GROUP BY $groupby");
    }

    $categories = $relaxed = $happy = $unhappy = $upset = "";
    while ($tweet = mysqli_fetch_object($tweets)) {
        $categories .= ($categories!=""?",":"")."'$tweet->starttime'";
        $relaxed .= ($relaxed!=""?",":"").$tweet->relaxed;
        $happy .= ($happy!=""?",":"").$tweet->happy;
        $unhappy .= ($unhappy!=""?",":"").$tweet->unhappy;
        $upset .= ($upset!=""?",":"").$tweet->upset;
    }

$content = "
<br clear='all' />
<br clear='all' />
<script type='text/javascript'>
$(function () {
    $('#container').highcharts({
        //colors: ['#7cb5ec', '#f7a35c', '#90ee7e', '#7798BF', '#aaeeee', '#ff0066', '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
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
                text: 'Number of fruits'
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

<div id='container' style='min-width: 600px; height: 400px; margin: 0 auto'></div>
";