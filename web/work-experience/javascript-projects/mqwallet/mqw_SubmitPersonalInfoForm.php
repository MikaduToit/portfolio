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
    $loggedUserID = $_SESSION['loggedInUserInfoArray']['UserID'];
    foreach($_POST as $key => $value)
    {
        $$key = $value;

        if ($submitPersonalInfo = $dbConnection->prepare("UPDATE tbl_user_information SET $key = ? WHERE UserID = ?"))
        {
            if ($submitPersonalInfo->bind_param("ss", $$key, $loggedUserID))
            {
                if ($submitPersonalInfo->execute())
                {
                    //Do Nothing.
                } else exit("MySQL Error!");
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");

        $submitPersonalInfo->close();

        $_SESSION['loggedInUserInfoArray'][$key] = $$key;
    }

    if (array_key_exists('ProfilePicture', $_FILES))
    {
        if ($_FILES['ProfilePicture']['error'] != "0")
        {
            exit(nl2br("An error occured while uploading your profile picture: " . $_FILES['ProfilePicture']['error']));
        }
        else
        {
            if ($fetchCurrentProfilePictureURL = $dbConnection->prepare("SELECT ProfilePicture FROM tbl_user_information WHERE UserID = ?"))
            {
                if ($fetchCurrentProfilePictureURL->bind_param("s", $loggedUserID))
                {
                    if ($fetchCurrentProfilePictureURL->execute())
                    {
                        //Do Nothing.
                    } else exit("MySQL Error!");
                } else exit("MySQL Error!");
            } else exit("MySQL Error!");
            $fetchCurrentProfilePictureURLResult = $fetchCurrentProfilePictureURL->get_result();
            $fetchCurrentProfilePictureURLResultRow = $fetchCurrentProfilePictureURLResult->fetch_assoc();
            $currentProfilePictureURL = $fetchCurrentProfilePictureURLResultRow['ProfilePicture'];

            if (file_exists("uploads/userprofilepictures/" . $currentProfilePictureURL))
            {
                if ($currentProfilePictureURL != "DefaultPP.png")
                {
                    $currentPathInfo = pathinfo($currentProfilePictureURL);
                    $tempOldProfilePictureURL = $loggedUserID . "old." . $currentPathInfo['extension'];
                    rename("uploads/userprofilepictures/" . $currentProfilePictureURL, "uploads/userprofilepictures/" . $tempOldProfilePictureURL);
                }  
            }

            $pathInfo = pathinfo($_FILES['ProfilePicture']['name']);
            $newFileName = $loggedUserID . "pp." . $pathInfo['extension'];
            $_FILES['ProfilePicture']['name'] = $newFileName;

            $uploadedFileName = $_FILES['ProfilePicture']['name'];
            $uploadLocation = "uploads/userprofilepictures/" . $uploadedFileName;
            
            if (move_uploaded_file($_FILES['ProfilePicture']['tmp_name'], $uploadLocation))
            {
                if ($submitProfilePicture = $dbConnection->prepare("UPDATE tbl_user_information SET ProfilePicture = ? WHERE UserID = ?"))
                {
                    if ($submitProfilePicture->bind_param("ss", $uploadedFileName, $loggedUserID))
                    {
                        if ($submitProfilePicture->execute())
                        {
                            //Do Nothing.
                        } else exit("MySQL Error!");
                    } else exit("MySQL Error!");
                } else exit("MySQL Error!");
                unlink("uploads/userprofilepictures/" . $tempOldProfilePictureURL);
                $_SESSION['loggedInUserInfoArray']['ProfilePicture'] = $uploadedFileName;
            }
            else
            {
                rename("uploads/userprofilepictures/" . $tempOldProfilePictureURL, "uploads/userprofilepictures/" . $currentProfilePictureURL);
                exit("An error occured while uploading your profile picture!");
            }
        }
    }
    exit("Personal information changes saved!");
?>