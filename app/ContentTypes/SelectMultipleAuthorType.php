<?php

namespace App\ContentTypes;
use TCG\Voyager\Http\Controllers\ContentTypes\BaseType;

class SelectMultipleAuthorType extends BaseType
{
    public function handle()
    {
        $content = $this->request->input($this->row->field, []);

        if (true === empty($content)) {
            return json_encode([]);
        }

        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }
}
