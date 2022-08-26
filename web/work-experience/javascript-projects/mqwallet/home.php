<?php
    session_start();
    
    error_reporting(0);

    date_default_timezone_set('Europe/Amsterdam');

    if (isset($_SESSION['lastActivity']) && (time() - $_SESSION['lastActivity'] > 1800))//Destroy session after 30mins of inactivity server side.
    {
        session_unset();
        session_destroy();
    }
    $_SESSION['lastActivity'] = time();
    
    if ($_SESSION['loggedIn'] == false)
    {
        header('location:login');
    }
    
    //Connects to the desired database.
    $dbConnection = new mysqli("matrix-q.nl.mysql", "matrix_q_nl_wallet", "MQWALLET900", "matrix_q_nl_wallet");

    $fetchUserInfo = $dbConnection->prepare("SELECT Email, FirstName, LastName, PhoneNumber, Company, Address, City, ZIPCode, Province, Country, ProfilePicture FROM tbl_user_information WHERE UserID = ?");
    $userID = $_SESSION['loggedInUserInfoArray']['UserID'];
    $fetchUserInfo->bind_param("s", $userID);
    $fetchUserInfo->execute();
    $fetchUserInfoResult = $fetchUserInfo->get_result();
    $fetchUserInfoResultArray = $fetchUserInfoResult->fetch_assoc();
    foreach($fetchUserInfoResultArray as $key => $value)
    {
        $_SESSION['loggedInUserInfoArray'][$key] = $value;
    }
    
    $fetchWalletBalanceInfo = $dbConnection->prepare("SELECT TotalInvestment, CreditsInvestment, TokenBalance, Cypher1TokenBalance, Cypher2TokenBalance, Cypher3TokenBalance, Cypher4TokenBalance, Cypher5TokenBalance, Cypher6TokenBalance, Cypher7TokenBalance, Cypher8TokenBalance, Cypher9TokenBalance FROM tbl_wallet_balance_information WHERE UserID = ?");
    $fetchWalletBalanceInfo->bind_param("s", $userID);
    $fetchWalletBalanceInfo->execute();
    $fetchWalletBalanceInfoResult = $fetchWalletBalanceInfo->get_result();
    $_SESSION['loggedInUserBalanceInfoArray'] = $fetchWalletBalanceInfoResult->fetch_assoc();

    unset($userID);
    $loggedInUserInfoArray = json_encode($_SESSION['loggedInUserInfoArray']);
    $loggedInUserBalanceInfoArray = json_encode($_SESSION['loggedInUserBalanceInfoArray']);
    
    $dbConnection->close();
?>



