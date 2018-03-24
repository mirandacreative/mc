<?php

// Declare the campaign class
class Campaign
{
    public $id;
    public $title;
    public $sent;
    public $views;
    public $clicks;
    public $optouts;

    public function getProperties()
    {
        return array("title","sent","views","clicks","optouts");
    }

    public function toArray()
    {
        $result = array();

        foreach ($this->getProperties() as $property) {
            $result[$property] = $this->$property;
        }

        return $result;
    }
}

// Save hypermail api key
$HYPERMAIL_API_KEY = "18a53dfe062c7572b5e913f0bb5014a0b7348cf1y";

// Create base url for hypermail
$hypermail_base_url = "http://hypermaillogin.com/api.php?apikey=" . $HYPERMAIL_API_KEY;

// Create campaigns array
$campaigns = array();

// Get campaigns XML reposponse from Hypermail's API
$campaignsURL = $hypermail_base_url . "&area=email&action=getcampaigns&start=0&limit=1000";
$curlResults = curlGet($campaignsURL);

// If there are no errors
if (!$curlResults['error']) {
    // Parse XML response
    $campaignsXML = new SimpleXMLElement($curlResults['response']);

    // Loop through campaign objects
    foreach ($campaignsXML->campaign as $campaignXML) {
        // Create campaign object
        $campaign = new Campaign();
        $campaign->id = (int)$campaignXML->id;
        $campaign->title = (string)$campaignXML->title;

        // Add to campaigns array
        $campaigns[] = $campaign;

        // get campaign details
        $curlResults = curlGet($hypermail_base_url . "&area=email&action=getcampaigndetail&id=" . $campaign->id);

        // if there are no errors
        if (!$curlResults['error']) {
            // Parse XML response
            $campaignDetailsXML = new SimpleXMLElement($curlResults['response']);

            // store campaigns details into object
            $campaign->sent = (int)$campaignXML->sent;
            $campaign->views = (int)$campaignXML->views;
            $campaign->clicks = (int)$campaignXML->clicks;
            $campaign->optouts = (int)$campaignXML->optouts;
        }
        
    }
}

// Create CSV file
if (count($campaigns) > 0) {

    // prepare the file
    $csv = fopen('php://output', 'w');

    // Save header
    fputcsv($csv, $campaigns[0]->getProperties());

    // Save data
    foreach ($campaigns as $campaign) {
        fputcsv($csv, $campaign->toArray());
    }

    fclose($csv);
}







// Function to make a get request easy
// returns an array('error' => '', 'response' => '')
function curlGet($url){

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    return array('error' => $err, 'response' => $response);
}