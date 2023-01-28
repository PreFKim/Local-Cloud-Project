<?php
session_start();
$home="./";
include $home."path.php";
include $commonphp."checklogin.php";
date_default_timezone_set('Asia/Seoul');

if (isset($_GET['bid'])==false)
{
    $_SESSION['msg'] = '비정상적인 접근입니다.';
    header('Location: board.php');
    exit;
}
$bid = $_GET['bid'];

//해당 게시글 정보들 불러오기
$sql = "SELECT board.*,user.ID,user.nickname FROM board JOIN user ON board.uid = user.uid WHERE bid=$bid";
$result = mysqli_query($conn, $sql);
$row= mysqli_fetch_array($result) ;
if ($row == NULL) {
    $_SESSION['msg'] = '없는 게시글입니다.';
    header('Location: board.php');
}

//현재 회원이 이 글을 조회했었는지 확인하고 조회한 적이 없으면 view 테이블에 해당 게시글을 조회했다고 넣어줌
$sql = "SELECT * FROM view where bid=".$bid." AND uid=".$_SESSION['uid'];
if (mysqli_num_rows(mysqli_query($conn,$sql))==0)
{ 
    $sql = "INSERT INTO view (bid,uid) VALUES ($bid,".$_SESSION['uid'].")";
    mysqli_query($conn,$sql);
}

//해당 게시글을 조회한 사람의 수
$sql = "SELECT * FROM view WHERE bid=$bid";
$viewer = mysqli_num_rows(mysqli_query($conn,$sql));




//해당 게시글의 댓글과 해당 댓글의 회원정보 조회 (대댓글 기능을 사용하려면 무조건 rid기준 으로 오름차순 해야함 rid 기준으로 정렬해서 알고리즘이 간단해짐)
$sql = "SELECT review.*,user.ID,user.nickname FROM review JOIN user ON review.uid = user.uid WHERE bid=$bid ORDER BY review.rid ASC" ;
$result = mysqli_query($conn, $sql);

$list_review=array(); //댓글목록 ,각 rid별 댓글 정보들
$adj_list = array(); //각 rid별로 가지고 있는 하위 댓글 목록 인접리스트 예를들어 rid =2인 댓글이 target이 NULL이 아닌 1이라면 adj_list[1]={2};임 
$rid_list = array(); //각 인덱스에 rid정보 가지고 있음 (키값을 가지고 있는 배열)
$used = array();//해당댓글이 출력된 적이 있는지 판단 이걸로 root 댓글을 판단

while($review = mysqli_fetch_array($result)) {
    $tmp = array($review['rid'],$review['uid'],$review['ID'],$review['contents'],$review['date'],$review['target']);
    $list_review["".$review['rid'].""]=$tmp;
    $adj_list["".$review['rid'].""] = array();
	$used["".$review['rid'].""] = 0;
    $rid_list[] =$review['rid'];
    if($review['target']!="") $adj_list["".$review['target'].""][] = $review['rid'];
}

$result_review =array(); //댓글과 대댓글 순서를 저장한 최종 리스트 (수준과 rid정보가 저장됨)

function reply($depth,$idx) //root댓글을 통해 대댓글들 이어가기
{
    global $result_review;
	global $adj_list;
	global $used;
	$p ="";
	for($i=0;$i<$depth;$i++) //수준을 시각화 하지 않고 숫자로만 표현한다면 for문 사용 대신 $p=$depth로 표현하면 됨
	{
		if ($i == $depth-1) $p =  $p."→";
        else $p =  $p."─";
	}
	$used["$idx"] = 1;
    $result_review[] = array($p,$idx);
	if (isset($adj_list["$idx"])) //하위 댓글들도 정렬을 하려면  adj_list에 rid값만 넣는게 아니라 정렬기준을 넣고 정렬을 먼저 하고 for문 돌리기
	{
		for ($i=0;$i<count($adj_list["$idx"]);$i++)
		{
			reply($depth+1,$adj_list["$idx"][$i]);
		}
	}
}

//root 댓글들을 통해 대댓글들 목록화 하기
//일종의 정렬을 하고 싶다면 root 댓글만 정렬하고 대댓글들은 그냥 가져오기
for ($i=0;$i<count($rid_list);$i++) // 알고리즘 자체를 list_review[?][5]부분이 ""(NULL)인 값만 reply 함수 실행해도 결과는 같음
{
	if ($used["".$rid_list[$i].""]==0)
	{
		reply(0,$rid_list[$i]);
	}
}

