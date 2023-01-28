<?php
session_start();
include "home.php";
include $home."path.php";
include $commonphp."checklogin.php";

if (isset($_GET['bid'])==false || isset($_GET['rid'])==false)
{
    $_SESSION['msg'] = "비정상적인 접근입니다.";
    header("Location: ".$home."board.php");
    exit;
}

$bid = $_GET['bid'];
$rid = $_GET['rid'];

//해당 댓글 정보 조회
$sql = "SELECT * FROM review WHERE rid=$rid";
$row = mysqli_fetch_array(mysqli_query($conn, $sql));

if ($row==NULL)
{
    $_SESSION['msg'] = "댓글이 존재하지 않습니다.";
    header("Location: ".$home."form_post.php?bid=$bid");
    exit;
}
$uid = $row['uid'];

if ($uid != $_SESSION['uid'] && $user['auth']==0 ) 
{
    $_SESSION['msg'] = "삭제 권한이 없습니다.";
    header("Location: ".$home."form_post.php?bid=$bid");
    exit;
}

function delete_child_review($rid) //하위 댓글들 모두 삭제
{
    global $conn;
    $sql = "DELETE FROM review WHERE rid=$rid";
    $result = mysqli_query($conn, $sql);

    $sql = "SELECT rid FROM review WHERE target=$rid"; //해당 rid의 하위 댓글이 존재한다면 해당 하위 댓글도 모두 삭제
    while($row = mysqli_fetch_array(mysqli_query($conn, $sql)))
    {
        delete_child_review($row['rid']);
    }
}
delete_child_review($rid);

$_SESSION['msg'] = "해당 댓글과 하위 댓글들이 모두 삭제 되었습니다.";
header("Location: ".$home."form_post.php?bid=$bid");
?>