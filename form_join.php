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

    <body onload="javascript:startsetting(4)">
        <?php include $commonphp."header.php";?>
		<br>
        <div class="container">
            <div class="row">
                <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                    <div class="card card-signin my-5">
                        <div class="card-body">
                            <h2 class="card-title text-center">회원가입</h2>

                            <form method="post" action="<?=$memberctrl?>join.php">

                                <div class="form-label-group">
                                    <input type="text" id="id" name="id" class="form-control" placeholder="ID(4자리 이상)" required autofocus><br>
                                </div>

                                

                                <div class="form-label-group">
                                    <input type="password" id="pw" name="pw" class="form-control" placeholder="Password(8자리 이상)" required><br>
                                </div>

                                <div class="form-label-group">
                                    <input type="password" id="pwchk" name="pwchk" class="form-control" placeholder="Password Check" required><br>
                                </div>


                                
        
                                <div class="form-label-group">
                                    <input type="text" id="nickname" name="nickname" class="form-control" placeholder="Nickname" required ><br>
                                </div>

                                <div class="form-label-group">
                                    <input type="date" id="birth" name="birth" placeholder="생년월일" class="form-control" required>
                                </div>

                                <hr>
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