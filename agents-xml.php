<?php
require 'utils/index.php';
require_once __DIR__ . "/crest/settings.php";

header('Content-Type: application/xml; charset=UTF-8');

$baseUrl = C_REST_WEB_HOOK_URL;
$entityTypeId = AGENTS_ENTITY_TYPE_ID;
$fields = [
    'id',
    'ufCrm38AgentName',
    'ufCrm38AgentEmail',
    'ufCrm38AgentMobile',
    'ufCrm38AgentLicense',
    'ufCrm38AgentPhoto'
];

$agents = fetchAllAgents($baseUrl, $entityTypeId, $fields);

if (count($agents) > 0) {
    $xml = generateAgentsXml($agents);
    echo $xml;
} else {
    echo '<?xml version="1.0" encoding="UTF-8"?><agents></agents>';
}
