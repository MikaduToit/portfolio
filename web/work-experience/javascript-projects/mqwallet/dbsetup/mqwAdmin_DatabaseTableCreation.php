<?php
    //error_reporting(0);

    date_default_timezone_set('Europe/Amsterdam');

    //Connects to the desired database.
    $dbConnection = new mysqli("matrix-q.nl.mysql", "matrix_q_nl_wallet", "MQWALLET900", "matrix_q_nl_wallet");

    //Database connection check.
    if (!$dbConnection)
    {
        exit("Creation Failed: Connection Error!");
    }
    else if ($dbConnection->connect_error)
    {
        exit("Connection failed: " . $dbConnection->connect_error);
    }

    //If connected, do the following.
    $sqlCreateUserInformationTable = $dbConnection->prepare("CREATE TABLE `tbl_user_information`(`UserID` varchar(10) NOT NULL,`Email` varchar(255) NOT NULL,`FirstName` varchar(255) NOT NULL,`LastName` varchar(255) NOT NULL,`Admin` tinyint(1) NOT NULL,`Password` varchar(255) NOT NULL,`PhoneNumber` varchar(20) DEFAULT NULL,`Company` varchar(255) DEFAULT NULL,`Address` varchar(255) DEFAULT NULL,`City` varchar(255) DEFAULT NULL,`ZIPCode` varchar(10) DEFAULT NULL,`Province` varchar(255) DEFAULT NULL,`Country` varchar(255) DEFAULT NULL,`RegistrationDate` datetime NOT NULL,`ProfilePicture` varchar(20) NOT NULL DEFAULT 'DefaultPP.png',`EmailVerified` varchar(255) NOT NULL,PRIMARY KEY (`UserID`),UNIQUE KEY `Email` (`Email`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $sqlCreateUserInformationTable->execute();
    
    $adminPassword = password_hash('admin', PASSWORD_DEFAULT);
    $adminRegistrationDate = date('Y-m-d H:i:s');
    $registerAdminUser = $dbConnection->prepare("INSERT INTO `tbl_user_information`(`UserID`, `Email`, `FirstName`, `LastName`, `Admin`, `Password`, `PhoneNumber`, `Company`, `Address`, `City`, `ZIPCode`, `Province`, `Country`, `RegistrationDate`, `ProfilePicture`, `EmailVerified`) VALUES ('MQW0000000','admin@admin','admin','admin','1','$adminPassword','0','none','none','none','0','none','Netherlands','$adminRegistrationDate','DefaultPP.png','Yes')");
    $registerAdminUser->execute();

    $sqlCreateWalletBalanceInformationTable = $dbConnection->prepare("CREATE TABLE `tbl_wallet_balance_information`(`UserID` varchar(10) NOT NULL,`TotalInvestment` decimal(10,2) NOT NULL DEFAULT 0.00,`CreditsInvestment` int(10) NOT NULL DEFAULT 0,`TokenBalance` int(10) NOT NULL DEFAULT 0,`Cypher1TokenBalance` int(10) NOT NULL DEFAULT 0,`Cypher2TokenBalance` int(10) NOT NULL DEFAULT 0,`Cypher3TokenBalance` int(10) NOT NULL DEFAULT 0,`Cypher4TokenBalance` int(10) NOT NULL DEFAULT 0,`Cypher5TokenBalance` int(10) NOT NULL DEFAULT 0,`Cypher6TokenBalance` int(10) NOT NULL DEFAULT 0,`Cypher7TokenBalance` int(10) NOT NULL DEFAULT 0,`Cypher8TokenBalance` int(10) NOT NULL DEFAULT 0,`Cypher9TokenBalance` int(10) NOT NULL DEFAULT 0,PRIMARY KEY (`UserID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $sqlCreateWalletBalanceInformationTable->execute();

    $addAdminUserToWalletTable = $dbConnection->prepare("INSERT INTO `tbl_wallet_balance_information`(`UserID`, `TotalInvestment`, `CreditsInvestment`, `TokenBalance`, `Cypher1TokenBalance`, `Cypher2TokenBalance`, `Cypher3TokenBalance`, `Cypher4TokenBalance`, `Cypher5TokenBalance`, `Cypher6TokenBalance`, `Cypher7TokenBalance`, `Cypher8TokenBalance`, `Cypher9TokenBalance`) VALUES ('MQW0000000','0.00','0','0','0','0','0','0','0','0','0','0','0')");
    $addAdminUserToWalletTable->execute();

    $sqlCreateConversionRatiosTable = $dbConnection->prepare("CREATE TABLE `tbl_conversion_ratios`(`Description` varchar(6) NOT NULL DEFAULT 'Ratios',`CreditsToTokens` decimal(10,2) NOT NULL DEFAULT 1.00,`L1TokenBonus` decimal(10,2) NOT NULL DEFAULT 0.00,`L2TokenBonus` decimal(10,2) NOT NULL DEFAULT 0.00,`L3TokenBonus` decimal(10,2) NOT NULL DEFAULT 0.00,PRIMARY KEY (`Description`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $sqlCreateConversionRatiosTable->execute();

    $addDefaultRatiosToConversionTable = $dbConnection->prepare("INSERT INTO `tbl_conversion_ratios`(`Description`, `CreditsToTokens`, `L1TokenBonus`, `L2TokenBonus`, `L3TokenBonus`) VALUES ('Ratios','1.00','0.00','0.00','0.00')");
    $addDefaultRatiosToConversionTable->execute();

    $sqlCreateLoginTable = $dbConnection->prepare("CREATE TABLE `tbl_login_log` (`UserID_FK` varchar(10) NOT NULL, `Login_CET` datetime NOT NULL, KEY `UserID_FK` (`UserID_FK`), CONSTRAINT `userid_fk` FOREIGN KEY (`UserID_FK`) REFERENCES `tbl_user_information` (`UserID`) ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $sqlCreateLoginTable->execute();

    $sqlCreateGeneralTransactionTable = $dbConnection->prepare("CREATE TABLE `tbl_transaction_log_all` (`InvoiceNumber` int(10) NOT NULL,`UserID` varchar(10) NOT NULL, PRIMARY KEY (`InvoiceNumber`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $sqlCreateGeneralTransactionTable->execute();

    $sqlCreateAdminTransactionTable = $dbConnection->prepare("CREATE TABLE `tbl_transaction_log_for_mqw0000000` (`InvoiceNumber_FK` int(10) NOT NULL, `UserID` varchar(10) NOT NULL, `Email` varchar(255) NOT NULL, `PaymentStatus` varchar(50) NOT NULL, `PaidAt` date DEFAULT NULL, `PaymentType` varchar(50) DEFAULT NULL, `ShippingStatus` varchar(50) NOT NULL, `ShippedAt` date DEFAULT NULL, `ShippingMethod` varchar(50) DEFAULT NULL, `SubTotalInEuros` decimal(20,2) NOT NULL, `ShippingCostInEuros` decimal(20,2) NOT NULL, `VATAddedInEuros` decimal(20,2) NOT NULL, `VATIncludedInEuros` decimal(20,2) NOT NULL, `TotalInEuros` decimal(20,2) NOT NULL, `BillingVATNumber` varchar(50) DEFAULT NULL, `PaymentMethod` varchar(50) NOT NULL, `TransactionID` varchar(255) DEFAULT NULL, `AmountRefundedInEuros` decimal(20,2) DEFAULT NULL, `OutstandingBalanceInEuros` decimal(20,2) DEFAULT NULL, `CreatedTime` date NOT NULL, `CancelAt` date DEFAULT NULL, `ProductQuantity` int(10) NOT NULL, `ProductName` varchar(255) NOT NULL, `ProductOptions` varchar(255) DEFAULT NULL, `ProductPriceInEuros` decimal(20,2) NOT NULL, `ProductSKU` varchar(255) NOT NULL, `BillingFirstName` varchar(255) NOT NULL, `BillingLastName` varchar(255) NOT NULL, `BillingAddress` varchar(255) NOT NULL, `BillingCompany` varchar(255) DEFAULT NULL, `BillingCity` varchar(255) NOT NULL, `BillingZIP` varchar(10) NOT NULL, `BillingProvince` varchar(255) NOT NULL, `BillingCountry` varchar(255) NOT NULL, `BillingPhoneNumber` varchar(20) DEFAULT NULL, `ShippingFirstName` varchar(255) NOT NULL, `ShippingLastName` varchar(255) NOT NULL, `ShippingAddress` varchar(255) NOT NULL, `ShippingCompany` varchar(255) DEFAULT NULL, `ShippingCity` varchar(255) NOT NULL, `ShippingZIP` varchar(10) NOT NULL, `ShippingProvince` varchar(255) NOT NULL, `ShippingCountry` varchar(255) NOT NULL, `ShippingPhoneNumber` varchar(20) DEFAULT NULL, `UserNotes` varchar(255) DEFAULT NULL, `LoggedByUserID` varchar(10) NOT NULL, `LogDateTime` datetime NOT NULL, `CustomerRelationshipManagement` varchar(255) DEFAULT NULL, `ProductType` varchar(255) NOT NULL, `CreditsQuantity` int(10) DEFAULT NULL, `TransactionType` varchar(50) NOT NULL, `GiftRecipient` varchar(255) DEFAULT NULL, `BundleQuantity` int(10) NOT NULL DEFAULT 0, `BundleRecipients` varchar(500) DEFAULT NULL, `ProductEndOfLife` date DEFAULT NULL, KEY `InvoiceNumber_FK` (`InvoiceNumber_FK`), CONSTRAINT `invoicenumber_fk_MQW0000000` FOREIGN KEY (`InvoiceNumber_FK`) REFERENCES `tbl_transaction_log_all` (`InvoiceNumber`) ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $sqlCreateAdminTransactionTable->execute();

    $sqlCreateGeneralTokenTransactionTable = $dbConnection->prepare("CREATE TABLE `tbl_token_transaction_log_all` (`TransactionNumber` int(10) NOT NULL, `UserID` varchar(10) NOT NULL, PRIMARY KEY (`TransactionNumber`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $sqlCreateGeneralTokenTransactionTable->execute();

    $sqlCreateAdminTokenTransactionTable = $dbConnection->prepare("CREATE TABLE `tbl_token_transaction_log_for_mqw0000000` (`TransactionNumber_FK` int(10) NOT NULL, `UserID` varchar(10) NOT NULL, `Email` varchar(255) NOT NULL, `TransactionType` varchar(50) NOT NULL, `ProductQuantity` int(10) NOT NULL, `ProductName` varchar(255) NOT NULL, `ProductOptions` varchar(255) DEFAULT NULL, `ProductSKU` varchar(255) NOT NULL, `ProductPriceInEuros` decimal(10,2) DEFAULT NULL, `ProductEndOfLife` date DEFAULT NULL, `PaymentMethod` varchar(50) NOT NULL, `PaymentStatus` varchar(50) NOT NULL, `PaidAt` date DEFAULT NULL, `ShippingStatus` varchar(50) NOT NULL, `ShippedAt` date DEFAULT NULL, `ShippingMethod` varchar(50) DEFAULT NULL, `SubTotal` varchar(255) NOT NULL, `ShippingCost` varchar(255) DEFAULT NULL, `Total` varchar(255) NOT NULL, `Refunded` varchar(255) DEFAULT NULL, `Outstanding` varchar(255) DEFAULT NULL, `ShippingPhoneNumber` varchar(20) DEFAULT NULL, `ShippingCustomersCompany` varchar(255) DEFAULT NULL, `ShippingFirstName` varchar(255) DEFAULT NULL, `ShippingLastName` varchar(255) DEFAULT NULL, `ShippingAddress` varchar(255) DEFAULT NULL, `ShippingCity` varchar(255) DEFAULT NULL, `ShippingProvince` varchar(255) DEFAULT NULL, `ShippingCountry` varchar(255) DEFAULT NULL, `ShippingZIP` varchar(10) DEFAULT NULL, `PurchaseType` varchar(50) NOT NULL, `Contributors` varchar(255) DEFAULT NULL, `Recipients` varchar(255) DEFAULT NULL, `BundleQuantity` int(10) DEFAULT NULL, `CustomerRelationshipManagement` varchar(255) DEFAULT NULL, `LoggedByUserID` varchar(10) NOT NULL, `LogDateTime` datetime NOT NULL, `CancelledAt` date DEFAULT NULL, PRIMARY KEY (`TransactionNumber_FK`), CONSTRAINT `transactionnumber_fk_MQW0000000` FOREIGN KEY (`TransactionNumber_FK`) REFERENCES `tbl_token_transaction_log_all` (`TransactionNumber`) ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $sqlCreateAdminTokenTransactionTable->execute();

    exit("Tables Created and Admin Account Added! Email: admin@admin, Password: admin");
?>