<?php
    session_start();
    
    date_default_timezone_set('Europe/Amsterdam');
    $_SESSION['lastActivity'] = time();
    
    if ($_SESSION['loggedIn'] == false)
    {
        exit('logout');
    }

    error_reporting(0);

    //Connects to the desired database.
    $dbConnection = new mysqli("matrix-q.nl.mysql", "matrix_q_nl_wallet", "MQWALLET900", "matrix_q_nl_wallet");

    //Database connection check.
    if (!$dbConnection)
    {
        exit("Verification Failed: Connection Error!");
    }
    else if ($dbConnection->connect_error)
    {
        exit("Connection failed: " . $dbConnection->connect_error);
    }

    //If connected, do the following.
    $loggedUserID = $_SESSION['loggedInUserInfoArray']['UserID'];
    $vPassword = $_POST['Password'];

    if ($checkIfLoggedUserIDMatches = $dbConnection->prepare("SELECT Password FROM tbl_user_information WHERE UserID = ?"))
    {
        if ($checkIfLoggedUserIDMatches->bind_param("s", $loggedUserID))
        {
            if ($checkIfLoggedUserIDMatches->execute())
            {
                //Do Nothing.
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");
    } else exit("MySQL Error!");
    $checkIfLoggedUserIDMatchesResult = $checkIfLoggedUserIDMatches->get_result();
    
    //Check if UserID and Password combination exists.
    if ($checkIfLoggedUserIDMatchesResult->num_rows == 1)
    {
        $data = $checkIfLoggedUserIDMatchesResult->fetch_assoc();
        
        if (password_verify($vPassword, $data['Password']))
        {
            exit("True");
        }
        else
        {
            exit("Verification password is incorrect!");
        }
    }
?>