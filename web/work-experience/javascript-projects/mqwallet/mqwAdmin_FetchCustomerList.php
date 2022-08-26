<?php
    error_reporting(0);

    date_default_timezone_set('Europe/Amsterdam');

    //Connects to the desired database.
    $dbConnection = new mysqli("matrix-q.nl.mysql", "matrix_q_nl_wallet", "MQWALLET900", "matrix_q_nl_wallet");

    //If connected, do the following.
    $customerListArray = array();

    $fetchCustomerList = $dbConnection->prepare("SELECT UserID, Email, FirstName, LastName FROM tbl_user_information");
    $fetchCustomerList->execute();
    $fetchCustomerListResult = $fetchCustomerList->get_result();

    if ($fetchCustomerListResult->num_rows > 0)
    {
        while ($row = $fetchCustomerListResult->fetch_assoc())
        {
            $customerListArray[] = $row;
        }
    }
    exit(json_encode($customerListArray));
?>