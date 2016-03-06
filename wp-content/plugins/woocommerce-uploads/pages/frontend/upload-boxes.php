<div id="wpf-umf-uploads-wrapper">

    <div class="wpf-umf-header before">
        <h2><?php _e('Upload files', $this->plugin_id); ?></h2>
    </div>

    <?php if (isset($upload_mode) && $upload_mode == 'before')
            do_action('wpf_umf_before_upload_description', $cart_product_data); ?>

    <div id="wpf-umf-upload-description">

        <?php echo stripslashes(get_option('wpf_umf_message_upload_description')); ?>

    </div>

    <?php if (get_option('wpf_umf_uploader') == 'ajax'): ?>

        <div id="wpf-umf-browser-check">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>

    <?php else: ?>

        <div id="wpf-umf-uploading"><?php _e('Uploading', $this->plugin_id); ?>...</div>

    <?php endif; ?>

    <div id="wpf-umf-upload-boxes">

        <form method="post" enctype="multipart/form-data">

        <?php wp_nonce_field('uploads', '_wpf_umf_nonce'); ?>
        <input type="hidden" name="wpf_umf_order_id" value="<?php echo (isset($order_number))?$order_number:''; ?>" />

        <?php if (is_array($upload_products)): ?>

            <?php $m = 1; ?>

            <?php foreach ($upload_products AS $product_id => $product): ?>

                <fieldset>

                    <legend><?php echo $product['name']; ?> <?php echo (!empty($product['variation']))?'<span class="wpf-umf-upload-variation"> - '.$product['variation'].'</span>':''; ?></legend>

                    <?php foreach ($product['boxes'] AS $item_number => $boxes): ?>

                        <div class="wpf-umf-item-product-item">

                            <div class="wpf-umf-item-product-item-number"><?php echo (get_option('wpf_umf_upload_procedure') == 'multiple')?sprintf( __('Upload(s) for item #%d', $this->plugin_id), $item_number):sprintf( _n('Upload(s) for one item of this product', 'Upload(s) for %d items of this product', $product['quantity'], $this->plugin_id), $product['quantity']); ?></div>

                            <?php foreach ($boxes AS $box_id => $box): ?>

                                <?php

                                if (isset($current_uploads[$product_id][$item_number][$box_id]))
                                    $current_upload = $current_uploads[$product_id][$item_number][$box_id];

                                ?>

                                <?php $upload_info = array(
                                    'product_id' => $product_id,
                                    'item_number' => $item_number,
                                    'uploader_type' => $box_id
                                ); ?>

                                <div class="wpf-umf-single-upload">

                                    <div class="wpf-umf-single-upload-title"><?php echo $box['title']; ?></div>
                                    <div class="wpf-umf-single-upload-description"><?php echo $box['description']; ?></div>

                                        <?php if (get_option('wpf_umf_uploader') == 'ajax'): ?>

                                            <div id="wpf-umf-single-upload-field-<?php echo $m; ?>" class="wpf-umf-single-upload-field" <?php echo ($box['blocktype'] == 'allow')?'data-allowed="'.$box['filetypes'].'"':''; ?>data-productid="<?php echo $product_id; ?>" data-uploadtype="<?php echo $box_id; ?>" data-itemnumber="<?php echo $item_number; ?>" data-maxuploads="<?php echo $box['amount']; ?>" data-maxfilesize="<?php echo $box['maxuploadsize']; ?>" data-uploadmode="<?php echo (empty($upload_mode))?'after':$upload_mode; ?>">

                                                <?php if (get_option('wpf_umf_uploader_dropzone') == 1): ?>
                                                    <div id="wpf-umf-dropzone-<?php echo $m; ?>" class="wpf-umf-dropzone"><?php echo strtoupper(__('Drop your files', $this->plugin_id)); ?></div>
                                                <?php endif; ?>

                                                <div class="wpf-umf-file-list"></div>
                                                <div class="wpf-umf-error-el"></div>

                                                <div class="wpf-umf-single-upload-buttons">
                                                    <a id="browse-<?php echo $m; ?>" class="button wpf-umf-browse-button" href="javascript:;"><?php _e('Select files', $this->plugin_id); ?></a>
                                                    <?php if (get_option('wpf_umf_uploader_autostart') != 1): ?>
                                                        <a id="upload-<?php echo $m; ?>" class="button wpf-umf-upload-button"  href="javascript:;"><?php _e('Start upload', $this->plugin_id); ?></a>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="wpf-umf-uploaded-files-container">

                                                    <?php if (isset($current_upload) && is_array($current_upload)): ?>

                                                        <?php foreach ($current_upload AS $file_number => $upload):
                                                            $upload_info['file_number'] = $file_number;
                                                        ?>

                                                            <?php $mode = 'ajax'; ?>

                                                            <?php echo include((($upload_mode == 'before')?$mp->plugin_dir:$this->plugin_dir) . 'pages/frontend/_single-uploaded-file.php'); ?>

                                                        <?php endforeach; ?>

                                                    <?php endif; ?>

                                                </div>

                                            </div>

                                        <?php else: ?>

                                            <?php for($c=1; $c<=$box['amount']; $c++): ?>

                                                <?php
                                                $upload = $current_upload[$c];
                                                if (!empty($upload['name'])):
                                                    $upload_info['file_number'] = $c;
                                                ?>
                                                    <?php $mode = 'html'; ?>

                                                    <?php echo include((($upload_mode == 'before')?$mp->plugin_dir:$this->plugin_dir) . 'pages/frontend/_single-uploaded-file.php'); ?>

                                                <?php else: ?>

                                                <div class="wpf-umf-single-upload-field">

                                                    <input type="file" name="wpf_upload[<?php echo $product_id; ?>][<?php echo $item_number; ?>][<?php echo $box_id ?>][<?php echo $c; ?>]" />

                                                    <?php if (!empty($html_post_response[$product_id][$item_number][$box_id][$c]['error'])): ?>
                                                        <div class="wpf-umf-error-el wpf-umf-html-error"><?php echo $html_post_response[$product_id][$item_number][$box_id][$c]['error']; ?></div>
                                                    <?php endif; ?>

                                                </div>

                                                <?php endif; ?>

                                                <?php unset($upload); ?>

                                            <?php endfor; ?>

                                        <?php endif; ?>

                                         <div class="wpf-umf-single-upload-notice">

                                            <?php echo ($box['blocktype'] == 'disallow')?__('Disallowed filetype(s):', $this->plugin_id):__('Allowed filetype(s):', $this->plugin_id); ?> <?php echo $box['filetypes']; ?> | <?php _e('Max. uploads:', $this->plugin_id); ?> <?php echo $box['amount']; ?> | <?php _e('Max. filesize:', $this->plugin_id); ?> <?php echo $box['maxuploadsize']; ?>MB

                                            <?php if (!empty($box['min_resolution_width'])) echo ' | '.__('Min. width:', $this->plugin_id).' '.$box['min_resolution_width'].'px'; ?>
                                            <?php if (!empty($box['min_resolution_height'])) echo ' | '.__('Min. height:', $this->plugin_id).' '.$box['min_resolution_height'].'px'; ?>
                                            <?php if (!empty($box['max_resolution_width'])) echo ' | '.__('Max. width:', $this->plugin_id).' '.$box['max_resolution_width'].'px'; ?>
                                            <?php if (!empty($box['max_resolution_height'])) echo ' | '.__('Max. height:', $this->plugin_id).' '.$box['max_resolution_height'].'px'; ?>

                                         </div>

                                        <?php $m++; ?>

                                </div>

                                <?php
                                unset($current_upload);
                                unset($total_uploads);
                                ?>

                            <?php endforeach; ?>

                        </div>

                    <?php endforeach; ?>

                </fieldset>

            <?php endforeach; ?>

        <?php endif; ?>

        <?php if (get_option('wpf_umf_uploader') != 'ajax'): ?>

            <?php if ($upload_mode == 'before'): ?>

                <input type="hidden" name="wpf_umf_upload_mode" value="before" />
                <?php
                if (is_array($this->cart_product_data)):
                foreach ($this->cart_product_data AS $key => $value): ?>

                    <input type="hidden" name="wpf_umf_<?php echo $key; ?>" value="<?php echo $value; ?>" />

                <?php
                endforeach;
                endif; ?>

            <?php endif; ?>

            <input type="submit" class="button" value="<?php _e('Upload', $this->plugin_id); ?>" />
        <?php endif; ?>

        </form>

    </div>

    <?php if (isset($upload_mode) && $upload_mode == 'before'): ?>

        <div class="wpf-umf-add-to-cart">
            <a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" class="wpf-umf-view-cart-button button"><?php _e('View Cart', 'woocommerce'); ?></a>
            <div class="clear"></div>
        </div>

    <?php endif; ?>

</div>