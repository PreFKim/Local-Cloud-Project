<?php
session_start();

$home="./";
include $home."path.php";
include $commonphp."checklogin.php";

if ($user['auth']==0)
{
	$_SESSION['msg'] = "권한이 없습니다.";
	header("location: board.php");
	exit;
}


$list = array();
$where = "";
$search ="";
$mode=0;
$same=0;
if (isset($_GET['search']) && isset($_GET['mode']) && isset($_GET['same'])) //검색할 내용이 들어오면 조건에 맞게 WHERE절 수정
{
	$search=$_GET['search'];
	$mode=$_GET['mode']; 
	$same = $_GET['same'];
	if ($same)
	{
		if ($mode==0) $where = " WHERE uid='$search'";
		else if ($mode==1) $where = " WHERE ID='$search'";
		else if ($mode==2) $where = " WHERE PW='$search'";
		else if ($mode==3) $where = " WHERE nickname='$search'";
		else if ($mode==4) $where = " WHERE auth='$search'";
	}
	else
	{
		if ($mode==0) $where = " WHERE uid like '%$search%'";
		else if ($mode==1) $where = " WHERE ID like '%$search%'";
		else if ($mode==2) $where = " WHERE PW like '%$search%'";
		else if ($mode==3) $where = " WHERE nickname like '%$search%'";
		else if ($mode==4) $where = " WHERE auth like '%$search%'";
	}

}

$sql = "SELECT * FROM user".$where;
$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_array($result)) {
	$tmp = array($row['uid'], $row['ID'],$row['PW'],$row['nickname'],$row['birth'], $row['auth']);
	$list[] = $tmp;
}
?>

<!doctype html>
<html>
  	<head>
		<title>회원관리</title>
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
			function startsetting(searchmode,samemode,headerid)
			{
				document.getElementById('mode').options[searchmode].selected = true;
				document.getElementById('R'+samemode).checked = true;
				document.getElementById('header-'+headerid).className += " active";
			}
		</script>
	</head>
	<body onload="javascript:startsetting(<?=$mode?>,<?=$same?>,3)">   
	
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
										회원관리(<?=count($list)?>)
									</td>
									<td>
										<div style="float: right;">
											<form method="GET" action="form_memctrl.php" style="display:inline-block;">
												<input type="radio" name="same" id="R0" value="0">포함
												<input type="radio" name="same" id="R1" value="1">일치
												<select id="mode" name="mode">
													<option value='0'> uid </option>
													<option value='1'> ID </option>
													<option value='2'> PW </option>
													<option value='3'> 닉네임 </option>
													<option value='4'> 권한 </option>
												</select>
												<input type="text" id="search" name="search" value="<?=$search?>">
												<input type="submit" value="검색">
											</form>
										</div>
										
									</td>
								</tr>
							</thead>
						</table>
						<table class="table table-hover" id="filelist">
							<thead class="thead-dark">
								<tr>
									<th style="width:10%;">UID</th>
									<th style="width:20%;">ID</th>
									<th style="width:20%;">PW</th>
									<th style="width:20%;">닉네임</th>
									<th style="width:10%;">생년월일</th>
									<th style="width:10%;">권한</th>
									<th style="width:10%;">회원관리</th>
								</tr>
							</thead> 
							<tbody >
								<?php			     
									for ($i=0;$i<count($list);$i++) { 
										
										?>
										<tr class="alert" role="alert" id="<?=$i+1?>">
											<td><?=$list[$i][0]?></td>
											<td class="long-text"><?=$list[$i][1]?></td>
											<td class="long-text"><?=$list[$i][2]?></td>
											<td class="long-text"><?=$list[$i][3]?></td>
											<td><?=$list[$i][4]?></td>
											<td><?php if ($list[$i][5]==1) echo "관리자(1)"; else echo"일반회원(0)";?></td>
											<td>
												<a href="form_member.php?uid=<?=$list[$i][0]?>">
													<button class="post-cata"> 정보수정</button>
												</a>
												
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
