<div class="bg-white shadow-md rounded-lg p-6 ">
    <h2 class="text-2xl font-semibold">Management</h2>
    <p class="text-sm text-gray-500 mb-4">Please fill in all the required fields</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 my-4">
        <!-- Column 1 -->
        <div class="max-w-sm">
            <label for="reference" class="block text-sm font-medium mb-2">Reference <span class="text-danger">*</span></label>
            <input type="text" id="reference" name="reference" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" required>
        </div>
        <!-- Column 2 -->
        <div class="max-w-sm">
            <label for="listing_agent" class="block text-sm font-medium mb-2">Listing Agent <span class="text-danger">*</span></label>
            <select id="listing_agent" name="listing_agent" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" required>
                <option value="">Please select</option>
            </select>
        </div>
        <!-- Column 3 -->
        <div class="max-w-sm">
            <label for="qr_code_url" class="block text-sm font-medium mb-2">Listing Owner <span class="text-danger">*</span></label>
            <select id="listing_owner" name="listing_owner" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" required>
                <option value="">Please select</option>
                <option value="Abdallah Mohammad Saleem Damer">Abdallah Mohammad Saleem Damer</option>
                <option value="Afrah Abdalaziz Mohammed Yahia">Afrah Abdalaziz Mohammed Yahia</option>
                <option value="Ahmed Mohamed Mamdouh Mohamed Abozamel">Ahmed Mohamed Mamdouh Mohamed Abozamel</option>
                <option value="Ahsan Hafeez">Ahsan Hafeez</option>
                <option value="Alistair Watson">Alistair Watson</option>
                <option value="Emad Mohamed Younes">Emad Mohamed Younes</option>
                <option value="Faycal Brahimi">Faycal Brahimi</option>
                <option value="Hossein Alamdar Mohammadi">Hossein Alamdar Mohammadi</option>
                <option value="Laila Kastali">Laila Kastali</option>
                <option value="Lamiya Adilova">Lamiya Adilova</option>
                <option value="Lyna Mokhtari">Lyna Mokhtari</option>
                <option value="Mahmoud Kizawi">Mahmoud Kizawi</option>
                <option value="Manoj Kumar Hingorani">Manoj Kumar Hingorani</option>
                <option value="Mariyam Kaltai">Mariyam Kaltai</option>
                <option value="Mohamed Hamada Haggag Yousef">Mohamed Hamada Haggag Yousef</option>
                <option value="Mohamed Mamdouh Mohamed Ibrahim Elfarash">Mohamed Mamdouh Mohamed Ibrahim Elfarash</option>
                <option value="Mohamed Nagy Mohamed Khamis">Mohamed Nagy Mohamed Khamis</option>
                <option value="Mohamed Saeed Abdelgaber Ahmed">Mohamed Saeed Abdelgaber Ahmed</option>
                <option value="Mohammed Razy">Mohammed Razy</option>
                <option value="Nichol Mistoso">Nichol Mistoso</option>
                <option value="Odai Shoubaki">Odai Shoubaki</option>
                <option value="Onyeka Joshua Ogbuefi">Onyeka Joshua Ogbuefi</option>
                <option value="Rami Moustafa Abdelhamih Zaki">Rami Moustafa Abdelhamih Zaki</option>
                <option value="Stephane Nito Sob">Stephane Nito Sob</option>
                <option value="Thierno Birahim Ndao">Thierno Birahim Ndao</option>
                <option value="Yanoula Evangelou">Yanoula Evangelou</option>
            </select>
        </div>

        <!-- Column 1 -->
        <div class="max-w-sm">
            <label for="landlord_name" class="block text-sm font-medium mb-2">Landlord Name</label>
            <input type="text" id="landlord_name" name="landlord_name" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
        </div>
        <!-- Column 2 -->
        <div class="max-w-sm">
            <label for="landlord_email" class="block text-sm font-medium mb-2">Landlord Email</label>
            <input type="email" id="landlord_email" name="landlord_email" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
        </div>
        <!-- Column 1 -->
        <div class="max-w-sm">
            <label for="landlord_phone" class="block text-sm font-medium mb-2">Landlord Phone</label>
            <input type="text" id="landlord_phone" name="landlord_phone" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
        </div>

        <!-- Column 1 -->
        <div class="max-w-sm">
            <label for="availability" class="block text-sm font-medium mb-2">Availability</label>
            <select id="availability" name="availability" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                <option value="">Please select</option>
                <option value="available">Available</option>
                <option value="underOffer">Under Offer</option>
                <option value="reserved">Reserved</option>
                <option value="sold">Sold</option>
            </select>
        </div>
        <!-- Column 2 -->
        <div class="max-w-sm">
            <label for="available_from" class="block text-sm font-medium mb-2">Available From</label>
            <input type="date" id="available_from" name="available_from" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
        </div>
        <!-- Column 3 -->
        <div class="max-w-sm">
            <label for="contract_expiry" class="block text-sm font-medium mb-2">Contract Expiry Date</label>
            <input type="date" id="contract_expiry" name="contract_expiry" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const listingAgentSelect = document.getElementById('listing_agent');
        const listingOwnerSelect = document.getElementById('listing_owner');

        const baseUrl = API_BASE_URL;

        const fetchAndDisplayOptions = async () => {
            try {
                const agentsResponse = await fetch(`${baseUrl}/crm.item.list?entityTypeId=${AGENTS_ENTITY_ID}&select[0]=ufCrm38AgentId&select[1]=ufCrm38AgentName&order[ufCrm38AgentName]=asc`);
                const agentsData = await agentsResponse.json();
                const agents = agentsData.result.items;

                agents.forEach(agent => {
                    const option = document.createElement('option');
                    option.value = agent.ufCrm38AgentId;
                    option.textContent = agent.ufCrm38AgentName;
                    listingAgentSelect.appendChild(option);
                });

                /*
                let owners = [];
                const ownersResponse = await fetch(`${baseUrl}/user.get?select[0]=NAME&select[1]=LAST_NAME&order[NAME]=asc`);
                const ownersData = await ownersResponse.json();
                const totalOwners = ownersData.total;

                for (let i = 0; i < Math.ceil(totalOwners / 50); i++) {
                    const ownersResponse = await fetch(`${baseUrl}/user.get?select[0]=NAME&select[1]=LAST_NAME&order[NAME]=asc&start=${i * 50}`);
                    const ownersData = await ownersResponse.json();
                    owners = owners.concat(ownersData.result.map(owner => {
                        return {
                            NAME: (owner.NAME + ' ' + owner.LAST_NAME).trim()
                        }
                    }));
                }

                agents.forEach(agent => {
                    owners.push({
                        NAME: agent.ufCrm38AgentName
                    })
                })

                owners = owners.filter((owner, index, self) => {
                    return self.findIndex((o) => o.NAME === owner.NAME) === index && owner.NAME !== '';
                }).sort((a, b) => a.NAME.localeCompare(b.NAME));

                owners.forEach(owner => {
                    const option = document.createElement('option');
                    option.value = owner.NAME;
                    option.textContent = owner.NAME;
                    listingOwnerSelect.appendChild(option);
                });
                */

            } catch (error) {
                console.error('Error fetching listing agents:', error);
            }
        };

        fetchAndDisplayOptions();

        const timestamp = new Date().getTime().toString();
        const first4 = timestamp.slice(0, 4);
        const last4 = timestamp.slice(-4);
        const newReferenceNumber = 'giproperties-' + first4 + last4;
        document.getElementById('reference').value = newReferenceNumber;

    });
</script>