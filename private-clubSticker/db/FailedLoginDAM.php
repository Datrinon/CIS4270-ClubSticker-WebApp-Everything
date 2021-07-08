<?php

/**
 * Data access and manipulation (DAM) class for failed_logins. To be used to throttle users.
 *
 * @author dan
 * @version 171117
 */

/* Accesses this table
CREATE TABLE failed_logins (
   userID		  VARCHAR(100) NOT NULL PRIMARY KEY,
   count		  INT,
   lastLoginTime  DATETIME
);
 
 */

class FailedLoginDAM extends DAM
{
    // The parent class, DAM, constructs a database connection, allowing all subsequent queries to work
    function __construct()
    {
        parent::__construct();
    }

    // I need to take care of these functions:
    // • read from the database using the username.
    // • update the database -- use the ProductDAM() writeProduct() method to write and update.
    // • mapColsToVar() to make the results tangible.
    // I won't do anything for the throttle function in DAM file. I'll leave that to the loginVM to handle.
    // I also need to modify loginVM so that it displays the attempts remaining and time after getting locked.
    
        /**
     * Read the User object from the database with the specified ID
     * @param type $userId the user's unique user ID (probably email)
     * @return \User the resulting User object - null if user is
     * not in the database.
     */
    public function readFailedLogin($userId) {
        $query = 'SELECT * FROM failed_logins WHERE userID = :userID';
        $statement = $this->db->prepare($query); 
        $statement->bindValue(':userID', $userId);
        $statement->execute();
        $failedLoginDB = $statement->fetch();
        $statement->closeCursor();
        if ($failedLoginDB == null) {
            return null;
        } else {
            return new FailedLogin($this->mapColsToVars($failedLoginDB));
        }
    }

    public function writeFailedLogin($user) {
        $query = 'SELECT userID FROM failed_logins WHERE userID = :userID';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':userID', $user->id);
        $statement->execute();
        $userDB = $statement->fetch();
        $statement->closeCursor();
        if ($userDB == null) {
            // Record the failed login 
            $query = 'INSERT INTO failed_logins
                (userID, count, lastLoginTime)
              VALUES
                (:userID, :count, :lastLoginTime)';
            $statement = $this->db->prepare($query);
            $statement->bindValue(':userID', $user->id);
            $statement->bindValue(':count', 1);
            $statement->bindValue(':lastLoginTime', time());
            $statement->execute();
            // var_dump($statement->errorInfo());
            $statement->closeCursor();
        } else {

            // Update the record with the user object.
            $query = 'UPDATE failed_logins
              SET count = :count, lastLoginTime = :lastLoginTime
              WHERE userID = :userID';
            $statement = $this->db->prepare($query);
            $statement->bindValue(':userID', $user->id);
            $statement->bindValue(':lastLoginTime', time()); 
            $statement->bindValue(':count', $user->count); // I've already incremented it outside
            $statement->execute();
            $statement->closeCursor();
        }
    }



    // Translate database column names to object instance variable names
    private function mapColsToVars($colArray) {
        $varArray = array();
        foreach ($colArray as $key => $value) {
            if ($key == 'userID') {
                $varArray ['id'] = $value;
            } else if ($key == 'count') {
                $varArray ['count'] = $value;
            } else if ($key == 'lastLoginTime') {
                $varArray ['lastLoginTime'] = $value;
            }
        }
        return $varArray;
    }
}
