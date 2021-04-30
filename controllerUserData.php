<?php 
session_start();
require "connection.php";
$email = "";
$name = "";
$lastname = "";
$errors = array();

//if user signup button
if(isset($_POST['signup'])){
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $lastname = mysqli_real_escape_string($con, $_POST['lastname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);
    if($password !== $cpassword){
        $errors['password'] = "Confirm password not matched!";
    }
    $email_check = "SELECT * FROM usertable WHERE email = '$email'";
    $res = mysqli_query($con, $email_check);
    if(mysqli_num_rows($res) > 0){
        $errors['email'] = "Email that you have entered is already exist!";
    }
    if(count($errors) === 0){
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $code = rand(999999, 111111);
        $status = "notverified";
        $insert_data = "INSERT INTO usertable (name, email, password, code, status)
                        values('$name', '$email', '$encpass', '$code', '$status')";
        $data_check = mysqli_query($con, $insert_data);
        if($data_check){
            $subject = "Email Verification Code";
            $message = "Your verification code is $code";
            $sender = "From: iRegister.au@gmail.com";
            if(mail($email, $subject, $message, $sender)){
                $info = "We've sent a verification code to your email - $email";
                $_SESSION['info'] = $info;
                $_SESSION['email'] = $email;
                $_SESSION['password'] = $password;
                header('location: user-otp.php');
                exit();
            }else{
                $errors['otp-error'] = "Failed while sending code!";
            }
        }else{
            $errors['db-error'] = "Failed while inserting data into database!";
        }
    }

}
    //if user click verification code submit button
    if(isset($_POST['check'])){
        $_SESSION['info'] = "";
        $otp_code = mysqli_real_escape_string($con, $_POST['otp']);
        $check_code = "SELECT * FROM usertable WHERE code = $otp_code";
        $code_res = mysqli_query($con, $check_code);
        if(mysqli_num_rows($code_res) > 0){
            $fetch_data = mysqli_fetch_assoc($code_res);
            $fetch_code = $fetch_data['code'];
            $email = $fetch_data['email'];
            $code = 0;
            $status = 'verified';
            $update_otp = "UPDATE usertable SET code = $code, status = '$status' WHERE code = $fetch_code";
            $update_res = mysqli_query($con, $update_otp);
            if($update_res){
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                header('location: home.php');
                exit();
            }else{
                $errors['otp-error'] = "Failed while updating code!";
            }
        }else{
            $errors['otp-error'] = "You've entered incorrect code!";
        }
    }

    //if user click login button
    if(isset($_POST['loginbtn'])){
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $password = mysqli_real_escape_string($con, $_POST['password']);
        $check_email = "SELECT * FROM usertable WHERE email = '$email'";
        $res = mysqli_query($con, $check_email);
        if(mysqli_num_rows($res) > 0){
            $fetch = mysqli_fetch_assoc($res);
            $fetch_pass = $fetch['password'];
            if(password_verify($password, $fetch_pass)){
                $_SESSION['email'] = $email;
                $status = $fetch['status'];
                if($status == 'verified'){
                  $_SESSION['email'] = $email;
                  $_SESSION['password'] = $password;
                    header('location: home.php');
                }else{
                    $info = "It's look like you haven't still verify your email - $email";
                    $_SESSION['info'] = $info;
                    header('location: user-otp.php');
                }
            }else{
                $errors['email'] = "Incorrect email or password!";
            }
        }else{
            $errors['email'] = "It's look like you're not yet a member! Click on the bottom link to signup.";
        }
    }

    //if user click continue button in forgot password form
    if(isset($_POST['check-email'])){
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $check_email = "SELECT * FROM usertable WHERE email='$email'";
        $run_sql = mysqli_query($con, $check_email);
        if(mysqli_num_rows($run_sql) > 0){
            $code = rand(999999, 111111);
            $insert_code = "UPDATE usertable SET code = $code WHERE email = '$email'";
            $run_query =  mysqli_query($con, $insert_code);
            if($run_query){
                $subject = "Password Reset Code";
                $message = "Your password reset code is $code";
                $sender = "From: iRehistro.au@gmail.com";
                if(mail($email, $subject, $message, $sender)){
                    $info = "We've sent a passwrod reset otp to your email - $email";
                    $_SESSION['info'] = $info;
                    $_SESSION['email'] = $email;
                    header('location: reset-code.php');
                    exit();
                }else{
                    $errors['otp-error'] = "Failed while sending code!";
                }
            }else{
                $errors['db-error'] = "Something went wrong!";
            }
        }else{
            $errors['email'] = "This email address does not exist!";
        }
    }

    //if user click check reset otp button
    if(isset($_POST['check-reset-otp'])){
        $_SESSION['info'] = "";
        $otp_code = mysqli_real_escape_string($con, $_POST['otp']);
        $check_code = "SELECT * FROM usertable WHERE code = $otp_code";
        $code_res = mysqli_query($con, $check_code);
        if(mysqli_num_rows($code_res) > 0){
            $fetch_data = mysqli_fetch_assoc($code_res);
            $email = $fetch_data['email'];
            $_SESSION['email'] = $email;
            $info = "Please create a new password that you don't use on any other site.";
            $_SESSION['info'] = $info;
            header('location: new-password.php');
            exit();
        }else{
            $errors['otp-error'] = "You've entered incorrect code!";
        }
    }

    //if user click change password button
    if(isset($_POST['change-password'])){
        $_SESSION['info'] = "";
        $password = mysqli_real_escape_string($con, $_POST['password']);
        $cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);
        if($password !== $cpassword){
            $errors['password'] = "Confirm password not matched!";
        }else{
            $code = 0;
            $email = $_SESSION['email']; //getting this email using session
            $encpass = password_hash($password, PASSWORD_BCRYPT);
            $update_pass = "UPDATE usertable SET code = $code, password = '$encpass' WHERE email = '$email'";
            $run_query = mysqli_query($con, $update_pass);
            if($run_query){
                $info = "Your password changed. Now you can login with your new password.";
                $_SESSION['info'] = $info;
                header('Location: password-changed.php');
            }else{
                $errors['db-error'] = "Failed to change your password!";
            }
        }
    }
    
   //if login now button click
    if(isset($_POST['login-now'])){
        header('Location: login.php');
    }

    //if register button click
    if(isset($_POST['register'])){
        $email = $_SESSION['email']; //getting this email using session
        $firstname                = mysqli_real_escape_string($con, $_POST['firstname']);
        $midname                  = mysqli_real_escape_string($con, $_POST['midname']);
        $lastname                 = mysqli_real_escape_string($con, $_POST['lastname']);
        $birthdate                = mysqli_real_escape_string($con, $_POST['birthdate']);
        $birthplace               = mysqli_real_escape_string($con, $_POST['birthplace']);
        $gender                   = mysqli_real_escape_string($con, $_POST['gender']);
        $civil                    = mysqli_real_escape_string($con, $_POST['civil']);
        $contact                  = mysqli_real_escape_string($con, $_POST['contact']);
        $citizen                  = mysqli_real_escape_string($con, $_POST['citizen']);
        $acquisition              = mysqli_real_escape_string($con, $_POST['acquisition']);
        $province                 = mysqli_real_escape_string($con, $_POST['province']);
        $city                     = mysqli_real_escape_string($con, $_POST['city']);
        $brgy                     = mysqli_real_escape_string($con, $_POST['brgy']);
        $address                  = mysqli_real_escape_string($con, $_POST['address']);
        $postal                   = mysqli_real_escape_string($con, $_POST['postal']);
        $city_years_months_number = mysqli_real_escape_string($con, $_POST['city_years_months_number']);
        $country_years_number     = mysqli_real_escape_string($con, $_POST['country_years_number']);
        $usertype                 = mysqli_real_escape_string($con, $_POST['usertype']);
        $update_info = "UPDATE usertable SET firstname = '$firstname', midname = '$midname', lastname = '$lastname', birthdate = '$birthdate', birthplace = '$birthplace', 
        gender = '$gender', civil = '$civil', contact = '$contact', citizen = '$citizen', acquisition = '$acquisition', province = '$province', city = '$city', 
        brgy = '$brgy', address = '$address', postal = '$postal', city_years_months_number = '$city_years_months_number', country_years_number = '$country_years_number', usertype = '$usertype' WHERE email = '$email'";
        if(mysqli_query($con, $update_info)){

            // If everything runs fine with your sql query you will see a message and then the window
             //closes
            echo "<script language='javascript'>alert('Successfully Registered!')</script>";
            echo "<script>window.location.href='typage.php';</script>";  

        }

        else{
        
            echo "error" . $update_info . "<br>" . mysqli_error($con);
        
        } 
       
        mysqli_close($con);  //it is always closing the database connection

    }

    //if continue button clicked
    if(isset($_POST['homebtn'])){
        header('Location: login.php');
    }
        
    if(isset($_POST['editbtn'])){

        $id = $_POST['edit'];

        $sql = "SELECT * FROM usertable WHERE id = '$id'";
        $run_Sql = mysqli_query($con, $sql);
        if($run_Sql){
            $fetch_info = mysqli_fetch_assoc($run_Sql);
        }
    }

    if(isset($_POST['deletebtn'])){

        $id = $_POST['delete'];

        $sqldelete = "DELETE FROM usertable WHERE id = '$id'";
        $run_sqldelete = mysqli_query($con, $sqldelete);
        if($run_sqldelete){
        
            echo "<script language='javascript'>alert('Successfully Deleted!')</script>";
            echo "<script>window.location.href='index.php';</script>"; 

        }
        else{

            echo "There is an error while deleting the data" . mysqli_error($con);

        }

        mysqli_close($con);  //it is always closing the database connection

    }


    ?>
    <?php
    $con2 = mysqli_connect("localhost","root","", "userform2");

    if(isset($_POST['loginbtn2'])){

        $usertype1="admin";
        $email_login = $_POST['email2'];
        $password_login = $_POST['password'];

        $query = "SELECT * FROM usertable WHERE email='$email_login' AND password='$password_login' AND usertype='$usertype1'";
        $query_run = mysqli_query($con2, $query);

            $_SESSION['email2'] = $email_login;
            header('Location: index.php');
    }
    ?>
