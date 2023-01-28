<?php
session_start();
include "home.php";
include $home."path.php";
include $commonphp."checklogin.php";

if (isset($_GET['uid'])==false)
{
    $_SESSION['msg'] = "비정상적인 접근입니다.";
    header("Location: ".$home."board.php");
    exit;
}

if ($_GET['uid'] != $_SESSION['uid'] && $user['auth']==0 ) 
{
    $_SESSION['msg'] = "권한이 없습니다.";
    header("Location: ".$home."board.php");
    exit;
}

$uid = $_GET['uid'];

$sql = "SELECT * FROM user WHERE uid=$uid"; //해당 회원의 정보를 탐색
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);    
if ($row==NULL)
{
    $_SESSION['msg'] = "해당 회원의 정보가 존재하지 않습니다.";
    header("Location: logout.php");
    exit;
}
$id = $row['ID'];


function del_dir($dir)
{
    if (is_dir($dir)){                              
        if ($dh = opendir($dir)){                     
        while (($file = readdir($dh))){ 
            if ($file =="." || $file =="..") continue; 
            if (is_dir($dir.$file)) del_dir($dir.$file.'/');
            else if(is_file($dir.$file)) 
            {
            unlink($dir.$file);
            }
        }                                    
        closedir($dh);     
        $ret = rmdir($dir);
        }                                             
    } 
    return $ret;
}


$sql = "SELECT bid FROM board WHERE uid=$uid"; //해당 회원의 모든 글조회
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_array($result)) //해당 회원이 작성한 bid에 대한 업로드 폴더 삭제
{
    del_dir($uploaddir.$row['bid'].'/');  // 업로드 된 파일 삭제
}
del_dir($clouddir.$id."/"); // 클라우드 폴더 삭제

function delete_child_review($rid) //해당 회원이 작성한 댓글과 하위 댓글(답글)들도 모두 삭제
{
    global $conn;
    $sql = "DELETE FROM review WHERE rid=$rid";

    $result = mysqli_query($conn, $sql);
    $sql = "SELECT rid FROM review WHERE target=$rid";

    while($row = mysqli_fetch_array(mysqli_query($conn, $sql)))
    {
        delete_child_review($row['rid']);
    }
}


$sql = "SELECT * FROM review WHERE uid=$uid"; //댓글 조회

while($result = mysqli_fetch_array(mysqli_query($conn,$sql))) //해당 회원의 모든 댓글 삭제
{
    delete_child_review($result['rid']);
}

//최종적인 회원 탈퇴
$sql = "DELETE FROM user WHERE uid=$uid";
$result = mysqli_query($conn, $sql);

if ($uid == $_SESSION['uid']) 
{
    $_SESSION['msg'] = "회원 탈퇴가 완료됐습니다.";
    header("Location: logout.php");
}
else 
{
    $_SESSION['msg'] = "해당 회원의 탈퇴가 완료됐습니다.";
    header("Location: ".$home."board.php");
}


?>