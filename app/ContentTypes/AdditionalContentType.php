<?php

namespace App\ContentTypes;
use TCG\Voyager\Http\Controllers\ContentTypes\BaseType;

class AdditionalContentType extends BaseType
{
    /**
     * @return null|string
     */
    public function handle()
    {
        $value = $this->request->input($this->row->field);
        // dd($value);

        if(!empty($value)){
            $new_parameters = array();
            foreach ($value as $key => $val) {
                if(!empty($value[$key]['key'])&&!empty($value[$key]['type'])){
                    $new_parameters[] = $value[$key];
                }
            }
            return json_encode($new_parameters, JSON_UNESCAPED_UNICODE);
        }
        else{
            return null;
        }
        
    }
}
