<?php
$title = "View Tables";
require_once "php/classes/admin/adminHandler.php";
require_once "php/classes/admin/tableHandler.php";
require_once "php/classes/admin/orderHandler.php";
// require_once "classes/posPrinterHandler.php";

$adminHandler = new AdminHandler();
$tableHandler = new TableHandler();
$orderHandler = new OrderHandler();
// $printerHandler = new PosPrinter();
$tableData = $tableHandler->getTables();
// print_r($tableData);
$orderStatuses = $orderHandler->getOrderStatus();
// print_r($tableData);
$tableNo = $tableData['table_info'][0]['tableNumber'];

if(isset($router->params[0])){
    $tableNo = $router->params[0];
}
if(isset($_POST['disable_table'])){
    $tableNo = $_POST['disable_table'];
    $tableHandler->disableTable($tableNo);
    $_POST = array();
}

if(isset($_POST['orderUpdate'])){
    $orderUpdate = $_POST['orderUpdate'];
    $orderId = $_POST['orderId'];
    if($orderUpdate == "decline_order"){
        $orderHandler->declineOrder($orderId, $tableNo);
    }else{  
        $orderHandler->acceptOrder($orderId, $tableNo);
    }
}

$printerData = $adminHandler->getPrinters();
require_once "header.php";

?>
<body style="background-color: #f1f1f1;" style="height:130vh;">
<style>
    .table-btn{
        text-decoration: none;
        color: #000;
    }
    .table-btn:hover{
        text-decoration: none;
        color:#000;
        cursor: pointer;
    }
    .disable-btn {
        float:right;
        /* margin-top:20px; */
    }
    .spec-head {
        margin-top:20px;
    }
    @media only screen and (max-device-width: 814px) {
        .disable-btn {
        float:left;
        margin-top:20px;
        margin-bottom:20px;
    }
    }
</style>
<div id="layoutSidenav_content">
    <div style="width:60%; padding:80px 20px 0px 20px;">
        <div class="flex-container" id="table-container">
    <?php
        foreach($tableData['table_info'] as $table){
            ?>
                <div>
                    <a href="/view-tables/<?php echo $table['tableNumber']; ?>" class="table-btn">
                        <span class="table-btn" onclick="myFunction()">
                        <?php
                            
                            if($table['tableStatus'] == "Available"){ 
                                $tableStatus = "table-empty"; 
                                $statusIcon = "<i class='flaticon-dining-room'></i>";
                            }else if($table['tableStatus'] == "Occupied"){ 
                                $tableStatus = "table-green";
                                $statusIcon = "<i class='flaticon-man icon-green'></i>";
                            } else if($table['tableStatus'] == "Reviewing"){ 
                                $tableStatus = "table-warning-border";
                                $statusIcon = "<i class='flaticon-review text-warning'></i>";
                            }else if($table['tableStatus'] == "Cooking"){
                                $tableStatus = "table-warning-border";
                                $statusIcon = "<i class='flaticon-soup text-warning'></i>";    
                            }else if($table['tableStatus'] == "Eating"){
                                $tableStatus = "table-green";
                                $statusIcon = "<i class='flaticon-eat-1 icon-green'></i>";    
                            }else if($table['tableStatus'] == 'Payment Successful'){
                                $tableStatus = "table-green";
                                $statusIcon = "<i class='flaticon-bank icon-green'></i>";
                            }else if($table['tableStatus'] == 'Declined'){
                                $tableStatus = "table-red";
                                $statusIcon = "<i class='flaticon-cancel icon-red'></i>";
                            }else if($table['tableStatus'] == 'Payment Pending'){
                                $tableStatus = "table-red";
                                $statusIcon = "<i class='flaticon-pending icon-red'></i>";
                            }else{
                                $tableStatus = "table-red";
                                $statusIcon = "<i class='flaticon-close icon-red'></i>";
                            }
                        ?>
                            <div class="table-box <?= $tableStatus ?>">
                                <?php 
                                echo $statusIcon;
                                ?>
                            </div> 
                            <div class="table-number" style="margin-bottom:20px;">
                                <h5><?php echo $table['tableNumber']; ?></h5>
                            </div>
                        </span>
                    </a>
                </div>
            <?php
        }
        ?>
        </div>
    </div>
    <div id="right-sidebar" style="height:100vh;">
        <div class="container text-white mt-4" id="table-info">
            <div>
                <h3 class="mb-20 headconfirm" style="text-align: center; margin-top:50px;margin-bottom:30px;">Table <?php echo $tableNo; ?></h3>
                <div class="btn-list">
                    <form method="post" style="display: flex;flex-direction: row;justify-content: space-between;">
                        <a href="/enable-table/<?php echo $tableNo; ?>" class="btn btn-warning" onclick="return confirm('Are you sure?\nClick ok to clear the table')">Clear/Enable Table</a>
                        <button type="submit" name="disable_table" value="<?php echo $tableNo; ?>" class="btn btn-danger disable-btn" onclick="return confirm('Are you sure?\nClick ok to disable the table')">Disable this Table</button>
                    </form>
                </div>
                <br>
                <div id="order-info">
                    <center><img src="https://www.menuelsharkia.com/static/images/loading-foods.gif" width="100" height="100"alt="Loading..."></center>
                </div>
            </div>
        </div>
    </div>
