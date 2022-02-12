@php
    if($dataTypeContent->{$row->field}){
        $old_parameters = json_decode($dataTypeContent->{$row->field});
    }
    $end_id = 0;
@endphp


<div class="custom-parameters">
@if($dataTypeContent->{$row->field})
    @foreach($old_parameters as $parameter)
        <div class="form-group row" row-id="{{$loop->index}}">
            <div class="col-xs-10" style="margin-bottom:5px;" id="key">
                <select id="{{$row->field}}_{{$loop->index}}" class="form-control select2-author-player-{{$loop->index}} players-{{$row->details->team}}" name="{{ $row->field }}[{{$loop->index}}][key]">
                    
                </select>


                @push('javascript')

                <script>
                    
                    
                    // var team_in = $("[name='{{$row->details->team}}']");
                    var team_id=null;
                    // function getPlayer_{{$loop->index}}(team) {
                    $('select.select2-author-player-{{$loop->index}}').select2({
                        width: '100%',
                        allowClear: true,
                        placeholder :'Select Player',
                        ajax: {
                            url: '{{ route('get-player') }}',
                            data: function (params) {
                                var query = {
                                    search: params.term,
                                    team: team_id,
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
                        
                    // }

                    // getPlayer_{{$loop->index}}(team_in);

                    @if (!empty($parameter->key))
                        $.get( "{{ route('get-selected-player') }}", { player_id: '{!! $parameter->key !!}'} )
                        .done( function( data ) {
                            $.each(data.results, function(index, value) {
                                var newOption = new Option(value.text, value.s_id, true, true);
                            
                                $('#{{$row->field}}_{{$loop->index}}').append(newOption).trigger('change');
                            });
                        });
                    @endif
                </script>
                @endpush
                {{-- <input type="text" class="form-control" name="{{ $row->field }}[{{$loop->index}}][key]" value="{{ $parameter->key }}" id="key"> --}}
            </div>
            <div class="col-xs-7" style="margin-bottom:0;">
                <select class="form-control" name="{{ $row->field }}[{{$loop->index}}][type]" id="type">
                    <option @if($parameter->type==1)selected @endif value="1">goal</option>
                    <option @if($parameter->type==2)selected @endif value="2">asist</option>
                    <option @if($parameter->type==3)selected @endif value="3">Yellow Card</option>
                    <option @if($parameter->type==4)selected @endif value="4">Red Card</option>
                    <option @if($parameter->type==5)selected @endif value="5">Penalty</option>
                    <option @if($parameter->type==6)selected @endif value="6">Missing Penalty</option>
                </select>
                
            </div>
            <div class="col-xs-3" style="margin-bottom:0;">
                <input type="number" class="form-control" name="{{ $row->field }}[{{$loop->index}}][value]" value="{{ $parameter->value??0 }}" id="value" min="0">
            </div>
            
            <div class="col-xs-2" style="margin-bottom:0;">
                <button type="button" class="btn btn-xs btn-danger" style="margin-top:0px;"><i class="voyager-trash"></i></button>
            </div>
        </div>
        @php 
            $end_id = $loop->index + 1;
        @endphp
    @endforeach
@endif
    <div class="form-group row" row-id="{{ $end_id }}">
        <div class="col-xs-10" style="margin-bottom:5px;" id="key">
            <select id="{{$row->field}}_{{ $end_id }}" class="form-control select2-author-player-{{ $end_id }} players-{{$row->details->team}}" name="{{ $row->field }}[{{ $end_id }}][key]">
                
            </select>


            @push('javascript')

            <script>
                
                
                
                // var team_in = $("[name='{{$row->details->team}}']");
                var team_id=null;
                // function getPlayer_{{ $end_id }}(team) {
                $('select.select2-author-player-{{ $end_id }}').select2({
                    width: '100%',
                    allowClear: true,
                    placeholder :'Select Player',
                    ajax: {
                        url: '{{ route('get-player') }}',
                        data: function (params) {
                            var query = {
                                search: params.term,
                                team: team_id,
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
            </script>
            @endpush
            {{-- <input type="text" class="form-control" name="{{ $row->field }}[{{ $end_id }}][key]" value="{{ $parameter->key }}" id="key"> --}}
        </div>
        <div class="col-xs-7" style="margin-bottom:0;">
            <select class="form-control" name="{{ $row->field }}[{{$end_id}}][type]" id="type">
                <option selected value="1">goal</option>
                <option value="2">asist</option>
                <option value="3">Yellow Card</option>
                <option value="4">Red Card</option>
                <option value="5">Penalty</option>
                <option value="6">Missing Penalty</option>
            </select>
            
        </div>
        <div class="col-xs-3" style="margin-bottom:0;">
            <input type="number" class="form-control" name="{{ $row->field }}[{{ $end_id }}][value]" value="0" id="value"  min="0">
        </div>
        <div class="col-xs-2" style="margin-bottom:0;">
            <button id="{{ $row->field }}_adbtn" type="button" class="btn btn-success btn-xs" style="margin-top:0px;"><i class="voyager-plus"></i></button>
        </div>
    </div>
    @php
        
        // dd($dataTypeContent);
    @endphp

    {{-- {!! app('voyager')->formField($dataTypeContent->img, $dataType, $dataTypeContent) !!} --}}

    {{-- <input type="hidden" name="keyvaluejson" value="{{$row->field}}"> --}}
</div>



<script>

    // function editNameCount(el){
    //     var str = el.getAttribute('name');
    //     var old_id = parseInt(el.parentNode.parentNode.getAttribute('row-id'));
    //     new_str = str.substring(0,str.indexOf('[')+1)
    //                 + (old_id+1)
    //                 + str.substring(str.indexOf(']'), str.length);
    //     return(new_str);
    // }

    // function addRow(){

    //     var new_row = this.parentNode.parentNode.cloneNode(true);
        
    //     new_row.querySelector("#key").setAttribute('name', editNameCount(new_row.querySelector("#key")));
    //     new_row.querySelector("#key").value = '';
    //     new_row.querySelector("#value").setAttribute('name', editNameCount(new_row.querySelector("#value")));
    //     new_row.querySelector("#value").value = '';
    //     new_row.setAttribute('row-id', parseInt(this.parentNode.parentNode.getAttribute('row-id'))+1)
        
    //     this.classList.remove('btn-success');
    //     this.innerHTML = '<i class="voyager-trash"></i>';
    //     new_row.querySelector('.btn-success').onclick = this.onclick;
    //     this.onclick = removeRow;
    //     this.parentNode.parentNode.parentNode.appendChild(new_row);
    // };

    // function removeRow() {
    //     this.parentNode.parentNode.remove();
    // }

    var buttons = document.querySelectorAll('.custom-parameters .btn-danger');
    for (var i = 0; i < buttons.length; i++) buttons[i].onclick = removeRow;
    var suc_buttons = document.querySelector('#{{ $row->field }}_adbtn');
    suc_buttons.onclick = addRow;
    
</script>


