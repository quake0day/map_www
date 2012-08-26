
<!DOCTYPE html "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="css/style2.css" media="screen" />
    <title>GroundWave Online Simulator</title>


    <script src="http://maps.google.com/maps/api/js?v=3&sensor=true&key=AIzaSyA5rIPRrzlI8xgWMcA1dV92_Tn_iyT04ps"
            type="text/javascript"></script>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script src="src/dragzoom_packed.js" type="text/javascript"></script>
<script src="src/jquery.js" type="text/javascript"></script>
<script type="text/javascript" src="js/labels.js"></script>
<script type="text/javascript" src="js/ruler.js"></script>
<link href="facefiles/facebox.css" media="screen" rel="stylesheet" type="text/css" />
<script src="facefiles/facebox.js" type="text/javascript"></script> <script type="text/javascript">
jQuery(document).ready(function($) {
  $('a[rel*=facebox]').facebox() 
      })
      </script> 

<script type="text/javascript">
// max number of nodes
var MAX = 2;

var freq;
var pol;
var height;
var heightr;
var bandwidth;

var row_no = 0;
var map = null;
var mgr = null;
var markers = [];
var geocoder = null;
var cLat = null;
var cLng = null;
var chart = null;


var geocoderService = null;
var elevationService = null;
var directionsService = null;
var mousemarker = null;
var polyline = null;
var elevations = null;
var infoWindow = new google.maps.InfoWindow;
google.load('visualization', '1',
  {'packages': ['table']});
google.setOnLoadCallback(initialize);
// Load the Visualization API and the piechart package.
google.load("visualization", "1", {packages: ["columnchart"]});



function updateMarkerStatus(str,id) {
  document.getElementById('markerStatus'+id).innerHTML = str;
}

function updateMarkerPosition(latLng,id) {
  document.getElementById('info'+id).innerHTML = [
    latLng.lat(),
      latLng.lng()
      ].join(', ');
}

function updateMarkerAddress(str,id) {
  document.getElementById('address'+id).innerHTML = str;
}  

function initialize() {
  var myOptions = {
    zoom: 6,
      center:  new google.maps.LatLng(-41.60,173.84) ,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}

        };
        map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
        chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));



        geocoderService = new google.maps.Geocoder();
        elevationService = new google.maps.ElevationService();
        directionsService = new google.maps.DirectionsService();

        google.visualization.events.addListener(chart, 'onmouseover', function(e) {
          if (mousemarker == null) {
            mousemarker = new google.maps.Marker({
              position: elevations[e.row].location,
                map: map,
                icon: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
          });
        } else {
          mousemarker.setPosition(elevations[e.row].location);
        }
      });



    }    
    function reset() {
      clearOverlays();
    }


// Judge whether its all empty  
function isEmpty(expression) {  
  var arry;  
  if (typeof (expression) == "undefined") {  
    arry = $(".isEmpty");  
    } else {  
      arry = $(expression);  
    }  
    for (i = 0; i < arry.length; i++) {  
      var cur = $(arry[i]);  
      if (cur.val() == "") {  
        alert(cur.attr("info") + "cannot be empty");  
        cur.focus();  
        return false;  
        }  
    }  
    return true;  
}  


