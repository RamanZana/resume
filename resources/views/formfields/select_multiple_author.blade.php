
@php
$dataTypeContent->{$row->field} = json_decode($dataTypeContent->{$row->field})
@endphp
<select id="{{ $row->field }}" class="form-control select2-author-{{ $row->field }}" name="{{ $row->field }}[]" multiple @if($row->required == 1) required @endif/>
    {{-- @if(isset($options->relationship)) --}}
        {{-- Check that the relationship method exists --}}
        {{-- @if( method_exists( $dataType->model_name, \Illuminate\Support\Str::camel($row->field) ) )
            <?php $selected_values = isset($dataTypeContent) ? $dataTypeContent->{\Illuminate\Support\Str::camel($row->field)}()->pluck($options->relationship->key)->all() : []; ?>
            <?php
            $relationshipListMethod = \Illuminate\Support\Str::camel($row->field) . 'List';
            if (isset($dataTypeContent) && method_exists($dataTypeContent, $relationshipListMethod)) {
                $relationshipOptions = $dataTypeContent->$relationshipListMethod();
            } else {
                $relationshipClass = get_class(app($dataType->model_name)->{\Illuminate\Support\Str::camel($row->field)}()->getRelated());
                $relationshipOptions = $relationshipClass::all();
            }
            ?>
            @foreach($relationshipOptions as $relationshipOption)
                <option value="{{ $relationshipOption->{$options->relationship->key} }}" @if(in_array($relationshipOption->{$options->relationship->key}, $selected_values)) selected="selected" @endif>{{ $relationshipOption->{$options->relationship->label} }}</option>
            @endforeach
        @endif
    @elseif(isset($options->options))
        @foreach($options->options as $key => $label)
                <?php $selected = ''; ?>
            @if(is_array($dataTypeContent->{$row->field}) && in_array($key, $dataTypeContent->{$row->field}))
                <?php $selected = 'selected="selected"'; ?>
            @elseif(!is_null(old($row->field)) && in_array($key, old($row->field)))
                <?php $selected = 'selected="selected"'; ?>
            @endif
            <option value="{{ $key }}" {!! $selected !!}>
                {{ $label }}
            </option>
        @endforeach
    @endif --}}
</select>


@push('javascript')

<script>
    
    var locale = '{{ $options->locale??'' }}';
    $('select.select2-author-{{ $row->field }}').select2({
        width: '100%',
        allowClear: true,
        placeholder :'Select value',
        ajax: {
            url: '{{ route('get-'.$options->table) }}',
            data: function (params) {
                var query = {
                    search: params.term,
                    locale: locale,
                    type: 'json',
                    method: 'get',
                    page: params.page || 1
                }
                return query;
            },
            processResults: function (data) {
                var arr = [];

                $.each(data.results, function(index, value) {
                    arr.push({
                        id: value.s_id,
                        text: value.text,
                    })
                    
                })
                
                if (arr.length > 0) {
                    return {
                        results: arr,
                        pagination: {
                            // THE `10` SHOULD BE SAME AS `$resultCount FROM PHP, it is the number of records to fetch from table`
                            more: data.more === 'true'
                        }
                    };
                } else {
                    return {
                        results: null,
                    };
                }
            },
        }
    });

    @if ($row->field=='author_id')
    @if (!empty($dataTypeContent->{$row->field}))
        $.get( "{{ route('get-selected-authors') }}", { author_ids: '{!! json_encode($dataTypeContent->{$row->field}) !!}'} )
        .done( function( data ) {
            $.each(data.results, function(index, value) {
                var newOption = new Option(value.text, value.s_id, false, true);
            
                $('#{{ $row->field }}').append(newOption).trigger('change');
            });
        });
    @else
        $.get( "{{ route('get-selected-authors') }}", { author_ids: '{!! setting("admin.author_".app()->getLocale()) !!}'} )
        .done( function( data ) {
            $.each(data.results, function(index, value) {
                var newOption = new Option(value.text, value.s_id, false, true);
            
                $('#{{ $row->field }}').append(newOption).trigger('change');
            });
        });
    @endif
    @else
    @if (!empty($dataTypeContent->{$row->field}))
        $.get( "{{ route('get-selected-data') }}", { data_ids: '{!! json_encode($dataTypeContent->{$row->field}) !!}'} )
        .done( function( data ) {
            $.each(data.results, function(index, value) {
                var newOption = new Option(value.text, value.s_id, false, true);
            
                $('#{{ $row->field }}').append(newOption).trigger('change');
            });
        });
    @endif
    @endif
</script>
@endpush