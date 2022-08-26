<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>MQ WALLET - Login</title>
        <link rel="stylesheet" type="text/css" href="css/stylesheet_All.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
        <div id="loginContainer">
            <div id="loginInternalContainer">
                <div id="loginCompanyLogoContainer">
                    <img id="loginCompanyLogo" src="assets/images/MQWLogoBlack_200p.png" srcset="assets/images/MQWLogoBlack_40p.png 0.5x, assets/images/MQWLogoBlack_80p.png 1x, assets/images/MQWLogoBlack_120p.png 1.5x, assets/images/MQWLogoBlack_160p.png 2x, assets/images/MQWLogoBlack_200p.png 2.5x, assets/images/MQWLogoBlack_240p.png 3x, assets/images/MQWLogoBlack_280p.png 3.5x, assets/images/MQWLogoBlack_320p.png 4x, assets/images/MQWLogoBlack_360p.png 4.5x, assets/images/MQWLogoBlack_400p.png 5x"></img>
                </div>
                <form id="loginForm">
                    <div class="textInputContainer">
                        <input class="textInput" type="email" name="loginEmail" placeholder="" required></input>
                        <span class="floatingLabel">Email</span>
                    </div>
                    <div class="textInputContainer">
                        <input class="textInput" type="password" name="loginPassword" placeholder="" required></input>
                        <span class="floatingLabel">Password</span>
                    </div>
                    <div class="phpResponse" id="loginErrorMessage"></div>
                    <div class="buttonContainer">
                        <button id="loginButton" type="submit">LOGIN</button>
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
        if (localStorage.getItem("alertMessage") != null)
        {
            alert(localStorage.getItem("alertMessage"));
            localStorage.removeItem("alertMessage");
        }

        document.getElementById("loginForm").addEventListener('submit', function(event) {
            event.preventDefault();
            DisableFormWhileProcessing();
            LoginAJAX();
        });
    };

    function LoginAJAX()
    {
        const formElements = document.getElementById("loginForm").elements;
        const formData = new FormData();

        for (i = 0; i < formElements.length; i++)
        {
            if (formElements[i].nodeName === "INPUT")
            {
                formData.append(formElements[i].name, formElements[i].value);
            }
        }

        var initiateLogin = new XMLHttpRequest();

        initiateLogin.open("POST", "mqw_LoginVerification.php", true);
        initiateLogin.send(formData);

        initiateLogin.onreadystatechange = function () {
            if (initiateLogin.readyState === 4)
            {
                if (initiateLogin.status === 200)
                {
                    if (this.responseText.indexOf('home') > -1)
                    {
                        window.location.href = this.responseText;
                    }
                    else
                    {
                        document.getElementById("loginErrorMessage").innerHTML = this.responseText;
                        EnableFormWhenProcessingIsComplete();
                    }
                }
                else
                {
                    document.getElementById("loginErrorMessage").innerHTML = "HTTP Request Error: " + initiateLogin.status + ", " + initiateLogin.statusText;
                    EnableFormWhenProcessingIsComplete();
                }
            }
        }
    }

    function DisableFormWhileProcessing ()
    {
        document.getElementById("loginButton").disabled = true;
        document.getElementById("loginButton").innerHTML = "";
        document.getElementById("bulletPoint1").style.animationPlayState = "running";
        document.getElementById("bulletPoint2").style.animationPlayState = "running";
        document.getElementById("bulletPoint3").style.animationPlayState = "running";
        document.getElementById("loginLoadingAnimationContainer").style.display = "block";

        var fElements = document.getElementById("loginForm").elements;
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
        document.getElementById("loginButton").disabled = false;
        document.getElementById("loginButton").innerHTML = "LOGIN";
        document.getElementById("bulletPoint1").style.animationPlayState = "paused";
        document.getElementById("bulletPoint2").style.animationPlayState = "paused";
        document.getElementById("bulletPoint3").style.animationPlayState = "paused";
        document.getElementById("loginLoadingAnimationContainer").style.display = "none";

        var fElements = document.getElementById("loginForm").elements;
        for (i = 0; i < fElements.length; i++)
        {
            if (fElements[i].nodeName === "INPUT")
            {
                fElements[i].disabled = false;
            }
        }
    }
</script>