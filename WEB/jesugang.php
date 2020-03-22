<?php include_once('./header.php'); ?>

<?php
$subject = $_POST['subject'];

$subject_sql = "SELECT * FROM subject WHERE st_studentnum = '{$studentNum}' ";

if($subject != '') {
    $subject_sql .= " AND sub_subject LIKE '%{$subject}%' LIMIT 20 ";
}else{
    $subject_sql .= "LIMIT 20";
}

$subject_res = mysqli_query($conn, $subject_sql);

$object_sql = " SELECT st_object FROM student
WHERE st_studentnum = '{$studentNum}' ";
$object_res = mysqli_query($conn, $object_sql);
$object_row = mysqli_fetch_array($object_res);
$object = $object_row['st_object'];

$credit_sql = " SELECT sub_subject, sub_score FROM subject
WHERE st_studentnum = '{$studentNum}' ";
$credit_res = mysqli_query($conn, $credit_sql);
while($row = mysqli_fetch_array($credit_res)) {
    $s = " SELECT sub2_credit FROM subject2
    WHERE sub2_num = '{$row[sub2_num]}' ";
    $e = mysqli_query($conn, $s);
    $r = mysqli_fetch_array($e);
    $totalCredit += $r['sub2_credit'];
    $totalScore  += $row['sub_score'] * $r['sub2_credit'];
}
$avgScore =  round($totalScore / $totalCredit, 2);
//}
?>

<script src="./jquery.min.js"></script>
<script>

    function scoreValue(sub_subject, sub_score) {
        var totalScore = <?= $totalScore ?>;
        var totalCredit = <?= $totalCredit ?>;
        var innerHTML = "<table class='table table-striped'>";
        var innerHTML = innerHTML +"<tr>";
        var innerHTML = innerHTML +"<th><h5>"+sub_subject+"</h5></th>";
        var innerHTML = innerHTML +"<th><h5>변경학점</h5></th>";
        var innerHTML = innerHTML +"</tr>"

        for(sub_score += 0.5;sub_score <= 4.0; sub_score+=0.5) {
            totalScore += sub_score;
            var avgScore = totalScore / totalCredit;
            avgScore = avgScore.toFixed(2);
            var score = "";
            var innerHTML = innerHTML +"<tr>";
            if(sub_score == 4.0)
                score = "A";
            else if(sub_score == 3.5)
                score = "B+";
            else if(sub_score == 3.0)
                score = "B";
            else if(sub_score == 2.5)
                score = "C+";
            else if(sub_score == 2.0)
                score = "C";
            else if(sub_score == 1.5)
                score = "D+";
            var innerHTML = innerHTML +"<th>"+score+"</th>";
            var innerHTML = innerHTML +"<th>"+avgScore+"</th>";
            var innerHTML = innerHTML +"</tr>";
        }
        var innerHTML = innerHTML +"</table>";

        var scoreValue = document.getElementById("scoreValue");
        scoreValue.innerHTML = innerHTML;
    }
</script>

