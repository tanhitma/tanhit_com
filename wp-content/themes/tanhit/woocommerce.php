<?php get_header(); ?>
<section style="min-height: 300px">
    <div class="container" id="shop">
        <div class="content">

				<?php if ( is_product() ) { ?>
					<div class="row">
						<div class="col-sm-12">
							<h2><?php the_title(); ?></h2>
						</div>
					</div>
				<?php } else if ( is_shop() ) { ?>
					<?php // do nothing ?>
				<?php } ?>
				
                <div class="row">
                    <div class="col-sm-12">
                        <?php woocommerce_content(); ?>
                    </div>
                </div>

        </div>
    </div>
</section>
<?php get_footer(); ?>
