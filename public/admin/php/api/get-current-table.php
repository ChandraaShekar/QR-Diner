<?php

require_once "php/classes/admin/tableHandler.php";
$tableHandler = new TableHandler();

if(isset($router->params[0])){
    $tableData = $tableHandler->getTableData($router->params[0]);

    echo json_encode($tableData);
}
die();
?>