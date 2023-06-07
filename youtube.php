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

    <style>
        .youtube_title{
            text-align: center;
            margin: auto;
            background-color: green;
            width: 60rem;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .youtube_video{
            text-align: center;
            margin: auto;
            background-color: green;
            width: 60rem;
            padding-top: 3rem;
            padding-bottom: 1rem;
        }

        #m1{
            text-align: center;
            margin: auto;
            background-color: aqua;
        }
    </style>
    
</head>
<body>
    <div id="M1">
        <div id="one">  
        <h1>
            <div class="youtube_title">
                <?php 
                    $youtube_title = "DLP 88 Modules";
                    echo $youtube_title;
                ?>
            </div>
        </h1>

            <div class="youtube_video">
                <iframe id="youtubeIframe" width="810" height="535" src="https://www.youtube.com/embed/P9K_j1i_yHs" frameborder="0" allowfullscreen></iframe>
                <br><br>
                <button id="nextvid" onclick="changeYouTubeSrc()">Next Video</button>
            </div>

        <?php
            //YOUTUBE LINK HERE
            $stmt = $conn->prepare("SELECT link FROM content");
            $stmt->execute();
 
            $ytid1 = $stmt->fetch(PDO::FETCH_ASSOC);
            $module43 = $ytid1['link'];

            $ytid2 = $stmt->fetch(PDO::FETCH_ASSOC);
            $module45 = $ytid2['link'];

            $ytid3 = $stmt->fetch(PDO::FETCH_ASSOC);
            $module47 = $ytid3['link'];
              
        ?>
   
        </div>
    </div>

    <script>
        var youtube1 = "<?php echo $module43 ?>";
        var youtube2 = "<?php echo $module45 ?>";
        var youtube3 = "<?php echo $module47 ?>";

        var counter = 0;
        var sources = [
            youtube1,
            youtube2,
            youtube3
        ];

        function changeYouTubeSrc() {
            var iframe = document.getElementById('youtubeIframe');

            var newSrc = sources[counter];

            iframe.src = newSrc;
            counter++;

            if (counter >= sources.length) {
                counter = 0;
            }
        }
    </script>
</body>
</html>
    