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
		alert('챗봇에서 학번을 입력해주세요.');
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
?>

<html>
<head>
</head>

<!--
<style>
 *
 {
  margin:0;
  padding:0;
 }
 #jb-container
 {
  width:1000px;
  min-height:800px;
  margin:0px auto;
  padding:20px;
  
 }
 #jb-header
 {
  width:380px;
  height:70px;
  padding:20px;
  margin:0px auto;
  margin-bottom:20px;
  
 }
 .jb-hd-table
 {
  width:100%;
  height:100%;
  text-align:center;
  border-collapse:collapse;
  border-spacing:0;
 }
 .jb-hd-table tr:nth-child(1)
 {
  background-color:#ccc;
 }
 .jb-hd-table td
 {
  width:33.33333%;
 }







#cssmenu ul {

  margin: 0;

  padding: 7px 6px 0;

  background: #7d7d7d repeat-x 0 -110px;

  line-height: 100%;

  border-radius: 1em;

  font: normal 0.5333333333333333em Arial, Helvetica, sans-serif;

  -webkit-border-radius: 5px;

  -moz-border-radius: 5px;

  border-radius: 5px;

  -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);

  -moz-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);

  width: auto;

}

#cssmenu li {

  margin: 0 5px;

  padding: 0 0 8px;

  float: left;

  position: relative;

  list-style: none;

}

#cssmenu a,

#cssmenu a:link {



  font-size: 13px;

  color: #e7e5e5;

  text-decoration: none;

  display: block;

  padding: 8px 20px;

  margin: 0;

  border-radius: 5px;

  -webkit-border-radius: 5px;

  -moz-border-radius: 5px;

  text-shadow: 0 1px 1px rgba(0, 0, 0, 0.3);

}

#cssmenu a:hover {

  background: #000;

  color: #fff;

}

#cssmenu .active a,

#cssmenu li:hover > a {

  background: #979797 repeat-x 0 -40px;

  background: #666666 repeat-x 0 -40px;

 

  border-top: solid 1px  #f8f8f8;

  -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);

  -moz-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);

  box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);

 

}

#cssmenu ul ul li:hover a,

#cssmenu li:hover li a {

  background: none;

  border: none;

 

  -webkit-box-shadow: none;

  -moz-box-shadow: none;

}

#cssmenu ul ul a:hover {

  background: #7d7d7d repeat-x 0 -100px !important;



  -webkit-border-radius: 5px;

  -moz-border-radius: 5px;

  border-radius: 5px;

  text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);

}

#cssmenu li:hover > ul {

  display: block;

}

#cssmenu ul ul {

  z-index: 1000;

  display: none;

  margin: 0;

  padding: 0;

  width: 185px;

  position: absolute;

  top: 40px;

  left: 0;

  background: #ffffff repeat-x 0 0;

  border: solid 1px #b4b4b4;

  -webkit-border-radius: 5px;

  -moz-border-radius: 5px;

  border-radius: 5px;

  -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);

  -moz-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);

  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);

}

#cssmenu ul ul li {

  float: none;

  margin: 0;

  padding: 3px;

}

#cssmenu ul ul a,

#cssmenu ul ul a:link {

  font-weight: normal;

  font-size: 12px;

}
ss
#cssmenu ul:after {

  content: '.';

  display: block;

  clear: both;

  visibility: hidden;

  line-height: 0;

  height: 0;

}

* html #cssmenu ul {

  height: 1%;

}




</style>
-->


<!--


<body>


<div id='cssmenu'>

  <ul>

   <li class='active'>  <?php if(isset($_SESSION['member'])) { ?>
  <a href="./src/controller.php?mod=logout"><span>로그아웃</span></a></li>



   <li class='has-sub'><a href="./hakjeomgraph.php"><span>학점 그래프</span></a></li>

   <li class='has-sub'><a href="./jesugang.php"><span>재수강 학점 변화</span></a></li>

   <li class='has-sub'><a href="./mokpyo.php"><span>목표학점 설정</span></a></li>

   <?php } ?>

</ul>

</div>rrrrrrr

-->


<!--

 <div id="jb-container">




  <div id="jb-header">
   <table class="jb-hd-table">
    <tr>
     <td>학점</td>
     <td>이수학점</td>
     <td>잔여학점</td>
    </tr>
    <tr>
     <td><?= $avgScore ?>/ 4.5</td>
     <td><?= $totalCredit ?>/ 128</td>
     <td><?= 128-$totalCredit ?></td>
    </tr>
   </table>
  </div>


-->