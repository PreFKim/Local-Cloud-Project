<?php
session_start();
include "home.php";
include $home."path.php";
include $commonphp."checklogin.php";


if (isset($_POST['bid'])==false || isset($_POST['rid'])==false ||isset($_POST['contents'])==false)
{
    $_SESSION['msg'] = "비정상적인 접근입니다.";
    header("Location: ".$home."form_post.php?bid=".$_POST['bid']);
    exit;
}


$bid = $_POST['bid'];
$rid = $_POST['rid'];
$contents = $_POST['contents'];

//댓글 존재 여부
$sql = "SELECT * FROM review WHERE rid=$rid";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
if ($row==NULL)
{
    $_SESSION['msg'] = "존재하지 않는 댓글입니다.";
    header("Location: ".$home."form_post.php?bid=".$_POST['bid']);
    exit;
}

if ($row['uid'] != $_SESSION['uid'] && $user['auth']==0 ) 
{
    $_SESSION['msg'] = "수정 권한이 없습니다.";
    header("Location: ".$home."board.php");
    exit;
}

//댓글 수정
$sql = "UPDATE review SET contents='$contents' WHERE rid=$rid";
$result = mysqli_query($conn, $sql);
$_SESSION['msg'] = "댓글이 수정 되었습니다.";
header("Location: ".$home."form_post.php?bid=$bid");
?>