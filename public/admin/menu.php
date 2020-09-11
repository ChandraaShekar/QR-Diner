<?php
$access_to = 5;
$title = "Manage Menu";
require_once "php/classes/admin/itemHandler.php";
require_once "header.php";

$itemsHandler = new ItemHandler();
$itemList = $itemsHandler->getItemList();

?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
                        <h1 class="mt-5"><?php echo $title; ?>
                        <?php if($_SESSION['admin_user']['accessType'] == 1 || $_SESSION['admin_user']['accessType'] == 3){ ?>
                        <a style="float:right" href="/add-item" class="btn btn-primary">New Item</a>
                        <?php } ?>
                        </h1>
                        
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active"><?php echo $title; ?></li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-table mr-1"></i><?php echo $title; ?>
                    <button class="btn btn-warning" id="downloadBtn"><i class="fas fa-download mr-1">
                    </i> Download Excel</button></div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>price</th>
                                                <th>Offer Price</th>
                                                <th>Category</th>
                                                <th>Status</th>
                                                <th class="exclude">View</th>
                                            </tr>
                                        </thead>
                                        <tfoot class="exclude">
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>price</th>
                                                <th>Offer Price</th>
                                                <th>Category</th>
                                                <th>Status</th>
                                                <th class="exclude">View</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                                foreach($itemList as $item){
                                                    ?>
                                                <tr>
                                                    <td><?= $i++?></td>
                                                    <td><?= $item['name']?></td>
                                                    <td><?= $item['price']?></td>
                                                    <td><?= !empty($item['offerPrice'])? $item['offerPrice']: "None" ?></td>
                                                    <td><?= $item['categoryName']?></td>
                                                    <td><?= $item['status']?></td>
                                                    <td><a href="item-info/<?php echo $item['id']?>" class="btn btn-primary">View</a></td>
                                                </tr>

                                                    <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>    
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2019</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
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
                    exclude: ".exclude",
                    name: "<?php echo $title; ?>",
                    filename: "<?php echo $title; ?>.xls", // do include extension
                    preserveColors: false // set to true if you want background colors and font colors preserved
                });
            });
        </script>
    </body>
</html>
