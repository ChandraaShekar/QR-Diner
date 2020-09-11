<?php
$title = "Others";
require_once "php/classes/admin/othersHandler.php";
require_once "php/classes/admin/adminHandler.php";
$othersHandler = new OthersHandler();
$status = "";
$msg = "";
$orderstatus = "";
$ordermsg = "";
$oStatuses  = [];

if(isset($_POST['add-category'])){
    $name = $_POST['new-name'];
    $q = $othersHandler->addCategory($name);
    if($q[0]){
        move_uploaded_file($image['tmp_name'], $imgName);    
        $status = "success";
        $msg = "Added a new Category";
    }else{
        $status = "danger";
        $msg = "Unable to add a new Category";
        $msg .= "<br>". $q[1];
    }
}

if(isset($_POST['delete-category'])){
    $categoryId = $_POST['delete-category'];
    $res = $othersHandler->removeFromTable('menu_categories', 'id', $categoryId);
    if($res){
        $status = "success";
        $msg = "Deleted one category";
    }
}
$taxStatus = "";
$taxText = "";
$adminHandler = new AdminHandler();
if(isset($_POST['taxSubmit'])){
    $tax = $_POST['tax'];
    $res = $adminHandler->updateTaxPercent($tax);
    if($res){
        $taxStatus = "success";
        $taxText = "Updated Tax Info";
    }else{
        $taxStatus = "danger";
        $taxText = "Failed to Update Tax Info";
    }
}

$_POST = array();

$categories = $othersHandler->getFromTable("menu_categories");
// $oStatuses = $othersHandler->getFromTable("orderStatus");
$taxPercent = $adminHandler->getTax();
echo $taxPercent;
require_once "header.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-5"><?php echo $title; ?></h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item active"><?php echo $title; ?></li>
            </ol>
            <div class="card mb-4">
                <div class="card-header">
                    <p>Change Tax Percentage</p>
                    <div class="card-body">
                        <form method="post">
                            <div style="display: flex;flex-direction: row;justify-content: left;">
                                <div class="form-group">
                                    <input type="number" step="0.01" name="tax" id="tax" value="<?= (!empty($taxPercent))? $taxPercent : 0; ?>" class="form-control" required placeholder="Enter Tax Percentage">
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="taxSubmit" id="tax" class="btn btn-primary">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    <p>Categories</p>
                </div>
                <div class="card-body" style="overflow-y: scroll;"> 
                <?php if(!empty($status)){ ?>
                    <div id="status">
                        <div class="alert alert-dismissible alert-<?php echo $status ?>">
                            <p><?php echo $msg ?></p>
                        </div>
                    </div>
                <?php } ?>
                    <table class="table table-bordered">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Delete</th>
                        </tr>
                        <?php
                            $i = 0;
                            foreach($categories as $category){
                                ?>
                                <tr  id="<?= "category-".$category['id']; ?>">
                                    <td><?= ++$i ?></td>
                                    <td><?= $category['name'] ?></td>
                                    <td>
                                        <form method="post">
                                            <button type="submit" value="<?= $category['id'] ?>" name="delete-category" class="btn btn-danger" <?= ($category['restaurantId'] == 'default')? "disabled" : "" ?>>Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                        ?>
                    </table>
                    <form method="post">
                        <table class="table table-bordered">
                            <tr>
                                <td><input type="text" class="form-control" id="new-name" name="new-name" required onkeyup="newCategory('name')" placeholder="Enter new Category"></td>
                                <td><button id="addCategory" class="btn btn-primary" name="add-category">Add</button></td>
                            </tr>
                        </table>
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
                $("#tableData").table2excel({
                    name: "<?php echo $title; ?>",
                    filename: "<?php echo $title; ?>.xls", // do include extension
                    preserveColors: false // set to true if you want background colors and font colors preserved
                });
            });
            $(function () {
            $('[data-toggle="tooltip"]').tooltip()
            });
            function updateCategory(id, field){
                $.post(
                    "php/api/update-category.php",
                    {
                        category_id: id,
                        field: field,
                        value: $(`#${field}-${id}`).val()
                    },
                    function(data){
                        // alert(`${data}`);
                        $(`#${field}-${id}`).css('border', function(){
                            if (data == 'success') {
                                return '1px solid #0f0';
                            }
                            else {
                                return '1px solid #f00';
                            }
                        
                        });
                    }
                );
            }
            function removeCategory(id){
                if(confirm("Are you sure you want to delete?")){
                    $.post(
                        "php/api/remove-category.php",
                        {
                            category_id: id
                        },
                        function(data){
                            if(data == "Deleted a category"){
                                $(`#category-${id}`).hide();
                            }
                        }
                    );
                }
            }

            function removeOrderStatus(id){
                if(confirm("Are you sure you want to delete?")){
                    $.post(
                        "php/api/remove-order-status.php",
                        {
                            status_id: id
                        },
                        function(data){
                            if(data == "Deleted a Order Status"){
                                $(`#order-status-${id}`).hide();
                            }
                        }
                    );
                
                }
            }
            
        </script>
    </body>
</html>