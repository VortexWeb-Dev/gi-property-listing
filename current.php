<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quick start. Local server-side application with UI</title>
</head>

<body>
    <div id="name">
        <?php
        require_once(__DIR__ . '/crest/crestcurrent.php');

        //$result = CRest::call('user.current');
        $result = CRestCurrent::call('user.current');

        echo $result['result']['NAME'] . ' ' . $result['result']['LAST_NAME'];
        ?>
    </div>
</body>

</html>