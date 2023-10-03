<?php
session_start();
require_once "pdo.php";
if (!isset($_GET['page'])||!isset($_GET['comment_id'])) {
  die('Parameter error');
}

$thisPage="reply.php?next=".($_GET['next']??'1')."&page=".$_GET['page']."&comment_id=".$_GET['comment_id']."#comment";

if(isset($_POST['name'])&&isset($_POST['commenttext'])){
    $_SESSION['name']=$_POST['name'];
    $_SESSION['commenttext']=$_POST['commenttext'];
    if(!isset($_SESSION['admin'])){
        if(strlen($_POST['name'])<1||strlen($_POST['commenttext'])<1){
            $_SESSION['commentfailure']="请填写名称与留言";
            header("Location: $thisPage");
            return;
        }
        if(mb_strlen($_SESSION['name'],'utf8')>20){
            $_SESSION['commentfailure']="名称应在20字以内";
            header("Location: $thisPage");
            return;
        }
        if(strpos($_SESSION['name'],"管理员")||mb_substr($_SESSION['name'],0,3)=="管理员"){
            $_SESSION['commentfailure']='名称中不能出现“管理员”三字';
            header("Location: $thisPage");
            return;
        }
        if(mb_strlen($_SESSION['commenttext'],'utf8')>1000){
            $_SESSION['commentfailure']="留言应在1000字以内";
            header("Location: $thisPage");
            return;
        }
        $stmt=$pdo->prepare('SELECT * FROM '.$_GET['page'].' WHERE session_id = :xyz ORDER BY comment_id DESC');
        $stmt->execute(array(":xyz"=>session_id()));
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        if($row!==false){
            if(time()-$row['posttime']<300){
                $_SESSION['commentfailure']="发言间隔为五分钟，剩余".(300-time()+$row['posttime'])."秒";
                header("Location: $thisPage");
                return;
            }
        }

        $stmt=$pdo->prepare('INSERT INTO '.$_GET['page'].' (session_id,ip,support,name,comment,subcomment,posttime) VALUES (:si,:ip,:sp,:na,:cm,:sc,:pt)');
        $stmt->execute(array(
            ':si'=>session_id(),
            ':ip'=>$_SERVER['REMOTE_ADDR'],
            ':sp'=>$_POST['support'],
            ':na'=>$_POST['name'],
            ':cm'=>$_POST['commenttext'],
            ':sc'=>$_GET['comment_id'],
            ':pt'=>time()));
        unset($_SESSION['commenttext']);
        unset($_SESSION['name']);
        $_SESSION['commentsuccess']='留言发布！';
        header("Location: $thisPage");
        return;

    }else{
        $_SESSION['name']=$_POST['name'];
        $_SESSION['commenttext']=$_POST['commenttext'];
        $stmt=$pdo->prepare('INSERT INTO '.$_GET['page'].' (session_id,ip,support,name,comment,subcomment,posttime) VALUES (:si,:ip,:sp,:na,:cm,:sc,:pt)');
        $stmt->execute(array(
            ':si'=>session_id(),
            ':ip'=>$_SERVER['REMOTE_ADDR'],
            ':sp'=>'admin',
            ':na'=>$_SESSION['admin']." (管理员)",
            ':cm'=>$_POST['commenttext'],
            ':sc'=>$_GET['comment_id'],
            ':pt'=>time()));
        unset($_SESSION['commenttext']);
        unset($_SESSION['name']);
        $_SESSION['commentsuccess']='留言发布！';
        header("Location: $thisPage");
        return;
    }
}

if(isset($_POST['revisetext'])){
    if(!isset($_SESSION['admin'])){
        if(strlen($_POST['revisetext'])<1){
            $_SESSION['commentfailure']="请填写留言";
            header("Location: $thisPage");
            return;
        }

        if(mb_strlen($_POST['revisetext'],'utf8')>1000){
            $_SESSION['commentfailure']="留言应在1000字以内";
            header("Location: $thisPage");
            return;
        }
    }
    $stmt=$pdo->prepare('UPDATE '.$_GET['page'].' SET comment=:cm WHERE comment_id=:ci');
    $stmt->execute(array(":cm"=>$_POST['revisetext'],":ci"=>$_POST['reviseId']));
    $_SESSION['commentsuccess']='修改成功！';
    header("Location: $thisPage");
    return;
}

