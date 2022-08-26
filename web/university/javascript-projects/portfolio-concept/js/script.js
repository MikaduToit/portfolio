


//Variables



//click checker booleans
var firstClick = true;
var interactable = false;

//fetchCurrentRotation Variables
var degPerSecond = 120;
var negativeAngle;
var positiveAngle;
var newRotationSpeed;
var zeroAngle;

//animation controller variables
var projectselected = false;
var canDeselect = false;
var idleAnimStartPoint = 0;
var desktopAnim = true;
var aboutMeCanOpen = false;
var aboutMeOpen = false;
var canCloseTheOpenAboutMe = false;



//Calling Functions



$(document).ready(function()
{
    $("#aboutMeSquare").css({"-webkit-animation" : "AboutMeSquareAnimation 10s linear infinite forwards"});
    $("#hi-aboutMe-text").css({"-webkit-animation" : "FadeIn 3s ease-in forwards"});
    $("#pivots").css({"-webkit-animation" : "StarRotation 10s linear infinite forwards"});
    $("#header1").css({"opacity" : "0"});
    $("#header2").css({"opacity" : "0"});
    $("#header3").css({"opacity" : "0"});
    $("#header4").css({"opacity" : "0"});
    $("#header5").css({"opacity" : "0"});
    $("#project1").css({"opacity" : "0"});
    $("#project2").css({"opacity" : "0"});
    $("#project3").css({"opacity" : "0"});
    $("#project4").css({"opacity" : "0"});
    $("#project5").css({"opacity" : "0"});


    $('#aboutMeSquare').click(function()
    {
        if (firstClick == true)
        {
            firstClick = false;
            setTimeout(InteractableDelay, 3000);
            $("#parallelogram1").css({"-webkit-animation" : "StarCreation 2s linear forwards"});
            $("#parallelogram2").css({"-webkit-animation" : "StarCreation 2s linear forwards"});
            $("#parallelogram3").css({"-webkit-animation" : "StarCreation 2s linear forwards"});
            $("#parallelogram4").css({"-webkit-animation" : "StarCreation 2s linear forwards"});
            $("#parallelogram5").css({"-webkit-animation" : "StarCreation 2s linear forwards"});
            setTimeout(ProjectPreviewFadeInDelay, 2000);
            $("#hi-aboutMe-text").css({"-webkit-animation" : "FadeOut 1.5s ease-in forwards"});
            setTimeout(AboutMeTextTransition, 1500);
        }  

        if (firstClick == false && aboutMeCanOpen == true && projectselected == false)
        {
            if (aboutMeOpen == false && canCloseTheOpenAboutMe == false)
            {
                aboutMeOpen = true;
                setTimeout(CanCloseAboutMe, 2000);
                $("#aboutMe").css({"z-index" : "2"});

                if(window.innerWidth >= window.innerHeight)
                {
                    $("#aboutMe").css({"-webkit-animation" : "EnlargeAboutMeDesktop 2s linear forwards"});
                    $("#aboutMe-textcontainer").css({"-webkit-animation" : "FadeIn 2s linear forwards"});
                }
                if(window.innerHeight > window.innerWidth)
                {
                    $("#aboutMe").css({"-webkit-animation" : "EnlargeAboutMeMobile 2s linear forwards"});
                    $("#aboutMe-textcontainer").css({"-webkit-animation" : "FadeIn 2s linear forwards"});
                }
            }
            if (aboutMeOpen == true  && canCloseTheOpenAboutMe == true)
            {
                canCloseTheOpenAboutMe = false;
                setTimeout(AboutMeOpenDelay, 2000);

                if(window.innerWidth >= window.innerHeight)
                {
                    $("#aboutMe").css({"-webkit-animation" : "ShrinkAboutMeDesktop 2s linear forwards"});
                    $("#aboutMe-textcontainer").css({"-webkit-animation" : "FadeOut 2s linear forwards"});
                }
                if(window.innerHeight > window.innerWidth)
                {
                    $("#aboutMe").css({"-webkit-animation" : "ShrinkAboutMeMobile 2s linear forwards"});
                    $("#aboutMe-textcontainer").css({"-webkit-animation" : "FadeOut 2s linear forwards"});
                }
            }
        }     
    });

    $('#parallelogram1').click(function()
    {
        if (interactable == true)
        {
            if (projectselected == false)
            {
                idleAnimStartPoint = 1;
            }

            ProjectAnimationController();
        }
    });

    $('#parallelogram2').click(function()
    {
        if (interactable == true)
        {
            if (projectselected == false)
            {
                idleAnimStartPoint = 2;
            }

            ProjectAnimationController();
        }
    });

    $('#parallelogram3').click(function()
    {
        if (interactable == true)
        {
            if (projectselected == false)
            {
                idleAnimStartPoint = 3;
            }

            ProjectAnimationController();
        }
    });

    $('#parallelogram4').click(function()
    {
        if (interactable == true)
        {
            if (projectselected == false)
            {
                idleAnimStartPoint = 4;
            }

            ProjectAnimationController();
        }
    });

    $('#parallelogram5').click(function()
    {
        if (interactable == true)
        {
            if (projectselected == false)
            {
                idleAnimStartPoint = 5;
            }

            ProjectAnimationController();
        }
    });
});



