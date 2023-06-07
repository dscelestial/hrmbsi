<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:home.php');
}

require "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

if(isset($_POST['certificate'])){

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
   $select_user->execute([$user_id]);
   $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
      $id = $fetch_user['id'];
      $name = $fetch_user['name'];
      $email = $fetch_user['email'];
      $status = $fetch_user['status'];

   $phpmailer = new PHPMailer(true);

   $phpmailer->isSMTP();
   $phpmailer->SMTPAuth = true;
    
   $phpmailer->Host = "sandbox.smtp.mailtrap.io";
   $phpmailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
   $phpmailer->Port = 2525;

   $phpmailer->Username = '1969a7779d071c';
   $phpmailer->Password = '476aaaaf064ce1';

   $subject = "HRMBSI DLP";
   $gmail = "inquiries@hrmbsi.com.ph";
   $hname = "HRMBSI";
   $hnumber = "8-663-0077";

   //$user_id = $_POST['user_id'];
   //$user_id = filter_var($user_id, FILTER_SANITIZE_STRING);
   //$name = $_POST['name'];
   //$name = filter_var($name, FILTER_SANITIZE_STRING);
   //$email = $_POST['email'];
   //$email = filter_var($email, FILTER_SANITIZE_STRING);
   //$status = $_POST['status'];
   //$status = filter_var($status, FILTER_SANITIZE_STRING);

   $datetoday = date('M-d-Y');

   $font = 'components/inc/16304_GARA.ttf';
   $image = imagecreatefrompng("images/ecert.png"); // template ng cert
   $color = imagecolorallocate($image, 19, 21, 22);
   if (strlen($name) < 15){
   imagettftext($image, 25, 0, 180, 305, $color, $font, $name);// Name sa gitna ng Cert
   $date = date('M-d-Y');
   imagettftext($image, 10, 0, 225, 435, $color, $font, $date); // Date
   } else {
   imagettftext($image, 25, 0, 105, 305, $color, $font, $name);// Name sa gitna ng Cert
   $date = date('M-d-Y');
   imagettftext($image, 10, 0, 220, 435, $color, $font, $date); // Date   
   }
   $file = $name;
   $time = time();

   imagepng($image, "uploaded_files/ecert/" . $file . "-" . $date . "-" . $time . ".png"); // ex. filename = User's Name-April-27-2022-125612.png
   imagedestroy($image);

   $attachment_link = $file . "-" . $date . "-" . $time . ".png";
   $link_folder = 'uploaded_files/ecert/' . $attachment_link;

         $insert_user = $conn->prepare("INSERT INTO `ecert`(user_id, name, email, attachmentlink, attachmentloc, date) VALUES(?,?,?,?,?,?)");
         $insert_user->execute([$user_id, $name, $email, $attachment_link, $link_folder, $datetoday]);
         $message[] = 'Certificate Created! Visit their profile to view their Certificate.';
         //echo "<center><img src='$imgpath' class='img-thumbnail'><center>";   

         $phpmailer->addAttachment("uploaded_files/ecert/" . $file . "-" . $date . "-" . $time . ".png");
         $imgpath = ("uploaded_files/ecert/" . $file . "-" . $date . "-" . $time . ".png");

         $msg = "Good Day $name, Your have completed all the modules and the certificate has been sent to your email - $email.

         <center><img src='$imgpath' class='img-thumbnail'><center>
   
         Contact us for more details on our number $hnumber or email us at $gmail

         Regards, $hname.";

         $phpmailer->setFrom($gmail,$hname);
         $phpmailer->addAddress($email, $name);

         $phpmailer->Subject = $subject;
         $phpmailer->Body = $msg;

         $phpmailer->send();            
         $message[] = 'Congratulations! You have received your certificate.';  
}

if(isset($_POST['like_content'])){

   if($user_id != ''){

      $content_id = $_POST['content_id'];
      $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

      $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $select_content->execute([$content_id]);
      $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);

      $tutor_id = $fetch_content['tutor_id'];

      $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND content_id = ?");
      $select_likes->execute([$user_id, $content_id]);

      if($select_likes->rowCount() > 0){
         $remove_likes = $conn->prepare("DELETE FROM `likes` WHERE user_id = ? AND content_id = ?");
         $remove_likes->execute([$user_id, $content_id]);
         $message[] = 'removed from likes!';
      }else{
         $insert_likes = $conn->prepare("INSERT INTO `likes`(user_id, tutor_id, content_id) VALUES(?,?,?)");
         $insert_likes->execute([$user_id, $tutor_id, $content_id]);
         $message[] = 'Added to likes!';
      }

   }else{
      $message[] = 'Please login first!';
   }

}

if(isset($_POST['add_comment'])){

   if($user_id != ''){

      $id = unique_id();
      $comment_box = $_POST['comment_box'];
      $comment_box = filter_var($comment_box, FILTER_SANITIZE_STRING);
      $content_id = $_POST['content_id'];
      $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

      $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $select_content->execute([$content_id]);
      $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);

      $tutor_id = $fetch_content['tutor_id'];

      if($select_content->rowCount() > 0){

         $select_comment = $conn->prepare("SELECT * FROM `comments` WHERE content_id = ? AND user_id = ? AND tutor_id = ? AND comment = ?");
         $select_comment->execute([$content_id, $user_id, $tutor_id, $comment_box]);

         if($select_comment->rowCount() > 0){
            $message[] = 'Comment already added!';
         }else{
            $insert_comment = $conn->prepare("INSERT INTO `comments`(id, content_id, user_id, tutor_id, comment) VALUES(?,?,?,?,?)");
            $insert_comment->execute([$id, $content_id, $user_id, $tutor_id, $comment_box]);
            $message[] = 'New comment added!';
         }

      }else{
         $message[] = 'Something went wrong!';
      }

   }else{
      $message[] = 'Please login first!';
   }

}

