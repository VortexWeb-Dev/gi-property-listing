<?php

require __DIR__ . "/crest/crest.php";
require __DIR__ . "/crest/crestcurrent.php";
require __DIR__ . "/crest/settings.php";
require __DIR__ . "/utils/index.php";
require __DIR__ . "/vendor/autoload.php";

define('C_REST_WEB_HOOK_URL', 'https://gicrm.ae/rest/1945/7mnw3te56u363prw/');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$id = $_GET['id'];

$response = CRest::call('crm.item.list', [
    "entityTypeId" => LISTINGS_ENTITY_TYPE_ID,
    "filter" => ["id" => $id],
    "select" => [
        "ufCrm37ReferenceNumber",
        "ufCrm37OfferingType",
        "ufCrm37PropertyType",
        "ufCrm37SaleType",
        "ufCrm37UnitNo",
        "ufCrm37Size",
        "ufCrm37Bedroom",
        "ufCrm37Bathroom",
        "ufCrm37Parking",
        "ufCrm37LotSize",
        "ufCrm37TotalPlotSize",
        "ufCrm37BuildupArea",
        "ufCrm37LayoutType",
        "ufCrm37TitleEn",
        "ufCrm37DescriptionEn",
        "ufCrm37TitleAr",
        "ufCrm37DescriptionAr",
        "ufCrm37Geopoints",
        "ufCrm37ListingOwner",
        "ufCrm37LandlordName",
        "ufCrm37LandlordEmail",
        "ufCrm37LandlordContact",
        "ufCrm37ReraPermitNumber",
        "ufCrm37ReraPermitIssueDate",
        "ufCrm37ReraPermitExpirationDate",
        "ufCrm37DtcmPermitNumber",
        "ufCrm37Location",
        "ufCrm37BayutLocation",
        "ufCrm37ProjectName",
        "ufCrm37ProjectStatus",
        "ufCrm37Ownership",
        "ufCrm37Developers",
        "ufCrm37BuildYear",
        "ufCrm37Availability",
        "ufCrm37AvailableFrom",
        "ufCrm37RentalPeriod",
        "ufCrm37Furnished",
        "ufCrm37DownPaymentPrice",
        "ufCrm37NoOfCheques",
        "ufCrm37ServiceCharge",
        "ufCrm37PaymentMethod",
        "ufCrm37FinancialStatus",
        "ufCrm37AgentName",
        "ufCrm37ContractExpiryDate",
        "ufCrm37FloorPlan",
        "ufCrm37QrCodePropertyBooster",
        "ufCrm37VideoTourUrl",
        "ufCrm_37_360_VIEW_URL",
        "ufCrm37BrochureDescription",
        "ufCrm_37_BROCHURE_DESCRIPTION_2",
        "ufCrm37PhotoLinks",
        "ufCrm37Notes",
        "ufCrm37Amenities",
        "ufCrm37Price",
        "ufCrm37Status",
        "ufCrm37HidePrice",
        "ufCrm37PfEnable",
        "ufCrm37BayutEnable",
        "ufCrm37DubizzleEnable",
        "ufCrm37WebsiteEnable",
        "ufCrm37TitleDeed",
        "ufCrm_37_LANDLORD_NAME_2",
        "ufCrm_37_LANDLORD_EMAIL_2",
        "ufCrm_37_LANDLORD_CONTACT_2",
        "ufCrm_37_LANDLORD_NAME_3",
        "ufCrm_37_LANDLORD_EMAIL_3",
        "ufCrm_37_LANDLORD_CONTACT_3"
        // "ufCrm37City",
        // "ufCrm37Community",
        // "ufCrm37SubCommunity",
        // "ufCrm37Tower",
        // "ufCrm37BayutCity",
        // "ufCrm37BayutCommunity",
        // "ufCrm37BayutSubCommunity",
        // "ufCrm37BayutTower",
        // "ufCrm37AgentId",
        // "ufCrm37AgentEmail",
        // "ufCrm37AgentPhone",
        // "ufCrm37AgentLicense",
        // "ufCrm37AgentPhoto",
        // "ufCrm37Watermark",
    ]
]);

$property = $response['result']['items'][0];

if (!$property) {
    die("Property not found.");
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

function getExcelColumn($index)
{
    return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
}

$columnIndex = 1;
foreach ($property as $key => $value) {
    if (empty($value)) {
        continue;
    }

    $colLetter = getExcelColumn($columnIndex);
    $sheet->setCellValue($colLetter . '1', $key);
    $sheet->getStyle($colLetter . '1')->getFont()->setBold(true);
    $sheet->setCellValue($colLetter . '2', is_array($value) ? implode(', ', $value) : $value); // Values
    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
    $columnIndex++;
}

function sanitizeFileName($filename)
{
    $filename = trim($filename);
    $filename = str_replace(' ', '_', $filename);
    $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $filename);
    $filename = preg_replace('/_+/', '_', $filename);

    return $filename;
}

$filename = "property_" . sanitizeFileName($property['ufCrm37ReferenceNumber']) . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
