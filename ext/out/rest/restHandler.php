<?php
class RestHandler
{
	private $httpVersion = "HTTP/1.1";
	private $httpStatus = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => '(Unused)',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported');

	/*set headers for rest response*/
	function httpHeaders($contentType, $statusCode)
	{
		header($this->httpVersion. " ". $statusCode ." ". $this->httpStatus[$statusCode]);
		header("Content-Type:". $contentType);
	}

	/*encode response according to content accept*/
	function encodeResponse($requestContentType, $responseData)
	{
		switch($requestContentType)
		{
			case 'text/html':
				$response = "<table>";

				$titles = array_keys($responseData[0]);
				$response .= "<tr>";
				foreach($titles as $ttl)
				{
					$response .= '<th>' . $ttl . '</th>';
				}
				$response .= "</tr>";

				foreach($responseData as $key => $value)
				{
					$response .= "<tr>";

					foreach($value as $key2 => $val)
					{
						$response .= "<td>". $val . "</td>";
					}

					$response .= "</tr>";
				}
				$response .= "</table>";
				return $response;
			break;
			case 'application/json':
				return json_encode($responseData);

			break;
			case 'application/xml':
				$xml = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
				foreach($responseData as $key => $value)
				{
					$xml->addChild($key, $value);
				}
				return $xml->asXML();
			break;
			default:
				$response = "";
				foreach($responseData as $key => $value)
				{
					foreach($value as $key2 => $val)
					{
						$response .= $key2 . ";" . $val . "\n";
					}
				}
				return $response;
			break;
		}
	}
}
?>