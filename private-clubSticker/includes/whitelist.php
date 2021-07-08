<?php

/**
 * Contains GET request whitelist for the GuitarShop application.
 * 
 * @author jam
 * @version 180428
 */

// Whitelist of MVC actions allowed from a GET request.
$whiltelistGET = array('listProducts', 'viewProduct', 'login', 'addProduct', 'invalidForm',
'register', 'userIndex', 'viewProfile', 'uploadArtwork', 'logout', 'editProfile');

// removed deleteProduct from the GET whitelist
// it should be a POST action, as implied by the fact that the 'delete button' is part of a 
// 'post' method form on adminProductView.php


