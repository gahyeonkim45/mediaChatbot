<?php include_once('./src/dbConnect.php'); ?>
<?php

session_start();

if(!isset($_SESSION['member'])) {
    $kakao_id = $_GET['id'];

    $stNum_sql = " SELECT st_studentnum as num FROM student
            WHERE kakao_id='{$kakao_id}' ";
    $stNum_res = mysqli_query($conn, $stNum_sql);
    $stNum_row = mysqli_fetch_array($stNum_res);
    $studentNum = $stNum_row['num'];
    if($studentNum == '') {
        ?>
        <script>
        alert('챗봇에서 학번을 입력해주세요.(Ex. ###20111111)');
        </script>
        <?php
        exit;
    }
} else {
    $member = $_SESSION['member'];
    $studentNum = $member['st_studentnum'];
}

//$studentNum = '201321093';

$credit_sql = " SELECT sub_subject, sub_score FROM subject
         WHERE st_studentnum = '{$studentNum}' ";
$credit_res = mysqli_query($conn, $credit_sql);

while($credit_row = mysqli_fetch_array($credit_res)) {
    $s = " SELECT sub2_credit FROM subject2
        WHERE sub_subject = '{$credit_row[sub_subject]}' ";
    $e = mysqli_query($conn, $s);
    $r = mysqli_fetch_array($e);
    $totalCredit += $r['sub2_credit'];
    $totalScore  += $credit_row['sub_score'] * $r['sub2_credit'];
}
$avgScore = round($totalScore / $totalCredit, 2);

$sql = "select max(sub_year) from subject where st_studentnum='{$studentNum}'";

$res = mysqli_query($conn, $sql);

$row = mysqli_fetch_array($res); 
$year = $row[0];


$sql_semester = "select max(sub_semester) from subject where st_studentnum='{$studentNum}' and sub_year={$year}";

$res_semester = mysqli_query($conn, $sql_semester);

$row = mysqli_fetch_array($res_semester);
$semester = $row[0];


$label = "[";
$data  = "[";
for($i=1; $i<=$year; $i++) {
    if($i==$year)
        $j_length = $semester;
    else
        $j_length = 2;
    for($j=1; $j<=$j_length; $j++) {
        $avg_sql = " SELECT avg(sub_score) as avg FROM subject
        WHERE sub_year = {$i}
        AND sub_semester = {$j}
        AND st_studentnum = '{$studentNum}' ";
        $avg_res = mysqli_query($conn, $avg_sql);
        $avg_row = mysqli_fetch_array($avg_res);
        $avg     = round($avg_row['avg'], 2);

        $label .= "'{$i}학년 {$j}학기'";
        $data  .= $avg;
        if($i!=$year) {
            $label .= ",";
            $data .= ",";
        } else {
            if($j!=$j_length) {
                $label .= ",";
                $data .= ",";
            }
        }
    }
}
$label .= "]";
$data  .= "]";


$subject_sql = "SELECT sub_semester,sub_year,st_studentnum,ROUND(avg(sub_score),2) AS sub_score FROM subject JOIN (select * from subject2) table2 ON subject.sub_subject=table2.sub_subject group by sub_semester,sub_year,st_studentnum having st_studentnum = {$studentNum} order by sub_year DESC, sub_semester DESC";
$subject_res = mysqli_query($conn, $subject_sql);

$major_sql = "SELECT major from student where st_studentnum = {$studentNum}";
$major_res = mysqli_query($conn, $major_sql);
$major_row = mysqli_fetch_array($major_res);


if($major_row['major'] == "소셜미디어"){

    $sql1 = "SELECT COUNT(*) as count from subject JOIN (select * from subject2 where sub_group IN (1,6,7)) table2 ON subject.sub_subject = table2.sub_subject where st_studentnum='{$studentNum}'";
    $sql2 = "SELECT COUNT(*) as count from subject JOIN (select * from subject2 where sub_group IN (2,4,8)) table2 ON subject.sub_subject = table2.sub_subject where st_studentnum='{$studentNum}'";
    $sql3 = "SELECT COUNT(*) as count from subject JOIN (select * from subject2 where sub_group = 'NULL') table2 ON subject.sub_subject = table2.sub_subject where st_studentnum='{$studentNum}'";

    $sql1_res = mysqli_query($conn, $sql1);
    $sql1_row = mysqli_fetch_array($sql1_res);

    $sql2_res = mysqli_query($conn, $sql2);
    $sql2_row = mysqli_fetch_array($sql2_res);

    $sql3_res = mysqli_query($conn, $sql3);
    $sql3_row = mysqli_fetch_array($sql3_res);

}else if($major_row['major'] == "미디어콘텐츠"){

    $sql1 = "SELECT COUNT(*) as count from subject JOIN (select * from subject2 where sub_group IN (1,3,4)) table2 ON subject.sub_subject = table2.sub_subject where st_studentnum='{$studentNum}'";
    $sql2 = "SELECT COUNT(*) as count from subject JOIN (select * from subject2 where sub_group IN (2,5,7)) table2 ON subject.sub_subject = table2.sub_subject where st_studentnum='{$studentNum}'";
    $sql3 = "SELECT COUNT(*) as count from subject JOIN (select * from subject2 where sub_group = 'NULL') table2 ON subject.sub_subject = table2.sub_subject where st_studentnum='{$studentNum}'";

    $sql1_res = mysqli_query($conn, $sql1);
    $sql1_row = mysqli_fetch_array($sql1_res);

    $sql2_res = mysqli_query($conn, $sql2);
    $sql2_row = mysqli_fetch_array($sql2_res);

    $sql3_res = mysqli_query($conn, $sql3);
    $sql3_row = mysqli_fetch_array($sql3_res);

}


