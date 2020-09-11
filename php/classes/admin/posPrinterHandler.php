<?php


require __DIR__ . '/vendor/autoload.php';

require_once "Admin.php";
use Mike42\Escpos\Printer;
// use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
class PosPrinter extends Admin{
    public $printerName;
    public $orderList;
    public $orderId;
    public $singleOrder;
    public $userId;
    public $orderInfo = [];
    public $deviceName ="";
    public $ipAddress = "";
    public $toPrintData = [];
    public $printer;
    public function __construct(
        // $printerName, $singleOrder, $userId, $orderId, $deviceName="", $ipAddress=""
        ){
        // $this->printerName = $printerName;
        // $this->orderId = $orderId;
        // $this->singleOrder = $singleOrder;
        // $this->userId = $userId;
        // $this->deviceName = $deviceName;
        // $this->ipAddress = $ipAddress;
        parent::__construct();
    }

    public function instructor(){
        try{
            $this->getPrintData();
            if(!empty($this->deviceName)){
                $this->printFromUSB();
            }elseif(!empty($this->ipAddress)){
                $this->printFromNetwork();
            }else{
                return false;
            }
            if($this->startPrint()){
                return true;
            }else{
                return false;
            }
        }catch(Exception $e){
            die($e->getMessage());
        }
    }

    public function getPrintData(){
        try{
            if($this->singleOrder){
                if(!empty($this->orderId)){
                    $q = $this->db->prepare("SELECT * FROM order_info WHERE orderId = :orderId");
                    $q->bindParam(':orderId', $this->orderId);
                    $q->execute();
                    $orders = $q->fetch(PDO::FETCH_ASSOC);
                    $q1 = $this->db->prepare("SELECT * FROM order_items WHERE orderId = :orderId");
                    $q1->bindParam(':orderId', $this->orderId);
                    $q1->execute();
                    $this->orderInfo[] = ["order_details" => $orders, "order_items" => $q1->fetchAll(PDO::FETCH_ASSOC)];
                }else{
                    return false;
                }
            }else{
                if(!empty($this->userId)){
                    $q = $this->db->prepare("SELECT * FROM order_info WHERE userId = :userId");
                    $q->bindParam(':userId', $this->userId);
                    $q->execute();
                    $orders = $q->fetchAll(PDO::FETCH_ASSOC);
                    foreach($orders as $order){
                        $q1 = $this->db->prepare("SELECT * FROM order_items WHERE orderId = :orderId");
                        $q1->bindParam(':orderId', $order['orderId']);
                        $q1->execute();
                        $this->orderInfo[] = ['order_details' => $order, 'order_items' => $q1->fetchAll(PDO::FETCH_ASSOC)];
                    }
                }else{
                    return false;
                }
            }
        }catch(Exception $e){
            die($e->getMessage());
        }
    }

    public function printFromUSB(){
        $connector = new WindowsPrintConnector($this->deviceName);
        $this->printer = new Printer($connector);
    }

    public function printFromNetwork(){
        $connector = new NetworkPrintConnector($this->ipAddress, 9100);
        $this->printer = new Printer($connector);
    }

