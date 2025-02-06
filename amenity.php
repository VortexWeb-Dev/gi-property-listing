<?php

// Show errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// API credentials
$baseUrl = "https://gicrm.ae/rest/1945/7mnw3te56u363prw";
$entityTypeId = 1084;  // Replace with your actual Entity Type ID

// Function to make API calls
function apiCall($url, $method = 'GET', $data = []) {
    $ch = curl_init();
    
    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    
    if ($method == 'GET' && !empty($data)) {
        $url .= '&' . http_build_query($data);
    }
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Fetch all items with pagination
function fetchAllItems($baseUrl, $entityTypeId) {
    $allItems = [];
    $start = 0;
    
    do {
        $response = apiCall("$baseUrl/crm.item.list?select[]=id&select[]=ufCrm37Amenities&entityTypeId=$entityTypeId&start=$start", 'GET');
        
        if (isset($response['result']['items'])) {
            $allItems = array_merge($allItems, $response['result']['items']);
        }

        $start = $response['next'] ?? null;
    } while ($start !== null);
    
    return $allItems;
}

// Clean and update the amenities field
function updateItems($items, $baseUrl, $entityTypeId) {
    foreach ($items as $item) {
        $id = $item['id'];
        
        if ($item['ufCrm37Amenities'] && !empty($item['ufCrm37Amenities'])) {
            // Split the string into an array if it's a string
            $amenities = explode(',', $item['ufCrm37Amenities'][0]);
            
            // Prepare data for the update
            $updateData = [
                'fields' => [
                    'ufCrm37Amenities' => $amenities
                ]
            ];
            
            // API call to update the item
            $updateResponse = apiCall("$baseUrl/crm.item.update?entityTypeId=$entityTypeId&id=$id", 'POST', $updateData);
            
            if (isset($updateResponse['result'])) {
                echo "Updated item ID: $id successfully.\n";
            } else {
                echo "Failed to update item ID: $id.\n";
            }
        }
    }
}

// Run the script
$items = fetchAllItems($baseUrl, $entityTypeId);
echo '<pre>';
print_r($items);
echo '</pre>';
updateItems($items, $baseUrl, $entityTypeId);

?>