if(isset($_POST['delete_comment'])){

   $delete_id = $_POST['comment_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
   $verify_comment->execute([$delete_id]);

   if($verify_comment->rowCount() > 0){
      $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
      $delete_comment->execute([$delete_id]);
      $message[] = 'Comment deleted successfully!';
   }else{
      $message[] = 'Comment already deleted!';
   }

}

if(isset($_POST['update_now'])){

   $update_id = $_POST['update_id'];
   $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);
   $update_box = $_POST['update_box'];
   $update_box = filter_var($update_box, FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? AND comment = ?");
   $verify_comment->execute([$update_id, $update_box]);

   if($verify_comment->rowCount() > 0){
      $message[] = 'Comment already added!';
   }else{
      $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
      $update_comment->execute([$update_box, $update_id]);
      $message[] = 'Comment edited successfully!';
   }

}

if(isset($_POST['complete'])){

   if($user_id != ''){

      $content_id = $_POST['content_id'];
      $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

      $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $select_content->execute([$content_id]);
      $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);

      $tutor_id = $fetch_content['tutor_id'];

      $select_completion = $conn->prepare("SELECT * FROM `content_completion` WHERE user_id = ? AND content_id = ?");
      $select_completion->execute([$user_id, $content_id]);

      if($select_completion->rowCount() > 0){
         $remove_completion = $conn->prepare("DELETE FROM `content_completion` WHERE user_id = ? AND content_id = ?");
         $remove_completion->execute([$user_id, $content_id]);
         $message[] = 'Marked as Incomplete!';
      }else{
         $insert_completion = $conn->prepare("INSERT INTO `content_completion`(user_id, tutor_id, content_id) VALUES(?,?,?)");
         $insert_completion->execute([$user_id, $tutor_id, $content_id]);
         $message[] = 'Marked as Complete!';
      }

   }else{
      $message[] = 'Please login first!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>HRMBSi | Watch Video</title>
   <link rel="icon" href="images/hrmbsi icon.png">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <style>
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;500;600&display=swap');

:root{
   --main-color:#d6b43b;
   --red:#e74c3c;
   --green:#265828;
   --white:#fff;
   --black:#2c3e50;
   --light-color:#888;
   --light-bg:#eee;
   --border:.1rem solid rgba(0,0,0,.2);
}

*{
   font-family: 'Nunito', sans-serif;
   margin: 0; padding: 0;
   box-sizing: border-box;
   outline: none; border: none;
   text-decoration: none;
}

*::selection{
   background-color: var(--main-color);
   color: #fff;
}

*::-webkit-scrollbar{
   height: .5rem;
   width: 1rem;
}

*::-webkit-scrollbar-track{
   background-color: transparent;
}

*::-webkit-scrollbar-thumb{
   background-color: var(--main-color);
}

html{
   font-size: 62.5%;
   overflow-x: hidden;
}

body{
   background-color: var(--light-bg);
   padding-left: 30rem;
}

body.dark{
   --white:#222;
   --black:#fff;
   --light-color:#aaa;
   --light-bg:#333;
   --border:.1rem solid rgba(255,255,255,.2);
}

body.active{
   padding-left: 0;
}

section{
   padding:2rem;
   max-width: 1200px;
   margin: 0 auto;
}

.btn,
.option-btn,
.delete-btn,
.inline-btn,
.inline-option-btn,
.inline-delete-btn{
   border-radius: .5rem;
   padding: 1rem 3rem;
   font-size: 1.8rem;
   color: #fff;
   margin-top: 1rem;
   text-transform: capitalize;
   cursor: pointer;
   text-align: center;
}

.btn,
.option-btn,
.delete-btn{
   display: block;
   width: 100%;
}

.inline-btn,
.inline-option-btn,
.inline-delete-btn{
   display: inline-block;
}

.btn,
.inline-btn{
   background-color: var(--main-color);
}

.option-btn,
.inline-option-btn{
   background-color: var(--green);
}

.delete-btn,
.inline-delete-btn{
   background-color: var(--red);
}

.btn:hover,
.option-btn:hover,
.delete-btn:hover,
.inline-btn:hover,
.inline-option-btn:hover,
.inline-delete-btn:hover{
   background-color: var(--black);
   color: var(--white);
}

.flex-btn{
   display:flex;
   gap: 1rem;
}

.message{
   position: sticky;
   top:0;
   margin:0 auto;
   background-color: var(--light-bg);
   padding:2rem;
   display: flex;
   align-items: center;
   gap:1rem;
   justify-content: space-between;
}

.message.form{
   max-width: 1200px;
   margin: 0 auto;
   background-color: var(--white);
   top: 2rem;
   border-radius: .5rem;
}

.message span{
   font-size: 2rem;
   color:var(--black);
}

.message i{
   font-size: 2.5rem;
   color:var(--red);
   cursor: pointer;
   transition: .2s linear;
}

.message i:hover{
   transform: rotate(90deg);
}

.empty{
   background-color: var(--white);
   border-radius: .5rem;
   padding: 1.5rem;
   text-align: center;
   width: 100%;
   font-size: 2rem;
   color: var(--red);
}

.heading{
   padding-bottom: 1.8rem;
   border-bottom: var(--border);
   font-size: 2.5rem;
   color: var(--black);
   text-transform: capitalize;
   margin-bottom: 3rem;
}

.header{
   background-color: var(--white);
   border-bottom: var(--border);
   position: sticky;
   top: 0; left: 0; right: 0;
   z-index: 1000;
}

.header .flex{
   padding: 1.5rem 2rem;
   position: relative;
   display: flex;
   align-items: center;
   justify-content: space-between;
}

.header .flex .logo{
   font-size: 2.5rem;
   color: var(--black);
   font-weight: bolder;
}

.header .flex .search-form{
   width: 50rem;
   border-radius: .5rem;
   display: flex;
   align-items: center;
   gap: 2rem;
   padding: 1.5rem 2rem;
   background-color: var(--light-bg);
}

.header .flex .search-form input{
   width: 100%;
   background:none;
   font-size: 2rem;
   color: var(--black);
}

.header .flex .search-form button{
   font-size: 2rem;
   color: var(--black);
   cursor: pointer;
   background: none;
}

.header .flex .search-form button:hover{
   color: var(--main-color);
}

.header .flex .icons div{
   font-size: 2rem;
   color: var(--black);
   border-radius: .5rem;
   height: 4.5rem;
   cursor: pointer;
   width: 4.5rem;
   line-height: 4.4rem;
   background-color: var(--light-bg);
   text-align: center;
   margin-left: .5rem;
}

.header .flex .icons div:hover{
   background-color: var(--black);
   color:var(--white);
}

#search-btn{
   display: none;
}

.header .flex .profile{
   position: absolute;
   top: 120%; right: 2rem;
   background-color: var(--white);
   border-radius: .5rem;
   padding: 2rem;
   text-align: center;
   width: 30rem;
   transform: scale(0);
   transform-origin: top right;
}

.header .flex .profile.active{
   transform: scale(1);
   transition: .2s linear;
}

.header .flex .profile img{
   height: 10rem;
   width: 10rem;
   border-radius: 50%;
   object-fit: cover;
   margin-bottom: .5rem;
}

.header .flex .profile h3{
   font-size: 2rem;
   color: var(--black);
}

.header .flex .profile span{
   color: var(--light-color);
   font-size: 1.6rem;
}

.side-bar{
   position: fixed;
   top: 0; left: 0;
   height: 100vh;
   width: 30rem;
   background-color: var(--white);
   border-right: var(--border);
   z-index: 1200;
}

.side-bar .close-side-bar{
   text-align: right;
   padding: 2rem;
   padding-bottom: 0;
   display: none;
}

.side-bar .close-side-bar i{
   height: 4.5rem;
   width: 4.5rem;
   line-height: 4.4rem;
   font-size: 2.5rem;
   color: #fff;
   cursor: pointer;
   background-color: var(--red);
   text-align: center;
   border-radius: .5rem;
}

.side-bar .close-side-bar i:hover{
   background-color: var(--black);
}

.side-bar .profile{
   padding:3rem 2rem;
   text-align: center;
}

.side-bar .profile img{
   height: 10rem;
   width: 10rem;
   border-radius: 50%;
   object-fit: cover;
   margin-bottom: .5rem;
}

.side-bar .profile h3{
   font-size: 2rem;
   color: var(--black);
}

.side-bar .profile span{
   color: var(--light-color);
   font-size: 1.6rem;
}

.side-bar .navbar a{
   display:block;
   padding: 2rem;
   margin: .5rem 0;
   font-size: 1.8rem;
}

.side-bar .navbar a i{
   color: var(--main-color);
   margin-right: 1.5rem;
   transition: .2s linear;
}

.side-bar .navbar a span{
   color: var(--light-color);
}

.side-bar .navbar a:hover{
   background-color: var(--light-bg);
}

.side-bar .navbar a:hover i{
   margin-right: 2.5rem;
}

.side-bar.active{
   left: -31rem;
}

.quick-select .box-container{
   display: grid;
   grid-template-columns: repeat(auto-fit, minmax(27.5rem, 1fr));
   gap: 1.5rem;
   align-items: flex-start;
   justify-content: center;
}

.quick-select .box-container .box{
   border-radius: .5rem;
   background-color: var(--white);
   padding: 2rem;
}

.quick-select .box-container .box .title{
   font-size: 2rem;
   color: var(--black);
}

.quick-select .box-container .box p{
   line-height: 1.5;
   padding-top: 1.5rem;
   color: var(--light-color);
   font-size: 1.8rem;
}

.quick-select .box-container .box p span{
   color: var(--main-color);
}

.quick-select .box-container .tutor{
   text-align: center;
}

.quick-select .box-container .tutor p{
   padding-bottom: 1rem;
}

.quick-select .box-container .box .flex{
   display: flex;
   flex-wrap: wrap;
   gap: 1rem;
   padding-top: 2rem;
}

.quick-select .box-container .box .flex a{
   padding: 1rem 1.5rem;
   border-radius: .5rem;
   font-size: 1.6rem;
   background-color: var(--light-bg);
}

.quick-select .box-container .box .flex a i{
   margin-right: 1rem;
   color: var(--black);
}

.quick-select .box-container .box .flex a span{
   color: var(--light-color);
}

.quick-select .box-container .box .flex a:hover{
   background-color: var(--black);
}

.quick-select .box-container .box .flex a:hover i{
   color: var(--white);
}

.quick-select .box-container .box .flex a:hover span{
   color: var(--white)  ;
}

.courses .box-container{
   display: grid;
   grid-template-columns: repeat(auto-fit, 35rem);
   gap: 1.5rem;
   align-items: flex-start;
   justify-content: center;
}

.courses .box-container .box{
   border-radius: .5rem;
   background-color: var(--white);
   padding: 2rem;
}

.courses .box-container .box .tutor{
   margin-bottom: 2rem;
   display: flex;
   align-items: center;
   gap: 2rem;
}

.courses .box-container .box .tutor img{
   width: 5rem;
   height: 5rem;
   border-radius: 50%;
   object-fit: cover;
   margin-bottom: .5rem;
}

.courses .box-container .box .tutor h3{
   font-size: 2rem;
   color: var(--black);
   margin-bottom: .2rem;
}

.courses .box-container .box .tutor span{
   font-size: 1.5rem;
   color: var(--light-color);
}

.courses .box-container .box .thumb{
   width: 100%;
   border-radius: .5rem;
   height: 20rem;
   object-fit: cover;
   margin-bottom: .3rem;
}

.courses .box-container .box .title{
   font-size: 2rem;
   color: var(--black);
   margin-top: .5rem;
   padding: .5rem 0;
}

.courses .more-btn{
   margin-top: 2rem;
   text-align: center;
}

.about .row{
   display: flex;
   align-items: center;
   gap: 1.5rem;
   flex-wrap: wrap;
}

.about .row .image{
   flex: 1 1 40rem;
}

.about .row .image img{
   width: 100%;
   height: 50rem;
}

.about .row .content{
   flex: 1 1 40rem;
   text-align: center;
}

.about .row .content h3{
   font-size: 2.5rem;
   color: var(--black);
}

.about .row .content p{
   line-height: 2;
   font-size: 1.8rem;
   color: var(--light-color);
   padding: 1rem 0;
}

.about .box-container{
   margin-top: 3rem;
   display: flex;
   gap: 1.5rem;
   flex-wrap: wrap;
}

.about .box-container .box{
   flex: 1 1 25rem;
   display: flex;
   background-color: var(--white);
   border-radius: .5rem;
   padding: 2rem;
   align-items: center;
   gap: 2rem;
}

.about .box-container .box i{
   font-size: 3rem;
   color: var(--black);
}

.about .box-container .box h3{
   color: var(--main-color);
   font-size: 2.5rem;
   margin-bottom: .2rem;
}

.about .box-container .box span{
   font-size: 1.6rem;
   color: var(--light-color);
}

.reviews .box-container{
   display: grid;
   grid-template-columns: repeat(auto-fit, minmax(30rem, 1fr));
   gap: 1.5rem;
   align-items: flex-start;
   justify-content: center;
}

.reviews .box-container .box{
   border-radius: .5rem;
   padding: 2rem;
   background-color: var(--white);
   padding-top: 1.5rem;
}

.reviews .box-container .box p{
   line-height: 2;
   font-size: 1.7rem;
   color: var(--light-color);
}

.reviews .box-container .box .user{
   display: flex;
   align-items: center;
   gap: 1.5rem;
   margin-top: 1.5rem;
}

.reviews .box-container .box .user img{
   height: 5rem;
   width: 5rem;
   border-radius: 50%;
   object-fit: cover;
}

.reviews .box-container .box .user h3{
   font-size: 2rem;
   color: var(--black);
   margin-bottom: .2rem;
}

.reviews .box-container .box .user .stars i{
   color: var(--main-color);
   font-size: 1.5rem;
}

.playlist .row{
   display: flex;
   align-items: center;
   gap:2.5rem;
   flex-wrap: wrap;
   padding: 2rem;
   background-color: var(--white);
}

.playlist .row .col{
   flex: 1 1 40rem;
}

.playlist .row .col .save-list button{
   font-size: 2rem;
   border-radius: .5rem;
   background-color: var(--light-bg);
   padding: 1.2rem 2.5rem;
   cursor: pointer;
   margin-bottom: 2rem;
}

.playlist .row .col .save-list button i{
   color: var(--black);
   margin-right: 1rem;
}

.playlist .row .col .save-list button span{
   color: var(--light-color);
}

.playlist .row .col .save-list button:hover{
   background-color: var(--black);
}

.playlist .row .col .save-list button:hover i{
   color: var(--white);
}

.playlist .row .col .save-list button:hover span{
   color: var(--white);
}

.playlist .row .col .thumb{
   position: relative;
   height: 30rem;
}

.playlist .row .col .thumb span{
   position: absolute;
   top: 1rem; left: 1rem;
   border-radius: .5rem;
   padding: .5rem 1.5rem;
   font-size: 2rem;
   color: #fff;
   background-color: rgba(0,0,0,.3);
}

.playlist .row .col .thumb img{
   width: 100%;
   height: 100%;
   border-radius: .5rem;
   object-fit: cover;
}

.playlist .row .col .tutor{
   display: flex;
   align-items: center;
   gap: 1.7rem;
}

.playlist .row .col .tutor img{
   height: 7rem;
   width: 7rem;
   border-radius: 50%;
   object-fit: cover;
}

.playlist .row .col .tutor h3{
   font-size: 2rem;
   color: var(--black);
   margin-bottom: .2rem;
}

.playlist .row .col .tutor span{
   color: var(--main-color);
   font-size: 1.5rem;
}

.playlist .row .col .details{
   padding-top: 1.5rem;
}

.playlist .row .col .details h3{
   font-size: 2rem;
   color: var(--black);
}

.playlist .row .col .details p{
   padding: 1rem 0;
   line-height: 2;
   color: var(--light-color);
   font-size: 1.7rem;
}

.playlist .row .col .details .date{
   font-size: 1.7rem; 
   padding-top: .5rem;
}

.playlist .row .col .details .date i{
   color: var(--main-color);
   margin-right: 1rem;
}

.playlist .row .col .details .date span{
   color: var(--light-color);
}

.videos-container .box-container{
   display: grid;
   grid-template-columns: repeat(auto-fit, 35rem);
   gap: 1.5rem;
   align-items: flex-start;
   justify-content: center;
}

.videos-container .box-container .box{
   position: relative;
   border-radius: .5rem;
   padding: 2rem;
   background-color: var(--white);
}

.videos-container .box-container .box img{
   height: 20rem;
   width: 100%;
   border-radius: .5rem;
   object-fit: cover;
}

.videos-container .box-container .box i{
   position: absolute;
   top: 2rem; left: 2rem; right: 2rem;
   height: 20rem;
   background-color: rgba(0,0,0,.3);
   display: flex;
   align-items: center;
   justify-content: center;
   font-size: 4rem;
   color: #fff;
   border-radius: .5rem;
   display: none;
}

.videos-container .box-container .box:hover i{
   display: flex;
}

.videos-container .box-container .box h3{
   font-size: 2rem;
   color: var(--black);
   margin-top: 1rem;
}

.videos-container .box-container .box:hover h3{
   color: var(--main-color);
}

.watch-video .video-details{
   background-color: var(--white);
   padding: 2rem;
   border-radius: .5rem;
}

.watch-video .video-details .video{
   width: 100%;
   border-radius: .5rem;
   background: #000;
   height: 50rem;
}

.watch-video .video-details .title{
   font-size: 2rem;
   color: var(--black);
   padding: 1.5rem 0;
}

.watch-video .video-details .info{
   display: flex;
   gap: 2rem;
   padding-bottom: 1.5rem;
   border-bottom: var(--border);
}

.watch-video .video-details .info p{
   font-size:1.6rem;
}

.watch-video .video-details .info p i{
   margin-right: 1rem;
   color: var(--main-color);
}

.watch-video .video-details .info p span{
   color: var(--light-color);
}

.watch-video .video-details .tutor{
   padding: 2rem 0;
   display: flex;
   align-items: center;
   gap: 2rem;
}

.watch-video .video-details .tutor img{
   height: 7rem;
   width: 7rem;
   border-radius: 50%;
   object-fit: cover;
}

.watch-video .video-details .tutor h3{
   font-size: 2rem;
   color: var(--black);
   margin-bottom: .2rem;
}

.watch-video .video-details .tutor span{
   color: var(--light-color);
   font-size: 1.5rem;
}

.watch-video .video-details .flex{
   display: flex;
   align-items: center;
   gap: 1.5rem;
   justify-content: space-between;
}

.watch-video .video-details .flex a{
   margin-top: 0;
}

.watch-video .video-details .flex button{
   background-color: var(--light-bg);
   cursor: pointer;
   padding: 1rem 2.5rem;
   font-size: 2rem;
   border-radius: .5rem;
}

.watch-video .video-details .flex button i{
   color: var(--black);
   margin-right: 1rem;
}

.watch-video .video-details .flex button span{
   color: var(--light-color);
}

.watch-video .video-details .flex button:hover{
   background-color: var(--black);
}

.watch-video .video-details .flex button:hover i{
   color: var(--white);
}

.watch-video .video-details .flex button:hover span{
   color: var(--white);
}

.watch-video .video-details .description{
   padding-top: 2rem;
}

.watch-video .video-details .description p{
   line-height: 1.5;
   font-size: 1.7rem;
   color: var(--light-color);
   white-space: pre-line;
}

.comments .add-comment{
   background-color: var(--white);
   border-radius: .5rem;
   margin-bottom: 3rem;
   padding: 2rem;
}

.comments .add-comment textarea{
   border-radius: .5rem;
   padding: 1.4rem;
   width: 100%;
   height: 20rem;
   background-color: var(--light-bg);
   resize: none;
   font-size: 1.8rem;
   color: var(--black);
}

.comments .show-comments{
   background-color: var(--white);
   border-radius: .5rem;
   padding: 2rem;
   display: grid;
   gap: 2.5rem;
}

.comments .show-comments .user{
   display: flex;
   align-items: center;
   gap: 1.5rem;
   margin-bottom: 2rem;
}

.comments .show-comments .user img{
   height: 5rem;
   width: 5rem;
   border-radius: 50%;
   object-fit: cover;
}

.comments .show-comments .user h3{
   font-size: 2rem;
   color: var(--black);
   margin-bottom: .2rem;
}

.comments .show-comments .user span{
   color: var(--light-color);
   font-size: 1.5rem;
}

.comments .show-comments .content{
   margin-bottom: 2rem;
}

.comments .show-comments .content p{
   font-size: 2rem;
   color: var(--black);
   padding: 0 1rem;
   display: inline-block;
}

.comments .show-comments .content span{
   font-size: 1.7rem;
   color: var(--light-color);
}

.comments .show-comments .content a{
   color: var(--main-color);
   font-size: 1.8rem;
}

.comments .show-comments .content a:hover{
   text-decoration: underline;
}

.comments .show-comments .text{
   border-radius: .5rem;
   background-color: var(--light-bg);
   padding: 1rem 1.5rem;
   color: var(--black);
   margin: .5rem 0;
   position: relative;
   z-index: 0;
   white-space: pre-line;
   font-size: 1.8rem;
}

.comments .show-comments .text::before{
   content: '';
   position: absolute;
   top: -1rem; left: 1.5rem;
   height: 1.2rem;
   width: 2rem;
   background-color: var(--light-bg);
   clip-path: polygon(50% 0%, 0% 100%, 100% 100%); 
}

.edit-comment form{
   background-color: var(--white);
   border-radius: .5rem;
   padding: 2rem;
}

.edit-comment form .box{
   width: 100%;
   border-radius: .5rem;
   padding: 1.4rem;
   font-size: 1.8rem;
   color: var(--black);
   background-color: var(--light-bg);
   resize: none;
   height: 20rem;
}

.edit-comment form .flex{
   display: flex;
   gap: 1.5rem;
   justify-content: space-between;
   margin-top: .5rem;
}

.teachers .search-tutor{
   margin-bottom: 3rem;
   display: flex;
   align-items: center;
   gap: 1.5rem;
   padding: 1.5rem 2rem;
   background-color: var(--white);
   border-radius: .5rem;
}

.teachers .search-tutor input{
   width: 100%;
   font-size: 1.8rem;
   color: var(--black);
   background: none;
}

.teachers .search-tutor button{
   font-size: 2rem;
   cursor: pointer;
   color: var(--black);
   background: none;
}

.teachers .search-tutor button:hover{
   color: var(--main-color);
}

.teachers .box-container{
   display: grid;
   grid-template-columns: repeat(auto-fit, 35rem);
   gap: 1.5rem;
   align-items: flex-start;
   justify-content: center;
}

.teachers .box-container .box{
   border-radius: .5rem;
   padding: 2rem;
   background-color: var(--white);
}

.teachers .box-container .box .tutor{
   margin-bottom: 1rem;
   display: flex;
   align-items: center;
   gap: 1.5rem;
}

.teachers .box-container .box .tutor img{
   height: 5rem;
   width: 5rem;
   object-fit: cover;
   border-radius: 50%;
}

.teachers .box-container .box .tutor h3{
   color: var(--black);
   font-size: 2rem;
   margin-bottom: .2rem;
}

.teachers .box-container .box .tutor span{
   color: var(--main-color);
   font-size: 1.5rem;
}

.teachers .box-container .box p{
   padding-top: 1rem;
   font-size: 1.7rem;
   color: var(--light-color);
}

.teachers .box-container .box p span{
   color: var(--main-color);
}

.teachers .box-container .offer{
   text-align: center;
}

.teachers .box-container .offer h3{
   font-size: 2rem;
   color: var(--black);
}

.teachers .box-container .offer p{
   line-height: 2;
   padding-bottom: .5rem;
}

.tutor-profile .details{
   background-color: var(--white);
   border-radius: .5rem;
   padding: 2rem;
   text-align: center;
}

.tutor-profile .details .tutor{
   margin-bottom: 2rem;
}

.tutor-profile .details .tutor img{
   height: 10rem;
   width: 10rem;
   border-radius: 50%;
   object-fit: cover;
   margin-bottom: .5rem;
}

.tutor-profile .details .tutor h3{
   font-size: 2rem;
   color: var(--black);
   margin: .5rem 0;
}

.tutor-profile .details .tutor span{
   font-size: 1.5rem;
   color: var(--light-color);
}

.tutor-profile .details .flex{
   display: flex;
   gap: 1.5rem;
   align-items: center;
   flex-wrap: wrap;
}

.tutor-profile .details .flex p{
   flex: 1 1 25rem;
   border-radius: .5rem;
   background-color: var(--light-bg);
   padding: 1rem 3rem;
   font-size: 2rem;
   color: var(--light-color);
}

.tutor-profile .details .flex p span{
   color: var(--main-color);
}

.contact .row{
   display: flex;
   align-items: center;
   gap: 1.5rem;
   flex-wrap: wrap;
}

.contact .row .image{
   flex: 1 1 50rem;
}

.contact .row .image img{
   height: 50rem;
   width: 100%;
}

.contact .row form{
   flex:1 1 30rem;
   background-color: var(--white);
   border-radius: .5rem;
   padding: 2rem;
   text-align: center;
}

.contact .row form h3{
   font-size: 2.5rem;
   margin-bottom: 1rem;
   color: var(--black);
}

.contact .row form .box{
   width: 100%;
   margin: 1rem 0;
   border-radius: .5rem;
   background-color: var(--light-bg);
   padding: 1.4rem;
   color: var(--black);
   font-size: 1.8rem;
}

.contact .row form textarea{
   height: 20rem;
   resize: none;
}

.contact .box-container{
   margin-top: 3rem;
   display: flex;
   align-items: flex-start;
   gap: 1.5rem;
   flex-wrap: wrap;
}

.contact .box-container .box{
   flex: 1 1 30rem;
   border-radius: .5rem;
   background-color: var(--white);
   padding: 2rem;
   text-align: center;
}

.contact .box-container .box i{
   font-size: 3rem;
   color: var(--main-color);
   margin-bottom: 1rem;
}

.contact .box-container .box h3{
   margin: 1.5rem 0;
   font-size: 2rem;
   color: var(--black);
}

.contact .box-container .box a{
   display: block;
   font-size: 1.7rem;
   color: var(--light-color);
   line-height: 1.5;
   margin-top: .5rem;
}

.contact .box-container .box a:hover{
   text-decoration: underline;
   color: var(--main-color);
}

.profile .details{
   background-color: var(--white);
   border-radius: .5rem;
   padding: 2rem;
}

.profile .details .user{
   text-align: center;
   margin-bottom: 2rem;
}

.profile .details .user img{
   height: 10rem;
   width: 10rem;
   border-radius: 50%;
   object-fit: cover;
   margin-bottom: .5rem;
}

.profile .details .user h3{
   font-size: 2rem;
   margin: .5rem 0;
   color: var(--black);
}

.profile .details .user p{
   font-size: 1.7rem;
   color: var(--light-color);
}

.profile .details .box-container{
   display: flex;
   flex-wrap: wrap;
   align-items: flex-end;
   gap: 1.5rem;
}

.profile .details .box-container .box{
   background-color: var(--light-bg);
   border-radius: .5rem;
   padding: 2rem;
   flex: 1 1 30rem;
}

.profile .details .box-container .box .flex{
   display: flex;
   align-items: center;
   gap: 1.7rem;
   margin-bottom: 1rem;
}

.profile .details .box-container .box .flex i{
   height: 4.5rem;
   width: 4.5rem;
   border-radius: .5rem;
   background-color: var(--black);
   line-height: 4.4rem;
   font-size: 2rem;
   color: var(--white);
   text-align: center;
}

.profile .details .box-container .box .flex h3{
   font-size: 2rem;
   color: var(--main-color);
   margin-bottom: .2rem;
}

.profile .details .box-container .box .flex span{
   font-size: 1.5rem;
   color: var(--light-color);
}

.form-container{
   display: flex;
   align-items: center;
   justify-content: center;
   min-height:80vh;
}

.form-container form{
   background-color: var(--white);
   border-radius: .5rem;
   padding: 2rem;
}

.form-container .login{
   width: 50rem;
}

.form-container .register{
   width: 80rem;
}

.form-container form h3{
   text-align: center;
   font-size: 2.5rem;
   margin-bottom: 1rem;
   color: var(--black);
   text-transform: capitalize;
}

.form-container form p{
   padding-top: 1rem;
   font-size: 1.7rem;
   color: var(--light-color);
}

.form-container form p span{
   color: var(--red);
}

.form-container .link{
   padding-bottom: 1rem;
   text-align: center;
   font-size: 2rem;
}

.form-container .link a{
   color: var(--main-color);
}

.form-container .link a:hover{
   color: var(--black);
   text-decoration: underline;
}

.form-container form .box{
   width: 100%;
   border-radius: .5rem;
   margin: 1rem 0;
   font-size: 1.8rem;
   color: var(--black);
   padding: 1.4rem;
   background-color: var(--light-bg);
}

.form-container .flex{
   display: flex;
   gap: 2rem;
}

.form-container .flex .col{
   flex: 1 1 25rem;
}

.liked-videos .box-container{
   display: grid;
   grid-template-columns: repeat(auto-fit, 35rem);
   gap: 1.5rem;
   align-items: flex-start;
   justify-content: center;
   text-overflow: hidden;
}

.liked-videos .box-container .box{
   background-color: var(--white);
   border-radius: .5rem;
   padding: 2rem;
   overflow-x: hidden;
}

.liked-videos .box-container .box .tutor{
   margin-bottom:2rem;
   display: flex;
   align-items: center;
   gap: 1.5rem;
}

.liked-videos .box-container .box .tutor img{
   height: 5rem;
   width: 5rem;
   border-radius: 50%;
   object-fit: cover;
}

.liked-videos .box-container .box .tutor h3{
   font-size: 1.8rem;
   color: var(--black);
   margin-bottom: .2rem;
}

.liked-videos .box-container .box .tutor span{
   font-size: 1.5rem;
   color: var(--light-color);
}

.liked-videos .box-container .box .thumb{
   width: 100%;
   height: 20rem;
   object-fit: cover;
   border-radius: .5rem;
   margin-bottom: 1rem;
}

.liked-videos .box-container .box .title{
   font-size: 2rem;
   color: var(--black);
   padding: .5rem 0;
   text-overflow: ellipsis;
   overflow-x: hidden;
}















.footer{
   position: sticky;
   bottom: 0; right: 0; left: 0;
   background-color: var(--white);
   border-top: var(--border);
   padding:2.5rem 2rem;
   text-align: center;
   color: var(--black);
   font-size: 2rem;
   margin-top: 1.5rem;
   z-index: 1000;
   /* padding-bottom: 9.5rem; */
}

.footer span{
   color: var(--main-color);
}

/*#overlay {
   position: absolute;
    top: 81%;
    left: 0;
    height: 2%;
    width: 100%;
    background-color: black;
    z-index: 1;
    opacity: 0.9;
}*/


/* media queries  */

@media (max-width:1200px){

   body{
      padding-left: 0;
   }

   .side-bar{
      transition: .2s linear;
      left: -30rem;
   }

   .side-bar.active{
      left: 0;
      box-shadow: 0 0 0 100vw rgba(0,0,0,.7);
   }

   .side-bar .close-side-bar{
      display: block;
   }

}

@media (max-width:991px){
   
   html{
      font-size: 55%;
   }

}

@media (max-width:768px){

   #search-btn{
      display: inline-block;
   }

   .header .flex .search-form{
      position: absolute;
      top:99%; left: 0; right: 0;
      width: auto;
      border-top: var(--border);     
      border-bottom: var(--border);
      background-color: var(--white);
      clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);
      transition: .2s linear;
   }

   .header .flex .search-form.active{
      clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
   }

   .form-container .flex{
      display: flex;
      gap: 0;
      flex-flow: column;
   }

   .watch-video .video-details .video{
      height: 30rem;
   }

}

