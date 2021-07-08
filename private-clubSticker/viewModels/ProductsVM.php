<?php

/**
 * View model for the featured products.
 *
 * @author jam
 * @version 180428
 */
class ProductsVM {

    public $featuredProductIds;
    public $errorMsg;
    public $products;
    public $product;
    public $category;
    private $productse;
    private $categoryDAM;

    public function __construct() {
        $this->productDAM = new ProductDAM();
        $this->categoryDAM = new CategoryDAM();
        $this->errorMsg = '';
        $this->featuredProductIds = array(1, 7, 9);
        $this->products = array();
        $this->product = '';
        $this->category = '';
    }

    // for club sticker's use in populating user pages with their own creations.
    public static function getUserProductInstance() {
        $vm = new self();
        foreach ($vm->productDAM->getUserProductIDs(hSession('userId')) as $productId) {
            $product = $vm->productDAM->readProduct($productId);
            $vm->products[] = $product;
        }
        return $vm;
    }

    // for club sticker's use in populating the home page with products.
    public static function getCatalogInstance() {
        $vm = new self();
        foreach ($vm->productDAM->getAllProductIDs() as $productId) {
            // read product from db
            $product = $vm->productDAM->readProduct($productId);
            // then add product to array
            $vm->products[] = $product;
        }
        return $vm;
    }

#region Guitar Shop Stuff
    public static function getFeaturedInstance() {
        $vm = new self();
        foreach ($vm->featuredProductIds as $productId) {
            $product = $vm->productDAM->readProduct($productId);
            // Add product to array
            $vm->products[] = $product;
        }
        return $vm;
    }

    public static function getCategoryInstance($deletedProductCategoryId = null) {
        $vm = new self();
        if ($deletedProductCategoryId === null) {
            $categoryId = hPOST('categoryId'); 
            if ($categoryId === null) {
                // can't stack them like below since it won't be a GET param for the outer method.
                // echo "within getCategory: " . hGET(intGET('categoryId'));
                $categoryId = hGET('categoryId');
            }
        } else {
            $categoryId = $deletedProductCategoryId;
        }

        // I need to add an inclusion in... but how do I get the set of values here?
        // note: i ended up creating a custom function to return all the categories.
        if (has_exclusion_from($categoryId, $vm->categoryDAM->getAllCategoryIDs())) {
            $categoryId = 1;
        }

        // if at this point, categoryID is still null, just assign it 1.
        if ($categoryId === null) {
            $categoryId = 1;
        }

        // input sanitization and validation complete. Send it into the DAM to do database operations.
        $vm->products = $vm->productDAM->readProductsByCategoryId($categoryId);
        $vm->category = $vm->categoryDAM->readCategory($categoryId);
        return $vm;
    }

    public static function getProductInstance() {
        $vm = new self();
        $productId = hGET('productId'); //filter_input(INPUT_GET, 'productId');

        if (has_exclusion_from($productId, $vm->productDAM->getAllProductIDs())) {
            $productId = 1;
        }

        $vm->product = $vm->productDAM->readProduct($productId);

        return $vm;
    }

    public static function getEditProductInstance() {
        $vm = new self();
         // can just leave it as intPOST -- it will return false if it's not.
        $productId = intPOST('productId');
        $vm->product = $vm->productDAM->readProduct($productId);
        return $vm;
    }

    public static function getDeleteInstance() {
        $vm = new self();
        $productId = hPOST('productId'); //filter_input(INPUT_POST, 'productId');
        $categoryId = hPOST('categoryId'); //filter_input(INPUT_POST, 'categoryId');
        if ($productId == null) {
            $productId = hGET('productId');   //filter_input(INPUT_GET, 'productId');
            $categoryId = hGET('categoryId'); //filter_input(INPUT_GET, 'categoryId');
        }
        $vm->productDAM->deleteProductById($productId);
        $vm->category = $vm->categoryDAM->readCategory($categoryId);
        return $vm;
    }

    public static function getAddEditInstance() {
        $vm = new self();
        $productId = filter_input(INPUT_POST,'productId');
        if ($productId === null) { // Supposed to be null -- the productID will be automatically assigned because it is a PK!
            $productId = '';
        }
        // aligns to the name of the variables sent in the edit form
        $varArray = array('id' => $productId,
                            'categoryId' => hPOST('categoryId'),
                            'productCode' => hPOST('code'),
                            'name' => hPOST('name'),
                            'listPrice' => hPOST('price'),
                            'discountPercent' => hPOST('discountPercent'),
                            'description' => hPOST('description'));
        /*
        $varArray = array('id' => $productId,
            'categoryId' => filter_input(INPUT_POST,'categoryId'),
            'productCode' => filter_input(INPUT_POST, 'code'),
            'name' => filter_input(INPUT_POST, 'name'),
            'listPrice' => filter_input(INPUT_POST,'price'),
            'discountPercent' => filter_input(INPUT_POST, 'discountPercent'),
            'description' => filter_input(INPUT_POST, 'description'));
        */
        $vm->product = new Product($varArray);
        $vm->category = $vm->categoryDAM->readCategory($vm->product->categoryId);
        $vm->productDAM->writeProduct($vm->product);
        return $vm;
    }
#endregion

}
