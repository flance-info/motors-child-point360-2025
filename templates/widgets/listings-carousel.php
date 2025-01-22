<?php
if ( empty( $listing_types ) ) {
	$listing_types = stm_listings_post_type();
}

if ( empty( $listings_number ) ) {
	$listings_number = - 1;
}

$args = array(
	'post_type'      => $listing_types,
	'post_status'    => 'publish',
	'posts_per_page' => $listings_number,
);

$args['meta_query'] = array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query

if ( ! empty( $only_featured ) && 'yes' === $only_featured ) {
	$args['meta_query'][] = array(
		'key'     => 'special_car',
		'value'   => 'on',
		'compare' => '=',
	);
}

if ( empty( $include_sold ) || 'yes' !== $include_sold ) {
	$args['meta_query'][] = array(
		'key'     => 'car_mark_as_sold',
		'value'   => '',
		'compare' => '=',
	);
}

if ( empty( $link_text ) ) {
	$link_text = __( 'View all', 'motors-elementor-widgets' );
}

$special_query             = new WP_Query( $args );
$gallery_hover_interaction = stm_me_get_wpcfto_mod( 'gallery_hover_interaction', false );
$unique_id                 = 'selc-' . wp_rand( 1, 99999 );
$class                     = 'listing-cars-carousel';
$sell_online               = stm_me_get_wpcfto_mod( 'enable_woo_online', false );
$image_size                = 'stm-img-350-205';
$listings_per_view         = ( ! empty( $items_per_view ) && is_numeric( $items_per_view ) ) ? $items_per_view : 3;
$listings_per_view_class   = 'items-per-view-' . $listings_per_view;
?>