@media (max-width:450px){
   
   html{
      font-size: 50%;
   }

   .heading{
      font-size: 2rem;
   }

   .flex-btn{
      flex-flow: column;
      gap: 0;
   }

   .about .row .image img{
      height: auto;
   }

   .playlist .row .col .thumb{
      height: 20rem;
   }

   .contact .row .image img{
      height: auto;
   }

   .courses .box-container{
      grid-template-columns: 1fr;
   }

   .videos-container .box-container{
      grid-template-columns: 1fr;
   }

   .teachers .box-container{
      grid-template-columns: 1fr;
   }

   .watch-video .video-details .video{
      height: auto;
   }

}
   </style>

</head>
<body>

<?php include 'components/user_header.php'; ?>

<?php
   if(isset($_POST['edit_comment'])){
      $edit_id = $_POST['comment_id'];
      $edit_id = filter_var($edit_id, FILTER_SANITIZE_STRING);
      $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? LIMIT 1");
      $verify_comment->execute([$edit_id]);
      if($verify_comment->rowCount() > 0){
         $fetch_edit_comment = $verify_comment->fetch(PDO::FETCH_ASSOC);
?>
<section class="edit-comment">
   <h1 class="heading">Edit Comment</h1>
   <form action="" method="post">
      <input type="hidden" name="update_id" value="<?= $fetch_edit_comment['id']; ?>">
      <textarea name="update_box" class="box" maxlength="1000" required placeholder="Please enter your comment" cols="30" rows="10"><?= $fetch_edit_comment['comment']; ?></textarea>
      <div class="flex">
         <a href="watch_video.php?get_id=<?= $get_id; ?>" class="inline-btn">Cancel edit</a>
         <input type="submit" value="update now" name="update_now" class="inline-option-btn">
      </div>
   </form>
</section>
<?php
   }else{
      $message[] = 'Comment was not found!';
   }
}
?>

