<?php

$html = '
<div class="wpf-umf-single-uploaded-file '.$upload['extension'].' '.$upload['status'].'" data-filenumber="'.$upload_info['file_number'].'">';

    $html .= '<div class="wpf-umf-suf-delete-wrapper alignleft">';
    if (get_option('wpf_umf_customer_delete') == 1 && $upload['status'] !== 'approved') {

        if (!isset($mode))
            $mode = '';

        $html .=  '
        <div class="wpf-umf-suf-delete">
            <a href="" title="'.__('Delete this upload', $this->plugin_id).'" class="wpf-umf-suf-delete-button" data-productid="'.$upload_info['product_id'].'" data-itemnumber="'.$upload_info['item_number'].'" data-uploadertype="'.$upload_info['uploader_type'].'" data-filenumber="'.$upload_info['file_number'].'" data-mode="'.$mode.'" data-uploadmode="'.(!empty($upload_mode)?$upload_mode:'after').'"><span class="dashicons dashicons-trash"></span></a>
        </div>';

    }
    $html .= '</div>';


    $html .=  '

    <div class="wpf-umf-suf-thumb alignleft">';

        if (!empty($upload['thumb']) && get_option('wpf_umf_thumbnail_enable') == 1) {

            $html .= '<img src="'.WPF_Uploads::create_secret_image_url($upload['thumb']).'" width="'.get_option('wpf_umf_thumbnail_size_width').'" height="'.get_option('wpf_umf_thumbnail_size_height').'" />';

        } else {

            $html .= '<div class="wpf-umf-suf-file-img">'.$upload['extension'].'</div>';

        }

    $html .= '</div>

    <div class="wpf-umf-suf-info alignleft">

        <div class="wpf-umf-suf-file-name">'.$upload['name'].'</div>

        <div class="wpf-umf-suf-file-status"> <span class="dashicons dashicons-marker"></span>';


                    switch ($upload['status']) {

                        case 'on-hold':
                            $html .= (get_option('wpf_umf_message_enable') == 1)?get_option('wpf_umf_message_not_checked'):__('Your file will be manually verified.', $this->plugin_id);
                            break;
                        case 'approved':
                            $html .= (get_option('wpf_umf_message_enable') == 1)?get_option('wpf_umf_message_accepted_files'):__('Your file is approved.', $this->plugin_id);
                            break;
                        case 'declined':
                            $html .= (get_option('wpf_umf_message_enable') == 1)?get_option('wpf_umf_message_declined_files'):__('We have found a problem with this file. Please upload a new file.', $this->plugin_id);
                            break;

                    }

      $html .= '

        </div>

    </div>

    <div class="clear"></div>

</div>  ';

return $html;
