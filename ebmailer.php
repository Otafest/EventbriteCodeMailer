<?php
require_once('config.php');

//No need to change anything below this line

//Include trailing slash
DEFINE("EBAPI", "https://www.eventbriteapi.com/v3/");

// Grab available ticket types
function GetTicketTypes()
{
    $TicketData = array();

    //Trust me we'll need this later.
    $Continuation = "";

    do
    {
        //Let's grab us some ticket types!
        $TicketTypesUrl = "events/" . EVENT_ID . "ticket_classes" . $continuation;
        $EbTickets = CallEbApi("GET", $TicketTypesUrl);

        //Add this page's ticket data to our master array
        $TicketData = array_merge($TicketData, $EbTickets->ticket_classes);

        //Are there more pages of ticket classes?
        $is_there_more = $EbTickets->pagination->has_more_items;
        if($is_there_more)
            $continuation = "?continuation=" . $EbTickets->pagination->continuation;

    } while ($is_there_more);

    $ReturnArray = array();

    //Iterating to generate a list of ticket names
    foreach ($TicketData as $n => $v) {
        $TicketPrice = ($v->free == false) ? $v->actual_cost->major_value : "0.00";
        $TicketName = $v->name;
        $ReturnArray[$v->id] = $TicketName . " (" . $TicketPrice . ")";
    }

    //Alphabetize the array
    sort($ReturnArray);

    return $ReturnArray;
}

// Generate unique discount code
function GenerateDiscountCode($TicketId, $PercentOff)
{
    //TODO: type check $PercentOff for 2 decimal place percentage

    do
    {
        $DiscountCode = CODE_PREFIX . GenerateRandomCode();

        $DiscountBody = array (
            'discount' => array (
                'type' => 'coded',
                'code' => $DiscountCode,
                'percent_off' => $PercentOff,
                'event_id' => EVENT_ID,
                'ticket_class_ids' => array ($TicketId),
                'quantity_available' => 1,
                'start_date' => NULL, //Available immediately
                'end_date' => NULL, //Available until when sales end
            )
        );

        $RequestBody = json_encode($DiscountBody);

        $OrganizationId = GetOrganizationId(EVENT_ID);
        $DiscountUrl = "organizations/" . $OrganizationId . "/discounts";

        //Generate the code
        $DiscountCreation = CallEbApi("POST", $DiscountUrl, $RequestBody);
    } while ($DiscountCreation == FALSE);
}

function GenerateRandomCode()
{
    //https://stackoverflow.com/questions/4570980/generating-a-random-code-in-php
    //Because I'm too lazy to reinvent the wheel

    $chars = "abcdefghijkmnopqrstuvwxyz023456789"; 
    srand((double)microtime()*1000000); 
    $i = 0; 
    $pass = '' ; 

    while ($i <= 10) { 
        $num = rand() % 33; 
        $tmp = substr($chars, $num, 1); 
        $pass = $pass . $tmp; 
        $i++; 
    } 

    return $pass;
}

function GetOrganizationId()
{
    //I could have set this as yet another input parameter
    //but I can just as easily get it with another call.
    $OrgUrl = "events/" . EVENT_ID
    $EventInfo = CallEbApi("GET", $OrgUrl);

    return $EventInfo->organization_id;
}
// Send discount code

// Method: POST, PUT, GET etc
// Data: array("param" => "value") ==> index.php?param=value
function CallEbApi($method, $url, $data = false)
{
    $url = EBAPI . (substr($url, 0, 1) == "/") ? substr($url,1) : $url;

    $curl = curl_init();
	
	$content_type = "application/json; charset=utf-8";
	
    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
            {
				$data = json_encode($data);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			}
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

	//Set headers
    $headers = array( 
        "Content-Type: " . $content_type , 
        "Authorization: Bearer " . EB_API_KEY, 
        "Content-Length: " . strlen($data)
    );
	
	curl_setopt_array($curl, array(
		CURLOPT_URL				=> $url,
		CURLOPT_HTTPHEADER		=> $headers,
		CURLOPT_CRLF			=> 1,
		CURLOPT_RETURNTRANSFER	=> 1,
		//CURLOPT_VERBOSE         => 1,           // Logs verbose output to STDERR
		//CURLOPT_STDERR          => $curl_log,   // Output STDERR log to file
	));

    $result = curl_exec($curl);

    //If 200/201, return value
    //If 400+ return false
	
    curl_close($curl);
    return json_decode($result);
}
?>