<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>MQ Wallet Admin</title>
        <link rel="stylesheet" type="text/css" href="css/stylesheet_All.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
        <div id="displayContainer">
            <div id="navigationBar"><!--Element Container-->
                <div id="navigationBarMenuToggleContainer">
                    <button id="navigationBarMenuToggle" title="Menu"></button>
                    <img id="menuButtonIcon" src="./assets/images/MenuIcon.png"/>
                </div>
                <picture id="companyLogoContainer">
                    <source media="(max-width: 999px)" srcset="assets/images/MQWLogoBlack_40p.png 1x, assets/images/MQWLogoBlack_80p.png 2x, assets/images/MQWLogoBlack_120p.png 3x, assets/images/MQWLogoBlack_160p.png 4x, assets/images/MQWLogoBlack_200p.png 5x">
                    <source media="(min-width: 1000px)" srcset="assets/images/MQWLogoBlack_80p.png 0.5x, assets/images/MQWLogoBlack_120p.png 1x, assets/images/MQWLogoBlack_160p.png 1.5x, assets/images/MQWLogoBlack_200p.png 2x, assets/images/MQWLogoBlack_240p.png 2.5x, assets/images/MQWLogoBlack_280p.png 3x, assets/images/MQWLogoBlack_320p.png 3.5x, assets/images/MQWLogoBlack_360p.png 4x, assets/images/MQWLogoBlack_400p.png 4.5x">
                    <img id="companyLogo" src="assets/images/MQWLogoBlack_200p.png">
                </picture>
            </div>

            <div id="navigationMenu"><!--Element Container-->
                <div id="menuFilterLayer">
                    <div class="navigationBarSectionHeading">PERSONAL</div>
                    <button class="navigationBarTabSelector" id="personalWalletTabSelector">Wallet</button>
                    <button class="navigationBarTabSelector" id="personalTransactionLogTabSelector">Transaction Log</button>

                    <div class="navigationBarSectionHeading">HELP</div>
                    <button class="navigationBarTabSelector" id="contactTabSelector">Contact</button>
                    <button class="navigationBarTabSelector" id="aboutTabSelector">About</button>
                    <button class="navigationBarTabSelector" id="improveYourExperienceTabSelector">Improve Your Experience</button>
                </div>
            </div>

            <div id="informationBar"><!--Element Container-->
                <div id="logoutButtonContainer">
                    <button id="logoutButton" title="Logout"></button>
                    <img id="logoutButtonIcon" src="./assets/images/LogoutIcon.png"/>
                </div>
            </div>

            <div id="contentDisplayContainer"><!--Element Container-->
                <div id="filterLayer">
                    <div class="contentDisplay" id="personalWalletTab"><!--Page Tab-->
                        <div class="contentDisplayFlexBox">
                            <div class="contentDisplayHeading" id="walletPageHeading"></div>

                            <div class="container" id="totalInvestmentBalanceContainer">
                                <div class="internalContainer">
                                    <img id="creditCardIcon" src="./assets/images/CreditCardIcon.png"/>
                                    <div class="balanceHeading">Self-investment</div>
                                    <span class="balanceValue" id="totalInvestmentBalance"></span><span id="balanceCurrency">&euro;</span>
                                    <div class="balanceHeading" id="creditsBalanceHeading">Credits Purchased</div>
                                    <span class="balanceValue" id="creditsBalance"></span>
                                </div>
                            </div>
                            <div class="container" id="tokenBalanceContainer">
                                <div class="internalContainer">
                                    <img id="tokenIcon" src="./assets/images/TokenIcon.png"/>
                                    <div id="tokenBalanceHeading">Token Balance</div>
                                    <span id="tokenBalanceValue"></span>
                                    <div id="tokenBalanceDescription">Your current MQ Token balance.</div>
                                </div>
                            </div>
                            <div class="container" id="cypherTokenBalanceContainer">
                                <div class="internalContainer" id="cypherTokenBalanceInternalContainer">
                                    <div id="cypherTokenDisplayFlexBox">
                                        <div id="nonagonContainer">
                                            <div class="nonagonAnchor" id="nonagonAnchor9">
                                                <div class="nonagonSegment" id="token9"></div>
                                            </div>
                                            <div class="nonagonAnchor" id="nonagonAnchor8">
                                                <div class="nonagonSegment" id="token8"></div>
                                            </div>
                                            <div class="nonagonAnchor" id="nonagonAnchor7">
                                                <div class="nonagonSegment" id="token7"></div>
                                            </div>
                                            <div class="nonagonAnchor" id="nonagonAnchor6">
                                                <div class="nonagonSegment" id="token6"></div>
                                            </div>
                                            <div class="nonagonAnchor" id="nonagonAnchor5">
                                                <div class="nonagonSegment" id="token5"></div>
                                            </div>
                                            <div class="nonagonAnchor" id="nonagonAnchor4">
                                                <div class="nonagonSegment" id="token4"></div>
                                            </div>
                                            <div class="nonagonAnchor" id="nonagonAnchor3">
                                                <div class="nonagonSegment" id="token3"></div>
                                            </div>
                                            <div class="nonagonAnchor" id="nonagonAnchor2">
                                                <div class="nonagonSegment" id="token2"></div>
                                            </div>
                                            <div class="nonagonAnchor" id="nonagonAnchor1">
                                                <div class="nonagonSegment" id="token1"></div>
                                            </div>

                                            <div id="nonagonLabelContainer">
                                                <div class="nonagonLabelAnchor" id="nonagonLabelAnchor9">
                                                    <div class="nonagonSegmentLabel" id="nonagonSegmentLabel9">9</div>
                                                </div>
                                                <div class="nonagonLabelAnchor" id="nonagonLabelAnchor8">
                                                    <div class="nonagonSegmentLabel" id="nonagonSegmentLabel8">8</div>
                                                </div>
                                                <div class="nonagonLabelAnchor" id="nonagonLabelAnchor7">
                                                    <div class="nonagonSegmentLabel" id="nonagonSegmentLabel7">7</div>
                                                </div>
                                                <div class="nonagonLabelAnchor" id="nonagonLabelAnchor6">
                                                    <div class="nonagonSegmentLabel" id="nonagonSegmentLabel6">6</div>
                                                </div>
                                                <div class="nonagonLabelAnchor" id="nonagonLabelAnchor5">
                                                    <div class="nonagonSegmentLabel" id="nonagonSegmentLabel5">5</div>
                                                </div>
                                                <div class="nonagonLabelAnchor" id="nonagonLabelAnchor4">
                                                    <div class="nonagonSegmentLabel" id="nonagonSegmentLabel4">4</div>
                                                </div>
                                                <div class="nonagonLabelAnchor" id="nonagonLabelAnchor3">
                                                    <div class="nonagonSegmentLabel" id="nonagonSegmentLabel3">3</div>
                                                </div>
                                                <div class="nonagonLabelAnchor" id="nonagonLabelAnchor2">
                                                    <div class="nonagonSegmentLabel" id="nonagonSegmentLabel2">2</div>
                                                </div>
                                                <div class="nonagonLabelAnchor" id="nonagonLabelAnchor1">
                                                    <div class="nonagonSegmentLabel" id="nonagonSegmentLabel1">1</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="cypherTokenBalanceValueContainer">
                                            <div id="cypherTokenBalanceHeading">Cypher Tokens</div>
                                            <div id="cypherTokenBalance"></div>
                                            <div id="cypherTokenBalanceDescription">Please click on the relevant Cypher number to view your Token balance.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="container" id="personalInformationContainer">
                                <div class="internalContainer" id="personalInformationInternalContainer">
                                    <div id="editButtonContainer">
                                        <button id="editButton" title="Edit"></button>
                                        <img id="editButtonIcon" src="./assets/images/EditIcon.png"/>
                                    </div>
                                    <div id="cancelEditInfoButtonContainer" hidden>
                                        <button id="cancelEditInfoButton" title="Cancel"></button>
                                        <img id="cancelEditInfoButtonIcon" src="./assets/images/X-Icon.png"/>
                                    </div>
                                    <form id="personalInformationForm" autocomplete="off" onkeydown="return event.key != 'Enter';">
                                        <div class="personalInformationFormFlexContainer">
                                            <div id="saveButtonContainer" hidden>
                                                <button id="saveButton" type="submit" title="Save" disabled></button>
                                                <img id="saveButtonIcon" src="./assets/images/SaveIconDisabled.png"/>
                                            </div>
                                            <div id="profilePictureContainer">
                                                <input type="file" id="profilePictureUpload" name="personalProfilePicture" accept="image/png, image/jpeg" capture hidden disabled></input>
                                                <img id="profilePicture" src=""/>
                                                <button id="profilePictureEdit" hidden disabled>Choose Picture<br>(max 2MB)</button>
                                            </div>
                                            <div class="textInputAltContainer">
                                                <input class="textInputAlt" type="text" id="personalUserID" name="personalUserID" placeholder="" maxlength="10" readonly disabled></input>
                                                <span class="floatingLabelAlt">User ID</span>
                                            </div>
                                            <div class="textInputAltContainer" id="personalNamesContainer">
                                                <span class="personalNames" id="personalFirstName"></span>
                                                <span class="personalNames" id="personalLastName"></span>
                                            </div>  
                                        </div>
                                        <div class="personalInformationFormFlexContainer" id="personalInformationFormFlexContainer2">
                                            <div class="textInputAltContainer">
                                                <input class="textInputAlt" type="email" name="personalEmail" placeholder="" maxlength="255" readonly disabled></input>
                                                <span class="floatingLabelAlt">Email</span>
                                            </div>
                                            <div class="textInputAltContainer">
                                                <input class="telInputAlt" type="tel" name="personalPhoneNumber" placeholder="" maxlength="20" disabled></input>
                                                <span class="floatingLabelAlt">Phone Number</span>
                                                <span class="extensionPlusAlt">+</span>
                                            </div>
                                            <div class="textInputAltContainer">
                                                <input class="textInputAlt" type="text" name="personalCompany" maxlength="255" placeholder="" disabled></input>
                                                <span class="floatingLabelAlt">Company</span>
                                            </div>
                                            <div class="textInputAltContainer">
                                                <input class="textInputAlt" type="text" name="personalAddress" placeholder="" maxlength="255" required disabled></input>
                                                <span class="floatingLabelAlt">Address</span>
                                            </div>
                                            <div class="textInputAltContainer">
                                                <input class="textInputAlt" type="text" name="personalCity" placeholder="" maxlength="255" required disabled></input>
                                                <span class="floatingLabelAlt">City</span>
                                            </div>
                                            <div class="textInputAltContainer">
                                                <input class="textInputAlt" type="text" name="personalProvince" placeholder="" maxlength="255" required disabled></input>
                                                <span class="floatingLabelAlt">Province</span>
                                            </div>
                                            <div class="textInputAltContainer">
                                                <select class="dropdownListAlt" name="personalCountry" required disabled>
                                                    <option value="Afghanistan">Afghanistan</option>
                                                    <option value="Aland Islands">Aland Islands</option><!--May need to add support for the special character Å, and do the same for ShippingCountry-->
                                                    <option value="Albania">Albania</option>
                                                    <option value="Algeria">Algeria</option>
                                                    <option value="American Samoa">American Samoa</option>
                                                    <option value="Andorra">Andorra</option>
                                                    <option value="Angola">Angola</option>
                                                    <option value="Anguilla">Anguilla</option>
                                                    <option value="Antarctica">Antarctica</option>
                                                    <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                                    <option value="Argentina">Argentina</option>
                                                    <option value="Armenia">Armenia</option>
                                                    <option value="Aruba">Aruba</option>
                                                    <option value="Australia">Australia</option>
                                                    <option value="Austria">Austria</option>
                                                    <option value="Azerbaijan">Azerbaijan</option>
                                                    <option value="Bahamas">Bahamas</option>
                                                    <option value="Bahrain">Bahrain</option>
                                                    <option value="Bangladesh">Bangladesh</option>
                                                    <option value="Barbados">Barbados</option>
                                                    <option value="Belarus">Belarus</option>
                                                    <option value="Belgium">Belgium</option>
                                                    <option value="Belize">Belize</option>
                                                    <option value="Benin">Benin</option>
                                                    <option value="Bermuda">Bermuda</option>
                                                    <option value="Bhutan">Bhutan</option>
                                                    <option value="Bolivia">Bolivia</option>
                                                    <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                                    <option value="Botswana">Botswana</option>
                                                    <option value="Bouvet Island">Bouvet Island</option>
                                                    <option value="Brazil">Brazil</option>
                                                    <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                                                    <option value="Brunei Darussalam">Brunei Darussalam</option>
                                                    <option value="Bulgaria">Bulgaria</option>
                                                    <option value="Burkina Faso">Burkina Faso</option>
                                                    <option value="Burundi">Burundi</option>
                                                    <option value="Cambodia">Cambodia</option>
                                                    <option value="Cameroon">Cameroon</option>
                                                    <option value="Canada">Canada</option>
                                                    <option value="Cape Verde">Cape Verde</option>
                                                    <option value="Cayman Islands">Cayman Islands</option>
                                                    <option value="Central African Republic">Central African Republic</option>
                                                    <option value="Chad">Chad</option>
                                                    <option value="Chile">Chile</option>
                                                    <option value="China">China</option>
                                                    <option value="Christmas Island">Christmas Island</option>
                                                    <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
                                                    <option value="Colombia">Colombia</option>
                                                    <option value="Comoros">Comoros</option>
                                                    <option value="Congo">Congo</option>
                                                    <option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option>
                                                    <option value="Cook Islands">Cook Islands</option>
                                                    <option value="Costa Rica">Costa Rica</option>
                                                    <option value="Cote D'ivoire">Cote D'ivoire</option>
                                                    <option value="Croatia">Croatia</option>
                                                    <option value="Cuba">Cuba</option>
                                                    <option value="Cyprus">Cyprus</option>
                                                    <option value="Czech Republic">Czech Republic</option>
                                                    <option value="Denmark">Denmark</option>
                                                    <option value="Djibouti">Djibouti</option>
                                                    <option value="Dominica">Dominica</option>
                                                    <option value="Dominican Republic">Dominican Republic</option>
                                                    <option value="Ecuador">Ecuador</option>
                                                    <option value="Egypt">Egypt</option>
                                                    <option value="El Salvador">El Salvador</option>
                                                    <option value="Equatorial Guinea">Equatorial Guinea</option>
                                                    <option value="Eritrea">Eritrea</option>
                                                    <option value="Estonia">Estonia</option>
                                                    <option value="Ethiopia">Ethiopia</option>
                                                    <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
                                                    <option value="Faroe Islands">Faroe Islands</option>
                                                    <option value="Fiji">Fiji</option>
                                                    <option value="Finland">Finland</option>
                                                    <option value="France">France</option>
                                                    <option value="French Guiana">French Guiana</option>
                                                    <option value="French Polynesia">French Polynesia</option>
                                                    <option value="French Southern Territories">French Southern Territories</option>
                                                    <option value="Gabon">Gabon</option>
                                                    <option value="Gambia">Gambia</option>
                                                    <option value="Georgia">Georgia</option>
                                                    <option value="Germany">Germany</option>
                                                    <option value="Ghana">Ghana</option>
                                                    <option value="Gibraltar">Gibraltar</option>
                                                    <option value="Greece">Greece</option>
                                                    <option value="Greenland">Greenland</option>
                                                    <option value="Grenada">Grenada</option>
                                                    <option value="Guadeloupe">Guadeloupe</option>
                                                    <option value="Guam">Guam</option>
                                                    <option value="Guatemala">Guatemala</option>
                                                    <option value="Guernsey">Guernsey</option>
                                                    <option value="Guinea">Guinea</option>
                                                    <option value="Guinea-bissau">Guinea-bissau</option>
                                                    <option value="Guyana">Guyana</option>
                                                    <option value="Haiti">Haiti</option>
                                                    <option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option>
                                                    <option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
                                                    <option value="Honduras">Honduras</option>
                                                    <option value="Hong Kong">Hong Kong</option>
                                                    <option value="Hungary">Hungary</option>
                                                    <option value="Iceland">Iceland</option>
                                                    <option value="India">India</option>
                                                    <option value="Indonesia">Indonesia</option>
                                                    <option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option>
                                                    <option value="Iraq">Iraq</option>
                                                    <option value="Ireland">Ireland</option>
                                                    <option value="Isle of Man">Isle of Man</option>
                                                    <option value="Israel">Israel</option>
                                                    <option value="Italy">Italy</option>
                                                    <option value="Jamaica">Jamaica</option>
                                                    <option value="Japan">Japan</option>
                                                    <option value="Jersey">Jersey</option>
                                                    <option value="Jordan">Jordan</option>
                                                    <option value="Kazakhstan">Kazakhstan</option>
                                                    <option value="Kenya">Kenya</option>
                                                    <option value="Kiribati">Kiribati</option>
                                                    <option value="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option>
                                                    <option value="Korea, Republic of">Korea, Republic of</option>
                                                    <option value="Kuwait">Kuwait</option>
                                                    <option value="Kyrgyzstan">Kyrgyzstan</option>
                                                    <option value="Lao People's Democratic Republic">Lao People's Democratic Republic</option>
                                                    <option value="Latvia">Latvia</option>
                                                    <option value="Lebanon">Lebanon</option>
                                                    <option value="Lesotho">Lesotho</option>
                                                    <option value="Liberia">Liberia</option>
                                                    <option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
                                                    <option value="Liechtenstein">Liechtenstein</option>
                                                    <option value="Lithuania">Lithuania</option>
                                                    <option value="Luxembourg">Luxembourg</option>
                                                    <option value="Macao">Macao</option>
                                                    <option value="Macedonia, The Former Yugoslav Republic of">Macedonia, The Former Yugoslav Republic of</option>
                                                    <option value="Madagascar">Madagascar</option>
                                                    <option value="Malawi">Malawi</option>
                                                    <option value="Malaysia">Malaysia</option>
                                                    <option value="Maldives">Maldives</option>
                                                    <option value="Mali">Mali</option>
                                                    <option value="Malta">Malta</option>
                                                    <option value="Marshall Islands">Marshall Islands</option>
                                                    <option value="Martinique">Martinique</option>
                                                    <option value="Mauritania">Mauritania</option>
                                                    <option value="Mauritius">Mauritius</option>
                                                    <option value="Mayotte">Mayotte</option>
                                                    <option value="Mexico">Mexico</option>
                                                    <option value="Micronesia, Federated States of">Micronesia, Federated States of</option>
                                                    <option value="Moldova, Republic of">Moldova, Republic of</option>
                                                    <option value="Monaco">Monaco</option>
                                                    <option value="Mongolia">Mongolia</option>
                                                    <option value="Montenegro">Montenegro</option>
                                                    <option value="Montserrat">Montserrat</option>
                                                    <option value="Morocco">Morocco</option>
                                                    <option value="Mozambique">Mozambique</option>
                                                    <option value="Myanmar">Myanmar</option>
                                                    <option value="Namibia">Namibia</option>
                                                    <option value="Nauru">Nauru</option>
                                                    <option value="Nepal">Nepal</option>
                                                    <option value="Netherlands" selected>Netherlands</option>
                                                    <option value="Netherlands Antilles">Netherlands Antilles</option>
                                                    <option value="New Caledonia">New Caledonia</option>
                                                    <option value="New Zealand">New Zealand</option>
                                                    <option value="Nicaragua">Nicaragua</option>
                                                    <option value="Niger">Niger</option>
                                                    <option value="Nigeria">Nigeria</option>
                                                    <option value="Niue">Niue</option>
                                                    <option value="Norfolk Island">Norfolk Island</option>
                                                    <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                                                    <option value="Norway">Norway</option>
                                                    <option value="Oman">Oman</option>
                                                    <option value="Pakistan">Pakistan</option>
                                                    <option value="Palau">Palau</option>
                                                    <option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
                                                    <option value="Panama">Panama</option>
                                                    <option value="Papua New Guinea">Papua New Guinea</option>
                                                    <option value="Paraguay">Paraguay</option>
                                                    <option value="Peru">Peru</option>
                                                    <option value="Philippines">Philippines</option>
                                                    <option value="Pitcairn">Pitcairn</option>
                                                    <option value="Poland">Poland</option>
                                                    <option value="Portugal">Portugal</option>
                                                    <option value="Puerto Rico">Puerto Rico</option>
                                                    <option value="Qatar">Qatar</option>
                                                    <option value="Reunion">Reunion</option>
                                                    <option value="Romania">Romania</option>
                                                    <option value="Russian Federation">Russian Federation</option>
                                                    <option value="Rwanda">Rwanda</option>
                                                    <option value="Saint Helena">Saint Helena</option>
                                                    <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                                    <option value="Saint Lucia">Saint Lucia</option>
                                                    <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                                                    <option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option>
                                                    <option value="Samoa">Samoa</option>
                                                    <option value="San Marino">San Marino</option>
                                                    <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                                    <option value="Saudi Arabia">Saudi Arabia</option>
                                                    <option value="Senegal">Senegal</option>
                                                    <option value="Serbia">Serbia</option>
                                                    <option value="Seychelles">Seychelles</option>
                                                    <option value="Sierra Leone">Sierra Leone</option>
                                                    <option value="Singapore">Singapore</option>
                                                    <option value="Slovakia">Slovakia</option>
                                                    <option value="Slovenia">Slovenia</option>
                                                    <option value="Solomon Islands">Solomon Islands</option>
                                                    <option value="Somalia">Somalia</option>
                                                    <option value="South Africa">South Africa</option>
                                                    <option value="South Georgia and The South Sandwich Islands">South Georgia and The South Sandwich Islands</option>
                                                    <option value="Spain">Spain</option>
                                                    <option value="Sri Lanka">Sri Lanka</option>
                                                    <option value="Sudan">Sudan</option>
                                                    <option value="Suriname">Suriname</option>
                                                    <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
                                                    <option value="Swaziland">Swaziland</option>
                                                    <option value="Sweden">Sweden</option>
                                                    <option value="Switzerland">Switzerland</option>
                                                    <option value="Syrian Arab Republic">Syrian Arab Republic</option>
                                                    <option value="Taiwan">Taiwan</option>
                                                    <option value="Tajikistan">Tajikistan</option>
                                                    <option value="Tanzania, United Republic of">Tanzania, United Republic of</option>
                                                    <option value="Thailand">Thailand</option>
                                                    <option value="Timor-leste">Timor-leste</option>
                                                    <option value="Togo">Togo</option>
                                                    <option value="Tokelau">Tokelau</option>
                                                    <option value="Tonga">Tonga</option>
                                                    <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                                    <option value="Tunisia">Tunisia</option>
                                                    <option value="Turkey">Turkey</option>
                                                    <option value="Turkmenistan">Turkmenistan</option>
                                                    <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                                                    <option value="Tuvalu">Tuvalu</option>
                                                    <option value="Uganda">Uganda</option>
                                                    <option value="Ukraine">Ukraine</option>
                                                    <option value="United Arab Emirates">United Arab Emirates</option>
                                                    <option value="United Kingdom">United Kingdom</option>
                                                    <option value="United States">United States</option>
                                                    <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
                                                    <option value="Uruguay">Uruguay</option>
                                                    <option value="Uzbekistan">Uzbekistan</option>
                                                    <option value="Vanuatu">Vanuatu</option>
                                                    <option value="Venezuela">Venezuela</option>
                                                    <option value="Viet Nam">Viet Nam</option>
                                                    <option value="Virgin Islands, British">Virgin Islands, British</option>
                                                    <option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
                                                    <option value="Wallis and Futuna">Wallis and Futuna</option>
                                                    <option value="Western Sahara">Western Sahara</option>
                                                    <option value="Yemen">Yemen</option>
                                                    <option value="Zambia">Zambia</option>
                                                    <option value="Zimbabwe">Zimbabwe</option>
                                                </select>
                                                <span class="floatingLabelAlt">Country</span>
                                                <img class="dropdownListArrowAlt" src="./assets/images/DropdownArrowIcon.png"/>
                                            </div>
                                            <div class="textInputAltContainer">
                                                <input class="textInputAlt" type="text" id="personalZIPCode" name="personalZIPCode" placeholder="" maxlength="10" required disabled></input>
                                                <span class="floatingLabelAlt">ZIP</span> 
                                            </div>
                                        </div>
                                    </form>
                                    <div id="changePasswordContainer" hidden>
                                        <div id="changePasswordButtonsContainer">
                                            <div id="cancelEditPasswordButtonContainer" hidden>
                                                <button id="cancelEditPasswordButton" title="Cancel"></button>
                                                <img id="cancelEditPasswordButtonIcon" src="./assets/images/X-Icon.png"/>
                                            </div>
                                            <div id="editPasswordButtonContainer">
                                                <button id="editPasswordButton" title="Change Password"></button>
                                                <img id="editPasswordButtonIcon" src="./assets/images/EditIcon.png"/>
                                            </div>
                                        </div>
                                        <form id="changePasswordForm" autocomplete="off" onkeydown="return event.key != 'Enter';">
                                            <div id="changePasswordInputsContainer">
                                                <div class="textInputAltContainer" id="personalPasswordTextInputContainer">
                                                    <input class="textInputAlt" type="password" id="personalPassword" name="personalPassword" placeholder="" value="00000000" required disabled></input>
                                                    <span class="floatingLabelAlt">Password</span>
                                                </div>
                                                <div class="textInputAltContainer" id="personalConfirmPasswordTextInputContainer">
                                                    <input class="textInputAlt" type="password" id="personalConfirmPassword" name="personalConfirmPassword" placeholder="" value="00000000" required disabled></input>
                                                    <span class="floatingLabelAlt">Confirm Password</span>
                                                </div>
                                            </div>
                                            <div id="savePasswordButtonContainer" hidden>
                                                <button id="savePasswordButton" type="submit" title="Save Password" disabled></button>
                                                <img id="savePasswordButtonIcon" src="./assets/images/SaveIconDisabled.png"/>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="loadingOverlay" id="personalInformationLoadingOverlay" hidden>
                                    <div id="passwordVerificationContainerContainer">
                                        <div id="closeButtonContainer">
                                            <button id="closeButton" title="Close" disabled></button>
                                            <img id="closeButtonIcon" src="./assets/images/X-IconWhite.png"/>
                                        </div>
                                        <div class="container" id="passwordVerificationContainer">
                                            <div class="internalContainer" id="passwordVerificationInternalContainer">
                                                <form id="passwordVerificationForm" autocomplete="off">
                                                    <div id="passwordVerificationFormHeading">Please enter your current password to confirm changes.</div>
                                                    <div class="textInputContainer" id="verificationPasswordContainer">
                                                        <input class="textInput" type="password" id="verificationPassword" name="verificationPassword" placeholder="" required></input>
                                                        <span class="floatingLabel">Verify Current Password</span>
                                                    </div>
                                                    <div class="phpResponse" id="passwordVerificationErrorMessage"></div>
                                                    <div class="buttonContainer" id="passwordVerificationButtonContainer">
                                                        <button id="passwordVerificationButton" type="submit">SUBMIT</button>
                                                        <div class="loadingAnimationContainer" id="loginLoadingAnimationContainer">
                                                            <div class="bulletPoint" id="bulletPoint1">&#8226;</div>
                                                            <div class="bulletPoint" id="bulletPoint2">&#8226;</div>
                                                            <div class="bulletPoint" id="bulletPoint3">&#8226;</div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contentDisplay" id="personalTransactionLogTab"><!--Page Tab-->
                        <div class="contentDisplayNonFlexBox" id="transactionLogNonFlexBox">
                            <div class="container" id="transactionLogContainer">
                                <div class="internalContainer" id="transactionLogInternalContainer">
                                    <img id="tLogIcon" src="./assets/images/TLogIcon.png"/>
                                    <div id="transactionLogHeading">Transaction Log</div>
                                    <div id="tLogLoadingAnimationContainerContainer">
                                        <div class="loadingAnimationContainer" id="tLogLoadingAnimationContainer">
                                            <div class="bulletPoint" id="bulletPoint1">&#8226;</div>
                                            <div class="bulletPoint" id="bulletPoint2">&#8226;</div>
                                            <div class="bulletPoint" id="bulletPoint3">&#8226;</div>
                                        </div>
                                    </div>

                                    <div id="tLogSelectionContainer">
                                        <div id="tLogSelectTokenTransactions">TOKEN TRANSACTIONS</div>
                                        <div id="tLogSelectInvoices">INVOICES</div>
                                    </div>
                                    <div id="tLogHeadersContainer" hidden>
                                        <div id="tLogInvoiceHeader">Invoice</div>
                                        <div id="tLogProductHeader">Product</div>
                                    </div>
                                    <div class="tLogSelectionDisplayContainers" id="tLogTokenTransactionsContainer">
                                        <div id="emptyLogPlaceholderTokens">NO ENTRIES TO DISPLAY</div>
                                    </div>
                                    <div class="tLogSelectionDisplayContainers" id="tLogInvoicesContainer" hidden>
                                        <div id="emptyLogPlaceholderInvoices">NO ENTRIES TO DISPLAY</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="contentDisplay" id="contactTab"><!--Page Tab-->
                        <div class="contentDisplayNonFlexBox">
                            <div class="container" id="contactContainer">
                                <div class="internalContainer" id="contactInternalContainer">
                                    <div id="contactHeading">Get in touch...</div>
                                    <div class="contactInfoFlexContainer">
                                        <div class="contactInfoContainer">
                                            <img id="emailIcon" src="./assets/images/EmailIcon.png"/>
                                            <div id="contactInfoEmailAddress" onfocus="this.select();">welcome@matrix-q.solutions</div>
                                            <img id="phoneIcon" src="./assets/images/PhoneIcon.png"/>
                                            <div id="contactInfoPhoneNumber" onfocus="this.select();">0031 626673380</div>
                                            <a href="https://wa.me/message/GW4KMYHFHWLTC1" target="_blank"><img id="whatsappIcon" src="./assets/images/WhatsappIcon.png"/></a>
                                            <img id="calendarIcon" src="./assets/images/CalendarIcon.png"/>
                                            <div id="contactInfoCalendar1">Schedule a Meeting:</div>
                                            <div id="contactInfoCalendar2">Demos | Applications | Training</div>
                                            <a href="https://calendly.com/matrix-q-solutions/15min-session" target="_blank"><img id="calendlyIcon" src="./assets/images/CalendlyIcon.png"/></a>
                                        </div>
                                        <div class="contactInfoContainer" id="addressContactInfoContainer">
                                            <img id="addressIcon" src="./assets/images/LocationIcon.png"/>
                                            <div id="contactInfoAddress1">Visit us at our outdoor training<br>location:</div>
                                            <div id="contactInfoAddress2">Grebbeberg Forest,<br>Rhenen,<br>Netherlands</div>
                                            <iframe id="contactInfoGMaps" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4777.828498759547!2d5.592644334756651!3d51.95374763874833!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c6548cc197942d%3A0x61d904de29c8c494!2sGrebbeberg!5e0!3m2!1sen!2sza!4v1639508083091!5m2!1sen!2sza" fullscreen="" loading="lazy"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="contentDisplay" id="aboutTab"><!--Page Tab-->
                        <div class="contentDisplayNonFlexBox">
                            <div class="container" id="aboutContainer">
                                <div class="internalContainer" id="aboutInternalContainer">
                                    <div id="aboutHeading">The Matrix-Q Wallet</div>
                                    <div class="aboutInfoText">This tool keeps record of all your transactions and balances within the Matrix-Q Ecosystem. A holistic tool for the new economy.</div>
                                    <div class="aboutInfoFlexContainer">
                                        <div class="aboutInfoContainer">
                                            <div class="aboutInfoHeading">Holistic Economy...</div>
                                            <div class="aboutInfoText">Our innovative economy model unvails 9 categories of value. You create, produce, purchase or provide.</div>
                                        </div>
                                        <div class="aboutInfoContainer">
                                            <div class="aboutInfoHeading">Matrix-Q Tokens...</div>
                                            <div class="aboutInfoText">The Wallet provides tools for administration of an internal alternative currency, based on gift certificates and discount bonds.</div>
                                        </div>
                                        <div class="aboutInfoContainer">
                                            <div class="aboutInfoHeading">For whom...</div>
                                            <div class="aboutInfoText">The Wallet can be used by the self-employed, entrepreneurs, intrapreneurs, travel-preneurs, impact-investors, companies, organizations, communities, NGO's and cities. Each network can administrate their own internal holistic economy with the Matrix-Q Wallet</div>
                                        </div>
                                        <div class="aboutInfoContainer">
                                            <div class="aboutInfoHeading">Broader applications...</div>
                                            <div class="aboutInfoText">The Matrix-Q Ecosystem utilizes the wallet in games, education programs, e-learning, SaaS applications, business administration, product development, consultancy, research and innovation.</div>
                                        </div>
                                    </div>
                                    <div class="aboutInfoNonFlexContainer">
                                        <div class="aboutInfoHeading">Developed by Matrix-Q Solutions</div>
                                        <div class="aboutInfoText">MQ9 [Matrix-Quotient 9] Solutions B.V. www.mq9.nl KVK 72972181 Padualaan 8, 3584 CH, Science Park, Utrecht, The Netherlands & by The Matrix-Q Research Institute www.matrix-q.com Stichting Luis Daniel Maldonado Fonken, Matrix-Q License Provider, KVK 66225345 All Rights, Padualaan 8, 3584 CH, Science Park, Utrecht, The Netherlands</div>
                                    </div>
                                    <div class="aboutInfoNonFlexContainer">
                                        <div class="aboutInfoHeading">Copyright</div>
                                        <div class="aboutInfoText">&copy; Copyright by the MQ9 [Matrix-Quotient 9] Solutions B.V. www.mq9.nl KVK 72972181 Padualaan 8, 3584 CH, Science Park, Utrecht, The Netherlands & by The Matrix-Q Research Institute www.matrixq.com Stichting Luis Daniel Maldonado Fonken, KVK 66225345 All Rights, Padualaan 8, 3584 CH, Science Park, Utrecht, The Netherlands</div>
                                    </div>
                                    <div id="tosContainer">
                                        <div id="tosHeader">
                                            <div id="tosHeading">Terms of Service & Privacy Policy</div>
                                            <img id="tosDisplayMoreArrow" src="./assets/images/DropdownArrowIcon.png"/>
                                        </div>
                                        <div id="tosDetailsContainer">
                                            <div id="tosEffectiveDate">Effective Date: 18/12/2021</div>
                                            <div class="tosInfoHeading">0.0 CONTENT</div>
                                            <div class="tosInfoText">0.1 The content of the products and services vary depending on the complexity they solve, tools used and knowledge applied.</div>
                                            <div class="tosInfoText">0.2 The purchase of any product does not give you the right to receive any specific content. The Matrix-Q Ecosystem has organized its body-of-work, knowledge, and tools, according to 12 levels of complexity, for which skills and capacity need to be verified, and compliance (positive impact mindset and purpose) needs to be verified.</div>
                                            <div class="tosInfoText">0.3 For some products and services the customer needs to have completed specific previous training (compulsory content), or purchased and consumed a compulsory service. As a result, it may happen that the customer wishes to receive specific content but is not yet eligible to receive it. A Matrix-Q Consultant will advise our customers regarding the eligibility conditions and tailor-make a process/a series of steps for each customer, to reach eligibility and receive the content they prefer.</div>
                                            <div class="tosInfoText">0.4 For some products and services it is compulsory that the customer demonstrates not only intention, but tangible steps taken towards a contribution to the acceleration of the transition of our planet back to a sustainable future. It may happen that a customer plans to use our content, tools and/or knowledge, for projects, activities and/or purposes that will create as an outcome, a negative impact in nature, societies or economies. In these cases, we reserve our right to retain a particular product or service, cancel a subscription/membership/agreement of service, and limit access to features/resources/benefits given to the customer until the necessary conditions have been fulfilled. A Matrix-Q Consultant will advise our customers regarding the eligibility conditions and tailor-make a process/a series of steps for each customer, to reach eligibility and receive the content they prefer.</div>
                                            <div class="tosInfoHeading">1.0 PRICE</div>
                                            <div class="tosInfoText">1.1 The price of our products vary depending on the complexity they solve, the value of the tools used, whether or not they are hybrid or only digital ('hybrid' implying a human will also participate in the service delivery, adding value to the service through their skills, knowledge and training. For example as License Holders), as defined in each case, and can be adjusted from time to time. We reserve the right to change the price and value assigned to products according to our own judgment and policy of value.</div>
                                            <div class="tosInfoText">1.2 We do not sell time. Time is not a reference for us to define the price of our products and services.</div>
                                            <div class="tosInfoHeading">2.0 REFUND</div>
                                            <div class="tosInfoText">2.1 There is no refund. After purchase, quit notice, cancellation, end of product validity, change of product, rescheduling of services, or any other case, the company gives no refund.</div>
                                            <div class="tosInfoHeading">3.0 PRIVACY POLICY</div>
                                            <div class="tosInfoText">3.1 WHAT DO WE DO WITH YOUR INFORMATION?<br>When you purchase something from our store, as part of the buying and selling process we collect the personal information you give us such as your name, address and email address. When you browse our store, we also automatically receive your computer's IP address and other public data which helps us learn browser and operating system information, which in turn allows us to improve your user experience on our application.</div>
                                            <div class="tosInfoText">3.2 EMAIL MARKETING<br>With your permission, we may send you emails about our store, new products and other updates.</div>
                                            <div class="tosInfoText">3.3 CONSENT<br>When you provide us with personal information to complete a transaction, verify your credit card, place an order, arrange for a delivery or return a purchase, we imply that you consent to our collection of it and use of it for that specific reason only. If we ask for your personal information for a secondary reason, such as marketing, we will either ask you directly for your expressed consent, or provide you with an opportunity to say no.</div>
                                            <div class="tosInfoText">3.4 CONSENT WITHDRAWAL<br>If after you opt-in you change your mind, you may at any time withdraw your consent for: us to contact you, for the continued collection of your information, use of your information, or disclosure of your information, by contacting us at: 'onlineshop@matrix-q.solutions', or by mailing us at: 'Matrix-Q MarketPlace, Utrecht, 3584 CH, Padualaan 8, Netherlands'.</div>
                                            <div class="tosInfoText">3.5 DISCLOSURE<br>We may disclose your personal information if we are required by law to do so, or if you violate our Terms of Service.</div>
                                            <div class="tosInfoText">3.6 ONE.COM<br>Our store is hosted by 'one.com'. They provide us with the online e-commerce platform that allows us to sell our products and services to you. Your data is stored through one.com's data storage facilities, databases, and in the general one.com application. They store your data on a secure server behind a firewall.</div>
                                            <div class="tosInfoText">3.7 PAYMENT<br>If you choose a direct payment gateway to complete your purchase, then our third-party payment gateway might use your credit card information for that purpose (See 3.8).</div>
                                            <div class="tosInfoText">3.8 THIRD-PARTY SERVICES<br>In general, the third-party providers used by us will only collect, use and disclose your information to the extent necessary to allow them to perform the services they provide to us. However, certain third-party service providers, such as payment gateways and other payment transaction processors, have their own privacy policies in respect to the information we are required to provide to them for your purchase-related transactions. For these providers, we recommend that you read their privacy policies so you can understand the manner in which your personal information will be handled by these providers. In particular, remember that certain providers may be located in, or have facilities that are located in a different jurisdiction to either you or us. So if you elect to proceed with a transaction that involves the services of a third-party service provider, then your information may become subject to the laws of the jurisdiction(s) in which that service provider or its facilities are located. Once you leave our store's website or are redirected to a third-party website or application, you are no longer governed by this Privacy Policy or our website's Terms of Service.</div>
                                            <div class="tosInfoText">3.9 LINKS<br>When you click on links on our store, they may direct you away from our site. We are not responsible for the privacy practices of other sites and encourage you to read their privacy statements.</div>
                                            <div class="tosInfoText">3.10 SECURITY<br>To protect your personal information, we take reasonable precautions and follow industry best practices to ensure it is not inappropriately lost, misused, accessed, disclosed, altered or destroyed. If you provide us with your credit card information, the information is encrypted using secure socket layer technology (SSL), and stored with an AES-256 encryption.</div>
                                            <div class="tosInfoText">3.11 AGE OF CONSENT<br>By using this application, you are representing at least the age of majority in your state or province of residence, or representing the age of majority in your state or province of residence and implying consent given, allowing any of your minor dependants to use this site.</div>
                                            <div class="tosInfoHeading">4.0 CHANGES TO THESE TERMS OF SERVICE</div>
                                            <div class="tosInfoText">We reserve the right to modify our terms of service at any time, so please review it frequently. Changes and clarifications will take effect immediately upon their posting on the website. If we make material changes to this policy, we will notify you here that it has been updated, so that you are aware of what information we collect, how we use it, and under what circumstances, if any, we use and/or disclose it. If our store is acquired or merged with another company, your information may be transferred to the new owners so that we may continue to sell products to you.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="contentDisplay" id="improveYourExperienceTab"><!--Page Tab-->
                        <div class="contentDisplayNonFlexBox">
                            <div class="container" id="improveYourExperienceContainer">
                                <div class="internalContainer" id="improveYourExperienceInternalContainer">
                                    <div id="improveYourExperienceHeading">Invest in some of our other products...</div>
                                    <div id="improveYourExperienceFlexContainer">
                                        <div class="improveYourExperienceLinkContainer">
                                            <a href="https://matrix-q.solutions/nutshell" target="_blank"><img class="improveYourExperienceLink" src="./assets/images/MQEcosystem.png"/></a>
                                        </div>
                                        <div class="improveYourExperienceLinkContainer">
                                            <a href="https://matrix-q.solutions/index.html" target="_blank"><img class="improveYourExperienceLink" src="./assets/images/MQWebShop.png"/></a>
                                        </div>
                                        <div class="improveYourExperienceLinkContainer">
                                            <a href="https://matrixq9.gumroad.com/" target="_blank"><img class="improveYourExperienceLink" src="./assets/images/MQELearning.png"/></a>
                                        </div>
                                        <div class="improveYourExperienceLinkContainer">
                                            <a href="https://corporate-gifts.matrix-q.solutions/" target="_blank"><img class="improveYourExperienceLink" src="./assets/images/MQCorporateGifts.png"/></a>
                                        </div>
                                        <div class="improveYourExperienceLinkContainer">
                                            <a href="https://corporate-gifts.matrix-q.solutions/merchandise/" target="_blank"><img class="improveYourExperienceLink" src="./assets/images/MQMerchandise.png"/></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>



