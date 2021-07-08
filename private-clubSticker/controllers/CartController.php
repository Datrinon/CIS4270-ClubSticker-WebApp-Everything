<?php

/**
 * Controller that handles shopping cart functions of the Guitar Shop
 *  application.
 *
 * @author jam
 * @version 180429
 */
class CartController extends DefaultController {

    protected $model = null;

    public function __construct() {
        parent::__construct();
    }

    public function add() {
        Page::$title = 'My Guitar Shop - Cart';
        require(APP_NON_WEB_BASE_DIR .'views/shoppingCart.php');
    }
}
