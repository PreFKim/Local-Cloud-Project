<?php
session_start();
$home="./";
include $home."path.php";
include $commonphp."checklogin.php";
$root = $clouddir.$_SESSION['id'].'/';
date_default_timezone_set('Asia/Seoul');

if (isset($_POST['dir'])==false)
{
    $_SESSION['msg'] = '비정상적인 접근입니다.';
    header("Location: cloud.php");
    exit;
}
$dir = $_POST['dir'];
//파일의 종류에 맞는 명령어와 종류 설정
if (is_dir($root.$dir))
{
    $filename = explode('/',$dir)[count(explode('/',$dir))-2];
    $kind = "folder";
    $command ="changedir";
}
else if (is_file($root.$dir))
{
    
    $filename = basename($dir);
    $kind = "file";
    $command ="downloadfile";
}
else
{
    $_SESSION['msg'] = '해당 파일이 존재하지 않습니다.';
    header("Location: cloud.php");
    exit;
}

//재귀함수로 파일크기 구하기 (폴더의 경우 하위 폴더나 파일들의 파일 크기를 측정해야함)
function get_filesize($dir)
{
	global $root;
	$size = 0;
	if (is_dir($root.$dir)){                              
		if ($dh = opendir($root.$dir)){                     
			while (($file = readdir($dh))){ 
				if ($file =="." || $file =="..") continue; 
				if (is_dir($root.$dir.$file)) $size += get_filesize($dir.$file.'/');
				else if (is_file($root.$dir.$file)) $size += filesize($root.$dir.$file);
			}                                    
			closedir($dh);     
		}                                             
	}
	else if(is_file($root.$dir)) return filesize($root.$dir);
	return $size;

}

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">


        <link rel="stylesheet" href="css/mystyle.css">
		<link rel="stylesheet" href="css/bootstrap.css">
        <style>
            .dark{
                background-color:#343a40;
                border-style: none;
                width :100%;
            }
        </style>

        <title>파일정보</title>
        <script>
            <?php include $commonphp."filescript.php";?>
        </script>

    </head>

    <body>
        <?php include $commonphp."header.php";?>
		<br>
        <div class="container">
            <div class="row">
                <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                    <div class="card card-signin my-5">
                        <div class="card-body">
                            <h2 class="card-title text-center">파일정보</h2>

                            
                            <?php
                                echo "
                                <table class='table'>
                                
                                    <tr>
                                        <th>종류</th>
                                        <td><img src='img/".$kind.".png'></td>
                                    </tr>
                                    <tr>
                                        <th>파일명</th>
                                        <td class='long-text'><a href='#' onclick=\"javascript:$command('$dir');\">".$filename."</a></td>
                                    </tr>
                                    <tr>
                                        <th>저장경로</th>
                                        <td class='long-text'><a href='#' onclick=\"javascript:changedir('".dirname($dir).'/'."');\">".dirname($dir).'/'."</a></td>
                                    </tr>
                                    <tr>
                                        <th>용량</th>
                                        <td class='long-text'>".ceil(get_filesize($dir)/1024)."KB</td>
                                    </tr>
                                    <tr>
                                        <th>수정일자</th>
                                        <td>". date("Y-m-d H:i:s", filemtime($root.$dir))."</td>
                                    </tr>    
                                </table>
                                <form>
                                    <table class='table'>
                                        <thead>
                                            <tr>
                                            
                                                <th>
                                                    <input type='text' id='newname' name='newname' class='form-control' placeholder='새 이름' value='$filename' required>                                                
                                                </th>
                                                <th>
                                                    <input type='button' class='btn btn-md btn-primary btn-block dark' onclick='javascript:renamefile(\"".dirname($dir)."/"."\",\"$filename\",newname.value);' value='이름수정'>
                                                </th>
                                            
                                            </tr>
                                        </thead>
                                    </table>
                                </form>
                                <hr>
                                <button class='btn btn-md btn-primary btn-block text-uppercase dark' onclick='javascript:deletefile(\"$dir\");'>파일삭제</button>
                                ";
                            ?>
                            <hr>
                            <a href="cloud.php">Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    

    </body>
</html>