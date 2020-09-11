<?php
$access_to = 3;
require_once "auth.php";
require_once "classes/userHandler.php";
$title = "Users List";
require_once "php/includes/header.php";
$userHandler = new UserHandler();
$users = $userHandler->getUsers();

?>
 
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
                        <h1 style="margin-bottom:50px;" class="mt-4"><?php echo $title ?>
                        </h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                            <li class="breadcrumb-item active"><?php echo $title; ?></li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-table mr-1"></i><?php echo $title; ?><button class="btn btn-warning" id="downloadBtn"><i class="fas fa-download mr-1">
                    </i> Download Excel</button></div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>View</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php 
                                            $i = 0;
                                            foreach($users as $row){
                                            ?>
                                            <tr>
                                                <td><?php echo ++$i ?></td>
                                                <td><?php echo $row['name'] ?></td>
                                                <td><?php echo $row['phone'] ?></td>
                                                <td><?php echo $row['email'] ?></td>
                                                <td>
                                                    <a href="user-info.php?uid=<?php echo $row['uid']; ?>" class="btn btn-primary" style="background-color: #fa8c9ac7;border:0px">More Info</a>
                                                </td>
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
        <script src="js/scripts.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/datatables-demo.js"></script>
    <script>
        $("#downloadBtn").click(function(){
            $("#tableData").table2excel({
                name: "<?php echo $title; ?>",
                filename: "<?php echo $title; ?>.xls", // do include extension
                preserveColors: false // set to true if you want background colors and font colors preserved
            });
        });
    </script>
    
    </body>
</html>
