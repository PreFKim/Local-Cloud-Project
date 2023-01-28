<?php
session_start();
$home="./";
include $home."path.php";
include $commonphp."checklogin.php";

$dir = $_SESSION["dir"];
$id = $_SESSION["id"];
$nickname = $user["nickname"];
$root = $clouddir.$id.'/'; // 클라우드 폴더의 root폴더

$dirs = array();
$files = array();
$list = array();
$is_search = "";

//검색 모드일 경우 검색기능을 수행하는 재귀함수 (해당 폴더 내에 모든 폴더와 파일 검색하도록)
function search($dir,$filename)
{
	global $dirs;
	global $files;
	global $root;
	if (is_dir($root.$dir)){                              
		if ($dh = opendir($root.$dir)){                     
			while (($file = readdir($dh))){ 
				if ($file =="." || $file =="..") continue; 
				if (is_dir($root.$dir.$file)) 
				{
					if (strtolower($file)==strtolower($filename)) {
						$dirs[]=$dir.$file.'/';
					}
					search($dir.$file.'/',$filename);
				}
				else if(is_file($root.$dir.$file)) 
				{
					if(strtolower($file)==strtolower($filename)) {
						$files[]=$dir.$file;
					}
				}
			}                                    
			closedir($dh);     
		}                                             
	} 
}

//파일 사이즈 구하는 재귀 함수 (폴더일 경우 하위 폴더와 파일에 속한 사이즈를 모두 구해야 하기 때문)
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

if (strrpos($dir,"earch:")==1) //검색모드인 경우 search:로 시작하기에 이런식으로 검색함
{
	search(explode(':',$dir)[1],explode(':',$dir)[2]);
	$is_search = "true";
}
else
{
	if (is_dir($root.$dir)){                              
		if ($dh = opendir($root.$dir)){                     
			while (($file = readdir($dh))){ 
				if ($file =="." || $file =="..") continue; 
				if (is_dir($root.$dir.$file)) $dirs[]=$dir.$file.'/';
				else if(is_file($root.$dir.$file)) $files[]=$dir.$file;
			}   									
			closedir($dh);     
		}                                             
	}
}

for ($i=0;$i<count($dirs);$i++) $list[] = $dirs[$i];
for ($i=0;$i<count($files);$i++) $list[] = $files[$i];


?>

<!doctype html>
<html>
  	<head>
		<script>
			<?php include $commonphp."filescript.php";?>
		</script>

		<title>Cloud</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,700' rel='stylesheet' type='text/css'>

		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="css/bootstrap.css">
		<link rel="stylesheet" href="css/mystyle.css">

		<script src="js/jquery.min.js"></script>
		<script src="js/popper.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/main.js"></script>

	</head>
	<body onload="javascript:startsetting('<?=$is_search?>',2);">   
	
		<?php include $commonphp."header.php";?>
		<br>
		<section>
			<div class="container">
				
				<div class="row">
					<div class="col-md-12">
						<table class="table">
							<thead>
								<tr>
									<td>
										<div id ="createdir"style="float: left;">
											<input type="text" id ="dirname" >
											<input type="button" value="새 폴더" onclick="javascript:createdir(dirname.value);" >
										</div>
									</td>
									<td>
										<div class="text-center">
											<?php
												global $is_search;
												if ($is_search=="true")
												{
													
													echo "검색 위치:<a href=\"#\" onclick=\"javascript:changedir('".explode(':',$dir)[1]."');\">".explode(':',$dir)[1]."</a><br> ";
													echo "검색 내용:".explode(':',$dir)[2];
												}
												else
												{
													echo "경로: ";
													$sepdir = explode('/',$dir);
													$tmp = "";
													for ($i=0;$i<count($sepdir)-1;$i++)
													{
														$tmp = $tmp.$sepdir[$i].'/';
														echo "<a href=\"#\" onclick=\"javascript:changedir('$tmp');\">$sepdir[$i]/</a> ";
													}
												}
											?>
											<br>
											파일 수:<?=count($list)?>
										</div>
									</td>
									<td>
										<div id="search" style="float: right;">
											<input type="text" id="filename">
											<input type="button" value="검색" onclick="javascript:searchfile('<?=$dir?>',filename.value)">
										</div>
									</td>
								</tr>
							</thead>
						</table>
						<table class="table table-hover" id="filelist">
							<thead class="thead-dark">
								<tr>
									<th style="width:5%;">종류</th>
									<th style="width:35%;">경로</th>
									<th style="width:30%;">파일명</th>
									<th style="width:10%;">파일크기</th>
									<th style="width:10%;">수정날짜</th>
									<th style="width:10%;" class="text-center"><a href="cloud.php"><button class="post-cata" >새로고침</button></a></th>
								</tr>
							</thead> 
							<tbody>
								<?php			        
									for ($i=0;$i<count($list);$i++) { 
										if ($i<count($dirs)) {
											$filename = basename($list[$i]);
											$command ="changedir";
										}
										else {
											$filename = explode('/',$list[$i])[count(explode('/',$list[$i]))-1];
											$command ="downloadfile";
										}
										?>
										
										<tr class="alert" role="alert" id="<?=$i+1?>">
											<td><img src="img/<?php if ($i<count($dirs)) echo "folder"; else echo "file";?>.png"></td>
											<td class="long-text"><a href="#" onclick="javascript:changedir('<?=dirname($list[$i]).'/'?>');"><?=dirname($list[$i]).'/'?></a></td>	
											<?php echo "<td class='long-text'><a href=\"#\" onclick=\"javascript:$command('$list[$i]');\">".$filename."</a></td>";?>
											<td class="long-text"><?=ceil(get_filesize($list[$i])/1024)?>KB</td>
											<td><?=date("Y-m-d", filemtime($root.$list[$i]))?></td>
											<td>
												
												<div>
													<button class="post-cata" onclick="javascript:php_func('form_file.php',{dir:'<?=$list[$i]?>'});">수정</button>
													<button class="post-cata" onclick="javascript:deletefile('<?=$list[$i]?>');">삭제</button>
												</div>
												
											</td>
										</tr>
								<?php
									}
								?>
								<tr class="alert" role="alert" id="createfile">
									<td><img src="img/upload.png"></td>
									<td class="long-text"><a href="#" onclick="javascript:changedir('<?=$dir?>');"><?=$dir?></a></td>	
									<td>
										<form action="<?=$filectrl?>upload.php" enctype="multipart/form-data"  method="post" id="upload">
											<input type="hidden" name="dir" value="<?=$dir?>">
											<input type="file" name="newfile[]" id="newfile" multiple>
											<input type="button" value="업로드" onclick="javascript:uploadfile();">
										</form>

									</td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</section>

	</body>
</html>
