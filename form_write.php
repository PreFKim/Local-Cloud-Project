<?php
session_start();
$home="./";
include $home."path.php";
include $commonphp."checklogin.php";

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

        <title>게시글 작성</title>
    </head>

    <body>
        <?php include $commonphp."header.php";?>
		<br>
        <div class="container">
            <div class="row">
                <div class="mx-auto">
                    <div class="card card-signin my-5">
                        <div class="card-body">

                            <?php
                            if (isset($_GET['bid'])) //bid값이 존재하면 글 수정으로 변경되도록
                            {
                                $sql = "SELECT board.*,user.ID,user.nickname FROM board JOIN user ON board.uid = user.uid WHERE bid=".$_GET['bid'];
                                $result = mysqli_query($conn, $sql);
                                $row= mysqli_fetch_array($result) ;
                                if ($row == NULL) {
                                    $_SESSION['msg'] = '존재하지 않는 게시글입니다.';
                                    header('Location: board.php');
                                }       
                                echo "
                                <h2 class='card-title text-center'>글 수정</h2>
                                <form method='post' action='".$postctrl."edit.php'>
                                    <table class='table'>
                                        <tr>
                                            <th>작성자</th>
                                            <td><input type='hidden' name='uid' id='uid' value='".$row['uid']."'>".$row['ID']."</td>
                                        </tr>
                                        <tr>
                                            <th>게시글번호</th>
                                            <td><input type='hidden' name='bid' id='bid' value='".$row['bid']."'>".$row['bid']."</td>
                                        </tr>
                                        <tr>
                                            <th>제목</th>
                                            <td>
                                                <div class='form-label-group'>
                                                    <input type='text' id='title' name='title' class='form-control' placeholder='제목' value='".$row['title']."' required>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>내용</th>
                                            <td>
                                                <div class='form-label-group'>
                                                    <textarea rows='5' id='contents' name='contents' class='form-control' placeholder='내용' required>".$row['contents']."</textarea>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <button class='btn btn-md btn-primary btn-block text-uppercase dark' type='submit'>수정</button>
                                </form>
                                ";
                            }
                            else
                            {
                                echo "
                                <h2 class='card-title text-center'>글 작성</h2>
                                <form method='post' action='".$postctrl."write.php' enctype='multipart/form-data'>
                                    <table class='table'>
                                        <tr>
                                            <th>작성자</th>
                                            <td>".$_SESSION['id']."</td>
                                        </tr>
                                        <tr>
                                            <th>제목</th>
                                            <td>
                                                <div class='form-label-group'>
                                                    <input type='text' id='title' name='title' class='form-control' placeholder='제목' required>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>내용</th>
                                            <td>
                                                <div class='form-label-group'>
                                                    <textarea rows='5' id='contents' name='contents' class='form-control' placeholder='내용' required></textarea>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>업로드</th>
                                            <td>
                                                <div class='form-label-group'>
                                                    <input type='file' name='files[]' id='files' class='form-control' multiple>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <input type='submit' class='btn btn-md btn-primary btn-block text-uppercase dark' value='작성'>
                                    
                                </form>
                                ";
                            }
                            ?>
                            <hr>
                            <a href="board.php"><button class='btn btn-md btn-primary btn-block text-uppercase dark' type='submit'>작성 취소</button></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    

    </body>
</html>