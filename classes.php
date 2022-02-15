<?php 


function helper_json_pretty_print($json_data)
{

    $output = "";
    //Initialize variable for adding space
    $space = 0;
    $flag = false;

    //Using <pre> tag to format alignment and font
    $output = $output."<pre>";

    //loop for iterating the full json data
    for($counter=0; $counter<strlen($json_data); $counter++)
    {

        //Checking ending second and third brackets
        if ( $json_data[$counter] == '}' || $json_data[$counter] == ']' )
        {
            $space--;
            $output = $output."\n";
            $output = $output.str_repeat(' ', ($space*2));
        }
 

        //Checking for double quote(“) and comma (,)
        if ( $json_data[$counter] == '"' && ($json_data[$counter-1] == ',' ||
        $json_data[$counter-2] == ',') )
        {
            $output = $output."\n";
            $output = $output.str_repeat(' ', ($space*2));
        }
        if ( $json_data[$counter] == '"' && !$flag )
        {
            if ( $json_data[$counter-1] == ':' || $json_data[$counter-2] == ':' )
                //Add formatting for question and answer
                $output = $output.'<span style="color:#8db3f0;font-weight:bold">';
            else
                //Add formatting for answer options
                $output = $output.'<span style="color:#6b6b6b;">';
        }
        $output = $output.$json_data[$counter];
        //Checking conditions for adding closing span tag
        if ( $json_data[$counter] == '"' && $flag )
            $output = $output.'</span>';
        if ( $json_data[$counter] == '"' )
            $flag = !$flag;

        //Checking starting second and third brackets
        if ( $json_data[$counter] == '{' || $json_data[$counter] == '[' )
        {
            $space++;
            $output = $output."\n";
            $output = $output.str_repeat(' ', ($space*2));
        }
    }
    $output = $output."</pre>";

    return $output;
}

function helper_GetGuid()
{
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}



class clsView
{
	// Init Parameter
    var $data_verz = "";
	var $dbcon = null; // Database Connection

    // Views
    var $apicalls = array();


    var $errormessage = "";

    function __construct($data_verz,$dbcon)
    {
		// Init Parameter
        $this->data_verz = $data_verz;
		$this->dbcon = $dbcon;
	
    }

    function getApicalls()
    {
        try {

            foreach (scandir($this->data_verz,1) as $file) {
				if ($file === ".." or $file === "." or $file === "@eaDir") continue;
					
                // Split filename
                $file_notxt = str_replace('.txt','',$file);
                $file_array = explode("#",$file_notxt);

                // Get file content 
				$filelines_array = fopen($this->data_verz.$file,"r");
                $contents = fread($filelines_array, filesize($this->data_verz.$file));

                // Parse json content
                $obj = json_decode($contents);

                $apicallitem = array('id' => $file_array['1'],'unixtimestamp_added' => $file_array['0'],'unixtimestamp_used' => $obj->{'unixtimestamp_used'},'method' => $obj->{'method'}, 'request_base64' => $obj->{'request_base64'}, 
                                    'headers_base64' => $obj->{'headers_base64'}, 'url_base64' => $obj->{'url_base64'},'isanswered' => $file_array['2'],'response_base64' => $obj->{'response_base64'});
                array_push($this->apicalls,$apicallitem);	

                fclose($filelines_array);
            }
            
        } catch (PDOException $e) {

            $this->errormessage =  "Database Insert failed: ".$e->getMessage();
        }
    }

}

class clsApicall
{
	// Init Parameter
    var $data_verz = "";

    // Parameter
    var $filename = "";
    var $guid = "";
	var $unixtimestamp_added = "";
    var $unixtimestamp_used = "";
    var $method = "";
    var $request_base64 = "";
    var $headers_base64 = "";
    var $url_base64 = "";
    var $isanswered = "0";
    var $response_base64 = "";
    var $errormessage = "";

    function __construct($data_verz)
    {
		// Init Parameter
        $this->data_verz = $data_verz;
		
		// Default Response
        $this->filename = "";

        $this->unixtimestamp_added = time();
        $this->guid = "";
        $this->isanswered = "0";

        $this->unixtimestamp_used = "";
        $this->method = "";
        $this->request_base64 = "";
        $this->headers_base64 = "";
        $this->url_base64 = "";
        $this->response_base64 = "";

		$this->errormessage = "";

 
    }

