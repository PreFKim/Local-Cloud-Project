<?php
  session_start();
  include "home.php";
  include $home."path.php";
  include $commonphp."checklogin.php";

  if (isset($_POST['dir'])) 
  {
    $id = $_SESSION['id'];
    $dir = $_POST['dir'];
    $root = $clouddir.$id."/";
  }
  else
  {
    $_SESSION['msg'] = "비정상적인 접근입니다.";
    header("Location: ".$home."cloud.php");
    exit;
  }

  if (file_exists($root.$dir)==true) $_SESSION['msg']="해당 이름의 파일 또는 폴더가 이미 존재합니다.";
  else mkdir($root.$dir ,0777,true);

  header("Location: ".$home."cloud.php");
?>