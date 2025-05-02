<?php

//include('custom_functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$logatas = json_decode($_POST['visitslog']);
	foreach ($logatas as $logata) {

		// echo '<pre>';
		// print_r($logata);
		// echo '----------------------';
		// print_r($logata->visitorId);
		// echo '</pre>';
		$visitorid= $logata->visitorId;
        $page= $logata->url;                //. ' , ' . $logata->baseUrl;
		$locationip= $logata->ip;
		$country= $logata->country;
		$city= $logata->city;

		if(trim($logata->city)!=''){
		$city= $logata->city;
		}else{$city= $logata->region;}

		if(trim($logata->city)=='' and $logata->region==''){
		$city= -1;
		}
		
		$countvisits= $logata->pastcount;

		if(trim($logata->cid)!=''){
		$campaignid= $logata->cid;
		}else{$campaignid= -1;}

		if(trim($logata->sid)!=''){
		$suspectid= $logata->sid;
		}else{$suspectid= -1;}

		if(trim($logata->lid)!=''){
		$leadid= $logata->lid;
		}else{$leadid= -1;}

		if(trim($logata->pid)!=''){
		$prospectid= $logata->pid;
		}else{$prospectid= -1;}

		if(trim($logata->cuid)!=''){
		$customerid= $logata->cuid;
		}else{$customerid= -1;}
                
                if(trim($logata->email)!=''){
		$email= $logata->email;
		}else{$email= -1;}

                if(trim($suspectid)!=-1){
		
		}else{$suspectid= $email;}


		$dtime= $logata->timestamp;

		// print_r($logata->visitorId);
		
	   // echo $visitorid . $page . $locationip . $country . $city . $countvisits . $campaignid . $suspectid . $leadid . $prospectid . $customerid . $dtime;

		
		header('Content-type: application/json');
		$authToken = getAuthToken();

		//echo $authToken ;

		$options = [
		  'connection_timeout' => 10,
		  'trace' => true,
		  'exceptions' => true,
		  'cache_wsdl' => WSDL_CACHE_NONE, // Disable WSDL caching
		  'stream_context' => stream_context_create([
				'http' => [
					'header' => 'AuthToken:'.$authToken // Add the custom header
				]
			]),
		  ];

		 
		$soapClient = new SoapClient('https://werpservice.viennaadvantage.com/ErpService.asmx?wsdl', $options);
		$result = $soapClient->InsertWebSiteVisitor(array(
			'Visitor_ID'     => $visitorid,
			'Page'     => $page, 
			'Location_IP'     => $locationip,
			'Country'     => $country,
			'City'     => $city,
			'Count_Visits'     => $countvisits,
			'Campaign_ID'     => $campaignid, 
			'Suspect_ID'     => $suspectid, 
			'Lead_ID'     => $leadid,
			'Prospect_ID'     => $prospectid,
			'Customer_ID'     => $customerid, 
			'visitdate'     =>  $dtime,
			'accessKey'     => '0e810dc563d243c014199b5d48085556'));


			//echo "BYYEEEEEE";
   
        //$result= setvisitor($visitorid, $page, $locationip, $country, $city, $countvisits, $campaignid, $suspectid, $leadid, $prospectid, $customerid, $dtime);

		// echo '<pre>';
		// echo '----------------------';
		// print_r($result);
		// echo '</pre>';
		// echo "Jingalala";

		$obj = new stdClass();
		$obj->status=true;
		$obj->data= $result;
		echo json_encode($obj);

      if($leadid!=''){

               $url = "https://hook.us2.make.com/fa24nosfnqk7e76kdg6klssijts1w9ii";

// Parameters
$data = [
    'LeadID' => $leadid,
    'VisitorID' => $visitorid,
    'Country' => $country,
    'City' => $city

];

// Initialize cURL session
$ch = curl_init($url);

// Set cURL options for POST request
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute the request
$response = curl_exec($ch);

// Close cURL session
curl_close($ch);
}
           


	}	
}

function getAuthToken() {
	try {
		$url = "https://tamgmtapi.viennaadvantage.com/api/auth/token";
		
		// Initialize cURL
		$ch = curl_init($url);
		
		// Set the options
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Accept: application/json',
			'Content-Type: application/json'
		]);
		
		// If you need to ignore SSL certificate validation (not recommended for production)
		// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		
		// Execute the request
		$response = curl_exec($ch);
		
		// Check for errors
		if (curl_errno($ch)) {
			throw new Exception(curl_error($ch));
		}

		// Close the cURL session
		curl_close($ch);

		// Decode the JSON response
		$data = json_decode($response);
		
		// Return the token
		return $data->token ?? null;

	} catch (Exception $e) {
		// Log or handle the exception as needed
	}

}

?>
