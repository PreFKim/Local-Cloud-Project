<?php
session_start(); 
if (isset($_SESSION['msg'])==false) $tmp = "로그아웃 되었습니다.";
else $tmp = $_SESSION['msg'];
session_unset();
include "home.php";
$_SESSION['msg'] = $tmp;
header("location: ".$home."/form_login.php");
?>