<?php
  session_start();
  include "home.php";
  include $home."path.php";
  include $commonphp."checklogin.php";


  $id = $_SESSION['id'];
  $name = $_FILES['newfile']['name'];
  $root = $clouddir.$id."/";
  
  if (isset($_POST['dir']))
  {
    $dir = $_POST['dir'];
  }
  else
  {
    $_SESSION['msg'] = "비정상적인 접근입니다.";
    header("location: ".$home."cloud.php");
    exit;
  }

  
  for($i=0; $i<count($name); $i++)
  {
    $error = $_FILES['newfile']['error'][$i];
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
          $_SESSION['msg'] = "파일 제대로 업로드 되지 않았습니다. ($error)";
      }
      header("Location: ".$home."cloud.php");
      exit;
    }
    move_uploaded_file($_FILES['newfile']['tmp_name'][$i], "$root$dir$name[$i]"); // 디렉토리에 저장하기
    echo "$name[$i] 파일 업로드 완료<br>";
  }

  
  header("Location: ".$home."cloud.php");
  
?>