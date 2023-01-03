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
    foreach($_POST as $key => $value)
    {
        if (empty($value) && !is_numeric($value))
        {
            $value = NULL;
        }
        
        $$key = $value;
    }

    if ($fetchUserIDAssociatedWithEmail = $dbConnection->prepare("SELECT UserID FROM tbl_user_information WHERE Email = ?"))
    {
        if ($fetchUserIDAssociatedWithEmail->bind_param("s", $invoiceEmail))
        {
            if ($fetchUserIDAssociatedWithEmail->execute())
            {
                //Do Nothing.
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");
    } else exit("MySQL Error!");
    $fetchUserIDAssociatedWithEmailResult = $fetchUserIDAssociatedWithEmail->get_result();
    if ($fetchUserIDAssociatedWithEmailResult->num_rows < 1)
    {
        exit("Email submitted has not been registered!");
    }
    else
    {
        $associatedUserIDArray = $fetchUserIDAssociatedWithEmailResult->fetch_assoc();
        $associatedUserID = $associatedUserIDArray['UserID'];
        
        //Check if Invoice Number has already been submitted.
        if ($checkForDuplicateInvoiceNumber = $dbConnection->prepare("SELECT UserID FROM tbl_transaction_log_all WHERE InvoiceNumber = ?"))
        {
            if ($checkForDuplicateInvoiceNumber->bind_param("s", $invoiceNumber))
            {
                if ($checkForDuplicateInvoiceNumber->execute())
                {
                    //Do Nothing.
                } else exit("MySQL Error!");
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");
        $checkForDuplicateInvoiceNumberResult = $checkForDuplicateInvoiceNumber->get_result();
        $checkForDuplicateInvoiceNumberResultArray = $checkForDuplicateInvoiceNumberResult->fetch_assoc();
        if ($checkForDuplicateInvoiceNumberResult->num_rows <= 0)
        {
            if ($submitInvoiceAll = $dbConnection->prepare("INSERT INTO tbl_transaction_log_all(InvoiceNumber, UserID) VALUES (?, ?)"))
            {
                if ($submitInvoiceAll->bind_param("is", $invoiceNumber, $associatedUserID))
                {
                    if ($submitInvoiceAll->execute())
                    {
                        //Do Nothing.
                    } else exit("MySQL Error!");
                } else exit("MySQL Error!");
            } else exit("MySQL Error!");
        }
        else
        {
            if ($associatedUserID != $checkForDuplicateInvoiceNumberResultArray['UserID'])
            {
                exit("This Invoice Number has already been submitted for a different User ID!");
            }
        }
            
        $invoiceLogDateTime = date('Y-m-d H:i:s');

        if ($submitInvoice = $dbConnection->prepare("INSERT INTO tbl_transaction_log_for_$associatedUserID(InvoiceNumber_FK, UserID, Email, PaymentStatus, PaidAt, PaymentType, ShippingStatus, ShippedAt, ShippingMethod, SubTotalInEuros, ShippingCostInEuros, VATAddedInEuros, VATIncludedInEuros, TotalInEuros, BillingVATNumber, PaymentMethod, TransactionID, AmountRefundedInEuros, OutstandingBalanceInEuros, CreatedTime, CancelAt, ProductQuantity, ProductName, ProductOptions, ProductPriceInEuros, ProductSKU, BillingFirstName, BillingLastName, BillingAddress, BillingCompany, BillingCity, BillingZIP, BillingProvince, BillingCountry, BillingPhoneNumber, ShippingFirstName, ShippingLastName, ShippingAddress, ShippingCompany, ShippingCity, ShippingZIP, ShippingProvince, ShippingCountry, ShippingPhoneNumber, UserNotes, LoggedByUserID, LogDateTime, CustomerRelationshipManagement, ProductType, CreditsQuantity, TransactionType, GiftRecipient, BundleQuantity, BundleRecipients, ProductEndOfLife) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))
        {
            if ($submitInvoice->bind_param("issssssssdddddsssddssissdssssssssssssssssssssssssississ", $invoiceNumber, $associatedUserID, $invoiceEmail, $invoicePaymentStatus, $invoicePaidAt, $invoicePaymentType, $invoiceShippingStatus, $invoiceShippedAt, $invoiceShippingMethod, $invoiceSubTotal, $invoiceShippingCost, $invoiceVATAdded, $invoiceVATIncluded, $invoiceTotal, $invoiceBillingVATNumber, $invoicePaymentMethod, $invoiceTransactionID, $invoiceAmountRefunded, $invoiceOutstandingBalance, $invoiceCreatedTime, $invoiceCancelledAt, $invoiceProductQuantity, $invoiceProductName, $invoiceProductOptions, $invoiceProductPrice, $invoiceProductSKU, $invoiceBillingFirstName, $invoiceBillingLastName, $invoiceBillingAddress, $invoiceBillingCompany, $invoiceBillingCity, $invoiceBillingZIP, $invoiceBillingProvince, $invoiceBillingCountry, $invoiceBillingPhoneNumber, $invoiceShippingFirstName, $invoiceShippingLastName, $invoiceShippingAddress, $invoiceShippingCompany, $invoiceShippingCity, $invoiceShippingZIP, $invoiceShippingProvince, $invoiceShippingCountry, $invoiceShippingPhoneNumber, $invoiceUserNotes, $invoiceLoggedByUserID, $invoiceLogDateTime, $invoiceCustomerRelationshipManagement, $invoiceProductType, $invoiceCreditsQuantity, $invoiceTransactionType, $invoiceGiftRecipient, $invoiceBundleQuantity, $invoiceBundleRecipient, $invoiceProductEOL))
            {
                if ($submitInvoice->execute())
                {
                    //Do Nothing.
                } else exit("MySQL Error!");
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");

        //Fetch current wallet balance information.
        if ($fetchCurrentBalances = $dbConnection->prepare("SELECT TotalInvestment, CreditsInvestment, TokenBalance FROM tbl_wallet_balance_information WHERE UserID = ?"))
        {
            if ($fetchCurrentBalances->bind_param("s", $associatedUserID))
            {
                if ($fetchCurrentBalances->execute())
                {
                    //Do Nothing.
                } else exit("MySQL Error!");
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");

        $fetchCurrentBalancesResult = $fetchCurrentBalances->get_result();
        $currentBalancesArray = $fetchCurrentBalancesResult->fetch_assoc();

        //Update TotalInvestment balance.
        $finalInvoiceAmount = floatval($invoiceTotal - $invoiceOutstandingBalance - $invoiceAmountRefunded);
        $newTotalInvestmentBalance = floatval($currentBalancesArray['TotalInvestment'] + $finalInvoiceAmount);
        $newTotalInvestmentBalance = number_format((float)$newTotalInvestmentBalance, 2, '.', '');
        if ($updateInvestmentBalance = $dbConnection->prepare("UPDATE tbl_wallet_balance_information SET TotalInvestment = ? WHERE UserID = ?"))
        {
            if ($updateInvestmentBalance->bind_param("ds", $newTotalInvestmentBalance, $associatedUserID))
            {
                if ($updateInvestmentBalance->execute())
                {
                    //Do Nothing.
                } else exit("MySQL Error!");
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");


        if ($invoiceProductType == "Credits")
        {
            //Fetch current conversion ratio information.
            if ($fetchCurrentConversions = $dbConnection->prepare("SELECT CreditsToTokens, L1TokenBonus, L2TokenBonus, L3TokenBonus FROM tbl_conversion_ratios WHERE Description = 'Ratios'"))
            {
                if ($fetchCurrentConversions->execute())
                {
                    //Do Nothing.
                } else exit("MySQL Error!");
            } else exit("MySQL Error!");
            $fetchCurrentConversionsResult = $fetchCurrentConversions->get_result();
            $currentConversionsArray = $fetchCurrentConversionsResult->fetch_assoc();

            //Calculate updated values.
            $newCreditsBalance = intval($currentBalancesArray['CreditsInvestment'] + $invoiceCreditsQuantity);
            $tokensConversion = floatval($invoiceCreditsQuantity * $currentConversionsArray['CreditsToTokens']);
            $newTokensBalance = intval($currentBalancesArray['TokenBalance'] + $tokensConversion);

            //Store updated values.
            if ($updateCreditsAndTokensBalance = $dbConnection->prepare("UPDATE tbl_wallet_balance_information SET CreditsInvestment = ?, TokenBalance = ? WHERE UserID = ?"))
            {
                if ($updateCreditsAndTokensBalance->bind_param("iis", $newCreditsBalance, $newTokensBalance, $associatedUserID))
                {
                    if ($updateCreditsAndTokensBalance->execute())
                    {
                        //Do Nothing.
                    } else exit("MySQL Error!");
                } else exit("MySQL Error!");
            } else exit("MySQL Error!");
        }
        exit("Invoice successfully submitted!");
    }
?>