    public function startPrint(){
        $totalPrice = 0;
        $totalPriceWithTax = 0;
        foreach ($this->orderInfo as $key => $details) {
            $orderDetails = $details['order_items'];
            // $this->toPrintData[] = new Item("ORDER #" . $key + 1, "");
            // $this->printer->text();
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->setEmphasis(true);
            $this->printer->text("ORDER #" . $key + 1);
            $this->printer->text(new item('', '$'));
            $this->printer->setEmphasis(false);
            foreach ($details['order_items'] as $items) {
                // $this->toPrintData[] = new Item($items['itemName']. " x " . $items['itemCount'], $items['itemPrice']);
                $this->printer->text(new Item($items['itemName']. " x " . $items['itemCount'], $items['itemPrice']));
            }
            $this->printer->text(new item('Order Subtotal', $orderDetails['totalPrice']));
            $this->printer->text(new item('Order Tax', $orderDetails['totalPriceWithTax'] - $details['totalPrice']));
            $this->printer->setEmphasis(true);
            $this->printer->text(new item('Order Total', $orderDetails['totalPriceWithTax'], true));
            $this->printer->setEmphasis(false);
            $totalPrice += $orderDetails['totalPrice'];
            $totalPriceWithTax += $orderDetails['totalPriceWithTax'];
        }
        $this->printer->feed();
        $this->printer->feed();
        $this->printer->setEmphasis(true);
        $this->printer->text(new item('Subtotal', $totalPrice));
        $this->printer->text(new item('Tax', $totalPriceWithTax - $totalPrice));
        $this->printer->text(new item('Total', $totalPriceWithTax));
        $this->printer->setEmphasis(false);
        $this->printer -> cut();
        $this->printer -> pulse();
        $this->printer -> close();
    }

    public function getPrinters(){
        $res = [];
        try{
            $q = $this->db->prepare("SELECT * FROM printer_info");
            $q->execute();
            $res = $q->fetch(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            die("Exception: " . $e->getMessage);
        }
        return $res;
    }
}

/* Information for the receipt */
// $items = array(
//     new item("Example item #1", "4.00"),
//     new item("Another thing", "3.50"),
//     new item("Something else", "1.00"),
//     new item("A final item", "4.45"),
// );
// $subtotal = new item('Subtotal', '12.95');
// $tax = new item('A local tax', '1.30');
// $total = new item('Total', '14.25', true);
/* Date is kept the same for testing */
// $date = date('l jS \of F Y h:i:s A');
// $date = "Monday 6th of April 2015 02:56:25 PM";

/* Start the printer */
// $logo = EscposImage::load("resources/escpos-php.png", false);
// $printer = new Printer($connector);

/* Print top logo */
// $printer -> setJustification(Printer::JUSTIFY_CENTER);
// $printer -> graphics($logo);

/* Name of shop */
// $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
// $printer -> text("ExampleMart Ltd.\n");
// $printer -> selectPrintMode();
// $printer -> text("Shop No. 42.\n");
// $printer -> feed();

// /* Title of receipt */
// $printer -> setEmphasis(true);
// $printer -> text("SALES INVOICE\n");
// $printer -> setEmphasis(false);

// /* Items */
// $printer -> setJustification(Printer::JUSTIFY_LEFT);
// $printer -> setEmphasis(true);
// $printer -> text(new item('', '$'));
// $printer -> setEmphasis(false);
// foreach ($items as $item) {
//     $printer -> text($item);
// }
// $printer -> setEmphasis(true);
// $printer -> text($subtotal);
// $printer -> setEmphasis(false);
// $printer -> feed();

// /* Tax and total */
// $printer -> text($tax);
// $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
// $printer -> text($total);
// $printer -> selectPrintMode();

// /* Footer */
// $printer -> feed(2);
// $printer -> setJustification(Printer::JUSTIFY_CENTER);
// $printer -> text("Thank you for shopping at ExampleMart\n");
// $printer -> text("For trading hours, please visit example.com\n");
// $printer -> feed(2);
// $printer -> text($date . "\n");

// /* Cut the receipt and open the cash drawer */
// $printer -> cut();
// $printer -> pulse();

// $printer -> close();

/* A wrapper to do organise item names & prices into columns */
class item
{
    private $name;
    private $price;
    private $dollarSign;

    public function __construct($name = '', $price = '', $dollarSign = false)
    {
        $this -> name = $name;
        $this -> price = $price;
        $this -> dollarSign = $dollarSign;
    }

    public function __toString()
    {
        $rightCols = 10;
        $leftCols = 38;
        if ($this -> dollarSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this -> name, $leftCols) ;

        $sign = ($this -> dollarSign ? '$ ' : '');
        $right = str_pad($sign . $this -> price, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$right\n";
    }
}