<?php
require 'utils/index.php';
require_once __DIR__ . "/crest/settings.php";

header('Content-Type: application/xml; charset=UTF-8');

$baseUrl = WEB_HOOK_URL;
$entityTypeId = LISTINGS_ENTITY_TYPE_ID;
$fields = [
    'id',
    'ufCrm37ReferenceNumber',
    'ufCrm37PermitNumber',
    'ufCrm37ReraPermitNumber',
    'ufCrm37DtcmPermitNumber',
    'ufCrm37OfferingType',
    'ufCrm37PropertyType',
    'ufCrm37HidePrice',
    'ufCrm37RentalPeriod',
    'ufCrm37Price',
    'ufCrm37ServiceCharge',
    'ufCrm37NoOfCheques',
    'ufCrm37City',
    'ufCrm37Community',
    'ufCrm37SubCommunity',
    'ufCrm37Tower',
    'ufCrm37TitleEn',
    'ufCrm37TitleAr',
    'ufCrm37DescriptionEn',
    'ufCrm37DescriptionAr',
    'ufCrm37TotalPlotSize',
    'ufCrm37Size',
    'ufCrm37Bedroom',
    'ufCrm37Bathroom',
    'ufCrm37AgentId',
    'ufCrm37AgentName',
    'ufCrm37AgentEmail',
    'ufCrm37AgentPhone',
    'ufCrm37AgentPhoto',
    'ufCrm37BuildYear',
    'ufCrm37Parking',
    'ufCrm37Furnished',
    'ufCrm_37_360_VIEW_URL',
    'ufCrm37PhotoLinks',
    'ufCrm37FloorPlan',
    'ufCrm37Geopoints',
    'ufCrm37AvailableFrom',
    'ufCrm37VideoTourUrl',
    'ufCrm37Developers',
    'ufCrm37ProjectName',
    'ufCrm37ProjectStatus',
    'ufCrm37ListingOwner',
    'ufCrm37Status',
    'ufCrm37PfEnable',
    'ufCrm37BayutEnable',
    'ufCrm37DubizzleEnable',
    'ufCrm37WebsiteEnable',
    'updatedTime',
    'ufCrm37TitleDeed',
    'ufCrm37Amenities'
];

$properties = fetchAllProperties($baseUrl, $entityTypeId, $fields, 'website');

if (count($properties) > 0) {
    $xml = generateWebsiteXml($properties);
    echo $xml;
} else {
    echo '<?xml version="1.0" encoding="UTF-8"?><list></list>';
}
