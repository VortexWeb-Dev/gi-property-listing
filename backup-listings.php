<?php

require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';
require_once __DIR__ . '/utils/index.php';

define('C_REST_WEB_HOOK_URL', 'https://gicrm.ae/rest/1945/7mnw3te56u363prw/');

class ListingFetcher
{
    private const BATCH_SIZE = 50;
    private array $fieldsToSelect;

    public function __construct()
    {
        $this->fieldsToSelect = [
            "id",
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
            "ufCrm37City",
            "ufCrm37Community",
            "ufCrm37SubCommunity",
            "ufCrm37Tower",
            "ufCrm37BayutLocation",
            "ufCrm37BayutCity",
            "ufCrm37BayutCommunity",
            "ufCrm37BayutSubCommunity",
            "ufCrm37BayutTower",
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
            "ufCrm37AgentId",
            "ufCrm37AgentName",
            "ufCrm37AgentEmail",
            "ufCrm37AgentPhone",
            "ufCrm37AgentLicense",
            "ufCrm37AgentPhoto",
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
            "ufCrm37Watermark",
            "ufCrm_37_LANDLORD_NAME_2",
            "ufCrm_37_LANDLORD_EMAIL_2",
            "ufCrm_37_LANDLORD_CONTACT_2",
            "ufCrm_37_LANDLORD_NAME_3",
            "ufCrm_37_LANDLORD_EMAIL_3",
            "ufCrm_37_LANDLORD_CONTACT_3"
        ];
    }

    public function fetchAll(): array
    {
        try {
            $allListings = [];
            $start = 0;

            do {
                $response = CRest::call('crm.item.list', [
                    'entityTypeId' => LISTINGS_ENTITY_TYPE_ID,
                    'select' => $this->fieldsToSelect,
                    'start' => $start,
                ]);

                if (isset($response['error'])) {
                    error_log("Error fetching listings (start {$start}): " . $response['error_description']);
                    break;
                }

                $items = $response['result']['items'] ?? [];
                $allListings = array_merge($allListings, $items);
                $start += count($items);
            } while (!empty($response['next']) && count($items) === self::BATCH_SIZE);

            return $allListings;
        } catch (Exception $e) {
            error_log("Exception in ListingFetcher::fetchAll: " . $e->getMessage());
            return [];
        }
    }
}

class ListingBackup
{
    private string $backupDir;
    private ListingFetcher $fetcher;

    public function __construct()
    {
        $this->backupDir = __DIR__ . '/backups';
        $this->fetcher = new ListingFetcher();
    }

    private function ensureBackupDirectoryExists(): void
    {
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0777, true);
        }
    }

    private function generateBackupFilename(): string
    {
        return $this->backupDir . '/listings_backup_' . date('Ymd_His') . '.json';
    }

    public function createBackup(): void
    {
        $startTime = microtime(true);

        $listings = $this->fetcher->fetchAll();
        $totalListings = count($listings);
        error_log("Total listings fetched: " . $totalListings);

        if ($totalListings > 0) {
            $this->ensureBackupDirectoryExists();
            $backupFile = $this->generateBackupFilename();

            $jsonData = json_encode($listings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (file_put_contents($backupFile, $jsonData) !== false) {
                error_log("Backup saved to: " . $backupFile);
            } else {
                error_log("Failed to save backup file: " . $backupFile);
            }
        } else {
            error_log("No listings found to back up.");
        }

        $executionTime = microtime(true) - $startTime;
        $message = sprintf("Backup process completed in %.3f seconds\n", $executionTime);
        echo $message;
        error_log($message);
    }
}

class BackupManager
{
    public static function run(): void
    {
        try {
            $backup = new ListingBackup();
            $backup->createBackup();
        } catch (Exception $e) {
            error_log("Fatal error in backup process: " . $e->getMessage());
            echo "An error occurred during the backup process. Please check the error logs.\n";
        }
    }
}

BackupManager::run();
