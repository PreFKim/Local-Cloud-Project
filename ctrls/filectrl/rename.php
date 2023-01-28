<?php
  session_start();
  include "home.php";
  include $home."path.php";
  include $commonphp."checklogin.php";

  $id = $_SESSION['id'];
  if (isset($_POST['dir']) && isset($_POST['old']) && isset($_POST['new']) ) 
  {
    $dir = $_POST['dir'];
    $root = $clouddir.$id."/";
    $old = $root.$dir.$_POST['old'];
    $new = $root.$dir.$_POST['new'];
  }
  else
  {
    $_SESSION['msg'] = "비정상적인 접근입니다.";
    header("Location: ".$home."cloud.php");
    exit;
  }



  if( file_exists($new) ) 
  { 
    $_SESSION['msg'] = "해당 이름의 파일 또는 폴더가 이미 존재합니다.";
  }
  else
  {
    if (file_exists($old))
    {
      rename($old,$new);
    }
    else
    {
      $_SESSION['msg'] = "이름을 변경할 파일이나 폴더가 존재하지 않습니다.";
    }
  }

  header("Location: ".$home."cloud.php");
?>