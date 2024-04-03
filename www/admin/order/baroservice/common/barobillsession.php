<?php
class auctionSession
{
	private $serverUrl;
	private $soapAuction;
	
	public function __construct($serverUrl, $soapAuction)
	{
		$this->serverUrl = $serverUrl;
		$this->soapAuction = $soapAuction;	
	}
	
	/**	sendHttpRequest
		Sends a HTTP request to the server for this session
		Input:	$requestBody
		Output:	The HTTP Response as a String
	*/
	public function sendHttpRequest($requestBody)
	{		
		//build auction headers using variables passed via constructor
		$headers = $this->buildAuctionHeaders(strlen($requestBody));
				
		//initialise a CURL session
		$connection = curl_init();
				
		//set the server we are using (could be Sandbox or Production server)
		curl_setopt($connection, CURLOPT_URL, $this->serverUrl);
		
		//stop CURL from verifying the peer's certificate
		curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
		
		//set the headers using the array of headers
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
		
		//set method as POST
		curl_setopt($connection, CURLOPT_POST, 1);
		
		//set the XML body of the request
		curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);
		
		//set it to return the transfer as a string from curl_exec
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		
		//Send the Request
		$response = curl_exec($connection);
		
		//close the connection
		curl_close($connection);
		
		//return the response
		return $response;
	}
	
	private function buildAuctionHeaders($requestBodyLength)
	{
		$headers = array (
			"Content-Type: text/xml; charset=utf-8",
			"Content-Length: $requestBodyLength",
			"SOAPAction: $this->soapAuction"
		);
		
		return $headers;
	}
}
?>	