<script type="text/javascript">
    
//Variable Declarations
    const mediaQuery = window.matchMedia('(min-width: 1000px)');
    var desktopView;
    var navigationMenuOpen = false;
    var previousTabSelector = "personalWalletTabSelector";
    var previousSelectedTab = "personalWalletTab";
    var focusTarget = "";
    var walletPagePersonalInfoArray;
    var walletPagePersonalBalanceArray;
    var profilePictureFile;
    var personalInfoChangeSuccessful = false;
    var personalPasswordChangeSuccessful = false;
    var submittedPersonalInfoEdit = false;
    var submittedPasswordEdit = false;
    var firstTransactionLogLoad = true;
    var previousTLogEntryTokens = "";
    var previousTLogEntryInvoices = "";
    var fetchedTokenTCustomerInformationArray = [];
    var tosOpen = false;
    
    window.onload = function()
    {
        window.scrollTo(0, 0);

        InactivityTimer();

        if (mediaQuery.matches)
        {
            document.getElementById('navigationMenu').style.transition = "none";
            document.getElementById('filterLayer').style.transition = "none";
            desktopView = true;
        }
        else
        {
            document.getElementById('navigationMenu').style.transition = "0.3s ease-in-out";
            document.getElementById('filterLayer').style.transition = "0.3s ease-in-out";
            desktopView = false;
        }

        mediaQuery.addListener(ScreenTest);

        DisplayCorrectTabSelectorAndTabPageOnAppLoad();

        FetchAndDisplayUserInfo();

        InstantiateEventListeners();

        document.addEventListener("wheel", function(event) {
            if (document.activeElement.type === "number")
            {
                document.activeElement.blur();
            }
        });
    };
    