    function get($apicall_guid)
    {
        try {

            foreach (scandir($this->data_verz) as $file) 
            {
                if ($file === ".." or $file === "." or $file === "@eaDir") continue;
                    
                $file_notxt = str_replace('.txt','',$file);
                $file_array = explode("#",$file_notxt);
    
                if ($file_array['1'] == $apicall_guid)
                {
                    // Read File
                    $filelines_array = fopen($this->data_verz.$file,"r");
                    $contents = fread($filelines_array, filesize($this->data_verz.$file));
                    // Decode JSON
                    $obj = json_decode($contents);
                    // Fill 
                    $this->filename = $file;

                    $this->unixtimestamp_added = $file_array['0'];
                    $this->guid = $file_array['1'];
                    $this->isanswered = $file_array['2'];

                    $this->unixtimestamp_used = $obj->{'unixtimestamp_used'};
                    $this->method = $obj->{'method'};
                    $this->request_base64 = $obj->{'request_base64'};
                    $this->headers_base64 = $obj->{'headers_base64'};
                    $this->url_base64 = $obj->{'url_base64'};
                    $this->response_base64 = $obj->{'response_base64'};

                }

            }
            
        } catch (PDOException $e) {

            $this->errormessage =  "get failed: ".$e->getMessage();
        }
    }

    function getNext()
    {
        
        try {

            $next_guid = "";

            foreach (scandir($this->data_verz,1) as $file) 
            {
                if ($file === ".." or $file === "." or $file === "@eaDir") continue;

                // Split filename
                $file_notxt = str_replace('.txt','',$file);
                $file_array = explode("#",$file_notxt);

                // Wenn isanswered = 0
                if ($file_array['2']=="0")
                {
                    $next_guid = $file_array['1'];
                }
                
            }
                    
            $this->get($next_guid);
            
        } catch (PDOException $e) {

            $this->errormessage =  "getNext failed: ".$e->getMessage();
        }

        return $this->response;
    }

    function add($response)
    {
        try {

            $unixtimestamp_added = microtime(true);
            $guid = helper_GetGuid();

            $this->filename = $unixtimestamp_added."#".$guid."#0.txt";

            $myfile = fopen($this->data_verz.$this->filename, 'a') or die("Unable to open file!");

            $data = '{
                        "unixtimestamp_used":"",
                        "method":"",
                        "request_base64":"",
                        "headers_base64":"",
                        "url_base64":"",
                        "response_base64":"'.base64_encode($response).'"
                    }';


            fwrite($myfile, $data);

            fclose($myfile);

            
        } catch (PDOException $e) {

            $this->errormessage =  "add failed: ".$e->getMessage();
        }
    }



    function update()
    {
        try {

            $myfile = fopen($this->data_verz.$this->filename, 'w') or die("Unable to open file! (update)");

            $data = '{
                        "unixtimestamp_used":"'.$this->unixtimestamp_used.'",
                        "method":"'.$this->method.'",
                        "request_base64":"'.$this->request_base64.'",
                        "headers_base64":"'.$this->headers_base64.'",
                        "url_base64":"'.$this->url_base64.'",
                        "response_base64":"'.$this->response_base64.'"
                    }';


            fwrite($myfile, $data);

            fclose($myfile);

            // Rename file (set isanswered to 1)
            $file_notxt = str_replace('.txt','',$this->filename);
            $file_array = explode("#",$file_notxt);

            $newfilename = $file_array['0'].'#'.$file_array['1'].'#1.txt';

            rename($this->data_verz.$this->filename, $this->data_verz.$newfilename);
                    
        } catch (PDOException $e) {

            $this->errormessage =  "update failed: ".$e->getMessage();
        }
    }

    function clearAll()
    {
        try {


            foreach (scandir($this->data_verz,1) as $file) 
            {
                if ($file === ".." or $file === "." or $file === "@eaDir") continue;

                // Lösche file
                unlink($this->data_verz.$file);
                
            }
            
        } catch (PDOException $e) {

            $this->errormessage =  "clearAll failed: ".$e->getMessage();
        }
    }

}


function helper_getRequestHeaders() {
    $headers = array();
    foreach($_SERVER as $key => $value) {
        if (substr($key, 0, 5) <> 'HTTP_') {
            continue;
        }
        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        $headers[$header] = $value;
    }
    return $headers;
}

?>