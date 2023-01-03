<?php
    session_start();
    
    date_default_timezone_set('Europe/Amsterdam');
    $_SESSION['lastActivity'] = time();
    
    if ($_SESSION['loggedIn'] == false)
    {
        exit('logout.php');
    }

    error_reporting(0);

    $loggedUserID = $_SESSION['loggedInUserInfoArray']['UserID'];

    //Connects to the desired database.
    $dbConnection = new mysqli("localhost", "root", "", "matrix_q_nl_wallet");

    //If connected, do the following.
    $tokenTransactionLogArray = array();

    $fetchUserTokenTransactionLog = $dbConnection->prepare("SELECT TransactionNumber_FK, TransactionType, ProductQuantity, ProductName, PaymentStatus, PaidAt, ShippingStatus, ShippedAt, ShippingMethod, Total, Outstanding FROM tbl_token_transaction_log_for_$loggedUserID ORDER BY TransactionNumber_FK ASC");
    $fetchUserTokenTransactionLog->execute();
    $fetchUserTokenTransactionLogResult = $fetchUserTokenTransactionLog->get_result();

    if ($fetchUserTokenTransactionLogResult->num_rows > 0)
    {
        while ($row = $fetchUserTokenTransactionLogResult->fetch_assoc())
        {
            $tokenTransactionLogArray[] = $row;
        }
    }
    exit(json_encode($tokenTransactionLogArray));
?>