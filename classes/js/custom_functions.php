<?php 

//$apiUrl = 'http://168.119.42.141:7073/Service.asmx?wsdl';

//$apiUrl = 'http://werpservice.viennaadvantage.com/Service.asmx?wsdl';

$apiUrl = 'https://werpservice.viennaadvantage.com/ErpService.asmx?wsdl';


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
	
		return null;
	}

  



function setvisitor($Visitorid, $Page, $Locationip, $Country, $City, $Countvisits, $Campaignid, $Suspectid, $Leadid, $Prospectid, $Customerid, $Dtime){

    //   echo "setlead(". $Name . "," . $Fname . "," . $Lname . "," . $Email . "," . $Phone . "," . $CompanyName . "," . $Interest . "," . $ResourceID . "," . $Countryid . "," . $Message . "," . $Campaignid . "," . $Regionid . "," . $Visitedsite . "," . $Salerepid . "," . $CompanySize . "," . $Subject . "," . $Productid . "," . $Blink . "," . $Title . "," . $Nextstep. ")";
    //  die;

    echo "test";
   

   header('Content-type: application/json');
           $authToken = getAuthToken();
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
               'Visitor_ID'     => $Visitorid,
               'Page'     => $Page, 
               'Location_IP'     => $Locationip,
               'Country'     => $Country,
               'City'     => $City,
               'Count_Visits'     => $Countvisits,
               'Campaign_ID'     => $Campaignid, 
               'Suspect_ID'     => $Suspectid, 
               'Lead_ID'     => $Leadid,
               'Prospect_ID'     => $Prospectid,
               'Customer_ID'     => $Customerid, 
               'visitdate'     =>  $Dtime,
               'accessKey'     => '0e810dc563d243c014199b5d48085556'));

               return $result;
}





?>