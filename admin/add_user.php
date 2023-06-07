<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

require "C:xampp/htdocs/hrmbsi/vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

if(isset($_POST['submit'])){

   $phpmailer = new PHPMailer(true);

   $phpmailer->isSMTP();
   $phpmailer->SMTPAuth = true;
   
   $phpmailer->Host = "sandbox.smtp.mailtrap.io";
   $phpmailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
   $phpmailer->Port = 2525;

   $phpmailer->Username = '313f38c4bad511';
   $phpmailer->Password = '2c18d304bdd4cf';

   $subject = "HRMBSI DLP";
   $gmail = "inquiries@hrmbsi.com.ph";
   $hname = "HRMBSI";
   $hnumber = "8-663-0077";

   $id = unique_id();
   $event_title = $_POST['event_title'];
   $event_title = filter_var($event_title, FILTER_SANITIZE_STRING);
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $organization = $_POST['organization'];
   $organization = filter_var($organization, FILTER_SANITIZE_STRING);
   $address = $_POST['address'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $contact = $_POST['contact'];
   $contact = filter_var($contact, FILTER_SANITIZE_STRING);
   $attendees = $_POST['attendees'];
   $attendees = filter_var($attendees, FILTER_SANITIZE_STRING);
   $source = $_POST['source'];
   $source = filter_var($source, FILTER_SANITIZE_STRING);
   $sector = $_POST['sector'];
   $sector = filter_var($sector, FILTER_SANITIZE_STRING);
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_files/'.$rename;

   $msg = "Your account has been created. Kindly wait for the admin to activate your account. For the payment,  You can pay through this accounts GCASH: 09279941026 BPO: 8372-92381-2304
   Here is the list of your provided details - Name: $name, Organization: $organization, Address: $address, Email: $email, Attendees: $attendees
   
   Contact us for more details on our number $hnumber or email us at $gmail
      
   Regards, $hname.";

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);
   
   if($select_user->rowCount() > 0){
      $message[] = 'Email already taken!';
   }else{
      if($pass != $cpass){
         $message[] = 'Confirm passowrd not matched!';
      }else{
         $insert_user = $conn->prepare("INSERT INTO `users`(id, event_title, name, organization, address, email, contact, password, attendees, source, sector, image, status) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
         $insert_user->execute([$id, $event_title, $name, $organization, $address, $email, $contact, $cpass, $attendees, $source, $sector, $rename, $status]);
         move_uploaded_file($image_tmp_name, $image_folder);
         
         $verify_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ? LIMIT 1");
         $verify_user->execute([$email, $pass]);
         $row = $verify_user->fetch(PDO::FETCH_ASSOC);
         
         if($verify_user->rowCount() > 0){
            //setcookie('user_id', $row['id'], time() + 60*60*24*30, '/');//
            $phpmailer->setFrom($gmail,$hname);
            $phpmailer->addAddress($email, $name);

            $phpmailer->Subject = $subject;
            $phpmailer->Body = $msg;

            $phpmailer->send();
            $message[] = 'User added!';
            //header('location:home.php');//
         }
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>HRMBSi | Create User</title>
   <link rel="icon" href="../images/hrmbsi icon.png">

   <!-- password toggle  -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

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

<?php include '../components/admin_header.php'; ?>

<section class="form-container">

   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3>Create User Account</h3>
      <div class="flex">
         <div class="col">
         <p>Event Title <span>*</span></p>
            	<select class="box" name="event_title" required>
                <option value="" disabled selected>-- select event title</option>
                <?php 
                $zoom = $conn->query("SELECT * FROM `zoom`");
                while ($rowzoom = $zoom->fetch()) {
                $id = $rowzoom['id'];
                $title = $rowzoom['title'];
                $link = $rowzoom['link'];
                $password = $rowzoom['password'];
                $start = $rowzoom['start']; 
                $end = $rowzoom['end']; 
                $status = $rowzoom['status'];
                ?>
                <option value="<?php echo $rowzoom["title"];
                // The value we usually set is the primary key
                ?>">
                <?php echo $rowzoom["title"];
                // To show the category name to the user
                ?>
                </option>
                <?php 
                }
                 // While loop must be terminated
                ?>
            	</select>
            <p>Name <span>*</span></p>
            <input type="text" name="name" placeholder="Enter your name" required class="box">
            <p>Organization <span>*</span></p>
            <input type="text" name="organization" placeholder="Enter your company" required class="box">
            <p>Address <span>*</span></p>
            <input type="text" name="address" placeholder="Enter your address"required class="box">
            <p>How did you hear about us? <span>*</span></p>
            <input type="checkbox" id="source" name="source" value="Colleague">
            <label for="source" style="font-size: 15px;"> Colleague</label><br>
            <input type="checkbox" id="source" name="source" value="email">
            <label for="source" style="font-size: 15px;"> Email</label><br>
            <input type="checkbox" id="source" name="source" value="Facebook">
            <label for="source" style="font-size: 15px;"> Facebook</label><br>
            <input type="checkbox" id="source" name="source" value="LinkedIn">
            <label for="source" style="font-size: 15px;"> LinkedIn</label><br>
            <input type="checkbox" id="source" name="source" value="Twitter">
            <label for="source" style="font-size: 15px;"> Twitter</label><br>
            <input type="checkbox" id="source" name="source" value="Website">
            <label for="source" style="font-size: 15px;"> Website</label><br>
            <input type="checkbox" id="source" name="source" value="Print & Media">
            <label for="source" style="font-size: 15px;"> Print Media</label><br>
            <input type="checkbox" id="source" name="source" value="Other">
            <label for="source" style="font-size: 15px;"> Other</label><br>
         </div>
         <div class="col">
            <p>Email <span>*</span></p>
            <input type="email" name="email" placeholder="Enter your email" required class="box">
            <p>Password <span>*</span></p>
            <input type="password" name="pass" id="pass" placeholder="Enter your password" minlength="8" maxlength="12" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Password must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required class="box">
            <p>Confirm Password <span>*</span></p>
            <input type="password" name="cpass" placeholder="Enter your password" minlength="8" maxlength="12" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Password must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required class="box">
            <p>Number of Attendees <span>*</span></p>
            <input type="number" name="attendees" placeholder="Enter number of attendees" maxlength="20" required class="box">
            <p>Which Industry Sector do you belong? <span>*</span></p>
            <input type="checkbox" id="sector" name="sector" value="Agriculture">
            <label for="sector" style="font-size: 15px;"> Agriculture</label><br>
            <input type="checkbox" id="sector" name="sector" value="Construction">
            <label for="sector" style="font-size: 15px;"> Construction</label><br>
            <input type="checkbox" id="sector" name="sector" value="Food & Beverage">
            <label for="sector" style="font-size: 15px;"> Food & Beverage</label><br>
            <input type="checkbox" id="sector" name="sector" value="IT/BPO">
            <label for="sector" style="font-size: 15px;"> IT/BPO</label><br>
            <input type="checkbox" id="sector" name="sector" value="Manufacturing">
            <label for="sector" style="font-size: 15px;"> Manufacturing</label><br>
            <input type="checkbox" id="sector" name="sector" value="Sales & Marketing">
            <label for="sector" style="font-size: 15px;"> Sales & Marketing</label><br>
            <input type="checkbox" id="sector" name="sector" value="Services">
            <label for="sector" style="font-size: 15px;"> Services</label><br>
            <input type="checkbox" id="sector" name="sector" value="Other">
            <label for="sector" style="font-size: 15px;"> Other</label><br>
         </div>
      </div>
      <p>Contact Number <span>*</span></p>
            <input type="text" name="contact" placeholder="Enter your contact number" minlength="11" maxlength="11" title="Contact number must contain 11 characters" required class="box">
      <p>Select Photo <span>*</span></p>
      <input type="file" name="image" accept="image/*" required class="box">
      <p hidden>Status <span>*</span></p>
            <select name="status" class="box" hidden required>
               <!--<option value="" disabled selected>-- select user status</option>
               <option value="Active">Active</option>-->
               <option value="Inactive">Inactive</option>
            </select>
      Show Password <i class="bi bi-eye-slash" id="togglePassword" style="font-size: 15px;"></i>
      <input type="submit" name="submit" value="Add User" class="btn">
   </form>

</section>










<script>
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#pass");

        togglePassword.addEventListener("click", function () {
            // toggle the type attribute
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            
            // toggle the icon
            this.classList.toggle("bi-eye");
        });

        // prevent form submit
        //const form = document.querySelector("form");
        //form.addEventListener('submit', function (e) {
            //e.preventDefault();
        //});
</script>

<?php //include '../components/footer.php'; //?>

<!-- custom js file link  -->
<script src="../js/script.js"></script>
   
</body>
</html>