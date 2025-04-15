<?php

require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/utils/index.php';

define('C_REST_WEB_HOOK_URL', 'https://gicrm.ae/rest/1945/7mnw3te56u363prw/');

$bulk_delete = isset($_GET['bulk_delete']) && $_GET['bulk_delete'] === 'true';

function getAllListingsReferences()
{
    try {
        $allReferences = [];
        $page = 1;
        $pageSize = 50;

        do {
            $response = CRest::call('crm.item.list', [
                'entityTypeId' => LISTINGS_ENTITY_TYPE_ID,
                'select' => ['ufCrm37ReferenceNumber', 'ufCrm37Status'],
                'start' => ($page - 1) * $pageSize,
                'limit' => $pageSize
            ]);

            if (isset($response['error'])) {
                echo "Error fetching listings references (page {$page}): " . $response['error_description'] . "</br>";
                break;
            }

            $items = $response['result']['items'] ?? [];

            $allReferences = array_merge($allReferences, $items);

            $morePages = count($items) >= $pageSize;
            $page++;
        } while ($morePages);

        echo "Total references fetched: " . count($allReferences) . "</br>";
        return $allReferences;
    } catch (Exception $e) {
        echo "Exception in getAllListingsReferences: " . $e->getMessage() . "</br>";
        return [];
    }
}

function getAllDntReferences()
{
    try {
        $allDntRecords = [];
        $page = 1;
        $pageSize = 50;

        do {
            $response = CRest::call('crm.item.list', [
                'entityTypeId' => DNT_ENTITY_TYPE_ID,
                'select' => ['id', 'ufCrm48ReferenceNumber', 'ufCrm48Status'],
                'start' => ($page - 1) * $pageSize,
                'limit' => $pageSize
            ]);

            if (isset($response['error'])) {
                echo "Error fetching DNT references (page {$page}): " . $response['error_description'] . "</br>";
                break;
            }

            $items = $response['result']['items'] ?? [];

            $validItems = array_filter($items, function ($item) {
                return !empty($item['ufCrm48ReferenceNumber']);
            });

            $allDntRecords = array_merge($allDntRecords, $validItems);

            $morePages = count($items) >= $pageSize;
            $page++;
        } while ($morePages);

        echo "Total DNT records fetched: " . count($allDntRecords) . "</br>";
        return $allDntRecords;
    } catch (Exception $e) {
        echo "Exception in getAllDntReferences: " . $e->getMessage() . "</br>";
        return [];
    }
}

function referenceExistsInListings($referenceNumber)
{
    try {
        $response = CRest::call('crm.item.list', [
            'entityTypeId' => LISTINGS_ENTITY_TYPE_ID,
            'select' => ['id'],
            'filter' => ['ufCrm37ReferenceNumber' => $referenceNumber],
            'limit' => 1
        ]);

        if (isset($response['error'])) {
            echo "Error checking if reference exists: " . $response['error_description'] . "</br>";
            return true;
        }

        return !empty($response['result']['items']);
    } catch (Exception $e) {
        echo "Exception in referenceExistsInListings: " . $e->getMessage() . "</br>";
        return true;
    }
}

function deleteDntItem($dntId, $referenceNumber)
{
    try {
        $response = CRest::call('crm.item.delete', [
            'entityTypeId' => DNT_ENTITY_TYPE_ID,
            'id' => $dntId
        ]);

        if (isset($response['error'])) {
            echo "Error deleting DNT item {$referenceNumber}: " . $response['error_description'] . "</br>";
        } else {
            echo "Deleted DNT item: {$referenceNumber}</br>";
        }
    } catch (Exception $e) {
        echo "Exception in deleteDntItem for {$referenceNumber}: " . $e->getMessage() . "</br>";
    }
}

