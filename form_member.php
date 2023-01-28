<?php
session_start();
$home="./";
include $home."path.php";
include $commonphp."checklogin.php";

if (isset($_GET['uid'])==false) 
{
    $_SESSION['msg'] = '비정상적인 접근입니다.';
    header("Location: board.php");
    exit;
}
$uid = $_GET['uid'];

//해당 uid에 대한 회원 정보 확인
$sql = "SELECT * FROM user where uid='$uid'"; 
$result = mysqli_query($conn, $sql);
      
$row= mysqli_fetch_array($result) ;
if ($row == NULL) {
    $_SESSION['msg'] = '조회할 수 없는 회원입니다.';
    header("Location: board.php");
}

//작성 글 수 확인
$sql = "SELECT * FROM board WHERE uid='$uid'";
$result = mysqli_query($conn,$sql);
$num_board = mysqli_num_rows($result);

//작성 댓글 수 확인
$sql = "SELECT * FROM review WHERE uid='$uid'";
$result = mysqli_query($conn,$sql);
$num_review = mysqli_num_rows($result);
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">


        
		<link rel="stylesheet" href="css/bootstrap.css">
        <style>
            .dark{
                background-color:#343a40;
                border-style: none;
                width :100%;
            }
        </style>

        <title>회원정보</title>
    </head>

    <body>
        <?php include $commonphp."header.php";?>
		<br>
        <div class="container">
            <div class="row">
                <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                    <div class="card card-signin my-5">
                        <div class="card-body">
                            <h2 class="card-title text-center">회원정보</h2>

                            
                            <?php
                            if ($row['auth']==1) $auth = "관리자";
                            else $auth ="일반회원";

                            if ($uid == $_SESSION['uid'] || $user['auth'])
                            { //본인이거나 관리자가 해당 회원의 정보를 볼 경우
                                echo "
                                <form method='post' action='".$memberctrl."edit.php?uid=".$uid."'>
                                    <table class='table'>
                                        <tr>
                                            <th>회원번호</th>
                                            <td>".$row['uid']."</td>
                                        </tr>
                                        <tr>
                                            <th>ID</th>
                                            <td>".$row['ID']."</td>
                                        </tr>
                                        <tr>
                                            <th>기존 비밀번호</th>
                                            <td>
                                                <div class='form-label-group'>
                                                    <input type='password' id='oldpw' name='oldpw' class='form-control' placeholder='Old Password'>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>비밀번호</th>
                                            <td>
                                                <div class='form-label-group'>
                                                    <input type='password' id='pw' name='pw' class='form-control' placeholder='New Password'>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>비밀번호 확인</th>
                                            <td>
                                                <div class='form-label-group'>
                                                    <input type='password' id='pwchk' name='pwchk' class='form-control' placeholder='New Password Check'>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>닉네임</th>
                                            <td>
                                                <div class='form-label-group'>
                                                    <input type='text' id='nickname' name='nickname' class='form-control' placeholder='nickname' value='".$row['nickname']."' required>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>생년월일</th>
                                            <td>
                                                <div class='form-label-group'>
                                                    <input type='date' id='birth' name='birth' class='form-control' placeholder='birth' value='".$row['birth']."' required>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>권한</th>
                                            ";
                                        if ($user['auth'] == 0) echo "<td>".$auth."</td>"; //일반 회원의 경우 권한 내용만 보여줌
                                        else  //아닐경우 권한 수정 가능
                                        {
                                            $sel_normal = '';
                                            $sel_admin = '';
                                            if ($row['auth']==1) $sel_admin = 'SELECTED';
                                            else $sel_normal = 'SELECTED';
                                            echo "
                                            <td>
                                                <div class='form-label-group'>
                                                    <select name='auth' class='form-control'>
                                                        <option value='0'". $sel_normal." > 일반회원 </option>
                                                        <option value='1'". $sel_admin."> 관리자 </option>
                                                    </select>
                                                </div>
                                            </td>
                                        ";
                                        }
                                        echo"
                                        </tr>
                                        <tr>
                                            <th>
                                                작성한 글 수
                                            </th>
                                            <td>
                                                $num_board
                                            </td>
                                        <tr>
                                            <th>
                                                작성한 댓글 수
                                            </th>
                                            <td>
                                                $num_review
                                            </td>
                                        </tr>
                                    </table>
                                    <button class='btn btn-md btn-primary btn-block text-uppercase dark' type='submit'>정보수정</button>
                                </form>
                                <hr>
                                <form method='post' action='".$memberctrl."unjoin.php?uid=$uid'>
                                    <button class='btn btn-md btn-primary btn-block text-uppercase dark' type='submit'>회원탈퇴</button>
                                </form>
                                ";
                            }
                            else //이외 사람이 회원 정보를 조회하는 경우
                            {
                                echo "
                                <form>
                                    <table class='table'>
                                        <tr>
                                            <th>회원번호</th>
                                            <td>".$row['uid']."</td>
                                        </tr>
                                        <tr>
                                            <th>ID</th>
                                            <td>".$row['ID']."</td>
                                        </tr>
                                        <tr>
                                            <th>닉네임</th>
                                            <td>".$row['nickname']."</td>
                                        </tr>
                                        <tr>
                                            <th>생년월일</th>
                                            <td>".$row['birth']."</td>
                                        </tr>
                                        <tr>
                                            <th>권한</th>
                                            <td>".$auth."</td>
                                        </tr>
                                        <tr>
                                            <th>
                                                작성한 글 수
                                            </th>
                                            <td>
                                                $num_board
                                            </td>
                                        <tr>
                                            <th>
                                                작성한 댓글 수
                                            </th>
                                            <td>
                                                $num_review
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                                ";
                            }
            
                            echo "<a href='board.php?same=1&mode=3&search=".$row['ID']."'><button class='btn btn-md btn-primary btn-block text-uppercase dark' type='submit'>작성글 조회</button></a>";
                            ?>
                            <hr>
                            <a href="board.php">Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    

    </body>
</html>