//Functions



/*
Source Reference:
The contents of the "fetchCurrentRotation" function below were found at the following url: "https://css-tricks.com/get-value-of-css-rotation-through-javascript/"
and were then modified for use in this project!
*/
function fetchCurrentRotation()
{
    var el = document.getElementById("pivots");
    var st = window.getComputedStyle(el, null);
    var tr = st.getPropertyValue("-webkit-transform") ||
             st.getPropertyValue("-moz-transform") ||
             st.getPropertyValue("-ms-transform") ||
             st.getPropertyValue("-o-transform") ||
             st.getPropertyValue("transform") ||
             "FAIL";

    var values = tr.split('(')[1].split(')')[0].split(',');
    var a = values[0];
    var b = values[1];
    var c = values[2];
    var d = values[3];

    var scale = Math.sqrt(a*a + b*b);

    var sin = b/scale;

    var angle = Math.round(Math.atan2(b, a) * (180/Math.PI));

    if (angle == -360)
    {
        document.documentElement.style.setProperty('--currentRotation', angle + "deg");
        //positiveAngle = Math.abs(angle);
        //newRotationSpeed = (((360 - positiveAngle)/degPerSecond).toFixed(3));
        console.log(angle);
    }
    if (angle == 0)
    {
        zeroAngle = -360;
        document.documentElement.style.setProperty('--currentRotation', zeroAngle + "deg");
        //positiveAngle = Math.abs(angle);
        //newRotationSpeed = (((360 - positiveAngle)/degPerSecond).toFixed(3));
        console.log(zeroAngle);
    }
    if(angle > 0)
    {
        negativeAngle = (-360 + angle);
        document.documentElement.style.setProperty('--currentRotation', negativeAngle + "deg");
        //positiveAngle = Math.abs(negativeAngle);
        //newRotationSpeed = (((360 - positiveAngle)/degPerSecond).toFixed(3));
        console.log(negativeAngle);
    }
    if (angle < 0)
    {
        document.documentElement.style.setProperty('--currentRotation', angle + "deg");
        //positiveAngle = Math.abs(angle);
        //newRotationSpeed = (((360 - positiveAngle)/degPerSecond).toFixed(3));
        console.log(angle);
             
    }
}

function InteractableDelay()
{
    interactable = true;
}

function ProjectPreviewFadeInDelay()
{
    $("#header1").css({"-webkit-animation" : "FadeIn 1s ease-in forwards"});
    $("#header2").css({"-webkit-animation" : "FadeIn 1s ease-in forwards"});
    $("#header3").css({"-webkit-animation" : "FadeIn 1s ease-in forwards"});
    $("#header4").css({"-webkit-animation" : "FadeIn 1s ease-in forwards"});
    $("#header5").css({"-webkit-animation" : "FadeIn 1s ease-in forwards"});
    $("#project1").css({"-webkit-animation" : "FadeIn 1s ease-in forwards"});
    $("#project2").css({"-webkit-animation" : "FadeIn 1s ease-in forwards"});
    $("#project3").css({"-webkit-animation" : "FadeIn 1s ease-in forwards"});
    $("#project4").css({"-webkit-animation" : "FadeIn 1s ease-in forwards"});
    $("#project5").css({"-webkit-animation" : "FadeIn 1s ease-in forwards"});
}