function getLatestProperties()
{
    try {
        $fiveMinutesAgo = date('c', strtotime('-5 minutes'));
        $response = CRest::call('crm.item.list', [
            'entityTypeId' => LISTINGS_ENTITY_TYPE_ID,
            'select' => [
                'id',
                'ufCrm37ReferenceNumber',
                'ufCrm37Status',
                'ufCrm37TitleEn',
                'ufCrm37AgentName',
                'ufCrm37PhotoLinks',
                'ufCrm37ListingOwner',
                'ufCrm37Bedroom',
                'ufCrm37Bathroom',
                'ufCrm37Size',
                'ufCrm37Price',
                'ufCrm37Location',
                'ufCrm37BayutLocation',
                'ufCrm37PropertyType'
            ],
            'filter' => ['>updatedTime' => $fiveMinutesAgo]
        ]);

        if (isset($response['error'])) {
            echo "Error fetching latest properties: " . $response['error_description'] . "</br>";
            return [];
        }

        return $response['result']['items'] ?? [];
    } catch (Exception $e) {
        echo "Exception in getLatestProperties: " . $e->getMessage() . "</br>";
        return [];
    }
}

function getExistingReference($referenceNumber)
{
    try {
        $response = CRest::call('crm.item.list', [
            'entityTypeId' => DNT_ENTITY_TYPE_ID,
            'select' => ['id', 'ufCrm48Status'],
            'filter' => ['ufCrm48ReferenceNumber' => $referenceNumber]
        ]);

        if (isset($response['error'])) {
            echo "Error fetching reference: " . $response['error_description'] . "</br>";
            return null;
        }

        return $response['result']['items'][0] ?? null;
    } catch (Exception $e) {
        echo "Exception in getExistingReference: " . $e->getMessage() . "</br>";
        return null;
    }
}

function getExistingReferenceWithDetails($referenceNumber)
{
    try {
        $response = CRest::call('crm.item.list', [
            'entityTypeId' => DNT_ENTITY_TYPE_ID,
            'select' => [
                'id',
                'ufCrm48Status',
                'ufCrm48ListingTitle',
                'ufCrm48AgentName',
                'ufCrm48OwnerName',
                'ufCrm48Bedrooms',
                'ufCrm48Bathrooms',
                'ufCrm48Size',
                'ufCrm48Price',
                'ufCrm48LocationPf',
                'ufCrm48LocationBayut',
                'ufCrm48UnitType',
                'ufCrm48OwnerPhone',
                'ufCrm48OwnerUrl'
            ],
            'filter' => ['ufCrm48ReferenceNumber' => $referenceNumber]
        ]);

        if (isset($response['error'])) {
            echo "Error fetching reference details: " . $response['error_description'] . "</br>";
            return null;
        }

        return $response['result']['items'][0] ?? null;
    } catch (Exception $e) {
        echo "Exception in getExistingReferenceWithDetails: " . $e->getMessage() . "</br>";
        return null;
    }
}

function formatImages($photoLinks)
{
    $formattedImages = [];
    if (!is_array($photoLinks)) return $formattedImages;

    foreach ($photoLinks as $index => $url) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            echo "Invalid image URL: {$url}</br>";
            continue;
        }

        try {
            $imageData = @file_get_contents($url);
            if ($imageData) {
                $formattedImages[] = ["image_{$index}.jpg", base64_encode($imageData)];
            } else {
                echo "Failed to fetch image: {$url}</br>";
            }
        } catch (Exception $e) {
            echo "Exception fetching image {$url}: " . $e->getMessage() . "</br>";
        }
    }
    return $formattedImages;
}

