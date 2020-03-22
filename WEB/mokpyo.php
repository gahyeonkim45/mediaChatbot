<?php include_once('./header.php'); ?>

<?php
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

$maxYsql = " SELECT MAX(sub_year) as max FROM subject
WHERE st_studentnum = '{$studentNum}' ";
$maxYres = mysqli_query($conn, $maxYsql);
$maxYrow = mysqli_fetch_array($maxYres);
$maxY    = $maxYrow['max'];




$credit_sql = " SELECT sub_subject, sub_score FROM subject
WHERE st_studentnum = '{$studentNum}' ";
$credit_res = mysqli_query($conn, $credit_sql);

while($credit_row = mysqli_fetch_array($credit_res)) {
	$s = " SELECT sub2_credit FROM subject2
  WHERE sub_subject = '{$credit_row[sub_subject]}' ";
  $e = mysqli_query($conn, $s);
  $r = mysqli_fetch_array($e);
  $totalCredit2 += $r['sub2_credit'];
  $totalScore2  += $credit_row['sub_score'] * $r['sub2_credit'];
}
$avgScore2 = round($totalScore2 / $totalCredit2, 2);
?>




<script src="./jquery.min.js"></script>
<script>
  function scoreValue(sub_subject, sub_score) {
   var totalScore = <?= $totalScore ?>;
   var totalCredit = <?= $totalCredit ?>;
   var innerHTML = "<table>";
   var innerHTML = innerHTML +"<tr>";
   var innerHTML = innerHTML +"<td>"+sub_subject+"</td>";
   var innerHTML = innerHTML +"<td>전체학점</td>";
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
   var innerHTML = innerHTML +"<td>"+score+"</td>";
   var innerHTML = innerHTML +"<td>"+avgScore+"</td>";
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
      border-bottom: 1px solid #ccc;
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
                    </div>  <!-- OK --> 

                    <div class="row">
                      <div class="col-lg-12">

                       <div class="col-lg-8" style=width:855px; height:700px;>
                        <div class="panel panel-default">

                          <div class="panel-heading">
                            <i class="fa fa-bell fa-fw"></i> 학년별 수강 학점 목록
                          </div>

                          <!-- /.panel-heading -->
                         
                           <div style="width:210px; height:300px; float:left; top: 50px; text-align:center;">
                            <table class='table table-striped'>
                               <tr>
                                 <th scope="cols">1학년</th>
                               </tr>
                               <tr>
                                 <td scope="row">
                                  <?php
                                  if(isset($maxY)) {
                                   if($maxY >= 1) {
                                    $sql = " SELECT * FROM subject
                                    WHERE st_studentnum = '{$studentNum}'
                                    AND sub_year = '1' ";
                                    $res = mysqli_query($conn, $sql);

                                    $i = 0;
                                    $total = 0;
                                    while($row = mysqli_fetch_array($res)) { 
                                     echo $row['sub_subject'] . '   ';
                                     if($row['sub_score'] == 4.5)
                                      echo 'A+';
                                    else if($row['sub_score'] == 4.0)
                                      echo 'A ';
                                    else if($row['sub_score'] == 3.5)
                                      echo 'B+';
                                    else if($row['sub_score'] == 3.0)
                                      echo 'B ';
                                    else if($row['sub_score'] == 2.5)
                                      echo 'C+';
                                    else if($row['sub_score'] == 2.0)
                                      echo 'C ';
                                    else if($row['sub_score'] == 1.5)
                                      echo 'D+';
                                    else if($row['sub_score'] == 1.0)
                                      echo 'D ';
                                    else
                                      echo 'F ';
                                    echo '<br>';
                                    $i++;
                                    $total += $row['sub_score'];
                                  }
                                  ?></td></td>
                                  <div><tr><td><h3><?= round($total/$i, 2) ?></h3></td></tr></div></table>
                                  <?php
                                }
                              }
                              ?>
                            </div>
                            <div style="width:210px; height:300px; float:left;  text-align:center;">
                              <div><table class='table table-striped'">
                                 <tr>
                                   <th scope="cols">2학년</th>
                                 </tr>
                                 <tr>
                                   <td scope="row">
                                    <?php
                                    if(isset($maxY)) {
                                      if($maxY >= 2) {
                                        $sql = " SELECT * FROM subject
                                        WHERE st_studentnum = '{$studentNum}'
                                        AND sub_year = '2' ";
                                        $res = mysqli_query($conn, $sql);

                                        $i = 0;
                                        $total = 0;
                                        while($row = mysqli_fetch_array($res)) { 
                                         echo $row['sub_subject'] . '   ';
                                         if($row['sub_score'] == 4.5)
                                          echo 'A+';
                                        else if($row['sub_score'] == 4.0)
                                          echo 'A ';
                                        else if($row['sub_score'] == 3.5)
                                          echo 'B+';
                                        else if($row['sub_score'] == 3.0)
                                          echo 'B ';
                                        else if($row['sub_score'] == 2.5)
                                          echo 'C+';
                                        else if($row['sub_score'] == 2.0)
                                          echo 'C ';
                                        else if($row['sub_score'] == 1.5)
                                          echo 'D+';
                                        else if($row['sub_score'] == 1.0)
                                          echo 'D ';
                                        else
                                          echo 'F ';
                                        echo '<br>';
                                        $i++;
                                        $total += $row['sub_score'];
                                      }
                                      ?></td></td>
                                      <div><tr><td><h3><?= round($total/$i, 2) ?></h3></td></tr></div></table>
                                      <?php
                                    }
                                  }
                                  ?>
                                </div>
                                <div style="width:210px; height:300px; float:left;  position: absolute; top: 42px; left: 430px; text-align:center;">
                                  <div><table class='table table-striped'">
                              
                                     <tr>
                                       <th scope="cols">3학년</th>
                                     </tr>
                                  
                                 
                                     <tr>
                                       <td scope="row">
                                        <?php
                                        if(isset($maxY)) {
                                          if($maxY >= 3) {
                                            $sql = " SELECT * FROM subject
                                            WHERE st_studentnum = '{$studentNum}'
                                            AND sub_year = '3' ";
                                            $res = mysqli_query($conn, $sql);

                                            $i = 0;
                                            $total = 0;
                                            while($row = mysqli_fetch_array($res)) { 
                                             echo $row['sub_subject'] . '   ';
                                             if($row['sub_score'] == 4.5)
                                              echo 'A+';
                                            else if($row['sub_score'] == 4.0)
                                              echo 'A ';
                                            else if($row['sub_score'] == 3.5)
                                              echo 'B+';
                                            else if($row['sub_score'] == 3.0)
                                              echo 'B ';
                                            else if($row['sub_score'] == 2.5)
                                              echo 'C+';
                                            else if($row['sub_score'] == 2.0)
                                              echo 'C ';
                                            else if($row['sub_score'] == 1.5)
                                              echo 'D+';
                                            else if($row['sub_score'] == 1.0)
                                              echo 'D ';
                                            else
                                              echo 'F ';
                                            echo '<br>';
                                            $i++;
                                            $total += $row['sub_score'];
                                          }
                                          ?></td></td>
                                          <div><tr><td><h3><?= round($total/$i, 2) ?></h3></td></tr></div></table>
                                          <?php
                                        }
                                      }
                                      ?>
                                    </div>
                                    <div style="width:210px; height:300px; float:left;  position: absolute; top: 0px; left: 200px; text-align:center;">
                                      <div><table class='table table-striped'">
                                     
                                         <tr>
                                           <th scope="cols">4학년</th>
										   
                                         </tr>
                                     
                                         <tr>
                                           <td scope="row">
                                            <?php
                                            if(isset($maxY)) {
                                              if($maxY >= 4) {
                                                $sql = " SELECT * FROM subject
                                                WHERE st_studentnum = '{$studentNum}'
                                                AND sub_year = '4' ";
                                                $res = mysqli_query($conn, $sql);

                                                $i = 0;
                                                $total = 0;
                                                while($row = mysqli_fetch_array($res)) { 
                                                 echo $row['sub_subject'] . '   ';
                                                 if($row['sub_score'] == 4.5)
                                                  echo 'A+';
                                                else if($row['sub_score'] == 4.0)
                                                  echo 'A ';
                                                else if($row['sub_score'] == 3.5)
                                                  echo 'B+';
                                                else if($row['sub_score'] == 3.0)
                                                  echo 'B ';
                                                else if($row['sub_score'] == 2.5)
                                                  echo 'C+';
                                                else if($row['sub_score'] == 2.0)
                                                  echo 'C ';
                                                else if($row['sub_score'] == 1.5)
                                                  echo 'D+';
                                                else if($row['sub_score'] == 1.0)
                                                  echo 'D ';
                                                else
                                                  echo 'F ';
                                                echo '<br>';
                                                $i++;
                                                $total += $row['sub_score'];
                                              }
                                              ?></td></td>
                                              <div><tr><td><h3><?= round($total/$i, 2) ?></h3></td></tr></div></table>
                                              <?php
                                            }
                                          }
                                          ?>
                                       </table> </div>
										</div>
                                      </div>
                                      <!-- /.panel-body -->
                                    </div>
                                    <!-- /.panel -->
                                  </div>
                                  </div>
                                  <div class="col-lg-4 panel panel-default">
                                    <div class="col-lg-12 panel-heading">
                                      <i class="fa fa-bar-chart-o fa-fw"></i> 현재상태
                                    </div>



                                    <div id="jb-header">
                                     <table class="table table-striped table-bordered table-hover " >
                                      <tr>
                                       <td>학점</td>
                                       <td>이수학점</td>
                                       <td>잔여학점</td>
                                     </tr>
                                     <tr>
                                       <td><?= $avgScore2 ?>/ 4.5</td>
                                       <td><?= $totalCredit2 ?>/ 128</td>
                                       <td><?= 128-$totalCredit2 ?></td>
                                     </tr>
                                   </table>
                                 </div>

                               </div>
                               <!--OK-->

                               <div class="col-lg-4 panel panel-default">
                                <div class="col-lg-12 panel-heading">
                                  <i class="fa fa-dashboard fa-fw"></i> 목표
                                </div>
                                <div id="jb-content2">    
                                  <form id="searchbox" action="<?= $PHP_SELF ?>" method="POST">
                                    <input type="text" id="search" style="width:300px; height:50px;"  placeholder="목표학점을 입력해주세요" name="grade"/>
                                    <button type="submit"style="width:40px;height:40px;" class="btn btn-default">
                                      <img src="https://d30y9cdsu7xlg0.cloudfront.net/png/197118-84.png" width="15px;"/></button>
                                    </form>
                                  </div>
                                  <div id="jb-content3">
                                    <?php
                                    if($_POST['grade'] != '') {
                                      $sql = " SELECT max(sub_year) as year, max(sub_semester) as semester FROM subject WHERE st_studentnum = '{$studentNum}' ";

                                      $res = mysqli_query($conn, $sql);
                                      $row = mysqli_fetch_array($res);
                                      if($row['semester'] == 2)
                                        $max = 2 * $row['year'];
                                      else
                                        $max = 2 * $row['year'] - 1;
                                      $grade = ($max +1) * $_POST['grade'] - $max *  $avgScore;
                                      echo "목표학점을 받으려면 다음학기에  {$grade} 학점을 받아야합니다.";
                                    }
                                    ?>
                                  </div>


                                </div><!-- OK -->
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

                            <style>
                              #jb-content {
                                width: 910px;
                                height: 400px;
                                padding: 20px;
                                margin: 0px auto;
                                margin-bottom: 20px;
                                float: left;

                              }

                              #jb-content2 {
                                width: 865px;
                                height: 50px;
                                padding: 20px;
                                margin: 0px auto;
                                margin-bottom: 20px;
                                float: left;

                              }
                              #jb-content3 {
                                width: 865px;
                                height: 30px;
                                padding: 20px;
                                margin: 0px auto;
                                margin-bottom: 20px;
                                float: left;

                              }


                              #jb-content4 {
                                width: 865px;
                                height: 400px;
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

                          </body>
                          </html>
