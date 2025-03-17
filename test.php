<?php

function fetchData($url, $start = 0) {
    $data = [];
    do {
        $paginatedUrl = $url . "&start=" . $start;
        $response = file_get_contents($paginatedUrl);
        if (!$response) {
            die("Failed to fetch data from API.");
        }
        $jsonData = json_decode($response, true);
        
        if (isset($jsonData['result']['items'])) {
            $data = array_merge($data, $jsonData['result']['items']);
        }
        
        $start = $jsonData['next'] ?? null;
    } while ($start !== null);
    
    return $data;
}

$api1 = "https://gicrm.ae/rest/1945/7mnw3te56u363prw/crm.item.list?entityTypeId=1084&select[]=ufCrm37ReferenceNumber&filter[ufCrm37Status]=PUBLISHED";
$api2 = "https://gicrm.ae/rest/1945/7mnw3te56u363prw/crm.item.list?entityTypeId=1130&select[]=ufCrm48ReferenceNumber&filter[ufCrm48Status]=41323";

$data1 = fetchData($api1);
echo "Data from Property Listing module:\n";
echo "<pre>";
print_r($data1);
echo "</pre>";
$data2 = fetchData($api2);
echo "Data from Inventory Management module:\n";
echo "<pre>";
print_r($data2);
echo "</pre>";

$refs1 = array_map(fn($item) => $item['ufCrm37ReferenceNumber'] ?? null, $data1);
$refs2 = array_map(fn($item) => $item['ufCrm48ReferenceNumber'] ?? null, $data2);

$refs1 = array_filter($refs1); // Remove null values
$refs2 = array_filter($refs2);

$diff = array_diff($refs2, $refs1);

echo "Reference Numbers in second API but not in first:\n";
echo "<pre>";
print_r($diff);
echo "</pre>";
echo "\n\nTotal items from Property Listing module : " . count($refs1) . "<br />";
echo "Total items from Inventory Management module : " . count($refs2) . "<br />";
echo "Total items from Inventory Management module but not in Property Listing module : " . count($diff) . "<br />";
?>
