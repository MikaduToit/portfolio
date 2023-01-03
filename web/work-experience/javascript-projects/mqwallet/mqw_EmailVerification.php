<?php
    error_reporting(0);

    date_default_timezone_set('Europe/Amsterdam');

    //Connects to the desired database.
    $dbConnection = new mysqli("localhost", "root", "", "matrix_q_nl_wallet");

    //Database connection check.
    if (!$dbConnection)
    {
        exit(nl2br("Login Failed: Connection Error!\nPlease try again later or contact support."));
    }
    else if ($dbConnection->connect_error)
    {
        exit("Connection failed: " . $dbConnection->connect_error);
    }

    //If connected, do the following.
    $email = $_POST['Email'];
    $hash = $_POST['Hash'];
    $newPassword = password_hash($_POST['NewPassword'], PASSWORD_DEFAULT);

    if ($checkEmail = $dbConnection->prepare("SELECT UserID, EmailVerified FROM tbl_user_information WHERE Email = ?"))
    {
        if ($checkEmail->bind_param("s", $email))
        {
            if ($checkEmail->execute())
            {
                //Do Nothing.
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");
    } else exit("MySQL Error!");
    
    $checkEmailResult = $checkEmail->get_result();
    if($checkEmailResult->num_rows == 1)
    {
        $checkEmailResultData = $checkEmailResult->fetch_assoc();
        $stringCheck = strpos($checkEmailResultData['EmailVerified'], "Yes");
        if($stringCheck === false)
        {
            if ($hash == $checkEmailResultData['EmailVerified'])
            {
                $userID = $checkEmailResultData['UserID'];
                $verificationDateTime = date('Y-m-d H:i:s');
                $emailVerified = "Yes (" . $verificationDateTime . ")";

                if ($submitNewPassword = $dbConnection->prepare("UPDATE tbl_user_information SET Password = ?, EmailVerified = ? WHERE UserID = ? && Email = ?"))
                {
                    if ($submitNewPassword->bind_param("ssss", $newPassword, $emailVerified, $userID, $email))
                    {
                        if ($submitNewPassword->execute())
                        {
                            //Do Nothing.
                        } else exit("MySQL Error!");
                    } else exit("MySQL Error!");
                } else exit("MySQL Error!");

                exit("login.php");
            }
            else
            {
                exit("Hash does not match!");
            }
        }
        else
        {
            exit("Email already verified!");
        }
    }
?>