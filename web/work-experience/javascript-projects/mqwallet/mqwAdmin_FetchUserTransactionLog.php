<?php
    session_start();
    
    date_default_timezone_set('Europe/Amsterdam');
    $_SESSION['lastActivity'] = time();
    
    if ($_SESSION['loggedIn'] == false)
    {
        exit('logout');
    }

    error_reporting(0);

    $loggedUserID = $_SESSION['loggedInUserInfoArray']['UserID'];

    //Connects to the desired database.
    $dbConnection = new mysqli("localhost", "matrix_q_nl_wallet", "admin", "admin");

    //If connected, do the following.
    $transactionLogArray = array();

    $fetchUserTransactionLog = $dbConnection->prepare("SELECT InvoiceNumber_FK, PaymentStatus, PaidAt, PaymentType, ShippingStatus, ShippedAt, ShippingMethod, TotalInEuros, BillingVATNumber, AmountRefundedInEuros, OutstandingBalanceInEuros, ProductQuantity, ProductName, ProductType, CreditsQuantity, TransactionType, GiftRecipient, BundleRecipients FROM tbl_transaction_log_for_$loggedUserID ORDER BY InvoiceNumber_FK ASC");
    $fetchUserTransactionLog->execute();
    $fetchUserTransactionLogResult = $fetchUserTransactionLog->get_result();

    if ($fetchUserTransactionLogResult->num_rows > 0)
    {
        while ($row = $fetchUserTransactionLogResult->fetch_assoc())
        {
            $transactionLogArray[] = $row;
        }
    }
    exit(json_encode($transactionLogArray));
?>