function addToDnt($property)
{
    try {
        $referenceNumber = $property['ufCrm37ReferenceNumber'] ?? '';
        if (!$referenceNumber) {
            echo "Missing reference number, skipping property</br>";
            return;
        }

        $status = $property['ufCrm37Status'] ?? '';
        if (!in_array($status, ['PUBLISHED', 'POCKET'])) {
            echo "Skipping property with status: {$status}</br>";
            return;
        }

        // Get existing DNT record with all fields
        $existingItem = getExistingReferenceWithDetails($referenceNumber);

        $newStatus = ($status === 'PUBLISHED') ? 41323 : 41324;

        // Prepare all fields that might need updating
        $formattedImages = formatImages($property['ufCrm37PhotoLinks'] ?? []);

        $ownerName = $property['ufCrm37ListingOwner'] ?? '';
        $owner = null;

        if ($ownerName) {
            $nameParts = explode(' ', trim($ownerName), 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';

            if (function_exists('getUser')) {
                $owner = getUser([
                    '%NAME' => $firstName,
                    '%LAST_NAME' => $lastName,
                    '!ID' => [3, 268]
                ]);
            } else {
                echo "Warning: getUser function not available</br>";
            }
        }

        $ownerUrl = "https://gicrm.ae/company/personal/user/" . ($owner['ID'] ?? '') . "/";
        $ownerPhone = '';
        if ($owner) {
            $ownerPhone = !empty($owner['PERSONAL_MOBILE'])
                ? preg_replace('/\D/', '', $owner['PERSONAL_MOBILE'])
                : (!empty($owner['WORK_PHONE']) ? preg_replace('/\D/', '', $owner['WORK_PHONE']) : '');
        }

        // Prepare updated fields
        $updatedFields = [
            'title' => $referenceNumber . ' - ' . $status,
            'ufCrm48ReferenceNumber' => $referenceNumber,
            'ufCrm48ListingTitle' => $property['ufCrm37TitleEn'] ?? '',
            'ufCrm48AgentName' => $property['ufCrm37AgentName'] ?? '',
            'ufCrm48OwnerName' => $property['ufCrm37ListingOwner'] ?? '',
            'ufCrm48PropertyImages' => $formattedImages,
            'ufCrm48Bedrooms' => $property['ufCrm37Bedroom'] ?? 0,
            'ufCrm48Bathrooms' => $property['ufCrm37Bathroom'] ?? 0,
            'ufCrm48Size' => $property['ufCrm37Size'] ?? 0,
            'ufCrm48Price' => $property['ufCrm37Price'] ?? 0,
            'ufCrm48LocationPf' => $property['ufCrm37Location'] ?? '',
            'ufCrm48LocationBayut' => $property['ufCrm37BayutLocation'] ?? '',
            'ufCrm48Status' => $newStatus,
            'ufCrm48UnitType' => getPropertyTypeFromId($property['ufCrm37PropertyType'] ?? ''),
            'ufCrm48OwnerPhone' => $ownerPhone,
            'ufCrm48OwnerUrl' => $ownerUrl,
            'stageId' => ($status === 'PUBLISHED') ? "DT1130_63:NEW" : 'DT1130_63:PREPARATION'
        ];

        if ($existingItem) {
            $existingId = $existingItem['id'];

            // Check if any fields have changed
            $hasChanges = false;
            $changedFields = [];

            // Fields to compare (exclude images for now as they're complex to compare)
            $fieldsToCompare = [
                'ufCrm48Status' => $newStatus,
                'ufCrm48ListingTitle' => $property['ufCrm37TitleEn'] ?? '',
                'ufCrm48AgentName' => $property['ufCrm37AgentName'] ?? '',
                'ufCrm48OwnerName' => $property['ufCrm37ListingOwner'] ?? '',
                'ufCrm48Bedrooms' => $property['ufCrm37Bedroom'] ?? 0,
                'ufCrm48Bathrooms' => $property['ufCrm37Bathroom'] ?? 0,
                'ufCrm48Size' => $property['ufCrm37Size'] ?? 0,
                'ufCrm48Price' => $property['ufCrm37Price'] ?? 0,
                'ufCrm48LocationPf' => $property['ufCrm37Location'] ?? '',
                'ufCrm48LocationBayut' => $property['ufCrm37BayutLocation'] ?? '',
                'ufCrm48UnitType' => getPropertyTypeFromId($property['ufCrm37PropertyType'] ?? '')
            ];

            foreach ($fieldsToCompare as $field => $newValue) {
                if ((string)($existingItem[$field] ?? '') !== (string)$newValue) {
                    $hasChanges = true;
                    $changedFields[$field] = [
                        'old' => $existingItem[$field] ?? '',
                        'new' => $newValue
                    ];
                }
            }

            // Always update images as they're hard to compare
            $fieldsToUpdate = $updatedFields;

            if ($hasChanges) {
                echo "Found changes for {$referenceNumber}: " . json_encode($changedFields) . "</br>";

                $response = CRest::call('crm.item.update', [
                    'entityTypeId' => DNT_ENTITY_TYPE_ID,
                    'id' => $existingId,
                    'fields' => $fieldsToUpdate
                ]);

                if (isset($response['error'])) {
                    echo "Error updating property: " . $response['error_description'] . "</br>";
                } elseif (!empty($response['result'])) {
                    echo "Updated property fields for: {$referenceNumber}</br>";
                } else {
                    echo "Failed to update fields for: {$referenceNumber}</br>";
                }
            } else {
                echo "No changes detected for {$referenceNumber}, skipping update</br>";
            }

            return;
        }

        // If we reach here, it's a new record to add
        $response = CRest::call('crm.item.add', [
            'entityTypeId' => DNT_ENTITY_TYPE_ID,
            'fields' => $updatedFields
        ]);

        if (isset($response['error'])) {
            echo "Error adding property: " . $response['error_description'] . "</br>";
        } elseif (!empty($response['result']['item'])) {
            echo "Property added to DNT: {$referenceNumber}</br>";
        } else {
            echo "Failed to add property: {$referenceNumber}</br>";
        }
    } catch (Exception $e) {
        echo "Exception in addToDnt for property " . ($property['ufCrm37ReferenceNumber'] ?? 'unknown') . ": " . $e->getMessage() . "</br>";
    }
}

try {
    if (!defined('LISTINGS_ENTITY_TYPE_ID') || !defined('DNT_ENTITY_TYPE_ID')) {
        echo "Error: Required constants are not defined. Check your settings file.</br>";
        exit(1);
    }

    $latestProperties = getLatestProperties();
    $latestReferenceNumbers = getAllListingsReferences();
    $allDntRecords = getAllDntReferences();

    $totalDntRecords = count($allDntRecords);

    $potentialDeletions = count(array_filter($allDntRecords, function ($dntRecord) use ($latestReferenceNumbers) {
        $referenceNumber = $dntRecord['ufCrm48ReferenceNumber'] ?? '';
        $status = $dntRecord['ufCrm48Status'] == 41323 ? 'PUBLISHED' : 'POCKET';

        if (empty($referenceNumber)) return false;

        $matchingRecord = array_values(array_filter($latestReferenceNumbers, function ($item) use ($referenceNumber) {
            return $item['ufCrm37ReferenceNumber'] === $referenceNumber;
        }));

        if (empty($matchingRecord)) return true;

        $matched = reset($matchingRecord);

        if (!(isset($matched['ufCrm37Status']) && $matched['ufCrm37Status'] === $status)) {
            echo "Reference {$referenceNumber} exists in listings with different status. Deleting it.</br>";
        }
        return !(isset($matched['ufCrm37Status']) && $matched['ufCrm37Status'] === $status);
    }));

    $deletionPercentage = round(($totalDntRecords > 0) ? ($potentialDeletions / $totalDntRecords) * 100 : 0, 2);

    if ($deletionPercentage > 10 && !$bulk_delete) {
        echo "WARNING: Script would delete {$potentialDeletions} out of {$totalDntRecords} DNT records ({$deletionPercentage}%).</br>";
        echo "This exceeds the 10% safety threshold. Skipping deletion process.</br>";
    } else {
        echo "Proceeding with deletion check: {$potentialDeletions} out of {$totalDntRecords} DNT records ({$deletionPercentage}%).</br>";

        foreach ($allDntRecords as $dntRecord) {
            $dntId = $dntRecord['id'];
            $dntReferenceNumber = $dntRecord['ufCrm48ReferenceNumber'] ?? '';
            $dntStatus = $dntRecord['ufCrm48Status'] == 41323 ? 'PUBLISHED' : 'POCKET';

            if (empty($dntReferenceNumber)) {
                continue;
            }

            $matchingRecord = array_values(array_filter($latestReferenceNumbers, function ($item) use ($dntReferenceNumber) {
                return $item['ufCrm37ReferenceNumber'] === $dntReferenceNumber;
            }));

            if (empty($matchingRecord)) {
                deleteDntItem($dntId, $dntReferenceNumber);
            } else {
                $matched = reset($matchingRecord);
                $listingStatus = $matched['ufCrm37Status'] ?? '';

                if ($listingStatus !== $dntStatus) {
                    deleteDntItem($dntId, $dntReferenceNumber);
                } else {
                    echo "Reference {$dntReferenceNumber} exists in listings with the same status. Keeping it.</br>";
                }
            }
        }
    }

    echo "Found " . count($latestProperties) . " properties to process</br>";
    foreach ($latestProperties as $property) {
        addToDnt($property);

        usleep(200000);
    }

    echo "Processing completed successfully</br>";
} catch (Exception $e) {
    echo "Critical error: " . $e->getMessage() . "</br>";
    exit(1);
}
