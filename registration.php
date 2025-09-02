<?php
session_start();

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

include "master.php"; // Your DB connection

if (isset($_POST['submit'])) {
    // Sanitize input
    $firstName = trim(mysqli_real_escape_string($conn, $_POST['fname']));
    $lastName = trim(mysqli_real_escape_string($conn, $_POST['lname']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $phone = trim(mysqli_real_escape_string($conn, $_POST['phone']));
    $dept = trim(mysqli_real_escape_string($conn, $_POST['department']));
    $course = trim(mysqli_real_escape_string($conn, $_POST['course']));
    $year = intval($_POST['year']);
    $sem = intval($_POST['semester']);
    $roll_no = trim(mysqli_real_escape_string($conn, $_POST['roll_no']));
    $social = trim(mysqli_real_escape_string($conn, $_POST['social']));
    $aoi = trim(mysqli_real_escape_string($conn, $_POST['aoi']));
    $password = $_POST['password'];  // will hash later
    $date = date('Y-m-d');

    // Generate registration ID and OTP
    $regId = 'HNB' . date('Y') . rand(1000, 9999);
    $otpGeneration = rand(1000, 9999);

    // Check if email already exists
    $sql_check = "SELECT * FROM users WHERE email = '{$email}'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        echo "<script>alert('User with this email already exists!');</script>";
    } else {
        // Hash password before storing
        $passwordHash = $password;

        // Prepare PHPMailer
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 0; // Disable verbose debug output
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'negiarav43@gmail.com';
            $mail->Password   = 'ybqqnjkrvfaasrtj'; // App password or real password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            //Recipients
            $mail->setFrom('negiarav43@gmail.com', 'GEHU Clubs and Societies');
            $mail->addAddress($email, $firstName . ' ' . $lastName);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'User Email Verification';
            $mail->Body = "
                <div>
                    <h1>GEHU Clubs & Societies</h1><br>
                    <h3>Hello, {$firstName} {$lastName}</h3>
                    <h6>Verify Email Address</h6>
                    <p>Your OTP is: <strong>{$otpGeneration}</strong></p><br>
                    <p>Thanks & Regards</p>
                    <p>GEHU Clubs</p>
                </div>
            ";

            if ($mail->send()) {
                // Insert into DB
                $sql_insert = "INSERT INTO users 
                    (reg_id, firstname, lastname, email, phone, department, year, semester, roll_no, course, social, aoi, password, verified, date) 
                    VALUES 
                    ('{$regId}', '{$firstName}', '{$lastName}', '{$email}', '{$phone}', '{$dept}', {$year}, {$sem}, '{$roll_no}', '{$course}', '{$social}', '{$aoi}', '{$passwordHash}', 0, '{$date}')";
                $result_insert = mysqli_query($conn, $sql_insert);

                if ($result_insert) {
                    $_SESSION['user_name'] = $firstName;
                    $_SESSION['last_name'] = $lastName;
                    $_SESSION['email_address'] = $email;
                    $_SESSION['register_id'] = $regId;
                    $_SESSION['otp'] = $otpGeneration;
                    $_SESSION['course'] = $course;
                    $_SESSION['joiner_name'] = $firstName . ' ' . $lastName;

                    header("Location: verifyemail.php");
                    exit;
                } else {
                    echo "Database error: " . mysqli_error($conn);
                }
            } else {
                echo "Mailer Error: " . $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>
    <div class="form-wrap">
      <div class="container shadow">
        <div class="row">
          <div class="col-md-12 m-0 p-0 form-heading">
            <h1 class="btn-block">User Registration Form</h1>
          </div>
        </div>
        <div class="row justify-content-center align-items-center display-none" id="signup">
          <div class="col-md-12">
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" class="form p-md-5">
                <!-- Firstname and Lastname -->
                <div class="row">
                  <div class="col-md-6 mt-3 mt-3">
                    <label for="username"
                      >Firstname<span class="imp-op">*</span></label
                    >
                    <input required type="text" name="fname" class="form-control" />
                  </div>
                  <div class="col-md-6 mt-3 mt-3">
                    <label for="username"
                      >Lastname<span class="imp-op">*</span></label
                    >
                    <input required type="text" name="lname" class="form-control" />
                  </div>
                </div>
                <!-- Email and Phone -->
                <div class="row">
                  <div class="col-md-6 mt-3">
                    <label for="email"
                      >Email<span class="imp-op">*</span></label
                    >
                    <input required type="text" name="email" class="form-control" />
                  </div>
                  <div class="col-md-6 mt-3">
                    <label for="phone"
                      >Contact Number<span class="imp-op">*</span></label
                    >
                    <input required type="number" name="phone" class="form-control" />
                  </div>
                  <!-- <input required type="text" class="form-control"> -->
                </div>

                <!-- Department, Clubs, Roll No -->
                <div class="row">
                  <div class="col-md-4 mt-3">
                    <label for="password">Department<span class="imp-op">*</span></label>
                    <select name="department" id="depart" class="form-control">
                      <option value="00">Select Department</option>
                      <option value="Department of Computer Science & Engineering">
                        Computer Science & Engineering
                      </option>
                      <option value="Department of Electronic and Communication Engineering">
                        Electronic and Communication Engineering
                      </option>
                      <option value="Department of Instrumentation Engineering">
                      Instrumentation Engineering
                      </option>
                      <option value="Department of Mechanical Engineering">
                        Mechanical Engineering
                      </option>
                      <option value="Department of Information Technology">
                      Information Technology
                      </option>
                        <option value="Department of Zoology & Biotechnology">
                                      Zoology & Biotechnology
                                    </option>
                        <option value="Department of Centre for Mountain Tourism & Hospitality Studies">
                                      Centre for Mountain Tourism & Hospitality Studies
                                    </option>
                        <option value="Department of Commerce">
                                      Commerce
                                    </option>
                        <option value="Department of Horticulture">
                                      Horticulture
                                    </option>
                        <option value="Department of Center for Journalism& Mass Mass Com.">
                                      Center for Journalism& Mass Mass Com.
                                    </option>
                        <option value="Department of Business Management">
                                      Business Management
                      	</option>
						  
                      <option value="Others">
                      Others
                      </option>
                    </select>
                  </div>
                  <div class="col-md-2 mt-3">
                      <label for="password">Course<span class="imp-op">*</span></label>
                      <input type="text" class="form-control" name="course">
                  </div>
                  <div class="col-md-1 mt-3">
                    <label for="password">Year<span class="imp-op">*</span></label>
                    <select name="year" id="year" class="form-control">
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                    </select>
                  </div>
                  <div class="col-md-1 mt-3">
                    <label for="password">Semester<span class="imp-op">*</span></label
                    >
                    <select name="semester" id="semester" class="form-control">
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                      <option value="6">6</option>
                      <option value="7">7</option>
                      <option value="8">8</option>
                      <!-- <option value="9">9</option>
                      <option value="10">10</option> -->
                    </select>
                  </div>
                  <div class="col-md-4 mt-3">
                    <label for="council">Roll Number
                      <span class="imp-op">*</span>
                    </label>
                    <input type="number" class="form-control" name="roll_no">
                  </div>
                  <!-- <input required type="text" class="form-control"> -->
                </div>

                <!-- Social media link & Area of Interest -->
                <div class="row">
                  <div class="col-md-6 mt-3">
                    <label for="Social">Social Media Link
                      <span class="imp-op">(optional)</span>
                    </label>
                    <input type="url" name="social" class="form-control" />
                  </div>
                  <div class="col-md-6 mt-3">
                    <label for="aoi">Area of Interest
                      <span class="imp-op">(optional)</span></label>
                    <input name="aoi" id="aoi" class="form-control" />
                  </div>
                </div>

                <!-- Password & confirm password -->
                <div class="row">
                  <div class="col-md-6 mt-3">
                    <label for="password">Password<span class="imp-op">*</span></label>
                    <input
                      required
                      type="password"
                      name="password"
                      class="form-control"
                      id="pass"
                    />
                    <span id="passError"></span>
                  </div>
                  <div class="col-md-6 mt-3">
                    <label for="password">Confirm Password<span class="imp-op">*</span></label>
                    <input
                      required
                      type="text"
                      class="form-control"
                      id="c-pass"
                      autocomplete="off"
                    />
                    <span id="passError"></span>
                  </div>
                  <div class="col-md-12">
                    <span class="imp-op imp-op-tx">Note: Field marked with (*) are mandatory fields.</span>
                  </div>
                  <!-- <input required type="text" class="form-control"> -->
                </div>

                <!-- Submit button -->
                <div class="row">
                  <div class="col-md-5 mb-3">
                    <button
                      required
                      name="submit"
                      type="submit"
                      id="reg-submit"
                      class="form-control mt-4 bg-secondary text-light"
                      style="cursor:not-allowed"
                      disabled>
                      Submit
                    </button>
                  </div>
                  <div class="col-md-3">
                    <button class="form-control mt-4 border-0">
                      <a href="home" class="bg-primary text-light p-2 px-5 text-center rounded text-decoration-none"><i class="fas fa-home"></i> Home</a>
                    </button>
                  </div>
                  <div class="col-md-4 text-center">
                    <button class="form-control mt-4 border-0">
                        <span>Already Registered?</span>
                        <a href="login" class="text-center">Login Here</a>
                    </button>
                  </div>
                  <div class="col-md-12 text-center">
                    <span><a href="https://GEHUclubs.live/" class="text-secondary">https://GEHUclubs.live/</a></span>
                  </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
