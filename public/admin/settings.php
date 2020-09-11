<?php
$status = "";
$msg = "";
require_once "php/classes/admin/userHandler.php";
require_once "php/classes/admin/adminHandler.php";
if(isset($_POST['change_password'])){
    $oldPass = $_POST['old_pass'];
    $newPass = $_POST['new_pass'];
    $confirmPass = $_POST['confirm_pass'];
    $username = $_SESSION['admin_user']['username'];
    if($newPass == $confirmPass){
        $userHandler = new UserHandler();
        $res = $userHandler->changePassword($username, $oldPass, $newPass);
        if($res[0]){
            $status = "success";
            $msg = "Updated your password";
        }else{
            $status = "danger";
            $msg = "FAILED: ". $res[1];
        }
    }else{
        $status = "danger";
        $msg = "Your New Passowrd and Confirm Password must match.";
    }
}

$adminHandler = new AdminHandler();
$ipStatus = "";
$ipMsg = "";
if(isset($_POST['add-ip'])){
    $ip = $_POST['new-ip'];
    $res = $adminHandler->addIpAddress($ip);
    if($res[0]){
        $_GET['newIp'] = "";
        $ipStatus = "success";
    }else{
        $ipStatus = "danger";
    }
    $ipMsg = $res[1];
}
if(isset($_POST['delete-ip'])){
    $ip = $_POST['delete-ip'];
    $res = $adminHandler->deleteIp($ip);
    if($res[0]){
        $ipStatus = "success";
    }else{
        $ipStatus = "danger";
    }
    $ipMsg = $res[1];
}
$printerMsg = "";
$printerStatus = "";
if(isset($_POST['printer-submit'])){
    // print_r($_POST);
    // die();
    $printerName = $_POST['printerName'];
    $ipAddress = $_POST['printerIpAddress'];
    $printerSize = $_POST['printerSize'];
    $res = $adminHandler->addNewPrinter($printerName, $ipAddress, $printerSize);
    if($res[0]){
        $printerStatus = "success";
    }else{
        $printerStatus = "danger";
    }
    $printerMsg = $res[1];
}

if(isset($_POST['delete-printer'])){
    $printerId = $_POST['delete-printer'];
    $res = $adminHandler->deletePrinter($printerId);
    if($res[0]){
        $printerStatus = "success";
    }else{
        $printerStatus = "danger";
    }
    $printerMsg = $res[1];
}
$title = "Settings";
require_once "header.php";
$ips = $adminHandler->getIpAddresses();
$printers = $adminHandler->getPrinters();
$_POST = array();
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-5"><?= $title; ?></h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><?= $title; ?></li>
            </ol>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table mr-1">
                    </i>
                    Change Password
                    </div>
                <div class="card-body">
                    <?php if($status != ""){ ?>
                    <div id="status">
                        <div class="alert alert-dismissible alert-<?= $status ?>">
                            <p><?= $msg ?></p>
                        </div>
                    </div>
                    <?php } ?>
                    <form method="post">
                        <div class="form-group">
                            <input type="password" name="old_pass" placeholder="Old Password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="new_pass" placeholder="New Password" class="form-control" required minlength="6">
                        </div>
                        <div class="form-group">
                            <input type="password" name="confirm_pass" placeholder="Confirm Password" class="form-control" required minlength="6">
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Change Password" name="change_password" class="btn btn-primary">
                        </div>
                    </form>
                </div>
                <div class="card mb-4">
                <div class="card-header">
                    <p>IP Whitelist</p>
                </div>
                <div class="card-body" style="height: 300px; overflow-y: scroll;">
                    <?php if(!empty($ipStatus)){ ?>
                    <div id="status">
                        <div class="alert alert-dismissible alert-<?= $ipStatus ?>">
                            <p><?= $ipMsg ?></p>
                        </div>
                    </div>
                    <?php } ?>
                    <table class="table table-bordered">
                        <tr>
                            <th>#</th>
                            <th>IP Address</th>
                            <th>Added By</th>
                            <th>Remove</th>
                        </tr>
                        <?php
                            $i = 0;
                            foreach($ips as $ip){
                                ?>
                                <tr  id="<?= "ip-".$ip['id']; ?>">
                                    <td><?= ++$i ?></td>
                                    <td><?= $ip['ipAddress'] ?></td>
                                    <td><?= $ip['addedBy'] ?></td>
                                    <td><form method="post"><button type="submit" name="delete-ip" value="<?php echo $ip['ipAddress'] ?>" class="btn btn-danger">Delete</button></form></td>
                                </tr>
                                <?php
                            }
                        ?>
                    </table>
                    <form method="post" action="/settings">
                        <table class="table table-bordered">
                            <tr>
                                <td><input type="text" class="form-control" id="new-ip" name="new-ip" value="<?= $router->params[0]; ?>" required placeholder="Enter new IP Address"></td>
                                <td><button id="addIp" class="btn btn-primary" name="add-ip">Add</button></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    <p>Printer Setup</p>
                </div>
                <div class="card-body" style="height: 300px; overflow-y: scroll;">
                    <?php if(!empty($printerStatus)){ ?>
                    <div id="status">
                        <div class="alert alert-dismissible alert-<?php echo $printerStatus ?>">
                            <p><?php echo $printerMsg ?></p>
                        </div>
                    </div>
                    <?php } ?>
                    <table class="table table-bordered">
                        <tr>
                            <th>#</th>
                            <th>Printer Name</th>
                            <th>Ip Address</th>
                            <th>Printer Size</th>
                            <th>Delete</th>
                        </tr>
                        <?php
                            $i = 0;
                            foreach($printers as $printer){
                                ?>
                                <tr  id="<?php echo "printer-".$printer['id']; ?>">
                                    <td><?php echo ++$i ?></td>
                                    <td><?php echo $printer['printerName'] ?></td>
                                    <td><?php echo $printer['ipAddress'] ?></td>
                                    <td><?php echo $printer['printerSize'] ?></td>
                                    <td><form method="post"><button type="submit" name="delete-printer" value="<?php echo $printer['id'] ?>" class="btn btn-danger">Delete</button></form></td>
                                </tr>
                                <?php
                            }
                        ?>
                    </table>
                    <form method="post">
                        <div class="form-group">
                            <input type="text" name="printerName" id="" placeholder="Printer Name" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="text" name="printerIpAddress" id="" placeholder="Printer IP Address" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <select name="printerSize" id="printerSize" class="form-control">
                                <option value="inch2">inch2</option>
                                <option value="inch3" selected>inch3</option>
                                <option value="inch4">inch4</option>
                            </select>    
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" name="printer-submit" value="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="/js/admin/scripts.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        <script src="/public/admin/assets/demo/datatables-demo.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.26.0/polyfill.js"></script>
        <script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
        <script>
            $("#downloadBtn").click(function(){
                $("#dataTable").table2excel({
                    name: "<?php echo $title; ?>",
                    filename: "<?php echo $title; ?>.xls", // do include extension
                    preserveColors: false // set to true if you want background colors and font colors preserved
                });
            });
        </script>

    </body>
</html>