<!-- watch video section starts  -->

<section class="watch-video">

   <?php
      $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND status = ?");
      $select_content->execute([$get_id, 'active']);
      if($select_content->rowCount() > 0){
         while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){
            $content_id = $fetch_content['id'];

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
   <div class="video-details">
      
      <iframe id="myVideo" src="<?= $fetch_content['link']; ?>" class="video" poster="uploaded_files/<?= $fetch_content['thumb']; ?>"></iframe>

      <form action="" method="post">
         <input type="hidden" name="content_id" value="<?= $content_id; ?>">
         <?php
            if($verify_completion->rowCount() > 0){
         ?>
         <button type="submit" name="complete" class="inline-delete-btn" onclick="return confirm('Mark as Incomplete?');">Mark as Incomplete</button>
         <button onclick = "stopVideo(body)" class="inline-delete-btn"> Stop Video </button>
         <!--<button type="submit" name="certificate" class="inline-option-btn" onclick="return confirm('Generate your Certificate?');">Next Video</button>-->


            <?php
               if($verify_completion1->rowCount() == 1){
                  $select_content1 = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND status = ?");
                  $select_content1->execute(['e7zSxaOfT6mpNnfkscIu', 'Active']);
                  $fetch_content1 = $select_content1->fetch(PDO::FETCH_ASSOC);
            ?>
            <a type="btn" href="#" class="inline-btn">Complete all Modules to generate your Certificate</a>
            <a href="watch_video_copy.php?get_id=<?= $fetch_content1['id']; ?>" class="inline-btn"><i class="fas fa-eye"></i> Next</a>
            <?php
            }else if($verify_completion1->rowCount() == 2){
                  $select_content2 = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND status = ?");
                  $select_content2->execute(['9Hdt7hvYzKmO6dkopjkQ', 'Active']);
                  $fetch_content2 = $select_content2->fetch(PDO::FETCH_ASSOC);
            ?>
            
            <a type="btn" href="#" class="inline-btn">Complete all Modules to generate your Certificate</a>
            <a href="watch_video_copy.php?get_id=<?= $fetch_content2['id']; ?>" class="inline-btn"><i class="fas fa-eye"></i> Next</a>
            <?php
            }else if($verify_completion1->rowCount() == 3){
                  $select_content2 = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND status = ?");
                  $select_content2->execute(['vpVcfCNquH543nEGhSrR', 'Active']);
                  $fetch_content2 = $select_content2->fetch(PDO::FETCH_ASSOC);
            ?>
            
            <a type="btn" href="#" class="inline-btn">Complete all Modules to generate your Certificate</a>
            <a href="watch_video_copy.php?get_id=<?= $fetch_content2['id']; ?>" class="inline-btn"><i class="fas fa-eye"></i> Next</a>
            <?php
            }else{
            ?>
         
            <a type="btn" href="#" class="inline-btn">Complete all Modules to generate your Certificate</a>
            <?php
               }
            ?>
         
         <?php

         }else{
         ?>
         <button type="submit" name="complete" class="inline-option-btn" onclick="return confirm('Mark as complete?');">Mark as Complete</button>
         <button onclick = "stopVideo(body)" class="inline-delete-btn"> Stop Video </button>
         
         <?php
            }
         ?>
      </form>
      <h3 class="title"><?= $fetch_content['title']; ?></h3>
      <div class="info">
         <p><i class="fas fa-calendar"></i><span><?= $fetch_content['date']; ?></span></p>
         <p><i class="fas fa-heart"></i><span><?= $total_likes; ?> likes</span></p>
      </div>
      <div class="tutor">
         <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
         <div>
            <h3><?= $fetch_tutor['name']; ?></h3>
            <span><?= $fetch_tutor['profession']; ?></span>
         </div>
      </div>
      <form action="" method="post" class="flex">
         <input type="hidden" name="content_id" value="<?= $content_id; ?>">
         <a href="playlist.php?get_id=<?= $fetch_content['playlist_id']; ?>" class="inline-btn"><i class="fas fa-eye"></i> View playlist</a>
         <?php
            if($verify_likes->rowCount() > 0){
         ?>
         <button type="submit" name="like_content"><i class="fas fa-heart"></i><span>Liked</span></button>
         <?php
         }else{
         ?>
         <button type="submit" name="like_content"><i class="far fa-heart"></i><span>Like</span></button>
         <?php
            }
         ?>
      </form>
      <div class="description"><p><?= $fetch_content['description']; ?></p></div>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">No videos added yet!</p>';
      }
   ?>

