<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
    <label class='col-sm-2 control-label'>{{$form['label']}}
        @if($required)
            <span class='text-danger' title='{!! trans('crudbooster.this_field_is_required') !!}'>*</span>
        @endif
    </label>

    <div class="{{$col_width?:'col-sm-10'}}">
        @if($value)
            <?php

        if (env('UPLOAD_TO', 'storage') == "storage"):
            if(Storage::exists($value) || file_exists($value)):
                $url = asset($value);
                $ext = pathinfo($url, PATHINFO_EXTENSION);
                $images_type = array('jpg', 'png', 'gif', 'jpeg', 'bmp', 'tiff');
                if(in_array(strtolower($ext), $images_type)):
                ?>
                <p><a data-lightbox='roadtrip' href='{{$url}}'><img style='max-width:160px' title="Image For {{$form['label']}}" src='{{$url}}'/></a></p>
                <?php else:?>
                <p><a href='{{$url}}'>{{trans("crudbooster.button_download_file")}}</a></p>
                <?php endif;
                echo "<input type='hidden' name='_$name' value='$value'/>";
            
            else:
                echo "<p class='text-danger'><i class='fa fa-exclamation-triangle'></i> ".trans("crudbooster.file_broken")."</p>";
            endif;
            ?>
            
        @else
        <input type='hidden' name='{{ "_".$name }}' value='{{ $value }}'/>
        <a data-lightbox='roadtrip' href='{{ env('BASE_CLOUDINARY', 'https://res.cloudinary.com/sikoji')."c_scale,w_1024/".$value }}'><img style='max-width:150px' title="Image For {{$form['label']}}" src='{{ env('BASE_CLOUDINARY', 'https://res.cloudinary.com/sikoji')."c_scale,w_150/".$value }}'/></a>    
        @endif
        @if(!$readonly || !$disabled)
                <p><a class='btn btn-danger btn-delete btn-sm' onclick="if(!confirm('{{trans("crudbooster.delete_title_confirm")}}')) return false"
                      href='{{url(CRUDBooster::mainpath("delete-image?image=".$value."&id=".$row->id."&column=".$name))}}'><i
                                class='fa fa-ban'></i> {{trans('crudbooster.text_delete')}} </a></p>
            @endif
        @endif
        
        @if(!$value)
            <input type='file' id="{{$name}}" title="{{$form['label']}}" {{$required}} {{$readonly}} {{$disabled}} class='form-control' name="{{$name}}"/>
            <p class='help-block'>{{ @$form['help'] }}</p>
        @else
            <p class='text-muted'><em>{{trans("crudbooster.notice_delete_file_upload")}}</em></p>
        @endif
        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>

    </div>

</div>
