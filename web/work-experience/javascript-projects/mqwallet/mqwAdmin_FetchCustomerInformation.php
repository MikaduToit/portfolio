<?php
    error_reporting(0);

    date_default_timezone_set('Europe/Amsterdam');

    //Connects to the desired database.
    $dbConnection = new mysqli("localhost", "root", "", "matrix_q_nl_wallet");

    //If connected, do the following.
    $customerListArray = array();
    $selectedUserID = $_POST['SelectedUserID'];

    $fetchCustomerInformation = $dbConnection->prepare("SELECT Email, FirstName, LastName, PhoneNumber, Company, Address, City, ZIPCode, Province, Country FROM tbl_user_information WHERE UserID = ?");
    $fetchCustomerInformation->bind_param("s", $selectedUserID);
    $fetchCustomerInformation->execute();
    $fetchCustomerInformationResult = $fetchCustomerInformation->get_result();

    if($fetchCustomerInformationResult->num_rows == 1)
    {
        $customerInformationArray = $fetchCustomerInformationResult->fetch_assoc();
    }

    exit(json_encode($customerInformationArray));
?>