?>

<html lang="ko">

<head>

    <!-- jQuery -->
    <script src="./jquery.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>

    <script>

        $(function(){
            var label = "<?= $label ?>";
            var data  = "<?= $data ?>";
            var ctx = document.getElementById("myChart").getContext('2d');
            
            new Chart(ctx,{
                "type":"line",
                "data":
                {
                    "labels":<?= $label ?>,
                    "datasets":
                    [
                    {
                        "label":"학기별 학점변화",
                        "data":<?= $data ?>,
                        "fill":false,
                        "borderColor":"rgb(75, 192, 192)",
                        "lineTension":0.3
                    }
                    ]
                },
                "options":{scales: {
                    yAxes: [{
                        ticks: {
                            max: 4.5,
                            min: 0,
                            stepSize: 0.5
                        }
                    }]
                }}
            });

            Morris.Donut({
                element: 'morris-donut-chart',
                data: [{
                    label: "전필",
                    value: <?= $sql1_row['count'] ?>
                }, {
                    label: "전선",
                    value: <?= $sql2_row['count'] ?>
                }, {
                    label: "교양",
                    value: <?= $sql3_row['count'] ?>

                }],
                resize: true
            });
        });



    </script>

    <style>
      #jb-content {
        width: 950px;
        height: 600px;
        padding: 20px;
        margin: 0px auto;
        margin-bottom: 20px;
        border: 1px solid #bcbcbc;
    }

    #jb-content2 {
        width: 540px;
        height: 70px;
        padding: 20px;
        margin-bottom: 20px;
        float: left;
        border: 1px solid #bcbcbc;
    }
    #jb-sidebar {
        width: 300px;
        height: 300px;
        padding: 20px;
        margin-bottom: 20px;
        float: right;
        border: 1px solid #bcbcbc;
    }
    #jb-footer {
        clear: both;
        padding: 20px;
        border: 1px solid #bcbcbc;
    }
</style>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">

<title></title>

<!-- Bootstrap Core CSS -->
<link href="./bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Core JavaScript -->
<script src="./bootstrap.min.js"></script>

<!-- MetisMenu CSS -->
<link href="./metisMenu.min.css" rel="stylesheet">

<!-- Custom CSS -->
<link href="./sb-admin-2.css" rel="stylesheet">

<!-- Morris Charts CSS -->
<link href="./morris.css" rel="stylesheet">

<!-- Custom Fonts -->
<link href="./font-awesome.min.css" rel="stylesheet" type="text/css">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body>

        <div id="wrapper">

            <!-- Navigation -->
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.html">Ajou univ. Media 
                    </a>
                </div>
                <!-- /.navbar-header -->
                    <?php if(isset($_SESSION['member'])) { ?>
                     <a href="./src/controller.php?mod=logout" class="navbar-brand" style="right: 0; position: absolute;"><span>logout</span></a>  <?php } ?>

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">
                        <!-- <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            </div>
                        </li> -->
                        <li>
                            <a href="./index_test.php"><i class="fa fa-bar-chart-o fa-fw"></i>학점 그래프<span class="fa arrow"></span></a>
                        </li>
                        <li>
                            <a href="./jesugang.php"><i class="fa fa-table fa-fw"></i>재수강 학점 변화</a>
                        </li>
                        <li>
                            <a href="./mokpyo.php"><i class="fa fa-dashboard fa-fw"></i>목표 학점 설정</a>
                        </li>
						
					


                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>   

            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-8 panel panel-default">
                        <div class="col-lg-12 panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Graph
                        </div>


                        <canvas id="myChart" width="400" height="250"></canvas>
                        <!-- /.panel-heading -->
                        <!-- <div class="panel-body">
                        </div>
                    --><!-- /.panel-body -->
                </div>


                <!-- /.col-lg-8 -->
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bell fa-fw"></i> 학점 목록
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="list-group">
                                <?php 
                                while($row = mysqli_fetch_array($subject_res)) { ?> 
                                    <li class="list-group-item dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <?php
                                        $str = "";
                                        $str .= $row['sub_year'];
                                        $str .= "학년 ";
                                        $str .= $row['sub_semester'];
                                        $str .= "학기 : ";
                                        $str .= $row['sub_score'];
                                        $str .= " / 4.5";
                                        echo $str; 
                                     ?>
                                  </a>
                                  <ul class="dropdown-menu">
                                  <?php

                                    $getsql = "SELECT * from subject where st_studentnum='{$studentNum}' and sub_year = ".$row['sub_year']." and sub_semester = ".$row['sub_semester']."";
                                    $get_res = mysqli_query($conn, $getsql);

                                    while($row = mysqli_fetch_array($get_res)) {
                                        echo '<li>'.$row['sub_subject'].' : '.$row['sub_score'].'</li>';
                                    }

                                   ?>
                                  </ul>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> 이수 전공 비율
                        </div>
                        <div class="panel-body">
                            <div id="morris-donut-chart"></div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.col-lg-4 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /#page-wrapper -->

        </div>
        <!-- /#wrapper -->


        <!-- Metis Menu Plugin JavaScript -->
        <script src="./metisMenu.min.js"></script>

        <!-- Morris Charts JavaScript -->
        <script src="./raphael.min.js"></script>
        <script src="./morris.min.js"></script>

        <!-- Custom Theme JavaScript -->
        <script src="./sb-admin-2.js"></script>

    </body>

    </html>
