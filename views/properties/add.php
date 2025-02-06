<div class="w-4/5 mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <form class="w-full space-y-4" id="addPropertyForm" onsubmit="handleAddProperty(event)" enctype="multipart/form-data">
            <!-- Management -->
            <?php include_once('views/components/add-property/management.php'); ?>
            <!-- Specifications -->
            <?php include_once('views/components/add-property/specifications.php'); ?>
            <!-- Property Permit -->
            <?php include_once('views/components/add-property/permit.php'); ?>
            <!-- Pricing -->
            <?php include_once('views/components/add-property/pricing.php'); ?>
            <!-- Title and Description -->
            <?php include_once('views/components/add-property/title.php'); ?>
            <!-- Amenities -->
            <?php include_once('views/components/add-property/amenities.php'); ?>
            <!-- Location -->
            <?php include_once('views/components/add-property/location.php'); ?>
            <!-- Photos and Videos -->
            <?php include_once('views/components/add-property/media.php'); ?>
            <!-- Floor Plan -->
            <?php include_once('views/components/add-property/floorplan.php'); ?>
            <!-- Documents -->
            <?php // include_once('views/components/add-property/documents.php'); 
            ?>
            <!-- Notes -->
            <?php include_once('views/components/add-property/notes.php'); ?>
            <!-- Portals -->
            <?php include_once('views/components/add-property/portals.php'); ?>
            <!-- Status -->
            <?php include_once('views/components/add-property/status.php'); ?>

            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="window.location.href = 'index.php?page=properties'" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1">
                    Back
                </button>
                <button type="submit" id="submitButton" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById("offering_type").addEventListener("change", function() {
        const offeringType = this.value;
        console.log(offeringType);

        if (offeringType == 'RR' || offeringType == 'CR') {
            document.getElementById("rental_period").setAttribute("required", true);
            document.querySelector('label[for="rental_period"]').innerHTML = 'Rental Period (if rental) <span class="text-danger">*</span>';
        } else {
            document.getElementById("rental_period").removeAttribute("required");
            document.querySelector('label[for="rental_period"]').innerHTML = 'Rental Period (if rental)';
        }
    })

    async function addItem(entityTypeId, fields) {
        try {
            const response = await fetch(`${API_BASE_URL}crm.item.add?entityTypeId=${entityTypeId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    fields,
                }),
            });

            if (response.ok) {
                window.location.href = 'index.php?page=properties';
            } else {
                console.error('Failed to add item');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function handleAddProperty(e) {
        e.preventDefault();

        document.getElementById('submitButton').disabled = true;
        document.getElementById('submitButton').innerHTML = 'Submitting...';

        const form = document.getElementById('addPropertyForm');
        const formData = new FormData(form);
        const data = {};

        formData.forEach((value, key) => {
            data[key] = typeof value === 'string' ? value.trim() : value;
        });

        const agent = await getAgent(data.listing_agent);

        const fields = {
            "ufCrm37TitleDeed": data.title_deed,
            "ufCrm37ReferenceNumber": data.reference,
            "ufCrm37OfferingType": data.offering_type,
            "ufCrm37PropertyType": data.property_type,
            "ufCrm37Price": data.price,
            "ufCrm37TitleEn": data.title_en,
            "ufCrm37DescriptionEn": data.description_en,
            "ufCrm37TitleAr": data.title_ar,
            "ufCrm37DescriptionAr": data.description_ar,
            "ufCrm37Size": data.size,
            "ufCrm37Bedroom": data.bedrooms,
            "ufCrm37Bathroom": data.bathrooms,
            "ufCrm37Parking": data.parkings,
            "ufCrm37Geopoints": `${data.latitude}, ${data.longitude}`,
            "ufCrm37PermitNumber": data.dtcm_permit_number,
            "ufCrm37RentalPeriod": data.rental_period,
            "ufCrm37Furnished": data.furnished,
            "ufCrm37TotalPlotSize": data.total_plot_size,
            "ufCrm37LotSize": data.lot_size,
            "ufCrm37BuildupArea": data.buildup_area,
            "ufCrm37LayoutType": data.layout_type,
            "ufCrm37ProjectName": data.project_name,
            "ufCrm37ProjectStatus": data.project_status,
            "ufCrm37Ownership": data.ownership,
            "ufCrm37Developers": data.developer,
            "ufCrm37BuildYear": data.build_year,
            "ufCrm37Availability": data.availability,
            "ufCrm37AvailableFrom": data.available_from,
            "ufCrm37PaymentMethod": data.payment_method,
            "ufCrm37DownPaymentPrice": data.downpayment_price,
            "ufCrm37NoOfCheques": data.cheques,
            "ufCrm37ServiceCharge": data.service_charge,
            "ufCrm37FinancialStatus": data.financial_status,
            "ufCrm37VideoTourUrl": data.video_tour_url,
            "ufCrm_37_360_VIEW_URL": data["360_view_url"],
            "ufCrm37QrCodePropertyBooster": data.qr_code_url,
            "ufCrm37Location": data.pf_location,
            "ufCrm37City": data.pf_city,
            "ufCrm37Community": data.pf_community,
            "ufCrm37SubCommunity": data.pf_subcommunity,
            "ufCrm37Tower": data.pf_building,
            "ufCrm37BayutLocation": data.bayut_location,
            "ufCrm37BayutCity": data.bayut_city,
            "ufCrm37BayutCommunity": data.bayut_community,
            "ufCrm37BayutSubCommunity": data.bayut_subcommunity,
            "ufCrm37BayutTower": data.bayut_building,
            "ufCrm37Status": data.status,
            "ufCrm37ReraPermitNumber": data.rera_permit_number,
            "ufCrm37ReraPermitIssueDate": data.rera_issue_date,
            "ufCrm37ReraPermitExpirationDate": data.rera_expiration_date,
            "ufCrm37DtcmPermitNumber": data.dtcm_permit_number,
            "ufCrm37ListingOwner": data.listing_owner,
            "ufCrm37LandlordName": data.landlord_name,
            "ufCrm37LandlordEmail": data.landlord_email,
            "ufCrm37LandlordContact": data.landlord_phone,
            "ufCrm37ContractExpiryDate": data.contract_expiry,
            "ufCrm37UnitNo": data.unit_no,
            "ufCrm37SaleType": data.sale_type,
            "ufCrm37BrochureDescription": data.brochure_description_1,
            "ufCrm_37_BROCHURE_DESCRIPTION_2": data.brochure_description_2,
            "ufCrm37HidePrice": data.hide_price == "on" ? "Y" : "N",
            "ufCrm37PfEnable": data.pf_enable == "on" ? "Y" : "N",
            "ufCrm37BayutEnable": data.bayut_enable == "on" ? "Y" : "N",
            "ufCrm37DubizzleEnable": data.dubizzle_enable == "on" ? "Y" : "N",
            "ufCrm37WebsiteEnable": data.website_enable == "on" ? "Y" : "N",
        };

        if (agent) {
            fields["ufCrm37AgentId"] = agent.ufCrm38AgentId;
            fields["ufCrm37AgentName"] = agent.ufCrm38AgentName;
            fields["ufCrm37AgentEmail"] = agent.ufCrm38AgentEmail;
            fields["ufCrm37AgentPhone"] = agent.ufCrm38AgentMobile;
            fields["ufCrm37AgentPhoto"] = agent.ufCrm38AgentPhoto;
            fields["ufCrm37AgentLicense"] = agent.ufCrm38AgentLicense;
        }

        // Notes
        const notesString = data.notes;
        if (notesString) {
            const notesArray = JSON.parse(notesString);
            if (notesArray) {
                fields["ufCrm37Notes"] = notesArray;
            }
        }

        // Amenities
        const amenitiesString = data.amenities;
        if (amenitiesString) {
            const amenitiesArray = JSON.parse(amenitiesString);
            if (amenitiesArray) {
                fields["ufCrm37Amenities"] = amenitiesArray;
            }
        }

        // Property Photos
        const photos = document.getElementById('selectedImages').value;
        if (photos) {
            const fixedPhotos = photos.replace(/\\'/g, '"');
            const photoArray = JSON.parse(fixedPhotos);
            const watermarkPath = 'assets/images/watermark.png?cache=' + Date.now();
            const uploadedImages = await processBase64Images(photoArray, watermarkPath);

            if (uploadedImages.length > 0) {
                fields["ufCrm37PhotoLinks"] = uploadedImages;
            }
        }

        // Floorplan
        const floorplan = document.getElementById('selectedFloorplan').value;
        if (floorplan) {
            const fixedFloorplan = floorplan.replace(/\\'/g, '"');
            const floorplanArray = JSON.parse(fixedFloorplan);
            const watermarkPath = 'assets/images/watermark.png?cache=' + Date.now();
            const uploadedFloorplan = await processBase64Images(floorplanArray, watermarkPath);

            if (uploadedFloorplan.length > 0) {
                fields["ufCrm37FloorPlan"] = uploadedFloorplan[0];
            }
        }

        // Documents
        // const documents = document.getElementById('documents')?.files;
        // if (documents) {
        //     if (documents.length > 0) {
        //         let documentUrls = [];

        //         for (const document of documents) {
        //             if (document.size > 10485760) {
        //                 alert('File size must be less than 10MB');
        //                 return;
        //             }
        //             const uploadedDocument = await uploadFile(document);
        //             documentUrls.push(uploadedDocument);
        //         }

        //         fields["ufCrm37Documents"] = documentUrls;
        //     }

        // }

        // Add to CRM
        addItem(LISTINGS_ENTITY_TYPE_ID, fields, '?page=properties');
    }
</script>