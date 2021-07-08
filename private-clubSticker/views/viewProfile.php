<?php require('views/clubStickerUserHeader.php'); ?>
<main id="Content">
    <div class="column one column_column">
        <div class="column_attr">
            <h1 id="profile-page-header-title">View Profile</h1>
        </div>
    </div>
    <div class="column one-third column_column">
        <div class="column_attr user-profile-avatar">
            <img alt="" src="<?php echo  'club-sticker-images/profilePictures/' . $userVM->user->profilePicture ?>">
        </div>
    </div>
    <div class="column two-third column_column" style="padding-top: 55px;">
        <div class="column_attr">
            <h1 id='username'> <?php echo $userVM->user->firstName . " " . $userVM->user->lastName ?> </h1>
            <p id='ranking'><?php echo $userVM->user->id ?></p>
            <p id='quote'><?php echo $userVM->user->description ?></p>
            <button type="button" style="font-size: 0.75em;" onclick="location.href='?ctlr=user&action=editProfile'"> Edit Profile </button>
        </div>
    </div>
    <div class="column one column_column">
        <h1 id="sticker-header">MY STICKERS</h1>
    </div>

    <?php
    if (!empty($productVM->products)) { ?>

        <div class="shop_slider">
            <div class="blog_slider_header">
                <a class="button button_js slider_prev" href="#"><span class="button_icon"><i class="icon-left-open-big"></i></span></a><a class="button button_js slider_next" href="#"><span class="button_icon"><i class="icon-right-open-big"></i></span></a>
            </div>
            <ul class="shop_slider_ul">
                <!-- dht: each list item is a product. -->
                <?php
                foreach ($productVM->products as $product) {
                    $productName = $product->name;
                    $listPrice = $product->listPrice;
                    $productImageFile = $product->productCode;
                ?>

                    <li class="post-1171 product type-product status-publish has-post-thumbnail shipping-taxable purchasable product-type-simple product-cat-clothing product-cat-hoodies instock">
                        <div class="item_wrapper">
                            <div class="image_frame scale-with-grid product-loop-thumb">
                                <div class="image_wrapper">
                                    <a href="#">
                                        <div class="mask"></div><img src="club-sticker-images/products/<?php echo $productImageFile ?>.jpg" class="scale-with-grid wp-post-image" alt="Sticker product" width="500" height="500">
                                    </a>
                                    <div class="image_links">
                                        <a class="link" href="#"><i class="icon-link"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="desc" style="background-color: rgba(255, 255, 0, 0.0);">
                                <h4><a href="#"><?php echo $productName ?></a></h4><span class="price"><span class="amount"> $<?php echo $listPrice ?></span></span>
                            </div>
                        </div>
                    </li>

                <?php } // end for loop 
                ?>

            </ul>
            <div class="slider_pagination"></div>
        </div>

    <?php } else { ?>
        <div class="column one column_column no-sticker-message">
            <div class="column_attr ">
                <p> No stickers here... <em>yet.</em>
                    <a href='?ctlr=user&action=uploadArtwork'>Upload your first sticker</a>!</p>
            </div>
        </div>
    <?php } ?>


</main>


<?php require('views/clubStickerFooter.php'); ?>