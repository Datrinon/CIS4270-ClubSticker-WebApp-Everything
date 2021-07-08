<?php

/**
 * View model for login functions.
 *
 * @author jam
 * @version 181117
 */
class LoginVM
{

    public $enteredUserId;
    public $enteredPassword;
    public $userType;
    public $errorMsg;
    public $statusMsg;

    // User type constants used for switching in the controller.
    const VALID_LOGIN = 'valid_login';
    const INVALID_LOGIN = 'invalid_login';

    public function __construct()
    {
        $this->failedLoginDAM = new failedLoginDAM(); // Added this in to throttle brute-force attacks.
        $this->userDAM = new UserDAM();
        $this->errorMsg = '';
        $this->statusMsg = array();
        $this->enteredUserId = '';
        $this->enteredPassword = '';
    }

    public static function getInstance()
    {
        $vm = new self();
        // sanitization within hPOST protects from XSS
        $vm->enteredUserId = strtolower(hPOST('username'));
        $vm->enteredPassword = hPOST('password');
        // sanitization within DAM protects from sqli
        $user = $vm->userDAM->readUser($vm->enteredUserId);
        // Use this DAM to get the failed login records for the user.
        $failedLoginRecord = $vm->failedLoginDAM->readFailedLogin($vm->enteredUserId);

        // Does any failed login record for the associated user exist? If so, call throttleFailedLogins to process.
        // Otherwise, it doesn't, and set to 0, which passes the user through the throttleDelay check.
        $throttleDelay = isset($failedLoginRecord) ? $vm->throttleFailedLogins($failedLoginRecord) : 0;

        // If they had an existing record and they surpassed the login attempts, it would set a delay on them in throttleFailedLogins.
        if ($throttleDelay > 0) {
            $vm->errorMsg = "Too many failed logins. </br>";
            $vm->errorMsg .= "You must wait {$throttleDelay} seconds before attempting another login.";
            $vm->userType = self::INVALID_LOGIN;
        // Else it doesn't and we proceed with authentication measures.
        } else {
            // if the user authenticates successfully...
            if ($vm->authenticateUser($user)) {
                // check if they have a failed login record.
                // if yes, wipe it and update the db.
                if (!empty($failedLoginRecord)) {
                   $failedLoginRecord->count = 0;
                   $vm->failedLoginDAM->writeFailedLogin($failedLoginRecord);
                }

                $vm->userType = self::VALID_LOGIN;
                session_start();
                after_successful_login();
                $_SESSION['userName'] = $user->firstName . ' ' . $user->lastName;
                $_SESSION['userId'] = $vm->enteredUserId;
            // else, write on the failedLoginRecord.
            } else {
                // complain and set status.
                $vm->errorMsg = "Invalid username and/or password!";
                $vm->userType = self::INVALID_LOGIN;
                // If the user decided not to enter anything in the input fields, we won't punish them for that.
                // However, if not empty, that means it was a valid user and we'll proceed with checking their failedLogin records.
                // How do we know it is a valid user? Well, in the assignment near the top of the function,
                // we can rely on the database to only return a result if the enteredUserId actually exists in the db.
                // If it doesn't, we know the username is not valid.
                if (!empty($user)) { 
                    // If their record was empty...
                    if (empty($failedLoginRecord)) {
                        // we'll write them to the database.
                        $vm->failedLoginDAM->writeFailedLogin($user);
                    }
                    // Otherwise, we should update the record by iterating the failed login count.
                    // A new time is set when we writeFailedLogin to the database.
                    else {
                        // increment user login
                        $count = $failedLoginRecord->__get('count') + 1;
                        $failedLoginRecord->__set('count', $count);
                        // write to DB
                        $vm->failedLoginDAM->writeFailedLogin($failedLoginRecord);
                    }
                }
                //* DEBUG
                // echo("Var dumps: ");
                // var_dump($user);
                // var_dump($failedLoginRecord);
            }
        }
        return $vm;
    }

    private function authenticateUser($user)
    {
        $userFound = true;
        // The user wasn't found.
        if ($user === null) {
            $userFound = false;
        }

        return $userFound &&
            password_verify($this->enteredPassword, $user->password);
    }

    private function throttleFailedLogins($record)
    {
        $throttle_at = 8;
        $delay_in_minutes = 4;
        $delay = 60 * $delay_in_minutes;

        if ($record->__get('count') >= $throttle_at) {
            $remainingDelay = ($record->__get('lastLoginTime') + $delay) - time();
            //$remainingDelayInMinutes = //* Fill this out instead if you don't like seconds.
            return $remainingDelay;
        } else {
            return 0;
        }
    }
}
