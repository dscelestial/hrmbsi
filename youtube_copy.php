<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
    $user_id = $_COOKIE['user_id'];
 }else{
    $user_id = '';
 }
?>

<!DOCTYPE html>
<html>
<head>
    <title>YouTube Video Player</title>
    <!-- <link rel="stylesheet" href="styles/style.css"> -->

    <style>
        #M1{
            background-color: lightblue;
            width: 80em;
            margin: auto;
        }

        #one{
            text-align: center;
            padding: 1rem;
            
        }

        #btn1{
            text-align: center;
            padding: 1rem;
        }

        #M2{
            background-color: lightyellow;
            width: 80em;
            margin: auto;
            display: none;
        }

        #two{
            text-align: center;
            padding: 1rem;
        }

        #btn2{
            text-align: center;
            padding: 1rem;
        }

        #M3{
            background-color: lightcyan;
            width: 80em;
            margin: auto;
            display: none;
        }

        #three{
            text-align: center;
            padding: 1rem;
        }

        #btn3{
            text-align: center;
            padding: 1rem;
        }

        #M4{
            background-color: lightgoldenrodyellow;
            width: 80em;
            margin: auto;
            display: none;
        }

        #four{
            text-align: center;
            padding: 1rem;
        }

        #btn4{
            text-align: center;
            padding: 1rem;
        }

        #M5{
            background-color: lightsteelblue;
            width: 80em;
            margin: auto;
            display: none;
        }

        #five{
            text-align: center;
            padding: 1rem;
        }

        #btn5{
            text-align: center;
            padding: 1rem;
        }

        #M6{
            background-color: lightpink;
            width: 80em;
            margin: auto;
            display: none;
        }

        #six{
            text-align: center;
            padding: 1rem;
        }

        #btn6{
            text-align: center;
            padding: 1rem;
        }

        #M7{
            background-color: lightgray;
            width: 80em;
            margin: auto;
            display: none;
        }

        #seven{
            text-align: center;
            padding: 1rem;
        }

        #btn7{
            text-align: center;
            padding: 1rem;
        }

        #complete{
            background-color: lightgreen;
            width: 80em;
            margin: auto;
            display: none;
        }

        #com{
            text-align: center;
            padding: 1rem;
        }

        #comp{
            text-align: center;
            padding: 1rem;
        }
    </style>
    
