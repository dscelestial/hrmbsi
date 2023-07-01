<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('location:login.php');
}

require "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

if(isset($_POST['submit'])){

   $phpmailer = new PHPMailer(true);

   $subject = "HRMBSI Reply";
   $gmail = "inquiries@hrmbsi.com.ph";
   $hname = "HRMBSI";
   $hnumber = "8-663-0077";

   $phpmailer->isSMTP();
   $phpmailer->SMTPAuth = true;
    
   $phpmailer->Host = "sandbox.smtp.mailtrap.io";
   $phpmailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
   $phpmailer->Port = 2525;

   $phpmailer->Username = '2a7c1506066d65';
   $phpmailer->Password = '15b87228e2aba6';

   $id = $_POST['id'];
   $id = filter_var($id, FILTER_SANITIZE_STRING);
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $msg = $_POST['msg'];
   $msg = filter_var($msg, FILTER_SANITIZE_STRING);
   $timeactivity = date("F j, Y, g:i a");
   $phpmailer->isHTML(true);
   
         $insert_user = $conn->prepare("INSERT INTO `reply`(id, user_id, name, email, message, date) VALUES(?,?,?,?,?,?)");
         $insert_user->execute([$id, $user_id, $name, $email, $msg, $timeactivity]);

         $phpmailer->setFrom($email,$name);
         $phpmailer->addAddress($gmail);
      
         $phpmailer->Subject = $subject;
         $phpmailer->Body = 
         '<table style="max-width: 600px; margin: 0 auto; padding: 20px;">'
               .'<tr>'
                  .'<div class="tempp">'
                  .'<td style="background-color: #f1f1f1; padding: 20px; text-align: center">'
                     .'<img src="https://dl.dropboxusercontent.com/scl/fi/7yqtrq36ov3o5pd6y362b/resHRMBSi-LOGO_embossed.png?raw=1&rlkey=ifaw5w3bihozd5n9zsd8tbbsr" alt="Your Logo" style="max-width: 15rem;">'
                  .'</td>'
                  .'</div>'
               .'</tr>'
               .'<tr>'
                  .'<td style="padding: 20px; background-color: #ffffff;">'
                  .'<h1 style="font-size: 24px; margin-bottom: 20px;">Reply</h1>'
                  .'<p style="font-size: 16px; line-height: 1.5;">You have received a reply:</p>'
                  .'<p style="font-size: 16px; line-height: 1.5 text-align: center"><b>From:</b> ' . $name . '</p>'
                  .'<p style="font-size: 16px; line-height: 1.5;"><b>Message: </b>' . $msg . '</p>'
                  .'<p style="font-size: 16px; line-height: 1.5;">This is an automated message. Best regards,</p>'
                  .'<p style="font-size: 16px; line-height: 1.5;">HRMBSi Interns</p>'
                  .'</td>'
               .'</tr>'
               .'<tr>'
                  .'<td style="background-color: #f1f1f1; padding: 20px; text-align: center;">'
                  .'<p style="font-size: 14px; color: #888888;"> Copyright 2023 HRMBSi - All Rights Reserved.</p>'
                  .'</td>'
               .'</tr>'
            .'</table>';
         
      
         $phpmailer->send();
         $message[] = 'Message sent successfully!';

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>HRMBSi | Messages</title>
   <link rel="icon" href="images/hrmbsi icon.png">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/style.css">

   <style>
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;500;600&display=swap');

:root{
   --main-color:#d6b43b;
   --red:#e74c3c;
   --oragen:#265828;
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
   /* padding-bottom: 7rem; */
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
   background-color: var(--oragen);
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
   margin-bottom: 2rem;
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

.header .flex .profile .flex-btn{
   margin-top: .5rem;
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

.side-bar .profile .flex-btn{
   margin-top: .5rem;
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

.form-container{
   display: flex;
   align-items: center;
   justify-content: center;
   min-height: 100vh;
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

.dashboard .box-container{
   display: grid;
   grid-template-columns: repeat(auto-fit, minmax(30rem, 1fr));
   align-items: flex-start;
   justify-content: center;
   gap: 1.5rem;
}

.dashboard .box-container .box{
   padding: 2rem;
   border-radius: .5rem;
   background-color: var(--white);
   text-align: center;
}

.dashboard .box-container .box h3{
   margin-bottom: .5rem;
   font-size: 2.5rem;
   color: var(--black);
   margin-bottom: 1.5rem;
}

.dashboard .box-container .box p{
   font-size: 2rem;
   color: var(--light-color);
   padding: 1rem 1.5rem;
   border-radius: .5rem;
   background-color: var(--light-bg);
   margin: 1rem 0;
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
   font-size: 2.5rem;
   color: var(--black);
   margin: .5rem 0;
}

.tutor-profile .details .tutor span{
   font-size: 1.8rem;
   color: var(--light-color);
   display: block;
}

.tutor-profile .details .flex{
   display: flex;
   gap: 1.5rem;
   align-items: center;
   flex-wrap: wrap;
}

.tutor-profile .details .flex .box{
   flex: 1 1 26rem;
   border-radius: .5rem;
   background-color: var(--light-bg);
   padding: 2rem;
}

.tutor-profile .details .flex span{
   color: var(--main-color);
   display: block;
   margin-bottom: .5rem;
   font-size: 2.5rem;
}

.tutor-profile .details .flex .box p{
   font-size: 2rem;
   color: var(--black);
   padding: .5rem 0;
}

.playlist-form form{
   background-color: var(--white);
   border-radius: .5rem;
   padding: 2rem;
   padding-top: 1rem;
   max-width: 50rem;
   margin: 0 auto;
}

.playlist-form form p{
   font-size: 1.7rem;
   color: var(--light-color);
   padding-top: 1rem;
}

.playlist-form form p span{
   color: var(--red);
}

.playlist-form form .box{
   margin: 1rem 0;
   border-radius: .5rem;
   padding: 1.4rem;
   font-size: 1.8rem;
   color: var(--black);
   background: var(--light-bg);
   width: 100%;
}

.playlist-form form textarea{
   height: 20rem;
   resize: none;
}

.playlist-form form .thumb{
   height: 22rem;
   margin-top: 1rem;
   position: relative;
}

.playlist-form form .thumb img{
   height: 100%;
   width: 100%;
   object-fit: cover;
   border-radius: .5rem;
}

.playlist-form form .thumb span{
   background-color: rgba(0,0,0,.3);
   color: #fff;
   border-radius: .5rem;
   position: absolute;
   top: 1rem; left: 1rem;
   padding: .5rem 1.5rem;
   font-size: 2rem;
}

.playlists .box-container{
   display: grid;
   grid-template-columns: repeat(auto-fit, 35rem);
   align-items: flex-start;
   justify-content: center;
   gap: 1.5rem;
}

.playlists .box-container .box{
   background-color: var(--white);
   border-radius: .5rem;
   padding: 2rem;
   overflow-x:hidden;
}

.playlists .box-container .box .thumb{
   height: 20rem;
   position: relative;
   margin: 1.5rem 0;
}

.playlists .box-container .box .flex{
   display: flex;
   align-items: center;
   gap: 1.5rem;
   justify-content: space-between;
}

.playlists .box-container .box .flex i{
   font-size: 1.5rem;
   color: var(--main-color);
   margin-right:.7rem;
}

.playlists .box-container .box .flex span{
   color: var(--light-color);
   font-size: 1.7rem;
}

.playlists .box-container .box .thumb img{
   height: 100%;
   width: 100%;
   object-fit: cover;
   border-radius: .5rem;
}

.playlists .box-container .box .thumb span{
   background-color: rgba(0,0,0,.3);
   color: #fff;
   border-radius: .5rem;
   position: absolute;
   top: 1rem; left: 1rem;
   padding: .5rem 1.5rem;
   font-size: 2rem;
}

.playlists .box-container .box .title{
   font-size: 2rem;
   color: var(--black);
   margin-bottom: 1rem;
   text-overflow: ellipsis;
   white-space: nowrap;
   overflow-x:hidden;
}

.playlists .box-container .box .description{
   line-height: 2;
   font-size: 1.7rem;
   color: var(--light-color);
}

.playlists .box-container .box .description::after{
   content: '...';
}

.video-form form{
   max-width: 50rem;
   margin: 0 auto;
   background-color: var(--white);
   border-radius: .5rem;
   padding: 2rem;
   padding-top: 1rem;
}

.video-form form p{
   padding-top: 1rem;
   font-size: 1.7rem;
   color: var(--light-color);
}

.video-form form p span{
   color:var(--red);
}

.video-form form .box{
   width: 100%;
   border-radius: .5rem;
   background-color: var(--light-bg);
   padding: 1.4rem;
   font-size: 1.8rem;
   color: var(--black);
   margin: 1rem 0;
}

.video-form form textarea{
   height: 20rem;
   resize: none;
}

.video-form form img{
   width: 100%;
   height: 20rem;
   border-radius: .5rem;
   object-fit: contain;
   margin: .5rem 0;
}

.video-form form video{
   background-color: #000;
   width: 100%;
   height: 20rem;
   border-radius: .5rem;
   margin: .5rem 0;
}

.playlist-details .row{
   display: flex;
   gap: 2.5rem;
   flex-wrap: wrap;
   align-items: flex-start;
   background-color: var(--white);
   border-radius: .5rem;
   padding: 2rem;
}

.playlist-details .row .thumb{
   flex: 1 1 40rem;
   height: 30rem;
   position: relative;
}

.playlist-details .row .thumb img{
   height: 100%;
   width: 100%;
   border-radius: .5rem;
   object-fit: cover;
}

.playlist-details .row .thumb span{
   background-color: rgba(0,0,0,.3);
   color: #fff;
   border-radius: .5rem;
   position: absolute;
   top: 1rem; left: 1rem;
   padding: .5rem 1.5rem;
   font-size: 2rem;
}

.playlist-details .row .details{
   flex: 1 1 40rem;
}

.playlist-details .row .details .date{
   font-size: 1.5rem;
   margin: 1rem 0;
}

.playlist-details .row .details .date i{
   color: var(--main-color);
   margin-right: 1rem;
}

.playlist-details .row .details .date span{
   color: var(--light-color);
}

.playlist-details .row .details .title{
   font-size: 2rem;
   color: var(--black);
   padding-bottom: .5rem;
}

.playlist-details .row .details .description{
   padding: .5rem 0;
   font-size: 1.7rem;
   color: var(--light-color);
   line-height: 2;
   white-space: pre-line;
}

.contents .box-container{
   display: grid;
   grid-template-columns: repeat(auto-fit, 35rem);
   align-items: flex-start;
   justify-content: center;
   gap: 1.5rem;
}

.contents .box-container .box{
   border-radius: .5rem;
   background-color: var(--white);
   padding: 2rem;
   overflow-x: hidden;
}

.contents .box-container .box .thumb{
   height: 20rem;
   width: 100%;
   border-radius: .5rem;
   object-fit: cover;
   margin: 1rem 0;
}

.contents .box-container .box .flex{
   display: flex;
   align-items: center;
   justify-content: space-between;
   gap: 1.5rem;
   margin-bottom: .5rem;
}

.contents .box-container .box .flex i{
   font-size: 1.5rem;
   color: var(--main-color);
   margin-right: 1rem;
}

.contents .box-container .box .flex span{
   color: var(--light-color);
   font-size: 1.7rem;
}

.contents .box-container .box .title{
   font-size: 2rem;
   color: var(--black);
   line-height: 1.5;
   text-overflow: ellipsis;
   overflow-x: hidden;
}

.view-content .container{
   background-color: var(--white);
   border-radius: .5rem;
   padding: 2rem;
}

.view-content .container .video{
   width: 100%;
   object-fit: contain;
   margin-bottom: 1rem;
   background: #000;
   height: 50rem;
}

.view-content .container .date{
   padding:1rem 0;
   font-size: 1.7rem;
}

.view-content .container .date i{
   margin-right: 1rem;
   color: var(--main-color);
}

.view-content .container .date span{
   color: var(--light-color);
}

.view-content .container .title{
   font-size: 2rem;
   color: var(--black);
   padding: .5rem 0;
}

.view-content .container .flex{
   display: flex;
   align-items: center;
   justify-content: space-between;
   gap: 1.5rem;
   font-size: 1.8rem;
   border-top: var(--border);
   padding-top: 1.5rem;
   padding-bottom: 1rem;
   margin-top: 1rem;
}

.view-content .container .flex div{
   background-color: var(--light-bg);
   border-radius: .5rem;
   padding: .5rem 1rem;
}

.view-content .container .flex i{
   margin-right: 1rem;
   color: var(--main-color);
}

.view-content .container .flex span{
   color: var(--light-color);
}

.view-content .container .description{
   padding: .5rem 0;
   line-height: 1.7;
   font-size: 1.7rem;
   color: var(--light-color);
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














.footer{
   position: sticky;
   bottom: 0; right: 0; left: 0;
   background-color: var(--white);
   border-top: var(--border);
   padding:2.5rem 2rem;
   text-align: center;
   color: var(--black);
   font-size: 2rem;
   margin-top: 2rem;
   z-index: 1000;
}

.footer span{
   color: var(--main-color);
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

   .view-content .container .video{
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

   .playlists .box-container{
      grid-template-columns: 1fr;
   }

   .view-content .container .video{
      height: auto;
   }

}
</style>


</head>
<body>

<?php include 'components/user_header.php'; ?>

<?php
   if(isset($_POST['reply'])){
      $reply_id = $_POST['id'];
      $reply_id = filter_var($reply_id, FILTER_SANITIZE_STRING);
      $verify_contact = $conn->prepare("SELECT * FROM `contact` WHERE id = ? LIMIT 1");
      $verify_contact->execute([$reply_id]);
      if($verify_contact->rowCount() > 0){
         $fetch_reply_contact = $verify_contact->fetch(PDO::FETCH_ASSOC);
      
      $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
      $select_user->execute([$user_id]);
      $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
         $id = $fetch_user['id'];
         $name = $fetch_user['name'];
         $email = $fetch_user['email'];
?>
<section class="edit-comment">
   <h1 class="heading">Reply</h1>
   <form action="" method="post">
      <!--<p>User ID <span>*</span></p>-->
      <input type="text" name="id" value="<?= $fetch_reply_contact['id']; ?>" hidden>
      <!--<p>Name <span>*</span></p>-->
      <input type="text" name="name" value="<?= $fetch_user['name']; ?>" required class="box" readonly hidden>
      <!--<p>Email <span>*</span></p>-->
      <input type="text" name="email" value="<?= $fetch_user['email']; ?>" required class="box" readonly hidden>
      <p>Message <span>*</span></p>
      <textarea readonly class="box" cols="30" rows="10"><?= $fetch_reply_contact['message']; ?></textarea>
      <p>Reply <span>*</span></p>
      <textarea name="msg" class="box" maxlength="5000" required placeholder="Please enter your Reply" cols="30" rows="10"></textarea>
      <div class="flex">
         <a href="user_messages.php" class="inline-btn">Cancel</a>
         <input type="submit" value="Send Reply" name="submit" class="inline-option-btn">
      </div>
   </form>
</section>
<?php
   }else{
      $message[] = 'Feedback was not found!';
   }
}
?>

<section class="comments">

   <h1 class="heading">User Message & Concerns</h1>

   
   <div class="show-comments">
      <?php
         $select_messages = $conn->prepare("SELECT * FROM `contact` ORDER BY DATE DESC");
         $select_messages->execute();
         if($select_messages->rowCount() > 0){
            while($fetch_messages = $select_messages->fetch(PDO::FETCH_ASSOC)){
                $id = $fetch_messages['id'];
                $user_id = $fetch_messages['user_id'];
                $name = $fetch_messages['name'];
                $email = $fetch_messages['email'];
                $number = $fetch_messages['number'];
                $message = $fetch_messages['message'];
                //$file = $fetch_messages['file'];
                $date = $fetch_messages['date'];

               //  if (!empty($fetch_messages['file'])) { 
               //    $fileattachment = '<a href="downloadfile1.php?file=' .  $fetch_messages['file'] . '">Download Evaluation</a>';
               // }else{
               //    $fileattachment = '<p style="color:red; pointer-events: none;">No File Attached</p>';
               // }
      ?>
      <div class="box" style="<?php if($fetch_messages['name'] == $name){echo 'order:-1;';} ?>">
         <div class="content"><span><?= $fetch_messages['name']; ?></span><span> - <?= $fetch_messages['email']; ?>: </span><span style="font-size: 12px;"><?= $fetch_messages['date']; ?></span></div>
         <p class="text"><?= $fetch_messages['message']; ?></p>
         <!--<p class="text"><?= $fileattachment; ?></p>-->
         <form action="" method="post">
            <input type="hidden" name="id" value="<?= $fetch_messages['id']; ?>">
            <button type="submit" name="reply" class="inline-option-btn">Reply</button>
         </form>
         <br>
         <?php
         $select_replies = $conn->prepare("SELECT * FROM `reply` WHERE id = '$id' ORDER BY id DESC");
         $select_replies->execute();
         if($select_replies->rowCount() > 0){
            while($fetch_replies = $select_replies->fetch(PDO::FETCH_ASSOC)){
                $id = $fetch_replies['id'];
                $user_id = $fetch_replies['user_id'];
                $name = $fetch_replies['name'];
                $email = $fetch_replies['email'];
                $message = $fetch_replies['message'];
                $date = $fetch_replies['date'];
      ?>
      <div class="box" style="<?php if($fetch_replies['name'] == $name){echo 'order:-1;';} ?>" >
         <div class="content"><span><?= $fetch_replies['name']; ?></span><span> - <?= $fetch_replies['email']; ?>: </span><span style="font-size: 12px;"><?= $fetch_replies['date']; ?></span></div>
         <p class="text" style=" width: 50%;"><?= $fetch_replies['message']; ?></p>
         <!--<form action="" method="post">
            <input type="hidden" name="id" value="<?= $fetch_comment['id']; ?>">
            <button type="submit" name="reply" class="inline-option-btn">Reply</button>
         </form>-->
      </div>
      <?php
         }
        }else{

        }
      ?>
         <!--<form action="" method="post">
            <input type="hidden" name="id" value="<?= $fetch_messages['id']; ?>">
            <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm('Delete this comment?');">Delete Message</button>
         </form>-->
      </div>
      <?php
       }
      }else{
         echo '<p class="empty">No messages or concerns added yet!</p>';
      }
      ?>
      </div>
   
</section>














<?php 
  $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
  $select_profile->execute([$user_id]);
  if($select_profile->rowCount() > 0){
  $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
  
  //include 'components/footer.php'; 
?>
  
<?php } else {
    include 'components/footer.php'; 
} ?>

<script src="js/admin_script.js"></script>

</body>
</html>