<div class="stm-elementor_listings_carousel view_type_carousel <?php echo esc_attr( $view_style ); ?>">
	<?php if ( ! empty( $carousel_title ) || ( ! empty( $show_all_link ) && 'yes' === $show_all_link && ! empty( $search_results_link ) ) ) : ?>
		<div class="title heading-font">
			<?php
			if ( ! empty( $carousel_title ) ) {
				echo esc_html( $carousel_title );
			}

			if ( ! empty( $show_all_link ) && 'yes' === $show_all_link && ! empty( $search_results_link ) ) :
				?>
				<a href="<?php echo esc_url( $search_results_link ); ?>" class="all-listings">
					<?php
					if ( ! empty( $link_icon ) && ! empty( $link_icon['value'] ) ) :
						if ( 'svg' === $link_icon['library'] && ! empty( $link_icon['value']['url'] ) ) :
							?>
							<img src="<?php echo esc_attr( $link_icon['value']['url'] ); ?>" class="svg-icon" alt="<?php esc_html_e( 'SVG icon', 'motors-elementor-widgets' ); ?>">
							<?php
						else :
							?>
							<i class="stm-elementor-icon <?php echo esc_attr( $link_icon['value'] ); ?>"></i>
							<?php
						endif;
					endif;
					?>
					<span><?php echo esc_html( $link_text ); ?></span>
				</a>
				<?php
			endif;
			?>
		</div>

		<div class="colored-separator">
			<div class="first-long stm-base-background-color"></div>
			<div class="last-short stm-base-background-color"></div>
		</div>

	<?php endif; ?>
	<?php
	if ( $special_query->have_posts() ) :
		?>
		<div class="listing-car-items-units swiper-container <?php echo esc_attr( $listings_per_view_class ); ?>" id="<?php echo esc_attr( $unique_id ); ?>">
			<div class="listing-car-items swiper-wrapper <?php echo esc_attr( $class ); ?> text-center clearfix">
				<?php
				while ( $special_query->have_posts() ) :
					$special_query->the_post();
					$spec_banner = get_post_meta( get_the_ID(), 'special_image', true );
					if ( empty( $spec_banner ) ) :
						$car_price_form_label = get_post_meta( get_the_ID(), 'car_price_form_label', true );
						$price                = get_post_meta( get_the_ID(), 'price', true );
						$sale_price           = get_post_meta( get_the_ID(), 'sale_price', true );
						$is_sell_online       = ( true === $sell_online ) ? ! empty( get_post_meta( get_the_ID(), 'car_mark_woo_online', true ) ) : false;
						if ( function_exists( 'stm_get_listings_filter' ) ) {
							$labels = stm_get_listings_filter( get_post_type( get_the_ID() ), array( 'where' => array( 'use_on_car_listing_page' => true ) ), false );
						} else {
							$labels = stm_get_car_listings();
						}
						?>
						<div class="dp-in swiper-slide">
							<div class="listing-car-item">
								<div class="listing-car-item-inner">
									<a href="<?php the_permalink(); ?>" class="rmv_txt_drctn" title="
										<?php
										esc_attr_e( 'View full information about', 'motors-elementor-widgets' );
										echo esc_attr( ' ' . get_the_title() );
										?>
									">
										<?php
										if ( has_post_thumbnail() ) :
											?>
											<div class="text-center">
												<?php
												$img    = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $image_size );
												$img_2x = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'stm-img-796-466' );
												?>
												<div class="image dp-in">


													<?php

													if ( true === $gallery_hover_interaction && ! wp_is_mobile() ) {
														$thumbs = stm_get_hoverable_thumbs( get_the_ID(), $image_size );
														if ( empty( $thumbs['gallery'] ) || 1 === count( $thumbs['gallery'] ) ) :
															?>
															<img src="<?php echo esc_url( $img[0] ); ?>" data-retina="<?php echo esc_url( $img_2x[0] ); ?>" class="img-responsive" alt="<?php echo esc_attr( stm_get_img_alt( get_post_thumbnail_id( get_the_ID() ) ) ); ?>">
															<?php
															get_template_part( 'partials/listing-cars/listing-directory', 'badges' );
															$listing_id    = get_the_ID();
															$lowercase_sku = get_points_plan( $listing_id );
															if ( $lowercase_sku ): ?>
																<div class="stm-listing-point"> <?php echo $lowercase_sku; ?></div>
															<?php endif;
															if ( 'style_2' === $view_style ) :
																if ( ! empty( $car_price_form_label ) ) :
																	?>
																	<div class="price heading-font">
																		<div class="normal-price"><?php echo esc_attr( $car_price_form_label ); ?></div>
																	</div>
																	<?php
																else :
																	?>
																	<?php
																	if ( ! empty( $price ) && ! empty( $sale_price ) ) :
																		?>
																		<div class="price heading-font discounted-price">
																			<div class="regular-price">
																				<?php echo esc_attr( stm_listing_price_view( $price ) ); ?>
																			</div>
																			<div class="sale-price">
																				<?php echo esc_attr( stm_listing_price_view( $sale_price ) ); ?>
																			</div>
																		</div>
																		<?php
																	elseif ( ! empty( $price ) ) :
																		?>
																		<div class="price heading-font">
																			<div class="normal-price">
																				<?php echo esc_attr( stm_listing_price_view( $price ) ); ?>
																			</div>
																		</div>
																		<?php
																	endif;
																endif;
															endif;
														else :
															$array_keys    = array_keys( $thumbs['gallery'] );
															$last_item_key = array_pop( $array_keys );
															?>
															<div class="interactive-hoverable">
																<div class="hoverable-wrap">
																	<?php
																	foreach ( $thumbs['gallery'] as $key => $img_url ) :
																		?>
																		<div class="hoverable-unit <?php echo ( 0 === $key ) ? 'active' : ''; ?>">
																			<div class="thumb">
																				<?php if ( $key === $last_item_key && 5 === count( $thumbs['gallery'] ) && 0 < $thumbs['remaining'] ) : ?>
																					<div class="remaining">
																						<i class="stm-icon-album"></i>
																						<p>
																							<?php
																							/* translators: number of remaining photos */
																							echo esc_html( sprintf( _n( '%d more photo', '%d more photos', $thumbs['remaining'], 'motors-elementor-widgets' ), $thumbs['remaining'] ) );
																							?>
																						</p>
																					</div>
																				<?php endif; ?>
																				<?php if ( is_array( $img_url ) ) : ?>
																					<img
																							data-src="<?php echo esc_url( $img_url[0] ); ?>"
																							srcset="<?php echo esc_url( $img_url[0] ); ?> 1x, <?php echo esc_url( $img_url[1] ); ?> 2x"
																							src="<?php echo esc_url( $img_url[0] ); ?>"
																							class="lazy img-responsive"
																							alt="<?php echo esc_attr( get_the_title( get_the_ID() ) ); ?>">
																				<?php else : ?>
																					<img src="<?php echo esc_url( $img_url ); ?>" class="lazy img-responsive" alt="<?php echo esc_attr( get_the_title( get_the_ID() ) ); ?>">
																				<?php endif; ?>
																			</div>
																		</div>
																		<?php
																	endforeach;
																	get_template_part( 'partials/listing-cars/listing-directory', 'badges' );
																	if ( 'style_2' === $view_style ) :
																		if ( ! empty( $car_price_form_label ) ) :
																			?>
																			<div class="price heading-font">
																				<div class="normal-price"><?php echo esc_attr( $car_price_form_label ); ?></div>
																			</div>
																			<?php
																		else :
																			?>
																			<?php
																			if ( ! empty( $price ) && ! empty( $sale_price ) ) :
																				?>
																				<div class="price heading-font discounted-price">
																					<div class="regular-price">
																						<?php echo esc_attr( stm_listing_price_view( $price ) ); ?>
																					</div>
																					<div class="sale-price">
																						<?php echo esc_attr( stm_listing_price_view( $sale_price ) ); ?>
																					</div>
																				</div>
																				<?php
																			elseif ( ! empty( $price ) ) :
																				?>
																				<div class="price heading-font">
																					<div class="normal-price">
																						<?php echo esc_attr( stm_listing_price_view( $price ) ); ?>
																					</div>
																				</div>
																				<?php
																			endif;
																		endif;
																	endif;
																	?>
																</div>
																<div class="hoverable-indicators">
																	<?php
																	$first = true;
																	foreach ( $thumbs['gallery'] as $thumb ) :
																		?>
																		<div class="indicator <?php echo ( $first ) ? 'active' : ''; ?>"></div>
																		<?php
																		$first = false;
																	endforeach;
																	?>
																</div>
															</div>
															<?php
														endif;
													} else {
														?>
														<img src="<?php echo esc_url( $img[0] ); ?>" data-retina="<?php echo esc_url( $img_2x[0] ); ?>" class="img-responsive" alt="<?php echo esc_attr( stm_get_img_alt( get_post_thumbnail_id( get_the_ID() ) ) ); ?>">
														<?php
														get_template_part( 'partials/listing-cars/listing-directory', 'badges' );
														$listing_id    = get_the_ID();
															$lowercase_sku = get_points_plan( $listing_id );
															if ( $lowercase_sku ): ?>
																<div class="stm-listing-point"> <?php echo $lowercase_sku; ?></div>
															<?php endif;
														if ( 'style_2' === $view_style ) :
															if ( ! empty( $car_price_form_label ) ) :
																?>
																<div class="price heading-font">
																	<div class="normal-price"><?php echo esc_attr( $car_price_form_label ); ?></div>
																</div>
																<?php
															else :
																?>
																<?php
																if ( ! empty( $price ) && ! empty( $sale_price ) ) :
																	?>
																	<div class="price heading-font discounted-price">
																		<div class="regular-price">
																			<?php echo esc_attr( stm_listing_price_view( $price ) ); ?>
																		</div>
																		<div class="sale-price">
																			<?php echo esc_attr( stm_listing_price_view( $sale_price ) ); ?>
																		</div>
																	</div>
																	<?php
																elseif ( ! empty( $price ) ) :
																	?>
																	<div class="price heading-font">
																		<div class="normal-price">
																			<?php echo esc_attr( stm_listing_price_view( $price ) ); ?>
																		</div>
																	</div>
																	<?php
																endif;
															endif;
														endif;
													}
													?>
												</div>
											</div>
											<?php
										else :
											?>
											<div class="image dp-in">
												<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/plchldr350.png' ); ?>" class="img-responsive">
											</div>
											<?php
											get_template_part( 'partials/listing-cars/listing-directory', 'badges' );
											$listing_id    = get_the_ID();
															$lowercase_sku = get_points_plan( $listing_id );
															if ( $lowercase_sku ): ?>
																<div class="stm-listing-point stm-place"> <?php echo $lowercase_sku; ?></div>
															<?php endif;
										endif;
										?>
										<div class="listing-car-item-meta">
											<div class="car-meta-top heading-font clearfix">
												<?php
												if ( 'style_1' === $view_style ) :
													if ( stm_is_dealer_two() && $is_sell_online ) :
														if ( ! empty( $sale_price ) ) {
															$price = $sale_price;
														}
														?>
														<div class="sell-online-wrap price">
															<div class="normal-price">
																<span class="normal_font"><?php echo esc_html__( 'BUY ONLINE', 'motors-elementor-widgets' ); ?></span>
																<span class="heading-font"><?php echo esc_attr( stm_listing_price_view( $price ) ); ?></span>
															</div>
														</div>
														<?php
													else :
														if ( ! empty( $car_price_form_label ) ) :
															?>
															<div class="price">
																<div class="normal-price"><?php echo esc_attr( $car_price_form_label ); ?></div>
															</div>
															<?php
														else :
															if ( ! empty( $price ) && ! empty( $sale_price ) ) :
																?>
																<div class="price discounted-price">
																	<div class="regular-price">
																		<?php echo esc_attr( stm_listing_price_view( $price ) ); ?>
																	</div>
																	<div class="sale-price">
																		<?php echo esc_attr( stm_listing_price_view( $sale_price ) ); ?>
																	</div>
																</div>
															<?php elseif ( ! empty( $price ) ) : ?>
																<div class="price">
																	<div class="normal-price">
																		<?php echo esc_attr( stm_listing_price_view( $price ) ); ?>
																	</div>
																</div>
															<?php endif; ?>
														<?php endif; ?>
													<?php endif; ?>
												<?php endif; ?>
												<?php
												if ( 'style_2' === $view_style ) :
													$subtitle = '';
													$car_uear = wp_get_post_terms( get_the_ID(), 'ca-year', array( 'fields' => 'names' ) );
													$body     = wp_get_post_terms( get_the_ID(), 'body', array( 'fields' => 'names' ) );

													if ( ! is_wp_error( $car_uear ) && is_array( $car_uear ) ) {
														$subtitle .= ( $car_uear ) ? '<span>' . $car_uear[0] . '</span> ' : '';
													}

													if ( ! is_wp_error( $body ) && is_array( $body ) ) {
														$subtitle .= ( $body ) ? '<span>' . $body[0] . '</span>' : '';
													}
													?>
													<div class="car-subtitle heading-font">
														<?php echo wp_kses_post( $subtitle ); ?>
													</div>
												<?php endif; ?>
												<div class="car-title">
													<?php echo esc_attr( trim( preg_replace( '/\s+/', ' ', mb_substr( get_the_title(), 0, 35 ) ) ) ); ?>
													<?php
													if ( strlen( get_the_title() ) > 35 ) {
														echo esc_attr( '...' );
													}
													?>
												</div>
											</div>
											<div class="car-meta-bottom">
												<?php $special_text = get_post_meta( get_the_ID(), 'special_text', true ); ?>
												<?php if ( empty( $special_text ) ) : ?>
													<?php if ( ! empty( $labels ) ) : ?>
														<ul>
															<?php foreach ( $labels as $label ) : ?>
																<?php $label_meta = get_post_meta( get_the_ID(), $label['slug'], true ); ?>
																<?php if ( ! empty( $label_meta ) ) : ?>
																	<li class="icon-position-<?php echo esc_attr( $listing_meta_icon_position ); ?>">
																		<?php if ( ! empty( $label['font'] ) ) : ?>
																			<i class="<?php echo esc_attr( $label['font'] ); ?>"></i>
																		<?php endif; ?>

																		<?php if ( ! empty( $label['numeric'] ) && $label['numeric'] ) : ?>
																			<span><?php echo esc_attr( $label_meta ); ?></span>
																		<?php else : ?>

																			<?php
																			$data_meta_array = explode( ',', $label_meta );
																			$datas           = array();

																			if ( ! empty( $data_meta_array ) ) {
																				foreach ( $data_meta_array as $data_meta_single ) {
																					$data_meta = get_term_by( 'slug', $data_meta_single, $label['slug'] );
																					if ( ! empty( $data_meta->name ) ) {
																						$datas[] = esc_attr( $data_meta->name );
																					}
																				}
																			}

																			if ( ! empty( $datas ) ) :
																				if ( count( $datas ) > 1 ) {
																					?>

																					<span
																							class="stm-tooltip-link"
																							data-toggle="tooltip"
																							data-placement="top"
																							title="<?php echo esc_attr( implode( ', ', $datas ) ); ?>">
																						<?php echo esc_html( $datas[0] ) . '<span class="stm-dots dots-aligned">...</span>'; ?>
																					</span>

																				<?php } else { ?>
																					<span><?php echo esc_html( implode( ', ', $datas ) ); ?></span>
																					<?php
																				}
																			endif;
																			?>

																		<?php endif; ?>
																	</li>
																<?php endif; ?>
															<?php endforeach; ?>
														</ul>
													<?php endif; ?>
												<?php else : ?>
													<ul>
														<li>
															<div class="special-text">
																<?php stm_dynamic_string_translation_e( 'Special Text', $special_text ); ?>
															</div>
														</li>
													</ul>
												<?php endif; ?>
											</div>
										</div>
									</a>
								</div>
							</div>
						</div>
					<?php else : ?>
						<div class="dp-in swiper-slide">
							<div class="listing-car-item">
								<div class="listing-car-item-inner">
									<?php $banner_src = wp_get_attachment_image_src( $spec_banner, 'stm-img-350-356' ); ?>
									<?php $banner_src_retina = wp_get_attachment_image_src( $spec_banner, 'full' ); ?>
									<a href="<?php the_permalink(); ?>">
										<img class="img-responsive" src="<?php echo esc_url( $banner_src[0] ); ?>" data-retina="<?php echo esc_url( $banner_src_retina[0] ); ?>" alt="<?php the_title(); ?>"/>
									</a>
								</div>
							</div>
						</div>
					<?php endif; ?>
				<?php endwhile; ?>
			</div>

			<?php if ( ! empty( $navigation ) || ! empty( $pagination ) ) : ?>
				<div class="stm-swiper-controls">
					<?php if ( ! empty( $navigation ) && 'yes' === $navigation ) : ?>
						<div class="stm-swiper-prev"><i class="fas fa-angle-left"></i></div>
					<?php endif; ?>
					<?php if ( ! empty( $pagination ) && 'yes' === $pagination ) : ?>
						<div class="swiper-pagination"></div>
					<?php endif; ?>
					<?php if ( ! empty( $navigation ) && 'yes' === $navigation ) : ?>
						<div class="stm-swiper-next"><i class="fas fa-angle-right"></i></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
		wp_reset_postdata();

	endif;
	?>
