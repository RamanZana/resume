<?php

namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class AdditionalContentField extends AbstractHandler
{
    protected $codename = 'adcontent';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('formfields.adcontent', [
            'row' => $row,
            'options' => $options,
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent
        ]);
    }
}