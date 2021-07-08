<?php

/**
 * View model for user registration functions.
 *
 * @author jam
 * @version 201015
 */
class RegisterVM
{

    public $enteredPW;
    public $enteredConfPW;
    public $registrationType;
    public $errorMsg;
    public $statusMsg;
    public $newUser;

    // dht custom
    private $providedAttributes = array(
        'email', 'firstName', 'lastName',
        'phone', 'password', 'confirmPassword'
    );

    // User type constants used for switching in the controller.
    // Aside about constants: they are defined on a per-class basis (i.e. like static)
    // Thus, you have to reference the class when you use it in the controller.
    const VALID_REGISTRATION = 'valid_registration';
    const INVALID_REGISTRATION = 'invalid_registration';

    public function __construct()
    {
        $this->userDAM = new UserDAM();
        $this->errorMsg = '';
        $this->statusMsg = array();
        $this->enteredPW = '';
        $this->enteredConfPW = '';
        $this->registrationType = self::INVALID_REGISTRATION; //dht: must pass validation
        $this->newUser = null;
    }

    public static function getInstance()
    {

        $vm = new self();

        $varArray = array(
            'id' => strtolower(emailPOST('email')), // store emails in lowercase so users can have case-insensitive login.
            'lastName' => hPOST('lastName'),
            'firstName' => hPOST('firstName'),
            'phone' => hPOST('phone'),
            'description' => "No description provided.",
            'profilePicture' => "placeholder.jpg"        
        );
        $vm->newUser = new User($varArray);
        $vm->enteredPW = hPOST('password');
        $vm->enteredConfPW = hPOST('confirmPassword');
        if ($vm->validateUserInput()) {
            // hash the password
            $vm->newUser->password = password_hash($vm->enteredPW, PASSWORD_DEFAULT);
            // Write the user to the DB using the userDAM code
            $vm->userDAM->writeUser($vm->newUser);
            $vm->registrationType = self::VALID_REGISTRATION;
        }
        return $vm;
    }

    // not static b/c it belongs to getInstance.
    private function validateUserInput()
    {
        $success = true;

        // Validation 1: All inputs were given.
        foreach ($this->providedAttributes as $parameter) {
            if (empty(hPOST($parameter))) {
                // preg_split - splits a string by regex, returns an array. i.e. convert camelcase to an array of words
                // implode joins array elements in a string using a space
                // ucwords converts the first character of each word to uppercase.
                $this->errorMsg .= "<b> Required field: </b>" .
                    ucwords(implode(
                        ' ',
                        preg_split('/(?=[A-Z])/', $parameter) 
                        //regex Lookahead used here to leverage camelcase convention
                        //basically, whenever it finds an uppercase character
                        //it splits the string there.
                    ))
                    . "</br>";
                $success = false;
            }
        }


        // Validation 1b: For the names, they were of the right length and don't contain any sus symbols.
        if (($this->newUser->firstName != '' && $this->checkName($this->newUser->firstName, 20) == false) ||
            ($this->newUser->lastName != '' && $this->checkName($this->newUser->lastName, 20) == false)) {
            $success = false;
        } 


        // Validation 2: The email address was given in the correct format.
        // Note: filter_input does not accept returns from __get method for user. Only GET / POST.
        // debug $this->errorMsg .= "EMAIL TEST: " . emailPOST('email') . "</br>";
        if (!emailPOST('email')) {
            $this->errorMsg .= "<b> Error: </b> The email address was invalid. </br>";
            $success = false;
        }

        // Validation 2b: Email not above 100 characters.
        if (emailPOST('email') >= 100) {
            $this->errorMsg .= "<b> Error: </b> The email must be 100 characters or less. </br>";
            $success = false;
        }

        // Validation 2c: The email address given isn't already registered in the db.
        if ($this->userDAM->readUser(emailPOST('email')) != null) {
            $this->errorMsg .= "<b> Error: </b> The email address is already registered. </br>";
            $success = false;
        }

        

        // Validation 3: The phone number is formatted correctly. Using RegEx.
        if (!preg_match(
            '/\A\(\d{3}\)\s?\d{3}-\d{4}\Z/',
            $this->newUser->__get('phone')
        )) {
            $this->errorMsg .= "<b> Error: </b> The phone number was incorrectly formatted. </br>";
            $success = false;
        }

        // Validation 4 for the CSRF token is taken care of in the request broker.

        // Validation 5: Password confirmation match.
        if ($this->enteredPW !== $this->enteredConfPW) {
            $this->errorMsg .= "<b> Error: </b> Passwords do not match. </br>";
            $success = false;
        }

        // Validation 6: The user must have set a strong password.
        // * Has a symbol, has a number, digit, and character.
        // * Is at least 8 characters long.
        // * Uses an uppercase letter.
        // * Uses a symbol.
        if (!empty($this->enteredPW)) {
            if (!preg_match('/[A-Z]/', $this->enteredPW)) {
                $this->errorMsg .= "<b> Password error: </b> You must use an uppercase letter. </br>";
                $success = false;
            }
            if (!preg_match('/[0-9]/', $this->enteredPW)) {
                $this->errorMsg .= "<b> Password error: </b> You must use a number. </br>";
                $success = false;
            }
            if (!preg_match('/[^\w\s]|_/', $this->enteredPW)) {
                $this->errorMsg .= "<b> Password error: </b> You must use a special character. </br>";
                $success = false;
            }
            if (preg_match('/\A.{0,7}\Z/', $this->enteredPW)) {
                $this->errorMsg .= "<b> Password error: </b> The password must be 8 characters or longer. </br>";
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Checks that the name has letters and uses symbols properly.
     * @param name name of the user
     * @param allowedLength (optional) how long a name is allowed to be.
     * @return - True if the name is valid, false if the name is invalid.
     */
    private function checkName($name, $allowedLength = -1)
    {
        $success = true;

        if ($allowedLength > 0 && strlen($name) > $allowedLength) {
            $this->errorMsg .= "<b>Error</b>: Names can only be " . $allowedLength . " characters long.</br>";
            $success = false;
        }

        if (preg_match("~[^A-Z\-\'.]~i", $name)) {
            $this->errorMsg .= "<b>Error</b>: Names can only contain letters and/or the following symbols: - . ' </br>";
            $success = false;
        }
        if (!preg_match("~[A-Z]~i", $name)) {
            $this->errorMsg .= "<b>Error</b>: Names did not begin with a letter.</br>";
            $success = false;
        }
        if (preg_match("~\A[\-'.]{2,}\Z~", $name)) {
            $this->errorMsg .= "<b>Error</b>: Names cannot contain symbols in succession.</br>";
            $success = false;
        }

        return $success;
    }
}
