<?php
/**
 * @package Tanhit
 */
add_action( 'woocommerce_before_my_account', '_tanhit_before_my_account_last_news', 1 );
function _tanhit_before_my_account_last_news() {
	$args = array(
		'post_type' => 'post',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'orderby'    	 => 'post_date',
		'order'      	 => 'DESC',		
		'tax_query' => array(
			array(
				'taxonomy' => 'category',
				'field'    => 'slug',
				'terms'    => 'news',
			),
		)
	);

	$loop = new WP_Query( $args );
	$news = array();
	if ( ! empty( $loop->posts ) ) { 
		$news = $loop->posts; 
	}    
	if ( ! empty( $news ) ) {
		?>
			<span class="my-account-news"><h2><a href="/category/news"><?php pll_e( 'Новости', 'tanhit' ); ?></a></h2></span>
		<?php
		foreach( $news as $item ) { ?>
			<div class="last-news">
				<?php echo get_the_post_thumbnail( $item->ID, 'thumbnail' ); ?>
				<a href="<?php the_permalink( $item->ID ); ?>"><?php echo $item->post_title; ?></a>
			</div>  <?php
		}   
	}
}	


add_action( 'woocommerce_before_my_account', '_tanhit_before_my_account_webinars', 20 );
function _tanhit_before_my_account_webinars() {

	/**
	 * Блок "Участвую в вебинаре" состоит из двух частей:
	 *	3.1 Все купленные пользователем вебинары + ВЕБИНАРЫ С НУЛЕВОЙ ЦЕНОЙ, которые будут в будущем.
	 *		Суть: Показать пользователю списком какие онлайн-вебинары ожидают его участия и через какое время.
	 *  3.2 Ближайший НЕ купленный и НЕ бесплатный вебинар.
	 *		Суть: Позволить пользователю купить ближайший по расписанию доступный для покупки онлайн-вебинар.
	 *		Бесплатные, как и уже купленные снова покупать не нужно поэтому их исключаем, они отображаются только в 3.1
	 */

	global $tanhit_customer_products;

	$my_webinars = $tanhit_customer_products;

	$now = date( 'Ymd', time() );

	$args = array(
		'post_type' 	 => 'product',
		'posts_per_page' => -1,
		'product_cat'	 => 'webinar',
		'post_status'    => 'publish',
		'meta_key'   	 => 'product_date_start',
		'orderby'    	 => 'meta_value_num',
		'order'      	 => 'ASC',
		'meta_query'	 => array(
			array(
				'key'     => 'product_date_start',
				'value'   => '\d*',
				'compare' => 'REGEXP',
			)
		)
	);

    $events = new WP_Query( $args );

	/**
	 * Get future webinars
	 */
	$future_webinars = array();
	foreach( $events->posts as $key=>$product ) {

		$product_date_start = strtotime( get_post_meta( $product->ID, 'product_date_start', true ) );

		if ( $product_date_start <= strtotime( $now ) ) {
			continue;
		}

		$future_webinars[ $key ] = $product;
		$future_webinars[ $key ]->days_remain 		  = ( $product_date_start - strtotime( $now ) ) / DAY_IN_SECONDS;
		$future_webinars[ $key ]->product_date_start = date( 'd.m.Y', $product_date_start );
		$future_webinars[ $key ]->permalink		  = get_the_permalink( $product->ID );

		$pr = wc_get_product( $product->ID );
		$future_webinars[ $key ]->price_html 	= $pr->get_price_html();
		$future_webinars[ $key ]->price 	= $pr->get_price();
		$future_webinars[ $key ]->product_type = $pr->product_type;

	}


	/**
	 * Get free webinar in future
	 * @see $events above
	 */
	$show_free_events = array();

	foreach( $events->posts  as $post ) {

		if ( ! empty ( get_post_meta( $post->ID, 'product_date_start', true ) ) ) {

			$pr = wc_get_product( $post->ID );
			if ( $pr->get_price() == 0 ) {
				$show_free_events[] = $pr;
			}

		}

	}

	$myaccount_url = wc_get_page_permalink( 'myaccount' );

	/**
	 * Array to check duplicate of prodicts
	 */
	$checked_ids = array();
	?>

	<br>
	<h2><?php pll_e( 'Участвую в вебинаре', 'tanhit' ); ?></h2>
	<ul class="my-webinars offer-block">

		<?php
		/**
		 * <!-- Блок 3.1 -->
		 */
		$show_my_webinars = array();
		$i = 0;
		$webinar_cnt = 0;
		foreach( $my_webinars as $id=>$product ) :

			if ( 'webinar' != $product[ 'product_cat' ]->slug ) {
				continue;
			}

			/**
			 * Если дата не задано, значит это запись старого вебинара (давнишнего),
			 * его не нужно выводить в "Участвую вебинаре" - по нему (как по товару) ожидается загрузка записи
			 * (видео файла - для того что бы он появился в Мои вебинары и практики)
			 */
			if ( empty( $product[ 'product_date_start' ] ) ) {
				continue;
			}

			/**
			 * If we don't need product in the past then remove comment marks
			 */
			if ( strtotime( $product[ 'product_date_start' ] ) <= strtotime( $now ) ) {
				continue;
			}

			if ( in_array( $product[ 'product_id' ], $checked_ids ) ) {
				continue;
			}
			$checked_ids[] = $product[ 'product_id' ];

			$key = ( $product[ 'product_date_start' ] . $i ) * 1;

			$show_my_webinars[ $key ] = $product;

			$i++;
		endforeach;

		/**
		 * Sort array of webinars by date asc
		 */
		ksort( $show_my_webinars );

		foreach( $show_my_webinars as $id=>$product ) :

			$remain = ( strtotime( $product[ 'product_date_start' ] ) - strtotime( $now ) ) / DAY_IN_SECONDS;

			if ( empty( $product[ 'product_date_start' ] ) ) {
				$ds = '';
			} else {
				$ds = date_i18n( 'd F \<\b\r\>Y', strtotime( $product[ 'product_date_start' ] ) );
			}

			$webinar_cnt++;
			?>
			<li data-product-id="<?php echo $product[ 'product_id' ]; ?>">
				<!-- Купленные вебинары -->
				<span class="item-date"><?php echo $ds; ?></span>
				<a href="<?php echo $product[ 'permalink' ]; ?>" class="item-link" target="_blank"><?php echo $product[ 'product_name' ]; ?></a>
				<a href="/current-webinar/?id=<?php echo $product[ 'product_id' ]; ?>" class="btn-enter"><?php pll_e( 'Вход на вебинар', 'tanhit' ); ?></a>
				<?php if ( $remain > 0 ) {	?>
					<span class="time-remains"><?php pll_e( 'осталось<br>дней:', 'tanhit' ); ?> <?php echo $remain; ?></span>
				<?php } else { ?>
					<span class="time-remains"></span>
				<?php } ?>
			</li>
		<?php endforeach; ?>

		<?php foreach( $show_free_events as $id=>$product ) :
			/**
			 * бесплатные вебинары в будущем
			 */
			$date_start = get_post_meta( $product->id, 'product_date_start', true);

			if ( strtotime( $date_start ) <= strtotime( $now ) ) {
				continue;
			}

			if ( in_array( $product->id, $checked_ids ) ) {
				continue;
			}
			$checked_ids[] = $product->id;

			$remain = ( strtotime( $date_start ) - strtotime( $now ) ) / DAY_IN_SECONDS;

			$webinar_cnt++;

			?>
			<li data-product-id="<?php echo $product->id; ?>">
				<!--  ВЕБИНАРЫ С НУЛЕВОЙ ЦЕНОЙ, которые будут в будущем      -->
				<span class="item-date"><?php echo date_i18n( 'd F \<\b\r\>Y', strtotime( $date_start ) ); ?></span>
				<a href="<?php echo get_the_permalink( $product->id ); ?>" class="item-link" target="_blank"><?php echo $product->post->post_title; ?></a>
				<a href="/current-webinar/?id=<?php echo $product->id; ?>" class="btn-enter"><?php pll_e( 'Вход на вебинар', 'tanhit' ); ?></a>
				<span class="time-remains"><?php pll_e( 'осталось<br>дней:', 'tanhit' ); ?> <?php echo $remain; ?></span>
			</li>
		<?php endforeach; ?>

		<?php if ($webinar_cnt>0) { ?>
			<li class="emptyli"></li>
			<?php
		}


		/**
		 * <!-- Блок 3.2 -->
		 */
		foreach( $future_webinars as $webinar ) : 
			/**
			 * 1 платный вебинар в будущем
			 */
			//if ( array_key_exists( $webinar->ID , $tanhit_customer_products ) ) {
				//continue;	
			//}

			// Skip free webinars
			if ( $webinar->price == 0 ) {
				continue;
			}
			
			if ( in_array( $webinar->ID, $checked_ids ) ) {
				continue;	
			}	
			$checked_ids[] = $webinar->ID;

			$webinar_cnt++;
			
			?>
			<!-- Отображать ближайший вебинар -->
			<!-- НЕ  Купленный вебинар и вебинар с НЕ нулевой ценой (ближайший вебинар с нулевой ценой будет в первой строке) -->			
			<li data-product-id="<?php echo $webinar->ID; ?>">
				<span class="item-date"><?php echo date_i18n( 'd F \<\b\r\>Y', strtotime( $webinar->product_date_start ) ); ?></span>
				<span class="item-link" style="">
					<a href="<?php echo $webinar->permalink; ?>" class="item-link" target="_blank"><?php echo $webinar->post_title; ?></a>
				</span>
				<span class="item-price" style="">
					<?php echo $webinar->price_html; ?>
				</span>
				<span class="item-button">
					<?php if ( 'variable' == $webinar->product_type ) { ?>
						<a rel="nofollow"  href="<?php echo $webinar->permalink; ?>" class="btn-cart">
							<?php pll_e( 'Выбрать', 'tanhit' ); ?>
						</a>
					<?php } else { ?>
						<a rel="nofollow" 
							href="<?php echo $myaccount_url . '?add-to-cart=' . $webinar->ID; ?>" class="btn-cart">
							<?php pll_e( 'В библиотеку', 'tanhit' ); ?>
						</a>
					<?php }	?>	
				</span>
				<span class="time-remains"><?php pll_e( 'осталось<br>дней:', 'tanhit' ); ?> <?php echo $webinar->days_remain; ?></span>
			</li>	<?php
			break; // we need one webinar only
		endforeach; 	?>	
	</ul>

	<?php if ($webinar_cnt==0) {
		pll_e( 'Здесь Вы сможете участвовать в онлайн вебинарах с Танит. Пока они ищут свое место в расписании.', 'tanhit' );
	}
	
}

