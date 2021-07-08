<?php

/**
 * View model for user upload functions
 *
 * @author dan
 */
class UploadVM
{

    public $uploadType;
    public $errorMsg;
    public $statusMsg;

    const MAX_FILE_SIZE = 1048576 * 2; // 2MB file size limit 
    const UPLOAD_PATH = WEB_BASE_DIR . 'club-sticker-images/products';
    
    // dan: three attributes for a product. Same name as the upload file form.
    // code -- the filename btw
    // name -- the product name
    // price -- the price of the product.
    private $productAttributes = array(
        'productImage', 'productName', 'listPrice'
    );

    private $allowedMimeTypes = ['image/png', 'image/jpg', 'image/jpeg'];
    private $allowedExtensions = ['png', 'jpg', 'jpeg'];

    // User type constants used for switching in the controller.
    // These are class constants; they are defined on a per-class basis (i.e. like static)
    // Thus, you have to reference the class wherever you use it.
    const VALID_UPLOAD = 'valid_upload';
    const INVALID_UPLOAD = 'invalid_upload';

    public function __construct()
    {
        $this->productDAM = new ProductDAM();
        $this->errorMsg = '';
        $this->statusMsg = array();
        $this->uploadType = self::INVALID_UPLOAD;
        $this->newProduct = null;
    }

    public static function getInstance()
    {

        $vm = new self();

        $varArray = array(
            'id' => '',
            'productCode' => hPOST('productImage'),
            'name' => preg_replace('/\s+/', ' ', trim(hPOST('productName'))), //trim and replace multiple whitespace.
            'listPrice' => trim(hPOST('listPrice')),
            'uploader' => hSession('userId')
        );

        $vm->newProduct = new Product($varArray);


        if ($vm->validateUploadInput()) {

            $stickerFilePath = $vm->processAndUploadFile();

            if ($stickerFilePath == "") {
                $vm->errorMsg = "We ran into an issue uploading your sticker. Please try again later.";
            } else {
                // link the productCode to the basename of the image file.
                $vm->newProduct->productCode = file_name_no_extension(basename($stickerFilePath));

                $vm->productDAM->writeProduct($vm->newProduct);

                $vm->statusMsg[] = "Upload for '" . $vm->newProduct->name . "' sticker was successful.";
                $vm->statusMsg[] = "You may now view the sticker on the catalog or your profile.";

                $vm->uploadType = self::VALID_UPLOAD;
            }
        }
        return $vm;
    }

    private function validateUploadInput()
    {
        if (!$this->checkAllInputsPresent()) {
            return false; 
        }

        //productImage refers to the key associated with the file upload in $_FILES
        $fileErrorMsg = validate_image_file('productImage', self::MAX_FILE_SIZE,
                                            $this->allowedMimeTypes, $this->allowedExtensions);
        if (!empty($fileErrorMsg)) {
            $this->errorMsg .= $fileErrorMsg;
            return false;
        }

        if (!$this->validateProductNameAndPrice()) {
            return false;
        }

        return true;
    }

    private function checkAllInputsPresent()
    {
        $success = true;
        foreach ($this->productAttributes as $parameter) {
            if ($parameter != "productImage") {
                if (empty(hPOST($parameter))) {
                    $this->errorMsg .= "<b> Missing: </b>" . ucwords(implode(' ', preg_split('/(?=[A-Z])/', $parameter))) . "<br />";
                    $success = false;
                }
            } else {
                if ($_FILES[$parameter]['error'] == 4) {
                    $this->errorMsg .= "<b> Missing: </b>" . ucwords(implode(' ', preg_split('/(?=[A-Z])/', $parameter))) . "<br />";
                    $success = false;
                }
            }
        }
        return $success;
    }
    private function validateProductNameAndPrice()
    {
        $success = true;
        //* Validating product name.
        // The product name should have three or more alphanumeric characters in succession.
        if (!preg_match("~[a-z0-9]{3,}~i", $this->newProduct->name)) {
            $this->errorMsg .=  "<b>Error</b>: Product name must be three alphanumerical characters or longer. <br />";
            $success = false;
            // Only uses alphanumericals and a subset of special characters.
        } elseif (!preg_match("~\A[a-z0-9\-_!$. '\/]+\Z~i", $this->newProduct->name)) {
            $this->errorMsg .=  "<b>Error</b>: Product name can only use alphanumerical characters
            or the following symbols: - _ ! $ . /'<br />";
            $success = false;
            // Doesn't use said subset of special characters in succession.
        } elseif (preg_match("~[\-_!$. '\/]{2,}~", $this->newProduct->name)) {
            $this->errorMsg .=  "<b>Error</b>: Product name cannot use more than two symbols in succession.<br />";
            $success = false;
        }

        //* Validating price.
        // Price should match X.XX format.
        if (!preg_match("/\A\d+(\.\d{2})?\Z/", $this->newProduct->listPrice)) {
            // $this->errorMsg .= "Price?" . $this->newProduct->listPrice; //Debug
            $this->errorMsg .= "<b>Error</b>: Product price must follow format X.XX. <br />";
            $success = false;
        }
        return $success;
    }

    /**
     * Process and upload the file. Three criteria for it to be considered successful.
     * 1. The file was successfully moved to a persistent location.
     * 2. The file was converted to a JPEG for speed and storage concerns.
     * 3. The file's execution permission was successfully removed.
     * 
     * @return filepath - the filepath of the uploaded sticker. If it failed any of these conditions, nothing returned.
     */
    private function processAndUploadFile()
    {
        $success = true;
        
        $tmpFile = hFILES('productImage', 'tmp_name');
        // create a file name using the given product name and a unique string.
        $productNameWords = str_replace(' ', '', ucwords($this->newProduct->name));
        $productNameWords = preg_replace('~[\W]~i', '', $productNameWords) .
        '.' . strtolower(file_extension(hFILES('productImage', 'name')));

        $uniqueFileName = generate_unique_file_name($productNameWords, "stkr_");
        // resulting filename is stkr_{productNameWords}_{uniquestring}.{originalUploadExtension}

        // Move the original file to a persistent location.
        $initialFilePath = UploadVM::UPLOAD_PATH . '/' .  $uniqueFileName;
        if (!move_uploaded_file($tmpFile, $initialFilePath)) {
            $success = false;
        }

        #region This region contains non-security related code to convert to JPEG and crop+resize it to 500x500.
        // Convert original file to JPEG. Get filepath of the sticker.
        $stickerPath = convert_to_jpeg($initialFilePath, $initialFilePath, 85, true);
        if (empty($stickerPath)) {
            $success = false;
        }

        // Now check if we need to scale down or scale up the image to 500 x 500 resolution.
        $originalDimensions = getimagesize($stickerPath);
        
        if ($originalDimensions[0] != 500 && $originalDimensions[1] != 500) {
            $sticker = imagecreatefromjpeg($stickerPath);
            scale_and_crop($sticker, 500, 500, $stickerPath);
        }
        #endregion

        // lastly, remove file execution.
        if (!chmod($stickerPath, 0644)) {
            $success = false;
        }

        if ($success) {
            return $stickerPath;
        } else {
            return "";
        }
    }
}
