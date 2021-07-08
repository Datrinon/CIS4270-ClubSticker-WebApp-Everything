<?php

/**
 * View model for user registration functions.
 *
 * @author jam
 * @version 201015
 */
class EditProfileVM
{
    const DESCRIPTION_CHAR_LIMIT = 250;
    const MAX_FILE_SIZE = 1048576 * 2; // 2MB file size limit 
    const UPLOAD_PATH = WEB_BASE_DIR . 'club-sticker-images/profilePictures';
    const DEFAULT_PROFILE_PICTURE = "placeholder.jpg";

    public $enteredPW;
    public $enteredConfPW;
    public $registrationType;
    public $errorMsg;
    public $statusMsg;
    public $existingUser;
    public $userChanges;

    // the user attributes that pertain to text fields. No password here.
    private $userAttributes = array(
        'firstName', 'lastName',
        'phone', 'description'
    );

    private $allowedMimeTypes = ['image/png', 'image/jpg', 'image/jpeg'];
    private $allowedExtensions = ['png', 'jpg', 'jpeg'];

    public function __construct()
    {
        $this->userDAM = new UserDAM();
        $this->errorMsg = array();
        $this->statusMsg = array();
        $this->enteredPW = '';
        $this->enteredConfPW = '';
        $this->existingUser = null;
        $this->userChanges = array();
    }

    public static function getInstance()
    {

        $vm = new self();

        // Get existing user. Will update later in if statement below.
        $vm->existingUser = $vm->userDAM->readUser(hSession('userId'));
        // var_dump($vm->existingUser);
        // var_dump($_POST);   
        // get an array of the hPOST values that were sent in
        foreach ($vm->userAttributes as $attribute) {
            $vm->userChanges[$attribute] = !empty(hPOST($attribute)) ? hPOST($attribute) : '';
            // trim and remove the extra whitespace of all fields.
            $vm->userChanges[$attribute] = preg_replace('/\s+/', ' ', trim($vm->userChanges[$attribute]));
        }
        // var_dump($vm->userChanges);

        if ($vm->validateUserInput()) {
            // var_dump($vm->userChanges);
            // if no changes were made... tell the user of our results.
            if ($vm->isArrayEmpty($vm->userChanges) && empty(hFILES('profilePicture', 'name'))) {
                $vm->statusMsg[] = "<b><i>No changes were made.</i></b>";
            } else { //there was a change made and we need to check.
                // process file.
                if (!empty(hFILES('profilePicture', 'name'))) {
                    $vm->processAndUploadFile();
                    $vm->statusMsg[] = "Your Profile Picture was updated.";
                }
                // process each of the text fields. Any left non-empty, we'll update.
                foreach ($vm->userAttributes as $attribute) {
                    if (!empty($vm->userChanges[$attribute])) {
                        $vm->existingUser->$attribute = $vm->userChanges[$attribute];
                        $vm->statusMsg[] = "Your " .
                            ucwords(implode(' ', preg_split('/(?=[A-Z])/', $attribute))) .
                            " was updated.";
                    }
                }
                // var_dump($vm->existingUser);
                // write updated one to user.
                $vm->userDAM->writeUser($vm->existingUser);


                $vm->statusMsg[] = "<b>Click <a href='?ctlr=user&amp;action=viewProfile'>
                here</a> to return to your profile.</b>";
            }
        }
        return $vm;
    }

    /**
     * Validates user input on edit profile page
     * @return true if successful validation. False if not.
     */
    private function validateUserInput()
    {
        $success = true;

        // print_r($this->userChanges); //*DEBUG
        // declare the fields to make it easier to read.
        $firstName = $this->userChanges['firstName'];
        $lastName = $this->userChanges['lastName'];
        $phone = $this->userChanges['phone'];
        $description = $this->userChanges['description'];


        // Validation for first name or last name.
        if (!empty($firstName) && !$this->checkName($firstName, 'firstName', 20)) {
            $success = false;
        }
        if (!empty($lastName) && !$this->checkName($lastName, 'lastName', 20)) {
            $success = false;
        }
        if (!empty($phone) && !$this->checkPhone($phone)) {
            $success = false;
        }

        if (strlen($description) > self::DESCRIPTION_CHAR_LIMIT) {
            $this->errorMsg[] = "Your description is over 250 characters!";
            $success = false;
        }

        // uploaded file validation checks. Only activate these checks if a file was uploaded.
        // the _FILES array is filled if they have variables.
        if (!empty(hFILES('profilePicture', 'name'))) {
            // var_dump($_FILES);

            $fileErrorMsg = validate_image_file(
                'profilePicture',
                self::MAX_FILE_SIZE,
                $this->allowedMimeTypes,
                $this->allowedExtensions
            );
            if (!empty($fileErrorMsg)) {
                $this->errorMsg[] = $fileErrorMsg;
                $success = false;
            }
        }


        return $success;
    }

