<?php
if (isset($_SESSION['uid']))
{
	$conn = mysqli_connect('localhost','root','','pref');
	$sql = "SELECT * FROM user WHERE uid=".$_SESSION['uid'];
	$user = mysqli_fetch_array(mysqli_query($conn, $sql));
	if ($user == NULL) 
	{
		$_SESSION['msg'] = "회원님의 회원정보가 없어 로그아웃 하였습니다.";
		header("location: ".$home.$memberctrl."logout.php");
		exit;
	}
}
else 
{
	$_SESSION['msg'] = "로그인이 필요한 서비스 입니다.";
	header("Location: ".$home."form_login.php");
	exit;
}
?>