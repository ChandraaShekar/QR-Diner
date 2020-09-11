<?php

$title = "Dashboard";
// require_once "auth.php";
// echo "<pre>";
require_once "php/classes/admin/orderHandler.php";
$orderHandler = new OrderHandler();
// print_r($orderHandler->getGraphData()['today_menu_performance']);
$graphData = $orderHandler->getGraphData();

// die();
require_once "header.php";


?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
                        <br><br>
                        <h1 class="mt-5"><?php echo $title; ?></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><?php echo $title; ?></li>
                        </ol>
                        <br/>
                    </div>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-danger text-white mb-4">
                                    <div class="card-body">Unattended Orders</div>
                                    <div class="card-body"><h3 id="new_orders"></h3></div>
                                    
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card text-white mb-4" style="background-color: #6f42c1;">
                                    <div class="card-body">Total Orders Today</div>
                                    <div class="card-body"><h3 id="total_orders"></h3></div>
                                    
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-success text-white mb-4">
                                    <div class="card-body">Total Visits Today</div>
                                    <div class="card-body"><h3 id="total_users"></h3></div>
                                    
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-warning text-dark mb-4">
                                    <div class="card-body">Total Tables Occupied</div>
                                    <div class="card-body"><h3 id="total_restaurants"></h3></div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header"><i class="fas fa-chart-area mr-1"></i>Overall Menu Performance</div>
                                    <div class="card-body">
                            <div id="piechart"></div></div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header"><i class="fas fa-chart-bar mr-1"></i>Day to day Order Info</div>
                                    <div class="card-body"><div id="ordersChart"></div></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header"><i class="fas fa-chart-area mr-1"></i>Revenue From past 30 days</div>
                                    <div class="card-body">
                            <div id="30dayrevenue"></div></div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header"><i class="fas fa-chart-bar mr-1"></i>Today Menu Performance</div>
                                    <div class="card-body"><div id="dailyMenuData"></div></div>
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
        <audio id="alert" allow="autoplay" style="display: none;">
            <source src="/public/admin/assets/sounds/alert.mp3" type="audio/mpeg">
        </audio>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="/js/admin/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <!-- <script src="assets/demo/chart-area-demo.js"></script> -->
        <!-- <script src="assets/demo/chart-bar-demo.js"></script> -->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        <script src="/public/admin/assets/demo/datatables-demo.js"></script>
        <script>
            // $.ajaxSetup({ cache: true });
            var mydata;
            setInterval(function(){
                // $.ajaxSetup({ cache: false });
                $.get(
                "/home-data", 
                function(data){
                    // console.log(data);
                    var x = JSON.parse(data);
                    if(mydata != null){
                        if(mydata != data){
                            document.querySelector("audio").play();
                        }
                    }
                    mydata = data;
                    $("#new_orders").html(x.home_data.new_order_count);
                    $("#total_orders").html(x.home_data.order_count);
                    $("#total_users").html(x.home_data.user_count);
                    $("#total_restaurants").html(x.home_data.table_count);
                    var table_body = $("#table_body");
                    table_body.html("");
                    // for(var y = 0; y <= x.new_orders_list.length-1; y++){
                    //     var status = (x.new_orders_list[y].seenStatus == 'false')? "table-light" : "";
                    //     table_body.append(`<tr class="${status}">
                    //     <td>${y + 1}</td>
                    //     <td><a href="order-info.php?orderId=${x.new_orders_list[y].orderId }">${x.new_orders_list[y].orderId }</a></td>
                    //     <td>${x.new_orders_list[y].name }</td>
                    //     <td>${(x.new_orders_list[y].note == "")? "None" : x.new_orders_list[y].note }</td>
                    //     <td>$${ Math.round(x.new_orders_list[y].totalAmount * 1.1 * 100) / 100 }/-</td>
                    //     <td>${x.new_orders_list[y].orderStatus }</td>
                    //     <td>${x.new_orders_list[y].time }</td>
                    //     <td><a href="order-info.php?order_id=${x.new_orders_list[y].orderId }" class = 'btn btn-primary'>View</a></td>
                    //     </tr>`);
                    // }
                }
            )}, 3000);
        </script>
        <script type="text/javascript">
            // Load google charts
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            // Draw the chart and set the chart values
            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                ['Item', 'Sales'],
                    <?php foreach ($graphData['menu_performance'] as $key => $value) {
                    ?>    
                        ['<?= $value['name'] ?>', <?= $value['itemCount'] ?>],
                    <?php } ?>
                ]);

                // Optional; add a title and set the width and height of the chart
                var options = {'title':'Overall Menu Performance', 'width':550, 'height':400};

                // Display the chart inside the <div> element with id="piechart"
                var chart = new google.visualization.PieChart(document.getElementById('piechart'));
                chart.draw(data, options);

                var orderdata = google.visualization.arrayToDataTable([
                ['Date', 'orderCount'],
                    <?php foreach ($graphData['restaurant_daily_orders'] as $key => $value) {
                    ?>    
                        ['<?= $value['mydate'] ?>', <?= $value['orderCount'] ?>],
                    <?php } ?>
                ]);

                // Optional; add a title and set the width and height of the chart
                var orderChartOptions = {'title':'Day to day Order Info', 'width':650, 'height':400};

                // Display the chart inside the <div> element with id="piechart"
                var chart = new google.visualization.BarChart(document.getElementById('ordersChart'));
                chart.draw(orderdata, orderChartOptions);
                

                var revenueData = google.visualization.arrayToDataTable([
                ['Date', 'orderCount'],
                    <?php foreach ($graphData['daily_revenue'] as $key => $value) {
                    ?>
                        [<?= $key+1 ?>, <?= $value['totalPriceWithTax'] ?>],
                    <?php } ?>
                ]);

                // Optional; add a title and set the width and height of the chart
                var orderChartOptions = {'title':'Revenue Since last 30 days', 'width':650, 'height':400};

                // Display the chart inside the <div> element with id="piechart"
                var chart = new google.visualization.LineChart(document.getElementById('30dayrevenue'));
                chart.draw(revenueData, orderChartOptions);
                

                var dailyMenuData = google.visualization.arrayToDataTable([
                ['Date', 'orderCount'],
                    <?php foreach ($graphData['today_menu_performance'] as $key => $value) {
                    ?>
                        ['<?= $value['name'] ?>', <?= $value['menuCount'] ?>],
                    <?php } ?>
                ]);

                // Optional; add a title and set the width and height of the chart
                var orderChartOptions = {'title':'Today Menu Performance', 'width':650, 'height':400};

                // Display the chart inside the <div> element with id="piechart"
                var chart = new google.visualization.BarChart(document.getElementById('dailyMenuData'));
                chart.draw(dailyMenuData, orderChartOptions);
            }
        </script>
    </body>
</html>