<style>
    a{text-decoration:none;}

    table.type09 {
        border-collapse: collapse;
        text-align: left;
        line-height: 1.5;

    }
    table.type09 thead th {
        padding: 5px;
        font-weight: bold;
        vertical-align: top;
        color: #369;
        border-bottom: 3px solid #036;
    }
    table.type09 tbody th {
        width: 170px;
        padding: 10px;
        font-weight: bold;
        vertical-align: top;
        border-bottom: 1px solid #ccc;
        background: #f3f6f7;
    }
    table.type09 td {
        width: 70px;
        padding: 10px;
        vertical-align: top;
        border-bottom: 1px solid #ccc;
    }



    h5{
        color: #369;
        padding : 5px;}



        th{

            padding : 10px;
            vertical-align: center;
            border-bottom: 1px solid #ccc;}

            #jb-content {
                width: 910px;
                padding: 20px;
                margin: 0px auto;
                margin-bottom: 20px;
                float: left;

            }

            #jb-sidebar {
                width: 300px;
                height: 300px;
                padding: 20px;
                margin-bottom: 20px;
                float: right;

            }
            #jb-footer {
                clear: both;
                padding: 20px;

            }



        </style>
        
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

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
                        <a class="navbar-brand" href="index.html">Ajou univ. Media</a>
                        
                    </div>
                    <!-- /.navbar-header -->
                    <?php if(isset($_SESSION['member'])) { ?>
                     <a href="./src/controller.php?mod=logout" class="navbar-brand" style="right: 0; position: absolute;"><span>logout</span></a>  <?php } ?>

                    <div class="navbar-default sidebar" role="navigation">
                        <div class="sidebar-nav navbar-collapse">
                            <ul class="nav" id="side-menu">
                                <li>
                                    <a href="./index_test.php"><i class="fa fa-bar-chart-o fa-fw"></i>학점 그래프</span></a>
                                </li>
                                <li>
                                    <a href="./jesugang.php"><i class="fa fa-table fa-fw"></i>재수강 학점 변화<span class="fa arrow"></a>
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

                <div style="height:1000px;" id="page-wrapper" class="row">
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header"></h1>
                        </div>
                        <!-- /.col-lg-12 -->
                    </div>   

                    <div class="row" style="height:1000px;">
                        <div class="col-lg-8" style="float: left">
                            <div class="col-lg-12 panel panel-default" style="height:1000px;">
                                <div class="col-lg-12 panel-heading">
                                    <i class="fa fa-table fa-fw"></i> 수강 목록
                                </div>


                                <div id="jb-content" class="col-lg-9">

                                    <div id="wrapper col-lg-9" >

                                        <div id="jb-sidebar" class="table-responsive col-lg-7" style="width: 50%;">
                                            <div id="scoreValue">
                                            </div>
                                        </div>

                                        <br/>

                                        <div class="table-responsive col-lg-7" style="width: 50%;">
                                            <table class="table table-striped table-bordered table-hover " >
                                               <thead >
                                                   <tr>
                                                       <th scope="cols" style="text-align: center;">과목</th>

                                                       <th scope="cols" style="text-align: center;">성적</th>
                                                   </tr>
                                               </thead>
                                               <tbody style="text-align: center;">
                                                   <tr>
                                                       <td></td>
                                                   </td></td>
                                                   <td></td>
                                               </tr>

                                               <?php

                                               while($row = mysqli_fetch_array($subject_res)) { ?>
                                               <tr>
                                                <th scope="row" style="text-align: center;"><a href="javascript:scoreValue('<?= $row[sub_subject] ?>', <?= $row[sub_score] ?>);"><?= $row['sub_subject'] ?></a></th>

                                                <td>
                                                    <?php
                                                    if($row['sub_score'] == '4.5')
                                                        echo 'A+';
                                                    else if($row['sub_score'] == '4.0')
                                                        echo 'A  ';
                                                    else if($row['sub_score'] == '3.5')
                                                        echo 'B+ ';
                                                    else if($row['sub_score'] == '3.0')
                                                        echo 'B  ';
                                                    else if($row['sub_score'] == '2.5')
                                                        echo 'C+ ';
                                                    else if($row['sub_score'] == '2.0')
                                                        echo 'C  ';
                                                    else if($row['sub_score'] == '1.5')
                                                        echo 'D+ ';
                                                    else if($row['sub_score'] == '1.0')
                                                        echo 'D  ';
                                                    else
                                                        echo 'F  ';
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <br/>
                        </div>
                        <!-- /.panel-heading -->
                        <!-- <div class="panel-body">
                        </div>
                    --><!-- /.panel-body -->

                </div>
                </div>

                <div id="jb-content2" class= "input-group custom-search-form col-lg-4" style="float: left">

                    <form method="POST" action="jesugang.php">
                        <input type="text" id="search" class="form-control col-lg-12" style="float: left; width:80%;" placeholder="과목을 검색하세요"  name="subject"/>

                        <span class="input-group-btn" style="float: left;" class="col-lg-4">
                            <button class="btn btn-default" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>

                    </div>
                </div>
            </div>

        </body>
        </html>
