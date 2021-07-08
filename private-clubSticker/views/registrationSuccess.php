<?php require('views/clubStickerHeader.php'); ?>
<main id='Content'>
<section class="content_wrapper clearfix">
	<h1 class="page-subtitle"> Welcome to Club Sticker </h1>
    <p>Success! All user registration inputs are valid!</h1>
	<p>First name: <?php echo $vm->newUser->firstName; ?><br>
	   Last name: <?php echo $vm->newUser->lastName; ?><br>
	   Email address: <?php echo $vm->newUser->id; ?><br>
	   Phone number: <?php echo $vm->newUser->phone; ?></p>
	<button onclick="document.location='?ctlr=user&action=login'"> Go Login </button>
    
</section>
</main>
<?php require('views/clubStickerFooter.php');