function AboutMeTextTransition()
{
    $("#hi-aboutMe-text").html("About Me");
    $("#hi-aboutMe-text").css({"font-family" : "EngraversGothic"});
    $("#hi-aboutMe-text").css({"font-size" : "1.5vmax"});
    $("#hi-aboutMe-text").css({"text-shadow" : "0.1vmax 0.1vmax 0.1vmax black"});
    $("#hi-aboutMe-text").css({"line-height" : "2vmax"});
    $("#hi-aboutMe-text").css({"top" : "0.5vmax"});
    $("#hi-aboutMe-text").css({"-webkit-animation" : "FadeIn 1.5s ease-in forwards"});
    aboutMeCanOpen = true;
}

function CanCloseAboutMe()
{
    canCloseTheOpenAboutMe = true; 
}

function AboutMeOpenDelay()
{
    aboutMeOpen = false;
    $("#aboutMe").css({"z-index" : "0"});
}

function ExpansionDelay()
{  
    if (idleAnimStartPoint == 1 && desktopAnim == true)
    {
        $("#parallelogram1").css({"-webkit-animation" : "EnlargeProjectDesktop 2s linear forwards"});
        $("#mondrianProjectContainer").css({"-webkit-animation" : "FadeToColour 2s linear forwards"});
        setTimeout(Loadiframe, 2000);
    }
    if (idleAnimStartPoint == 1 && desktopAnim == false)
    {
        $("#parallelogram1").css({"-webkit-animation" : "EnlargeProjectMobile 2s linear forwards"});
        $("#mondrianProjectContainer").css({"-webkit-animation" : "FadeToColour 2s linear forwards"});
        setTimeout(Loadiframe, 2000);
    }
    if (idleAnimStartPoint == 2 && desktopAnim == true)
    {
        $("#parallelogram2").css({"-webkit-animation" : "EnlargeProjectDesktop 2s linear forwards"});
        $("#jqueryProjectContainer").css({"-webkit-animation" : "FadeToColour 2s linear forwards"});
        setTimeout(Loadiframejquery, 2000);
    }
    if (idleAnimStartPoint == 2 && desktopAnim == false)
    {
        $("#parallelogram2").css({"-webkit-animation" : "EnlargeProjectMobile 2s linear forwards"});
        $("#jqueryProjectContainer").css({"-webkit-animation" : "FadeToColour 2s linear forwards"});
        setTimeout(Loadiframejquery, 2000);
    }
    if (idleAnimStartPoint == 3 && desktopAnim == true)
    {
        $("#parallelogram3").css({"-webkit-animation" : "EnlargeProjectDesktop 2s linear forwards"});
    }
    if (idleAnimStartPoint == 3 && desktopAnim == false)
    {
        $("#parallelogram3").css({"-webkit-animation" : "EnlargeProjectMobile 2s linear forwards"});
    }
    if (idleAnimStartPoint == 4 && desktopAnim == true)
    {
        $("#parallelogram4").css({"-webkit-animation" : "EnlargeProjectDesktop 2s linear forwards"});
    }
    if (idleAnimStartPoint == 4 && desktopAnim == false)
    {
        $("#parallelogram4").css({"-webkit-animation" : "EnlargeProjectMobile 2s linear forwards"});
    }
    if (idleAnimStartPoint == 5 && desktopAnim == true)
    {
        $("#parallelogram5").css({"-webkit-animation" : "EnlargeProjectDesktop 2s linear forwards"});
    }
    if (idleAnimStartPoint == 5 && desktopAnim == false)
    {
        $("#parallelogram5").css({"-webkit-animation" : "EnlargeProjectMobile 2s linear forwards"});
    }
}