</section>

<!-- watch video section ends -->

<!-- comments section starts  -->

<section class="comments">

   <h1 class="heading">Add a comment</h1>

   <form action="" method="post" class="add-comment">
      <input type="hidden" name="content_id" value="<?= $get_id; ?>">
      <textarea name="comment_box" required placeholder="Write your comment..." maxlength="5000" cols="30" rows="10"></textarea>
      <input type="submit" value="add comment" name="add_comment" class="inline-btn">
   </form>

   <h1 class="heading">User Comments</h1>

   
   <div class="show-comments">
      <?php
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE content_id = ?");
         $select_comments->execute([$get_id]);
         if($select_comments->rowCount() > 0){
            while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){   
               $select_commentor = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
               $select_commentor->execute([$fetch_comment['user_id']]);
               $fetch_commentor = $select_commentor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box" style="<?php if($fetch_comment['user_id'] == $user_id){echo 'order:-1;';} ?>">
         <div class="user">
            <img src="uploaded_files/<?= $fetch_commentor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_commentor['name']; ?></h3>
               <span><?= $fetch_comment['date']; ?></span>
            </div>
         </div>
         <p class="text"><?= $fetch_comment['comment']; ?></p>
         <?php
            if($fetch_comment['user_id'] == $user_id){ 
         ?>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
            <button type="submit" name="edit_comment" class="inline-option-btn">Edit Comment</button>
            <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm('Delete this comment?');">Delete Comment</button>
         </form>
         <?php
         }
         ?>
      </div>
      <?php
       }
      }else{
         echo '<p class="empty">No comments added yet!</p>';
      }
      ?>
      </div>
   
</section>

<!-- comments section ends -->






<script>
      // to stop the video
      function stopVideo(element) {
         // getting every iframe from the body
         var iframes = element.querySelectorAll('iframe');
         // reinitializing the values of the src attribute of every iframe to stop the YouTube video.
         for (let i = 0; i < iframes.length; i++) {
            if (iframes[i] !== null) {
               var temp = iframes[i].src;
               iframes[i].src = temp;
            }
         }
      };
</script>

<?php //include 'components/footer.php'; //?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>