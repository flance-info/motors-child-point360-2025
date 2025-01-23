<?php
remove_action('wp_ajax_stm_ajax_add_a_car_media', 'stm_ajax_add_a_car_media');
remove_action('wp_ajax_nopriv_stm_ajax_add_a_car_media', 'stm_ajax_add_a_car_media');


/**
 * Car media
 */


add_action('wp_ajax_stm_ajax_add_a_car_media_child', 'stm_ajax_add_a_car_media_child', 99);
add_action('wp_ajax_nopriv_stm_ajax_add_a_car_media_child', 'stm_ajax_add_a_car_media_child', 99);
function stm_ajax_add_a_car_media_child()
{

    write_log('connect child');
    if (stm_is_site_demo_mode()) {
        wp_send_json(array('message' => esc_html__('Site is on demo mode', 'stm_vehicles_listing')));
        exit;
    }

    $redirect_type = (isset($_POST['redirect_type'])) ? $_POST['redirect_type'] : '';
    $post_id = intval($_POST['post_id']);
    if (!$post_id) {
        /*No id passed from first ajax Call?*/
        wp_send_json(array('message' => esc_html__('Some error occurred, try again later', 'stm_vehicles_listing')));
        exit;
    }

    $user_id = get_current_user_id();
    $limits = stm_get_post_limits($user_id);

    $updating = !empty($_POST['stm_edit']) && 'update' === $_POST['stm_edit'];

    if (intval(get_post_meta($post_id, 'stm_car_user', true)) !== intval($user_id)) {
        /*User tries to add info to another car*/
        wp_send_json(array('message' => esc_html__('You are trying to add car to another car user, or your session has expired, please sign in first', 'stm_vehicles_listing')));
        exit;
    }

    $attachments_ids = array();
    $previews_ids = array();
    foreach ($_POST as $get_media_keys => $get_media_values) {
        if (strpos($get_media_keys, 'media_position_') !== false) {
            $attachments_ids[str_replace('media_position_', '', $get_media_keys)] = intval($get_media_values);
        }
    }

    $error = false;
    $response = array(
        'message' => '',
        'post' => $post_id,
        'errors' => array(),
    );

    $files_approved = array();
    $files_approved_preview = array();

    if (!empty($_FILES)) {

        $max_file_size = apply_filters('stm_listing_media_upload_size', 1024 * 4000); /*4mb is highest media upload here*/

        $max_uploads = intval($limits['images']) - count($attachments_ids);

        if (isset($_FILES['files'])) {
            if (count($_FILES['files']['name']) > $max_uploads) {
                $error = true;
                $response['message'] = sprintf(esc_html__('Sorry, you can upload only %d images per add', 'stm_vehicles_listing'), $max_uploads);
            } else {
                // Check if user is trying to upload more than the allowed number of images for the current post
                foreach ($_FILES['files']['name'] as $f => $name) {
                    $check_file_type = wp_check_filetype($name, null);

                    if (!$check_file_type['ext']) {
                        $response['message'] = esc_html__('Sorry, you are trying to upload the wrong image format', 'stm_vehicles_listing') . ': ' . $name;
                        $error = true;
                    } elseif (count($files_approved) === $max_uploads) {
                        break;
                    } elseif (UPLOAD_ERR_OK !== $_FILES['files']['error'][$f]) {
                        $error = true;
                    } else {
                        // Check if image size is larger than the allowed file size

                        // Check if the file being uploaded is in the allowed file types
                        $check_image = getimagesize($_FILES['files']['tmp_name'][$f]);
                        if ($_FILES['files']['size'][$f] > $max_file_size) {
                            $response['message'] = esc_html__('Sorry, image is too large', 'stm_vehicles_listing') . ': ' . $name;
                            $error = true;
                        } elseif (empty($check_image)) {
                            $response['message'] = esc_html__('Sorry, image has invalid format', 'stm_vehicles_listing') . ': ' . $name;
                            $error = true;
                        } else {
                            $tmp_name = $_FILES['files']['tmp_name'][$f];
                            $error = $_FILES['files']['error'][$f];
                            $type = $_FILES['files']['type'][$f];
                            $files_approved[$f] = compact('name', 'tmp_name', 'type', 'error');
                        }
                    }
                }
            }
        }


        if (isset($_FILES['video_preview'])) {
            if (count($_FILES['video_preview']['name']) > $max_uploads) {
                $error = true;
                $response['message'] = sprintf(esc_html__('Sorry, you can upload only %d images per add', 'stm_vehicles_listing'), $max_uploads);
            } else {
                // Check if user is trying to upload more than the allowed number of images for the current post
                foreach ($_FILES['video_preview']['name'] as $f => $name) {
                    $check_file_type = wp_check_filetype($name, null);

                    if (!$check_file_type['ext']) {
                        $response['message'] = esc_html__('Sorry, you are trying to upload the wrong image format', 'stm_vehicles_listing') . ': ' . $name;
                        $error = true;
                    } elseif (count($files_approved_preview) === $max_uploads) {
                        break;
                    } elseif (UPLOAD_ERR_OK !== $_FILES['video_preview']['error'][$f]) {
                        $error = true;
                    } else {
                        // Check if image size is larger than the allowed file size

                        // Check if the file being uploaded is in the allowed file types
                        $check_image = getimagesize($_FILES['video_preview']['tmp_name'][$f]);
                        if ($_FILES['video_preview']['size'][$f] > $max_file_size) {
                            $response['message'] = esc_html__('Sorry, image is too large', 'stm_vehicles_listing') . ': ' . $name;
                            $error = true;
                        } elseif (empty($check_image)) {
                            $response['message'] = esc_html__('Sorry, image has invalid format', 'stm_vehicles_listing') . ': ' . $name;
                            $error = true;
                        } else {
                            $tmp_name = $_FILES['video_preview']['tmp_name'][$f];
                            $error = $_FILES['video_preview']['error'][$f];
                            $type = $_FILES['video_preview']['type'][$f];
                            $files_approved_preview[$f] = compact('name', 'tmp_name', 'type', 'error');
                        }
                    }
                }
            }
        }
    }

    if ($error) {
        if (!$updating) {
            wp_delete_post($post_id, true);
        }
        wp_send_json($response);
        exit;
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';


    foreach ($files_approved as $f => $file) {
        $uploaded = wp_handle_upload($file, array('action' => 'stm_ajax_add_a_car_media_child'));

        if ($uploaded['error']) {
            $response['errors'][$file['name']] = $uploaded;
            continue;
        }

        $filetype = wp_check_filetype(basename($uploaded['file']), null);

        // Insert attachment to the database
        $attach_id = wp_insert_attachment(
            array(
                'guid' => $uploaded['url'],
                'post_mime_type' => $filetype['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($uploaded['file'])),
                'post_content' => '',
                'post_status' => 'inherit',
            ),
            $uploaded['file'],
            $post_id
        );

        // Generate meta data
     //   wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $uploaded['file']));

        $attachments_ids[$f] = $attach_id;
    }

    foreach ($files_approved_preview as $f => $file) {
        $uploaded = wp_handle_upload($file, array('action' => 'stm_ajax_add_a_car_media_child', 'test_form' => false));


        if (isset($uploaded['error']) && !empty($uploaded['error'])) {
            $response['errors'][$file['name']] = $uploaded;
            continue;
        }

        $filetype = wp_check_filetype(basename($uploaded['file']), null);

        // Insert attachment to the database
        $attach_id = wp_insert_attachment(
            array(
                'guid' => $uploaded['url'],
                'post_mime_type' => $filetype['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($uploaded['file'])),
                'post_content' => '',
                'post_status' => 'inherit',
            ),
            $uploaded['file']
        );

        // For video files, we only need basic metadata
        $metadata = array(
            'file' => _wp_relative_upload_path($uploaded['file'])
        );
        wp_update_attachment_metadata($attach_id, $metadata);

        $previews_ids[$f] = $attach_id;
    }

    if (!empty($previews_ids)) {
        update_post_meta($post_id, 'gallery_videos_posters', $previews_ids);
    }



    $current_attachments = get_posts(
        array(
            'fields' => 'ids',
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'post_parent' => $post_id,
        )
    );

    $delete_attachments = array_diff($current_attachments, $attachments_ids);
    foreach ($delete_attachments as $delete_attachment) {
        stm_delete_media(intval($delete_attachment));
    }

    ksort($attachments_ids);
    if (!empty($attachments_ids)) {
        update_post_meta($post_id, '_thumbnail_id', reset($attachments_ids));
        array_shift($attachments_ids);
    }

    update_post_meta($post_id, 'gallery', $attachments_ids);
    do_action('stm_after_listing_gallery_saved', $post_id, $attachments_ids);


    if ($updating) {
        $response['message'] = esc_html__('Car updated, redirecting to your account profile', 'stm_vehicles_listing');

        $to = get_bloginfo('admin_email');

        $args = array(
            'user_id' => $user_id,
            'car_id' => $post_id,
        );

        $subject = stm_generate_subject_view('update_a_car', $args);
        $body = stm_generate_template_view('update_a_car', $args);

        if ('edit-ppl' === $redirect_type) {
            $args = array(
                'user_id' => $user_id,
                'car_id' => $post_id,
                'revision_link' => getRevisionLink($post_id),
            );
            $subject = stm_generate_subject_view('update_a_car_ppl', $args);
            $body = stm_generate_template_view('update_a_car_ppl', $args);
        }
    } else {
        $response['message'] = esc_html__('Listing added, redirecting to your account profile', 'stm_vehicles_listing');

        $to = get_bloginfo('admin_email');
        $args = array(
            'user_id' => $user_id,
            'car_id' => $post_id,
        );
        $subject = stm_generate_subject_view('add_a_car', $args);
        $body = stm_generate_template_view('add_a_car', $args);
    }

    add_filter('wp_mail_content_type', 'stm_set_html_content_type_mail');
    if (apply_filters('stm_listings_notify_updated', true)) {
        wp_mail($to, $subject, apply_filters('stm_listing_saved_email_body', $body, $post_id, $updating));

        if (stm_me_get_wpcfto_mod('send_email_to_user', false) && !$updating && 'pending' === get_post_status($post_id)) {
            $email = get_userdata($user_id);
            $to = $email->user_email;

            $args = array(
                'car_id' => $post_id,
                'car_title' => get_the_title($post_id),
            );

            $subject = stm_generate_subject_view('user_listing_wait', $args);
            $body = stm_generate_template_view('user_listing_wait', $args);

            wp_mail($to, $subject, $body);
        }
    }
    remove_filter('wp_mail_content_type', 'stm_set_html_content_type_mail');

    $response['success'] = true;

    $checkout_url = '';

    // multilisting
    $current_listing_type = get_post_type($post_id);
    $dealer_ppl = stm_me_get_wpcfto_mod('dealer_pay_per_listing', false);
    $pay_per_listing_price = 0;

    if (stm_is_multilisting()) {
        if (stm_listings_post_type() !== $current_listing_type) {

            $ml = new STMMultiListing();

            if ($ml->stm_get_listing_type_settings('inventory_custom_settings', $current_listing_type) === true) {

                $custom_dealer_ppl = $ml->stm_get_listing_type_settings('dealer_pay_per_listing', $current_listing_type);
                $custom_ppl_price = $ml->stm_get_listing_type_settings('pay_per_listing_price', $current_listing_type);

                if ($custom_dealer_ppl) {
                    $dealer_ppl = $custom_dealer_ppl;
                }

                if (!empty($custom_ppl_price)) {
                    $pay_per_listing_price = $custom_ppl_price;
                }
            }
        }
    }

    if (class_exists('WooCommerce') && $dealer_ppl && !$updating && !empty($redirect_type) && 'pay' === $redirect_type) {

        $user_roles = wp_get_current_user()->roles;
       
        if (in_array('administrator', $user_roles) || in_array('subscriber', $user_roles)) {
            $payment_response = create_and_process_automatic_order($post_id, $pay_per_listing_price);
            $checkout_url = $response['url'] = $payment_response['url'];
            $response['message'] = $payment_response['message'];
        }else{
            update_post_meta($post_id, '_price', $pay_per_listing_price);
            update_post_meta($post_id, 'pay_per_listing', 'pay');
            empty_woocommerce_cart_if_not_empty();
            $checkout_url = wc_get_checkout_url() . '?add-to-cart=' . $post_id;
        }

    }

    $response['url'] = (!empty($redirect_type) && 'pay' === $redirect_type) ? $checkout_url : esc_url(get_author_posts_url($user_id));
    if (!empty($redirect_type) && 'pay' === $redirect_type && !$updating) {
        $response['message'] = esc_html__('Listing added, redirecting to checkout', 'stm_vehicles_listing');
    }

    wp_send_json(apply_filters('stm_filter_add_car_media', $response));
    exit;
}

add_action( 'init', 'custom_modify_woocommerce_hooks' );
function custom_modify_woocommerce_hooks() {
	if ( is_listing() || stm_is_dealer_two() ) {
		remove_action( 'woocommerce_checkout_update_order_meta', 'stm_before_create_order', 200 );
		add_action( 'woocommerce_checkout_update_order_meta', 'stm_before_create_order_child', 200, 2 );
	}
}

function stm_before_create_order_child( $order_id, $data ) {
	$cart = WC()->cart->get_cart();
	foreach ( $cart as $cart_item ) {
		$id          = $cart_item['product_id'];
		$post_object = get_post( $cart_item['product_id'] );
		if ( 'product' === $post_object->post_type || 'car_option' === $post_object->post_type ) {
			continue;
		}
		if ( ! empty( get_post_meta( $id, 'car_make_featured_status', true ) ) ) {
			update_post_meta( $id, 'car_make_featured_status', 'processing' );
			update_post_meta( $order_id, 'car_make_featured_id', $id );
		} elseif ( ! empty( get_post_meta( $id, 'is_sell_online_status', true ) ) ) {
			if (in_array( $post_object->post_type, ['listings' , 'caravan', 'motor', 'mc-atv'] )) {
				update_post_meta( $id, 'is_sell_online_status', 'processing' );
				update_post_meta( $order_id, 'is_sell_online_car_id', $id );
			}
		} else {
			update_post_meta( $order_id, 'order_pay_per_listing_id', $id );
			update_post_meta( $id, 'pay_per_order_id', $order_id );
		}
	}

	return true;
}

