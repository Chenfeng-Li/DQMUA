<?php
session_start();
require_once('pdo.php');

if(isset($_POST['name'])&&isset($_POST['sid'])){
    if(strlen($_POST['name'])<1||strlen($_POST['sid'])<1){
        $_SESSION['commentfailure']="请填写姓名和SID";
        header('Location: signature.php#comment');
        return;
    }
    
    if(strlen($_POST['sid'])!=10){
        $_SESSION['commentfailure']="学生证号为10位";
        header('Location: signature.php#comment');
        return;
    }
    if(substr($_POST['sid'],0,4)!="1155"){
        $_SESSION['commentfailure']="学生证号以1155起头";
        header('Location: signature.php#comment');
        return;
    }

    $stmt=$pdo->prepare('SELECT * FROM signature WHERE session_id = :xyz OR sid=:s');
    $stmt->execute(array(":xyz"=>session_id(),":s"=>$_POST['sid']));
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    if($row!==false){
        $_SESSION['commentfailure']="您已参与过联署";
        header('Location: signature.php#comment');
        return;
    }

   
    $stmt=$pdo->prepare('INSERT INTO signature (Timestamp, name, sid, session_id) VALUES (:ts,:na,:sid,:si)');
        $stmt->execute(array(
        	'ts'=>date('Y/m/d h/i/s A EST',time()+3600),
            ':si'=>session_id(),
            ':na'=>$_POST['name'],
            ':sid'=>$_POST['sid']));
    $_SESSION['commentsuccess']='联署成功！';
    header('Location: signature.php#comment');
    return;

    }

?>

<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name = 'viewport' content='width=device-width, initial-scale=1'>
        <title>参与联署</title>
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
                    <li id="abo" class="act" ><a id='top' href='index.php' aria-label='Home' class='navContent'>主页</a></li>
                    <li id="kit"><a  href='ninecomments.php' aria-label='Kitchen' class='navContent'>《对MUA的九条评论》</a></li>
                    <li id="con"><a href='rockytalk.php' aria-label='Contact' class='navContent'>rockytuan谈解散MUA</a></li>
                    <li><a href='signature.php' aria-label='Contact' class='navContent active'>参与联署</a></li>
                </ul>
            </div>
            <div id="navBarBackground"></div>
        </header>




        <main>
            <div id="bodyText">
            <div class='bg'>
            <h2>联署召开全民投票解散香港中文大学内地生联合会(MUA)及其一切附属组织</h2>
            <p><a href="https://forms.gle/Fj9XYGi9BQNgj7wc9">原联署表格</a>(已弃用)<br>最后编辑于 2/10/2022</p>


            <p>联署于 6 Sep 2022 14:00发起<br>7 Sep 2022 8:00 已获得十人签署<br>7 Sep 21:00 2022 已获得二十人签署<br>8 Sep 2022 10:00 已获得三十人签署<br>8 Sep 21:00 2022 已获得四十人签署<br>2 Oct 20:00 2022 已获得五十人签署</p>

            <p>
            	联署目的：<br>
            	腐朽堕落的MUA组织身为我们内地生的领袖组织，吸着我们的血，却没有履行任何对我们的义务。这个组织已经走到了历史尽头，应当由我们亲手推翻。此次联署的目标是是解散MUA及其一切附属组织。
            </p>

            <p>
            	解散原因：<br>
            	其一：仅就2022年选举至今：
            	<ol>
            		<li>MUA当选内阁“廿梧”抄袭往届内阁政纲，被发现后便装死拒绝回应。当选内阁在第一天便爆出丑闻实属罕见，接下来一年的内阁已经失去了信用。</li>
            		<li>2.MUA没有丝毫为内地生发声的迹象。希望大家能认清现状，放弃“MUA为我们争取诉求”的幻想。 4月至今，长达五个月的时间，无论是临时行政会或是当选内阁，无论是迫切期待GPA改革或是内地生ocamp的安排，MUA要么视而不见，要么顺其自然：作为传声筒传达学校的意思。不论是否能左右结果，MUA连争取权益的态度都没拿出来：没有看到据理力争的邮件/电话，没有组织内地生联名请愿，更别说稍微激烈些的抗争手断了。</li>
            		<li>3.MUA的内地生ocamp很失败。成本高昂，安排混乱，考虑不周。很多参与者都表达除了他们的不满。此外，MUA带领初到香港的新生们，组织超过500人群聚活动，无视香港法律及防疫规定，破坏了内地生一直重视的香港法治，贻害无穷。</li>
            	</ol>

            	<br>
            	其二：对MUA这个组织的评论：<br>
            	论纲：
            	<ol>
            	<li>内地生不需要一个领袖组织 </li>
            	<li>MUA丑闻频出  </li>
            	<li>MUA无力组织活动  </li>
            	<li>MUA无法为内地生争取权利  </li>
            	<li>MUA失去民心  </li>
            	<li>MUA操纵选举  </li>
            	<li>MUA控制成员流动  </li>
            	<li>MUA控制不利舆论  </li>
            	<li>MUA作奸犯科 </li>
            	<li> MUA没有未来 </li>
                </ol>
            	关于具体论述，为节省空间，参见<a href='ninecomments.php'>对MUA的九条评论</a>
            </p>

            <p>
            	联署法理依据：<br>
            	MUA会章 第十二条  全民投票可由百分之七点五以上基本会员联署召开。<br>
            	暂未知MUA会员总数，因此无法计算“百分之七点五以上基本会员”人数。根据1月23日弹劾案，该人数约为80。<br>
            	由于会章未提及解散该组织程序，在此采用MUA临时行政会主席释宪(参见下图)。希望MUA在汹涌的民意之下自行解散。<br><br>
            	MUA临时行政会主席释宪<br>
            	<img id="president" src="image/president.png" alt="czn001021: 其实真要弹劾或者让mua解散可以先拉一批人来联名然后交给监察会（邮箱能在推送里看找） 监察会觉得ok就可以召开全民大会，到时候就可以一条条列举罪状看我们咋解释；不过要搞的话快点哈，9.2mcamp开始可能就没太有时间了哈哈哈哈（还挺想感受一次这个氛围的">
            </p>

            <p>收集个人资料声明：<br>
            	会章订明，仅有基本会员有权联署。本表单所收集之个人资料仅会用于确认联署人身份之用途。除MUA监察会外，所有个人资料不会被联署发起者泄露给任何第三方。本联署结果被发送给监察会之前，联署发起者会采取措施，尽力确保监察会对个人资料采取同等程度的保护。联署者提交此表单，即代表同意收取监察会可能发出之身份确认邮件。任何所得之个人资料均会依照《个人资料（私隐）条例》予以保护。如联署者未能提供正确的个人资料，其联署可能无效；如联署者故意提供错误资料，其行为可能触犯香港法例。
            </p>

            <p>
            	发起人的联络方式：<br>
            	disqualify.mua@gmail.com<br>
            	欢迎加入Telegram群 <a href="https://t.me/dqmua">@dqmua</a> 讨论
            </p>
            <br>
            <hr>

            <h2 id="comment">参与联署</h2>
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

            <form method='post'>
            	<p>1.会员姓名: <input type='text' id='name' name='name' size=30></p>
            	<p>2.会员学生证号: <input type='text' id='sid' name='sid' size=30></p>
                <p>注意：一旦提交，即表示您清楚本次联署的目的，并同意参与联署。</p>
            	<input type="submit" value="提交联署">
            </form>
            <br>
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
