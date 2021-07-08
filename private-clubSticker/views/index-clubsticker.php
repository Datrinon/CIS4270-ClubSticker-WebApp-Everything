<?php require($header); ?>
<!-- Main Content -->
<div id="Content">
<?php
    if ($errorVM != null) {
        if ($errorVM->errorMsg != '') { ?>
            <div class="error-diagnostic">
                <p id="error-diagnostic-title"> Form Error </p>
                <p id="error-diagnostic-text"> <?php echo $errorVM->errorMsg; ?></p>
                <p id="error-diagnostic-text"> <?php echo "This error can occur when refreshing a form." ?></p>
                <p id="error-diagnostic-text"> <?php echo "You have been redirected to the home page." ?></p>
            </div>
    <?php }
    } ?>
    <div class="content_wrapper clearfix">
        <div class="sections_group">
            <div class="section">
                <img id="banner" src="club-sticker-images/banner.png">
                <h1 id="catalog-subtitle">Sticker Catalog</h1>
                <div class="section_wrapper clearfix">
                    <div class="items_group clearfix">
                        <!-- One full width row-->
                        <div class="column one woocommerce-content">
                            <div class="products_wrapper isotope_wrapper">
                                <ul class="products masonry isotope" id="product-list">
                                    <?php
                                    foreach ($vm->products as $product) {
                                        $productName = $product->name;
                                        $listPrice = $product->listPrice;
                                        $productImageFile = $product->productCode;

                                    ?>
                                        <!-- dht: each list item is a product. -->
                                        <li class="post-70 product type-product has-post-thumbnail isotope-item sale shipping-taxable purchasable product-type-simple product-cat-posters instock">
                                            <div class="image_frame scale-with-grid product-loop-thumb">
                                                <div class="image_wrapper">
                                                    <a href="#">
                                                        <div class="mask"></div> <img width="500" height="500" src="club-sticker-images/products/<?php echo $productImageFile ?>.jpg" class="scale-with-grid wp-post-image" alt="poster_2_up" />
                                                    </a>
                                                    <div class="image_links double">
                                                        <a href="#" rel="nofollow" data-product_id="70" class="product_type_simple"><i class="icon-basket"></i></a><a class="link" href="#"><i class="icon-link"></i></a>
                                                    </div>
                                                </div>
                                                <!-- <span class="onsale"><i class="icon-star"></i></span><a href="#"><span class="product-loading-icon added-cart"></span></a> -->
                                            </div>
                                            <div class="desc">
                                                <h4><a href="#"><?php echo $productName ?> </a></h4>
                                                <span class="price"><span class="amount">&#36;<?php echo $listPrice ?></span></span>
                                                <div class="excerpt">
                                                </div>
                                            </div>
                                        </li>
                                        <?php } ?>
                                </ul>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require('views/clubStickerFooter.php'); ?>