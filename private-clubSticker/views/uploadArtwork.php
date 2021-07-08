<?php
require('views/clubStickerUserHeader.php');
?>
<main id='Content'>

    <header class="column one column_column">
        <div class="column_attr">
            <h1 id="profile-page-header-title">Upload Artwork</h1>
        </div>
    </header>

    <section>
        <div class="column one-second column_column">
            <?php
            if ($vm != null) {
                if ($vm->errorMsg != '') { ?>
                    <div class="error-diagnostic">
                        <p id="error-diagnostic-title"> UPLOAD ERROR </p>
                        <p id="error-diagnostic-text"> <?php echo $vm->errorMsg; ?></p>
                    </div>
                <?php }
                // use empty since statusMsg is an array.
                if (!empty($vm->statusMsg)) { ?>
                    <div class="status-diagnostic">
                        <p id="diagnostic-title"> UPLOAD SUCCESS! </p>
                        <p id="diagnostic-text"> <?php
                                                    foreach ($vm->statusMsg as $message) {
                                                        echo $message . "<br/>";
                                                    }
                                                    ?></p>
                    </div>
            <?php }
            }
            ?>
            <form action="" method="POST" enctype="multipart/form-data" id="upload-form">

                <?php // The next four lines are all related to PHP. CSRF, file-size limit, and
                // You need these ctlr and action inputs always since there's no GET request providing it!
                echo csrf_token_tag(); ?>
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxFileSize; ?>" />

                <input type="hidden" name="ctlr" value="user" />
                <input type="hidden" name="action" value="uploadArtwork" />

                <label for="product-image">Select an image</label>
                <div class="form-label-tips">
                    <label for="product-image"> Your file should be: </label>
                    <label for="product-image"> • no more than 2MB in size.</label>
                    <label for="product-image"> • in a .JPG, .JPEG, or .PNG format.</label>
                    <label for="product-image"> • be 500 x 500 for best results. </label>
                </div>
                <input type="file" name="productImage" onchange="previewFile()" id="product-image" />

                <label for="productName">Product Name</label>
                <div class="form-label-tips">
                    <label for="productName"> You may use: </label>
                    <label for="productName"> • alphanumeric characters and spaces.</label>
                    <label for="productName"> • the following symbols: - _ ! $ . /</label>
                </div>
                <input type="text" id="productName" name="productName" placeholder="Sticker name" />

                <label for="productPrice">Product Price</label>
                <div id="price-field">
                    <i class="form-icon">$</i>
                    <input type="text" id="productPrice" name="listPrice" placeholder="x.xx" />
                </div>

                <input type="submit" name="submit" value="Upload My Sticker" />
            </form>
        </div>
        <div class="column one-second column_column">
            <div class="column_attr">
                <h2 id="preview-title"> Preview Pane </h2>
                <div class="preview-box">
                    <img id="preview-image" src="" alt="Your preview will show up here...">
                </div>
            </div>
    </section>

    <script>
        // From Mozilla's FileReader documentation
        // more info @ https://developer.mozilla.org/en-US/docs/Web/API/FileReader/readAsDataURL
        function previewFile() {
            // Select the image in the DOM model
            // fyi: the argument to query selector is equivalent to a css rule.
            const preview = document.querySelector("#preview-image");
            // Select the file input, obtain files[0], which contains info about the "uploaded" file.
            const file = document.querySelector('input[type=file]').files[0];
            // Create FileReader object
            const reader = new FileReader();

            // the FileReader will run the function below when it is instantiated (which happens when file input changed)
            reader.addEventListener("load", function() {
                // convert image file to base64 string; the if statement below obtains the string as such.
                // set the src to that.
                preview.src = reader.result;
            }, false);

            // read the file (64-bit encoded string) as a data URL 
            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>

</main>


<?php
require('views/clubStickerFooter.php');
?>