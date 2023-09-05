<?php
session_start();
require_once('pdo.php');

if($_SERVER['REMOTE_ADDR']=='154.6.80.4'){
    die();
}

if(isset($_POST['alias'])&&isset($_POST['response'])){
    $_SESSION['alias']=$_POST['alias'];
    $_SESSION['response']=$_POST['response'];

    if(strlen($_POST['alias'])<1||strlen($_POST['response'])<1){
        $_SESSION['replyfailure']="请填写名称与留言";
        header('Location: index.php#dissatisfy');
        return;
    }
    if(mb_strlen($_SESSION['alias'],'utf8')>20){
        $_SESSION['replyfailure']="名称应在20字以内";
        header('Location: index.php#dissatisfy');
        return;
    }
    if(mb_strlen($_SESSION['response'],'utf8')>200){
        $_SESSION['replyfailure']="留言应在200字以内";
        header('Location: index.php#dissatisfy');
        return;
    }

    $stmt=$pdo->prepare('SELECT * FROM dissatisfy WHERE session_id = :xyz');
    $stmt->execute(array(":xyz"=>session_id()));
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    if(!isset($_SESSION['admin'])&&$row!==false){
        $_SESSION['replyfailure']="您已留言过，十分感谢";
        header('Location: index.php#dissatisfy');
        return;       
    }

    $stmt=$pdo->prepare('INSERT INTO dissatisfy (name, text, session_id) VALUES (:na,:tx,:si)');
        $stmt->execute(array(
            ':na'=>$_POST['alias'],
            ':tx'=>$_POST['response'],
            ':si'=>session_id()));
        unset($_SESSION['alias']);
        unset($_SESSION['response']);
        $_SESSION['replysuccess']='感谢您的留言！';
        header('Location: index.php#dissatisfy');
        return;
}



if(isset($_POST['name'])&&isset($_POST['commenttext'])){
    $_SESSION['name']=$_POST['name'];
    $_SESSION['commenttext']=$_POST['commenttext'];
    if(!isset($_SESSION['admin'])){
        if(strlen($_POST['name'])<1||strlen($_POST['commenttext'])<1){
            $_SESSION['commentfailure']="请填写名称与留言";
            header('Location: index.php#comment');
            return;
        }
        if(mb_strlen($_SESSION['name'],'utf8')>20){
            $_SESSION['commentfailure']="名称应在20字以内";
            header('Location: index.php#comment');
            return;
        }
        if(strpos($_SESSION['name'],"管理员")||mb_substr($_SESSION['name'],0,3)=="管理员"){
            $_SESSION['commentfailure']='名称中不能出现“管理员”三字';
            header('Location: index.php#comment');
            return;
        }
        if(mb_strlen($_SESSION['commenttext'],'utf8')>1000){
            $_SESSION['commentfailure']="留言应在1000字以内";
            header('Location: index.php#comment');
            return;
        }
        $stmt=$pdo->prepare('SELECT * FROM comment_index WHERE session_id = :xyz ORDER BY comment_id DESC');
        $stmt->execute(array(":xyz"=>session_id()));
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        if($row!==false){
            if(time()-$row['posttime']<300){
                $_SESSION['commentfailure']="发言间隔为五分钟，剩余".(300-time()+$row['posttime'])."秒";
                header('Location: index.php#comment');
                return;
            }
        }

        $stmt=$pdo->prepare('INSERT INTO comment_index (session_id,ip,support,name,comment,subcomment,posttime) VALUES (:si,:ip,:sp,:na,:cm,:sc,:pt)');
        $stmt->execute(array(
            ':si'=>session_id(),
            ':ip'=>$_SERVER['REMOTE_ADDR'],
            ':sp'=>$_POST['support'],
            ':na'=>$_POST['name'],
            ':cm'=>$_POST['commenttext'],
            ':sc'=>0,
            ':pt'=>time()));
        unset($_SESSION['commenttext']);
        unset($_SESSION['name']);
        $_SESSION['commentsuccess']='留言发布！';
        header('Location: index.php#comment');
        return;

    }else{
        $_SESSION['name']=$_POST['name'];
        $_SESSION['commenttext']=$_POST['commenttext'];
        $stmt=$pdo->prepare('INSERT INTO comment_index (session_id,ip,support,name,comment,subcomment,posttime) VALUES (:si,:ip,:sp,:na,:cm,:sc,:pt)');
        $stmt->execute(array(
            ':si'=>session_id(),
            ':ip'=>$_SERVER['REMOTE_ADDR'],
            ':sp'=>'admin',
            ':na'=>$_SESSION['admin']." (管理员)",
            ':cm'=>$_POST['commenttext'],
            ':sc'=>0,
            ':pt'=>time()));
        unset($_SESSION['commenttext']);
        unset($_SESSION['name']);
        $_SESSION['commentsuccess']='留言发布！';
        header('Location: index.php#comment');
        return;
    }
}


