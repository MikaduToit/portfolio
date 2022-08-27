<?php
    session_start();
    
    date_default_timezone_set('Europe/Amsterdam');
    $_SESSION['lastActivity'] = time();

    error_reporting(0);

    //Connects to the desired database.
    $dbConnection = new mysqli("localhost", "matrix_q_nl_wallet", "admin", "admin");

    //Database connection check.
    if (!$dbConnection)
    {
        exit("Save Failed: Connection Error!");
    }
    else if ($dbConnection->connect_error)
    {
        exit("Connection failed: " . $dbConnection->connect_error);
    }

    //If connected, do the following.
    $userID = $_SESSION['loggedInUserInfoArray']['UserID'];
    $password = password_hash($_POST["Password"], PASSWORD_DEFAULT);

    if ($submitPassword = $dbConnection->prepare("UPDATE tbl_user_information SET Password = ? WHERE UserID = ?"))
    {
        if ($submitPassword->bind_param("ss", $password, $userID))
        {
            if ($submitPassword->execute())
            {
                //Do Nothing.
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");
    } else exit("MySQL Error!");

    
    mail_utf8($_SESSION['loggedInUserInfoArray']['Email'], $_SESSION['loggedInUserInfoArray']['FirstName']);
    
    exit("Password successfully changed!");
    
    //Functions.
    function mail_utf8($email, $firstName)
    {
        ini_set("SMTP", "mailout.one.com");
        ini_set("smtp_port", "465");
        
        $fromName = "=?UTF-8?B?" . base64_encode('MQ Wallet Admin') . "?=";
        $fromEmail = "no-reply@matrix-q.nl";
        $subject = "=?UTF-8?B?" . base64_encode('Notification of Password change') . "?=";
        $emailBody = '
        
        Greeetings ' . $firstName . '.

        This email is to notify you that your MQ Wallet password has just been changed.
        If this was done by you, please ignore this email. If not, please contact an administrator immediately!
        
        Best wishes,
        Matrix-Q Team.

        ';
        $headers = "From: $fromName <$fromEmail>\r\n" . "MIME-Version: 1.0" . "\r\n" . "Content-type: text/plain; charset=UTF-8" . "\r\n";//For an html email use: Content-type: text/html;

        return mail($email, $subject, $emailBody, $headers);
    }
?>