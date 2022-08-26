//Variables



var tabIsOpen = 0;
var tabSelected = 0;



//Calling Functions



$(document).ready(function()
{
    $("#businessTab").css({"height" : "0"});
    $("#productsTab").css({"height" : "0"});
    $("#affiliationsTab").css({"height" : "0"});
    $("#newsTab").css({"height" : "0"});
    $("#aboutUsTab").css({"height" : "0"});
    $("#productsTab").css({"-pointer-events" : "none"});
    $("#affiliationsTab").css({"pointer-events" : "none"});


    $('#businessText').click(function()
    {
        if (tabSelected == 0)
        {
            tabSelected = 1;
            HighlightSection();
            setTimeout(OpenTab, 500);
            setTimeout(DisplayContents, 1500);
        }
        else if (tabSelected != 0 && tabSelected != 1 && tabIsOpen == 1)
        {
            HideContents();
            setTimeout(CloseTab, 500)
            setTimeout(DeselectSection, 1500);
        }
    });
    $('#productsText').click(function()
    {
        if (tabSelected == 0)
        {
            tabSelected = 2;
            HighlightSection();
            setTimeout(OpenTab, 500);
            setTimeout(DisplayContents, 1500);
        }
        else if (tabSelected != 0 && tabSelected != 2 && tabIsOpen == 1)
        {
            HideContents();
            setTimeout(CloseTab, 500)
            setTimeout(DeselectSection, 1500);
        }
    });
    $('#affiliationsText').click(function()
    {
        if (tabSelected == 0)
        {
            tabSelected = 3;
            HighlightSection();
            setTimeout(OpenTab, 500);
            setTimeout(DisplayContents, 1500);
        }
        else if (tabSelected != 0 && tabSelected != 3 && tabIsOpen == 1)
        {
            HideContents();
            setTimeout(CloseTab, 500)
            setTimeout(DeselectSection, 1500);
        }
    });
    $('#newsText').click(function()
    {
        if (tabSelected == 0)
        {
            tabSelected = 4;
            HighlightSection();
            setTimeout(OpenTab, 500);
            setTimeout(DisplayContents, 1500);
        }
        else if (tabSelected != 0 && tabSelected != 4 && tabIsOpen == 1)
        {
            HideContents();
            setTimeout(CloseTab, 500)
            setTimeout(DeselectSection, 1500);
        }
    });
    $('#aboutUsText').click(function()
    {
        if (tabSelected == 0)
        {
            tabSelected = 5;
            HighlightSection();
            setTimeout(OpenTab, 500);
            setTimeout(DisplayContents, 1500);
        }
        else if (tabSelected != 0 && tabSelected != 5 && tabIsOpen == 1)
        {
            HideContents();
            setTimeout(CloseTab, 500)
            setTimeout(DeselectSection, 1500);
        }
    });

});



//Functions



function HighlightSection()
{
    if (tabSelected == 1)
    {
        $("#businessBox").css({"-webkit-animation" : "HighlightSection 0.5s linear forwards"});
    }
    if (tabSelected == 2)
    {
        $("#productsBox").css({"-webkit-animation" : "HighlightSection 0.5s linear forwards"});
    }
    if (tabSelected == 3)
    {
        $("#affiliationsBox").css({"-webkit-animation" : "HighlightSection 0.5s linear forwards"});
    }
    if (tabSelected == 4)
    {
        $("#newsBox").css({"-webkit-animation" : "HighlightSection 0.5s linear forwards"});
    }
    if (tabSelected == 5)
    {
        $("#aboutUsBox").css({"-webkit-animation" : "HighlightSection 0.5s linear forwards"});
    }    
}

function OpenTab()
{
    if (tabSelected == 1)
    {
        setTimeout(CloseDelay, 2000);
        $("#businessTab").css({"-webkit-animation" : "OpenTabBlack 1s linear forwards"});
    }
    if (tabSelected == 2)
    {
        setTimeout(CloseDelay, 2000);
        $("#productsTab").css({"-webkit-animation" : "OpenTabWhite 1s linear forwards"});
    }
    if (tabSelected == 3)
    {
        setTimeout(CloseDelay, 2000);
        $("#affiliationsTab").css({"-webkit-animation" : "OpenTabWhite 1s linear forwards"});
    }
    if (tabSelected == 4)
    {
        setTimeout(CloseDelay, 2000);
        $("#newsTab").css({"-webkit-animation" : "OpenTabBlack 1s linear forwards"});
    }
    if (tabSelected == 5)
    {
        setTimeout(CloseDelay, 2000);
        $("#aboutUsTab").css({"-webkit-animation" : "OpenTabBlack 1s linear forwards"});
    }
}