</div>

</body>


<audio id="alert" allow="autoplay" style="display: none;">
    <source src="public/admin/assets/sounds/alert.mp3" type="audio/mpeg">
</audio>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="/js/admin/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<!-- <script src="assets/demo/chart-area-demo.js"></script> -->
<!-- <script src="assets/demo/chart-bar-demo.js"></script> -->
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<script src="public/admin/assets/demo/datatables-demo.js"></script>
<script type='text/javascript' src='/js/admin/StarWebPrintBuilder.js'></script>
<script type='text/javascript' src='/js/admin/StarWebPrintTrader.js'></script>
<script>

    var tableInfo;
    var rawData;
    setInterval(function(){
        $.get("/get-table-info", function(data){
            
            if(rawData != data){
                if(rawData != null){
                    document.querySelector("audio").play();    
                }
                rawData = data;
                tableInfo = JSON.parse(data);
                getCurrentTableUpdates();
                $("#table-container").html("");
                $.each(tableInfo['table_info'], function(key, table){
                    var tableStatus;
                    var statusIcon;
                    if(table['tableStatus'] == "Available"){ 
                        tableStatus = "table-empty"; 
                        statusIcon = "<i class='flaticon-dining-room'></i>";
                    }else if(table['tableStatus'] == "Occupied"){ 
                        tableStatus = "table-green";
                        statusIcon = "<i class='flaticon-man icon-green'></i>";
                    } else if(table['tableStatus'] == "Order Placed" || table['tableStatus'] == "Reviewing"){ 
                        tableStatus = "table-warning-border";
                        statusIcon = "<i class='flaticon-review text-warning'></i>";
                    }else if(table['tableStatus'] == "Cooking"){
                        tableStatus = "table-warning-border";
                        statusIcon = "<i class='flaticon-soup text-warning'></i>";    
                    }else if(table['tableStatus'] == "Eating"){
                        tableStatus = "table-green";
                        statusIcon = "<i class='flaticon-eat-1 icon-green'></i>";    
                    }else if(table['tableStatus'] == 'Payment Successful'){
                        tableStatus = "table-green";
                        statusIcon = "<i class='flaticon-bank icon-green'></i>";
                    }else if(table['tableStatus'] == 'Declined'){
                        tableStatus = "table-red";
                        statusIcon = "<i class='flaticon-cancel icon-red'></i>";
                    }else if(table['tableStatus'] == 'Payment Pending'){
                        tableStatus = "table-red";
                        statusIcon = "<i class='flaticon-pending icon-red'></i>";
                    }else{
                        tableStatus = "table-red";
                        statusIcon = "<i class='flaticon-close icon-red'></i>";
                    }
                    $("#table-container").append(`<div>
                <a href="/view-tables/${table['tableNumber']}" class="table-btn">
                    <span class="table-btn">
                        <div class="table-box ${tableStatus}">
                             ${statusIcon}
                        </div> 
                        <div class="table-number">
                            <h5>${table['tableNumber']}</h5>
                        </div>
                    </span>
                </a>
            </div>`);
                });
            }
        });
    }, 1000);

    var sendData;
    function getCurrentTableUpdates(){
        $.get("/get-current-table/<?php echo $tableNo ?>", function(data){
            console.log(data);
            var tableData = JSON.parse(data);
            sendData = tableData;
            $("#order-info").html("");
            $("#order-info").append(`<div class="heading">
                    <h5 class="spec-head">Current Status: ${tableData['tableStatus']}</h5>
                </div>
                <div class="progress-table-wrap">
                    <form action="">
                        <div class="form-group">
                            <label for="update-order-status">Update Order Status:</label>
                            
                            <select name="update-order-status" id="update-order-status" class="form-control" onchange="updateOrderStatus()">
                                <option>Update Order Status</option>
                                <?php  
                                
                                    foreach($orderStatuses as $orderStatus){
                                        ?>
                                <option value="<?php echo $orderStatus['name'] ?>"><?php echo $orderStatus['name'] ?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </form>`);
                        var i = tableData['orderData'].length;
                $("#order-info").append(`
                <div style="display: flex;flex-direction: row;justify-content: space-between;">
                    <div class="form-group">
                    <select id="completePrintInfo" class="form-control" style="flex: 1">
                        <option>Select a Printer</option>
                        <?php foreach ($printerData as $key => $printer) {
                            ?>
                            <option value='<?php echo json_encode($printer); ?>'><?php echo $printer['printerName'] ?></option>
                            <?php
                        } ?>
                        
                    </select> 
                    </div>
                    <div class="form-group">
                    <button id="print-complete" onclick="onSendMessageApi('all','')" class="btn btn-primary">Print Complete Receipt</button>
                    </div>
                </div>
                    `);
            $.each(tableData['orderData'], function(key, value) {
                if(value['order_info']['orderStatus'] == "Reviewing"){
                    // $("#order_info").append("123456");
                    // alert("Hello")
                    $("#order-info").append(`
                        <div class="jumbotron" style="display: flex; flex-direction: column; justify-content: space-between;">
                            <p class="text-dark text-center">New Incomming Order:</p>
                            <div style="display: flex;flex-direction: row;justify-content: space-between;">
                                <form method="post">
                                    <input type="hidden" name="orderId" value="${value['order_info']['orderId']}">
                                    <button class="btn btn-success" name="orderUpdate" value="accept_order">Accept Order</button>
                                    <button class="btn btn-danger" name="orderUpdate" value="decline_order">Decline Order</button>
                                </form>
                            </div>
                        </div>
                    `);
                }
                // console.log(value);
                $("#order-info").append(`<h6>Order #${i--}</h6>`);
                if(value['order_info']['note'] != ""){
                    $("#order-info").append(`
                        <div class="alert alert-warning" role="alert">
                            ${value['order_info']['note']}
                        </div>
                    `);
                }
                if(value['order_info']['paymentStatus'] == 'pending'){
                    $("#order-info").append(`
                        <div class="alert alert-warning" role="alert">
                            Payment Pending!
                        </div>
                    `);
                }else if(value['order_info']['paymentStatus'] == 'succeeded'){
                    $("#order-info").append(`
                    <div class="alert alert-success" role="alert">
                            Payment Successful!
                        </div>
                    `);
                }
                $("#order-info").append(`
                    <table class="progress-table">
                        <tr class="table-head text-white">
                            <th class="serial" style="width: 30px">#</th>
                            <th class="country" style="width: 250px">Items</th>
                            
                            <th class="price" style="font-weight: 500;">Price</th>
                        </tr>
                        `);
                var x = 0;
                $.each(value['order_items'], function(key2, orderItem){
                    $("#order-info").append(`
                        <tr class="table-row text-white">
                            <td class="serial" style="width: 30px">${++x}</td>
                            <td class="country" style="width: 250px">${orderItem['name']}</td>
                            <td class="price">$${orderItem['itemPrice']} x ${orderItem['itemCount']} = $${orderItem['totalPrice']}/-</td>
                        </tr>`);
                        
                });
                $("#order-info").append(`
                    <tr class="table-row text-white">
                        <td class="serial" style="width: 30px"></td>
                        <td class="country" style="width: 250px">Tax</td>
                        <td class="price">$${Math.round((value['order_info']['totalPriceWithTax'] - value['order_info']['totalPrice']) * 100) / 100}/-</td>
                    </tr>
                    <tr class="table-row text-white">
                        <td class="serial" style="width: 30px"></td>
                        <td class="country" style="width: 250px">TotalPrice</td>
                        <td class="price">$${value['order_info']['totalPriceWithTax']}/-</td>
                    </tr>`);
                $("#order-info").append(`</table></div><hr></div>`);
                $("#order-info").append(`
                <div style="display: flex;flex-direction: row;justify-content: space-between;">
                    <div class="form-group">
                    <select id="printerInfo-${key}" class="form-control">
                    <option>SELECT A PRINTER</option>
                    <?php foreach ($printerData as $key => $printer) {
                        ?>
                        <option value='<?php echo json_encode($printer); ?>'><?php echo $printer['printerName'] ?></option>
                        <?php
                    } ?>
                        
                    </select>  
                    </div>
                    <div class="form-group mb-2">      
                    <button id="print-receipt" class="btn btn-primary" onclick="onSendMessageApi('single', ${key})">Print Order #${i+1} Receipt</button>
                    </div><br><br><br><br><br><br>
                    `);
            });
        });
    }

    function updateOrderStatus(){
        $.post(
            '/update-order-status',
            {
                tableStatus: $("#update-order-status").val(),
                tableNo: "<?php echo $tableNo ?>",
            },
            function(data,status){
                if(data == "success"){
                    alert("Updated the order Status");
                }else{
                    alert("Error updating order Status "+ data);
                }
            }
        );
    }

    function sendMessageApi(request, printType, key) {
        // showNowPrinting();
        var printerInfo;
        if(printType != "single"){
            printerInfo = document.querySelector('#completePrintInfo');
        }else{
            printerInfo = document.querySelector(`#printerInfo-${key}`);
        }
        var printerJson = JSON.parse(printerInfo.value);
        console.log(printerJson);
        // var url = document.getElementById('url').value;

        var trader = new StarWebPrintTrader({url: `https://${printerJson['ipAddress']}/StarWebPRNT/SendMessage`});

        trader.onReceive = function (response) {
            // hideNowPrinting();

            var msg = '- onReceive -\n\n';

            msg += 'TraderSuccess : [ ' + response.traderSuccess + ' ]\n';

    //      msg += 'TraderCode : [ ' + response.traderCode + ' ]\n';

            msg += 'TraderStatus : [ ' + response.traderStatus + ',\n';

            if (trader.isCoverOpen            ({traderStatus:response.traderStatus})) {msg += '\tCoverOpen,\n';}
            if (trader.isOffLine              ({traderStatus:response.traderStatus})) {msg += '\tOffLine,\n';}
            if (trader.isCompulsionSwitchClose({traderStatus:response.traderStatus})) {msg += '\tCompulsionSwitchClose,\n';}
            if (trader.isEtbCommandExecute    ({traderStatus:response.traderStatus})) {msg += '\tEtbCommandExecute,\n';}
            if (trader.isHighTemperatureStop  ({traderStatus:response.traderStatus})) {msg += '\tHighTemperatureStop,\n';}
            if (trader.isNonRecoverableError  ({traderStatus:response.traderStatus})) {msg += '\tNonRecoverableError,\n';}
            if (trader.isAutoCutterError      ({traderStatus:response.traderStatus})) {msg += '\tAutoCutterError,\n';}
            if (trader.isBlackMarkError       ({traderStatus:response.traderStatus})) {msg += '\tBlackMarkError,\n';}
            if (trader.isPaperEnd             ({traderStatus:response.traderStatus})) {msg += '\tPaperEnd,\n';}
            if (trader.isPaperNearEnd         ({traderStatus:response.traderStatus})) {msg += '\tPaperNearEnd,\n';}

            msg += '\tEtbCounter = ' + trader.extractionEtbCounter({traderStatus:response.traderStatus}).toString() + ' ]\n';

    //      msg += 'Status : [ ' + response.status + ' ]\n';
    //
    //      msg += 'ResponseText : [ ' + response.responseText + ' ]\n';

            alert(msg);
        }

        trader.onError = function (response) {
            // hideNowPrinting();

            var msg = '- onError -\n\n';

            msg += '\tStatus:' + response.status + '\n';

            msg += '\tResponseText:' + response.responseText;

            alert(msg);
        }

        trader.sendMessage({request:request});
    }

    function onSendMessageApi(printType, orderNumber) {
        var builder = new StarWebPrintBuilder();

        var request = '';
        var printer;
        if(printType != "single"){
            printer = document.querySelector('#completePrintInfo');
        }else{
            printer = document.querySelector(`#printerInfo-${orderNumber}`);
        }
        var printerInfo = JSON.parse(printer.value);
        var printerSize = printerInfo['printerSize'];
        console.log(`Printer Size: ${printerSize}`);
        request += builder.createInitializationElement();

        // request += builder.createTextElement({characterspace:2});

        request += builder.createAlignmentElement({position:'center'});
        // request += builder.createLogoElement({number:1});
        request += builder.createTextElement({emphasis: true,data:'SIRI INDIAN RESTAURANT\n'});
        request += builder.createTextElement({emphasis: false,data:'T520 W TAYLOR ST\n'});
        request += builder.createTextElement({data:'CHICAGO, IL 60607\n'});
        request += builder.createTextElement({data:'3127667474\n'});
        request += builder.createTextElement({data:'https://www.sirichicago.com\n'});

        request += builder.createTextElement({data:'\n'});
        request += builder.createAlignmentElement({position:'left'});
        
        if(printType == "single"){
            
            if(orderNumber == undefined){
                alert("Unknown Request");
                return;
            }else{
                mainData = sendData['orderData'][orderNumber];
                orderInfo = mainData['order_info'];
                orderItems = mainData['order_items'];
                console.log(orderItems);
                request += builder.createAlignmentElement({position: 'center'});
                request += builder.createTextElement({emphasis: true, data: `ORDER ${parseInt(orderNumber) + 1}\n`});
                request += builder.createTextElement({data: `Dine-In\n`});
                request += builder.createTextElement({emphasis: false, data: `bisi\n`});
                request += builder.createAlignmentElement({position: 'left'});
                var currentDate = new Date();

                request += builder.createTextElement({data: 'Cashier: P R\n'});
                request += builder.createTextElement({data: `${currentDate.toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: '2-digit'})} \n`})
                orderItems.forEach(function(val, key){
                    request += builder.createTextElement({data: textUdjester(`${val['itemCount']} ${val['name']}`, `$${val['itemPrice']}`, printerSize)});
                });
                request += builder.createTextElement({emphasis: true, data: textUdjester('Subtotal', `$${parseFloat(orderInfo['totalPrice']).toFixed(2)}`, printerSize)});
                request += builder.createTextElement({emphasis: false, data: textUdjester('Tax(10%)', `$${parseFloat(parseFloat(orderInfo['totalPriceWithTax']) - parseFloat(orderInfo['totalPrice'])).toFixed(2)}`, printerSize)});
                request += builder.createTextElement({emphasis: true, data: textUdjester('Total', `$${parseFloat(orderInfo['totalPriceWithTax']).toFixed(2)}`, printerSize)});
            }
            
        }else{
            var totalPrice = 0;
            var totalPriceWithTax = 0;
            sendData['orderData'].forEach(function(value, key) {
                currentOrderInfo = value['order_info'];
                currentOrderItems = value['order_items'];
                totalPrice += parseFloat(currentOrderInfo['totalPrice']);
                totalPriceWithTax += parseFloat(currentOrderInfo['totalPriceWithTax']);
                request += builder.createAlignmentElement({position:'center'});
                request += builder.createTextElement({emphasis: true, data: `Order #${sendData['orderData'].length - key}\n`});
                currentOrderItems.forEach(function(orderItems, key) {
                    request += builder.createTextElement({emphasis: true, data: textUdjester(`${orderItems['itemCount']} ${orderItems['name']}`,  `$${orderItems['itemPrice']}`, printerSize)})
                });
                request += builder.createTextElement({emphasis: true, data: textUdjester('Subtotal', `$${parseFloat(currentOrderInfo['totalPrice']).toFixed(2)}`, printerSize)});
                request += builder.createTextElement({emphasis: false, data: textUdjester('Tax(10%)', `$${(parseFloat(currentOrderInfo['totalPriceWithTax']).toFixed(2) - parseFloat(currentOrderInfo['totalPrice']).toFixed(2)).toFixed(2)}`, printerSize)});
                request += builder.createTextElement({emphasis: true, data: textUdjester('Total', `$${parseFloat(currentOrderInfo['totalPriceWithTax']).toFixed(2)}/-`, printerSize)});
            });
            request += builder.createTextElement({data: "\n\n"});
            
            request += builder.createTextElement({emphasis: true, data: textUdjester('Complete Subtotal', `$${parseFloat(totalPrice).toFixed(2)}`, printerSize)});
                request += builder.createTextElement({emphasis: false, data: textUdjester('Complete Tax(10%)', `$${(parseFloat(totalPriceWithTax).toFixed(2) - parseFloat(totalPrice).toFixed(2)).toFixed(2)}`, printerSize)});
                request += builder.createTextElement({emphasis: true, data: textUdjester('Complete Total', `$${parseFloat(totalPriceWithTax).toFixed(2)}/-`, printerSize)});
        }
        request += builder.createTextElement({data: "\n\n"});
        request += builder.createTextElement({data: "Thank you for your business\nPlease Visit Again!!"});
        request += builder.createCutPaperElement({feed:true});

        sendMessageApi(request, printType, orderNumber);
    }

    function textUdjester(leftText, rightText, printerSize){
        
        var leftCount = leftText.toString().length;
        var rightCount = rightText.toString().length;
        var totalSpaceLength = 48;
        switch(printerSize){
            case 'inch2':
                totalSpaceLength = 27;
                break;
            case 'inch3':
                totalSpaceLength = 42;
                break;
            case 'inch4':
                totalSpaceLength = 64;
                break;
            default:
                totalSpaceLength = 48;
                break;
        }
        var centerSpaceCount = totalSpaceLength - leftCount - rightCount;
        var outputString = "";
        if(centerSpaceCount <= 0){
            outputString = `${spaces(3, leftText.substring(0, (leftCount - (centerSpaceCount * -1) - 3)))}${rightText}\n  -${leftText.slice(leftCount - (centerSpaceCount * -1) - 3)}\n`;
        }else{
            outputString = `${spaces(centerSpaceCount, leftText)}${rightText}\n`;
        }
        return outputString;
    }

    function spaces(x, text){
        var res = text;
        while(x--) res += " ";
        return res;
    }
    
</script>
</body> 
</html>