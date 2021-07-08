<?php require('views/clubStickerHeader.php'); ?>
<main id='Content'>
    <section>
        <h1 class='page-subtitle'>User Login</h1>
        <?php
        if ($vm != null) {
            if ($vm->errorMsg != '') { ?>
                <div class="error-diagnostic">
                    <p id="error-diagnostic-title"> Login Failed </p>
                    <p id="error-diagnostic-text"> <?php echo $vm->errorMsg; ?></p>
                </div>
        <?php }
        } ?>
        <form action="." method="post">
            <div id="input-form">
                <?php echo csrf_token_tag(); ?> 
                <input type="hidden" name="ctlr" value="user">
                <input type="hidden" name="action" value="login">
                <div>
                    <label for="id"> Email </label>
                    <input type="text" name="username" id="id"
                    value="<?php if (isset($vm->enteredUserId)) {
                        echo trim($vm->enteredUserId);
                    }?>">
                </div>
                <div>
                    <label for="password"> Password</label>
                    <input type="password" name="password" id="pw">
                </div>
                <div>
                    <input type="submit" value="Login">
                </div>
            </div>
        </form>
        <br>
        <div class="formLink">
            <p> New to Club Sticker?
                <a href="?ctlr=user&amp;action=register">Register</a> </p>

        </div>
    </section>
</main>
<?php
require('views/clubStickerFooter.php');
