<div id="wpf-umf-order-uploads">

    <?php
    foreach ($products AS $product):

         // Variation support
        $product_id  = (!empty($product['variation_id']))?$product['variation_id']:$product['product_id'];

        $item_meta = new WC_Order_Item_Meta( $product['item_meta'] );
        $variation = $item_meta->display($flat=true,$return=true);
    ?>

        <div class="wpf-umf-ou-upload">

            <div class="wpf-umf-ou-upload-product-name"><?php echo $product['name']; ?> <?php echo (!empty($variation))?'<span class="wpf-umf-ou-upload-product-variation"> - '.$variation.'</span>':''; ?></div>

            <?php if(is_array($order_uploads[$product_id])): ?>

                <?php foreach ($order_uploads[$product_id] AS $item_number => $upload_types): ?>

                    <div class="wpf-umf-ou-upload-product-item"><?php printf(__('Item #%s', $this->plugin_id), $item_number); ?></div>

                    <?php foreach ($upload_types AS $upload_type => $uploads): ?>

                        <?php $count = 1; ?>

                        <?php foreach ($uploads AS $number => $upload): ?>

                            <div class="wpf-umf-ou-upload-type"><?php echo $upload['type']; ?> #<?php echo $count; ?></div>

                            <div class="wpf-umf-ou-uploaded-file <?php echo $upload['status']; ?>">
                                <div class="alignleft">
                                    <input type="checkbox" name="wpf_umf_uploaded_file[<?php echo $product_id; ?>][<?php echo $item_number; ?>][<?php echo $upload_type; ?>][<?php echo $number; ?>][status]" value="1" />
                                    <?php echo $upload['name']; ?>
                                </div>

                                <div class="alignright">
                                    <a href="<?php echo $this->create_secret_url($upload['path']); ?>" target="_blank" class="wpf-umf-ou-uploaded-download button button-small button-secondary"> Download</a>
                                </div>

                                <div class="clear"></div>

                            </div>

                        <?php $count++; endforeach; ?>

                    <?php endforeach; ?>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>


    <?php endforeach; ?>

    <input type="hidden" name="wpf_umf_uploaded_order_id" value="<?php echo $order->id; ?>" />
    <?php wp_nonce_field('approve-uploads', '_wpf_umf_uploads_nonce'); ?>

    <div id="wpf-umf-ou-actions">

        <?php _e('With selected:', $this->plugin_id); ?>

        <div class="alignright">

            <select name="wpf_umf_uploaded_file_approve" class="wpf-umf-select-small">
                <option value="accept"><?php _e('Accept', $this->plugin_id); ?></option>
                <option value="decline"><?php _e('Decline', $this->plugin_id); ?></option>
            </select>
            <a href="#" class="button button-small button-primary" id="wpf_umf_uploaded_file_submit"><?php _e('GO', $this->plugin_id); ?></a>

        </div>

        <div class="clear"></div>

    </div>

</div>

<div id="wpf-umf-order-uploads-email">

    <h4><?php _e('Send mail',$this->plugin_id); ?></h4>

    <?php wp_nonce_field('approve-uploads-email', '_wpf_umf_uploads_email_nonce'); ?>
    <input type="hidden" name="wpf_umf_uploads_email_order_id" value="<?php echo $order->id; ?>" />

    <div id="wpf-umf-order-uploads-email-error" class="wpf-umf-order-uploads-email-message hidden"></div>
    <div id="wpf-umf-order-uploads-email-success" class="wpf-umf-order-uploads-email-message hidden"></div>

    <div>

        <label for="wpf_umf_order_uploads_email"><?php _e('Reason accepted / declined:', $this->plugin_id); ?></label>
        <textarea name="wpf_umf_order_uploads_email" id="wpf_umf_order_uploads_email"></textarea>

    </div>

    <div class="alignright">

        <select name="wpf_umf_order_uploads_email_reason" class="wpf-umf-select-small">
            <option value="approved"><?php _e('Files accepted', $this->plugin_id); ?></option>
            <option value="declined"><?php _e('Files rejected', $this->plugin_id); ?></option>
        </select>

        <a href="#" class="button button-small button-primary" id="wpf_umf_uploads_email_submit"><?php _e('Send mail',$this->plugin_id); ?></a>

    </div>

    <div class="clear"></div>

</div>