<?php
session_start();
include "home.php";
include $home."path.php";
include $commonphp."checklogin.php";


if (isset($_POST['uid'])==false ||isset($_POST['bid'])==false || isset($_POST['title'])==false|| isset($_POST['contents'])==false)
{
    $_SESSION['msg'] = "비정상적인 접근입니다.";
    header("Location: ".$home."board.php");
    exit;
}



$uid = $_POST['uid'];
$bid = $_POST['bid'];
$title = $_POST['title'];
$contents = $_POST['contents'];

$sql = "SELECT * FROM board WHERE bid=$bid";
$result = mysqli_query($conn, $sql);
    
if (mysqli_fetch_array($result)==NULL)
{
    $_SESSION['msg'] = "해당 게시글이 존재하지 않습니다.";
    header("Location: logout.php");
    exit;
}

if ($uid != $_SESSION['uid'] && $user['auth']==0 ) 
{
    $_SESSION['msg'] = "수정 권한이 없습니다.";
    header("Location: ".$home."board.php");
    exit;
}

$sql = "UPDATE board SET title='$title',contents='$contents' WHERE bid=$bid";
$result = mysqli_query($conn, $sql);
$_SESSION['msg'] = "게시글이 수정 되었습니다.";
header("Location: ".$home."form_post.php?bid=$bid");
?>