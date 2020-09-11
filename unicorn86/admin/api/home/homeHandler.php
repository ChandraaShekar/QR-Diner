<?php

require_once "../classes/api.php";

class HomeHandler extends Api {

    private $category;

    public function __construct(){
        parent::__construct('');
    }

    public function getHomeData(){
        try{
            $data = [];
            $q = $this->db->prepare("SELECT `image` FROM banners");
            $q->execute();
            $banner_images = [];
            while($image = $q->fetch(PDO::FETCH_ASSOC)){
                $banner_images[] = $image;
            }
            $q2 = $this->db->prepare("SELECT `name`, `image` FROM meta_categories");
            $q2->execute();
            $categories = [];
            while($category = $q2->fetch(PDO::FETCH_ASSOC)){
                $categories[] = $category;
            }
            $q3 = $this->db->prepare("SELECT meta_products.product_code, meta_products.product_name, meta_products.price, meta_products.sale_price, meta_categories.name AS category, product_meta_images.image FROM meta_products 
            LEFT JOIN meta_categories ON meta_categories.id = meta_products.category
            LEFT JOIN product_meta_images ON product_meta_images.product_code = meta_products.product_code 
            WHERE meta_products.product_code IN (SELECT product_code FROM daily_deals)
            GROUP BY meta_products.product_code");
            $q3->execute();
            $daily_deals = [];
            while($deal = $q3->fetch(PDO::FETCH_ASSOC)){
                $daily_deals[] = $deal;
            }
            $q4 = $this->db->prepare("SELECT meta_products.product_code, meta_products.product_name, meta_products.price, meta_products.sale_price, meta_categories.name AS category, product_meta_images.image FROM meta_products 
            LEFT JOIN meta_categories ON meta_categories.id = meta_products.category
            LEFT JOIN product_meta_images ON product_meta_images.product_code = meta_products.product_code 
            WHERE meta_products.product_code IN (SELECT product_code FROM daily_deals)
            GROUP BY meta_products.product_code");
            $q4->execute();
            $featured = [];
            while($item = $q4->fetch(PDO::FETCH_ASSOC)){
                $featured[] = $item;
            }
            $data = [
                "banners" => $banner_images,
                "categories" => $categories,
                "daily_deals" => $daily_deals,
                "featured" => $featured
            ];
            $this->returnResponse(SUCCESS_RESPONSE, $data);
        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }

    public function getCategoryItems(){
        $this->category = $this->validateParameter('Category', $this->param['category'], STRING);
        try{
            $q = $this->db->prepare("SELECT meta_products.product_code, meta_products.product_name, meta_products.price, meta_products.sale_price, meta_categories.name AS category, product_meta_images.image FROM meta_products 
            LEFT JOIN meta_categories ON meta_categories.id = meta_products.category
            LEFT JOIN product_meta_images ON product_meta_images.product_code = meta_products.product_code 
            WHERE meta_categories.name = :category GROUP BY meta_products.product_code");
            $q->bindParam(':category', $this->category);
            $q->execute();
            $items = [];
            while($item = $q->fetch(PDO::FETCH_ASSOC)){
                $items[] = $item;
            }
            if(count($items) == 0){
                $this->throwError(NO_ITEMS, "Invalid Category");
            }
            $this->returnResponse(SUCCESS_RESPONSE, $items);
        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }
}