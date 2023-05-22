    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
              
                <a class="navbar-brand" href="index.php">Acasa</a>
            </div>
                            <!-- Top Right Menu Items -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            
            <ul class="nav navbar-right top-nav ">
                <li class="dropdown">
                    <a href="#" class="navbar-brand" data-toggle="dropdown"><i class="fa fa-user"></i><?php echo '  '.$_SESSION['prenume'].' '.$_SESSION['nume'];?><b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="utilizator.php?id=<?php echo $_SESSION['id'] ?>"><i class="fa fa-fw fa-user"></i> Profile</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
             <!-- Collect the nav links, forms, and other content for toggling -->
                <ul class="nav navbar-nav">
                    <li>
                    <?php if($_SESSION['rol'] =='admin'):?>
                        <a href="departamente.php">Departamente</a>
                    <?php endif ?>  
                    </li>
                    <?php if(isset($_SESSION['username']) && !empty($_SESSION['username'])):?>
                    <li>
                        <a href="logout.php">Logout</a>
                    </li>
                    <?php else:?>
                    <li>
                        <a href="login.php">Login</a>
                    </li>
                    <?php endif ?> 
                    <li>
                        <a href="register.php">Register</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>