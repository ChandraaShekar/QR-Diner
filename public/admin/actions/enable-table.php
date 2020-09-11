<?php

require_once "php/classes/admin/tableHandler.php";

if(isset($router->params[0])){
    $tableNo = $router->params[0];
    $tableHandler = new TableHandler();
    $res = $tableHandler->enableTable($tableNo);
    if($res[0]){
        header("Location: /view-tables/$tableNo");
        die($res[1]);
    }else{
        die("Error: ". $res[1] . '<br><a href="/view-tables/'.$tableNo.'">Go Back</a>');
    }
}

?>