<?php
    session_start();

    error_reporting(0);

    date_default_timezone_set('Europe/Amsterdam');
    $_SESSION['admin'] = false;
    $_SESSION['loggedIn'] = false;
    $_SESSION['loggedInUserInfoArray'] = array();

    //Connects to the desired database.
    $dbConnection = new mysqli("localhost", "root", "", "matrix_q_nl_wallet");

    //Database connection check.
    if (!$dbConnection)
    {
        session_unset();
        session_destroy();
        exit(nl2br("Login Failed: Connection Error!\nPlease try again later or contact support."));
    }
    else if ($dbConnection->connect_error)
    {
        session_unset();
        session_destroy();
        exit("Connection failed: " . $dbConnection->connect_error);
    }

    //If connected, do the following.
    $loginEmail = $_POST['loginEmail'];
    $loginPassword = $_POST['loginPassword'];

    if ($checkLoginCredentials = $dbConnection->prepare("SELECT UserID, FirstName, Admin, Password, EmailVerified FROM tbl_user_information WHERE Email = ?"))
    {
        if ($checkLoginCredentials->bind_param("s", $loginEmail))
        {
            if ($checkLoginCredentials->execute())
            {
                //Do Nothing.
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");
    } else exit("MySQL Error!");
    $checkLoginCredentialsResult = $checkLoginCredentials->get_result();

    if ($checkLoginCredentialsResult->num_rows == 1)
    {
        $_SESSION['loggedInUserInfoArray'] = $checkLoginCredentialsResult->fetch_assoc();
        
        $stringCheck = strpos($_SESSION['loggedInUserInfoArray']['EmailVerified'], "Yes");
        if($stringCheck !== false)
        {
            unset($_SESSION['loggedInUserInfoArray']['FirstName']);
            unset($_SESSION['loggedInUserInfoArray']['EmailVerified']);

            if (password_verify($loginPassword, $_SESSION['loggedInUserInfoArray']['Password']))
            {
                unset($_SESSION['loggedInUserInfoArray']['Password']);
            
                $loggedInUserID = $_SESSION['loggedInUserInfoArray']['UserID'];
                $loginDateTime = date('Y-m-d H:i:s');
                if ($updateLoginLog = $dbConnection->prepare("INSERT INTO tbl_login_log(UserID_FK, Login_CET) VALUES (?, ?)"))
                {
                    if ($updateLoginLog->bind_param("ss", $loggedInUserID, $loginDateTime))
                    {
                        if ($updateLoginLog->execute())
                        {
                            //Do Nothing.
                        } else exit("MySQL Error!");
                    } else exit("MySQL Error!");
                } else exit("MySQL Error!");
                
                $_SESSION['loggedIn'] = true;

                if ($_SESSION['loggedInUserInfoArray']['Admin'] == true)
                {
                    $_SESSION['admin'] = true;
                    unset($_SESSION['loggedInUserInfoArray']['Admin']);
                    exit("adminhome.php");
                }
                else
                {
                    exit("home.php");
                }
            }
            else 
            {
                session_unset();
                session_destroy();
                exit("Incorrect Email or Password!");
            }
        }
        else 
        {
            $verificationFirstName = $_SESSION['loggedInUserInfoArray']['FirstName'];
            $verificationUserID = $_SESSION['loggedInUserInfoArray']['UserID'];
            $verificationHash = $_SESSION['loggedInUserInfoArray']['EmailVerified'];
            session_unset();
            session_destroy();
            mail_utf8($loginEmail, $verificationFirstName, $verificationUserID, $verificationHash);
            exit(nl2br("Email address has not been verified!\nPlease check your email or spam folder for a verification link."));
        }
        
    } 
    else 
    {
        session_unset();
        session_destroy();
        exit("Incorrect Email or Password!");
    }

    //Functions.
    function mail_utf8($registrationEmail, $registrationFirstName, $newUserID, $registrationEmailVerificationHash)
    {
        ini_set("SMTP", "mailout.one.com");
        ini_set("smtp_port", "465");
        
        $fromName = "=?UTF-8?B?" . base64_encode('MQ Wallet Admin') . "?=";
        $fromEmail = "no-reply@matrix-q.nl";
        $subject = "=?UTF-8?B?" . base64_encode('Email Verification') . "?=";
        $emailBody = '
        
        Greeetings ' . $registrationFirstName . '.

        Your Matrix-Q Wallet account has just been registered!
        
        Your new User ID is: ' . $newUserID . '.
        
        To complete your registration, please verify your email address by clicking the link below.

        Verification link:
        https://matrix-q.nl/mqwallet/emailverification.php?email=' . $registrationEmail . '&hash=' . $registrationEmailVerificationHash . '
        
        Best wishes,
        Matrix-Q Team.
        
        ';
        $headers = "From: $fromName <$fromEmail>\r\n" . "MIME-Version: 1.0" . "\r\n" . "Content-type: text/plain; charset=UTF-8" . "\r\n";//For an html email use: Content-type: text/html;

        return mail($registrationEmail, $subject, $emailBody, $headers);
    }
?>