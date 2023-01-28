<?php
session_start();
$home="./"; 
// 메인 폼과의 상대 거리
include $home."path.php";
//웹페이지 구동에 필요한 파일 또는 폴더들의 경로가 저장되어 있는 파일 include
$conn = mysqli_connect('localhost','root','','pref');
if (isset($_SESSION['uid'])) // 로그인 확인 후 회원정보가 존재하는지 확인
{
	$sql = "SELECT * FROM user WHERE uid=".$_SESSION['uid'];
	$user = mysqli_fetch_array(mysqli_query($conn, $sql));
	if ($user == NULL) 
	{
		$_SESSION['msg'] = "회원님의 회원정보가 없어 로그아웃 하였습니다.";
		header("location: ".$memberctrl."logout.php");
	}
}
$list = array(); //게시글 목록
$uid = array(); //해당 게시글의 작성자 uid
$list_num_review = array(); //댓글 수
$list_exists_file = array(); //첨부파일 존재 여부
$list_view = array(); //조회수


$where = "";
$search ="";
$mode=0;
$same=0;

if (isset($_GET['search']) && isset($_GET['mode']) && isset($_GET['same'])) //해당 조건들이 참이라면 검색 모드로 쿼리문제 추가할 WHERE절 수정
{
	$search=$_GET['search'];
	$mode=$_GET['mode']; 
	$same = $_GET['same'];
	if ($same)
	{
		if ($mode==0) $where = " WHERE title='$search'";
		else if ($mode==1) $where = " WHERE contents='$search'";
		else if ($mode==2) $where = " WHERE title='$search' OR contents='$search'";
		else if ($mode==3) $where = " WHERE ID='$search'";
		else if ($mode==4) $where = " WHERE nickname='$search'";
	}
	else
	{
		if ($mode==0) $where = " WHERE title like '%$search%'";
		else if ($mode==1) $where = " WHERE contents like '%$search%'";
		else if ($mode==2) $where = " WHERE title like '%$search%' OR contents like '%$search%'";
		else if ($mode==3) $where = " WHERE ID like '%$search%'";
		else if ($mode==4) $where = " WHERE nickname like '%$search%'";
	}

}

//쿼리문 실행
$sql = "SELECT board.*,user.ID,user.nickname FROM board JOIN user ON board.uid = user.uid".$where." ORDER BY board.date DESC,board.bid DESC";
$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_array($result)) {
	//게시글 정보
	$tmp = array($row['bid'], $row['title'],$row['ID'].'('.$row['nickname'].')',$row['date'] );
	$uid[] = $row['uid'];
	$list[] = $tmp;

	//댓글 수
	$sql = "SELECT * FROM review WHERE bid=".$row['bid'];
	$list_num_review[] = mysqli_num_rows(mysqli_query($conn,$sql));

	//해당 게시글을 조회한 사람의 수
	$sql = "SELECT * FROM view WHERE bid=".$row['bid'];
	$list_view[] = mysqli_num_rows(mysqli_query($conn,$sql));

	//첨부파일의 유무
	$sql = "SELECT * FROM files WHERE bid=".$row['bid'];
	if (mysqli_num_rows(mysqli_query($conn,$sql))>0)
		$list_exists_file[] = 1;
	else
		$list_exists_file[] = 0;	
}

?>

<!doctype html>
<html>
  	<head>
		<title>게시판</title>
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

		<script>
			function startsetting(searchmode,samemode,headerid) //검색기능
			{
				document.getElementById('mode').options[searchmode].selected = true;
				document.getElementById('R'+samemode).checked = true;
				document.getElementById('header-'+headerid).className += " active";
			}
		</script>
	</head>
	<body onload="javascript:startsetting(<?=$mode?>,<?=$same?>,1)">   
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
										게시판(<?=count($list)?>)
									</td>
									<td>
										<div style="float: right;">
											<form method="GET" action="board.php" style="display:inline-block;">
												<input type="radio" name="same" id="R0" value="0">포함
												<input type="radio" name="same" id="R1" value="1">일치
												<select id="mode" name="mode">
													<option value='0'> 제목 </option>
													<option value='1'> 내용 </option>
													<option value='2'> 제목+내용 </option>
													<option value='3'> ID </option>
													<option value='4'> 닉네임 </option>
												</select>
												<input type="text" id="search" name="search" value="<?=$search?>" required>
												<input type="submit" value="검색">
											</form>
											<a href="form_write.php" style="display:inline-block;"><input type="button" value="글 작성"></a>
										</div>
										
									</td>
								</tr>
							</thead>
						</table>
						<table class="table table-hover" id="filelist">
							<thead class="thead-dark">
								<tr>
									<th style="width:10%;">게시판번호</th>
									<th style="width:40%;">제목</th>
									<th style="width:10%;">작성자</th>
									<th style="width:10%;">조회수</th>
									<th style="width:10%;">작성일자</th>
									<th style="width:10%;">관리</th>
								</tr>
							</thead> 
							<tbody>
								<?php			     
									for ($i=0;$i<count($list);$i++) { 
										
										?>

										<tr class="alert" role="alert" id="<?=$i+1?>">
											<td><?=$list[$i][0]?></td>
											<td class='long-text'><a href="form_post.php?bid=<?=$list[$i][0]?>"><?=$list[$i][1]?></a> <?php if ($list_exists_file[$i]==1) echo "<img src='img/attachment.png'>";?> (<?=$list_num_review[$i]?>)</td>
											
											<td class='long-text'><a href="form_member.php?uid=<?=$uid[$i]?>"><?=$list[$i][2]?></a></td>
											<td><?=$list_view[$i]?></td>
											<td><?=$list[$i][3]?></td>
											<td>
												<?php
													if (isset($_SESSION['id']) && isset($user) && ($uid[$i] == $_SESSION['uid'] || $user['auth'] == 1 ))
													{
														echo "
															<div style='display :inline-block;'>
																<a href='form_write.php?bid=".$list[$i][0]."'>
																	<button class='post-cata'>수정</button>
																</a>
															</div>
															<div style='display :inline-block;'>
																<a href='".$postctrl."delete.php?bid=".$list[$i][0]."'>
																	<button class='post-cata'>삭제</button>
																</a>
															</div>
														";
													}
													else{
														echo "권한 없음";
													}
												?>
											</td>
										</tr>
								<?php
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</section>

	</body>
</html>
