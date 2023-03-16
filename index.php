<?php 
header("Content-type: text/html");

error_reporting(-1);

require_once('setup.php');
require_once('classes.php');

?>

<!DOCTYPE HTML>
<html>
    <head>
	
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="Content-Language" content="de">
		
        <title>MockMe</title>
		
		<!-- <link rel="icon" href="img/logo/icon_80_x_80.png"> -->
		
        <link rel="stylesheet" href="style.css?v1" type="text/css" media="screen" />

		

		<!-- JQUERY -->
		<script src="js/jquery/jquery-3.0.0.min.js" type="text/javascript"></script>
		<!-- JQUERY UI -->
		<script src="js/jquery_ui_1.12.1/jquery-ui.js" type="text/javascript"></script>
		<link href="js/jquery_ui_1.12.1/jquery-ui.css" type="text/css" rel="stylesheet">



		<script type="text/javascript">
			
			function reload_repository_continuously() {
				setInterval("reload_repository_if_checked();", 5000); 
			}

			function reload_repository_if_checked() {
				if (document.getElementById('auto_refresh_repository').checked) refresh_repository();
			}

			function generateParamsFromClass(classident) {
				// Prepare Parameter
				var params = "areqinfo=actionfromwebsite" + document.getElementsByClassName("response_textarea").value;
				// Collect Parameter from all elements with specific class (text elements)
				var cols = document.getElementsByClassName(classident);
				for (var i=0; i<cols.length; i++) {
					params = params + "&" + cols[i].getAttribute("id") + "=" + cols[i].value;
				}
				// Collect Parameter from all elements with specific class (autocomplete)
				var cols = document.getElementsByClassName(classident + '_autocomplete');
				for (var i=0; i<cols.length; i++) {
					params = params + "&" + cols[i].getAttribute("id") + "=" + cols[i].getAttribute("returnvalue");
				}
				// Collect Parameter from all elements with specific class (checkboxes)
				var cols = document.getElementsByClassName(classident + '_checkbox');
				for (var i=0; i<cols.length; i++) {
					//if ($("#" + cols[i].getAttribute("id")).is(':checked') == true) {
					if (cols[i].checked) {	
						params = params + "&" + cols[i].getAttribute("id") + "=1";
					} else {
						params = params + "&" + cols[i].getAttribute("id") + "=0";
					}
				}
				// return 
				return params;
			}

			// Show Loading
			function showLoading(elementid) {
				document.getElementById(elementid).innerHTML = "<img widht=\"100px\" height=\"100px\" src=\"img/wait/loading.gif\">";
			}

			// Background Request Handling
			function sendRequest_echoOutputInElement_reloadElements(url,objecttoecho) {
				// Nutze Ajax um mit einer PHP Seite das edit zu speichern
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					// Reload Object echo outpup
					$("#"+objecttoecho).fadeOut(500, function() {
						document.getElementById(objecttoecho).innerHTML  = xmlhttp.responseText;	
						

						$("#"+objecttoecho).fadeIn(500, function() {
	
							});
						});
					}
				}
				xmlhttp.open("GET", url, true);
				xmlhttp.send();
			}

			function sendRequest_withParams_echoOutputInElement_reloadElements(url, classident, objecttoecho) {
				// Prepare Parameter
				var params = generateParamsFromClass(classident);

				// Nutze Ajax um mit einer PHP Seite das rename zu speichern
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						// Echo Repsone in Section
						if (document.getElementById(objecttoecho)) {
							document.getElementById(objecttoecho).innerHTML  = xmlhttp.responseText;	
						}
							
					}
				}
				xmlhttp.open("POST", url, true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(params);
			}

			function refresh_repository() { showLoading("element_repository_wrapper"); sendRequest_echoOutputInElement_reloadElements("reload_repository.php?show","element_repository_wrapper"); }
			function add_apicall() { showLoading("element_repository_wrapper"); sendRequest_withParams_echoOutputInElement_reloadElements("reload_repository.php?add","response_textarea","element_repository_wrapper"); }	
			function clear_repository() { showLoading("element_repository_wrapper"); sendRequest_withParams_echoOutputInElement_reloadElements("reload_repository.php?clear","response_textarea","element_repository_wrapper"); }			
		</script>

    </head>

    <body onload="reload_repository_continuously();">

                     
<div class="content">

<img src="img/logo/logosmall.png"><span class="small_gray">from Jan with love...</span>

<?php 


$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// Response Repository Add Mask
echo '<div id="element_apicall_header" class="div_list_apicall_header">
		<span class="small_gray">New Response</span><br>
		</div>

	  <div id="element_response_add" class="div_list_response">
		<table>
		<tr>
			<td class="apicall_request" style="width:100px;">
				<button id="add_response_button" class="form_button" tabindex="-1" onclick="add_apicall()">Add</button><br>
				<button id="clear_repository_button" class="form_button" tabindex="-1" onclick="clear_repository()">Clear</button>
				<button id="refresh_repository_button" class="form_button" tabindex="-1" onclick="refresh_repository()">Refresh</button>
			</td>
			<td class="apicall_separator"></td>
			<td class="apicall_separator_free"></td>
			<td class="apicall_response">
				<textarea id="response_textarea" class="response_textarea">{"message":"Dies ist eine einfache Antwort"}</textarea>
			</td>
		</tr>
		</table>
		<br>
		<span class="small_gray"><input type="checkbox" id="auto_refresh_repository" name="auto_refresh_repository" class="form_checkbox" value="Auto refresh"> repository auto refresh every 5 seconds</span>
		<br>
		<span class="small_gray">--> Now fill the repository and send a request to <a href="'.$actual_link.'api.php">'.$actual_link.'api.php</a></span>
	  </div>';

// Response Repository List
echo '<div id="element_repository_wrapper" class="div_list_repository_wrapper">

		<script type="text/javascript" language="JavaScript">
		<!--
		refresh_repository();
		//-->
		</script>

	  </div>';



?>

</div>

	</body>	
	
</html>
