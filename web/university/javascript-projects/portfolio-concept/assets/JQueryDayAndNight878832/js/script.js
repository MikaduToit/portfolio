/////////// write a function to check the time ///////////
    var date = new Date();
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var time = hours + ":" + minutes;



  function getTime()
  {
    if (date.getHours() < 10)
    {
      var newHours = "0" + date.getHours();
    }
    else
    {
      var newHours = " " + date.getHours();
    }

    if (date.getMinutes() < 10)
    {
      var newMinutes = "0" + date.getMinutes();
    }
    else
    {
      var newMinutes = " " + date.getMinutes();
    }

    $("#time").html(newHours + " : " + newMinutes);
  }


  function changeBackground()
  {
    if ((hours < 7) && (hours >= 5))
    {
      console.log("sunrise");
      $("body").addClass("sunrise");
      $("#time").css({"color": "green", "text-shadow": "5px 5px black"});

    }
    if ((hours < 17) && (hours >= 7))
    {
      console.log("day");
      $("body").addClass("day");
      $("#time").css({"color": "red", "text-shadow": "5px 5px black"});
    }
    if ((hours < 19) && (hours >= 17))
    {
      console.log("sunset");
      $("body").addClass("sunset");
      $("#time").css({"color": "white", "text-shadow": "5px 5px black"});
    }
    if ((hours < 24) && (hours >= 19))
    {
      console.log("night");
      $("body").addClass("night");
      $("#time").css({"color": "yellow", "text-shadow": "5px 5px black"});
    }
    if ((hours < 5) && (hours >= 0))
    {
      console.log("night");
      $("body").addClass("night");
      $("#time").css({"color": "yellow", "text-shadow": "5px 5px black"});
    }

  }

  function dontPress ()
  {
    $("#button").hide();
    $("#text").show();
    audio.currentTime = 0;
    audio.play();
  }

  $(document).ready(function()
  {
    document.getElementsByTagName("html")[0].style.overflow="hidden";
    document.body.style.margin="0";
    document.body.style.display="block";
    document.body.style.transformOrigin="top left";
    document.body.style.transform="scale("+Math.min(window.innerWidth/document.body.offsetWidth, window.innerHeight/document.body.offsetHeight, 1)+")";

    var audio = document.getElementById('audio');

    $("#text").hide();
  	getTime();
    changeBackground();

    $('#button').click(function()
  {
    dontPress();
  });

  });