</head>
<body>
    <?php
        // ("SELECT c.*, cc.*, u.* FROM `content_copy` AS C JOIN `content_completion` AS cc ON c.id = cc.content_id JOIN `users` AS u ON cc.user_id = u.id WHERE c.status=?");
        $select_content = $conn->prepare("SELECT c.*, cc.*, u.* FROM `content_copy` AS c JOIN `content_completion` AS cc ON c.id = cc.content_id JOIN `users` AS u ON cc.user_id = u.id WHERE c.status=?");
        $select_content->execute(['active']);
        if($select_content->rowCount() > 0){
                $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);
                $content_id = $fetch_content['vid_id'];
                $vid_id = $fetch_content['vid_id'];
                $ytlink = $fetch_content['link'];
                
              

                $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE content_id = ?");
                $select_likes->execute([$content_id]);
                $total_likes = $select_likes->rowCount();  

                $verify_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND content_id = ?");
                $verify_likes->execute([$user_id, $content_id]);

                $select_completion = $conn->prepare("SELECT * FROM `content_completion` WHERE content_id = ?");
                $select_completion->execute([$content_id]);
                $total_completion = $select_completion->rowCount();  

                $verify_completion = $conn->prepare("SELECT * FROM `content_completion` WHERE user_id = ? AND content_id = ?");
                $verify_completion->execute([$user_id, $content_id]);

                $verify_completion1 = $conn->prepare("SELECT * FROM `content_completion` WHERE user_id = ?");
                $verify_completion1->execute([$user_id]);

                $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
                $select_tutor->execute([$fetch_content['tutor_id']]);
                $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
    ?>
    
    <div id="M1">
        <div id="one">  
        <h1>
            <?php 
                $stmt = $conn->prepare("SELECT title FROM content_copy");
                $stmt->execute();

                $row1 = $stmt->fetch(PDO::FETCH_ASSOC);
                $firstRowId = $row1['title'];
                echo $firstRowId;

                $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
                $secondRowId = $row2['title'];
            ?>
        </h1>

        <?php
            $stmt = $conn->prepare("SELECT link FROM content_copy");
            $stmt->execute();
            $yt1 = $stmt->fetch(PDO::FETCH_ASSOC);
            $m43 = $yt1['link'];

            $yt2 = $stmt->fetch(PDO::FETCH_ASSOC);
            $m45 = $yt2['link'];
        ?>
            <iframe width="850" height="500"
            src="<?= $m43; ?>" >
            </iframe> 
        </div>
            <div id="btn1">
                <button onclick="toggle1()">Mark as complete</button>
            </div>
    </div>

    <div id="M2">
        <div id="two">
            <h1>
                <?php 
                    $stmt = $conn->prepare("SELECT title FROM content_copy");
                    $stmt->execute();
        
                    $row1 = $stmt->fetch(PDO::FETCH_ASSOC);
                    $firstRowId = $row1['title'];
                    
                    $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
                    $secondRowId = $row2['title'];
                    echo $secondRowId;
                ?>
            </h1>

            <?php
                $stmt = $conn->prepare("SELECT link FROM content_copy");
                $stmt->execute();
                $yt1 = $stmt->fetch(PDO::FETCH_ASSOC);
                $m43 = $yt1['link'];

                $yt2 = $stmt->fetch(PDO::FETCH_ASSOC);
                $m45 = $yt2['link'];
            ?>
            
            <iframe width="850" height="500"
            src="<?= $m45; ?>" >
            </iframe>
        </div>
            <div id="btn2">
                <button onclick="toggle2()">Mark as complete</button>
            </div>
    </div>

    <div id="M3">
        <div id="three">
            <h1>
                <?php 
                    $stmt = $conn->prepare("SELECT title FROM content_copy");
                    $stmt->execute();
        
                    $row1 = $stmt->fetch(PDO::FETCH_ASSOC);
                    $firstRowId = $row1['title'];
                    
                    $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
                    $secondRowId = $row2['title'];

                    $row3 = $stmt->fetch(PDO::FETCH_ASSOC);
                    $thirdRowId = $row3['title'];
                    echo $thirdRowId;
                ?>
            </h1>

            <?php
                $stmt = $conn->prepare("SELECT link FROM content_copy");
                $stmt->execute();
                $yt1 = $stmt->fetch(PDO::FETCH_ASSOC);
                $m43 = $yt1['link'];

                $yt2 = $stmt->fetch(PDO::FETCH_ASSOC);
                $m45 = $yt2['link'];

                $yt3 = $stmt->fetch(PDO::FETCH_ASSOC);
                $m47 = $yt3['link'];
            ?>
            
            <iframe width="850" height="500"
            src="<?= $m47; ?>" >
            </iframe>
        </div>
            <div id="btn3">
                <button onclick="toggle3()">Mark as complete</button>
            </div>
    </div>

    <div id="M4">
        <div id="four">
        <h1>DLP: Module 49 and 50</h1>
        <iframe width="850" height="500"
        src="https://www.youtube.com/embed/TyAopBXbbqw?autoplay=1&mute=1controls=0">
        </iframe>
        </div>

        <div id="btn4">
        <button onclick="toggle4()">Mark as complete</button>
        </div>
    </div>

    <div id="M5">
        <div id="five">
        <h1>DLP: Module 51 and 52</h1>
        <iframe width="850" height="500"
        src="https://www.youtube.com/embed/i_Ebjp3iFa8?autoplay=1&mute=1controls=0">
        </iframe>
        </div>

        <div id="btn5">
        <button onclick="toggle5()">Mark as complete</button>
        </div>
    </div>

    <div id="M6">
        <div id="six">
        <h1>DLP: Module 53 and 54</h1>
        <iframe width="850" height="500"
        src="https://www.youtube.com/embed/As0lX8jxIQI?autoplay=1&mute=1controls=0">
        </iframe>
        </div>

        <div id="btn6">
        <button onclick="toggle6()">Mark as complete</button>
        </div>
    </div>

    <div id="M7">
        <div id="seven">
        <h1>DLP: Module 67 and 68</h1>
        <iframe width="850" height="500"
        src="https://www.youtube.com/embed/SlcG4ozzXGQ?autoplay=1&mute=1controls=0">
        </iframe>
        </div>

        <div id="btn7">
        <button onclick="toggle7()">Mark as complete</button>
        </div>
    </div>

    <div id="complete">
        <div id="com">
        <h1>You have now completed the seminar, Click below to generate your certificate which will be sent to your email</h1>

        <div id="comp">
        <button onclick="co()">Generate Certificate</button>
        </div>
    </div>

    <?php
        }
    ?>

    <script>
        function toggle1(){
            var m1 = document.getElementById("M1");
            var m2 = document.getElementById("M2");

            m1.style.display = "none"
            m2.style.display = "block";
        }

        function toggle2(){
            var m2 = document.getElementById("M2");
            var m3 = document.getElementById("M3");

            m2.style.display = "none"
            m3.style.display = "block";
        }

        function toggle3(){
            var m3 = document.getElementById("M3");
            var m4 = document.getElementById("M4");

            m3.style.display = "none";
            m4.style.display = "block";
        }

        function toggle4(){
            var m4 = document.getElementById("M4");
            var m5 = document.getElementById("M5");

            m4.style.display = "none";
            m5.style.display = "block";
        }

        function toggle5(){
            var m5 = document.getElementById("M5");
            var m6 = document.getElementById("M6");

            m5.style.display = "none";
            m6.style.display = "block";
        }

        function toggle6(){
            var m6 = document.getElementById("M6");
            var m7 = document.getElementById("M7");

            m6.style.display = "none";
            m7.style.display = "block";
        }

        function toggle7(){
            var m7 = document.getElementById("M7");
            var comple = document.getElementById("complete");

            m7.style.display = "none";
            comple.style.display = "block";
        }

        function co(){
            var c = document.getElementById("complete");

            if(c.style.display = "block"){
                c.style.display = "none"
            }else{
                c.style.display = "block"
            }
        }
    </script>

    
</body>
</html>
