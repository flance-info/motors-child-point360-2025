<?php
$default_types = array(
	'label' => $default_lt_label,
	'slug' => stm_listings_post_type(),
	'add_page' => stm_me_get_wpcfto_mod('user_add_car_page'),
);
$listings = STMMultiListing::stm_get_listings();
array_unshift($listings, $default_types);
?>

<div class="listing-type-list multilisting-buttons-wrap">
	<?php if (!empty($listings)) : ?>
		<div class="stm-row">
			<?php foreach ($listings as $key => $listing) : ?>
				<?php
				if ('listings' === $listing['slug'] && !empty($default_lt_icon) && is_array($default_lt_icon) && isset($default_lt_icon['value'])) {
					$icon = $default_lt_icon['value'];
				} else {
					$icon = stm_multilisting_get_type_icon_by_slug($listing['slug']);
				}
				$add_item_link = (!empty($listing['add_page']) && is_numeric($listing['add_page'])) ? get_permalink($listing['add_page']) : '#!';
				?>
				<div class="stm-col-3 m-b-15">
					<div class="card">
						<div class="card-body">

							<div class="card-icon fas fa-<?php echo esc_html(strtolower($listing['slug'])); ?>">
								<?php if (!empty($icon)) : ?>
									<i class="<?php echo esc_attr($icon); ?>"></i>
								<?php endif; ?>

							</div>

							<h6 class="card-title"><?php echo esc_html($listing['label']); ?></h6>

							<?php if ($add_item_link) : ?>
								<a href="<?php echo esc_url($add_item_link); ?>" class="btn btn-primary">
									<i class="fas fa-arrow-right"></i>
								</a>
							<?php endif; ?>

						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else : ?>

		<p><?php esc_html_e('Sorry, no listing types found', 'motors_listing_types'); ?></p>

	<?php endif ?>

	<?php wp_reset_postdata(); ?>
</div>
