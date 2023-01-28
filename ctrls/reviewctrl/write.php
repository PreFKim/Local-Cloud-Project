<?php
session_start();
include "home.php";
include $home."path.php";
include $commonphp."checklogin.php";

if (isset($_GET['bid'])==false || $_POST['contents']==false)
{
    $_SESSION['msg'] = "비정상적인 접근입니다.";
    header("location: ".$home."board.php");
    exit;
}

$uid = $_SESSION['uid'];
$bid = $_GET['bid'];
$contents = $_POST['contents']; 

//게시글 존재 여부
$sql = "SELECT * FROM board WHERE bid=$bid";
if (mysqli_fetch_array(mysqli_query($conn, $sql))==NULL)
{
    $_SESSION['msg'] = "해당 게시글이 존재하지 않습니다.";
    header("location: ".$home."board.php");
    exit;
}

if (isset($_POST['target'])) //target이 주어지면 답글이라는 것을 알려줌
{
    //답글을 다는 경우
    $sql = "SELECT * FROM review WHERE rid=".$_POST['target'];
    if (mysqli_fetch_array(mysqli_query($conn, $sql))==NULL)
    {
        $_SESSION['msg'] = "해당 댓글이 존재하지 않습니다.";
        header("location: ".$home."form_post.php?bid=$bid");
        exit;
    }
    $target = ",target";
    $tid = ",".$_POST['target'];
}
else 
{
    $target = "";
    $tid = "";
}

//댓글 또는 답글 작성
$sql = "INSERT INTO review (rid,bid, uid, contents".$target.") VALUES (NULL,".$bid.", ".$uid.",'".$contents."'".$tid.")";
$result = mysqli_query($conn, $sql);
        
$_SESSION['msg'] = "댓글 작성이 완료 되었습니다.";
header("location: ".$home."form_post.php?bid=$bid");
?>