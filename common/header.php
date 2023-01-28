<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <div class="collapse navbar-collapse justify-content-between">
            <div class="navbar-nav">
                <!--왼쪽부분-->
                <a href="board.php" class="nav-item nav-link" id="header-1">게시판</a> 
                <a href="cloud.php" class="nav-item nav-link" id="header-2">Cloud</a> 
                <?php
                    if(isset($_SESSION['uid']) && $user['auth']) echo "<a href='form_memctrl.php' class='nav-item nav-link' id='header-3'>회원관리</a>";
                ?>
            </div>
            <?php					
                //중간 부분에 메세지 띄우기
                if (isset($_SESSION['msg']))
                {
                    echo "
                    <div class='navbar-nav'>
                        <label class='nav-item nav-link active'>".$_SESSION['msg']."</label>
                    </div>
                    ";
                    unset( $_SESSION['msg'] );
                }
                if (isset($_SESSION['uid']))
                {
                    echo "
                    <div class='navbar-nav'>
                        <a href='form_member.php?uid=".$_SESSION['uid']."' class='nav-item nav-link'>".$user['nickname']."(".$_SESSION['id'].")</a>
                        <a href='ctrls/memctrl/logout.php' class='nav-item nav-link'>Logout</a>
                    </div>
                    ";
                }
                else
                {
                    echo "
                    <div class='navbar-nav'>
                        <a href='form_join.php' class='nav-item nav-link' id='header-4'>Join</a>
                        <a href='form_login.php' class='nav-item nav-link' id='header-5'>Login</a>
                    </div>
                    ";
                }
            ?>
        </div>
    </div>
</nav>