//Functions
    function InactivityTimer()
    {
        var inactivityDelay;

        document.onload = ResetTimer;
        document.onmousemove = ResetTimer;
        document.onmousedown = ResetTimer;
        document.ontouchstart = ResetTimer;
        document.onclick = ResetTimer;
        document.onkeydown = ResetTimer;
        document.addEventListener("scroll", ResetTimer, true);

        function ResetTimer()
        {
            clearTimeout(inactivityDelay);
            inactivityDelay = setTimeout(InactivityLogout, 300000); //5min Delay
        }

        function InactivityLogout()
        {
            var alertMessage = "Logged out due to inactivity!";
            localStorage.setItem("alertMessage", alertMessage);
            Logout();
        }
    }

    function ScreenTest(e)
    {
        let navigationMenu = document.getElementById('navigationMenu');
        let filterLayer = document.getElementById('filterLayer');

        if (e.matches)
        {
            navigationMenu.style.transition = "none";
            filterLayer.style.transition = "none";

            navigationMenu.style.left = "0px";
            navigationMenu.style.opacity = "1.0";
            filterLayer.style.pointerEvents = "auto";
            filterLayer.style.touchAction = "auto";
            filterLayer.style.opacity = "1.0";
            navigationMenuOpen = false;
            desktopView = true;
        }
        else
        {
            navigationMenu.style.left = "-300px";
            navigationMenu.style.opacity = "0.75";
            filterLayer.style.pointerEvents = "auto";
            filterLayer.style.touchAction = "auto";
            filterLayer.style.opacity = "1.0";
            navigationMenuOpen = false;
            desktopView = false;
            setTimeout(AddMenuAndFilterLayerTransitionAnimation, 10);
        }
    }

    function AddMenuAndFilterLayerTransitionAnimation()
    {
        document.getElementById('navigationMenu').style.transition = "0.3s ease-in-out";
        document.getElementById('filterLayer').style.transition = "0.3s ease-in-out";
    }

    function DisplayCorrectTabSelectorAndTabPageOnAppLoad()
    {
        document.getElementById("personalWalletTabSelector").style.backgroundColor = "rgba(143,0,255,0.8)";
        document.getElementById("personalWalletTabSelector").style.color = "white";
        document.getElementById(previousSelectedTab).style.display = "block";
    }

    function FetchAndDisplayUserInfo()
    {
        walletPagePersonalInfoArray = <?=$loggedInUserInfoArray?>;
        walletPagePersonalBalanceArray = <?=$loggedInUserBalanceInfoArray?>;

        document.getElementById('walletPageHeading').innerHTML = "Hello " + walletPagePersonalInfoArray["FirstName"] + "! Welcome to your personal wallet.";

        document.getElementById('totalInvestmentBalance').innerHTML = walletPagePersonalBalanceArray["TotalInvestment"];
        document.getElementById('creditsBalance').innerHTML = walletPagePersonalBalanceArray["CreditsInvestment"];

        document.getElementById('tokenBalanceValue').innerHTML = walletPagePersonalBalanceArray["TokenBalance"];

        document.getElementById("profilePicture").src = "uploads/userprofilepictures/" + walletPagePersonalInfoArray["ProfilePicture"];
        document.getElementById("personalInformationForm").elements["personalUserID"].value = walletPagePersonalInfoArray["UserID"];
        document.getElementById("personalFirstName").innerHTML = walletPagePersonalInfoArray["FirstName"];
        document.getElementById("personalLastName").innerHTML = walletPagePersonalInfoArray["LastName"];
        document.getElementById("personalInformationForm").elements["personalEmail"].value = walletPagePersonalInfoArray["Email"];
        document.getElementById("personalInformationForm").elements["personalPhoneNumber"].value = walletPagePersonalInfoArray["PhoneNumber"];
        document.getElementById("personalInformationForm").elements["personalCompany"].value = walletPagePersonalInfoArray["Company"];
        document.getElementById("personalInformationForm").elements["personalAddress"].value = walletPagePersonalInfoArray["Address"];
        document.getElementById("personalInformationForm").elements["personalCity"].value = walletPagePersonalInfoArray["City"];
        document.getElementById("personalInformationForm").elements["personalProvince"].value = walletPagePersonalInfoArray["Province"];
        document.getElementById("personalInformationForm").elements["personalCountry"].value = walletPagePersonalInfoArray["Country"];
        document.getElementById("personalInformationForm").elements["personalZIPCode"].value = walletPagePersonalInfoArray["ZIPCode"];
    }

    function InstantiateEventListeners()
    {
        //NAVIGATION EVENT LISTENERS.
        document.getElementById("navigationBarMenuToggle").addEventListener("click", function() {
            let navigationMenu = document.getElementById('navigationMenu');
            let filterLayer = document.getElementById('filterLayer');
        
            if (navigationMenuOpen == false)
            {
                navigationMenu.style.left = "0px";
                navigationMenu.style.opacity = "1.0";
                filterLayer.style.pointerEvents = "none";
                filterLayer.style.touchAction = "none";
                filterLayer.style.opacity = "0.25";
                navigationMenuOpen = true;
            }
            else if (navigationMenuOpen == true)
            {
                navigationMenu.style.left = "-300px";
                navigationMenu.style.opacity = "0.75";
                filterLayer.style.pointerEvents = "auto";
                filterLayer.style.touchAction = "auto";
                filterLayer.style.opacity = "1.0";
                navigationMenuOpen = false;
            }
        });
        document.getElementById("logoutButton").addEventListener("click", function() {
            Logout();
        });

        //NAVIGATION MENU EVENT LISTENERS.
        var navigationBarTabSelectors = document.getElementsByClassName("navigationBarTabSelector");
        for (var i = 0; i < navigationBarTabSelectors.length; i++)
        {
            if (navigationBarTabSelectors[i].id == "personalTransactionLogTabSelector")
            {
                navigationBarTabSelectors[i].addEventListener("click", function() {
                    DisplayTab(this.id.replace("Selector", ""));
                    ShowSelectedTabInNavigationBar(this.id);
                    if (firstTransactionLogLoad == true)
                    {
                        var animationContainer = document.getElementById("tLogLoadingAnimationContainer");
                        animationContainer.style.display = "block";
                        var bulletPoints = animationContainer.getElementsByClassName("bulletPoint");
                        for (i = 0; i < bulletPoints.length; i++)
                        {
                            bulletPoints[i].style.animationPlayState = "running";
                        }
                        
                        FetchUserTransactionLogAJAX();
                    }
                });
            }
            else
            {
                navigationBarTabSelectors[i].addEventListener("click", function() {
                    DisplayTab(this.id.replace("Selector", ""));
                    ShowSelectedTabInNavigationBar(this.id);
                });
            }
            navigationBarTabSelectors[i].addEventListener("mouseover", function() {
                MouseOverEffect(this.id);
            });
            navigationBarTabSelectors[i].addEventListener("mouseout", function() {
                MouseOutEffect(this.id);
            });
            navigationBarTabSelectors[i].addEventListener("focusin", function() {
                FocusInEffect(this.id);
            });
            navigationBarTabSelectors[i].addEventListener("focusout", function() {
                FocusOutEffect(this.id);
            });
        }
        
        //PERSONAL WALLET EVENT LISTENERS.
        var nonagonSegments = document.getElementsByClassName("nonagonSegment");
        for (var i = 0; i < nonagonSegments.length; i++)
        {
            nonagonSegments[i].addEventListener('click', function() {
                let cypherTokenNumber = this.id.replace("token", "");
                document.getElementById("cypherTokenBalanceHeading").innerHTML = "Cypher " + cypherTokenNumber + " Tokens";
                document.getElementById("cypherTokenBalance").innerHTML = walletPagePersonalBalanceArray["Cypher" + cypherTokenNumber + "TokenBalance"];
            });
        }
        document.getElementById("editButton").addEventListener('click', function() {
            document.getElementById('editButtonContainer').hidden = true;
            document.getElementById('cancelEditInfoButtonContainer').hidden = false;
            document.getElementById('saveButtonContainer').hidden = false;
            document.getElementById('profilePictureEdit').hidden = false;

            const fElements = document.getElementById('personalInformationForm').elements;
            for (i = 0; i < fElements.length; i++)
            {
                if (fElements[i].name != "personalUserID" && fElements[i].name != "personalEmail" && fElements[i].id != "saveButton")
                {
                    fElements[i].disabled = false;
                    if (fElements[i].id != "profilePictureUpload" && fElements[i].id != "profilePictureEdit")
                    {
                        fElements[i].addEventListener("keyup", function() {
                            document.getElementById('saveButtonIcon').src = "./assets/images/SaveIcon.png";
                            document.getElementById('saveButton').disabled = false;
                        });
                    }
                    else if (fElements[i].id == "profilePictureUpload")
                    {
                        fElements[i].addEventListener('change', function() {
                            ImageUploadValidationCheck(this);
                        });
                    }
                }
            }

            document.getElementById('cancelEditInfoButton').addEventListener('click', function() {
                document.getElementById('cancelEditInfoButtonContainer').hidden = true;
                document.getElementById('saveButtonContainer').hidden = true;
                document.getElementById('saveButtonIcon').src = "./assets/images/SaveIconDisabled.png";
                document.getElementById('saveButton').disabled = true;
                document.getElementById('profilePictureEdit').hidden = true;
                document.getElementById('profilePictureUpload').value = "";
                document.getElementById("profilePicture").src = "uploads/userprofilepictures/" + walletPagePersonalInfoArray["ProfilePicture"];
                document.getElementById("personalInformationForm").elements["personalPhoneNumber"].value = walletPagePersonalInfoArray["PhoneNumber"];
                document.getElementById("personalInformationForm").elements["personalCompany"].value = walletPagePersonalInfoArray["Company"];
                document.getElementById("personalInformationForm").elements["personalAddress"].value = walletPagePersonalInfoArray["Address"];
                document.getElementById("personalInformationForm").elements["personalCity"].value = walletPagePersonalInfoArray["City"];
                document.getElementById("personalInformationForm").elements["personalProvince"].value = walletPagePersonalInfoArray["Province"];
                document.getElementById("personalInformationForm").elements["personalCountry"].value = walletPagePersonalInfoArray["Country"];
                document.getElementById("personalInformationForm").elements["personalZIPCode"].value = walletPagePersonalInfoArray["ZIPCode"];

                document.getElementById('changePasswordContainer').hidden = true;
                document.getElementById('cancelEditPasswordButtonContainer').hidden = true;
                document.getElementById('savePasswordButtonContainer').hidden = true;
                document.getElementById('editPasswordButtonContainer').hidden = false;
                document.getElementById('personalPassword').disabled = true;
                document.getElementById('personalPassword').value = "00000000";
                document.getElementById('personalConfirmPassword').disabled = true;
                document.getElementById('personalConfirmPassword').value = "00000000";
                
                for (i = 0; i < fElements.length; i++)
                {
                    if (fElements[i].name != "personalUserID" && fElements[i].name != "personalEmail" && fElements[i].id != "saveButton")
                    {
                        fElements[i].disabled = true;
                    }
                }
                document.getElementById('editButtonContainer').hidden = false;
            });

            document.getElementById('changePasswordContainer').hidden = false;

            document.getElementById("editPasswordButton").addEventListener('click', function() {
                document.getElementById('personalPassword').disabled = false;
                document.getElementById('personalPassword').value = "";
                document.getElementById('personalConfirmPassword').disabled = false;
                document.getElementById('personalConfirmPassword').value = "";
                document.getElementById('editPasswordButtonContainer').hidden = true;
                document.getElementById('cancelEditPasswordButtonContainer').hidden = false;
                document.getElementById('savePasswordButtonContainer').hidden = false;

                document.getElementById('cancelEditPasswordButton').addEventListener('click', function() {
                    document.getElementById('personalPassword').disabled = true;
                    document.getElementById('personalPassword').value = "00000000";
                    document.getElementById('personalConfirmPassword').disabled = true;
                    document.getElementById('personalConfirmPassword').value = "00000000";
                    document.getElementById('savePasswordButtonIcon').src = "./assets/images/SaveIconDisabled.png";
                    document.getElementById('savePasswordButton').disabled = true;
                    document.getElementById('savePasswordButtonContainer').hidden = true;
                    document.getElementById('cancelEditPasswordButtonContainer').hidden = true;
                    document.getElementById('editPasswordButtonContainer').hidden = false;
                });
                ConfirmPassword();
            });
        });
        document.getElementById("profilePictureEdit").addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById("profilePictureUpload").click();
        });
        document.getElementById("personalInformationForm").addEventListener('submit', function(event) {
            event.preventDefault();
            WasPersonalInformationChanged();
        });
        document.getElementById("changePasswordForm").addEventListener('submit', function(event) {
            event.preventDefault();
            document.getElementById("closeButton").disabled = false;
            document.getElementById("verificationPassword").focus();
            document.getElementById("personalInformationLoadingOverlay").hidden = false;
            submittedPasswordEdit = true;
        });
        document.getElementById("closeButton").addEventListener('click', function() {
            document.getElementById("closeButton").disabled = true;
            document.getElementById("personalInformationLoadingOverlay").hidden = true;
            document.getElementById("passwordVerificationFormHeading").innerHTML = "Please enter your current password to confirm changes.";
            document.getElementById("verificationPasswordContainer").hidden = false;
            document.getElementById("passwordVerificationButtonContainer").hidden = false;
            document.getElementById("passwordVerificationButton").innerHTML = "SUBMIT";
            document.getElementById("passwordVerificationErrorMessage").innerHTML = "";

            submittedPersonalInfoEdit = false;
            submittedPasswordEdit = false;

            if (personalInfoChangeSuccessful == true)
            {
                personalInfoChangeSuccessful = false;

                document.getElementById('cancelEditInfoButtonContainer').hidden = true;
                document.getElementById('saveButtonContainer').hidden = true;
                document.getElementById('saveButton').disabled = true;
                document.getElementById('saveButtonIcon').src = "./assets/images/SaveIconDisabled.png";
                document.getElementById('profilePictureEdit').hidden = true;
                document.getElementById('profilePictureUpload').value = "";

                const fElements = document.getElementById('personalInformationForm').elements;
                for (i = 0; i < fElements.length; i++)
                {
                    if (fElements[i].name != "personalUserID" && fElements[i].name != "personalEmail" && fElements[i].id != "saveButton")
                    {
                        fElements[i].disabled = true;
                    }
                }

                document.getElementById('changePasswordContainer').hidden = true;
                document.getElementById('personalPassword').disabled = true;
                document.getElementById('personalPassword').value = "00000000";
                document.getElementById('personalConfirmPassword').disabled = true;
                document.getElementById('personalConfirmPassword').value = "00000000";
                document.getElementById('cancelEditPasswordButtonContainer').hidden = true;
                document.getElementById('savePasswordButtonContainer').hidden = true;
                document.getElementById('savePasswordButton').disabled = true;
                document.getElementById('savePasswordButtonIcon').src = "./assets/images/SaveIconDisabled.png";
                document.getElementById('editPasswordButtonContainer').hidden = false;

                document.getElementById('editButtonContainer').hidden = false;
            }
            else if (personalPasswordChangeSuccessful == true)
            {
                personalPasswordChangeSuccessful = false;

                document.getElementById('personalPassword').disabled = true;
                document.getElementById('personalPassword').value = "00000000";
                document.getElementById('personalConfirmPassword').disabled = true;
                document.getElementById('personalConfirmPassword').value = "00000000";
                document.getElementById('cancelEditPasswordButtonContainer').hidden = true;
                document.getElementById('savePasswordButtonContainer').hidden = true;
                document.getElementById('savePasswordButton').disabled = true;
                document.getElementById('savePasswordButtonIcon').src = "./assets/images/SaveIconDisabled.png";
                document.getElementById('editPasswordButtonContainer').hidden = false;
            }
            else
            {
                document.getElementById('personalPassword').value = "00000000";
                document.getElementById('personalConfirmPassword').value = "00000000";
                document.getElementById('savePasswordButton').disabled = true;
                document.getElementById('savePasswordButtonIcon').src = "./assets/images/SaveIconDisabled.png";
            }
        });
        document.getElementById("passwordVerificationForm").addEventListener('submit', function(event) {
            event.preventDefault();
            document.getElementById("closeButton").disabled = true;
            document.getElementById("verificationPassword").disabled = true;

            document.getElementById("passwordVerificationButton").disabled = true;
            document.getElementById("passwordVerificationButton").innerHTML = "";
            var animationContainers = this.getElementsByClassName("loadingAnimationContainer");
            animationContainers[0].style.display = "block";
            var bulletPoints = this.getElementsByClassName("bulletPoint");
            for (i = 0; i < bulletPoints.length; i++)
            {
                bulletPoints[i].style.animationPlayState = "running";
            }

            var vPassword = document.getElementById("verificationPassword").value;
            PasswordVerificationAJAX(vPassword);
        });

        //TRANSACTION LOG EVENT LISTENERS.
        document.getElementById("tLogSelectTokenTransactions").addEventListener('click', function() {
            document.getElementById("tLogSelectInvoices").style.background = "none";
            this.style.backgroundColor = "rgba(245,245,245,0.3)";
            document.getElementById("tLogInvoicesContainer").hidden = true;
            document.getElementById("tLogTokenTransactionsContainer").hidden = false;
        });
        document.getElementById("tLogSelectInvoices").addEventListener('click', function() {
            document.getElementById("tLogSelectTokenTransactions").style.background = "none";
            this.style.backgroundColor = "rgba(245,245,245,0.3)";
            document.getElementById("tLogTokenTransactionsContainer").hidden = true;
            document.getElementById("tLogInvoicesContainer").hidden = false;
        });

        //ABOUT PAGE EVENT LISTENERS
        document.getElementById("tosDisplayMoreArrow").addEventListener("click", function() {
            tosDetailsContainer = document.getElementById("tosDetailsContainer");
            if (tosOpen == false)
            {
                tosDetailsContainer.style.display = "block";
                tosDetailsContainer.style.height = "auto";
                tosOpen = true;
            }
            else if (tosOpen == true)
            {
                tosDetailsContainer.style.height = "0px";
                tosDetailsContainer.style.display = "none";
                tosOpen = false;
            }
        });
    }

    function DisplayTab(selectedTab)
    {
        document.getElementById(previousSelectedTab).style.display = "none";
        document.getElementById(selectedTab).style.display = "block";
        document.getElementById(selectedTab).scrollTo(0, 0);
        previousSelectedTab = selectedTab;

        if (desktopView == false)
        {
            document.getElementById('navigationMenu').style.left = "-300px";
            document.getElementById('navigationMenu').style.opacity = "0.75";
            document.getElementById('filterLayer').style.pointerEvents = "auto";
            document.getElementById('filterLayer').style.touchAction = "auto";
            document.getElementById('filterLayer').style.opacity = "1.0";
            navigationMenuOpen = false;
        }
    }

    function ShowSelectedTabInNavigationBar(tabSelector)
    {
        var currentTabSelector = tabSelector;
        document.getElementById(previousTabSelector).style.backgroundColor = "transparent";
        document.getElementById(previousTabSelector).style.color = "#5a5a5a";
        document.getElementById(currentTabSelector).style.backgroundColor = "rgba(143,0,255,0.8)";
        document.getElementById(currentTabSelector).style.color = "white";
        previousTabSelector = currentTabSelector;
    }

    function MouseOverEffect(TabSelector)
    {
        document.getElementById(TabSelector).style.backgroundColor = "rgba(0,0,0,0.9)";
        document.getElementById(TabSelector).style.color = "white";
    }

    function MouseOutEffect(TabSelector)
    {
        if (TabSelector != previousTabSelector)
        {
            document.getElementById(TabSelector).style.backgroundColor = "transparent";
            document.getElementById(TabSelector).style.color = "#5a5a5a";
        }
        else
        {
            document.getElementById(TabSelector).style.backgroundColor = "rgba(143,0,255,0.8)";
            document.getElementById(TabSelector).style.color = "white";
        }
    }

    function FocusInEffect(TabSelector)
    {
        document.getElementById(TabSelector).style.backgroundColor = "rgba(0,0,0,0.9)";
        document.getElementById(TabSelector).style.color = "white";
    }

    function FocusOutEffect(TabSelector)
    {
        if (TabSelector != previousTabSelector)
        {
            document.getElementById(TabSelector).style.backgroundColor = "transparent";
            document.getElementById(TabSelector).style.color = "#5a5a5a";
        }
        else
        {
            document.getElementById(TabSelector).style.backgroundColor = "rgba(143,0,255,0.8)";
            document.getElementById(TabSelector).style.color = "white";
        }     
    }

    function ConfirmPassword()
    {
        var password = document.getElementById("personalPassword");
        var confirmPassword = document.getElementById("personalConfirmPassword");

        function VerifyPassword()
        {
            if(password.value != confirmPassword.value)
            {
            confirmPassword.setCustomValidity("Passwords Don't Match!");
            document.getElementById('savePasswordButtonIcon').src = "./assets/images/SaveIconDisabled.png";
            document.getElementById('savePasswordButton').disabled = true;
            }
            else
            {
            confirmPassword.setCustomValidity("");
            document.getElementById('savePasswordButtonIcon').src = "./assets/images/SaveIcon.png";
            document.getElementById('savePasswordButton').disabled = false;
            }
        }
        password.onchange = VerifyPassword;
        confirmPassword.onkeyup = VerifyPassword;
    }

    function ImageUploadValidationCheck(profilePictureUpload)
    {
        if (typeof (profilePictureUpload.files[0]) != "undefined")
        {
            var imageDataSize = parseFloat(profilePictureUpload.files[0].size / 1024).toFixed(0);
            if (imageDataSize > 2000)
            {
                profilePictureUpload.value = "";
                alert("The image you selected is larger than the maximum allowed file size of 2MB. Please select a smaller image.");
            }
            else
            {
                var profilePictureFile = profilePictureUpload.files[0];
                document.getElementById("profilePicture").src = window.URL.createObjectURL(profilePictureFile);

                document.getElementById('saveButtonIcon').src = "./assets/images/SaveIcon.png";
                document.getElementById('saveButton').disabled = false;
            }
        }
        else
        {
            profilePictureUpload.value = "";
            alert("This browser does not support HTML5. Please use a more up to date browser.");
        }
    }

    function WasPersonalInformationChanged()
    {
        var wasPersonalInformationChanged = false;
        var wasProfilePictureChanged = false;
        const fElements = document.getElementById('personalInformationForm').elements;
        for (i = 0; i < fElements.length; i++)
        {
            if (fElements[i].name != "personalUserID" && fElements[i].name != "personalEmail" && fElements[i].name != "personalProfilePicture" && fElements[i].id != "saveButton" && fElements[i].id != "profilePictureEdit")
            {
                if (fElements[i].value != walletPagePersonalInfoArray[fElements[i].name.replace("personal", "")])
                {
                    wasPersonalInformationChanged = true;
                }
            }
            else if (fElements[i].name == "personalProfilePicture")
            {
                if (fElements[i].value != "")
                {
                    var wasProfilePictureChanged = true;
                }
            }
        }

        if (wasPersonalInformationChanged == true || wasProfilePictureChanged == true)
        {
            document.getElementById("passwordVerificationFormHeading").innerHTML = "Please enter your current password to confirm changes.";
            document.getElementById("closeButton").disabled = false;
            document.getElementById("personalInformationLoadingOverlay").hidden = false;
            document.getElementById("verificationPassword").focus();
            submittedPersonalInfoEdit = true;
        }
        else
        {
            document.getElementById("passwordVerificationFormHeading").innerHTML = "No changes found. Please ensure you made the adjustments desired.";
            document.getElementById("closeButton").disabled = false;
            document.getElementById("verificationPasswordContainer").hidden = true;
            document.getElementById("passwordVerificationButtonContainer").hidden = true;
            document.getElementById("personalInformationLoadingOverlay").hidden = false;
            document.getElementById('saveButton').disabled = true;
            document.getElementById('saveButtonIcon').src = "./assets/images/SaveIconDisabled.png";
        }
    }

    function PasswordVerificationAJAX(vPassword)
    {
        //If the user tries to refresh the page, this prompts the user that a process may be active.
        window.onbeforeunload = function(event) {
            return true;
        }

        const vPasswordData = new FormData();
        vPasswordData.append("Password", vPassword);

        const submitVPassword = new XMLHttpRequest();

        submitVPassword.open("POST", "mqw_SubmitPasswordVerification.php", true);
        submitVPassword.send(vPasswordData);

        submitVPassword.onreadystatechange = function () {
            if (submitVPassword.readyState === 4)
            {
                if (submitVPassword.status === 200)
                {
                    if (this.responseText == "True")
                    {
                        if (submittedPersonalInfoEdit == true)
                        {  
                            SubmitEditedPersonalInfoAJAX();
                        }
                        else if (submittedPasswordEdit == true)
                        {
                            SubmitEditedPasswordAJAX();
                        }
                    }
                    else if (this.responseText == "logout")
                    {
                        Logout();
                    }
                    else
                    {
                        document.getElementById("verificationPassword").value = "";
                        document.getElementById("verificationPassword").disabled = false;
                        document.getElementById("verificationPassword").focus();
                        document.getElementById("passwordVerificationErrorMessage").innerHTML = this.responseText;

                        var animationContainers = document.getElementById("passwordVerificationForm").getElementsByClassName("loadingAnimationContainer");
                        animationContainers[0].style.display = "none";
                        var bulletPoints = document.getElementById("passwordVerificationForm").getElementsByClassName("bulletPoint");
                        for (i = 0; i < bulletPoints.length; i++)
                        {
                            bulletPoints[i].style.animationPlayState = "paused";
                        }
                        document.getElementById("passwordVerificationButton").innerHTML = "SUBMIT";
                        document.getElementById("passwordVerificationButton").disabled = false;

                        document.getElementById("closeButton").disabled = false;
                    }
                }
                else
                {
                    document.getElementById("verificationPassword").value = "";
                    document.getElementById("verificationPassword").disabled = false;
                    document.getElementById("verificationPassword").focus();
                    document.getElementById("passwordVerificationErrorMessage").innerHTML = "HTTP Request Error: " + submitVPassword.status + ", " + submitVPassword.statusText;

                    var animationContainers = document.getElementById("passwordVerificationForm").getElementsByClassName("loadingAnimationContainer");
                    animationContainers[0].style.display = "none";
                    var bulletPoints = document.getElementById("passwordVerificationForm").getElementsByClassName("bulletPoint");
                    for (i = 0; i < bulletPoints.length; i++)
                    {
                        bulletPoints[i].style.animationPlayState = "paused";
                    }
                    document.getElementById("passwordVerificationButton").innerHTML = "SUBMIT";
                    document.getElementById("passwordVerificationButton").disabled = false;

                    document.getElementById("closeButton").disabled = false;
                }
            }
        }
    }

    function SubmitEditedPersonalInfoAJAX()
    {
        const personalInfoFormData = new FormData();
        const personalInfoFormElements = document.getElementById("personalInformationForm").elements;
        for (i = 0; i < personalInfoFormElements.length; i++)
        {
            if (personalInfoFormElements[i].name != "personalUserID" && personalInfoFormElements[i].name != "personalEmail" && personalInfoFormElements[i].name != "personalProfilePicture" && personalInfoFormElements[i].id != "saveButton" && personalInfoFormElements[i].id != "profilePictureEdit")
            {
                var personalInfoDataName = personalInfoFormElements[i].name.replace("personal", "");
                if (personalInfoFormElements[i].value != walletPagePersonalInfoArray[personalInfoDataName])
                {
                    personalInfoFormData.append(personalInfoDataName, personalInfoFormElements[i].value);
                }
            }
            else if (personalInfoFormElements[i].name == "personalProfilePicture")
            {
                var personalInfoDataName = personalInfoFormElements[i].name.replace("personal", "");
                if (personalInfoFormElements[i].value != "")
                {
                    personalInfoFormData.append(personalInfoDataName, personalInfoFormElements[i].files[0]);

                    //var fileReader = new FileReader();
                    //fileReader.onload = function(e) {
                    //    personalInfoFormData.append(personalInfoDataName, e.target.result);
                    //};
                    //fileReader.readAsText(profilePictureFile);
                }
            }
        }

        const submitPersonalInfoForm = new XMLHttpRequest();
        submitPersonalInfoForm.open("POST", "mqw_SubmitPersonalInfoForm.php", true);
        submitPersonalInfoForm.send(personalInfoFormData);

        submitPersonalInfoForm.onreadystatechange = function () {
            if (submitPersonalInfoForm.readyState === 4)
            {
                if (submitPersonalInfoForm.status === 200)
                {
                    if (this.responseText == "Personal information changes saved!")
                    {
                        var animationContainers = document.getElementById("passwordVerificationForm").getElementsByClassName("loadingAnimationContainer");
                        animationContainers[0].style.display = "none";
                        var bulletPoints = document.getElementById("passwordVerificationForm").getElementsByClassName("bulletPoint");
                        for (i = 0; i < bulletPoints.length; i++)
                        {
                            bulletPoints[i].style.animationPlayState = "paused";
                        }
                        document.getElementById("passwordVerificationButtonContainer").hidden = true;
                        document.getElementById("passwordVerificationButton").innerHTML = "SUBMIT";
                        document.getElementById("passwordVerificationButton").disabled = false;
                        
                        document.getElementById("verificationPasswordContainer").hidden = true;
                        document.getElementById("verificationPassword").value = "";
                        document.getElementById("verificationPassword").disabled = false;

                        document.getElementById("passwordVerificationFormHeading").innerHTML = "Complete";

                        document.getElementById("passwordVerificationErrorMessage").innerHTML = this.responseText;

                        document.getElementById("closeButton").disabled = false;

                        walletPagePersonalInfoArray["PhoneNumber"] = document.getElementById("personalInformationForm").elements["personalPhoneNumber"].value;
                        walletPagePersonalInfoArray["Company"] = document.getElementById("personalInformationForm").elements["personalCompany"].value;
                        walletPagePersonalInfoArray["Address"] = document.getElementById("personalInformationForm").elements["personalAddress"].value;
                        walletPagePersonalInfoArray["City"] = document.getElementById("personalInformationForm").elements["personalCity"].value;
                        walletPagePersonalInfoArray["Province"] = document.getElementById("personalInformationForm").elements["personalProvince"].value;
                        walletPagePersonalInfoArray["Country"] = document.getElementById("personalInformationForm").elements["personalCountry"].value;
                        walletPagePersonalInfoArray["ZIPCode"] = document.getElementById("personalInformationForm").elements["personalZIPCode"].value;

                        personalInfoChangeSuccessful = true;
                    }
                    else
                    {
                        var animationContainers = document.getElementById("passwordVerificationForm").getElementsByClassName("loadingAnimationContainer");
                        animationContainers[0].style.display = "none";
                        var bulletPoints = document.getElementById("passwordVerificationForm").getElementsByClassName("bulletPoint");
                        for (i = 0; i < bulletPoints.length; i++)
                        {
                            bulletPoints[i].style.animationPlayState = "paused";
                        }
                        document.getElementById("passwordVerificationButton").innerHTML = "RETRY";
                        document.getElementById("passwordVerificationButton").disabled = false;
                        
                        document.getElementById("verificationPassword").value = "";
                        document.getElementById("verificationPassword").disabled = false;
                        document.getElementById("verificationPassword").focus();

                        document.getElementById("passwordVerificationFormHeading").innerHTML = "Something went wrong!";

                        document.getElementById("passwordVerificationErrorMessage").innerHTML = this.responseText;

                        document.getElementById("closeButton").disabled = false;

                        personalInfoChangeSuccessful = false;
                    }
                }
                else
                {
                    var animationContainers = document.getElementById("passwordVerificationForm").getElementsByClassName("loadingAnimationContainer");
                    animationContainers[0].style.display = "none";
                    var bulletPoints = document.getElementById("passwordVerificationForm").getElementsByClassName("bulletPoint");
                    for (i = 0; i < bulletPoints.length; i++)
                    {
                        bulletPoints[i].style.animationPlayState = "paused";
                    }
                    document.getElementById("passwordVerificationButton").innerHTML = "RETRY";
                    document.getElementById("passwordVerificationButton").disabled = false;
                        
                    document.getElementById("verificationPassword").value = "";
                    document.getElementById("verificationPassword").disabled = false;
                    document.getElementById("verificationPassword").focus();

                    document.getElementById("passwordVerificationFormHeading").innerHTML = "Something went wrong!";

                    document.getElementById("passwordVerificationErrorMessage").innerHTML = "HTTP Request Error: " + submitPersonalInfoForm.status + ", " + submitPersonalInfoForm.statusText;

                    document.getElementById("closeButton").disabled = false;

                    personalInfoChangeSuccessful = false;
                }
            }
        }
        //Allows the user to refresh freely.
        window.onbeforeunload = function(event) {
            return;
        }
    }

    function SubmitEditedPasswordAJAX()
    { 
        const passwordFormData = new FormData();
        passwordFormData.append("Password", document.getElementById("personalPassword").value);

        const submitPasswordForm = new XMLHttpRequest();

        submitPasswordForm.open("POST", "mqw_SubmitPasswordChangeForm.php", true);
        submitPasswordForm.send(passwordFormData);

        submitPasswordForm.onreadystatechange = function () {
            if (submitPasswordForm.readyState === 4)
            {
                if (submitPasswordForm.status === 200)
                {
                    if (this.responseText == "Password successfully changed!")
                    {
                        var animationContainers = document.getElementById("passwordVerificationForm").getElementsByClassName("loadingAnimationContainer");
                        animationContainers[0].style.display = "none";
                        var bulletPoints = document.getElementById("passwordVerificationForm").getElementsByClassName("bulletPoint");
                        for (i = 0; i < bulletPoints.length; i++)
                        {
                            bulletPoints[i].style.animationPlayState = "paused";
                        }
                        document.getElementById("passwordVerificationButtonContainer").hidden = true;
                        document.getElementById("passwordVerificationButton").innerHTML = "SUBMIT";
                        document.getElementById("passwordVerificationButton").disabled = false;
                        
                        document.getElementById("verificationPasswordContainer").hidden = true;
                        document.getElementById("verificationPassword").value = "";
                        document.getElementById("verificationPassword").disabled = false;

                        document.getElementById("passwordVerificationFormHeading").innerHTML = "Complete";

                        document.getElementById("passwordVerificationErrorMessage").innerHTML = this.responseText;

                        document.getElementById("closeButton").disabled = false;

                        personalPasswordChangeSuccessful = true;
                    }
                    else
                    {
                        var animationContainers = document.getElementById("passwordVerificationForm").getElementsByClassName("loadingAnimationContainer");
                        animationContainers[0].style.display = "none";
                        var bulletPoints = document.getElementById("passwordVerificationForm").getElementsByClassName("bulletPoint");
                        for (i = 0; i < bulletPoints.length; i++)
                        {
                            bulletPoints[i].style.animationPlayState = "paused";
                        }
                        document.getElementById("passwordVerificationButton").innerHTML = "RETRY";
                        document.getElementById("passwordVerificationButton").disabled = false;
                        
                        document.getElementById("verificationPassword").value = "";
                        document.getElementById("verificationPassword").disabled = false;
                        document.getElementById("verificationPassword").focus();

                        document.getElementById("passwordVerificationFormHeading").innerHTML = "Something went wrong!";

                        document.getElementById("passwordVerificationErrorMessage").innerHTML = this.responseText;

                        document.getElementById("closeButton").disabled = false;

                        personalPasswordChangeSuccessful = false;
                    }
                }
                else
                {
                    var animationContainers = document.getElementById("passwordVerificationForm").getElementsByClassName("loadingAnimationContainer");
                    animationContainers[0].style.display = "none";
                    var bulletPoints = document.getElementById("passwordVerificationForm").getElementsByClassName("bulletPoint");
                    for (i = 0; i < bulletPoints.length; i++)
                    {
                        bulletPoints[i].style.animationPlayState = "paused";
                    }
                    document.getElementById("passwordVerificationButton").innerHTML = "RETRY";
                    document.getElementById("passwordVerificationButton").disabled = false;
                        
                    document.getElementById("verificationPassword").value = "";
                    document.getElementById("verificationPassword").disabled = false;
                    document.getElementById("verificationPassword").focus();

                    document.getElementById("passwordVerificationFormHeading").innerHTML = "Something went wrong!";

                    document.getElementById("passwordVerificationErrorMessage").innerHTML = "HTTP Request Error: " + submitPasswordForm.status + ", " + submitPasswordForm.statusText;

                    document.getElementById("closeButton").disabled = false;

                    personalPasswordChangeSuccessful = false;
                }
            }
        }
        //Allows the user to refresh freely.
        window.onbeforeunload = function(event) {
            return;
        }
    }

    function FetchUserTransactionLogAJAX()
    {
        firstTransactionLogLoad = false;

        var FetchUserTokenTransactionLog = new XMLHttpRequest();

        FetchUserTokenTransactionLog.open("POST", "mqwAdmin_FetchUserTokenTransactionLog.php", true);
        FetchUserTokenTransactionLog.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        FetchUserTokenTransactionLog.send(null);

        FetchUserTokenTransactionLog.onreadystatechange = function () {
            if (FetchUserTokenTransactionLog.readyState === 4)
            {
                if (FetchUserTransactionLog.status === 200)
                {
                    if (this.responseText == "logout")
                    {
                        Logout();
                    }
                    else
                    {
                        let fetchedUserTokenTransactionLogArray = JSON.parse(this.responseText);
                        DisplayUserTokenTransactionLog(fetchedUserTokenTransactionLogArray);
                    }
                }
            }
        }

        var FetchUserTransactionLog = new XMLHttpRequest();

        FetchUserTransactionLog.open("POST", "mqwAdmin_FetchUserTransactionLog.php", true);
        FetchUserTransactionLog.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        FetchUserTransactionLog.send(null);

        FetchUserTransactionLog.onreadystatechange = function () {
            if (FetchUserTransactionLog.readyState === 4)
            {
                if (FetchUserTransactionLog.status === 200)
                {
                    if (this.responseText == "logout")
                    {
                        Logout();
                    }
                    else
                    {
                        let fetchedUserTransactionLogArray = JSON.parse(this.responseText);
                        DisplayUserTransactionLog(fetchedUserTransactionLogArray);
                    }
                }
            }
        }
    }

    function DisplayUserTokenTransactionLog(fetchedUserTokenTransactionLogArray)
    {
        const tLogMainContainer = document.getElementById("tLogTokenTransactionsContainer");
    
        if (fetchedUserTokenTransactionLogArray.length >= 1)
        {
            var animationContainer = document.getElementById("tLogLoadingAnimationContainer");
            animationContainer.style.display = "none";
            var bulletPoints = animationContainer.getElementsByClassName("bulletPoint");
            for (i = 0; i < bulletPoints.length; i++)
            {
                bulletPoints[i].style.animationPlayState = "paused";
            }
            
            document.getElementById("emptyLogPlaceholderTokens").hidden = true;
            document.getElementById("tLogHeadersContainer").hidden = false;
            
            var previousTLogEntryContainer;

            for (var i = 0; i < fetchedUserTokenTransactionLogArray.length; i++)
            {
                let tLogEntryContainer = document.createElement("div");
                tLogMainContainer.appendChild(tLogEntryContainer);
                tLogEntryContainer.className = "tLogEntryContainer";
                if (previousTLogEntryContainer != null)
                {
                    previousTLogEntryContainer.parentNode.insertBefore(tLogEntryContainer, previousTLogEntryContainer);
                }
                previousTLogEntryContainer = tLogEntryContainer;

                let tLogEntry = document.createElement("div");
                tLogEntry.className = "tLogEntry";
                tLogEntryContainer.appendChild(tLogEntry);

                let tLogInvoiceNumber = document.createElement("div");
                tLogInvoiceNumber.className = "tLogInvoiceNumber";
                tLogInvoiceNumber.innerHTML = fetchedUserTokenTransactionLogArray[i]["TransactionNumber_FK"];
                tLogEntry.appendChild(tLogInvoiceNumber);

                let tLogProductName = document.createElement("div");
                tLogProductName.className = "tLogProductName";
                tLogProductName.innerHTML = fetchedUserTokenTransactionLogArray[i]["ProductName"];
                tLogEntry.appendChild(tLogProductName);

                let tLogDisplayMoreArrow = document.createElement("img");
                tLogDisplayMoreArrow.className = "displayMoreArrow";
                tLogDisplayMoreArrow.id = "displayMoreArrow" + i;
                tLogDisplayMoreArrow.src = "./assets/images/DropdownArrowIcon.png";
                tLogEntry.appendChild(tLogDisplayMoreArrow);

                tLogDisplayMoreArrow.addEventListener("click", function() {
                    let relatedDetailsContainer = this.parentElement.nextSibling.id;

                    if (previousTLogEntryTokens != "")
                    {
                        if (relatedDetailsContainer == previousTLogEntryTokens)
                        {
                            document.getElementById(relatedDetailsContainer).style.height = "0px";
                            document.getElementById(relatedDetailsContainer).style.display = "none";
                            previousTLogEntryTokens = "";
                        }
                        else
                        {
                            document.getElementById(previousTLogEntryTokens).style.height = "0px";
                            document.getElementById(previousTLogEntryTokens).style.display = "none";
                            previousTLogEntryTokens = "";
                            document.getElementById(relatedDetailsContainer).style.display = "flex";
                            document.getElementById(relatedDetailsContainer).style.height = "auto";
                            previousTLogEntryTokens = relatedDetailsContainer;
                        }
                    }
                    else
                    {
                        document.getElementById(relatedDetailsContainer).style.display = "flex";
                        document.getElementById(relatedDetailsContainer).style.height = "auto";
                        previousTLogEntryTokens = relatedDetailsContainer;
                    }
                });

                let tLogEntryDetailsContainer = document.createElement("div");
                tLogEntryDetailsContainer.className = "tLogEntryDetailsContainer";
                tLogEntryDetailsContainer.id = "tLogEntryDetailsContainer" + i;
                tLogEntryDetailsContainer.style.display = "none";
                tLogEntryDetailsContainer.style.height = "0px";
                tLogEntryContainer.appendChild(tLogEntryDetailsContainer);

                let tLogEntryDetailsContainerOne = document.createElement("div");
                tLogEntryDetailsContainerOne.className = "tLogEntryDetailsContainerOne";
                tLogEntryDetailsContainer.appendChild(tLogEntryDetailsContainerOne);

                var tLogEntryDetailsHeading = document.createElement("div");
                tLogEntryDetailsHeading.className = "tLogEntryDetailsHeading";
                tLogEntryDetailsHeading.innerHTML = "Payment and Shipping";
                tLogEntryDetailsContainerOne.appendChild(tLogEntryDetailsHeading);

                var tLogEntryDetailsInfoFlexContainer = document.createElement("div");
                tLogEntryDetailsInfoFlexContainer.className = "tLogEntryDetailsInfoFlexContainer";
                tLogEntryDetailsContainerOne.appendChild(tLogEntryDetailsInfoFlexContainer);

                var tLogEntryDetailsSubHeading = document.createElement("div");
                tLogEntryDetailsSubHeading.className = "tLogEntryDetailsSubHeading";
                tLogEntryDetailsSubHeading.innerHTML = "Transaction Type:";
                tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsSubHeading);

                var tLogEntryDetailsInfo = document.createElement("div");
                tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                tLogEntryDetailsInfo.innerHTML = fetchedUserTokenTransactionLogArray[i]["TransactionType"];
                tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsInfo);

                var tLogEntryDetailsInfoFlexContainer = document.createElement("div");
                tLogEntryDetailsInfoFlexContainer.className = "tLogEntryDetailsInfoFlexContainer";
                tLogEntryDetailsContainerOne.appendChild(tLogEntryDetailsInfoFlexContainer);

                var tLogEntryDetailsSubHeading = document.createElement("div");
                tLogEntryDetailsSubHeading.className = "tLogEntryDetailsSubHeading";
                tLogEntryDetailsSubHeading.innerHTML = "Product Quantity:";
                tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsSubHeading);

                var tLogEntryDetailsInfo = document.createElement("div");
                tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                tLogEntryDetailsInfo.innerHTML = fetchedUserTokenTransactionLogArray[i]["ProductQuantity"];
                tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsInfo);

                if (fetchedUserTokenTransactionLogArray[i]["PaymentStatus"] == "Pending")
                {
                    var tLogEntryDetailsInfoContainer = document.createElement("div");
                    tLogEntryDetailsInfoContainer.className = "tLogEntryDetailsInfoContainer";
                    tLogEntryDetailsContainerOne.appendChild(tLogEntryDetailsInfoContainer);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.id = "tLogEntryPaymentStatus" + i;
                    tLogEntryDetailsInfo.innerHTML = "&bull; " + fetchedUserTokenTransactionLogArray[i]["PaymentStatus"] + " Payment";
                    tLogEntryDetailsInfoContainer.appendChild(tLogEntryDetailsInfo);
                }
                else
                {
                    var tLogEntryDetailsInfoContainer = document.createElement("div");
                    tLogEntryDetailsInfoContainer.className = "tLogEntryDetailsInfoContainer";
                    tLogEntryDetailsContainerOne.appendChild(tLogEntryDetailsInfoContainer);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.id = "tLogEntryPaymentStatus" + i;
                    tLogEntryDetailsInfo.innerHTML = "&bull; " + fetchedUserTokenTransactionLogArray[i]["PaymentStatus"] + " (" + fetchedUserTokenTransactionLogArray[i]["PaidAt"] + ")+";
                    tLogEntryDetailsInfoContainer.appendChild(tLogEntryDetailsInfo);
                }

                if (fetchedUserTokenTransactionLogArray[i]["ShippingStatus"] == "Processing")
                {
                    var tLogEntryDetailsInfoContainer = document.createElement("div");
                    tLogEntryDetailsInfoContainer.className = "tLogEntryDetailsInfoContainer";
                    tLogEntryDetailsContainerOne.appendChild(tLogEntryDetailsInfoContainer);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.id = "tLogEntryShippingStatus" + i;
                    tLogEntryDetailsInfo.innerHTML = "&bull; " + fetchedUserTokenTransactionLogArray[i]["ShippingStatus"] + " Shipment";
                    tLogEntryDetailsInfoContainer.appendChild(tLogEntryDetailsInfo);
                }
                else
                {
                    var tLogEntryDetailsInfoContainer = document.createElement("div");
                    tLogEntryDetailsInfoContainer.className = "tLogEntryDetailsInfoContainer";
                    tLogEntryDetailsContainerOne.appendChild(tLogEntryDetailsInfoContainer);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.id = "tLogEntryShippingStatus" + i;
                    tLogEntryDetailsInfo.innerHTML = "&bull; " + fetchedUserTokenTransactionLogArray[i]["ShippingStatus"] + " (" + fetchedUserTokenTransactionLogArray[i]["ShippedAt"] + ") - " + fetchedUserTokenTransactionLogArray[i]["ShippingMethod"];
                    tLogEntryDetailsInfoContainer.appendChild(tLogEntryDetailsInfo);
                }

                let tLogEntryDetailsContainerTwo = document.createElement("div");
                tLogEntryDetailsContainerTwo.className = "tLogEntryDetailsContainerTwo";
                tLogEntryDetailsContainer.appendChild(tLogEntryDetailsContainerTwo);

                var tLogEntryDetailsHeading = document.createElement("div");
                tLogEntryDetailsHeading.className = "tLogEntryDetailsHeading";
                tLogEntryDetailsHeading.innerHTML = "Transaction Information";
                tLogEntryDetailsContainerTwo.appendChild(tLogEntryDetailsHeading);

                var tLogEntryDetailsInfoFlexContainer = document.createElement("div");
                tLogEntryDetailsInfoFlexContainer.className = "tLogEntryDetailsInfoFlexContainer";
                tLogEntryDetailsContainerTwo.appendChild(tLogEntryDetailsInfoFlexContainer);

                var tLogEntryDetailsSubHeading = document.createElement("div");
                tLogEntryDetailsSubHeading.className = "tLogEntryDetailsSubHeading";
                tLogEntryDetailsSubHeading.innerHTML = "Total:";
                tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsSubHeading);

                var tLogEntryDetailsInfo = document.createElement("div");
                tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                tLogEntryDetailsInfo.innerHTML = fetchedUserTokenTransactionLogArray[i]["Total"];
                tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsInfo);

                if (fetchedUserTokenTransactionLogArray[i]["Outstanding"] != "")
                {
                    var tLogEntryDetailsInfoFlexContainer = document.createElement("div");
                    tLogEntryDetailsInfoFlexContainer.className = "tLogEntryDetailsInfoFlexContainer";
                    tLogEntryDetailsContainerTwo.appendChild(tLogEntryDetailsInfoFlexContainer);

                    var tLogEntryDetailsSubHeading = document.createElement("div");
                    tLogEntryDetailsSubHeading.className = "tLogEntryDetailsSubHeading";
                    tLogEntryDetailsSubHeading.innerHTML = "Outstanding:";
                    tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsSubHeading);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.innerHTML = fetchedUserTokenTransactionLogArray[i]["Outstanding"];
                    tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsInfo);
                }
            }
        }
        else
        {
            var animationContainer = document.getElementById("tLogLoadingAnimationContainer");
            animationContainer.style.display = "none";
            var bulletPoints = animationContainer.getElementsByClassName("bulletPoint");
            for (i = 0; i < bulletPoints.length; i++)
            {
                bulletPoints[i].style.animationPlayState = "paused";
            }
        }

        fetchedUserTokenTransactionLogArray = [];
    }

    function DisplayUserTransactionLog(fetchedUserTransactionLogArray)
    {
        const tLogMainContainer = document.getElementById("tLogInvoicesContainer");
    
        if (fetchedUserTransactionLogArray.length >= 1)
        {
            var animationContainer = document.getElementById("tLogLoadingAnimationContainer");
            animationContainer.style.display = "none";
            var bulletPoints = animationContainer.getElementsByClassName("bulletPoint");
            for (i = 0; i < bulletPoints.length; i++)
            {
                bulletPoints[i].style.animationPlayState = "paused";
            }
            
            document.getElementById("emptyLogPlaceholderInvoices").hidden = true;
            document.getElementById("tLogHeadersContainer").hidden = false;
            
            var previousTLogEntryContainer;

            j = 100000;
            for (var i = 0; i < fetchedUserTransactionLogArray.length; i++)
            {
                
                let tLogEntryContainer = document.createElement("div");
                tLogMainContainer.appendChild(tLogEntryContainer);
                tLogEntryContainer.className = "tLogEntryContainer";
                if (previousTLogEntryContainer != null)
                {
                    previousTLogEntryContainer.parentNode.insertBefore(tLogEntryContainer, previousTLogEntryContainer);
                }
                previousTLogEntryContainer = tLogEntryContainer;

                let tLogEntry = document.createElement("div");
                tLogEntry.className = "tLogEntry";
                tLogEntryContainer.appendChild(tLogEntry);

                let tLogInvoiceNumber = document.createElement("div");
                tLogInvoiceNumber.className = "tLogInvoiceNumber";
                tLogInvoiceNumber.innerHTML = fetchedUserTransactionLogArray[i]["InvoiceNumber_FK"];
                tLogEntry.appendChild(tLogInvoiceNumber);

                let tLogProductName = document.createElement("div");
                tLogProductName.className = "tLogProductName";
                tLogProductName.innerHTML = fetchedUserTransactionLogArray[i]["ProductName"];
                tLogEntry.appendChild(tLogProductName);

                let tLogDisplayMoreArrow = document.createElement("img");
                tLogDisplayMoreArrow.className = "displayMoreArrow";
                tLogDisplayMoreArrow.id = "displayMoreArrow" + j;
                tLogDisplayMoreArrow.src = "./assets/images/DropdownArrowIcon.png";
                tLogEntry.appendChild(tLogDisplayMoreArrow);

                tLogDisplayMoreArrow.addEventListener("click", function() {
                    let relatedDetailsContainer = this.parentElement.nextSibling.id;

                    if (previousTLogEntryInvoices != "")
                    {
                        if (relatedDetailsContainer == previousTLogEntryInvoices)
                        {
                            document.getElementById(relatedDetailsContainer).style.height = "0px";
                            document.getElementById(relatedDetailsContainer).style.display = "none";
                            previousTLogEntryInvoices = "";
                        }
                        else
                        {
                            document.getElementById(previousTLogEntryInvoices).style.height = "0px";
                            document.getElementById(previousTLogEntryInvoices).style.display = "none";
                            previousTLogEntryInvoices = "";
                            document.getElementById(relatedDetailsContainer).style.display = "flex";
                            document.getElementById(relatedDetailsContainer).style.height = "auto";
                            previousTLogEntryInvoices = relatedDetailsContainer;
                        }
                    }
                    else
                    {
                        document.getElementById(relatedDetailsContainer).style.display = "flex";
                        document.getElementById(relatedDetailsContainer).style.height = "auto";
                        previousTLogEntryInvoices = relatedDetailsContainer;
                    }
                });

                let tLogEntryDetailsContainer = document.createElement("div");
                tLogEntryDetailsContainer.className = "tLogEntryDetailsContainer";
                tLogEntryDetailsContainer.id = "tLogEntryDetailsContainer" + j;
                tLogEntryDetailsContainer.style.display = "none";
                tLogEntryDetailsContainer.style.height = "0px";
                tLogEntryContainer.appendChild(tLogEntryDetailsContainer);

                let tLogEntryDetailsContainerOne = document.createElement("div");
                tLogEntryDetailsContainerOne.className = "tLogEntryDetailsContainerOne";
                tLogEntryDetailsContainer.appendChild(tLogEntryDetailsContainerOne);

                var tLogEntryDetailsHeading = document.createElement("div");
                tLogEntryDetailsHeading.className = "tLogEntryDetailsHeading";
                tLogEntryDetailsHeading.innerHTML = "Payment and Shipping";
                tLogEntryDetailsContainerOne.appendChild(tLogEntryDetailsHeading);

                if (fetchedUserTransactionLogArray[i]["PaymentStatus"] == "Pending")
                {
                    var tLogEntryDetailsInfoContainer = document.createElement("div");
                    tLogEntryDetailsInfoContainer.className = "tLogEntryDetailsInfoContainer";
                    tLogEntryDetailsContainerOne.appendChild(tLogEntryDetailsInfoContainer);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.id = "tLogEntryPaymentStatus" + j;
                    tLogEntryDetailsInfo.innerHTML = "&bull; " + fetchedUserTransactionLogArray[i]["PaymentStatus"] + " Payment";
                    tLogEntryDetailsInfoContainer.appendChild(tLogEntryDetailsInfo);
                }
                else
                {
                    var tLogEntryDetailsInfoContainer = document.createElement("div");
                    tLogEntryDetailsInfoContainer.className = "tLogEntryDetailsInfoContainer";
                    tLogEntryDetailsContainerOne.appendChild(tLogEntryDetailsInfoContainer);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.id = "tLogEntryPaymentStatus" + j;
                    tLogEntryDetailsInfo.innerHTML = "&bull; " + fetchedUserTransactionLogArray[i]["PaymentStatus"] + " (" + fetchedUserTransactionLogArray[i]["PaidAt"] + ") - " + fetchedUserTransactionLogArray[i]["PaymentType"];
                    tLogEntryDetailsInfoContainer.appendChild(tLogEntryDetailsInfo);
                }

                if (fetchedUserTransactionLogArray[i]["ShippingStatus"] == "Processing")
                {
                    var tLogEntryDetailsInfoContainer = document.createElement("div");
                    tLogEntryDetailsInfoContainer.className = "tLogEntryDetailsInfoContainer";
                    tLogEntryDetailsContainerOne.appendChild(tLogEntryDetailsInfoContainer);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.id = "tLogEntryShippingStatus" + j;
                    tLogEntryDetailsInfo.innerHTML = "&bull; " + fetchedUserTransactionLogArray[i]["ShippingStatus"] + " Shipment";
                    tLogEntryDetailsInfoContainer.appendChild(tLogEntryDetailsInfo);
                }
                else
                {
                    var tLogEntryDetailsInfoContainer = document.createElement("div");
                    tLogEntryDetailsInfoContainer.className = "tLogEntryDetailsInfoContainer";
                    tLogEntryDetailsContainerOne.appendChild(tLogEntryDetailsInfoContainer);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.id = "tLogEntryShippingStatus" + j;
                    tLogEntryDetailsInfo.innerHTML = "&bull; " + fetchedUserTransactionLogArray[i]["ShippingStatus"] + " (" + fetchedUserTransactionLogArray[i]["ShippedAt"] + ") - " + fetchedUserTransactionLogArray[i]["ShippingMethod"];
                    tLogEntryDetailsInfoContainer.appendChild(tLogEntryDetailsInfo);
                }

                var tLogEntryDetailsInfoFlexContainer = document.createElement("div");
                tLogEntryDetailsInfoFlexContainer.className = "tLogEntryDetailsInfoFlexContainer";
                tLogEntryDetailsContainerOne.appendChild(tLogEntryDetailsInfoFlexContainer);

                var tLogEntryDetailsSubHeading = document.createElement("div");
                tLogEntryDetailsSubHeading.className = "tLogEntryDetailsSubHeading";
                tLogEntryDetailsSubHeading.innerHTML = "Total:";
                tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsSubHeading);

                var tLogEntryDetailsInfo = document.createElement("div");
                tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                tLogEntryDetailsInfo.id = "tLogEntryTotal" + j;
                tLogEntryDetailsInfo.innerHTML = fetchedUserTransactionLogArray[i]["TotalInEuros"] + " &euro;";
                tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsInfo);

                if (fetchedUserTransactionLogArray[i]["OutstandingBalanceInEuros"] != "0.00")
                {
                    var tLogEntryDetailsInfoFlexContainer = document.createElement("div");
                    tLogEntryDetailsInfoFlexContainer.className = "tLogEntryDetailsInfoFlexContainer";
                    tLogEntryDetailsContainerOne.appendChild(tLogEntryDetailsInfoFlexContainer);

                    var tLogEntryDetailsSubHeading = document.createElement("div");
                    tLogEntryDetailsSubHeading.className = "tLogEntryDetailsSubHeading";
                    tLogEntryDetailsSubHeading.innerHTML = "Outstanding:";
                    tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsSubHeading);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.id = "tLogEntryOutstanding" + j;
                    tLogEntryDetailsInfo.innerHTML = fetchedUserTransactionLogArray[i]["OutstandingBalanceInEuros"] + " &euro;";
                    tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsInfo);
                }

                if (fetchedUserTransactionLogArray[i]["AmountRefundedInEuros"] != "0.00")
                {
                    var tLogEntryDetailsInfoFlexContainer = document.createElement("div");
                    tLogEntryDetailsInfoFlexContainer.className = "tLogEntryDetailsInfoFlexContainer";
                    tLogEntryDetailsContainerOne.appendChild(tLogEntryDetailsInfoFlexContainer);

                    var tLogEntryDetailsSubHeading = document.createElement("div");
                    tLogEntryDetailsSubHeading.className = "tLogEntryDetailsSubHeading";
                    tLogEntryDetailsSubHeading.innerHTML = "Refunded:";
                    tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsSubHeading);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.id = "tLogEntryRefunded" + j;
                    tLogEntryDetailsInfo.innerHTML = fetchedUserTransactionLogArray[i]["AmountRefundedInEuros"] + " &euro;";
                    tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsInfo);
                }

                let tLogEntryDetailsContainerTwo = document.createElement("div");
                tLogEntryDetailsContainerTwo.className = "tLogEntryDetailsContainerTwo";
                tLogEntryDetailsContainer.appendChild(tLogEntryDetailsContainerTwo);

                var tLogEntryDetailsHeading = document.createElement("div");
                tLogEntryDetailsHeading.className = "tLogEntryDetailsHeading";
                tLogEntryDetailsHeading.innerHTML = "Transaction Information";
                tLogEntryDetailsContainerTwo.appendChild(tLogEntryDetailsHeading);

                var tLogEntryDetailsInfoFlexContainer = document.createElement("div");
                tLogEntryDetailsInfoFlexContainer.className = "tLogEntryDetailsInfoFlexContainer";
                tLogEntryDetailsContainerTwo.appendChild(tLogEntryDetailsInfoFlexContainer);

                var tLogEntryDetailsSubHeading = document.createElement("div");
                tLogEntryDetailsSubHeading.className = "tLogEntryDetailsSubHeading";
                tLogEntryDetailsSubHeading.innerHTML = "Product Quantity:";
                tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsSubHeading);

                var tLogEntryDetailsInfo = document.createElement("div");
                tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                tLogEntryDetailsInfo.id = "tLogEntryProductQuantity" + j;
                tLogEntryDetailsInfo.innerHTML = fetchedUserTransactionLogArray[i]["ProductQuantity"];
                tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsInfo);

                if (fetchedUserTransactionLogArray[i]["BillingVATNumber"] != "")
                {
                    var tLogEntryDetailsInfoFlexContainer = document.createElement("div");
                    tLogEntryDetailsInfoFlexContainer.className = "tLogEntryDetailsInfoFlexContainer";
                    tLogEntryDetailsContainerTwo.appendChild(tLogEntryDetailsInfoFlexContainer);

                    var tLogEntryDetailsSubHeading = document.createElement("div");
                    tLogEntryDetailsSubHeading.className = "tLogEntryDetailsSubHeading";
                    tLogEntryDetailsSubHeading.innerHTML = "VAT #";
                    tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsSubHeading);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.id = "tLogEntryVATNumber" + j;
                    tLogEntryDetailsInfo.innerHTML = fetchedUserTransactionLogArray[i]["BillingVATNumber"];
                    tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsInfo);
                }

                if (fetchedUserTransactionLogArray[i]["ProductType"] == "Product/Service")
                {
                    var tLogEntryDetailsInfoFlexContainer = document.createElement("div");
                    tLogEntryDetailsInfoFlexContainer.className = "tLogEntryDetailsInfoFlexContainer";
                    tLogEntryDetailsContainerTwo.appendChild(tLogEntryDetailsInfoFlexContainer);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.id = "tLogEntryProductType" + j;
                    tLogEntryDetailsInfo.innerHTML = "&bull; Product/Service";
                    tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsInfo);
                }
                else if (fetchedUserTransactionLogArray[i]["ProductType"] == "Credits")
                {
                    var tLogEntryDetailsInfoFlexContainer = document.createElement("div");
                    tLogEntryDetailsInfoFlexContainer.className = "tLogEntryDetailsInfoFlexContainer";
                    tLogEntryDetailsContainerTwo.appendChild(tLogEntryDetailsInfoFlexContainer);

                    var tLogEntryDetailsSubHeading = document.createElement("div");
                    tLogEntryDetailsSubHeading.className = "tLogEntryDetailsSubHeading";
                    tLogEntryDetailsSubHeading.innerHTML = "Credits:";
                    tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsSubHeading);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.id = "tLogEntryProductType" + j;
                    tLogEntryDetailsInfo.innerHTML = fetchedUserTransactionLogArray[i]["CreditsQuantity"];
                    tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsInfo);
                }

                var tLogEntryDetailsInfoContainer = document.createElement("div");
                tLogEntryDetailsInfoContainer.className = "tLogEntryDetailsInfoContainer";
                tLogEntryDetailsContainerTwo.appendChild(tLogEntryDetailsInfoContainer);

                var tLogEntryDetailsInfo = document.createElement("div");
                tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                tLogEntryDetailsInfo.id = "tLogEntryTransactionType" + j;
                tLogEntryDetailsInfo.innerHTML = "&bull; " + fetchedUserTransactionLogArray[i]["TransactionType"];
                tLogEntryDetailsInfoContainer.appendChild(tLogEntryDetailsInfo);

                if (fetchedUserTransactionLogArray[i]["ProductType"] == "Gift Sent")
                {
                    var tLogEntryDetailsInfoFlexContainer = document.createElement("div");
                    tLogEntryDetailsInfoFlexContainer.className = "tLogEntryDetailsInfoFlexContainer";
                    tLogEntryDetailsContainerTwo.appendChild(tLogEntryDetailsInfoFlexContainer);

                    var tLogEntryDetailsSubHeading = document.createElement("div");
                    tLogEntryDetailsSubHeading.className = "tLogEntryDetailsSubHeading";
                    tLogEntryDetailsSubHeading.innerHTML = "Recipient:";
                    tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsSubHeading);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.id = "tLogEntryRecipients" + j;
                    tLogEntryDetailsInfo.innerHTML = fetchedUserTransactionLogArray[i]["GiftRecipient"];
                    tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsInfo);
                }
                else if (fetchedUserTransactionLogArray[i]["ProductType"] == "Bundle")
                {
                    var tLogEntryDetailsInfoFlexContainer = document.createElement("div");
                    tLogEntryDetailsInfoFlexContainer.className = "tLogEntryDetailsInfoFlexContainer";
                    tLogEntryDetailsContainerTwo.appendChild(tLogEntryDetailsInfoFlexContainer);

                    var tLogEntryDetailsSubHeading = document.createElement("div");
                    tLogEntryDetailsSubHeading.className = "tLogEntryDetailsSubHeading";
                    tLogEntryDetailsSubHeading.innerHTML = "Recipients:";
                    tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsSubHeading);

                    var tLogEntryDetailsInfo = document.createElement("div");
                    tLogEntryDetailsInfo.className = "tLogEntryDetailsInfo";
                    tLogEntryDetailsInfo.id = "tLogEntryRecipients" + j;
                    tLogEntryDetailsInfo.innerHTML = fetchedUserTransactionLogArray[i]["BundleRecipients"];
                    tLogEntryDetailsInfoFlexContainer.appendChild(tLogEntryDetailsInfo);
                }
                j++;
            }
        }
        else
        {
            var animationContainer = document.getElementById("tLogLoadingAnimationContainer");
            animationContainer.style.display = "none";
            var bulletPoints = animationContainer.getElementsByClassName("bulletPoint");
            for (i = 0; i < bulletPoints.length; i++)
            {
                bulletPoints[i].style.animationPlayState = "paused";
            }
        }

        fetchedUserTransactionLogArray = [];
    }

    function DisableFormWhileProcessing(activeForm)
    {
        //If the user tries to refresh the page, this prompts the user that a process may be active.
        window.onbeforeunload = function(event) {
            return true;
        }

        var activeFormsElements = activeForm.elements;

        for (i = 0; i < activeFormsElements.length; i++)
        {
            if (activeFormsElements[i].nodeName === "TEXTAREA" || activeFormsElements[i].nodeName === "SELECT" || activeFormsElements[i].nodeName === "INPUT")
            {
                activeFormsElements[i].disabled = true;
            }
            else if (activeFormsElements[i].nodeName === "BUTTON")
            {
                activeFormsElements[i].disabled = true;
                activeFormsElements[i].innerHTML = "";
            }
        }

        var animationContainers = activeForm.getElementsByClassName("loadingAnimationContainer");
        animationContainers[0].style.display = "block";

        var bulletPoints = activeForm.getElementsByClassName("bulletPoint");
        for (i = 0; i < bulletPoints.length; i++)
        {
            bulletPoints[i].style.animationPlayState = "running";
        }
    }

    function EnableFormWhenProcessingIsComplete(activeForm, focusTarget)
    {
        //Allows the user to refresh freely.
        window.onbeforeunload = function(event) {
            return;
        }

        var activeFormsElements = activeForm.elements;

        for (i = 0; i < activeFormsElements.length; i++)
        {
            if (activeFormsElements[i].nodeName === "TEXTAREA" || activeFormsElements[i].nodeName === "SELECT" || activeFormsElements[i].nodeName === "INPUT")
            {
                //Token Transaction Form related checks.
                if (activeFormsElements[i].name === "tokenTShippingStatus")
                {
                    activeFormsElements[i].disabled = false;

                    if (activeFormsElements[i].value == "Shipped")
                    {
                        document.getElementById("tokenTShippedAt").disabled = false;
                        document.getElementById("tokenTShippingMethod").disabled = false;
                    }
                    else if (activeFormsElements[i].value == "Processing")
                    {
                        document.getElementById("tokenTShippedAt").disabled = true;
                        document.getElementById("tokenTShippedAt").value = "";
                        document.getElementById("tokenTShippingMethod").disabled = true;
                        document.getElementById("tokenTShippingMethod").value = "";
                    }
                }
                else if (activeFormsElements[i].name === "tokenTShippingMethod")
                {
                    if (this.value == "Courier" || this.value == "Postal")
                    {
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingPhoneNumber"].disabled = false;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingCompany"].disabled = false;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingFirstName"].disabled = false;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingLastName"].disabled = false;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingAddress"].disabled = false;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingCity"].disabled = false;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingZIP"].disabled = false;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingProvince"].disabled = false;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingCountry"].disabled = false;
                
                        document.getElementById("tokenTShippingInformationContainer").style.display = "flex";
                    }
                    else
                    {
                        document.getElementById("tokenTShippingInformationContainer").style.display = "none";

                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingPhoneNumber"].disabled = true;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingPhoneNumber"].value = "";
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingCompany"].disabled = true;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingCompany"].value = "";
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingFirstName"].disabled = true;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingFirstName"].value = "";
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingLastName"].disabled = true;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingLastName"].value = "";
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingAddress"].disabled = true;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingAddress"].value = "";
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingCity"].disabled = true;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingCity"].value = "";
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingZIP"].disabled = true;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingZIP"].value = "";
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingProvince"].disabled = true;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingProvince"].value = "";
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingCountry"].disabled = true;
                        document.getElementById("tokenTDataEntryForm").elements["tokenTShippingCountry"].value = "";
                    }
                }
                else if (activeFormsElements[i].name === "tokenTShippedAt" || activeFormsElements[i].name === "tokenTShippingPhoneNumber" || activeFormsElements[i].name === "tokenTShippingCompany" || activeFormsElements[i].name === "tokenTShippingFirstName" || activeFormsElements[i].name === "tokenTShippingLastName" || activeFormsElements[i].name === "tokenTShippingAddress" || activeFormsElements[i].name === "tokenTShippingCity" || activeFormsElements[i].name === "tokenTShippingProvince" || activeFormsElements[i].name === "tokenTShippingCountry" || activeFormsElements[i].name === "tokenTShippingZIP" || activeFormsElements[i].name === "tokenTPurchaseType")
                {
                    //DO NOTHING!
                }
                //Invoice Form related checks.
                else if (activeFormsElements[i].name === "invoiceTransactionType")
                {
                    activeFormsElements[i].disabled = false;

                    if (activeFormsElements[i].value == "Self-Purchase")
                    {
                        document.getElementById("invoiceGiftRecipientContainer").hidden = true;
                        document.getElementById("invoiceGiftRecipient").disabled = true;
                        document.getElementById("invoiceBundleQuantityContainer").hidden = true;
                        document.getElementById("invoiceBundleQuantity").disabled = true;
                        document.getElementById("invoiceBundleRecipientContainer").hidden = true;
                        document.getElementById("invoiceBundleRecipient").disabled = true;
                    }
                    else if (activeFormsElements[i].value == "Gift")
                    {
                        document.getElementById("invoiceGiftRecipientContainer").hidden = false;
                        document.getElementById("invoiceGiftRecipient").disabled = false;
                        document.getElementById("invoiceBundleQuantityContainer").hidden = true;
                        document.getElementById("invoiceBundleQuantity").disabled = true;
                        document.getElementById("invoiceBundleRecipientContainer").hidden = true;
                        document.getElementById("invoiceBundleRecipient").disabled = true;
                    }
                    else if (activeFormsElements[i].value == "Bundle")
                    {
                        document.getElementById("invoiceGiftRecipientContainer").hidden = true;
                        document.getElementById("invoiceGiftRecipient").disabled = true;
                        document.getElementById("invoiceBundleQuantityContainer").hidden = false;
                        document.getElementById("invoiceBundleQuantity").disabled = false;
                        document.getElementById("invoiceBundleRecipientContainer").hidden = false;
                        document.getElementById("invoiceBundleRecipient").disabled = false;
                    }
                }
                else if (activeFormsElements[i].name === "invoicePaymentStatus")
                {
                    activeFormsElements[i].disabled = false;

                    if (activeFormsElements[i].value == "Paid")
                    {
                        document.getElementById("invoicePaidAt").disabled = false;
                        document.getElementById("invoicePaymentType").disabled = false;
                    }
                    else if (activeFormsElements[i].value == "Pending")
                    {
                        document.getElementById("invoicePaidAt").disabled = true;
                        document.getElementById("invoicePaymentType").disabled = true;
                    }
                }
                else if (activeFormsElements[i].name === "invoiceShippingStatus")
                {
                    activeFormsElements[i].disabled = false;

                    if (activeFormsElements[i].value == "Shipped")
                    {
                        document.getElementById("invoiceShippedAt").disabled = false;
                        document.getElementById("invoiceShippingMethod").disabled = false;
                    }
                    else if (activeFormsElements[i].value == "Processing")
                    {
                        document.getElementById("invoiceShippedAt").disabled = true;
                        document.getElementById("invoiceShippedAt").value = "";
                        document.getElementById("invoiceShippingMethod").disabled = true;
                        document.getElementById("invoiceShippingMethod").value = "";
                    }
                }
                else if(activeFormsElements[i].name === "invoiceProductType")
                {
                    activeFormsElements[i].disabled = false;

                    if (activeFormsElements[i].value === "Credits")
                    {
                        document.getElementById("invoiceCreditsQuantityContainer").hidden = false;
                        document.getElementById("invoiceCreditsQuantity").disabled = false;
                    }
                    else
                    {
                        document.getElementById("invoiceCreditsQuantityContainer").hidden = true;
                        document.getElementById("invoiceCreditsQuantity").disabled = true;
                    }
                }
                else if (activeFormsElements[i].name === "invoiceGiftRecipient" || activeFormsElements[i].name === "invoiceBundleQuantity" || activeFormsElements[i].id === "invoiceBundleRecipient" || activeFormsElements[i].id === "invoicePaidAt" || activeFormsElements[i].id === "invoicePaymentType" || activeFormsElements[i].id === "invoiceShippedAt" || activeFormsElements[i].id === "invoiceShippingMethod" || activeFormsElements[i].name === "invoiceCreditsQuantity")
                {
                    //DO NOTHING!
                }
                else if (activeFormsElements[i].name === "invoiceShippingSameAsBilling")
                {
                    activeFormsElements[i].disabled = false;
                    activeFormsElements[i].checked = false;

                    const fakeEvent = new MouseEvent("change", {});
                    document.getElementById("invoiceShippingSameAsBilling").dispatchEvent(fakeEvent);
                }
                //Registration Form related checks.
                else if (activeFormsElements[i].name === "registrationFirstName" && focusTarget == "registrationFirstName")
                {
                    activeFormsElements[i].disabled = false;
                    activeFormsElements[i].focus();
                }
                else if (activeFormsElements[i].name === "registrationEmail" && focusTarget == "registrationEmail")
                {
                    activeFormsElements[i].disabled = false;
                    activeFormsElements[i].focus();
                }
                //Otherwise do this.
                else
                {
                    activeFormsElements[i].disabled = false;
                }
            }
            else if (activeFormsElements[i].nodeName === "BUTTON")
            {
                activeFormsElements[i].disabled = false;
                activeFormsElements[i].innerHTML = "SUBMIT";
            }
        }
        var animationContainers = activeForm.getElementsByClassName("loadingAnimationContainer");
        animationContainers[0].style.display = "none";

        var bulletPoints = activeForm.getElementsByClassName("bulletPoint");
        for (i = 0; i < bulletPoints.length; i++)
        {
            bulletPoints[i].style.animationPlayState = "paused";
        }
    }

    function Logout()
    {
        location.href = 'mqw_Logout.php';
    }
</script>