    /**
     * Checks that the name has letters and uses symbols properly.
     * @param name  name of the user
     * @param nameType  specify whether it's a first name (firstName) or surname (lastName).
     * @param allowedLength the characters allowed for the name (optional).
     * @return bool True if the name is valid, false if the name is invalid.
     */
    private function checkName($name, $nameType, $allowedLength)
    {
        $success = true;
        $nameType = ucwords(implode(' ', preg_split('/(?=[A-Z])/', $nameType)));

        if (isset($allowedLength) && strlen($name) > $allowedLength) {
            $this->errorMsg[] .= "<b>Error</b>: " . $nameType . " can only be " . $allowedLength . " characters long.";
            $success = false;
        }
        if (preg_match("~[^A-Z\'-.]~i", htmlspecialchars_decode($name, ENT_QUOTES))) {
            $this->errorMsg[] = "<b>Error</b>: " . $nameType . " can only contain letters and/or the following symbols: - . ' ";
            $success = false;
        }
        if (!preg_match("~\A[A-Z]~i", $name)) {
            $this->errorMsg[] = "<b>Error</b>: " . $nameType . " did not begin with a letter.";
            $success = false;
        }
        if (preg_match("~[\-'.]{2,}~", htmlspecialchars_decode($name, ENT_QUOTES))) {
            $this->errorMsg[] = "<b>Error</b>: " . $nameType . " cannot contain symbols in succession.";
            $success = false;
        }

        return $success;
    }

    /**
     * Checks if the given phone number is formatted as (xxx) xxx-xxxx.
     * @param phone 
     * @return - True if the phone is valid, false if the phone number did not follow the prescribed format.
     */
    private function checkPhone($phone)
    {
        if (!preg_match('/\A\(\d{3}\)\s?\d{3}-\d{4}\Z/', $phone)) {
            $this->errorMsg[] = "<b> Error: </b> The phone number was incorrectly formatted. </br>";
            return false;
        }
        return true;
    }

    /**
     * Check if the array's elements are empty.
     * Returns true if all elements are empty.
     */
    private function isArrayEmpty($array)
    {
        $isEmpty = true;
        foreach ($array as $element) {
            if (!empty($element)) {
                $isEmpty = false;
            }
        }

        return $isEmpty;
    }

    /**
     * Create a new filename, move the file to a persistent location, convert it to JPEG, strip
     * it of execution permissions, and set the user's profilePicture value to the newly made image.
     */
    private function processAndUploadFile()
    {
        $tmpFile = hFILES('profilePicture', 'tmp_name');
        // generate new filename from the 
        $newFileName = sanitize_file_name(hFILES('profilePicture', 'name'));
        $uniqueFileName = generate_unique_file_name($newFileName, 'pfp_');

        $initialFilePath = self::UPLOAD_PATH . '/' .  $uniqueFileName;
        move_uploaded_file($tmpFile, $initialFilePath);

        $stickerPath = convert_to_jpeg($initialFilePath, $initialFilePath, 90, true);
        // var_dump($initialFilePath);
        // var_dump($stickerPath);
        chmod($stickerPath, 0644);

        // let's make sure to remove the old profile pic.
        if($this->existingUser->profilePicture != self::DEFAULT_PROFILE_PICTURE) {
           unlink(self::UPLOAD_PATH . '/' . $this->existingUser->profilePicture);
        }

        $this->existingUser->profilePicture = basename($stickerPath);
    }
}
