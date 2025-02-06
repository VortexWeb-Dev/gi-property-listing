<?php
require __DIR__ . '/crest/settings.php';
require __DIR__ . '/controllers/SpaController.php';
require __DIR__ . '/utils/index.php';
?>

<script>
    let DEVELOPERS_ENTITY_ID = <?php echo json_encode(DEVELOPERS_ENTITY_TYPE_ID); ?>;
    let AGENTS_ENTITY_ID = <?php echo json_encode(AGENTS_ENTITY_TYPE_ID); ?>;
    let PF_LOCATIONS_ENTITY_ID = <?php echo json_encode(PF_LOCATIONS_ENTITY_TYPE_ID); ?>;
    let BAYUT_LOCATIONS_ENTITY_ID = <?php echo json_encode(BAYUT_LOCATIONS_ENTITY_TYPE_ID); ?>;
    let LISTINGS_ENTITY_TYPE_ID = <?php echo json_encode(LISTINGS_ENTITY_TYPE_ID); ?>;

    let API_BASE_URL = <?php echo json_encode(C_REST_WEB_HOOK_URL); ?>;
</script>

<?php

include __DIR__ . '/views/header.php';

$currentUser = fetchCurrentUser();
$currentUserId = $currentUser['ID'];
$isAdmin = isAdmin($currentUserId);

include 'views/components/toast.php';
include 'views/components/topbar.php';

$pages = [
    'properties' => 'views/properties/index.php',
    'add-property' => 'views/properties/add.php',
    'edit-property' => 'views/properties/edit.php',
    'view-property' => 'views/properties/view.php',

    'pocket' => 'views/pocket/index.php',
    'agents' => 'views/agents/index.php',
    'developers' => 'views/developers/index.php',
    'pf-locations' => 'views/pf-locations/index.php',
    'bayut-locations' => 'views/bayut-locations/index.php',
    'settings' => 'views/settings/index.php',
    'reports' => 'views/reports/index.php',
];

$page = isset($_GET['page']) && array_key_exists($_GET['page'], $pages) ? $_GET['page'] : 'properties';


require $pages[$page];

if (!array_key_exists($page, $pages)) {
    header("Location: index.php?page=properties';");
    exit;
}
?>

<script>
    // Store isAdmin in localStorage
    localStorage.setItem('isAdmin', <?php echo json_encode($isAdmin); ?>);
</script>

<?php
include __DIR__ . '/views/footer.php';
