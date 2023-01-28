<?php
session_start();
include "home.php";
include $home."path.php";
include $commonphp."checklogin.php";

if (isset($_GET['bid'])==false)
{
    $_SESSION['msg'] = "비정상적인 접근입니다.";
    header("Location: ".$home."board.php");
    exit;
}

$bid = $_GET['bid'];

$sql = "SELECT * FROM board WHERE bid=$bid";
$row = mysqli_fetch_array(mysqli_query($conn, $sql));

if ($row==NULL)
{
    $_SESSION['msg'] = "게시글이 존재하지 않습니다.";
    header("Location: ".$home."board.php");
    exit;
}
$uid = $row['uid'];

if ($uid != $_SESSION['uid'] && $user['auth']==0 ) 
{
    $_SESSION['msg'] = "삭제 권한이 없습니다.";
    header("Location: ".$home."board.php");
    exit;
}

$sql = "SELECT filename FROM files WHERE bid=$bid"; //업로드된 파일이 존재하는지 확인
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_array($result))
    unlink($uploaddir.$bid."/".$row['filename']); //업로드 된 파일 모두 삭제
rmdir($uploaddir.$bid."/"); //업로드 폴더 삭제


$sql = "DELETE FROM board WHERE uid=$uid AND bid=$bid";
$result = mysqli_query($conn, $sql);

$_SESSION['msg'] = "글이 삭제 되었습니다.";
header("Location: ".$home."board.php");
?>