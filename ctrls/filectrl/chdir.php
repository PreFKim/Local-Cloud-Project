<?php
    session_start();
    include "home.php";
    include $home."path.php";
    include $commonphp."checklogin.php";
    
    if( isset($_POST['dir'])) {
        $id = $_SESSION['id'];
        $dir = $_POST['dir'];

        if (is_dir($clouddir.$id."/".$dir)==true || (strrpos($dir,"earch:")==1 && is_dir($clouddir.$id."/".explode(':',$dir)[1])==true)) //해당 폴더가 존재하거나 검색모드일때 검색 루트 폴더가 존재하는 경우
        {
            $_SESSION['dir'] = $dir;
        }
        else
        {
            $_SESSION['msg'] = "해당 경로가 존재하지 않습니다.";
        }
    }
    else
    {
        $_SESSION['msg'] = "비정상적인 접근입니다.";
    }
    header("Location: ".$home."cloud.php");
?>
