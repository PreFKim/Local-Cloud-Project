<?php
session_start();
include "home.php";
include $home."path.php";
include $commonphp."checklogin.php";

if (isset($_POST['contents']) == false || isset($_POST['title']) == false)
{
    $_SESSION['msg'] ="비정상적인 접근입니다.";
    header("location: ".$home."form_write.php");
    exit;
}

$title =$_POST['title'];
$contents =$_POST['contents'];



$sql = "INSERT INTO board (bid, uid, title, contents) VALUES (NULL, ".$_SESSION['uid'].", '$title','$contents')"; //글 작성
$result = mysqli_query($conn, $sql);
$bid = mysqli_fetch_array(mysqli_query($conn,"SELECT LAST_INSERT_ID() AS bid"))['bid']; //최근에 작성한 글의 auto_increment값 가져옴(bid값)

if ($_FILES['files']['error'][0]!=UPLOAD_ERR_NO_FILE) //첨부 파일이 있는지부터 확인 없으면 파일 첨부 x
{
  $name = $_FILES['files']['name']; 


  for($i=0; $i<count($name); $i++)
  {
    $error = $_FILES['files']['error'][$i];
    if( $error != UPLOAD_ERR_OK ) 
    {
      switch( $error ) 
      {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
          $_SESSION['msg'] = "파일이 너무 큽니다. ($error)";
        case UPLOAD_ERR_NO_FILE:
          $_SESSION['msg'] = "파일이 첨부되지 않았습니다. ($error)";
        default:
          $_SESSION['msg'] = "파일이 제대로 업로드 되지 않았습니다. ($error)";
      }
      header("Location: ".$home."board.php");
      $sql = "DELETE FROM board WHERE bid=$bid"; //파일이 제대로 첨부되지 않았으면 글 삭제
      mysqli_query($conn,$sql);
      exit;
    }
    if (file_exists($uploaddir.$bid."/")==false) 
      mkdir($uploaddir.$bid."/",0777,true);
    $sql = "INSERT INTO files (fid,bid,filename) VALUES (NULL, '$bid', '".$name[$i]."')";
    $result = mysqli_query($conn, $sql);
    move_uploaded_file($_FILES['files']['tmp_name'][$i], $uploaddir.$bid."/".$name[$i]); // 디렉토리에 저장하기
  }
}


$_SESSION['msg'] = "글 작성이 완료되었습니다.";
header("Location: ".$home."form_post.php?bid=$bid");
?>