<div class="col-md-3">
          <div class="useful-links">
              <h4>See also</h4>
              <?php
              if(isset($_SESSION['isLoggedIn'])){
                echo "<a href='clubregistration'>Register a New Club</a>";
              }else{
                echo "<a href='login'>Register a New Club</a>";
              }
              ?>
              <a href="<?php echo (isset($_SESSION['isLoggedIn']))?'userJoinClub':'login' ?>">Join a Club</a>
              <a href="registeredclubs">Clubs</a>
              <a href="contact">Contact us</a>
              <a href="about">About</a>
          </div>

          <div class="useful-links mt-5">
              <h4>Contact details</h4>
              <p>Graphic Era Hill University, Haldwani – Haldwani(Uttarakhand), India</p>
              <a href="#" class="nav-link text-dark"><b>Phone:</b>123456789</a>
              <a href="#" class="nav-link text-dark"><b>Email:</b> councilandclubs@gmail.com</a>
          </div>
          <div class="useful-links mt-5">
            <h4>Socials</h4>
              <a href="#" style="text-align:left" class="mt-2 btn text-light d-flex align-items-center px-2 ins-color"><img class="mr-2 sc-color-link" src="images/sm/cl/instagram.png" />Instagram</a>
              <a href="#" style="text-align:left" class="mt-2 btn text-light d-flex align-items-center px-2 tw-color"> <img class="mr-2 sc-color-link" src="images/sm/cl/twitter.png" />Twitter</a>
              <a href="#" style="text-align:left" class="mt-2 btn text-light d-flex align-items-center px-2 ln-color"> <img class="mr-2 sc-color-link" src="images/sm/cl/linkedin.png" />Linkedin</a>
              <a href="#" style="text-align:left" class="mt-2 btn text-light d-flex align-items-center px-2 fb-color"> <img class="mr-2 sc-color-link" src="images/sm/cl/fb.png" />Facebook</a>
          </div>
        </div>