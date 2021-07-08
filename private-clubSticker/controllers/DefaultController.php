<?php

/**
 * Default controller class provides the run method to all subclasses in the
 * GuitarShop application.
 * The index method provides the default action of the application and can be
 * overridden in subclasses.
 *
 * @author JAM
 * @version 201012
 */
class DefaultController {

		// Similarly, in Keith Casey's, there was no __construct().
    public function __construct() {
        
    }

    public final function run($action = 'index', $vm = null) { // final function prevents it from being overrun by child classes.
    
        if (!method_exists($this, $action)) {
            $action = 'index';
        }

        // The value retuned to the caller is not used in this application.
        /**
         * e.g.
         * index.php case statement to UserController class
         * $action = registerPOST
         * $vm = null
         * $action($vm) = registerPOST()
         */
        return $this->$action($vm);
    }

    /**
     * Displays the default view for the application.
     */
    public function index($errorVM = null) {

        // Set page tab title
        Page::$title = 'Club Sticker';
        
        $vm = ProductsVM::getCatalogInstance();
        
        if(is_logged_in()) {
            $header = 'views/clubStickerUserHeader.php';
        } else {
            $header = 'views/clubStickerHeader.php';
        }
        // Go to the default view of the application.
        require(APP_NON_WEB_BASE_DIR . 'views/index-clubsticker.php');
    }

}
