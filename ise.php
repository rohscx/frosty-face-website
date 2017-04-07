<?php
include 'restAuth.php';    // tickets tokons and secure data
if (isset($_GET['iseData']) & isset($_GET['iseAddress']))
{
	$data = $_GET['iseData'];
	$addr = $_GET['iseAddress'];
	//echo "This mac was passed" . $name . "\r\n";  // debug
	//$data = "(" . $data . ")";
	//echo $addr . $data . "\r\n"; //debug
	$curl = curl_init();
	$somevar = $_GET["uid"];
	curl_setopt_array($curl, array(
		CURLOPT_SSL_VERIFYPEER => false,    //disables ssl server cert verify check
    	CURLOPT_SSL_VERIFYHOST => false,    //disables ssk host cert verify check
    	CURLOPT_URL => $addr . $data,
    	CURLOPT_RETURNTRANSFER => true,
    	CURLOPT_ENCODING => "",
    	CURLOPT_MAXREDIRS => 10,
    	CURLOPT_TIMEOUT => 1000,
    	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    	CURLOPT_CUSTOMREQUEST => "GET",
      	CURLOPT_HTTPHEADER => $iseAuth, //restAuth contains the auth Tokens. This also need to be update to return JSON instead of include
	));
  	$response = curl_exec($curl);
  	$err = curl_error($curl);
  	curl_close($curl);
	//echo $response;	// debug
    	$xml = new SimpleXMLElement($response);
    	//echo $xml->asXML();
   	$dom = new DOMDocument('1.0');
    	$dom->preserveWhiteSpace = false;
    	$dom->formatOutput = true;
    	$dom->loadXML($xml->asXML());
    	//echo $dom->saveXML();
    	$json = json_encode($xml);
    	$array = json_decode($json,TRUE);
    	//echo print_r($array);
	#echo $array['user_name'] . "<br>";
  	if ($err) {
    		echo "cURL Error #:" . $err;
  	} elseif ($array['http-code'] == 401 || $array['http-code'] == 500) {
		echo print_r($array);
  	} else {
    		// echo print_r($array);	// debug
	    	#echo $array['user_name'] . "<br>";	// debug
        	$match = array("EndPoint Auth Status :" => 'passed',"EndPoint User :" => 'user_name',
			"EndPoint Authentication Status :"=>'passed',"EndPoint Auth Server :"=>'acs_server',
                        "EndPoint Auth Method :"=>'authentication_method',"EndPoint Auth Protocol :"=>'authentication_protocol',
                        "EndPoint Idendity Group :"=>'identity_group',"EndPoint IP :"=>'framed_ip_address',
                        "EndPoint Location :"=>'location',"EndPoint Type :"=>'device_type',
			"EndPoint Auth(Z) :"=>'selected_azn_profiles',"EndPoint SGT :"=>'cts_security_group',     
			"NAS IP :" => 'nas_ip_address',"NAS Name :"=>'network_device_name');	    	
	    for ($i1 = 0; $i1 < 1; $i1++) {
		foreach ($match as $x => $item) {
				 echo "<b>" . $x . "</b>" . "  " . $array[$item] . "<br>";
		}    
	    }
    
	}

}
?>
