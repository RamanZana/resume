<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ViewOnSite extends AbstractAction
{
    public function getTitle()
    {
        return 'Web';
    }

    public function getIcon()
    {
        return 'voyager-world';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-success pull-right preview',
            'target' => '_blank'
        ];
    }

    public function getDefaultRoute()
    {
        return config('app.site_url').app()->getLocale().'/article/'.$this->data->id;
    }
    
    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'posts';
    }

    public function shouldActionDisplayOnRow($row)
    {
        // dd($row->status == 'PUBLISHED' && $row->deleted_at!=null);
        return ($row->status == 'PUBLISHED' && $row->deleted_at==null);
    }

}