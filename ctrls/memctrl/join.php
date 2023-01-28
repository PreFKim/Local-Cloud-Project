<?php
session_start();
include "home.php";
include $home."path.php";

$joinpage = $home."form_join.php";
$loginpage = $home."form_login.php";

if (isset($_POST['id'])==false||isset($_POST['pw']) ==false||isset($_POST['pwchk']) ==false||isset($_POST['nickname']) ==false||isset($_POST['birth']) ==false) 
{
    $_SESSION['msg']="비정상적인 접근입니다.";
    header("Location: $joinpage");
    exit;
}

$id = $_POST['id'];
$pw = $_POST['pw'];
$pwchk = $_POST['pwchk'];
$nickname = $_POST['nickname'];
$birth = $_POST['birth'];


$conn = mysqli_connect('localhost','root','','pref');
$sql = "SELECT * FROM user where ID='$id'";
$result = mysqli_query($conn, $sql);    
$row= mysqli_fetch_array($result);
if ($row != NULL)  //회원 아이디 중복검사
{
    $_SESSION['msg']="해당 아이디가 이미 존재합니다.";
    header("Location: $joinpage");
    exit;
}

if ($pw != $pwchk)
{
    $_SESSION['msg']="비밀번호가 다릅니다.";
    header("Location: $joinpage");
    exit;
}

if (strlen($id)<4)
{
    $_SESSION['msg']="아이디는 4자리 이상으로 지정해주세요.";
    header("Location: $joinpage");
    exit;
}
if (strlen($pw)<8)
{
    $_SESSION['msg']="비밀번호는 8자리 이상으로 지정해주세요.";
    header("Location: $joinpage");
    exit;
}
if ($birth>date("Y-m-d"))
{
    $_SESSION['msg']="생년월일을 정확히 입력해주세요";
    header("Location: $joinpage");
    exit;
}
$sql = "INSERT INTO user (uid, ID, PW, nickname, birth, auth) VALUES (NULL, '$id', '$pw', '$nickname', '$birth', 0)";
$result = mysqli_query($conn, $sql);
        
$_SESSION['msg'] = "회원가입이 완료됐습니다.";
header("Location: $loginpage");
?>