if(isset($_POST['deleteId'])){
    $stmt=$pdo->prepare('DELETE FROM '.$_GET['page'].' WHERE comment_id=:ci');
    $stmt->execute(array(":ci"=>$_POST['deleteId']));
    $_SESSION['commentsuccess']='删除成功！';
    header("Location: $thisPage");
    return;
}




?>

<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name = ‘viewport’ content=‘width=device-width, initial-scale=1’>
        <title>留言回复</title>
        <link rel='icon' type='image/jpg' href='image/icon/DQMUA.jpeg'>
        <link rel="shortcut icon" type='image/jpg' href='image/icon/DQMUA.jpeg'>

        <script src="https://kit.fontawesome.com/4dd2f70620.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href='css/style.css'>
        <script type="text/javascript" src="js/script.js"></script>

    </head>

        <body>

        <header id='header'>
            <h1 id="h1">DQMUA</h1>
            <i id="navbar" class="fa fa-bars" aria-hidden="true" isopen="True" onclick="clicknav()"></i>
            <div id="mobileNavBar">
                <ul id='navList'>
                    <li id="abo" class="act" ><a id='top' href='index.php' aria-label='Home' class='navContent'>主页</a></li>
                    <li id="kit"><a  href='ninecomments.php' aria-label='Kitchen' class='navContent'>《对MUA的九条评论》</a></li>
                    <li id="con"><a href='rockytalk.php' aria-label='Contact' class='navContent'>rockytuan谈解散MUA</a></li>
                    <li><a href='signature.php' aria-label='Contact' class='navContent'>参与联署</a></li>
                </ul>
            </div>
            <div id="navBarBackground"></div>
        </header>


        <main>

            <br><br><br>


        	<?php
            $pageName=substr($_GET['page'],8,strlen($_GET['page']));
            echo("<p><a href=".$pageName.".php?next=".($_GET['next']??'1')."#".$pageName."reply".$_GET['comment_id'].">返回评论</a></p>");


            if($_GET['page']=='comment_ninecomments'||$_GET['page']=='comment_index'){
            $stmt = $pdo->query("SELECT * FROM ".$_GET['page']." WHERE comment_id=".$_GET['comment_id']);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if($row['support']=="DQMUA"){
                $image="image/icon/taffyDQMUA.png";
            }elseif($row['support']=="MUA"){
                $image="image/icon/nyaruMUA.png";
            }elseif($row['support']=="Neutral"){
                $image="image/icon/sereiNeutral.png";
            }else{
                $image="image/icon/rockytuan.jpg";
            }

            echo "<div class='reply' id='mainreply".$row['comment_id']."'>";
            echo "<p><img src='$image' alt=".$row['support'].">   ";
            echo "<span>".htmlentities($row['name']).str_repeat('&nbsp;', 5)."#".$row['comment_id']. str_repeat('&nbsp;', 3).gmdate('H:i d/m/Y',$row['posttime']+28800)."</span></p>";
            echo "<p>".htmlentities($row['comment'])."</p><br></div>";

            }elseif($_GET['page']=='comment_rockytalk'){

            $stmt = $pdo->query("SELECT * FROM rockytalk WHERE talk_id=".$_GET['comment_id']);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            echo "<div class='rockytalk rkt' id='rockytalkreply".$row['talk_id']."'>";
            if($row['image']!=false){
                echo '<div class="rockytalkimg"><img src="data:image/jpeg;base64,'.base64_encode($row['image']).'"/></div>';
            }
            echo "<div class='rockytalktext'><p>";
            if($row['app']!=false){echo $row['app']."<br>";}
            echo "作者: ".$row['name']."<br>".$row['postdate']."</p>";
            echo "<p>".$row['text']."</p>";
            echo "</div>";
            echo "<br></div>";


            }
            ?>



            <hr>

            <div id='comment'>
            <h2>回复</h2>
            <p>
                名称应在20字以内，且为了便于区分，名称中不能出现“管理员”三字。留言应在1000字以内。<br>
                目前每两条留言需要间隔五分钟。
            </p>
            <?php 
            if ( isset($_SESSION['commentfailure']) ) {
                echo '<p style="color:red">'.$_SESSION['commentfailure']."</p>\n";
                unset($_SESSION['commentfailure']);
            }
            if ( isset($_SESSION['commentsuccess']) ) {
                echo '<p style="color:green">'.$_SESSION['commentsuccess']."</p>\n";
                unset($_SESSION['commentsuccess']);
            }
            ?>
            <form method="post">
                <p>姓名/化名: <input type="text" id='name' name="name" size="30" value="<?php echo($_SESSION['name']??'')?>" onchange='entername()'>   
                 <input type='checkbox' id='isan'  onchange='isano()'>使用匿名</p>
                <p id="support">
                    立场: <img src="image/icon/taffyDQMUA.png" alt=‘DQMUA’ id='supportimage'>
                    <input id='taffy' type='radio' name="support" value="DQMUA" onchange="supporting()" checked>支持DQMUA
                    <input id='nyaru' type='radio' name="support" value="MUA" onchange="supporting()">支持MUA
                    <input id='seren' type='radio' name="support" value="Neutral" onchange="supporting()">站中间
                </p>
                <p>
                    留言：字数： <span id='count'>0</span> / 1000<br>
                    <textarea id="text" name="commenttext" rows="5" cols="50" onchange='wordcount("count",this)'><?php echo($_SESSION['commenttext']??'') ?></textarea><br>
                </p>
                <input type="submit" value="留言发布"> 
            </form>
            </div>
            <br>
            <hr>

            <?php
            $stmt = $pdo->query("SELECT * FROM ".$_GET['page']." WHERE subcomment = ".$_GET['comment_id']." ORDER BY comment_id DESC");

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

                if($row['support']=="DQMUA"){
                    $image="image/icon/taffyDQMUA.png";
                }elseif($row['support']=="MUA"){
                    $image="image/icon/nyaruMUA.png";
                }elseif($row['support']=="Neutral"){
                    $image="image/icon/sereiNeutral.png";
                }else{
                    $image="image/icon/rockytuan.jpg";
                }

                echo "<div class='reply' id=ninecommentsreply".$row['comment_id'].">";
                echo "<p><img src='$image' alt=".$row['support'].">   ";
                echo "<span>".htmlentities($row['name']).str_repeat('&nbsp;', 5)."#".$row['comment_id']. str_repeat('&nbsp;', 3).gmdate('H:i d/m/Y',$row['posttime']+28800)."</span></p>";
                echo "<p>".htmlentities($row['comment'])."</p>";

                if(isset($_SESSION['admin']) || $row['session_id']==session_id()){
                    echo "<p><button type='button' onclick='revise(".$row['comment_id'].",this)'>修改</button>".str_repeat('&nbsp;', 5);
                    echo "<button type='button' onclick='Delete(".$row['comment_id'].",this)'>删除</button></p>";

                    echo "<form method='post' style='display:none' id='delete".$row['comment_id']."'>";
                    echo "<input type='hidden' name='deleteId' value=".$row['comment_id'].">";
                    echo "<input type='submit' value='确认删除'>";
                    echo "</form>";


                    echo "<form method='post' style='display:none' id='revise".$row['comment_id']."'>";
                    echo "修改：字数： <span id='".$row['comment_id']."'>".mb_strlen($row['comment'],'utf8')."</span> / 1000<br>";
                    echo "<textarea name='revisetext' rows='5' cols='50' onchange='wordcount(".$row['comment_id'].",this)'>".htmlentities($row['comment'])."</textarea><br>";
                    echo "<input type='hidden' name='reviseId' value=".$row['comment_id'].">";
                    echo "<input type='submit' value='确认修改'>";
                    echo "</form>";
                    
                }
                echo "<hr>";
                echo "</p>";
                echo "</div>";
            }



            ?>







        </main>

        <footer>
            <p> &copy rockytuan DQMUA</p>
            <p> DQMUA唯一指定官网 </p>
            <p>

                <?php
                if(!isset($_SESSION['admin'])){
                    echo "<a href='adminlogin.php' style='color:white'>管理员登录</a>";
                }else{
                    echo "<a href='adminlogout.php' style='color:white'>管理员登出</a>";
                }
                ?>
           
            </p>
        </footer>






        </body>





</html>