//첨부파일 목록 가져오기
$sql = "SELECT * FROM files WHERE bid=$bid";
$result = mysqli_query($conn, $sql);
$list_files=array();
while($files = mysqli_fetch_array($result)) {
    $tmp = array($files['fid'],$files['bid'],$files['filename']);
    $list_files[]=$tmp;
}

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">


        
		<link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/mystyle.css">
        <style>
            .dark{
                background-color:#343a40;
                border-style: none;
                width :100%;
            }
        </style>
        <script>
            function php_func(url,params) {
				var form = document.createElement("form");
				form.setAttribute("method", "post");
				form.setAttribute("action", url);

				for (var key in params) {
					var hiddenField = document.createElement('input');
					hiddenField.setAttribute('type', 'hidden');
					hiddenField.setAttribute('name', key);
					hiddenField.setAttribute('value', params[key]);
					form.appendChild(hiddenField);
				}

				document.body.appendChild(form);
				
				form.submit();
			}

            function downloadfile(filename)
			{
				php_func('<?=$filectrl?>download.php',{bid:'<?=$bid?>',filename:filename});
			}

            function write_nested_reply(targetid)
            {
                var newcontents = prompt("작성할 댓글을 입력하세요", "내용");
                if (newcontents)
                {
                    if (newcontents == "") alert("댓글을 입력하세요");
                    else php_func("<?=$reviewctrl?>write.php?bid=<?=$bid?>",{contents:newcontents,target:targetid});
                }
            }

            function edit_reply(targetrid)
            {
                var newcontents = prompt("수정할 내용을 입력하세요", "내용");
                if (newcontents)
                {
                    if (newcontents == "") alert("댓글을 입력하세요");
                    else php_func("<?=$reviewctrl?>edit.php",{bid:'<?=$bid?>',rid:targetrid,contents:newcontents});
                }
            }
        </script>
        <title>게시글 조회</title>
    </head>

    <body>
        <?php include $commonphp."header.php";?>
		<br>
        <div class="container">
            <div class="row">
                <div class="mx-auto">
                    <div class="card card-signin my-5">
                        <div class="card-body">
                            <h2 class="card-title text-center">게시글 조회</h2>
                            <?php
                            
                            echo "
                            <table class='table'>
                                <thead>
                                    <th style='width:15%;'></th>
                                    <th style='width:85%;'></th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>게시글번호</th>
                                        <td>".$row['uid']."</td>
                                    </tr>
                                    <tr>
                                        <th>작성자</th>
                                        <td>".$row['ID']."</td>
                                    </tr>
                                    <tr>
                                        <th>작성일자</th>
                                        <td>".$row['date']."</td>
                                    </tr>
                                    <tr>
                                        <th>조회수</th>
                                        <td>".$viewer."</td>
                                    </tr>
                                    <tr>
                                        <th>제목</th>
                                        <td class='long-text'>".$row['title']."</td>
                                    </tr>
                                    <tr>
                                        <th>내용</th>
                                        <td class='long-text'>".$row['contents']."</td>
                                    </tr>
                                </tbody>
                            ";
                            if (count($list_files) !=0)
                            {
                                echo "
                                <tr>
                                    <th>첨부파일</th>
                                    <td>
                                ";
                                for ($i=0;$i<count($list_files);$i++)
                                {
                                    echo "<a href='#' onclick='javascript:downloadfile(\"".$list_files[$i][2]."\");'>".$list_files[$i][2]."</a><br>";
                                }
                                echo "
                                    </td>
                                </tr>
                                "
                                ;
                            }

                                        
                            echo "
                            </table>
                            ";
                            if ($row['uid'] == $_SESSION['uid'] || $user['auth']) 
                            {
                                echo "
                                    <div>
                                        <div style='display :inline-block;'>
                                            <a href='form_write.php?bid=$bid'>
                                                <button>수정</button>
                                            </a>
                                        </div>
                                        <div style='display :inline-block;'>
                                            <a href='".$postctrl."delete.php?bid=$bid'>
                                                <button>삭제</button>
                                            </a>
                                        </div>
                                    </div>
                                <hr>
                                ";
                            }
                            
                            echo "

                            <form method='post' action='".$reviewctrl."write.php?bid=$bid'>
                                <textarea rows='2' id='contents' name='contents' class='form-control' placeholder='댓글' required></textarea>
                                <br>
                                <button class='btn btn-md btn-primary btn-block text-uppercase dark' type='submit'>댓글작성</button>
                            </form>
                            <hr>
                            <h2 class='card-title text-center'>댓글</h2>
                            <table class='table table-hover'>
                                <thead>
                                    <tr>
                                        <th style='width:10%;'>번호</th>
                                        <th style='width:10%;'>아이디</th>
                                        <th style='width:10%;'></th>
                                        <th style='width:45%;'>내용</th>
                                        <th style='width:10%;'>작성일자</th>
                                        <th class='text-center' style='width:15%;'>관리 </th>
                                    </tr>
                                </thead>
                                <tbody>
                            ";
                            //댓글 출력
                            for ($i=0;$i<count($result_review);$i++)
                            {
                                echo"
                                <tr>
                                    <td>".($i+1)."</td>
                                    <td><a href='form_member.php?uid=".$list_review["".$result_review[$i][1].""][1]."'>".$list_review["".$result_review[$i][1].""][2]."</a></td>
                                    <td>".$result_review[$i][0]."</td>
                                    <td class='long-text'>".$list_review["".$result_review[$i][1].""][3]."</td>
                                    <td>".$list_review["".$result_review[$i][1].""][4]."</td>
                                    <td class='text-center'>
                                        <div style='display :inline-block;'>
                                            <button onclick='javascript:write_nested_reply(\"".$list_review["".$result_review[$i][1].""][0]."\");'>답글</button>
                                        </div>
                                ";
                                if (isset($_SESSION['id']) && $list_review["".$result_review[$i][1].""][1] == $_SESSION['uid'] || $user['auth'] == 1 )
                                {
                                    echo "
                                        <div style='display :inline-block;'>
                                                <button onclick='javascript:edit_reply(\"".$list_review["".$result_review[$i][1].""][0]."\");'>수정</button>
                                        </div>
                                        <div style='display :inline-block;'>
                                            <a href='".$reviewctrl."delete.php?bid=$bid&rid=".$list_review["".$result_review[$i][1].""][0]."'>
                                                <button>삭제</button>
                                            </a>
                                        </div>
                                    ";
                                }
                                else{
                                    
                                }
                                echo "
                                    
                                    </td>
                                </tr>
                                ";
                            }    
                            
                        
                            ?>
                                </tbody>
                            </table>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    

    </body>
</html>