function ShrinkProject()
{
    if (idleAnimStartPoint == 1 && desktopAnim == true)
    {
        $("#parallelogram1").css({"-webkit-animation" : "ShrinkProjectDesktop 2s linear forwards"});
        $("#mondrianProjectContainer").css({"-webkit-animation" : "FadeToBW 2s linear forwards"});
        setTimeout(Unloadiframe, 1000);
    }
    if (idleAnimStartPoint == 1 && desktopAnim == false)
    {
        $("#parallelogram1").css({"-webkit-animation" : "ShrinkProjectMobile 2s linear forwards"});
        $("#mondrianProjectContainer").css({"-webkit-animation" : "FadeToBW 2s linear forwards"});
        setTimeout(Unloadiframe, 1000);
    }
    if (idleAnimStartPoint == 2 && desktopAnim == true)
    {
        $("#parallelogram2").css({"-webkit-animation" : "ShrinkProjectDesktop 2s linear forwards"});
        $("#jqueryProjectContainer").css({"-webkit-animation" : "FadeToBW 2s linear forwards"});
        setTimeout(Unloadiframejquery, 1000);
    }
    if (idleAnimStartPoint == 2 && desktopAnim == false)
    {
        $("#parallelogram2").css({"-webkit-animation" : "ShrinkProjectMobile 2s linear forwards"});
        $("#jqueryProjectContainer").css({"-webkit-animation" : "FadeToBW 2s linear forwards"});
        setTimeout(Unloadiframejquery, 1000);
    }
    if (idleAnimStartPoint == 3 && desktopAnim == true)
    {
        $("#parallelogram3").css({"-webkit-animation" : "ShrinkProjectDesktop 2s linear forwards"});
    }
    if (idleAnimStartPoint == 3 && desktopAnim == false)
    {
        $("#parallelogram3").css({"-webkit-animation" : "ShrinkProjectMobile 2s linear forwards"});
    }
    if (idleAnimStartPoint == 4 && desktopAnim == true)
    {
        $("#parallelogram4").css({"-webkit-animation" : "ShrinkProjectDesktop 2s linear forwards"});
    }
    if (idleAnimStartPoint == 4 && desktopAnim == false)
    {
        $("#parallelogram4").css({"-webkit-animation" : "ShrinkProjectMobile 2s linear forwards"});
    }
    if (idleAnimStartPoint == 5 && desktopAnim == true)
    {
        $("#parallelogram5").css({"-webkit-animation" : "ShrinkProjectDesktop 2s linear forwards"});
    }
    if (idleAnimStartPoint == 5 && desktopAnim == false)
    {
        $("#parallelogram5").css({"-webkit-animation" : "ShrinkProjectMobile 2s linear forwards"});
    }
}

function Loadiframe()
{
    $('#mondrianProject').attr('src', "assets/AnimatedMondrian878832/index.html");
}

function Unloadiframe()
{
    $('#mondrianProject').attr('src', "");
}

function Loadiframejquery()
{
    console.log("true");
    $('#jqueryProject').attr('src', "assets/JQueryDayAndNight878832/index.html");
}

function Unloadiframejquery()
{
    $('#jqueryProject').attr('src', "");
}

function ReselectionDelay()
{
    projectselected = false;
}

function CanDeselectDelay()
{
    canDeselect = true;
}

function ContinueIdleAnimationDelay()
{
    if (idleAnimStartPoint == 1)
    {
        $("#pivots").css({"animation-name" : "StarRotation"});
        $("#pivots").css({"animation-duration" : "10s"});
        $("#pivots").css({"animation-direction" : "forwards"});
        $("#pivots").css({"animation-iteration-count" : "infinite"});
        $("#pivots").css({"animation-delay" : "-10s"});
        $("#pivot1").css({"z-index" : "-1"});
    }
    if (idleAnimStartPoint == 2)
    {
        $("#pivots").css({"animation-name" : "StarRotation"});
        $("#pivots").css({"animation-duration" : "10s"});
        $("#pivots").css({"animation-direction" : "forwards"});
        $("#pivots").css({"animation-iteration-count" : "infinite"});
        $("#pivots").css({"animation-delay" : "-2s"});
        $("#pivot2").css({"z-index" : "-1"});
    }
    if (idleAnimStartPoint == 3)
    {
        $("#pivots").css({"animation-name" : "StarRotation"});
        $("#pivots").css({"animation-duration" : "10s"});
        $("#pivots").css({"animation-direction" : "forwards"});
        $("#pivots").css({"animation-iteration-count" : "infinite"});
        $("#pivots").css({"animation-delay" : "-4s"});
        $("#pivot3").css({"z-index" : "-1"});
    }
    if (idleAnimStartPoint == 4)
    {
        $("#pivots").css({"animation-name" : "StarRotation"});
        $("#pivots").css({"animation-duration" : "10s"});
        $("#pivots").css({"animation-direction" : "forwards"});
        $("#pivots").css({"animation-iteration-count" : "infinite"});
        $("#pivots").css({"animation-delay" : "-6s"});
        $("#pivot4").css({"z-index" : "-1"});
    }
    if (idleAnimStartPoint == 5)
    {
        $("#pivots").css({"animation-name" : "StarRotation"});
        $("#pivots").css({"animation-duration" : "10s"});
        $("#pivots").css({"animation-direction" : "forwards"});
        $("#pivots").css({"animation-iteration-count" : "infinite"});
        $("#pivots").css({"animation-delay" : "-8s"});
        $("#pivot5").css({"z-index" : "-1"});
    }
}

