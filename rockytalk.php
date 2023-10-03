<?php
session_start();
require_once('pdo.php');

if(isset($_POST['name'])){
    if(!empty($_FILES['image']['name'])){
        $image=$_FILES['image']['tmp_name'];
        $imgContent=file_get_contents($image);
        $stmt=$pdo->prepare('INSERT INTO rockytalk (image,text,name,postdate,app) VALUES (:img,:tx,:na,:pd,:ap)');
        $stmt->execute(array(
            ':img'=>$imgContent,
            ':tx'=>$_POST['text'],
            ':na'=>$_POST['name'],
            ':pd'=>$_POST['postdate'],
            ':ap'=>$_POST['app']));
    }else{
        $stmt=$pdo->prepare('INSERT INTO rockytalk (text,name,postdate,app) VALUES (:tx,:na,:pd,:ap)');
        $stmt->execute(array(
            ':tx'=>$_POST['text'],
            ':na'=>$_POST['name'],
            ':pd'=>$_POST['postdate'],
            ':ap'=>$_POST['app']));
    }
    $_SESSION['success']='发布成功！';
    header('Location: rockytalk.php');
    return;
}



?>




<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name = ‘viewport’ content=‘width=device-width, initial-scale=1’>
        <title>rockytuan谈解散MUA</title>
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
                    <li id="con"><a href='rockytalk.php' aria-label='Contact' class='navContent active'>rockytuan谈解散MUA</a></li>
                    <li><a href='signature.php' aria-label='Contact' class='navContent'>参与联署</a></li>
                </ul>
            </div>
            <div id="navBarBackground"></div>
        </header>




        <main>
            <div id="bodyText">
            <div class='bg'>


                <?php
                if ( isset($_SESSION['failure']) ) {
                    echo '<p style="color:red">'.$_SESSION['failure']."</p>\n";
                    unset($_SESSION['failure']);
                }
                if ( isset($_SESSION['success']) ) {
                    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
                    unset($_SESSION['success']);
                }

                if(isset($_SESSION['admin'])){
                    echo "<div id='upload'><form method='post' enctype='multipart/form-data'>";
                    echo "<p>作者： <input type='text' id='name' name='name' size='30' value='rockytuan'></p>";
                    echo "<p>发布场合： <input type='text' id='app' name='app' size='30' value='树洞#'></p>";
                    echo "<p>发布时间： <input type='text' id='postdate' name='postdate' size='30'></p>";
                    echo "<p>内容：<textarea id='text' name='text' rows='5' cols='50'></textarea></p>";
                    echo "<p>图片：<input type='file' name='image'></p>";
                    echo "<input type='submit' value='Submit'>";
                    echo "</form></div>";
                }

                ?>

                <div>
                <h2>rockytuan历来在树洞的发言（正在补充中）</h2>
                <p>完整内容欢迎查看Telegram频道<a href="https://t.me/TheManWhoDisqualifyingMUA" target='_blank'>rockytuan谈解散MUA</a></p>
                </div>

                <?php
                $num=$_GET['next']??1;
                $stmt=$pdo->query('SELECT * FROM rockytalk ORDER BY talk_id DESC LIMIT '.(10*$num));


                while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    echo "<div class='rockytalk' id='rockytalkreply".$row['talk_id']."'>";
                    if($row['image']!=false){
                        echo '<div class="rockytalkimg"><img src="data:image/jpeg;base64,'.base64_encode($row['image']).'"/></div>';
                    }
                    echo "<div class='rockytalktext'><p>";
                    if($row['app']!=false){echo $row['app']."<br>";}
                    echo "作者: ".$row['name']."<br>".$row['postdate']."</p>";
                    echo "<p>".$row['text']."</p>";
                    echo "</div>";
                    echo "<span> <a href='reply.php?next=$num&page=comment_rockytalk&comment_id=".$row['talk_id']."'>回复/查看回复</a></span>".str_repeat('&nbsp;', 5);
                    echo "<br></div><hr>";
                    $last=$row;
                }

                if($last['talk_id']!=1){
                echo "<p><a href='rockytalk.php?next=".($num+1)."#rockytalkreply".$last['talk_id']."'>更多发言</a></p>";
            }



                ?>











            </div>
            </div>
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