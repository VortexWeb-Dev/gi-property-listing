<?php

require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';
require_once __DIR__ . '/utils/index.php';

function getLatestProperties()
{
    try {
        $eightMinutesAgo = date('c', strtotime('-8 minutes'));
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
            'filter' => ['>updatedTime' => $eightMinutesAgo]
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

        $ownerPhone = '';
        if ($owner) {
            $ownerPhone = !empty($owner['PERSONAL_MOBILE']) ? $owner['PERSONAL_MOBILE'] : (!empty($owner['WORK_PHONE']) ? $owner['WORK_PHONE'] : '');
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