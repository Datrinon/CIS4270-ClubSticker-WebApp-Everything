<?php require('views/clubStickerHeader.php'); ?>
<main id="Content">
    <h1 class='page-subtitle'>Register as a new user</h1>
    <?php
    if ($vm != null) {
        if ($vm->errorMsg != '') { ?>
            <div class="error-diagnostic">
                <p id="error-diagnostic-title"> REGISTRATION ERROR </p>
                <p id="error-diagnostic-text"> <?php echo $vm->errorMsg; ?></p>
            </div>
    <?php }
    } ?>
    <form action="." method="post">
        <!-- same form. POST method though. -->
        <div id="input-form">
            <?php echo csrf_token_tag(); ?> <br />
            <input type="hidden" name="ctlr" value="user" />
            <input type="hidden" name="action" value="register" />
            <div>
                <label for="email"> Email </label>
                <input type="text" name="email" id="email" value="<?php echo hPOST('email'); ?>">
            </div>
            <div>
                <label for="firstName"> First Name</label>
                <input type="text" name="firstName" id="firstName" value="<?php echo hPOST('firstName'); ?>">
            </div>
            <div>
                <label for="lastName"> Last Name</label>
                <input type="text" name="lastName" id="lastName" value="<?php echo hPOST('lastName'); ?>">
            </div>
            <div>
                <label for="phone"> Phone </label>
                <input type="text" name="phone" id="phone" placeholder="(xxx) xxx-xxxx" value="<?php echo hPOST('phone'); ?>">
            </div>
            <div>
                <div id="password-tips">
                    <label for="password"> Your password should: </label>
                    <label for="password"> • be 8 characters or longer </label>
                    <label for="password"> • have an uppercase letter </label>
                    <label for="password"> • have a number </label>
                    <label for="password"> • have a symbol </label>
                </div>
                <label for="password"> Password </label>
                <input type="password" name="password" id="password">

            </div>
            <div>
                <label for="password">Confirm Password</label>
                <input type="password" name="confirmPassword" id="confirmPassword">
            </div>
            <div>
                <input type="submit" value="Sign up">
            </div>
            <div class="formLink">
                <p>Already have an account?
                    <a href="?ctlr=user&amp;action=login"> Login </a></p>

            </div>
        </div>
    </form>
</main>
<?php require('views/clubStickerFooter.php'); ?>