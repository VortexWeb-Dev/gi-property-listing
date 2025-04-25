<!-- Modal (Transfer to Agent) -->
<div class="modal fade" id="transferAgentModal" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">Transfer Property to Agent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="transferAgentForm" onsubmit="handleTransferAgentSubmit(event)">
                    <input type="hidden" id="transferAgentPropertyIds" name="transferAgentPropertyIds">

                    <div class="form-group">
                        <label for="listing_agent" class="block text-sm font-medium mb-2">Listing Agent <span class="text-danger">*</span></label>
                        <select id="listing_agent" name="listing_agent" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" required>
                            <option value="">Please select</option>
                            <?php
                            define('C_REST_WEB_HOOK_URL', 'https://gicrm.ae/rest/1945/7mnw3te56u363prw/');
                            $agents_result = CRest::call('crm.item.list', [
                                'entityTypeId' => AGENTS_ENTITY_TYPE_ID,
                                'select' => ['ufCrm38AgentId', 'ufCrm38AgentName']
                            ]);
                            $listing_agents = $agents_result['result']['items'] ?? [];

                            if (empty($listing_agents)) {
                                echo '<option disabled>No agents found</option>';
                            } else {
                                foreach ($listing_agents as $agent) {
                                    echo '<option value="' . htmlspecialchars($agent['ufCrm38AgentId']) . '">' . htmlspecialchars($agent['ufCrm38AgentName']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary" id="transferAgentBtn">
                            Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    async function updateItem(entityTypeId, fields, id) {
        try {
            const response = await fetch(`${API_BASE_URL}crm.item.update?entityTypeId=${entityTypeId}&id=${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    fields
                })
            });

            if (!response.ok) throw new Error('Failed to update item');
            console.log('Item updated successfully');
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function getAgent(agentId) {
        const response = await fetch(`${API_BASE_URL}crm.item.list?entityTypeId=${AGENTS_ENTITY_ID}&filter[ufCrm38AgentId]=${agentId}`);
        return (await response.json()).result.items[0] || null;
    }

    async function handleTransferAgentSubmit(e) {
        document.getElementById("transferAgentBtn").disabled = true;
        document.getElementById("transferAgentBtn").innerHTML = 'Transferring...';

        e.preventDefault();

        const formData = new FormData(e.target);
        const agent = await getAgent(formData.get('listing_agent'));
        if (!agent) return console.error('Agent not found');

        const fields = {
            "ufCrm37AgentId": agent.ufCrm38AgentId,
            "ufCrm37AgentName": agent.ufCrm38AgentName,
            "ufCrm37AgentEmail": agent.ufCrm38AgentEmail,
            "ufCrm37AgentPhone": agent.ufCrm38AgentMobile,
            "ufCrm37AgentPhoto": agent.ufCrm38AgentPhoto,
            "ufCrm37AgentLicense": agent.ufCrm38AgentLicense
        };

        const propertyIds = formData.get('transferAgentPropertyIds').split(',') || JSON.parse(localStorage.getItem('transferAgentPropertyIds')) || [];

        for (const id of propertyIds) {
            await updateItem(LISTINGS_ENTITY_TYPE_ID, fields, Number(id));
        }

        localStorage.removeItem('transferAgentPropertyIds');

        window.location.replace('?page=properties');
    }
</script>