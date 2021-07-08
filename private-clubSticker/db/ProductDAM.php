<?php

/**
 * Product model data access and manipulation (DAM) class.
 * This version is vulnerable to SQL injection.
 *
 * @author jam
 * @version 180428
 */
class ProductDAM extends DAM
{

    // Database connection is inherited from the parent (from common/).
    // access database connection through $this->db
    function __construct()
    {
        parent::__construct(); 
    }

    /**
     * Read the Product object from the database with the specified ID
     * @param type $productID the ID of the product to be read.
     * @return \Product the resulting Product object - null if product is
     * not in the database.
     */
    public function readProduct($productID)
    {
        
        $query = 'SELECT * FROM products WHERE productID = :productID';  // write
        $statement = $this->db->prepare($query);                         // prepare
        $statement->bindValue(':productID', $productID, PDO::PARAM_INT); // bind
        // echo $statement->debugDumpParams();
        // $query = 'SELECT * FROM products WHERE productID = \'' . $productID . '\'';
        // $statement = $this->db->prepare($query);
        $statement->execute();
        $productDB = $statement->fetch();
        $statement->closeCursor();
        if ($productDB == null) {
            return null;
        } else {
            return new Product($this->mapColsToVars($productDB));
        }
    }

    /**
     * Read all the Product objects in the database.
     * dht: no update needed here because there is no variable.
     * @return \Product an array of Product objects.
     */
    public function readProducts()
    {
        $query = 'SELECT * FROM products
              ORDER BY productID';
        $statement = $this->db->prepare($query);
        $statement->execute();
        $productsDB = $statement->fetchAll();
        $statement->closeCursor();

        // Build an array of Product objects
        $products = array();
        foreach ($productsDB as $key => $value) {
            $products[$key] = new Product($this->mapColsToVars($productsDB[$key]));
        }
        return $products;
    }

    /**
     * Read all the Product objects in the database with the specified
     * categoryID.
     * @param type $categoryID the ID of the product category to be read.
     * @return \Product an array of Product objects.
     */
    public function readProductsByCategoryId($categoryID)
    {
        $query = 'SELECT * FROM products WHERE categoryID = :categoryID ORDER BY productID';
        $statement = $this->db->prepare($query);
        $statement->bindParam(':categoryID', $categoryID, PDO::PARAM_INT);

        $statement->execute();
        // echo $statement->debugDumpParams(); 
        $productsDB = $statement->fetchAll();
        $statement->closeCursor();

        // Build an array of Product objects
        $products = array();
        foreach ($productsDB as $key => $value) {
            $products[$key] = new Product($this->mapColsToVars($productsDB[$key]));
        }
        return $products;
    }

    /**
     * Write the specified product to the database. If the product is not
     * in the database, the object is added. If the product is already in the
     * database, the object is updated.
     * @param type $product the Product object to be written.
     */
    public function writeProduct($product)
    {
        $query = 'SELECT productID FROM products WHERE productID = :productID';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':productID', $product->id);
        $statement->execute();
        // echo $statement->debugDumpParams();
        $productDB = $statement->fetch();
        $statement->closeCursor();
        if ($productDB == null) {
            // Add a new product to the database
            $query = 'INSERT INTO products (productCode, productName,
                                            listPrice, uploader)
                      VALUES               (:productCode, :productName,
                                            :listPrice, :uploader)';
           

            $statement = $this->db->prepare($query);
            $statement->bindValue(':productCode', $product->productCode);
            $statement->bindValue(':productName', $product->name);
            $statement->bindValue(':listPrice', $product->listPrice);
            $statement->bindValue(':uploader', $product->uploader);

            
            $statement->execute();
            // echo $statement->debugDumpParams(); //DHT: DEBUG
            $statement->closeCursor(); // enables you to execute statement again
        } else {
            
            // Update an existing Product.
            $query = 'UPDATE products SET 
                        productName       = :productName,
                        productCode       = :productCode,
                        listPrice         = :listPrice,
                      WHERE productID   = :productID';

            $statement = $this->db->prepare($query);
            $statement->bindValue(':productCode', $product->productCode);
            $statement->bindValue(':productName', $product->name);
            $statement->bindValue(':listPrice', $product->listPrice);
            $statement->bindValue(':productID', $product->id);

            $statement->execute();
            $statement->closeCursor();
        }
    }

    /**
     * Delete the specified Product object from the database.
     * No SQL here to fix.
     * @param type $product the Product object to be deleted.
     */
    public function deleteProduct($product)
    {
        $this->deleteProductById($product->id);
    }

    /**
     * Delete the Product object from the database with the specified ID.
     * 
     * @param type $productID the ID of the Product to be deleted.
     */
    public function deleteProductById($productID)
    {
        // $query = 'DELETE FROM products WHERE productID = \'' . $productID . '\'';
        $query = 'DELETE FROM products WHERE productID = :productID';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':productID', $productID);
        $statement->execute();
        $statement->closeCursor();
    }

    // Translate database columnames to object instance variable names
    private function mapColsToVars($colArray)
    {
        $varArray = array();
        foreach ($colArray as $key => $value) {
            if ($key == 'productID') {
                $varArray['id'] = $value;
            } else if ($key == 'categoryID') {
                $varArray['categoryId'] = $value;
            } else if ($key == 'productCode') {
                $varArray['productCode'] = $value;
            } else if ($key == 'productName') {
                $varArray['name'] = $value;
            } else if ($key == 'description') {
                $varArray['description'] = $value;
            } else if ($key == 'listPrice') {
                $varArray['listPrice'] = $value;
            } else if ($key == 'discountPercent') {
                $varArray['discountPercent'] = $value;
            }
        }
        return $varArray;
    }

    /**
     * dht custom function
     * @return $productIds - an array of all productIDs in the database.
     */
    public function getAllProductIDs() 
    {
        $query = 'SELECT productID from products';
        $statement = $this->db->prepare($query);
        $statement->execute();
        // this tells PDO to only fetch a single column... the first column (0).
        // otherwise, it fetches an array of columns representing the entirety of the rows.
        $productIds = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
        $statement->closeCursor();

        return $productIds;
    }

    /**
     * dht custom function
     * @param type $userID
     * @return $userProductIds - an array of all productIDs in the database associated with a user.
     */
    public function getUserProductIDs($userID) 
    {
        $query = 'SELECT productID FROM products WHERE uploader = :userID';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':userID', $userID);
        $statement->execute();
        $userProductIds = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
        $statement->closeCursor();

        return $userProductIds;
    }

}
