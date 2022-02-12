
@php
$dataTypeContent->{$row->field} = json_decode($dataTypeContent->{$row->field})
@endphp
<select id="{{ $row->field }}" class="form-control select2-tags-{{ $row->field }}" name="{{ $row->field }}[]" multiple @if($row->required == 1) required @endif/>
    @if(isset($options->relationship))
        {{-- Check that the relationship method exists --}}
        @if( method_exists( $dataType->model_name, \Illuminate\Support\Str::camel($row->field) ) )
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
    @endif
</select>


@push('javascript')

<script>
    
    
    $('select.select2-tags-{{ $row->field }}').select2({
        width: '100%',
        allowClear: true,
        tags: true,
        placeholder :'Select/Create Tag',
        ajax: {
            url: '{{ route('get-'.$options->table) }}',
            data: function (params) {
                var query = {
                    search: params.term,
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
        },
        createTag: function(params) {
            var term = $.trim(params.term);

            if (term === '') {
                return null;
            }

            return {
                id: term,
                text: term,
                newTag: true
            }
        }
    });

    @if (!empty($dataTypeContent->{$row->field}))
        @php
            $data = json_encode($dataTypeContent->{$row->field});
        @endphp
        
        var multi_tag_{{ $row->field }} = JSON.parse('{!! $data !!}');

        multi_tag_{{ $row->field }}.forEach(data => {
            var newOption = new Option(data, data, false, true);
            
            $('#{{ $row->field }}').append(newOption).trigger('change');
        });
    @endif
</script>
@endpush