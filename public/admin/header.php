<?php
function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

$myIp = getUserIpAddr();
require_once "php/classes/admin/adminHandler.php";
$adminHandler = new AdminHandler();

$ipList = $adminHandler->getIpAddresses();
$ipStatus = false;
foreach ($ipList as $key => $ip) {
    if($ip['ipAddress'] == $myIp){
        $ipStatus = true;
        break;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title><?php echo $title; ?> - QR-diner Admin</title>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/css/admin/my-icons-collection/font/flaticon.css">
        <link href="/css/admin/styles.css" rel="stylesheet" />
        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>    
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand" href="/">QR DINER</a><button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button
            ><!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
               
            </form> 
            <ul class="navbar-nav ml-auto ml-md-0">
               <li class="nav-item dropdown">
                   <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                   <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                       <a class="dropdown-item" href="/settings">Settings</a>
                        <?php if($_SESSION['admin_user']['accessType'] == '1'){ ?>
                       <a class="dropdown-item" href="/manage-access">Manage Access</a>
                        <?php } ?>
                       <div class="dropdown-divider"></div>
                       <a class="dropdown-item" href="/logout">Logout</a>
                   </div>
               </li>
           </ul>
       </nav>
       <div id="layoutSidenav">
           <div id="layoutSidenav_nav">
               <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                   <div class="sb-sidenav-menu">
                       <div class="nav">
                           <?php if($_SESSION['admin_user']['accessType'] == '1' || $_SESSION['admin_user']['accessType'] == '2'){ ?>
                           <a class="nav-link" href="/"
                               ><div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                               Dashboard</a>
                           <?php } ?>
                           <?php if($_SESSION['admin_user']['accessType'] == '1' || $_SESSION['admin_user']['accessType'] == '2'){ ?>
                           <a class="nav-link" href="/view-tables"
                               ><div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                               View Tables</a>
                           <?php } ?>
                           <?php if($_SESSION['admin_user']['accessType'] == '1' || $_SESSION['admin_user']['accessType'] == '2' || $_SESSION['admin_user']['accessType'] == '5'){ ?>
                           <a class="nav-link" href="/orders"
                               ><div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                               Orders</a>
                           <?php } ?>
                           <?php if($_SESSION['admin_user']['accessType'] == '1' || $_SESSION['admin_user']['accessType'] == '2' || $_SESSION['admin_user']['accessType'] == '5'){ ?>
                           <a class="nav-link" href="/menu"
                               ><div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                               Manage Menu</a>
                           <?php } ?>
                           
                           <?php if($_SESSION['admin_user']['accessType'] == '1' || $_SESSION['admin_user']['accessType'] == '2' || $_SESSION['admin_user']['accessType'] == '5'){ ?>
                           <a class="nav-link" href="/tables"><div class="sb-nav-link-icon"><i class="fas fa-handshake"></i></div>
                               Manage Tables</a>
                               <?php } ?>
                           <?php if($_SESSION['admin_user']['accessType'] == '1' || $_SESSION['admin_user']['accessType'] == '2' || $_SESSION['admin_user']['accessType'] == '3'){ ?>
                           <a class="nav-link" href="/feedbacks"
                               ><div class="sb-nav-link-icon"><i class="fas fa-shopping-basket"></i></div>
                               Feedback</a>
                           <?php } ?>
                           
                           <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                               <nav class="sb-sidenav-menu-nested nav"><a class="nav-link" href="layout-static.php">Static Navigation</a></nav>
                           </div>
                           <?php if($_SESSION['admin_user']['accessType'] == '1'){ ?>
                           <a class="nav-link" href="/others"
                               ><div class="sb-nav-link-icon"><i class="fas fa-compass"></i> </div>
                                Others</a>
                           <?php } ?>
                       </div>
                    <?php if(!$ipStatus){ ?>
                    <div id="status">
                        <div class="alert alert-dismissible alert-danger">
                            <p><?php echo $ipMsg ?>Your Public IP is Changed, <a href="/settings/<?php echo $myIp; ?>">Click Here to Update now</a></p>
                        </div>
                    </div>
                    <?php } ?>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        <?php echo $_SESSION['admin_user']['username']; ?>
                    </div>
                </nav>
            </div>
<br><br>