<?php require('views/clubStickerUserHeader.php'); ?>
<main id="Content">
    <div class="column one column_column">
        <div class="column_attr">
            <h1 id="profile-page-header-title">Edit Profile</h1>
        </div>
    </div>
    <?php
        // var_dump($vm);
        if ($vm != null) {
            if (!empty($vm->errorMsg)) { ?>
            <div class="column one column_column">
                <div class="error-diagnostic column_attr">
                    <p id="error-diagnostic-title"> ERROR </p>
                    <p id="error-diagnostic-text"> <?php 
                        foreach ($vm->errorMsg as $message) {
                            echo $message . "<br/>";
                        }
                        echo "<b><i>No changes were made. </i></b>"
                    ?></p>
                </div>
            </div>
        <?php }
            // use empty since statusMsg is an array.
            if (!empty($vm->statusMsg)) { ?>
                <div class="column one column_column">
                <div class="status-diagnostic column_attr" style="margin-left:24.5%; width:50%;">
                    <p id="diagnostic-title"> Notice </p>
                    <p id="diagnostic-text"> <?php 
                        foreach ($vm->statusMsg as $message) {
                            echo $message . "<br/>";
                        }
                    ?></p>
                </div>
        <?php }
        }
        ?>
    <form action="." method="POST" enctype="multipart/form-data">
        <div class="column one column_column">
            <div class="column_attr" id="input-form">
                <?php echo csrf_token_tag(); ?> <br />
                <input type="hidden" name="ctlr" value="user" />
                <input type="hidden" name="action" value="editProfile" />
                <div>
                    <label for="profilePicture"> Set Profile Picture</label>
                    <div class="form-label-tips"">
                        <label for="product-image"> Your file should be: </label>
                        <label for="product-image"> • no more than 2MB in size.</label>
                        <label for="product-image"> • in a .JPG, .JPEG, or .PNG format.</label>
                        <label for="product-image"> • be 500 x 500 for best results. </label>
                    </div>
                    <input type="file" name="profilePicture" id="editProfilePicture">
                </div>
                <div>
                    <label for="firstName"> Change First Name</label>
                    <input type="text" name="firstName" id="firstName"
                    value="<?php echo hPOST('firstName'); ?>">
                </div>
                <div>
                    <label for="lastName"> Change Last Name</label>
                    <input type="text" name="lastName" id="lastName"
                    value="<?php echo hPOST('lastName'); ?>">
                </div>
                <div>
                    <label for="phone"> Change Phone </label>
                    <input type="text" name="phone" id="phone" placeholder="(xxx) xxx-xxxx"
                     value="<?php echo hPOST('phone'); ?>">
                </div>
                <div>
                    <label for="phone"> Change Description </label>
                    <textarea id="userDescription" name="description"
                    placeholder="Give a little information about yourself. 250 characters max."
                    rows="3" cols="80" maxlength=250
                    ><?php echo hPOST('description'); ?></textarea>
                </div>
                <div>
                    <input type="submit" name="submit" value="Confirm Changes">
                    <a class="button button_left button_js" href="?ctlr=user&amp;action=viewProfile" id="cancel-button">
                        <span class="button_icon">
                            <i class="icon-level-up"></i>
                        </span>
                        <span class="button_label">Return</span>
                    </a>
                </div>
            </div>
        </div>
    </form>
</main>


<?php require('views/clubStickerFooter.php'); ?>