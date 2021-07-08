<?php
// Form upload functions

// Sanitize the files superglobal before using it.
function hFILES($userFile, $parameter)
{
    return h($_FILES[$userFile][$parameter]);
}

// Provides plain-text error messages for file upload errors.
function file_upload_error($error_integer)
{
    $upload_errors = array(
        // http://php.net/manual/en/features.file-upload.errors.php
        UPLOAD_ERR_OK                 => "No errors.",
        UPLOAD_ERR_INI_SIZE      => "Larger than allowed upload size.",
        UPLOAD_ERR_FORM_SIZE     => "Larger than allowed upload size.",
        UPLOAD_ERR_PARTIAL         => "Partial upload.",
        UPLOAD_ERR_NO_FILE         => "No file.",
        UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
        UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
        UPLOAD_ERR_EXTENSION     => "File upload stopped by extension."
    );
    return $upload_errors[$error_integer];
}

// Sanitizes a file name to ensure it is harmless
function sanitize_file_name($filename)
{
    // Remove characters that could alter file path.
    // I disallowed spaces because they cause other headaches.
    // "." is allowed (e.g. "photo.jpg") but ".." is not.
    $filename = preg_replace("/([^A-Za-z0-9_\-\.]|[\.]{2})/", "", $filename);
    // basename() ensures a file name and not a path
    $filename = basename($filename);
    return $filename;
}

// Returns the file permissions in octal format.
function file_permissions($file)
{
    // fileperms returns a numeric value
    $numeric_perms = fileperms($file);
    // but we are used to seeing the octal value
    $octal_perms = sprintf('%o', $numeric_perms);
    return substr($octal_perms, -4);
}

// Returns the file extension of a file
function file_extension($file)
{
    $path_parts = pathinfo($file);
    return $path_parts['extension'];
}

// custom
// Return the filename without an extension.
function file_name_no_extension($file)
{
    $path_parts = pathinfo($file);
    return $path_parts['filename'];
}

/**
 * Generates a unique filename which incorporates the original filename.
 * @param file - the filename to change
 * @param prefix - what to precede the filename with.
 * @return newFileName - the changed filename.
 */
function generate_unique_file_name($file, $prefix = null)
{
    $fileName = file_name_no_extension($file);
    $uniqueId = uniqid($prefix . $fileName . '_', true);
    $newFileName = $uniqueId . '.' . file_extension($file);
    return $newFileName;
}

/**
 * 
 * Scale and crop a given image, then place the result in a given destination.
 * @param image image resource to scale and crop
 * @param width the desired width to scale the image to.
 * @param height the desired height to scale the image to. If it's beyond the height, cropping actions ensue.
 * @param destination the filepath of where the newly modified image should be located.
 * 
 * @return status True if operation was successful, false if not.
 */
function scale_and_crop($image, $width, $height, $destination)
{
    // crop the image to get a square photo.
    if (imagesx($image) != imagesy($image)) {
        $startX = 0;
        $startY = 0;
        // If the image's width is larger...
        if (imagesx($image) > imagesy($image)) {
            // We'll base the size of the square on the height.
            $squareSide = imagesy($image);
            // the cut-out space is even because the crop is a square.
            // we can get the length of that cut-out space by finding difference
            // of the width and the crop square.
            // Dividing by two gets us the offset we need to center the image.
            $startX = (imagesx($image) - $squareSide) / 2;
        } else {
            // The image's height is larger, so do the opposite.
            $squareSide = imagesx($image);
            $startY = (imagesy($image) - $squareSide) / 2;
        }

        $newDimensions = [
            'x' => $startX, 'y' => $startY,
            'width' => $squareSide,
            'height' => $squareSide
        ];
        $image = imagecrop($image, $newDimensions);
    }


    $image = imagescale($image, $width); // scale the square photo down to 500.

    return imagejpeg($image, $destination, 100);
}

/**
 * Converts png, jpg, and gif to JPEG to desired quality. 
 * Will not convert if not in these categories.
 * @param originalImage - the image to convert. 
 * @param destinationPath - Where to put the converted image.
 * @param quality - the quality of the converted image.
 * @param deleteOriginal - if 1, delete the originally converted image (only if the conversion is successful)
 * @return pathName - Returns the path of the converted image. "" if no conversion occurred.
 */
