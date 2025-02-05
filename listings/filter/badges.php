<?php
$filter_badges = stm_get_filter_badges();
if (!empty($filter_badges)): ?>
    <div class="stm-filter-chosen-units">
        <ul class="stm-filter-chosen-units-list">
            <?php foreach ($filter_badges as $badge => $badge_info) :

				if ( $badge_info['slug'] !== 'stm-layout'):
				?>
                <li>
                    <?php if ( !empty( $badge_info['name'] ) ): ?>
                        <span><?php stm_dynamic_string_translation_e('Filter Badge Name', $badge_info['name']); ?>: </span>
                    <?php endif; ?>
                    <?php echo str_replace('\\', '', $badge_info['value'] ); ?>
                    <i data-url="<?php echo esc_url( $badge_info['url'] ); ?>"
                       data-type="<?php echo esc_attr($badge_info['type']); ?>"
                       data-slug="<?php echo esc_attr($badge_info['slug']); ?>"
                       class="fas fa-times stm-clear-listing-one-unit stm-clear-listing-one-unit-classic"></i>
                </li>
            <?php
				endif;
				endforeach; ?>
        </ul>
    </div>
<?php endif; ?>