<?php
require_once('config.php');

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
        $TicketTypesUrl = "events/" . EVENT_ID . "/ticket_classes" . $continuation;
        $EbTickets = CallEbApi("GET", $TicketTypesUrl);

        //Add this page's ticket data to our master array
        $TicketData = array_merge($TicketData, (array) $EbTickets->ticket_classes);

        //Are there more pages of ticket classes?
        $is_there_more = $EbTickets->pagination->has_more_items;
        if($is_there_more)
            $continuation = "?continuation=" . $EbTickets->pagination->continuation;

    } while ($is_there_more);

    $ReturnArray = array();

    //Iterating to generate a list of ticket names
    foreach ($TicketData as $n => $v) {
        $TicketPrice = ($v->free == false) ? $v->actual_cost->display : "FREE";
        $TicketName = $v->name;
        $ReturnArray[] = array($TicketName . " (" . $TicketPrice . ")", $v->id);
    }

    //Alphabetize the array
    sort($ReturnArray);

    return $ReturnArray;
}

// Generate unique discount code
function GenerateDiscountCode($TicketId, $PercentOff)
{
    //Check $PercentOff to make sure it's playing nicely
    if(!is_numeric($PercentOff) || $PercentOff > 100 || $PercentOff < 0)
        return FALSE;
    
    $PercentOff = round($PercentOff, 2);

    $i = 0;
    do
    {
        //Add the user-defined prefix to a random code
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

        $OrganizationId = GetOrganizationId(EVENT_ID);
        $DiscountUrl = "organizations/" . $OrganizationId . "/discounts/";

        //Generate the code - will return false if code is a duplicate/error/etc.
        $DiscountCreation = CallEbApi("POST", $DiscountUrl, $DiscountBody);

        //Emergency counter to prevent infinite loops
        $i++;
    } while ($DiscountCreation == FALSE && $i < 10);

    if($DiscountCreation == FALSE)
        return FALSE;
    return $DiscountCode;
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
    $OrgUrl = "events/" . EVENT_ID;
    $EventInfo = CallEbApi("GET", $OrgUrl);

    return $EventInfo->organization_id;
}

// Method: POST, PUT, GET etc
// Data: array("param" => "value") ==> index.php?param=value
function CallEbApi($method, $url, $data = false)
{
    $url = (substr($url, 0, 1) == "/") ? substr($url,1) : $url;
    $FullUrl = EBAPI . $url;

    $curl = curl_init();
	
	$content_type = "application/json";
	
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
                $FullUrl = sprintf("%s?%s", $FullUrl, http_build_query($data));
    }

	//Set headers
    $headers = array( 
        "Content-Type: " . $content_type , 
        "Authorization: Bearer " . EB_API_KEY
    );
    
	curl_setopt_array($curl, array(
		CURLOPT_URL				=> $FullUrl,
		CURLOPT_HTTPHEADER		=> $headers,
		CURLOPT_CRLF			=> 1,
        CURLOPT_RETURNTRANSFER	=> 1,
        CURLOPT_FOLLOWLOCATION  => 1
    ));

    $result = curl_exec($curl);

    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    //If 200/201, return value
    if($httpcode == 200 || $httpcode == 201)
        return json_decode($result);

    //If anything else return false
    return false;
}
?>