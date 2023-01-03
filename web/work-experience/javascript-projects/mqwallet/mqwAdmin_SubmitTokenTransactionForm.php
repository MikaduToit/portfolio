<?php
    session_start();
    
    date_default_timezone_set('Europe/Amsterdam');
    $_SESSION['lastActivity'] = time();
    $transactionType = "";
    $tokenTPaidAt = NULL;
    $tokenTOutstanding = "";
    
    if ($_SESSION['loggedIn'] == false)
    {
        exit('logout.php');
    }

    //error_reporting(0);

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
    //Fetch current wallet balance information.
    $associatedUserID = $_POST['tokenTCustomerSelect'];
    if ($fetchCurrentBalances = $dbConnection->prepare("SELECT TokenBalance, Cypher1TokenBalance, Cypher2TokenBalance, Cypher3TokenBalance, Cypher4TokenBalance, Cypher5TokenBalance, Cypher6TokenBalance, Cypher7TokenBalance, Cypher8TokenBalance, Cypher9TokenBalance FROM tbl_wallet_balance_information WHERE UserID = ?"))
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

    $transactionType = $_POST['tokenTTransactionType'];
    foreach($_POST as $key => $value)
    {
        if (empty($value) && !is_numeric($value))
        {
            $value = NULL;
        }
        if ($transactionType == "Tokens Paid")
        {
            if ($key == "tokenTTotalMQT")
            {
                if (intval($value) > intval($currentBalancesArray['TokenBalance']))
                {
                    $tokenTOutstanding = $tokenTOutstanding . "MQT: " . abs(intval($currentBalancesArray['TokenBalance']) - intval($value)) . ", ";
                }
                else
                {
                    $tokenTOutstanding = $tokenTOutstanding . "MQT: 0, ";
                }
            }
            else if ($key == "tokenTTotalCT9")
            {
                if (intval($value) > intval($currentBalancesArray['Cypher9TokenBalance']))
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT9: " . abs(intval($currentBalancesArray['Cypher9TokenBalance']) - intval($value)) . ", ";
                }
                else
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT9: 0, ";
                }
            }
            else if ($key == "tokenTTotalCT8")
            {
                if (intval($value) > intval($currentBalancesArray['Cypher8TokenBalance']))
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT8: " . abs(intval($currentBalancesArray['Cypher8TokenBalance']) - intval($value)) . ", ";
                }
                else
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT8: 0, ";
                }
            }
            else if ($key == "tokenTTotalCT7")
            {
                if (intval($value) > intval($currentBalancesArray['Cypher7TokenBalance']))
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT7: " . abs(intval($currentBalancesArray['Cypher7TokenBalance']) - intval($value)) . ", ";
                }
                else
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT7: 0, ";
                }
            }
            else if ($key == "tokenTTotalCT6")
            {
                if (intval($value) > intval($currentBalancesArray['Cypher6TokenBalance']))
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT6: " . abs(intval($currentBalancesArray['Cypher6TokenBalance']) - intval($value)) . ", ";
                }
                else
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT6: 0, ";
                }
            }
            else if ($key == "tokenTTotalCT5")
            {
                if (intval($value) > intval($currentBalancesArray['Cypher5TokenBalance']))
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT5: " . abs(intval($currentBalancesArray['Cypher5TokenBalance']) - intval($value)) . ", ";
                }
                else
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT5: 0, ";
                }
            }
            else if ($key == "tokenTTotalCT4")
            {
                if (intval($value) > intval($currentBalancesArray['Cypher4TokenBalance']))
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT4: " . abs(intval($currentBalancesArray['Cypher4TokenBalance']) - intval($value)) . ", ";
                }
                else
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT4: 0, ";
                }
            }
            else if ($key == "tokenTTotalCT3")
            {
                if (intval($value) > intval($currentBalancesArray['Cypher3TokenBalance']))
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT3: " . abs(intval($currentBalancesArray['Cypher3TokenBalance']) - intval($value)) . ", ";
                }
                else
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT3: 0, ";
                }
            }
            else if ($key == "tokenTTotalCT2")
            {
                if (intval($value) > intval($currentBalancesArray['Cypher2TokenBalance']))
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT2: " . abs(intval($currentBalancesArray['Cypher2TokenBalance']) - intval($value)) . ", ";
                }
                else
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT2: 0, ";
                }
            }
            else if ($key == "tokenTTotalCT1")
            {
                if (intval($value) > intval($currentBalancesArray['Cypher1TokenBalance']))
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT1: " . abs(intval($currentBalancesArray['Cypher1TokenBalance']) - intval($value));
                }
                else
                {
                    $tokenTOutstanding = $tokenTOutstanding . "CT1: 0";
                }
            }
        }
        
        $$key = $value;
    }

    //Generate new unique TransactionNumber.
    $newTransactionNumber = 0;

    if ($checkTransactionCount = $dbConnection->prepare("SELECT TransactionNumber FROM tbl_token_transaction_log_all ORDER BY TransactionNumber ASC"))
    {
        if ($checkTransactionCount->execute())
        {
            //Do Nothing.
        } else exit("MySQL Error!");
    } else exit("MySQL Error!");
    $checkTransactionCountResult = $checkTransactionCount->get_result();

    $transactionNumberCounter = 1;
    while ($row = $checkTransactionCountResult->fetch_assoc())
    {
        $existingTransactionNumber = $row['TransactionNumber'];
        $existingTransactionNumberNum = intval($existingTransactionNumber);
            
        if ($existingTransactionNumberNum === $transactionNumberCounter)
        {
            $transactionNumberCounter++;
        }
        else
        {
            break;
        }
    }
    $newTransactionNumber = $transactionNumberCounter;

    //Insert data into database.
    if ($submitTransactionAll = $dbConnection->prepare("INSERT INTO tbl_token_transaction_log_all(TransactionNumber, UserID) VALUES (?, ?)"))
    {
        if ($submitTransactionAll->bind_param("is", $newTransactionNumber, $tokenTCustomerSelect))
        {
            if ($submitTransactionAll->execute())
            {
                //Do Nothing.
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");
    } else exit("MySQL Error!");

    if ($tokenTPaymentStatus == "Paid")
    {
        $tokenTPaidAt = date('Y-m-d');
    }
    $tokenTLogDateTime = date('Y-m-d H:i:s');

    if ($submitTransaction = $dbConnection->prepare("INSERT INTO tbl_token_transaction_log_for_$tokenTCustomerSelect(TransactionNumber_FK, UserID, Email, TransactionType, ProductQuantity, ProductName, ProductOptions, ProductSKU, ProductPriceInEuros, ProductEndOfLife, PaymentMethod, PaymentStatus, PaidAt, ShippingStatus, ShippedAt, ShippingMethod, SubTotal, ShippingCost, Total, Outstanding, ShippingPhoneNumber, ShippingCustomersCompany, ShippingFirstName, ShippingLastName, ShippingAddress, ShippingCity, ShippingProvince, ShippingCountry, ShippingZIP, PurchaseType, CustomerRelationshipManagement, LoggedByUserID, LogDateTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))
    {
        if ($submitTransaction->bind_param("isssisssdssssssssssssssssssssssss", $newTransactionNumber, $tokenTCustomerSelect, $tokenTEmail, $tokenTTransactionType, $tokenTProductQuantity, $tokenTProductName, $tokenTProductOptions, $tokenTProductSKU, $tokenTProductPrice, $tokenTProductEOL, $tokenTPaymentMethod, $tokenTPaymentStatus, $tokenTPaidAt, $tokenTShippingStatus, $tokenTShippedAt, $tokenTShippingMethod, $tokenTSubTotal, $tokenTShippingCost, $tokenTTotal, $tokenTOutstanding, $tokenTShippingPhoneNumber, $tokenTShippingCompany, $tokenTShippingFirstName, $tokenTShippingLastName, $tokenTShippingAddress, $tokenTShippingCity, $tokenTShippingProvince, $tokenTShippingCountry, $tokenTShippingZIP, $tokenTPurchaseType, $tokenTCustomerRelationshipManagement, $tokenTLoggedByUserID, $tokenTLogDateTime))
        {
            if ($submitTransaction->execute())
            {
                //Do Nothing.
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");
    } else exit("MySQL Error!");

    //Update Token balances.
    if ($transactionType == "Tokens Received")
    {
        $mqtokens = intval($currentBalancesArray['TokenBalance']) + intval($tokenTTotalMQT);
        $ct1tokens = intval($currentBalancesArray['Cypher1TokenBalance']) + intval($tokenTTotalCT1);
        $ct2tokens = intval($currentBalancesArray['Cypher2TokenBalance']) + intval($tokenTTotalCT2);
        $ct3tokens = intval($currentBalancesArray['Cypher3TokenBalance']) + intval($tokenTTotalCT3);
        $ct4tokens = intval($currentBalancesArray['Cypher4TokenBalance']) + intval($tokenTTotalCT4);
        $ct5tokens = intval($currentBalancesArray['Cypher5TokenBalance']) + intval($tokenTTotalCT5);
        $ct6tokens = intval($currentBalancesArray['Cypher6TokenBalance']) + intval($tokenTTotalCT6);
        $ct7tokens = intval($currentBalancesArray['Cypher7TokenBalance']) + intval($tokenTTotalCT7);
        $ct8tokens = intval($currentBalancesArray['Cypher8TokenBalance']) + intval($tokenTTotalCT8);
        $ct9tokens = intval($currentBalancesArray['Cypher9TokenBalance']) + intval($tokenTTotalCT9);
    }
    else if ($transactionType == "Tokens Paid")
    {
        $mqtokens = max(intval($currentBalancesArray['TokenBalance']) - intval($tokenTTotalMQT), 0);
        $ct1tokens = max(intval($currentBalancesArray['Cypher1TokenBalance']) - intval($tokenTTotalCT1), 0);
        $ct2tokens = max(intval($currentBalancesArray['Cypher2TokenBalance']) - intval($tokenTTotalCT2), 0);
        $ct3tokens = max(intval($currentBalancesArray['Cypher3TokenBalance']) - intval($tokenTTotalCT3), 0);
        $ct4tokens = max(intval($currentBalancesArray['Cypher4TokenBalance']) - intval($tokenTTotalCT4), 0);
        $ct5tokens = max(intval($currentBalancesArray['Cypher5TokenBalance']) - intval($tokenTTotalCT5), 0);
        $ct6tokens = max(intval($currentBalancesArray['Cypher6TokenBalance']) - intval($tokenTTotalCT6), 0);
        $ct7tokens = max(intval($currentBalancesArray['Cypher7TokenBalance']) - intval($tokenTTotalCT7), 0);
        $ct8tokens = max(intval($currentBalancesArray['Cypher8TokenBalance']) - intval($tokenTTotalCT8), 0);
        $ct9tokens = max(intval($currentBalancesArray['Cypher9TokenBalance']) - intval($tokenTTotalCT9), 0);
    }

    if ($updateInvestmentBalance = $dbConnection->prepare("UPDATE tbl_wallet_balance_information SET TokenBalance = ?, Cypher1TokenBalance = ?, Cypher2TokenBalance = ?, Cypher3TokenBalance = ?, Cypher4TokenBalance = ?, Cypher5TokenBalance = ?, Cypher6TokenBalance = ?, Cypher7TokenBalance = ?, Cypher8TokenBalance = ?, Cypher9TokenBalance = ? WHERE UserID = ?"))
    {
        if ($updateInvestmentBalance->bind_param("iiiiiiiiiis", $mqtokens, $ct1tokens, $ct2tokens, $ct3tokens, $ct4tokens, $ct5tokens, $ct6tokens, $ct7tokens, $ct8tokens, $ct9tokens, $associatedUserID))
        {
            if ($updateInvestmentBalance->execute())
            {
                //Do Nothing.
            } else exit("MySQL Error!");
        } else exit("MySQL Error!");
    } else exit("MySQL Error!");

    if ($tokenTOutstanding != "")
    {
        exit(nl2br("Token transaction successfully submitted!\nThe customer did not have enough tokens so the outstanding balance was recorded to the database!"));
    }
    else
    {
        exit("Token transaction successfully submitted!");
    } 
?>