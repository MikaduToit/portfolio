<?php
    session_start();
    
    date_default_timezone_set('Europe/Amsterdam');
    $_SESSION['lastActivity'] = time();
    
    if ($_SESSION['loggedIn'] == false)
    {
        exit('logout.php');
    }

    error_reporting(0);
    
    //Connects to the desired database.
    $dbConnection = new mysqli("localhost", "root", "", "matrix_q_nl_wallet");

    //Database connection check.
    if (!$dbConnection)
    {
        exit("Submission Failed: Connection Error!");
    }
    else if ($dbConnection->connect_error)
    {
        exit("Connection failed: " . $dbConnection->connect_error);
    }

    //If connected, do the following.
    $errorDuringTableCreation = false;

    foreach($_POST as $key => $value)
    {
        $$key = $value;
    }

    if ($checkForEmailDuplicates = $dbConnection->prepare("SELECT Email FROM tbl_user_information WHERE Email = ?"))
    {
        if ($checkForEmailDuplicates->bind_param("s", $registrationEmail))
        {
            if ($checkForEmailDuplicates->execute())
            {
                //Do Nothing.
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");
    } else exit("MySQL Error!");
    $checkForEmailDuplicatesResult = $checkForEmailDuplicates->get_result();
    if ($checkForEmailDuplicatesResult->num_rows >= 1)
    {
        exit("Email Address already registered!");
    }
    else
    {
        $newUserID = "";

        if ($checkUserCount = $dbConnection->prepare("SELECT UserID FROM tbl_user_information ORDER BY UserID ASC"))
        {
            if ($checkUserCount->execute())
            {
                //Do Nothing.
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");
        $checkUserCountResult = $checkUserCount->get_result();

        $userIDCounter = 0;
        while ($row = $checkUserCountResult->fetch_assoc())
        {
            $existingUserID = $row['UserID'];
            $existingUserIDNum = preg_replace('/[^0-9]/', '', $existingUserID);
            $existingUserIDNum = ltrim($existingUserIDNum, "0");
            $existingUserIDNum = intval($existingUserIDNum);
            
            if ($existingUserIDNum === $userIDCounter)
            {
                $userIDCounter++;
            }
            else
            {
                break;
            }
        }
        $newUserIDNum = str_pad("$userIDCounter", 7, "0", STR_PAD_LEFT);
        $newUserID = ("MQW" . "$newUserIDNum");

        //Register new user to tbl_user_information.
        $registrationAdmin = 0;
        $registrationRandomPassword = password_hash(rand(0,10000), PASSWORD_DEFAULT);
        $registrationDate = date('Y-m-d H:i:s');
        $registrationProfilePicture = "DefaultPP.png";
        $registrationEmailVerificationHash = MD5(rand(0,10000));
        if ($registerNewUserInfo = $dbConnection->prepare("INSERT INTO tbl_user_information(UserID, Email, FirstName, LastName, Admin, Password, PhoneNumber, Company, Address, City, ZIPCode, Province, Country, RegistrationDate, ProfilePicture, EmailVerified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))
        {
            if ($registerNewUserInfo->bind_param("ssssisssssssssss", $newUserID, $registrationEmail, $registrationFirstName, $registrationLastName, $registrationAdmin, $registrationRandomPassword, $registrationPhoneNumber, $registrationCompany, $registrationAddress, $registrationCity, $registrationZIPCode, $registrationProvince, $registrationCountry, $registrationDate, $registrationProfilePicture, $registrationEmailVerificationHash))
            {
                if ($registerNewUserInfo->execute())
                {
                    //Do Nothing.
                } else exit("MySQL Error!");
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");

        //Send email address verification email to the registered user.
        mail_utf8($registrationEmail, $registrationFirstName, $newUserID, $registrationEmailVerificationHash);

        //Register new user to tbl_wallet_balance_information.
        if ($checkForUserDuplicatesWallet = $dbConnection->prepare("SELECT * FROM tbl_wallet_balance_information WHERE UserID = ?"))
        {
            if ($checkForUserDuplicatesWallet->bind_param("s", $newUserID))
            {
                if ($checkForUserDuplicatesWallet->execute())
                {
                    //Do Nothing.
                } else exit("MySQL Error!");
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");
        $checkForUserDuplicatesWalletResult = $checkForUserDuplicatesWallet->get_result();
        if ($checkForUserDuplicatesWalletResult->num_rows == 0)
        {
            $registrationDefaultDecimal = 0.00;
            $registrationDefaultInteger = 0;
            if ($registerNewUserWalletBalances = $dbConnection->prepare("INSERT INTO tbl_wallet_balance_information(UserID, TotalInvestment, CreditsInvestment, TokenBalance, Cypher1TokenBalance, Cypher2TokenBalance, Cypher3TokenBalance, Cypher4TokenBalance, Cypher5TokenBalance, Cypher6TokenBalance, Cypher7TokenBalance, Cypher8TokenBalance, Cypher9TokenBalance) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))
            {
                if ($registerNewUserWalletBalances->bind_param("sdiiiiiiiiiii", $newUserID, $registrationDefaultDecimal, $registrationDefaultInteger, $registrationDefaultInteger, $registrationDefaultInteger, $registrationDefaultInteger, $registrationDefaultInteger, $registrationDefaultInteger, $registrationDefaultInteger, $registrationDefaultInteger, $registrationDefaultInteger, $registrationDefaultInteger, $registrationDefaultInteger))
                {
                    if ($registerNewUserWalletBalances->execute())
                    {
                        //Do Nothing.
                    } else exit("MySQL Error!");
                } else exit("MySQL Error!");
            } else exit("MySQL Error!");
        }

        //Create new user tables.
        CreateUserTransactionLogTable($newUserID, $dbConnection, $errorDuringTableCreation);
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

    function CreateUserTransactionLogTable($newUserID, $dbConnection, $errorDuringTableCreation)
    {
        if ($checkForUserTransactionLogDuplicates = $dbConnection->prepare("SELECT * FROM tbl_transaction_log_for_$newUserID"))
        {
            $errorDuringTableCreation = true;
            CreateUserTokenTransactionLogTable($newUserID, $dbConnection, $errorDuringTableCreation);
        }
        else
        {
            if ($createUserTransactionLogTable = $dbConnection->prepare("CREATE TABLE `tbl_transaction_log_for_$newUserID` (`InvoiceNumber_FK` int(10) NOT NULL, `UserID` varchar(10) NOT NULL, `Email` varchar(255) NOT NULL, `PaymentStatus` varchar(50) NOT NULL, `PaidAt` date DEFAULT NULL, `PaymentType` varchar(50) DEFAULT NULL, `ShippingStatus` varchar(50) NOT NULL, `ShippedAt` date DEFAULT NULL, `ShippingMethod` varchar(50) DEFAULT NULL, `SubTotalInEuros` decimal(20,2) NOT NULL, `ShippingCostInEuros` decimal(20,2) NOT NULL, `VATAddedInEuros` decimal(20,2) NOT NULL, `VATIncludedInEuros` decimal(20,2) NOT NULL, `TotalInEuros` decimal(20,2) NOT NULL, `BillingVATNumber` varchar(50) DEFAULT NULL, `PaymentMethod` varchar(50) NOT NULL, `TransactionID` varchar(255) DEFAULT NULL, `AmountRefundedInEuros` decimal(20,2) DEFAULT NULL, `OutstandingBalanceInEuros` decimal(20,2) DEFAULT NULL, `CreatedTime` date NOT NULL, `CancelAt` date DEFAULT NULL, `ProductQuantity` int(10) NOT NULL, `ProductName` varchar(255) NOT NULL, `ProductOptions` varchar(255) DEFAULT NULL, `ProductPriceInEuros` decimal(20,2) NOT NULL, `ProductSKU` varchar(255) NOT NULL, `BillingFirstName` varchar(255) NOT NULL, `BillingLastName` varchar(255) NOT NULL, `BillingAddress` varchar(255) NOT NULL, `BillingCompany` varchar(255) DEFAULT NULL, `BillingCity` varchar(255) NOT NULL, `BillingZIP` varchar(10) NOT NULL, `BillingProvince` varchar(255) NOT NULL, `BillingCountry` varchar(255) NOT NULL, `BillingPhoneNumber` varchar(20) DEFAULT NULL, `ShippingFirstName` varchar(255) NOT NULL, `ShippingLastName` varchar(255) NOT NULL, `ShippingAddress` varchar(255) NOT NULL, `ShippingCompany` varchar(255) DEFAULT NULL, `ShippingCity` varchar(255) NOT NULL, `ShippingZIP` varchar(10) NOT NULL, `ShippingProvince` varchar(255) NOT NULL, `ShippingCountry` varchar(255) NOT NULL, `ShippingPhoneNumber` varchar(20) DEFAULT NULL, `UserNotes` varchar(255) DEFAULT NULL, `LoggedByUserID` varchar(10) NOT NULL, `LogDateTime` datetime NOT NULL, `CustomerRelationshipManagement` varchar(255) DEFAULT NULL, `ProductType` varchar(255) NOT NULL, `CreditsQuantity` int(10) DEFAULT NULL, `TransactionType` varchar(50) NOT NULL, `GiftRecipient` varchar(255) DEFAULT NULL, `BundleQuantity` int(10) NOT NULL DEFAULT 0, `BundleRecipients` varchar(500) DEFAULT NULL, `ProductEndOfLife` date DEFAULT NULL, KEY `InvoiceNumber_FK` (`InvoiceNumber_FK`), CONSTRAINT `invoicenumber_fk_$newUserID` FOREIGN KEY (`InvoiceNumber_FK`) REFERENCES `tbl_transaction_log_all` (`InvoiceNumber`) ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"))
            {
                if ($createUserTransactionLogTable->execute())
                {
                    //Do Nothing.
                } else exit("MySQL Error!");
            } else exit("MySQL Error!");
            CreateUserTokenTransactionLogTable($newUserID, $dbConnection, $errorDuringTableCreation);
        }
    }

    function CreateUserTokenTransactionLogTable($newUserID, $dbConnection, $errorDuringTableCreation)
    {
        if ($checkForUserTokenTransactionLogDuplicates = $dbConnection->prepare("SELECT * FROM tbl_token_transaction_log_for_$newUserID"))
        {
            $errorDuringTableCreation = true;
            ExitProcedure($newUserID, $errorDuringTableCreation);
        }
        else
        {
            if ($createUserTokenTransactionLogTable = $dbConnection->prepare("CREATE TABLE `tbl_token_transaction_log_for_$newUserID` (`TransactionNumber_FK` int(10) NOT NULL, `UserID` varchar(10) NOT NULL, `Email` varchar(255) NOT NULL, `TransactionType` varchar(50) NOT NULL, `ProductQuantity` int(10) NOT NULL, `ProductName` varchar(255) NOT NULL, `ProductOptions` varchar(255) DEFAULT NULL, `ProductSKU` varchar(255) NOT NULL, `ProductPriceInEuros` decimal(10,2) DEFAULT NULL, `ProductEndOfLife` date DEFAULT NULL, `PaymentMethod` varchar(50) NOT NULL, `PaymentStatus` varchar(50) NOT NULL, `PaidAt` date DEFAULT NULL, `ShippingStatus` varchar(50) NOT NULL, `ShippedAt` date DEFAULT NULL, `ShippingMethod` varchar(50) DEFAULT NULL, `SubTotal` varchar(255) NOT NULL, `ShippingCost` varchar(255) DEFAULT NULL, `Total` varchar(255) NOT NULL, `Refunded` varchar(255) DEFAULT NULL, `Outstanding` varchar(255) DEFAULT NULL, `ShippingPhoneNumber` varchar(20) DEFAULT NULL, `ShippingCustomersCompany` varchar(255) DEFAULT NULL, `ShippingFirstName` varchar(255) DEFAULT NULL, `ShippingLastName` varchar(255) DEFAULT NULL, `ShippingAddress` varchar(255) DEFAULT NULL, `ShippingCity` varchar(255) DEFAULT NULL, `ShippingProvince` varchar(255) DEFAULT NULL, `ShippingCountry` varchar(255) DEFAULT NULL, `ShippingZIP` varchar(10) DEFAULT NULL, `PurchaseType` varchar(50) NOT NULL, `Contributors` varchar(255) DEFAULT NULL, `Recipients` varchar(255) DEFAULT NULL, `BundleQuantity` int(10) DEFAULT NULL, `CustomerRelationshipManagement` varchar(255) DEFAULT NULL, `LoggedByUserID` varchar(10) NOT NULL, `LogDateTime` datetime NOT NULL, `CancelledAt` date DEFAULT NULL, PRIMARY KEY (`TransactionNumber_FK`), CONSTRAINT `transactionnumber_fk_$newUserID` FOREIGN KEY (`TransactionNumber_FK`) REFERENCES `tbl_token_transaction_log_all` (`TransactionNumber`) ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"))
            {
                if ($createUserTokenTransactionLogTable->execute())
                {
                    //Do Nothing.
                } else exit("MySQL Error!");
            } else exit("MySQL Error!");
            ExitProcedure($newUserID, $errorDuringTableCreation);
        }
    }

    function ExitProcedure($newUserID, $errorDuringTableCreation)
    {
        if ($errorDuringTableCreation == true)
        {
            exit(nl2br("An error occured during table creation!\nThis could be because one of the tables already existed.\nPlease check the database to ensure all is correct for $newUserID"));
        }
        else
        {
            exit("User registration complete!");
        }
    }
?>