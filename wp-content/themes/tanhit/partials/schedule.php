<?php 
/**
 * Output schedule
 *
 * @see meta fields: product_date_start, product_date_end ( value holds in DB in Ymd format ), product_time_start
 */
 
$list = array();

$enabled_types = array(
	'all',
	'webinar',
	'seminar'
);

$enabled_types_value = array(
	'all' => 'Все',
	'webinar' => 'Вебинары',
	'seminar' => 'Семинары'
);

$tanhit_select_key  = 'filter'; 
$select_type = 'all';
if (  ! empty( $_GET[ $tanhit_select_key ] ) && in_array( $_GET[ $tanhit_select_key ], $enabled_types ) ) {
	$select_type = $_GET[ $tanhit_select_key ];
}	

$tax_query = array();

if ( 'all' === $select_type ) {

	$types = $enabled_types;
	unset( $types[0] );

	$tax_query = array(
		array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $types,
		),
	);
} else {	
	$tax_query = array(
		array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $select_type,
		),
	);
}				
 
$args = array(
	'post_type' 	 => 'product',
	'posts_per_page' => -1,
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

if ( ! empty( $tax_query ) ) {
	$args = array_merge( 
		$args, 
		array(
			'tax_query' => $tax_query
		) 
	);
}	

$loop = new WP_Query( $args );

if ( ! empty( $loop->posts ) ) {
	$list = $loop->posts;
}	

if ( ! empty( $list ) ) :
	?>
	<div class="row">
		<div class="col-sm-12 filter">
			<?php echo pll__('Отобразить', 'tanhit' ); ?>:
			<?php /*
            <select onchange="tanhitScheduleTypeSelect(this)" id="select_type" name="select_type">	<?php
				foreach( $enabled_types as $enabled_type ) { 
					$selected = '';
					if ( ! empty( $_GET[ $tanhit_select_key ] ) && $enabled_type == $_GET[ $tanhit_select_key ] ) {
						$selected = 'selected';
					}	?>
					<option value="<?php echo $enabled_type; ?>" <?php echo $selected; ?>><?php echo $enabled_types_value[$enabled_type]; ?></option>	<?php
				}	?>
			</select>
            */?>

    <?php
    foreach( $enabled_types as $enabled_type ) {
        $selected = '';
        if (
            ( ! empty( $_GET[ $tanhit_select_key ] ) && $enabled_type == $_GET[ $tanhit_select_key ] ) ||
            ( empty( $_GET[ $tanhit_select_key ]) && $enabled_type== "all" )
        ){
            $selected = 'class="selected"';
        }	?>
        <a href="?<?php echo $tanhit_select_key.'='.$enabled_type; ?>" <?php echo $selected; ?>><?php echo $enabled_types_value[$enabled_type]; ?></a>
    <?php  }      ?>

		</div>
	</div>
	<?php


	/**
	 * If you need show all events set to false
	 */
	$show_only_future_events = true;
	
    $now = date( 'Ymd', time() );

    ?> <div class="sched-table"> <?php



    $events_cnt=0;

    foreach( $list as $item ) {
	
		//Выводим в списке, даже если дата начала больше текущей
		$fixed_in_shedule = (int)get_post_meta( $item->ID, 'fixed_in_shedule', true );
	
		$date_start = strtotime( get_post_meta( $item->ID, 'product_date_start', true ) );
	
		$action = pll__( 'Участвовать', 'tanhit' ); 
		if ( ! $fixed_in_shedule && $date_start < strtotime( $now ) ) {
			
			if ( $show_only_future_events ) {
				continue;
			}
			/**
			 * obsolete date start
			 */
			$action = pll__( 'Уже состоялся<br />доступен в магазине', 'tanhit' ); 
		}
		?>
		<div class="sched-row">
            <div class="sched-date">
                <?php /* echo date( 'd', $date_start ).' '.$monthes[date( 'm', $date_start )].' '.date( 'Y', $date_start ); */ ?>
                <?php echo date_i18n( 'j F Y', $date_start ); ?>
            </div>
            <div class="sched-desc">
                <?php echo ' <a href="'; ?>
                <?php the_permalink( $item->ID ); ?>
                <?php echo'">' . $item->post_title . '</a>'; ?>
            </div>
            <div class="sched-link">
                <a href="<?php the_permalink( $item->ID ); ?>">
                    <?php echo $action; ?>
                </a>
            </div>
		</div>
		<?php
        $events_cnt++;

	}
?> 	</div> <?php

if ($events_cnt==0) {

    ?>
        <div style="text-align: center; margin-top: 30px; font-weight: bold;">
            <?php pll_e( 'Cобытий пока в расписании нет.', 'tanhit' ); ?>
        </div>
    <?php

}

endif;