function RotateTo()
{
    if (idleAnimStartPoint == 1)
    {
        $("#pivots").css({"animation-name" : "RotateToParallelogram1"});
        $("#pivots").css({"animation-duration" : "1s"});
        $("#pivots").css({"animation-direction" : "forwards"});
        $("#pivots").css({"animation-iteration-count" : "1"});
        $("#pivots").css({"animation-delay" : "0s"});
        $("#pivot1").css({"z-index" : "1"});
    }
    if (idleAnimStartPoint == 2)
    {
        $("#pivots").css({"animation-name" : "RotateToParallelogram2"});
        $("#pivots").css({"animation-duration" : "1s"});
        $("#pivots").css({"animation-direction" : "forwards"});
        $("#pivots").css({"animation-iteration-count" : "1"});
        $("#pivots").css({"animation-delay" : "0s"});
        $("#pivot2").css({"z-index" : "1"});
    }
    if (idleAnimStartPoint == 3)
    {
        $("#pivots").css({"animation-name" : "RotateToParallelogram3"});
        $("#pivots").css({"animation-duration" : "1s"});
        $("#pivots").css({"animation-direction" : "forwards"});
        $("#pivots").css({"animation-iteration-count" : "1"});
        $("#pivots").css({"animation-delay" : "0s"});
        $("#pivot3").css({"z-index" : "1"});
    }
    if (idleAnimStartPoint == 4)
    {
        $("#pivots").css({"animation-name" : "RotateToParallelogram4"});
        $("#pivots").css({"animation-duration" : "1s"});
        $("#pivots").css({"animation-direction" : "forwards"});
        $("#pivots").css({"animation-iteration-count" : "1"});
        $("#pivots").css({"animation-delay" : "0s"});
        $("#pivot4").css({"z-index" : "1"});
    }
    if (idleAnimStartPoint == 5)
    {
        $("#pivots").css({"animation-name" : "RotateToParallelogram5"});
        $("#pivots").css({"animation-duration" : "1s"});
        $("#pivots").css({"animation-direction" : "forwards"});
        $("#pivots").css({"animation-iteration-count" : "1"});
        $("#pivots").css({"animation-delay" : "0s"});
        $("#pivot5").css({"z-index" : "1"});
    }
}

function Offcenter()
{
    if(window.innerWidth >= window.innerHeight)
    {
        desktopAnim = true;
        $("#moveAnchor").css({"-webkit-animation" : "MoveStarOffCenterDesktop 1s linear forwards"});
    }
    if(window.innerHeight > window.innerWidth)
    {
        desktopAnim = false;
        $("#moveAnchor").css({"-webkit-animation" : "MoveStarOffCenterMobile 1s linear forwards"});
    }
}

function Center()
{
    if (desktopAnim == true)
    {
        $("#moveAnchor").css({"-webkit-animation" : "MoveStarToCenterDesktop 1s linear forwards"});
    }
    if (desktopAnim == false)
    {
        $("#moveAnchor").css({"-webkit-animation" : "MoveStarToCenterMobile 1s linear forwards"});
    }
}

function ProjectAnimationController()
{
    if (projectselected == false)
    {
        projectselected = true;
        setTimeout(CanDeselectDelay, 3000);

        fetchCurrentRotation();
        RotateTo();
        Offcenter();
        setTimeout(ExpansionDelay, 1000);
    }

    if (projectselected == true && canDeselect == true)
    {
        canDeselect = false;
        ShrinkProject();
        setTimeout(Center, 2000);
        setTimeout(Unloadiframe, 2000);
        setTimeout(ReselectionDelay, 3000);
        setTimeout(ContinueIdleAnimationDelay, 2000);
    }
}