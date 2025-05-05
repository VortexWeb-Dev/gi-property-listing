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

function getDetailedDntItem($id)
{
    try {
        $response = CRest::call('crm.item.get', [
            'entityTypeId' => DNT_ENTITY_TYPE_ID,
            'id' => $id
        ]);

        if (isset($response['error'])) {
            echo "Error fetching detailed DNT item: " . $response['error_description'] . "</br>";
            return null;
        }

        return $response['result']['item'] ?? null;
    } catch (Exception $e) {
        echo "Exception in getDetailedDntItem: " . $e->getMessage() . "</br>";
        return null;
    }
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

        $existingItem = getExistingReference($referenceNumber);
        $newStatus = ($status === 'PUBLISHED') ? 41323 : 41324;

        // Get owner information
        $ownerName = trim($property['ufCrm37ListingOwner'] ?? '');
        $owner = null;
        $ownerPhone = '';

        if (!empty($ownerName) && function_exists('getUser')) {
            $nameParts = preg_split('/\s+/', $ownerName);
            $totalParts = count($nameParts);

            for ($i = 1; $i < $totalParts; $i++) {
                $firstName = implode(' ', array_slice($nameParts, 0, $i));
                $lastName = implode(' ', array_slice($nameParts, $i));

                $owner = getUser([
                    '%NAME' => $firstName,
                    '%LAST_NAME' => $lastName,
                    '!ID' => [3, 268]
                ]);

                if (!empty($owner)) {
                    break;
                }
            }

            if (!$owner && $totalParts === 1) {
                $owner = getUser([
                    '%NAME' => $ownerName,
                    '!ID' => [3, 268]
                ]);
            }

            if (!$owner) {
                echo "User not found for owner name: <strong>$ownerName</strong><br/>";
            } else {
                $ownerPhone = !empty($owner['PERSONAL_MOBILE'])
                    ? preg_replace('/\D/', '', $owner['PERSONAL_MOBILE'])
                    : (!empty($owner['WORK_PHONE']) ? preg_replace('/\D/', '', $owner['WORK_PHONE']) : '');
            }
        }

        // Prepare fields for either update or create
        $fields = [
            'title' => $referenceNumber . ' - ' . $status,
            'ufCrm48ReferenceNumber' => $referenceNumber,
            'ufCrm48ListingTitle' => $property['ufCrm37TitleEn'] ?? '',
            'ufCrm48AgentName' => $property['ufCrm37AgentName'] ?? '',
            'ufCrm48OwnerName' => $property['ufCrm37ListingOwner'] ?? '',
            'ufCrm48PropertyImages' => $property['ufCrm37PhotoLinks'] ?? [],
            'ufCrm48Bedrooms' => $property['ufCrm37Bedroom'] ?? 0,
            'ufCrm48Bathrooms' => $property['ufCrm37Bathroom'] ?? 0,
            'ufCrm48Size' => $property['ufCrm37Size'] ?? 0,
            'ufCrm48Price' => $property['ufCrm37Price'] ?? 0,
            'ufCrm48LocationPf' => $property['ufCrm37Location'] ?? '',
            'ufCrm48LocationBayut' => $property['ufCrm37BayutLocation'] ?? '',
            'ufCrm48Status' => $newStatus,
            'ufCrm48ProjectStatus' => $property['ufCrm37ProjectStatus'] ?? '',
            'ufCrm48UnitType' => getPropertyTypeFromId($property['ufCrm37PropertyType'] ?? ''),
            'ufCrm48OwnerPhone' => $ownerPhone,
            'stageId' => ($status === 'PUBLISHED') ? "DT1130_63:NEW" : 'DT1130_63:PREPARATION'
        ];

        if ($existingItem) {
            $existingId = $existingItem['id'];

            // Get full existing item details to compare
            $detailedExistingItem = getDetailedDntItem($existingId);
            if (!$detailedExistingItem) {
                echo "Could not retrieve detailed information for existing DNT item: {$referenceNumber}</br>";
                return;
            }

            // Check if any fields need to be updated
            $needsUpdate = false;
            $updatedFields = [];

            foreach ($fields as $fieldName => $newValue) {
                // Skip reference number field as it's our key and shouldn't change
                if ($fieldName === 'ufCrm48ReferenceNumber') {
                    continue;
                }

                // Compare field values, considering special handling for arrays
                $currentValue = $detailedExistingItem[$fieldName] ?? null;

                // For arrays (like images), we need special comparison
                if (is_array($newValue) && is_array($currentValue)) {
                    $arrayDifference = array_diff($newValue, $currentValue);
                    $isEqual = empty($arrayDifference) && count($newValue) == count($currentValue);
                } else {
                    $isEqual = $currentValue === $newValue;
                }

                if (!$isEqual) {
                    $needsUpdate = true;
                    $updatedFields[] = $fieldName;
                }
            }

            if ($needsUpdate) {
                $response = CRest::call('crm.item.update', [
                    'entityTypeId' => DNT_ENTITY_TYPE_ID,
                    'id' => $existingId,
                    'fields' => $fields
                ]);

                if (isset($response['error'])) {
                    echo "Error updating property: " . $response['error_description'] . "</br>";
                } elseif (!empty($response['result'])) {
                    echo "Updated property {$referenceNumber} fields: " . implode(', ', $updatedFields) . "</br>";
                } else {
                    echo "Failed to update fields for: {$referenceNumber}</br>";
                }
            } else {
                echo "No updates needed for DNT item: {$referenceNumber}</br>";
            }

            return;
        }

        // If we get here, this is a new record
        $response = CRest::call('crm.item.add', [
            'entityTypeId' => DNT_ENTITY_TYPE_ID,
            'fields' => $fields
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
