<?php
require_once "php/classes/Router.php";
// echo "<pre>";
// print_r($_SERVER);
$router = new Router();
$router->get($_SERVER);
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if($_SESSION['isAdmin'] == 'true'){
        switch($router->uri){
            case '/login':
                require ADMINDIR . "login.php";
                break;
            case '/':
                require ADMINDIR . "dashboard.php";
                break;
            case '/view-tables':
                // echo "View Tables";
                require ADMINDIR . "viewtables.php";
                break;
            
            case '/view-tables/:a':
                // echo "View Tables";
                require ADMINDIR . "viewtables.php";
                break;
            case '/settings':
                require ADMINDIR . "settings.php";
                break;
            case '/settings/:a':
                require ADMINDIR . "settings.php";
                break;
            case '/manage-access':
                require ADMINDIR . "manage-access.php";
                break;
            case "/orders":
                require ADMINDIR . "orders.php";
                break;
            case '/menu':
                require ADMINDIR . "menu.php";
                break;
            case '/add-item':
                require ADMINDIR . "new-item.php";
                break;
            case '/others':
                require ADMINDIR . "others.php";        
            break;
            case '/home-data':
                require_once ADMINDIR . "php/api/home.php";
            break;
            case '/item-info/:a':
                require ADMINDIR . "item-info.php";
            break;
            case '/edit-item/:a':
                require ADMINDIR . "edit-item.php";
            break;
            case '/tables':
                require ADMINDIR . "tables.php";
            break;
            case '/add-table':
                require ADMINDIR . "add-table.php";
            break;
            case '/table-info/:a':
                require ADMINDIR . "table-info.php";
            break;
            case '/feedbacks':
                require ADMINDIR . "feedbacks.php";
            break;
            case '/get-table-info':
                require ADMINDIR . "php/api/get-table-info.php";
            break;
            case '/get-current-table/:a':
                require ADMINDIR . "php/api/get-current-table.php";
            break;
            case '/enable-table/:a':
                require ADMINDIR . "actions/enable-table.php";
            break;
            case '/order-info/:a':
                require ADMINDIR . "order-info.php";
            break;
            case '/logout':
                require ADMINDIR . "logout.php";
            break;
            default:
                require PUBLICDIR . "404.php";
                break;
        }
    }else{
        switch ($router->uri) {
            case '/menu':
                // $title = "Menu";
                require PUBLICDIR . "menu.php";
            break;
            case '/show-orders':
                require PUBLICDIR . "showOrder.php";
            break;
            case '/orderPlaced':
                // $title = "Order Placed";
                require PUBLICDIR . "orderPlaced.php";
            break;
            case '/feedback':
                require PUBLICDIR . "feedback.php";
            break;
            case '/not-authorized':
                // $title = "Not Authorized";
                require PUBLICDIR . "not-authorized.php";
            break;
            case '/welcome':
                require PUBLICDIR . "welcome.php";
            break;
            case '/new-user/:a/:b':
                // $title = implode($router->params);
                require PUBLICDIR."new-user.php";
            break;
            case '/get-orders':
                require PUBLICDIR . "get-orders.php";
            break;
            case '/order-again':
                require PUBLICDIR . "order-again.php";
            break;
            case '/thankyou':
                require PUBLICDIR . "thankyou.php";
            break;
            case '/get-order-status':
                require PUBLICDIR . "get-order-status.php";
            break;
            case '/logout':
                require PUBLICDIR . "logout.php";
            break;
            case '/404':
                require PUBLICDIR . "404.php";
                break;
            default:
                require  PUBLICDIR ."404.php";
                break;
        }
    }    
}else{
    $router->post($_SERVER, $_POST);
    if($_SESSION['isAdmin'] == 'true'){
        switch($router->uri){
            case '/login':
                require ADMINDIR . "login.php";
                break;
            case '/settings':
                require ADMINDIR . "settings.php";
                break;
            case '/manage-access':
                require ADMINDIR . "manage-access.php";
                break;
            case '/add-item':
                require ADMINDIR . "new-item.php";
                break;
                
            case '/others':
                require ADMINDIR . "others.php";
                break;
            case '/edit-item/:a':
                require ADMINDIR . "edit-item.php";
                break;
            case '/add-table':
                require_once ADMINDIR . "add-table.php";
                break;
            case '/table-info/:a':
                require_once ADMINDIR . "table-info.php";
                break;
            case '/table-info':
                require_once ADMINDIR . "table-info.php";
                break;
            case '/view-tables/:a':
                require_once ADMINDIR . "viewtables.php";
                break;
            case '/view-tables':
                require_once ADMINDIR . "viewtables.php";
                break;
            case '/update-order-status':
                require_once ADMINDIR . "php/api/update-order-status.php";
            break;
            default:
                require PUBLICDIR . "404.php";
                break;
        }
    }else{
        switch($router->uri){
            case '/add-to-orders':
                require PUBLICDIR . "add-to-orders.php";
                break;
            case '/place-order':
                require PUBLICDIR . "place-order.php";
                break;
            case '/feedback':
                require PUBLICDIR . "feedback.php";
                break;
        }
    }
}