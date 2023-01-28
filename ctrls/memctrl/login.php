<?php
session_start();
include "home.php";
include $home."path.php";

if (isset($_POST['id']) ==false || isset($_POST['pw']) ==false)
{
    $_SESSION['msg'] = "비정상적인 접근입니다.";
    header("Location: ".$home."form_login.php");
    exit;
}
$id = $_POST['id'];
$pw = $_POST['pw'];

$conn = mysqli_connect('localhost','root','','pref');
$sql = "SELECT * FROM user where ID='$id'";
$result = mysqli_query($conn, $sql);
      
$row= mysqli_fetch_array($result) ;
if ($row == NULL) {
    $_SESSION['msg'] = "ID가 존재하지 않습니다.";
}
else {
    if($row['ID'] == $id && $row['PW'] == $pw) {
        //클라우드 폴더가 없다면 생성
        if (file_exists($clouddir.$id.'/root/')==false) 
            mkdir($clouddir.$id.'/root/',0777,true);
        //SESSION값에 필요한 값만 주기
        $_SESSION['uid'] =$row['uid'];
        $_SESSION['id'] =$id;
        $_SESSION['dir'] = 'root/';
        $_SESSION['msg'] = $row['nickname']."(".$id.")회원님 환영합니다.";
        header("Location: ".$home."board.php");
        exit;
    }
    else{
        $_SESSION['msg'] = "ID 또는 PW가 잘못됐습니다.";
    }
}
header("Location: ".$home."form_login.php");
?>