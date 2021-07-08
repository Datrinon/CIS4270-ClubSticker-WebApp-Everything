<?php

class UserController extends DefaultController {

    protected $model = null;
    
    public function __construct()
    {
        parent::__construct();
        // session_start(); // not good... place session_start() in the request broker since it processes the code there.
    }

    public function registerGET($vm = null)
    {
        if (session_status() == PHP_SESSION_NONE) {
            // echo "Calling from registerGET prior to start: " . var_dump($_SESSION);
            session_start();
            // echo "Calling from registerGET after start: " . var_dump($_SESSION);
        }
        Page::$title = 'Club Sticker - Register'; //add :title field in header
        require(APP_NON_WEB_BASE_DIR . 'views/register.php');
        // var_dump($_SESSION);
    }

    public function registerPOST()
    {
        // Suppose this is called to refresh the CSRF defense mechanism.
        // Doing so though wipes out the entire session.
        after_successful_logout();
        $vm = RegisterVM::getInstance();
        if ($vm->registrationType === RegisterVM::VALID_REGISTRATION) {
            Page::$title = 'Valid Registration';
            require(APP_NON_WEB_BASE_DIR . 'views/registrationSuccess.php');
        } else {
            $this->registerGET($vm);
        }
    }

    public function loginGET() {
        $vm = null;
        Page::$title = 'Club Sticker - Login';
        require(APP_NON_WEB_BASE_DIR . 'views/login.php');
    }

    public function loginPOST() {
        after_successful_logout();
        $vm = LoginVM::getInstance();
        if ($vm->userType === LoginVM::VALID_LOGIN) {
            $this->viewProfile();
        } else {
            if (session_status() == PHP_SESSION_NONE) { session_start(); }
            Page::$title = 'Invalid Login';
            require(APP_NON_WEB_BASE_DIR .'views/login.php');
        }
    }

    public function viewProfile($vm = null) {
        before_every_protected_page();
        Page::$title = "Club Sticker - My Profile";
        $productVM = ProductsVM::getUserProductInstance();
        $userVM = UserVM::getUserInstance();
        require(APP_NON_WEB_BASE_DIR .'views/viewProfile.php');
    }

    public function uploadArtworkGET() {
        before_every_protected_page();
        $vm = null; 
        $maxFileSize = UploadVM::MAX_FILE_SIZE; // reference class constant
        Page::$title = "Club Sticker - Upload Artwork";
        require(APP_NON_WEB_BASE_DIR .'views/uploadArtwork.php');
    }

    public function uploadArtworkPOST() {
        before_every_protected_page();
        $vm = UploadVM::getInstance();
        $maxFileSize = UploadVM::MAX_FILE_SIZE; // reference class constant
        require(APP_NON_WEB_BASE_DIR . 'views/uploadArtwork.php');
    }

    public function logout() {
        // take user to log out.
        after_successful_logout();
        Page:: $title = "Club Sticker Says Goodbye!";
        require(APP_NON_WEB_BASE_DIR .'views/logoutSuccess.php');
    }

    public function editProfileGET() {
        before_every_protected_page();
        $vm = null; 
        Page::$title = "Club Sticker - Edit Profile";
        require(APP_NON_WEB_BASE_DIR .'views/editProfile.php');
    }

    public function editProfilePOST() {
        before_every_protected_page();
        Page::$title = "Club Sticker - Edit Profile";
        $vm = EditProfileVM::getInstance();
        require(APP_NON_WEB_BASE_DIR .'views/editProfile.php');
    }

}
