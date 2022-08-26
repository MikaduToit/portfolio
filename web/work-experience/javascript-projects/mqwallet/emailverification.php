<?php
    if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash']))
    {
        $email = htmlspecialchars($_GET['email']);
        $hash = htmlspecialchars($_GET['hash']);
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>MQ WALLET - Email Verification</title>
        <link rel="stylesheet" type="text/css" href="css/stylesheet_All.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
        <div id="loginContainer">
            <div id="loginInternalContainer">
                <div id="loginCompanyLogoContainer">
                    <img id="loginCompanyLogo" src="assets/images/MQWLogoBlack_200p.png" srcset="assets/images/MQWLogoBlack_40p.png 0.5x, assets/images/MQWLogoBlack_80p.png 1x, assets/images/MQWLogoBlack_120p.png 1.5x, assets/images/MQWLogoBlack_160p.png 2x, assets/images/MQWLogoBlack_200p.png 2.5x, assets/images/MQWLogoBlack_240p.png 3x, assets/images/MQWLogoBlack_280p.png 3.5x, assets/images/MQWLogoBlack_320p.png 4x, assets/images/MQWLogoBlack_360p.png 4.5x, assets/images/MQWLogoBlack_400p.png 5x"></img>
                </div>
                <form id="emailVerificationForm">
                    <div class="textInputContainer">
                        <input class="textInput" type="password" id="newPassword" name="newPassword" placeholder="" required></input>
                        <span class="floatingLabel">New Password</span>
                    </div>
                    <div class="textInputContainer">
                        <input class="textInput" type="password" id="confirmNewPassword"  name="confirmNewPassword" placeholder="" required></input>
                        <span class="floatingLabel">Confirm New Password</span>
                    </div>
                    <div class="phpResponse" id="emailVerificationErrorMessage"></div>
                    <div class="buttonContainer">
                        <button id="emailVerificationButton" type="submit">SUBMIT</button>
                        <div class="loadingAnimationContainer" id="loginLoadingAnimationContainer">
                            <div class="bulletPoint" id="bulletPoint1">&#8226;</div>
                            <div class="bulletPoint" id="bulletPoint2">&#8226;</div>
                            <div class="bulletPoint" id="bulletPoint3">&#8226;</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="forcePageBottomMargin"></div>
    </body>
</html>

<script type="text/javascript">
    window.onload = function()
    {
        ConfirmPassword();

        document.getElementById("emailVerificationForm").addEventListener('submit', function(event) {
            event.preventDefault();
            DisableFormWhileProcessing();
            VerifyEmailAJAX();
        });
    };

    function ConfirmPassword()
    {
        var password = document.getElementById("newPassword");
        var confirmPassword = document.getElementById("confirmNewPassword");

        function VerifyPassword()
        {
            if(password.value != confirmPassword.value)
            {
                confirmPassword.setCustomValidity("Passwords Don't Match!");
            }
            else
            {
                confirmPassword.setCustomValidity("");
            }
        }
        password.onchange = VerifyPassword;
        confirmPassword.onkeyup = VerifyPassword;
    }

    function VerifyEmailAJAX()
    {
        const email = "<?=$email?>";
        const hash = "<?=$hash?>";
        
        const formData = new FormData();
        formData.append("Email", email);
        formData.append("Hash", hash);
        formData.append("NewPassword", document.getElementById("newPassword").value);

        var verifyEmail = new XMLHttpRequest();
        verifyEmail.open("POST", "mqw_EmailVerification.php", true);
        verifyEmail.send(formData);

        verifyEmail.onreadystatechange = function () {
            if (verifyEmail.readyState === 4)
            {
                if (verifyEmail.status === 200)
                {
                    if (this.responseText == "login")
                    {
                        window.location.href = this.responseText;
                    }
                    else
                    {
                        document.getElementById("emailVerificationErrorMessage").innerHTML = this.responseText;
                        EnableFormWhenProcessingIsComplete();
                    }
                }
                else
                {
                    document.getElementById("emailVerificationErrorMessage").innerHTML = "HTTP Request Error: " + verifyEmail.status + ", " + verifyEmail.statusText;
                    EnableFormWhenProcessingIsComplete();
                }
            }
        }
    }

    function DisableFormWhileProcessing ()
    {
        document.getElementById("emailVerificationButton").disabled = true;
        document.getElementById("emailVerificationButton").innerHTML = "";
        document.getElementById("bulletPoint1").style.animationPlayState = "running";
        document.getElementById("bulletPoint2").style.animationPlayState = "running";
        document.getElementById("bulletPoint3").style.animationPlayState = "running";
        document.getElementById("loginLoadingAnimationContainer").style.display = "block";

        var fElements = document.getElementById("emailVerificationForm").elements;
        for (i = 0; i < fElements.length; i++)
        {
            if (fElements[i].nodeName === "INPUT")
            {
                fElements[i].disabled = true;
            }
        }
    }

    function EnableFormWhenProcessingIsComplete ()
    {
        document.getElementById("emailVerificationButton").disabled = false;
        document.getElementById("emailVerificationButton").innerHTML = "SUBMIT";
        document.getElementById("bulletPoint1").style.animationPlayState = "paused";
        document.getElementById("bulletPoint2").style.animationPlayState = "paused";
        document.getElementById("bulletPoint3").style.animationPlayState = "paused";
        document.getElementById("loginLoadingAnimationContainer").style.display = "none";

        var fElements = document.getElementById("emailVerificationForm").elements;
        for (i = 0; i < fElements.length; i++)
        {
            if (fElements[i].nodeName === "INPUT")
            {
                fElements[i].disabled = false;
            }
        }
    }
</script>