if(isset($_POST['revisetext'])){
    if(!isset($_SESSION['admin'])){
        if(strlen($_POST['revisetext'])<1){
            $_SESSION['commentfailure']="请填写留言";
            header('Location: index.php#comment');
            return;
        }

        if(mb_strlen($_POST['revisetext'],'utf8')>1000){
            $_SESSION['commentfailure']="留言应在1000字以内";
            header('Location: index.php#comment');
            return;
        }
    }
    $stmt=$pdo->prepare('UPDATE comment_index SET comment=:cm WHERE comment_id=:ci');
    $stmt->execute(array(":cm"=>$_POST['revisetext'],":ci"=>$_POST['reviseId']));
    $_SESSION['commentsuccess']='修改成功！';
    header('Location: index.php#comment');
    return;
}

if(isset($_POST['deleteId'])){
    $stmt=$pdo->prepare('DELETE FROM comment_index WHERE comment_id=:ci');
    $stmt->execute(array(":ci"=>$_POST['deleteId']));
    $_SESSION['commentsuccess']='删除成功！';
    header('Location: index.php#comment');
    return;
}


?>


<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name = 'viewport' content='width=device-width, initial-scale=1'>
        <title>DQMUA</title>
		<link rel='icon' type='image/png' href='image/icon/DQMUA.png'>

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
                    <li id="abo" class="act" ><a id='top' href='index.php' aria-label='Home' class='navContent active'>主页</a></li>
                    <li id="kit"><a  href='ninecomments.php' aria-label='Kitchen' class='navContent'>《对MUA的九条评论》</a></li>
                    <li id="con"><a href='rockytalk.php' aria-label='Contact' class='navContent'>rockytuan谈解散MUA</a></li>
                    <li><a href='signature.php' aria-label='Contact' class='navContent'>参与联署</a></li>
                </ul>
            </div>
            <div id="navBarBackground"></div>
        </header>




        <main>
        	<div id="bodyText">
        		<div id='aboutcontact'>
        	    	<div id='aboutDQMUA' class='bg'>
        	    		<h2>我们是DQMUA</h2>
        		    	<p>
        		    		欢迎来到DQMUA的网站喵～<br>
        				    DQMUA (Disqualify MUA) 运动发起于2022年4月。<br>
        				    我们旨在向内地生们揭露MUA的邪恶与腐败。这个组织收了我们的会费，自称代表我们，却无法组织像样的活动，不去也无力为内地生争取诉求，阻止内地生和本地生交流融合，终日不断地制造丑闻。
        				</p>
        				<p>
        				    越来越多内地生对MUA的忍耐达到极限，在2022年1月23日展开联署，成功弹劾时任MUA内阁，但这并没有让MUA有所改变。<br>
        				    在此之后，我进行了接近半年的大思考，意识到MUA这组织正不断侵犯着内地生的权益，必须将其彻底解散。<br>
        				</p>
        				<p>
        				    自2022年4月我在树洞 <a href='rockytalk.php?next=10000#rockytalkreply1'>打响解散MUA的第一枪</a> ，到8月系统性批判MUA的 <a href='ninecomments.php'>《九条评论》</a> 成文，我们遭受到了MUA和树洞的严酷打压与迫害，而坚定地活跃至今。<br>
        				    我们的总目标是将MUA及其附属组织彻底解散，与本地生联合，共同组织活动及争取诉求。
        			    </p>
        		    </div>

        		    <div id='contact' class='bg'>
        		    	<h2>网站内容及联络方式</h2>
        		    	<ul>
        		    		<li><b>欢迎阅读DQMUA的纲领 <a href='ninecomments.php'>《对MUA的九条评论》</a></b></li>
        		    		<li>参与 <a href='signature.php'>解散MUA联署</a></li>
        		    		<li>阅读rockytuan在树洞及伪树洞的<a href='rockytalk.php'>发言合集</a>。这些发言也会同步在Telegram群组 <a href='https://t.me/TheManWhoDisqualifyingMUA'>rockytuan谈解散MUA</a></li>
        		    		<li><b>欢迎加入Telegram群组 <a href='https://t.me/dqmua'>DQMUA</a>一同讨论解散MUA，我们主要在此活动。</b></li>
        		    		<li>本站各个页面下设置有评论区，欢迎匿名留言</li>
        		    		<li>为了更多内地生方便讨论与咨询，rockytuan建立了QQ群组 166506542。</li>
        		    		<li>如有必要，也可以通过邮箱联络我。<a href='mailto:jmlichenfeng@gmail.com?subject=联络DQMUA&body=rockytuan你好'> disqualify.mua@gmail.com </a> </li>
        		    	</ul>

        		    	</p>

        		    </div>
        		</div>

        		<div id='gallary'>
        			<h2>影像馆</h2>
        			<figure contenteditable='false' class='figureimg'>
        				<img src='image/poster/p1.jpeg'>
        				<figcaption dir='auto'>原版《对MUA的九条评论》海报</figcaption>
        			</figure>

        			<figure contenteditable='false' class='figureimg'>
        				<img src='image/poster/p2.jpeg'>
        				<figcaption dir='auto'>海报在大膳堂</figcaption>
        			</figure>

        			<figure contenteditable='false' class='figureimg'>
        				<img src='image/poster/p3.jpeg'>
        				<figcaption dir='auto'>海报在蒙工</figcaption>
        			</figure>

        			<figure contenteditable='false'>
        				<video onloadstart='this.volume=0.2' controls loop>
        					<source src="image/treehole.mp4" type="video/mp4">
        				</video>
        				<figcaption dir='auto'>2023年1月为遭爆破的树洞吊唁《谨以此纪念被微信封号禁言的树洞》<br>
        					（然而在一周后MUA的伪树洞上线，我意识到树洞是<a href='ninecomments.php#c82'>遭到MUA恶意举报被封</a>，于是DQMUA与树洞达成统一战线）
        				</figcaption>
        			</figure>

        					

        		</div>

        		<div id="dissatisfy">
        			<h2>欢迎留下您对MUA的控诉</h2>
        			<div id='quitresponse'>
        				
        				<?php 
                        if ( isset($_SESSION['replyfailure']) ) {
                            echo '<p style="color:red">'.$_SESSION['replyfailure']."</p>\n";
                            unset($_SESSION['replyfailure']);
                        }
                        if ( isset($_SESSION['replysuccess']) ) {
                            echo '<p style="color:green">'.$_SESSION['replysuccess']."</p>\n";
                            unset($_SESSION['replysuccess']);
                        }
                        ?>
        				<div id='quit' class='bg'>
        					<p>
        						<b>rockytuan:</b><br>
        						当年的ocamp小组，所有组家长都是MUA成员。为此，当时也被忽悠着加入MUA，虽然从来没当回事，也已经批判MUA一年了，早已不是会员了，还是声明一下退出好。
        					</p>
        				</div>
        				<div id='reply'>
        					<h3>请简单留下您对MUA的控诉</h3>
        					<p>化名在20字以内，留言在200字以内</p>
        					<form method="post">
                                <p>化名: <input type="text" id='alias' name="alias" size="30" value="<?php echo($_SESSION['alias']??'')?>" > </p>
                                <p>
                                    留言：字数： <span id='response'>0</span> / 200<br>
                                    <textarea id="text" name="response" rows="6" cols="35" onchange='wordcount("response",this)'><?php echo($_SESSION['response']??'') ?></textarea><br>
                                </p>
                                <input type="submit" value="留言发布"> 
                            </form>
                            <br>
                        </div>
                    </div>


                    <div id='showarea'>
                    	<h3>战友留言</h3>
                    	<div id='show' class='bg'>

                    	<?php

                    	 $stmt = $pdo->query("SELECT * FROM dissatisfy  ORDER BY id DESC");
                    	 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    	 	echo "<p class='replytext'><b>".$row['name']."</b><br>";
                    	 	echo $row['text']."</p><hr>";
                    	 }




                    	?>
                    </div>
                    </div>
                </div>
                <br>

        	</div>

        	 <hr>

            <div id='comment'>
            <h2> 留言区 </h2>

            <p>
                欢迎留言！<br>
                在DQMUA，没有人会因言获罪。无论是支持，反对或是辱骂，rockytuan不会删除。不会像树洞及伪树洞一样，强制要求登录及无理由删帖封号。<br>
                但以中大学生之名，希望能理性评论。<br>
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
                <p>姓名/化名: <input type="text" id='name' name="name" size="30" value="<?php echo($_SESSION['name']??'')?>" onchange='entername()'>   <br>
                 <input type='checkbox' id='isan'  onchange='isano()'>使用匿名</p>
                <p id="support">
                    立场: <img src="image/icon/taffyDQMUA.png" alt=‘DQMUA’ id='supportimage'>
                    <input id='taffy' type='radio' name="support" value="DQMUA" onchange="supporting()" checked>支持DQMUA
                    <input id='nyaru' type='radio' name="support" value="MUA" onchange="supporting()">支持MUA
                    <input id='seren' type='radio' name="support" value="Neutral" onchange="supporting()">站中间
                </p>
                <p>
                    留言：字数： <span id='count'>0</span> / 1000<br>
                    <textarea id="text" name="commenttext" rows="5" cols="40" onchange='wordcount("count",this)'><?php echo($_SESSION['commenttext']??'') ?></textarea><br>
                </p>
                <input type="submit" value="留言发布"> 
            </form>
            </div>

            <br>
            <hr>

            <?php
            $num=$_GET['next']??1;
            $stmt = $pdo->query("SELECT * FROM comment_index WHERE subcomment = '0' ORDER BY comment_id DESC LIMIT ".(10*$num));


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

                echo "<div class='reply' id=indexreply".$row['comment_id'].">";
                echo "<p><img src='$image' alt=".$row['support'].">   ";
                echo "<span>".htmlentities($row['name']).str_repeat('&nbsp;', 5)."#".$row['comment_id']. str_repeat('&nbsp;', 3).gmdate('H:i d/m/Y',$row['posttime']+28800)."</span></p>";
                echo "<p>".htmlentities($row['comment'])."</p><p>";
                echo "<span> <a href='reply.php?next=$num&page=comment_index&comment_id=".$row['comment_id']."'>回复/查看回复</a></span>".str_repeat('&nbsp;', 5);
                if(isset($_SESSION['admin']) || $row['session_id']==session_id()){
                    echo "<button type='button' onclick='revise(".$row['comment_id'].",this)'>修改</button>".str_repeat('&nbsp;', 5);
                    echo "<button type='button' onclick='Delete(".$row['comment_id'].",this)'>删除</button></p>";

                    echo "<form method='post' style='display:none' id='delete".$row['comment_id']."'>";
                    echo "<input type='hidden' name='deleteId' value=".$row['comment_id'].">";
                    echo "<input type='submit' value='确认删除'>";
                    echo "</form>";


                    echo "<form method='post' style='display:none' id='revise".$row['comment_id']."'>";
                    echo "修改：字数： <span id='".$row['comment_id']."'>".mb_strlen(htmlentities($row['comment']),'utf8')."</span> / 1000<br>";
                    echo "<textarea name='revisetext' rows='5' cols='40' onchange='wordcount(".$row['comment_id'].",this)'>".htmlentities($row['comment'])."</textarea><br>";
                    echo "<input type='hidden' name='reviseId' value=".$row['comment_id'].">";
                    echo "<input type='submit' value='确认修改'>";
                    echo "</form>";
                    
                }
                $last=$row;
                echo "<hr>";
                echo "</p>";
                echo "</div>";
            }

            if($last['comment_id']!=1){
                echo "<p><a href='index.php?next=".($num+1)."#indexreply".$last['comment_id']."'>更多评论</a></p>";
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