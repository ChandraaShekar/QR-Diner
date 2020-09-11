<?php

// echo "hello";


require_once "php/classes/admin/tableHandler.php";
$tableHandler = new TableHandler();

$tableData = $tableHandler->getTables();

echo json_encode($tableData);

?>