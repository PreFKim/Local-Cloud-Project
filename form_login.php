<?php
    session_start();
    $home="./";
    include $home."path.php";

    if (isset($_SESSION['id']))
    {
        $_SESSION['msg'] = "이미 로그인 상태입니다.";
        header("location: board.php");
        exit;
    }
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

        
        <title>로그인</title>
        <script>
			function startsetting(headerid)
			{
				document.getElementById('header-'+headerid).className += " active";
			}
		</script>
    </head>

    <body onload="javascript:startsetting(5)">
        <?php include $commonphp."header.php";?>
		<br>
        <div class="container">
            <div class="row">
                <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                    <div class="card card-signin my-5">
                        <div class="card-body">
                            <h2 class="card-title text-center">로그인</h2>

                            <form method="post" action="<?=$memberctrl?>login.php">

                                <div class="form-label-group">
                                    <input type="text" id="id" name="id" class="form-control" placeholder="ID" required autofocus>
                                </div>
                                <br>

                                <div class="form-label-group">
                                    <input type="password" id="pw" name="pw" class="form-control" placeholder="Password" required>
                                </div>
                                
                                <hr>

                                <button class="btn btn-md btn-primary btn-block text-uppercase dark" type="submit">로그인</button>
                                
                            </form>
                            
                            <form method="post" action="form_join.php">
                                <button class="btn btn-md btn-primary btn-block text-uppercase dark" type="submit">회원가입</button>
                            </form>
                            <hr>
                            <a href="board.php">Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>
</html>