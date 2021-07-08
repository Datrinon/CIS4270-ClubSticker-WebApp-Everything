<?php

/**
 * View model for the user. 
 * 
 * @author dan
 * @version 261120
 */
class UserVM {

    public $errorMsg;
    public $user;
    private $userDAM;
    private $userID;

    public function __construct() {
        $this->userDAM = new userDAM();
        $this->errorMsg = '';
        $this->user = '';
        $this->userId = hSession('userId');
    }

    // gets an instance of the user based on the ID they logged in with.
    // that ID is stored within
    public static function getUserInstance() {
        $vm = new self();
        
        $vm->user = $vm->userDAM->readUser($vm->userId);

        return $vm;
    }

}
