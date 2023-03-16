<?php 
header("Content-type: text/html");

error_reporting(-1);

require_once('setup.php');
require_once('classes.php');


if (isset($_GET['add']))
{

	$myApicall = NEW clsApicall($verzeichnis_data);
	$myApicall->add($_POST['response_textarea']);

}
if (isset($_GET['clear']))
{

	$myApicall = NEW clsApicall($verzeichnis_data);
	$myApicall->clearAll();

}


// Response Repository List
$myView = NEW clsView($verzeichnis_data);
$myView->getApicalls();

foreach($myView->apicalls as $apicall)
{

	$apicall_style = "background-color:#d4e4ff";
	if ($apicall['isanswered']==1) $apicall_style = "background-color:#ecffeb";

    echo '<div id="element_apicall_'.$apicall['id'].'_header" class="div_list_apicall_header">
			<span class="small_gray">';
				if ($apicall['isanswered']==1) echo $apicall['id'].' - '.date("d.m.Y H:i:s",$apicall['unixtimestamp_used']).' answered Request - '.$apicall['method'].' '.base64_decode($apicall['url_base64']).'
													<details>
														<summary>Header Info</summary>
														<p>'.base64_decode($apicall['headers_base64']).'</p>
													</details>';
				else  echo $apicall['id'].' - waiting for Incoming Request';
		echo '</span><br>
		  </div>
	
		  <div id="element_apicall_'.$apicall['id'].'_wrapper" class="div_list_apicall_wrapper">

			<div id="element_apicall_'.$apicall['id'].'" class="div_list_apicall" style="'.$apicall_style.'">
			<table>
			<tr>
				<td class="apicall_request">
					'.helper_json_pretty_print(base64_decode($apicall['request_base64'])).'
				</td>
				<td class="apicall_separator"></td>
				<td class="apicall_separator_free"></td>
				<td class="apicall_response">';
				if ($apicall['isanswered']==1)
					echo helper_json_pretty_print(base64_decode($apicall['response_base64']));
				else
					echo helper_json_pretty_print(base64_decode($apicall['response_base64']));
				
	echo'		</td>
			</tr>
			</table>
			</div>

		  </div>';

}

?>

