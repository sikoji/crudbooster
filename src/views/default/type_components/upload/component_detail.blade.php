<?php
$ext = pathinfo($value, PATHINFO_EXTENSION);
$images_type = array('jpg', 'png', 'gif', 'jpeg', 'bmp', 'tiff');

if (env('UPLOAD_TO', 'storage') == "storage"):
    if(Storage::exists($value) || file_exists($value)):
        if(in_array(strtolower($ext), $images_type)):?>
        <a data-lightbox='roadtrip' href='{{asset($value)}}'><img style='max-width:150px' title="Image For {{$form['label']}}" src='{{asset($value)}}'/></a>
        <?php else:?>
        <a href='{{asset($value)}}?download=1' target="_blank">{{trans("crudbooster.button_download_file")}}: {{basename($value)}} <i class="fa fa-download"></i></a>
        <?php endif;?>
     <?php endif;?>
<?php else:?>
    <a data-lightbox='roadtrip' href='{{ env('BASE_CLOUDINARY', 'https://res.cloudinary.com/sikoji')."c_scale,w_1024/".$value }}'><img style='max-width:150px' title="Image For {{$form['label']}}" src='{{ env('BASE_CLOUDINARY', 'https://res.cloudinary.com/sikoji')."c_scale,w_150/".$value }}'/></a>
<?php endif;?>