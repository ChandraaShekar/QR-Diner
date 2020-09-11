<?php

require_once "../classes/api.php";

class ProductHandler extends Api {

    private $barcode;
    private $searchTag;


    public function __construct(){
        parent::__construct('');
    }

    public function productSearch(){
        $this->searchTag = $this->validateParameter('Search Tag', $this->param['q'], STRING);
        try{
            $q = $this->db->prepare("SELECT meta_products.product_code, meta_products.product_name, meta_products.price, meta_products.sale_price, meta_categories.name AS category, product_meta_images.image FROM meta_products 
            LEFT JOIN meta_categories ON meta_categories.id = meta_products.category
            LEFT JOIN product_meta_images ON product_meta_images.product_code = meta_products.product_code 
            WHERE MATCH(`meta_products`.`product_name`) AGAINST(:search) OR `meta_products`.`product_name` LIKE :search GROUP BY meta_products.product_code");
            $q->bindParam(':search', $this->searchTag);
            $q->execute();
            $items = [];
            while($item = $q->fetch(PDO::FETCH_ASSOC)){
                $items[] = $item;
            }
            if(count($items) == 0){
                $this->throwError(NO_ITEMS, "No items found for the search query");
            }
            $this->returnResponse(SUCCESS_RESPONSE, $items);
        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }

    public function barcodeSearch(){
        $this->barcode = $this->validateParameter("Barcode", $this->param['barcode'], STRING);
        try{
            $q = $this->db->prepare("SELECT meta_products.product_code, meta_products.product_name, meta_products.price, meta_categories.name AS category, product_meta_images.image FROM meta_products 
                LEFT JOIN meta_categories ON meta_categories.id = meta_products.category
                LEFT JOIN product_meta_images ON product_meta_images.product_code = meta_products.product_code 
                WHERE meta_products.barcode_info = :barcode GROUP BY meta_products.product_code");
            $q->bindParam(':barcode', $this->barcode);
            $q->execute();
            $items = [];
            while($item = $q->fetch(PDO::FETCH_ASSOC)){
                $items[] = $item;
            }
            if(count($items) == 0){
                $this->throwError(NO_ITEMS, "No Items with this barcode");
            }
            $this->returnResponse(SUCCESS_RESPONSE, $items);

        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }

    public function productInfo(){
        $this->product_code = $this->validateParameter('Product Code', $this->param['product_code'], STRING);
        try{
            $q = $this->db->prepare("SELECT meta_products.*, meta_categories.name AS category FROM meta_products 
            LEFT JOIN meta_categories ON meta_categories.id = meta_products.category
            WHERE meta_products.product_code = :product_code GROUP BY meta_products.product_code");
            $q->bindParam(':product_code', $this->product_code);
            $q->execute();
            $item = [];
            $data = [];
            $item = $q->fetch(PDO::FETCH_ASSOC);
            // print_r($item);
            if(empty($item)){
                $this->throwError(NO_ITEMS, 'Product Code Invalid');
            }
            $new_product_code = $item['product_code'];
            $q2 = $this->db->prepare("SELECT image FROM product_meta_images WHERE product_code = '$new_product_code'");
            $q2->execute();
            $images = [];
            while($image = $q2->fetch(PDO::FETCH_ASSOC)){
                $images[] = $image['image'];
            }
            $data['info'] = $item;
            $data['images'] = $images;
            $this->returnResponse(SUCCESS_RESPONSE, $data);
        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }
}