</div>

<?php //phpcs:disable ?>
<script>
    (function ($) {
        "use strict";
		<?php
		$is_elementor_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
		if ( ! $is_elementor_editor ) :
		?>
        $(window).on('elementor/frontend/init', function () {
			<?php endif; ?>
            let swiper = new Swiper('#<?php echo esc_attr( $unique_id ); ?>', {
				<?php if ( ! empty( $loop ) && 'yes' === $loop ) : ?>
                loop: true,
				<?php endif; ?>

				<?php if ( ! empty( $click_drag ) && 'yes' === $click_drag ) : ?>
                simulateTouch: true,
				<?php else : ?>
                simulateTouch: false,
				<?php endif; ?>

				<?php if ( ! empty( $autoplay ) && 'yes' === $autoplay ) : ?>
                autoplay: {
					<?php if ( ! empty( $delay ) && is_numeric( $delay ) ) : ?>
                    delay: <?php echo esc_attr( $delay ); ?>,
					<?php else : ?>
                    delay: 3000,
					<?php endif; ?>

					<?php if ( ! empty( $reverse ) && 'yes' === $reverse ) : ?>
                    reverseDirection: true,
					<?php endif; ?>
                },
				<?php endif; ?>

				<?php if ( ! empty( $transition_speed ) && is_numeric( $transition_speed ) ) : ?>
                speed: <?php echo esc_attr( $transition_speed ); ?>,
				<?php endif; ?>

				<?php if ( ! empty( $slides_per_transition ) && is_numeric( $slides_per_transition ) ) : ?>
                slidesPerGroup: <?php echo esc_attr( $slides_per_transition ); ?>,
				<?php endif; ?>
                centerInsufficientSlides: true,

                slidesPerView: 1,

                breakpoints: {
                    640: {
                        slidesPerView: 2,
                    },
                    992: {
                        slidesPerView: <?php echo esc_attr( $listings_per_view ); ?>,
                    }
                },
				<?php if ( ! empty( $navigation ) && 'yes' === $navigation ) : ?>
                navigation: {
                    nextEl: '.stm-swiper-next',
                    prevEl: '.stm-swiper-prev',
                },
				<?php else : ?>
                navigation: false,
				<?php endif; ?>

				<?php if ( ! empty( $pagination ) && 'yes' === $pagination ) : ?>
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
				<?php else : ?>
                pagination: false,
				<?php endif; ?>
            });

			<?php if ( ! empty( $pause ) && 'yes' === $pause ) : ?>
            $('#<?php echo esc_attr( $unique_id ); ?>').hover(function () {
                (this).swiper.autoplay.stop();
            }, function () {
                (this).swiper.autoplay.start();
            });
			<?php
			endif;
			if ( ! $is_elementor_editor ) :
			?>
        });
		<?php endif; ?>
    }(jQuery));
</script>
<?php // phpcs:enable ?>