function postdata(data,randomnum,path_info){                             //提交数据函数

  $.ajax({                                                 //调用jquery的ajax方法
    type: "POST",                                     //设置ajax方法提交数据的形式
      url: "get.php",                                      
      data: "data="+data+"&rand="+randomnum+"&path_info="+path_info,
      success: function(msg){  

        $("#data").html(msg);
        $("#data").fadeIn();
        //Examples of how to assign the ColorBox event to elements
        $(".group1").colorbox({rel:'group1'});
        $(".group2").colorbox({rel:'group2', transition:"fade"});
        $(".group3").colorbox({rel:'group3', transition:"none", width:"75%", height:"75%"});
        $(".group4").colorbox({rel:'group4', slideshow:true});
        $(".ajax").colorbox();
        $(".youtube").colorbox({iframe:true, innerWidth:425, innerHeight:344});
        $(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
        $(".inline").colorbox({inline:true, width:"50%"});
        $(".callbacks").colorbox({
          onOpen:function(){ alert('onOpen: colorbox is about to open'); },
            onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
            onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
            onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
            onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
                });

                //Example of preserving a JavaScript event for inline calls.
                $("#click").click(function(){ 
                  $('#click').css({"background-color":"#f00", "color":"#fff", "cursor":"inherit"}).text("Open this window again and this message will still be here.");
                  return false;
                });
     }
    }
);
}
$(document).ready(function(){  
  freq=$("#para tr").children('td').eq(1).html();
  pol=$("#para tr").children('td').eq(3).html();
  height=$("#para tr").children('td').eq(5).html();  
  heightr=$("#para tr").children('td').eq(7).html();
  bandwidth=$("#para tr").children('td').eq(9).html();

  $('<div id="loading" style="display:none;width:500px; float:left;"><h1>Simulating......<img src = "images/ajax-loader.gif"></h1></div>').insertBefore('#data')
    .ajaxStart(function(){
      $(this).show();
}).ajaxStop(function(){
  $(this).hide();
});
$("#button").click(function(){          //当按钮button被点击时的处理函数
  var path_info = [];
  $("#tips").fadeOut();
  $("#data").fadeOut();

  get_path_info(elevations);
  showMarkers();                                     //button被点击时执行postdata函数

});
$("#add").click(function(){              
  $("#data").fadeOut();
  addruler();                                
  $("#infoPanel").fadeIn();
  $("#chart_div").fadeIn();
  $("#tips").fadeOut();

});
$("#setp").click(function(){              

  $("#set").fadeIn();

});
$("#testt").click(function(){              

  $("#set").fadeIn();

});
$("#sav").click(function(){   

  freq = $("#fq").val()  
    if (freq > 30 || freq < 0.01)
    {
      $(".validate_error").text("Frequency Out of Range !");
    }
    else{
      $(".validate_error").text("");
    }
    pol = $("#pol").val() 
      height = $("#height").val()  
      heightr = $("#heightr").val()
      bandwidth = $("#bandwidth").val()      
      $("#para tr").children('td').eq(1).html(freq);
    $("#para tr").children('td').eq(3).html(pol);
    $("#para tr").children('td').eq(5).html(height);  
    $("#para tr").children('td').eq(7).html(heightr);
    $("#para tr").children('td').eq(9).html(bandwidth);

    $("#set").fadeOut();
});
$("#reset").click(function(){    
	 
  $("#data").fadeOut();
  $("#tips").fadeIn();
  $("#loading").fadeOut();
  $("#infoPanel").fadeOut();
  $("#chart_div").fadeOut();
  reset();   

});
});

</script>
        <!-- Libraries -->
        <link type="text/css" href="css/layout.css" rel="stylesheet" />	
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script type="text/javascript" src="js/easyTooltip.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
        <script type="text/javascript" src="js/jquery.wysiwyg.js"></script>
        <script type="text/javascript" src="js/hoverIntent.js"></script>
        <script type="text/javascript" src="js/superfish.js"></script>
        <script type="text/javascript" src="js/custom.js"></script>
        <link rel="stylesheet" href="colorbox.css" />

        <script src="js/jquery.colorbox.js"></script>
        <!-- End of Libraries -->	



  </head>
  <body>
      <div id="wrapper">
      <div id="container">
      <div id="header">
      <div id="top">
          <div class="logo"><p>Groundwave Online Simulator  (0.9 beta) </p></div>
          <div class="meta"><ul><li><a href="http://www.quake0day.com:9090/" target="_blank" class="tooltip"><span class="ui-icon ui-icon-info"></span>About</a></li></ul></div>
      </div>
      <div id="navbar">      </div>
      </div>




    <div id="content">



       <table class="mainTable" cellpadding="0" cellspacing="0" align="center">

    <tr valign="top">

      <td style="width:50%; height:80%; padding: 5px;"> 
        <div id="map_canvas" class="canvasStyle"></div> 
         <fieldset>
             <legend>Terrain Info</legend>
        <div id="chart_div" style="display=none;"  onmouseout="clearMouseMarker()"></div>
        </fieldset>
      </td>
      <td style="padding: 5px;">
           <input type="submit" name="add" id="add"  class="button" value="Add Sender and Receiver" />
        <input type="submit" name="setp" id="setp"  class="button" value="Set Simulation Parameters" />
        <input type="submit" name="reset" id="reset" class="button" value="Reset" />
<input type="submit" name="button" id="button" class="button" value="Run Simulation" />
<hr />
   <div id="data"></div>


   <hr />
<div class="message information close" id="tips">
    <h2>Notifications</h2>
    <p>I'm back~!!! Fixed 5.8MHz bugs :) <br/> Click "Add Sender and Receiver" Button and Drag the icon </p>
</div>
 <div id="infoPanel">
                     <fieldset>
             <legend>Sender and Receiver Info</legend>
               <div id="sender">
    <b>Sender</b>
    <!--<div id="markerStatus1"><i>Click and drag the marker.</i></div> -->
    <b>Current position:</b>
    <div id="info1"></div>
    <b>Closest matching address:</b>
    <div id="address1"></div>
    </div>
    <div id="receiver">
    <b>Receiver</b>
    <!--<div id="markerStatus2"><i>Click and drag the marker.</i></div>-->
    <b>Current position:</b>
    <div id="info2"></div>
    <b>Closest matching address:</b>
    <div id="address2"></div>
    </div>


  </fieldset>
    </div>
<!--
         <fieldset>
             <legend>Simulation Parameters</legend>
             <table class="normal" id="para" cellpadding="0" cellspacing="0" border="0">
                 <thead>
                 </thead>
                 <tbody>
                     <tr><td><b>Frequency(MHz):</b></td> <td>1</td></tr> 
                     <tr class="odd"><td><b>Polarization:</b></td> <td>Vertical</td></tr>
                     <tr><td><b>Sender Antenna Height(m): </b></td> <td>5</td></tr>
                     <tr class="odd"><td><b>Receiver Antenna Height(m):</b></td> <td>5</td></tr>
                     <tr><td><b>Bandwidth(bps):</b></td> <td>9600</td></tr>
                     <span class="validate_error" id="fre_error"></span>
                 </tbody>
             </table>

           <div id="set">
<label for="fre">Frequency(MHz):</label><input class="sf"  name="fre" type="text"  id="fq" value="1" />
<label for="pol">Polarization:</label><select name="pol" class="dropdown" id="pol"><option>Vertical</option><option>Horizontal</option></select><br />
<label for="hei">Sender Antenna Height(m):</label><input class="sf"  name="hei" type="text"  id="height" value="5" />
<label for="heir">Receiver Antenna Height(m):</label><input class="sf"  name="heir" type="text" name="heightr" id="heightr" value="5" /><br />
<label for="bandwidth">Bandwidth(bps):</label><input class="sf"  name="bandwidth" type="text" id="bandwidth" value="9600" />
<a href="#" id="sav" class="button"><span class="ui-icon ui-icon-check"></span>Set</a><br />

</div>

        </fieldset>

-->



        <div id="table_canvas" style="width:100%; height:100%;">
        </div>

      </td>
    </tr>

  </table> 

  </div>
  </div> <!--end of content -->


  </div>
  <div class="push"></div>
  </div>
  <div id="footer"><p class="mid">&copy; quake0day 2012. All rights reserved.</p></div>
  </body>

</html>
