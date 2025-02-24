<?php

require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';

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

function referenceExists($referenceNumber)
{
    $response = CRest::call('crm.item.list', [
        'entityTypeId' => DNT_ENTITY_TYPE_ID,
        'filter' => ['ufCrm48ReferenceNumber' => $referenceNumber]
    ]);
    return !empty($response['result']['items']);
}

function formatImages($photoLinks)
{
    $formattedImages = [];
    foreach ($photoLinks as $index => $url) {
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
    if (!$referenceNumber || referenceExists($referenceNumber)) {
        return;
    }

    $status = $property['ufCrm37Status'] ?? '';
    if (!in_array($status, ['PUBLISHED', 'POCKET'])) {
        return;
    }

    $formattedImages = formatImages($property['ufCrm37PhotoLinks'] ?? []);

    $response = CRest::call('crm.item.add', [
        'entityTypeId' => DNT_ENTITY_TYPE_ID,
        'fields' => [
            'title' => $referenceNumber . ' - ' . $status,
            'ufCrm48ReferenceNumber' => $referenceNumber,
            'ufCrm48ListingTitle' => $property['ufCrm37TitleEn'] ?? '',
            'ufCrm48AgentName' => $property['ufCrm37AgentName'] ?? '',
            'ufCrm48OwnerName' => $property['ufCrm37ListingOwner'] ?? '',
            'ufCrm48PropertyImages' => $formattedImages,
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
