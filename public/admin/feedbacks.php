<?php
$title = "Feedback";
require_once "php/classes/admin/userHandler.php";

$userHandler = new UserHandler();

require_once "header.php";

$feedbacks = $userHandler->getFeedbackList();
?>

<div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
                        <h1 style="margin-bottom:50px;" class="mt-5"><?php echo $title; ?></h1>
                        
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active"><?php echo $title; ?></li>
                        </ol>
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">Avg Restaurant Rating</div>
                                    <div class="card-body"><h3 id="new_orders"><?= (!empty($feedbacks['avgs'][0]['avgResRating'])) ? $feedbacks['avgs'][0]['avgResRating'] : "None" ?></h3></div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">Total Ratings</div>
                                    <div class="card-body"><h3 id="new_orders"><?=  !empty($feedbacks['avgs'][0]['totalrestaurantRating']) ? $feedbacks['avgs'][0]['totalrestaurantRating']: "None" ?></h3></div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-table mr-1"></i><?php echo $title; ?>
                                <button class="btn btn-warning" id="downloadBtn"><i class="fas fa-download mr-1">
                                </i> Download Excel</button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Rating</th>
                                                <th>Review</th>
                                                <th>time</th>
                                            </tr>
                                        </thead>
                                        <tfoot class="exclude">
                                            <tr>
                                                <th>#</th>
                                                <th>Rating</th>
                                                <th>Review</th>
                                                <th>time</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                                foreach($feedbacks['ratings'] as $feedback){
                                                    ?>
                                                <tr>
                                                    <td><?php echo $i++?></td>
                                                    <td><?php echo $feedback['restaurantRating']?></td>
                                                    <td><?php echo $feedback['restaurantReview']?></td>
                                                    <td><?php echo $feedback['time']?></td>
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
