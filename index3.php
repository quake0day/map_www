<!DOCTYPE html "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
    <title>GroundWave Online Simulator</title>
   <!-- <link href="facefiles/facebox.css" media="screen" rel="stylesheet" type="text/css" />
<script src="facefiles/facebox.js" type="text/javascript"></script>
<script type="text/javascript">
    jQuery(document).ready(function($) {
      $('a[rel*=facebox]').facebox() 
    })
</script> -->



 

    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyA5rIPRrzlI8xgWMcA1dV92_Tn_iyT04ps"
            type="text/javascript"></script>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script src="src/dragzoom_packed.js" type="text/javascript"></script>
    <script src="src/jquery.js" type="text/javascript"></script>
    <script type="text/javascript" src="js/labels.js"></script>
<script type="text/javascript" src="js/ruler.js"></script>


    <script type="text/javascript">
	var MAX = 2;
    var data = null; 
    var row_no = 0;
    var map = null;
    var mgr = null;
    var markers = [];
    var geocoder = null;
    var cLat = null;
    var cLng = null;
    google.load('visualization', '1',
          {'packages': ['table']});
    google.setOnLoadCallback(initialize);
    
   
    function initialize() {
      setupTable();
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map_canvas"));
        map.enableGoogleBar();

        var tileLayersNormal = G_MAPMAKER_NORMAL_MAP.getTileLayers();
        
        var mapMakerNormal = new GMapType(tileLayersNormal,
        G_MAPMAKER_NORMAL_MAP.getProjection(), 'MapMaker Normal',{errorMessage:"Out of bounds"});
        map.addMapType(mapMakerNormal);
        
        var tileLayersHybrid = G_MAPMAKER_HYBRID_MAP.getTileLayers();     
        
        var mapMakerHybrid = new GMapType(tileLayersHybrid,
        G_MAPMAKER_HYBRID_MAP.getProjection(), 'MapMaker Hybrid',{errorMessage:"Out of bounds"});
        

        map.addMapType(mapMakerHybrid);
        var mapControl = new GMapTypeControl();
      
        map.setCenter(new GLatLng(-41.60,173.84), 6);
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
         
        /* first set of options is for the visual overlay.*/
        var boxStyleOpts = {
          opacity: .2,
          border: "2px solid red"
        }

        /* second set of options is for everything else */
        var otherOpts = {
          buttonHTML: "<img src='images/zoom-button.gif' />",
          buttonZoomingHTML: "<img src='images/zoom-button-activated.gif' />",
          buttonStartingStyle: {width: '24px', height: '24px'}
        };

        map.addControl(new DragZoomControl({}, {backButtonEnabled: true}, {}));
        
      
        
        geocoder = new GClientGeocoder();

        GEvent.addListener(map, 'click', function(overlay, latlng) {   
			
		

		  var lat = latlng.lat();
		  var lon = latlng.lng();
		  cLat =lat;
		  cLng = lon;
		  var latOffset = 0.01;
		  var lonOffset = 0.01;
		  var point = new GLatLng(lat, lon); 
		  if (markers.length < MAX){
          marker = new GMarker(point);
      
          markers.push(marker);
          map.addOverlay(new GMarker(point));

          geocoder.getLocations(latlng, showAddress);
	  }
          
        });
      }
      adjustPageHeight();
    }  
    function adjustPageHeight() {
      var height = document.body.scrollHeight;
      document.getElementById("map_canvas").style.height = 
        height - 400;
    }
    function clearMarkers() {
      map.clearOverlays();
    }
    function reloadMarkers() {
      clearMarkers();
      for(var i= 0; i <  markers.length ; i++) {
        map.addOverlay(markers[i]);
      }
    }
    
    function reset() {
      markers = [];
      clearMarkers();
      data.removeRows(0,row_no);
      row_no = 0;
      visualization.draw(data, null);
      //map.setCenter(new GLatLng(-41.60,173.84), 6);
    }
    function showMarkers(){
		if (markers.length == MAX){
		var randomnumber=Math.floor(Math.random()*101)   
		for(var i=0; i< markers.length; i++){
			postdata(markers[i].getPoint().toString(),randomnumber);
			}
		//alert(markers[i].getPoint().toString());
		//post_to_url('hget.php', {'lat':'a'});
	}
	}
	
	function postdata(data,randomnum){                             //提交数据函数
$.ajax({                                                 //调用jquery的ajax方法
    type: "POST",                                     //设置ajax方法提交数据的形式
    url: "get.php",                                      
    data: "data="+data+"&rand="+randomnum,
    success: function(msg){  
      $("#data").html(msg);
      $("#data").fadeIn();
	 }
    }
);
}
$(document).ready(function(){  
$('<div id="loading" style="display:none;"><h2>Running Simulation......<img src = "images/ajax-loader.gif"></h2></div>').insertBefore('#data')
.ajaxStart(function(){
	$(this).show();
}).ajaxStop(function(){
	$(this).hide();
});
$("#button").click(function(){          //当按钮button被点击时的处理函数
	$("#data").fadeOut();
    showMarkers();                                     //button被点击时执行postdata函数

});
$("#reset").click(function(){          //当按钮button被点击时的处理函数
	$("#data").fadeOut();
	$("#loading").fadeOut();
    reset();   

});
});

    function showAddress(response) {
      if (!response || response.Status.code != 200) {
        alert("Status Code:" + response.Status.code);
      } else {
        place = response.Placemark[0];
        point = new GLatLng(place.Point.coordinates[1],place.Point.coordinates[0]); 
        data.addRows(1);
		data.setCell(row_no,0,row_no + 1);
		data.setCell(row_no,1,place.address);
        data.setCell(row_no,2, cLat);
        data.setCell(row_no,3, cLng);
        ++row_no;
       
        visualization = new google.visualization.Table(document.getElementById('table_canvas'));
        visualization.draw(data, null);
        //alert(cLat);
        //alert(cLng);
        google.visualization.events.addListener(visualization, 'select', selectHandler);
       
         var marker = markers[row_no -1];
         marker.openInfoWindowHtml(
          '<b>orig latlng:</b>' + response.name + '<br/>' + 
          '<b>Reverse Geocoded latlng:</b>' + place.Point.coordinates[1] + "," + place.Point.coordinates[0] + '<br>' +
          '<b>Status Code:</b>' + response.Status.code + '<br>' +
          '<b>Status Request:</b>' + response.Status.request + '<br>' +
          '<b>Address:</b>' + place.address + '<br>' +
          '<b>Accuracy:</b>' + place.AddressDetails.Accuracy + '<br>' +
          '<b>Country code:</b> ' + place.AddressDetails.Country.CountryNameCode);
        }
        
    }
    function setupTable() {
      data = new google.visualization.DataTable();
      data.addColumn('number', 'Node. No');
      data.addColumn('string', 'Area');
      data.addColumn('number', 'Latitude');
      data.addColumn('number', 'Longitude');
      data.addColumn('number', 'S|R');

      // Create and draw the visualization.
      visualization = new google.visualization.Table(document.getElementById('table_canvas'));
      visualization.draw(data, null);
    }
    function selectHandler() {
      var selection = visualization.getSelection();
      var message = '';
      var lat1;
      var lng1;
      for (var i = 0; i < selection.length; i++) {
        var item = selection[i];
        if (item.row != null && item.column != null) {
          lat1 = data.getValue(item.row,2);
          lng1 = data.getValue(item.row,3);
        } else if (item.row != null) {
          lat1 = data.getValue(item.row,2);
          lng1 = data.getValue(item.row,3);
        } else if (item.column != null) {
          lat1 = data.getValue(item.row,2);
          lng1 = data.getValue(item.row,3);
        }
        var point = new GLatLng(lat1, lng1); 
        map.addOverlay(new GMarker(point));
      }
      if (message == '') {
        //do nothing
      }   
      
    }
    </script>

  </head>
  <body>
   <table class="mainTable" cellpadding="0" cellspacing="0" align="center">
    <tr>
      <td colspan="2" id="titleRow" class="headingStyle">GroundWave Online Simulator (&alpha; 0.1)</td>
    </tr>
    <tr>
      <td colspan="2" id="menuRow" class="menubarStyle">
       <!-- <input type="button" class="buttonStyle" onclick="clearMarkers()" value="clear all markers" /> -->
       <!-- <input type="button" class="buttonStyle" onclick="reloadMarkers()" value="reload all markers" /> -->
       <input type='button' id='addruler' onclick='addruler();' value='add a ruler'/> 
        <input type="submit" name="reset" id="reset" value="reset" />
          
      <label>
          <input type="submit" name="button" id="button" value="Run Simulation" />
    </label>
     <!--   <div id="know"><a href="#mydiv" rel="facebox"><img src = "images/kaicon_005.gif"></a></div> -->
        
        
      </td>
    </tr>
    <tr valign="top">
      <td style="width:60%; height:100%; padding: 5px;"> 
        <div id="map_canvas" class="canvasStyle"></div> 
      </td>
      <td style="padding: 5px;">
		 <div id="tips">How to Use.....</div>
		    <div id="data"></div>
        <div id="table_canvas" style="width:100%; height:100%;">
        </div>
      
      </td>
    </tr>
    <tr>
      <td colspan="2" id="copyrightRow" class="copyrightRow">Works best with Firefox 3. Contact <a href="mailto:quake0day@gmail.com">Si Chen</a> with feedback.</td>  
    </tr>
  </table> 
  </body>

</html>
