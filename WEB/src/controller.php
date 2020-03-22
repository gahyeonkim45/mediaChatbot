<?php
include_once('./dbConnect.php');

session_start();

$mod = $_GET['mod'];

switch($mod) {
	case 'login':
		$id  = $_POST['id'];
		$pwd = $_POST['pwd'];
		
		$sql = "select * from student 
					where st_studentnum = '{$id}'
					  and st_password   = '{$pwd}' ";
		$res = mysqli_query($conn, $sql);

		if($res->num_rows == 0) {
			?>
				<script>
				alert("해당 회원이 존재하지 않습니다.");
				history.back(-1);
				</script>
			<?php
		} else {
			$row = mysqli_fetch_array($res);
			$_SESSION['member'] = $row;

			header("Location:../index_test.php");
		}

		break;

	case 'logout':
		session_destroy();

		header("Location:../login.php");
		break;
}
?>