add_action( 'tanhit_my_account', '_tanhit_before_my_account_seminars', 30 );
function _tanhit_before_my_account_seminars() {
	
	global $tanhit_customer_products;

	$now = date( 'Ymd', time() );
	
	$args = array(
		'post_type' 	 => 'product',
		'posts_per_page' => -1,
		'product_cat'	 => 'seminar',
		'post_status'    => 'publish',
		'meta_key'   	 => 'product_date_start',
		'orderby'    	 => 'meta_value_num',
		'order'      	 => 'ASC',
		'meta_query'	 => array(
			array(
				'key'     => 'product_date_start',
				'value'   => '\d*',
				'compare' => 'REGEXP',
			)
		)
	);

    $loop = new WP_Query( $args );

	$seminars = array();	
	foreach( $loop->posts as $key=>$product ) {

		$product_date_start = strtotime( get_post_meta( $product->ID, 'product_date_start', true ) ); 

		if ( $product_date_start <= strtotime( $now ) ) {
				continue;	
		}
		
		$seminars[ $key ] = $product;	
		$seminars[ $key ]->days_remain 		  = ( $product_date_start - strtotime( $now ) ) / DAY_IN_SECONDS;
		$seminars[ $key ]->product_date_start = date( 'd.m.Y', $product_date_start );	
		$seminars[ $key ]->permalink		  = get_the_permalink( $product->ID );
		
		$pr = wc_get_product( $product->ID );
		$seminars[ $key ]->price_html 	= $pr->get_price_html();
		$seminars[ $key ]->product_type = $pr->product_type;
		


	}	
	?>
	<h2><?php pll_e( 'Участвую в семинаре', 'tanhit' ); ?></h2>
	<ul class="my-seminars offer-block">

		<?php foreach( $tanhit_customer_products as $id=>$product ) : 

			if ( 'seminar' != $product[ 'product_cat' ]->slug ) {
				continue;	
			}
			
			if ( strtotime( $product[ 'product_date_start' ] ) <= strtotime( $now ) ) {
				continue;	
			}			
			
			$remain = ( strtotime( $product[ 'product_date_start' ] ) - strtotime( $now ) ) / DAY_IN_SECONDS;
			?>
			<!-- Список купленных семинаров -->
			<li>
				<!-- Купленный семинар -->
				<span class="item-date"><?php echo date_i18n( 'd F \<\b\r\>Y', strtotime( $product[ 'product_date_start' ] ) ); ?></span>
				<a href="<?php echo $product[ 'permalink' ]; ?>" class="item-link" target="_blank"><?php echo $product[ 'product_name' ]; ?></a>
				<span class="time-remains"><?php pll_e( 'осталось<br>дней:', 'tanhit' ); ?> <?php echo $remain; ?></span>
			</li>
		<?php endforeach; ?>

		<li class="emptyli"></li>

		<!-- Список не купленных семинаров - ближайшие 5 -->
		<?php
		/**
		 * Seminar limit
		 */
		$i = 5;
		foreach( $seminars as $seminar ) : 
			if ( array_key_exists( $seminar->ID , $tanhit_customer_products ) ) {
				continue;	
			}				?>
			<li>
				<!-- НЕ  Купленный семинар -->
				<span class="item-date"><?php echo date_i18n( 'd F \<\b\r\>Y', strtotime( $seminar->product_date_start ) ); ?></span>
				<span class="item-link" style="">
					<a href="<?php echo $seminar->permalink; ?>" class="item-link"><?php echo $seminar->post_title; ?></a>
				</span>
				<span class="item-price" style="">
                    <?php pll_e( 'Предоплата:', 'tanhit' ); ?><br>
					<?php echo $seminar->price_html; ?>
				</span>
				<span class="item-button">
					<?php if ( 'variable' == $seminar->product_type ) { ?>
						<a rel="nofollow" href="<?php echo $seminar->permalink; ?>" class="btn-cart"><?php pll_e( 'Участвовать', 'tanhit' ); ?></a>
					<?php } else {	?>		
						<a rel="nofollow" 
							href="<?php echo wc_get_page_permalink( 'myaccount' ) . '?add-to-cart=' . $seminar->ID;?>" 	class="btn-cart"><?php pll_e( 'Участвовать', 'tanhit' ); ?></a>
					<?php } 	?>
				</span>	
				<span class="time-remains"><?php pll_e( 'осталось<br>дней:', 'tanhit' ); ?> <?php echo $seminar->days_remain; ?></span>
			</li>		<?php
			$i--;
			if ( $i < 1 ) {
				break;
			}	
		endforeach;	?>

	</ul>
	<?php
}