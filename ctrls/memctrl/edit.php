<?php
session_start();
include "home.php";
include $home."path.php";
include $commonphp."checklogin.php";

//수정할 회원 정보값 받기
if (isset($_GET['uid'])==false ||isset($_POST['pw'])==false|| isset($_POST['oldpw']) == false || isset($_POST['pwchk'])==false || isset($_POST['nickname'])==false|| isset($_POST['birth'])==false)
{
    $_SESSION['msg'] = "비정상적인 접근입니다.";
    header("Location: ".$home."board.php");
    exit;
}

if (isset($_POST['auth'])) $auth = $_POST['auth'];
else $auth = 0;


$uid = $_GET['uid'];
$pw = $_POST['pw'];
$oldpw = $_POST['oldpw'];
$nickname = $_POST['nickname'];
$birth = $_POST['birth'];

//수정하고자 하는 회원의 정보 확인
$sql = "SELECT * FROM user WHERE uid=$uid";
$result = mysqli_query($conn, $sql);
    
if (mysqli_fetch_array($result)==NULL)
{
    $_SESSION['msg'] = "해당 회원의 정보가 존재하지 않습니다.";
    header("Location: logout.php");
    exit;
}

//해당 회원의 uid와 현재 uid가 다르고 권한이 일반회원 권한인경우
if ($uid != $_SESSION['uid'] && $user['auth']==0 ) 
{
    $_SESSION['msg'] = "수정 권한이 없습니다.";
    header("Location: ".$home."board.php");
    exit;
}

if ($pw == "")
{
    $pw = $user['PW'];
}
else
{
    if ($oldpw != $user['PW'])
    {
        $_SESSION['msg'] = "이전 비밀번호와 다릅니다.";
        header("Location: ".$home."form_member.php?uid=$uid");
        exit;
    }

    if ($pw != $_POST['pwchk'])
    {
        $_SESSION['msg'] = "새 비밀번호를 확인해주세요.";
        header("Location: ".$home."form_member.php?uid=$uid");
        exit;
    }

    if ($oldpw == $pw)
    {
        $_SESSION['msg']="기존 비밀번호와 동일한 비밀번호 입니다.";
        header("Location: ".$home."form_member.php?uid=$uid");
        exit;
    }

    if (strlen($pw)<8)
    {
        $_SESSION['msg']="비밀번호는 8자리 이상으로 지정해주세요.";
        header("Location: ".$home."form_member.php?uid=$uid");
        exit;
    }
}

$sql = "UPDATE user SET PW='$pw',nickname='$nickname',birth='$birth',auth='$auth' WHERE uid='$uid'";
$result = mysqli_query($conn, $sql);
$_SESSION['msg'] = "회원정보가 수정 되었습니다.";
header("Location: ".$home."board.php");
?>