<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   <section class="flex">
      <?php
         $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
         $select_profile->execute([$user_id]);
         if($select_profile->rowCount() > 0){
         $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
      ?>  
      <div class="logo-image">
      <a href="home.php"><img src="./images/hrmbsi.png" style="width: 150px; height: 50px;" class="img-fluid"></a>
      </div>
      <!--<a href="home.php" class="logo">HRMBSi</a>-->

      <form action="search_course.php" method="post" class="search-form">
         <input type="text" name="search_course" placeholder="search courses..." required maxlength="100">
         <button type="submit" class="fas fa-search" name="search_course_btn"></button>
      </form>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="toggle-btn" class="fas fa-sun"></div>
      </div>

      <?php
            }else{
         ?>

      <div class="logo-image">
      <a href="home.php"><img src="./images/hrmbsi.png" style="width: 150px; height: 50px;" class="img-fluid"></a>
      </div>
      <!--<a href="home.php" class="logo">HRMBSi</a>-->

      <!--<form action="search_course.php" method="post" class="search-form">
         <input type="text" name="search_course" placeholder="search courses..." required maxlength="100">
         <button type="submit" class="fas fa-search" name="search_course_btn"></button>
      </form>-->

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="toggle-btn" class="fas fa-sun"></div>
      </div>


      <?php
         }
      ?>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if($select_profile->rowCount() > 0){
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <h3><?= $fetch_profile['name']; ?></h3>
         <span>student</span>
         <a href="profile.php" class="btn"><i class="fas fa-eye"></i> View Profile</a>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>
         <a href="components/user_logout.php" onclick="return confirm('Logout from your account?');" class="delete-btn">Logout</a>
         <?php
            }else{
         ?>
         <h3>Login or Register</h3>
          <div class="flex-btn">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>
         <?php
            }
         ?>
      </div>

   </section>

</header>

<!-- header section ends -->

<!-- side bar section starts  -->

<div class="side-bar">

   <div class="close-side-bar">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if($select_profile->rowCount() > 0){
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <h3><?= $fetch_profile['name']; ?></h3>
         <span>student</span>
         <a href="profile.php" class="btn"><i class="fas fa-eye"></i> View Profile</a>
   </div>
         <nav class="navbar">
            <a href="home.php"><i class="fas fa-home"></i><span>Home</span></a>
            <a href="about.php"><i class="fas fa-circle-info"></i><span>About Us</span></a>   
            <a href="FAQs.php"><i class="fas fa-question-circle"></i><span>FAQs</span></a>         
            <!--<a href="http://yanverabrenica.x10.mx/wp/hrmbsi-events/?post=2441"><i class="fas fa-calendar"></i><span>OLSP Events</span></a>-->
            <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
            <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span>Creators</span></a>
            <a href="contact.php"><i class="fas fa-headset"></i><span>Feedback</span></a>
            <a href="user_messages.php"><i class="fas fa-envelope"></i><span>Messages</span></a>
         </nav>
         <?php
            }else{
         ?>
        
         
        <div class="logo-image">
      <a href="home.php"><img src="./images/hrmbsi icon.png" style="width: 150px; height: 150px;" class="img-fluid"></a>
      </div>
          <!--<div class="flex-btn" style="padding-top: .5rem;">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>-->
   </div>

         <nav class="navbar">
            <a href="home.php"><i class="fas fa-home"></i><span>Home</span></a>
            <a href="about.php"><i class="fas fa-circle-info"></i><span>About Us</span></a>
            <a href="FAQs.php"><i class="fas fa-question-circle"></i><span>FAQs</span></a>
            <a href="http://yanverabrenica.x10.mx/wp/hrmbsi-events/?post=2441"><i class="fas fa-calendar"></i><span>OLSP Events</span></a>
            <a href="zoom_links.php"><i class="fas fa-link"></i><span>Zoom Seminars</span></a>
            <!--<a href="courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>-->
            <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span>Creators</span></a>
            <a href="contact.php"><i class="fas fa-headset"></i><span>Contact Us</span></a>
         </nav>
         <?php
            }
         ?>
</div>

<!-- side bar section ends -->
