<?php

require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';
require_once __DIR__ . '/utils/index.php';

function getLatestProperties()
{
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
            'ufCrm37ListingOwner'
        ],
        'filter' => ['>updatedTime' => $eightMinutesAgo]
    ]);

    return $response['result']['items'] ?? [];
}

function getExistingReference($referenceNumber)
{
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
}

function formatImages($photoLinks)
{
    $formattedImages = [];
    if (!is_array($photoLinks)) return $formattedImages;

    foreach ($photoLinks as $index => $url) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            continue;
        }

        $imageData = @file_get_contents($url);
        if ($imageData) {
            $formattedImages[] = ["image_{$index}.jpg", base64_encode($imageData)];
        }
    }
    return $formattedImages;
}

function addToDnt($property)
{
    $referenceNumber = $property['ufCrm37ReferenceNumber'] ?? '';
    if (!$referenceNumber) {
        return;
    }

    $status = $property['ufCrm37Status'] ?? '';
    if (!in_array($status, ['PUBLISHED', 'POCKET'])) {
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

            if (!empty($response['result'])) {
                echo "Updated property status: {$referenceNumber}\n";
            } else {
                echo "Failed to update status for: {$referenceNumber}\n";
            }
        }

        return;
    }

    $formattedImages = formatImages($property['ufCrm37PhotoLinks'] ?? []);

    $ownerName = $property['ufCrm37ListingOwner'] ?? null;
    $owner = null;

    if ($ownerName) {
        $nameParts = explode(' ', trim($ownerName), 2);
        $firstName = $nameParts[0] ?? null;
        $lastName = $nameParts[1] ?? null;

        $owner = getUser([
            '%NAME' => $firstName,
            '%LAST_NAME' => $lastName,
            '!ID' => [3, 268]
        ]);
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
            'ufCrm48Location' => $property['ufCrm37Location'] ?? '',
            'ufCrm48Status' => $newStatus,
            'ufCrm48UnitType' => getPropertyType($property['ufCrm37PropertyType']),
            'ufCrm48OwnerPhone' => $owner ? (!empty($owner['PERSONAL_MOBILE']) ? $owner['PERSONAL_MOBILE'] : $owner['WORK_PHONE']) : '',
            'stageId' => ($status === 'PUBLISHED') ? "DT1130_63:NEW" : 'DT1130_63:PREPARATION'
        ]
    ]);

    if (!empty($response['result']['item'])) {
        echo "Property added to DNT: {$referenceNumber}\n";
    } else {
        echo "Failed to add property: {$referenceNumber}\n";
    }
}

$latestProperties = getLatestProperties();

foreach ($latestProperties as $property) {
    addToDnt($property);
}
