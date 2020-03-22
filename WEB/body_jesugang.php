<?php
include_once('./dbConnect.php');

//include_once('./head.php');

#$studentNum = "201321093";
$kakaoid = $_GET['id'];
#학번 query
$stnum_sql = "SELECT st_studentnum as num FROM student WHERE kakao_id = '{$kakaoid}'";
$studentNumRes = mysqli_query($conn, $stnum_sql);
$studentNumRow = mysqli_fetch_array($studentNumRes);
$studentNum   = $studentNumRow['num'];

$studentNum  = '201321093';

//if(is_null($studentNum)){

        //more code
        //header("Location:URL");

        //수정!!!
        //header("Location: http://www.mediachatbotproj.tk:8080/404_error.html");
        //exit();

//}else{

$subject = $_POST['subject'];

$subject_sql = "SELECT * FROM subject WHERE st_studentnum = '{$studentNum}'";

if($subject != '') {
        $subject_sql .= " AND sub_subject LIKE '%{$subject}%' ";
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

<script>

        function scoreValue(sub_subject, sub_score) {
                var totalScore = <?= $totalScore ?>;
                var totalCredit = <?= $totalCredit ?>;
                var innerHTML = "<table>";
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



 
</style>
        
  <div id="jb-content">
  <div id="jb-content2">
        <form method="POST" action="jesugang.php">
                <input type="text" id="search" placeholder="과목을 검색하세요"  style="width:300px;height:50px;" name="subject"/>

		 <button type="submit"style="width:40px;height:40px;" class="btn btn-default">
                                        <img src="https://d30y9cdsu7xlg0.cloudfront.net/png/197118-84.png" width="15px;"/></button>

        </form>
  </div>

<div id="jb-sidebar">
<div id="scoreValue">
</div>
</div>
<br/>
        <table class="type09">
	<thead>
	<tr>
	<th scope="cols">수강한 과목</th>
	<th scope="cols"></th>
	<th scope="cols">성적</th>
	</tr>
	</thead>
	<tbody>
	<tr>
	<td></td>
	</td></td>
	<td></td>
	</tr>

                <?php

                while($row = mysqli_fetch_array($subject_res)) { ?>
                <tr>
                        <th scope="row"><a href="javascript:scoreValue('<?= $row[sub_subject] ?>', <?= $row[sub_score] ?>);"><?= $row['sub_subject'] ?></a></td>
                        <td></td>
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


<br/>

</div>

