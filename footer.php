    <div class="main-wrap">
            <div class="footer-wrap">
                <div class="footer-wrap-1">
                    <div class="footer-section">
                        <h1>About</h1>
                        <p class='text-justify'>
                            Student clubs are essential part of student life. HNBGU Clubs welcomes the contributions of student clubs and aims to promote a lively, creative and inclusive range of activities by providing support, resources and guidance for managing the clubs effectively.                       
                        </p>
                    </div>
                    <div class="footer-section">
                        <h1>Important Links</h1>
                     
                        <a href="https://gehu.ac.in/haldwani/">Main Website</a>
                       
                    
                    </div>
                    <div class="footer-section">
                        <h1>Contact Information</h1>
                        <a href="mailto:">councilandclubs@gmail.com</a>
                        
                    </div>
                    <div class="footer-section">
                      <h1>Councils & Clubs</h1>
                      <?php
                        $club_list_footer = "SELECT * FROM club_info";
                        $club_list_footer_query = mysqli_query($conn,$club_list_footer);

                       if(mysqli_num_rows($club_list_footer_query)){
    while($data = mysqli_fetch_assoc($club_list_footer_query)){
        $club_name_encoded = urlencode($data['club_name']);
        $club_id = $data['id']; // Assuming `id` is the club ID
        echo '<a href="clubs?cc_name='.$club_name_encoded.'&club_id='.$club_id.'">'.$data['club_name'].'</a>';
    }
}

                      ?>
                     
                    </div>
                </div>
                <div class="footer-wrap-2">
                    <div class="line"></div>
                    <div class="social-link">
                        <a href="">
                            <img src="images/sm/fb.png" alt="Facebook">
                        </a>
                        <a href="">
                            <img src="images/sm/ins.png" alt="Instagram">
                        </a>
                        <a href="">
                            <img src="images/sm/linkedin.png" alt="linkedin">
                        </a>
                        <a href="">
                            <img src="images/sm/twitter.png" alt="twitter">
                        </a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="first-box">
                    <a href="#">Terms & Conditions</a>
                    <a href="#">Privacy Policy</a>
                    <a href="">Developer Information</a>
                </div>
                <div class="last-box">
                    <a href="#">&copy; Copyright <?php echo date('Y'); ?></a>
                </div>
            </div>
        </div>