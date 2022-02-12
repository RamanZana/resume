<?php

namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class SelectMultipleAuthor extends AbstractHandler
{
    protected $codename = 'select_multiple_author';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('formfields.select_multiple_author', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
