<?php
  session_start();
  include "home.php";
  include $home."path.php";
  include $commonphp."checklogin.php";

  $id = $_SESSION['id'];
  if (isset($_POST['dir'])) 
  {
    $dir = $_POST['dir'];
  }
  else
  {
    $_SESSION['msg'] = "비정상적인 접근입니다.";
    header("Location: ".$home."cloud.php");
    exit;
  }
  $root = $clouddir.$id."/";

  function del_dir($dir) //디렉토리 삭제 재귀함수
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

  echo "삭제 시작<br>";
  if (is_file($root.$dir)) 
  {
    if (unlink($root.$dir)) echo "삭제 성공";
    else $_SESSION['msg'] = "파일 삭제를 실패하였습니다.";
  }
  else if (is_dir($root.$dir)) 
  {
    if (del_dir($root.$dir)) {}
    else $_SESSION['msg'] = "폴더 삭제를 실패하였습니다. 폴더 내 일부 파일은 삭제 되었을 수 있습니다.";
  }
  else
  {
    $_SESSION['msg'] = "해당 이름의 파일 또는 폴더가 이미 존재하지 않습니다.";
  }

header("Location: ".$home."cloud.php");
?>