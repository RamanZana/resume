<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Http\Controllers\Controller;
use TCG\Voyager\Facades\Voyager;

use App\ContentTypes\AdditionalContentType;
use App\ContentTypes\KeyValueJsonContentType;
use App\ContentTypes\SelectMultipleAuthorType;
use App\ContentTypes\SelectMultipleTagType;
use TCG\Voyager\Models\DataRow;

class ExtendedBreadFormFieldsController extends VoyagerBaseController
{

    public function getContentBasedOnType(Request $request, $slug, $row, $options = null)
    {
        switch ($row->type) {
            case 'adcontent':
                return (new AdditionalContentType($request, $slug, $row, $options))->handle();
            case 'select_multiple_tag':
                return (new SelectMultipleTagType($request, $slug, $row, $options))->handle();
            case 'select_multiple_author':
                return (new SelectMultipleAuthorType($request, $slug, $row, $options))->handle();
            case 'key-value_to_json':
                return (new KeyValueJsonContentType($request, $slug, $row, $options))->handle();
            default:
                return Controller::getContentBasedOnType($request, $slug, $row, $options);
        }
    }

    public function getMediaPicker($slug = 'posts', $id = '', $dataContent)
    {
        $rowArray = ["id" => 1000,
            "data_type_id" => 500,
            "field" => "adcontent_image",
            "type" => "media_picker",
            "display_name" => "Additional Image ",
            "required" => 0,
            "browse" => 1,
            "read" => 1,
            "edit" => 1,
            "add" => 1,
            "delete" => 1,
            "details" => '{"resize":{"width":"1000","height":"null"},"quality":"70%","upsize":true,"thumbnails":[{"name":"medium","scale":"50%"},{"name":"small","scale":"25%"},{"name":"cropped","crop":{"width":"300","height":"250"}}]}',
            "order" => 7,
            "col_width" => 100];

        $row = new DataRow($rowArray);

        $dataType = (object) ["slug" => $slug];

        $dataTypeContent = (object) ["id" => $id."_adcontent_image", "image_adcontent_image" => $dataContent];

        return app('voyager')->formField($row, $dataType, $dataTypeContent);
    }
}
