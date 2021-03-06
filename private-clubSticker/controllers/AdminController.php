<?php

/**
 * Controller that handles administrator functions of the Guitar Shop
 * application.
 * Added a simple user registration form.
 *
 * @author jam
 * @version 201015
 */
class AdminController extends DefaultController {

    protected $model = null;

    public function __construct() {
        parent::__construct();
    }

    public function loginGET() {
        Page::$title = 'My Guitar Shop - Admin Login';
        require(APP_NON_WEB_BASE_DIR . 'views/adminLogin.php');
    }
    
    public function registerGET() {
        $vm = null;
        Page::$title = 'My Guitar Shop - Register';
        require(APP_NON_WEB_BASE_DIR . 'views/register.php');
    }
    
    public function registerPOST() {
        $vm = RegisterVM::getInstance();
        if ($vm->registrationType === RegisterVM::VALID_REGISTRATION) {
            Page::$title = 'Valid Registration';
            require(APP_NON_WEB_BASE_DIR .'views/registrationSuccess.php');
        } else {
            Page::$title = 'Invalid Registration';
            require(APP_NON_WEB_BASE_DIR .'views/registrationErrors.php');
        }
    }
    
    public function listProducts() {
        $vm = ProductsVM::getCategoryInstance();
        Page::$title = 'Product Mgr - ' . $vm->category->name;
        require(APP_NON_WEB_BASE_DIR . 'views/adminProductList.php');
    }

    public function viewProduct() {
        $vm = ProductsVM::getProductInstance();
        Page::$title = 'Product Mgr - ' . $vm->product->name;
        require(APP_NON_WEB_BASE_DIR . 'views/adminProductView.php');
    }

    public function deleteProduct() {
        $vmDelete = ProductsVM::getDeleteInstance();
        $vm = ProductsVM::getCategoryInstance($vmDelete->category->id);
        Page::$title = 'Product Mgr - ' . $vm->category->name;
        require(APP_NON_WEB_BASE_DIR . 'views/adminProductList.php');
    }
    
    public function showAddProduct() {
        Page::$title = 'Product Mgr - Add Product';
        require(APP_NON_WEB_BASE_DIR . 'views/addProduct.php');
    }
    
    public function addEditProduct() {
        $vmAdd = ProductsVM::getAddEditInstance();
        $vm = ProductsVM::getCategoryInstance($vmAdd->category->id);
        Page::$title = 'Product Mgr - ' . $vm->category->name;
        require(APP_NON_WEB_BASE_DIR . 'views/adminProductList.php');
    }
    
    public function showEditProduct() {
        $vm = ProductsVM::getEditProductInstance();
        Page::$title = 'Product Mgr - Edit ' . $vm->product->name;
        require(APP_NON_WEB_BASE_DIR . 'views/editProduct.php');
    }
}
