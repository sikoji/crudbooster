 <?php
    $formula = $form['formula'];
    $re = '/\[(.*?)\]/m';
    preg_match_all($re, $formula, $matches);
    $script_onchange = "";
    $formula_function_name = 'formula' . str_slug($name . $form['name'], '');
    foreach($matches[1] as $name_subject) {
        $script_onchange .= "
        $('#$name_subject').change(function() {
            $formula_function_name();
        });
        ";
    }
      
    $formula = str_replace("[", "Number($('#", $formula);
    $formula = str_replace("]", "').val())", $formula);
    
    ?>
 @push('bottom')
 <script type="text/javascript">
     function {{ $formula_function_name }} () {
         var v = {!! $formula !!};
         $('#{{$name}}').val(v);
     }

     $(function() {
         {!! $script_onchange !!}
     })
 </script>
 @endpush
 <div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
     <label class="control-label {{$label_col_width?:'col-sm-2'}}">
         {{$form['label']}}
         @if($required)
         <span class='text-danger' title='{!! trans(' crudbooster.this_field_is_required') !!}'>*</span>
         @endif
     </label>

     <div class="{{$col_width?:'col-sm-10'}}">
         <input type='text' title="{{$form['label']}}" {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} {{$validation['max']?"maxlength=".$validation['max']:""}} class='form-control' name="{{$name}}" id="{{$name}}" value='{{$value}}' />

         <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
         <p class='help-block'>{{ @$form['help'] }}</p>

     </div>
 </div>