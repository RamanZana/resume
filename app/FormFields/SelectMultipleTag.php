<?php

namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class SelectMultipleTag extends AbstractHandler
{
    protected $codename = 'select_multiple_tag';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('formfields.select_multiple_tag', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