function CloseTab()
{
    if (tabSelected == 1)
    {
        tabIsOpen = 0;
        $("#businessTab").css({"-webkit-animation" : "CloseTabBlack 1s linear forwards"});
    }
    if (tabSelected == 2)
    {
        tabIsOpen = 0;
        $("#productsTab").css({"-webkit-animation" : "CloseTabWhite 1s linear forwards"});
    }
    if (tabSelected == 3)
    {
        tabIsOpen = 0;
        $("#affiliationsTab").css({"-webkit-animation" : "CloseTabWhite 1s linear forwards"});
    }
    if (tabSelected == 4)
    {
        tabIsOpen = 0;
        $("#newsTab").css({"-webkit-animation" : "CloseTabBlack 1s linear forwards"});
    }
    if (tabSelected == 5)
    {
        tabIsOpen = 0;
        $("#aboutUsTab").css({"-webkit-animation" : "CloseTabBlack 1s linear forwards"});
    }
}

function DeselectSection()
{
    if (tabSelected == 1)
    {
        $("#businessBox").css({"-webkit-animation" : "DeselectSection 0.5s linear forwards"});
        tabSelected = 0;
    }
    if (tabSelected == 2)
    {
        $("#productsBox").css({"-webkit-animation" : "DeselectSection 0.5s linear forwards"});
        tabSelected = 0;
    }
    if (tabSelected == 3)
    {
        $("#affiliationsBox").css({"-webkit-animation" : "DeselectSection 0.5s linear forwards"});
        tabSelected = 0;
    }
    if (tabSelected == 4)
    {
        $("#newsBox").css({"-webkit-animation" : "DeselectSection 0.5s linear forwards"});
        tabSelected = 0;
    }
    if (tabSelected == 5)
    {
        $("#aboutUsBox").css({"-webkit-animation" : "DeselectSection 0.5s linear forwards"});
        tabSelected = 0;
    }
}

function CloseDelay ()
{
    tabIsOpen = 1;
}

function DisplayContents ()
{
    if (tabSelected == 1)
    {
        $("#businessTab").css({"pointer-events" : "auto"});
        $("#businessTabContents").css({"-webkit-animation" : "FadeIn 0.5s linear forwards"});
    }
    if (tabSelected == 2)
    {
        $("#productsTab").css({"pointer-events" : "auto"});
        $("#productsTabContents").css({"-webkit-animation" : "FadeIn 0.5s linear forwards"});
    }
    if (tabSelected == 3)
    {
        $("#affiliationsTab").css({"pointer-events" : "auto"});
        $("#affiliationsTabContents").css({"-webkit-animation" : "FadeIn 0.5s linear forwards"});
    }
    if (tabSelected == 4)
    {
        $("#newsTab").css({"pointer-events" : "auto"});
        $("#newsTabContents").css({"-webkit-animation" : "FadeIn 0.5s linear forwards"});
    }
    if (tabSelected == 5)
    {
        $("#aboutUsTab").css({"pointer-events" : "auto"});
        $("#aboutUsTabContents").css({"-webkit-animation" : "FadeIn 0.5s linear forwards"});
    }

}

function HideContents ()
{
    if (tabSelected == 1)
    {
        $("#businessTab").css({"-pointer-events" : "none"});
        $("#businessTabContents").css({"-webkit-animation" : "FadeOut 0.5s linear forwards"});
    }
    if (tabSelected == 2)
    {
        $("#productsTab").css({"-pointer-events" : "none"});
        $("#productsTabContents").css({"-webkit-animation" : "FadeOut 0.5s linear forwards"});
    }
    if (tabSelected == 3)
    {
        $("#affiliationsTab").css({"pointer-events" : "none"});
        $("#affiliationsTabContents").css({"-webkit-animation" : "FadeOut 0.5s linear forwards"});
    }
    if (tabSelected == 4)
    {
        $("#newsTab").css({"-pointer-events" : "none"});
        $("#newsTabContents").css({"-webkit-animation" : "FadeOut 0.5s linear forwards"});
    }
    if (tabSelected == 5)
    {
        $("#aboutUsTab").css({"-pointer-events" : "none"});
        $("#aboutUsTabContents").css({"-webkit-animation" : "FadeOut 0.5s linear forwards"});
    }
}