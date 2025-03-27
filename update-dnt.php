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
                echo "Error fetching listings references (page {$page}): " . $response['error_description'] . "\n";
                break;
            }

            $items = $response['result']['items'] ?? [];

            $allReferences = array_merge($allReferences, $items);

            $morePages = count($items) >= $pageSize;
            $page++;
        } while ($morePages);

        echo "Total references fetched: " . count($allReferences) . "\n";
        return $allReferences;
    } catch (Exception $e) {
        echo "Exception in getAllListingsReferences: " . $e->getMessage() . "\n";
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
                echo "Error fetching DNT references (page {$page}): " . $response['error_description'] . "\n";
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

        echo "Total DNT records fetched: " . count($allDntRecords) . "\n";
        return $allDntRecords;
    } catch (Exception $e) {
        echo "Exception in getAllDntReferences: " . $e->getMessage() . "\n";
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
            echo "Error checking if reference exists: " . $response['error_description'] . "\n";
            return true;
        }

        return !empty($response['result']['items']);
    } catch (Exception $e) {
        echo "Exception in referenceExistsInListings: " . $e->getMessage() . "\n";
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
            echo "Error deleting DNT item {$referenceNumber}: " . $response['error_description'] . "\n";
        } else {
            echo "Deleted DNT item: {$referenceNumber}\n";
        }
    } catch (Exception $e) {
        echo "Exception in deleteDntItem for {$referenceNumber}: " . $e->getMessage() . "\n";
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
            echo "Error fetching latest properties: " . $response['error_description'] . "\n";
            return [];
        }

        return $response['result']['items'] ?? [];
    } catch (Exception $e) {
        echo "Exception in getLatestProperties: " . $e->getMessage() . "\n";
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
            echo "Error fetching reference: " . $response['error_description'] . "\n";
            return null;
        }

        return $response['result']['items'][0] ?? null;
    } catch (Exception $e) {
        echo "Exception in getExistingReference: " . $e->getMessage() . "\n";
        return null;
    }
}

function formatImages($photoLinks)
{
    $formattedImages = [];
    if (!is_array($photoLinks)) return $formattedImages;

    foreach ($photoLinks as $index => $url) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            echo "Invalid image URL: {$url}\n";
            continue;
        }

        try {
            $imageData = @file_get_contents($url);
            if ($imageData) {
                $formattedImages[] = ["image_{$index}.jpg", base64_encode($imageData)];
            } else {
                echo "Failed to fetch image: {$url}\n";
            }
        } catch (Exception $e) {
            echo "Exception fetching image {$url}: " . $e->getMessage() . "\n";
        }
    }
    return $formattedImages;
}

function addToDnt($property)
{
    try {
        $referenceNumber = $property['ufCrm37ReferenceNumber'] ?? '';
        if (!$referenceNumber) {
            echo "Missing reference number, skipping property\n";
            return;
        }

        $status = $property['ufCrm37Status'] ?? '';
        if (!in_array($status, ['PUBLISHED', 'POCKET'])) {
            echo "Skipping property with status: {$status}\n";
            return;
        }

        $existingItem = getExistingReference($referenceNumber);

        $newStatus = ($status === 'PUBLISHED') ? 41323 : 41324;

        if ($existingItem) {
            $existingId = $existingItem['id'];
            $existingStatus = $existingItem['ufCrm48Status'] ?? null;

            if ($existingStatus !== $newStatus) {
                $response = CRest::call('crm.item.update', [
                    'entityTypeId' => DNT_ENTITY_TYPE_ID,
                    'id' => $existingId,
                    'fields' => [
                        'ufCrm48Status' => $newStatus,
                        'stageId' => ($status === 'PUBLISHED') ? "DT1130_63:NEW" : 'DT1130_63:PREPARATION'
                    ]
                ]);

                if (isset($response['error'])) {
                    echo "Error updating property: " . $response['error_description'] . "\n";
                } elseif (!empty($response['result'])) {
                    echo "Updated property status: {$referenceNumber}\n";
                } else {
                    echo "Failed to update status for: {$referenceNumber}\n";
                }
            }

            return;
        }

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
                echo "Warning: getUser function not available\n";
            }
        }

        $ownerUrl = "https://gicrm.ae/company/personal/user/ " . $owner['ID'] . "/";
        $ownerPhone = '';
        if ($owner) {
            $ownerPhone = !empty($owner['PERSONAL_MOBILE'])
                ? preg_replace('/\D/', '', $owner['PERSONAL_MOBILE'])
                : (!empty($owner['WORK_PHONE']) ? preg_replace('/\D/', '', $owner['WORK_PHONE']) : '');
        }

        $response = CRest::call('crm.item.add', [
            'entityTypeId' => DNT_ENTITY_TYPE_ID,
            'fields' => [
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
            ]
        ]);

        if (isset($response['error'])) {
            echo "Error adding property: " . $response['error_description'] . "\n";
        } elseif (!empty($response['result']['item'])) {
            echo "Property added to DNT: {$referenceNumber}\n";
        } else {
            echo "Failed to add property: {$referenceNumber}\n";
        }
    } catch (Exception $e) {
        echo "Exception in addToDnt for property " . ($property['ufCrm37ReferenceNumber'] ?? 'unknown') . ": " . $e->getMessage() . "\n";
    }
}

try {
    if (!defined('LISTINGS_ENTITY_TYPE_ID') || !defined('DNT_ENTITY_TYPE_ID')) {
        echo "Error: Required constants are not defined. Check your settings file.\n";
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
            echo "Reference {$referenceNumber} exists in listings with different status. Deleting it.\n";
        }
        return !(isset($matched['ufCrm37Status']) && $matched['ufCrm37Status'] === $status);
    }));

    $deletionPercentage = round(($totalDntRecords > 0) ? ($potentialDeletions / $totalDntRecords) * 100 : 0, 2);

    if ($deletionPercentage > 1 && !$bulk_delete) {
        echo "WARNING: Script would delete {$potentialDeletions} out of {$totalDntRecords} DNT records ({$deletionPercentage}%).\n";
        echo "This exceeds the 1% safety threshold. Skipping deletion process.\n";
    } else {
        echo "Proceeding with deletion check: {$potentialDeletions} out of {$totalDntRecords} DNT records ({$deletionPercentage}%).\n";

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
                    echo "Reference {$dntReferenceNumber} exists in listings with the same status. Keeping it.\n";
                }
            }
        }
    }

    echo "Found " . count($latestProperties) . " properties to process\n";
    foreach ($latestProperties as $property) {
        addToDnt($property);

        usleep(200000);
    }

    echo "Processing completed successfully\n";
} catch (Exception $e) {
    echo "Critical error: " . $e->getMessage() . "\n";
    exit(1);
}
