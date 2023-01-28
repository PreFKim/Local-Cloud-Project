<?php
  session_start();
  include "home.php";
  include $home."path.php";
  include $commonphp."checklogin.php";

  if (isset($_POST['dir'])) // POST로 dir값이 전달되는 경우에는 클라우드에 있는 파일 다운로드
  {
    $id = $_SESSION['id'];
    $root = $clouddir.$id.'/';
    $target_Dir = $root.$_POST['dir'];
    $loc = "cloud.php";
  }
  else if(isset($_POST['bid']) && isset($_POST['filename'])) //POST로 bid값과 filename 값이 전달되는 경우 첨부파일 다운로드
  {
    $target_Dir = $uploaddir.$_POST['bid']."/".$_POST['filename'];
    $loc = "form_post.php?bid=".$_POST['bid'];
  }
  else
  {
    $_SESSION['msg'] = '비정상적인 접근입니다.';
    header("Location: ".$home."board.php");
    exit;
  }
  $file = basename($target_Dir);


  if(file_exists($target_Dir)){
    echo "다운로드 중입니다.";
    header("Content-Type:application/octet-stream");
    header("Content-Disposition:attachment;filename=".$file);
    header("Content-Transfer-Encoding:binary");
    header("Content-Length:".filesize($target_Dir));
    header("Cache-Control:cache,must-revalidate");
    header("Pragma:no-cache");
    header("Expires:0");
    if(is_file($target_Dir)){
        $fp = fopen($target_Dir,"r");
        while(!feof($fp)){
          $buf = fread($fp,8096);
          $read = strlen($buf);
          print($buf);
          flush();
        }
        fclose($fp);
    }
  } 
  else{
    $_SESSION['msg'] = "해당 파일이 존재하지 않습니다.";
  }
  header("Location: ".$home.$loc);
?>