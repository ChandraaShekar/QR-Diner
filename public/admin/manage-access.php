<?php

$status = "";
$msg = "";
require_once "php/classes/admin/userHandler.php";
$userHandler = new UserHandler();
if(isset($_POST['add_user'])){
    $username = $_POST['username'];
    $pass = $_POST['new_user_pass'];
    $access_type = $_POST['access-type'];
    $my_access = $_SESSION['admin_user']['access_type'];
    // $myPass = $_POST['myPass'];
    $res= $userHandler->addNewAdmin($username, $pass, $access_type);
    if($res[0]){
        $status = "success";
        $msg = $res[1];
    }else{
        $status = "danger";
        $msg = $res[1];
    }
}

if(isset($_POST['delete_user'])){
    // print_r($_POST);
    // die();
    $userId = $_POST['delete_user'];
    $userHandler->deleteAdminUser($userId);
    $_POST = array();

}
$title = "Manage Access";
$accessTypes = $userHandler->getAccessTypes();
$users = $userHandler->getAdminUsers();


require_once "header.php";

?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-5"><?php echo $title; ?></h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><?php echo $title; ?></li>
            </ol>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table mr-1">
                    </i>
                    Add a User
                    </div>
                <div class="card-body">
                    <?php if($status != ""){ ?>
                    <div id="status">
                        <div class="alert alert-dismissible alert-<?php echo $status ?>">
                            <p><?php echo $msg ?></p>
                        </div>
                    </div>
                    <?php } ?>
                    <form method="post" autocomplete="off">
                        <div class="form-group">
                            <input type="text" name="username" placeholder="Username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="new_user_pass" placeholder="password" class="form-control" required minlength="6">
                        </div>
                        <div class="form-group">
                            <select name="access-type" id="access" class="form-control" required>
                                <option value="">Access Type</option>
                                <?php 
                                    foreach($accessTypes as $accessType){
                                        ?>
                                    <option value="<?php echo $accessType['id']; ?>"><?php echo $accessType['name'] ?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Add User" name="add_user" class="btn btn-primary">
                        </div>
                    </form>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table mr-1">
                        </i>
                        Users List
                    </div>
                    <div class="card-body">
                    <div id="rmstatus">
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Type</th>
                            <?php if($_SESSION['admin_user']['accessType'] == "1" || $_SESSION['admin_user']['accessType'] == "2"){ ?>
                            <th>Added By</th>
                            <?php } ?>
                            <?php if($_SESSION['admin_user']['accessType'] == "1"){ ?>
                            <th>Remove</th>
                            <?php } ?>
                        </tr>
                        <?php 
                        $i = 0;
                        foreach($users as $user){
                            ?>
                            <tr id="<?= "user-".$user['username'] ?>">
                                <td><?= ++$i; ?></td>
                                <td><?= $user['username'] ?></td>
                                <td><?= $user['accessName'] ?></td>
                                <?php if($_SESSION['admin_user']['accessType'] == "1" || $_SESSION['admin_user']['accessType'] == "2"){ ?>
                                <td><?= $user['addedBy'] ?></td>
                                <?php } ?>
                                <?php if($_SESSION['admin_user']['accessType'] == "1"){ ?>
                                <form method="post">
                                    <td><button id="delete-user" type="submit" class="btn btn-danger" name="delete_user"  value="<?php echo $user['username']; ?>" <?php echo ($user['username'] == $_SESSION['admin_user']['username'])? "disabled": "" ?> onclick="return confirm('Are you sure you want to delete this User?')">Remove</button></td>
                                </form>
                                <?php } ?>
                            </tr>
                            <?php             
                        }
                        ?>
                    </table>
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
            function delete_user(username){
                if(confirm("Are you sure?")){
                    $.post(
                        "php/api/remove-user.php",
                        {
                            user_name: username
                        },
                        function(data){
                            if(data == "success"){
                                $("#rmstatus").html(`
                                    <div class="alnert alert-dismissible alert-${data}">
                                        <p>
                                            ${(data == 'success')? "Deleted User": "failed try again later"}
                                        </p>
                                    </div>
                                `);
                                if(data== "success"){
                                    $(`#user-${username}`).hide();
                                }
                            }
                        }
                    )
                }
            }
        </script>

    </body>
</html>