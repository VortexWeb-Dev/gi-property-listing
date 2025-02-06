<?php
require 'utils/index.php';
require __DIR__ . "/crest/settings.php";
header('Content-Type: application/json; charset=UTF-8');

$baseUrl = C_REST_WEB_HOOK_URL;
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

$properties = fetchAllProperties($baseUrl, $entityTypeId, $fields,);

if (count($properties) > 0) {
    $json = generateWebsiteJson($properties);
    echo $json;
} else {
    echo json_encode([]);
}
