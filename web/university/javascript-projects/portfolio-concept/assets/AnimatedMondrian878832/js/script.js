
$(document).ready(function()
{
    document.getElementsByTagName("html")[0].style.overflow="hidden";
    document.body.style.margin="0";
    document.body.style.display="inline-block";
    document.body.style.transformOrigin="top left";
    document.body.style.transform="scale("+Math.min(window.innerWidth/document.body.offsetWidth, window.innerHeight/document.body.offsetHeight, 1)+")";
});