function convert_to_jpeg($originalImage, $destinationPath, $quality, $deleteOriginal = false)
{
    $extension = strtolower(file_extension($originalImage)); 
    // echo "DEBUG: " . $extension;
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            $imageTmp = imagecreatefromjpeg($originalImage);
            break;
        case 'png':
            $imageTmp = imagecreatefrompng($originalImage);
            //extra code needed to remove transparency from PNG.
            //link: https://stackoverflow.com/questions/2569970/gd-converting-a-png-image-to-jpeg-and-making-the-alpha-by-default-white-and-not
            list($width, $height) = getimagesize($originalImage);
            // create true color image (all black)
            $output = imagecreatetruecolor($width, $height);
            // create color identifier for later usage.
            $white = imagecolorallocate($output, 255, 255, 255);
            // draws said color over the true color image. We now have a white image.
            imagefilledrectangle($output, 0, 0, $width, $height, $white);
            // We copy the png image onto the white background image.
            imagecopy($output, $imageTmp, 0, 0, 0, 0, $width, $height);
            // Set reference of output to to imageTmp.
            $imageTmp = $output;
            break;
        case 'gif':
            $imageTmp = imagecreatefromgif($originalImage);
            break;
        default:
            return ""; // no conversion performed. don't return any path name
    }

    $pathName = str_replace($extension, "jpg", $destinationPath);
    // will return true if creation was successful.
    $status = imagejpeg($imageTmp, $pathName, $quality);
    $sameImageName = ($pathName === $originalImage);
    // var_dump($sameImageName);
    // delete the original if method called with that intention, the conversion was successful, and they're not the same name
    // they can be the same name if the file sent in was a jpg.
    if ($deleteOriginal && $status && !$sameImageName) {
        unlink($originalImage);
    }

    imagedestroy($imageTmp);

    // echo $pathName; //DEBUG
    return $pathName;
}

// Searches the contents of a file for a PHP embed tag
// The problem with this check is that file_get_contents() reads 
// the entire file into memory and then searches it (large, slow).
// Using fopen/fread might have better performance on large files.
function file_contains_php_or_script($file)
{
    $contents = file_get_contents($file);
    $phpPosition = strpos($contents, '<?php');
    $jsPosition = strpos($contents, '<script>');
    return $phpPosition !== false || $jsPosition !== false;
}


/**
 * @param  fieldName-- represents the name that is associated with the file upload, as you'd use to refer to in $_FILES.
 * @param  allowedMimeTypes -- array that represents the allowed Mime types.
 * @param  allowedExtensions -- array that represents allowed file extensions.
 * @param  maxFileSize - The max file size allowed for upload.
 * @return errorMsg - indicates the error found during validation. If no error found, returns "".
 */
function validate_image_file($fieldName, $maxFileSize, $allowedMimeTypes, $allowedExtensions)
{
    // note about hFILES -- analogous to _FILES superglobal but with htmlspecialchars run on it.
    $errorMsg = "";
    $fileName = sanitize_file_name(hFILES($fieldName, 'name'));
    $fileExtension = strtolower(file_extension($fileName)); //just in case uppercased file ext.
    $fileType = hFILES($fieldName, 'type');
    $tmpFile = hFILES($fieldName, 'tmp_name');
    $error = hFILES($fieldName, 'error');
    $fileSize = hFILES($fieldName, 'size');

    // THE FILE CHECKS
    // 1. Make sure there were no upload errors.
    if ($error > 0) {
        $errorMsg = file_upload_error($error);
        // 2. Is the file actually uploaded via HTTP POST?
    } elseif (!is_uploaded_file($tmpFile)) {
        $errorMsg = "<b>Error</b>: This is not a recently uploaded file! <br />";
        // 3. Is the file within the size limit?
    } elseif ($fileSize > $maxFileSize) {
        $errorMsg = "<b>Error</b>: The file was larger than 2MB. <br />";
        // 4. Is the file the right MIME type?
    } elseif (!in_array($fileType, $allowedMimeTypes)) {
        $errorMsg = "<b>Error</b>: MIME type is invalid. <br />";
        // 5. Is the file the right extension?
    } elseif (!in_array($fileExtension, $allowedExtensions)) {
        $errorMsg = "<b>Error</b>: File extension invalid.<br />";
        // 6. Is the file given really an image? 
    } elseif (getimagesize($tmpFile) === false) {
        $errorMsg = "<b>Error</b>: Invalid image file.<br />";
        // 7. Does the image file contain any PHP or JS in it?
    } elseif (file_contains_php_or_script($tmpFile)) {
        $errorMsg = "<b>Error</b>: File had potentially malicious code in it. <br />";
        // 8. Check the image filename isn't blank after stripping out all illegal characters.
    } elseif (empty(file_name_no_extension($fileName))) {
        $errorMsg = "<b>Error</b>: Your filename must use alphanumeric characters.";
    }

    return $errorMsg;
}
