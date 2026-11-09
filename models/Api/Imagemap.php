<?php

/**
 * @package Omeka\Record\Api
 */
class Api_Imagemap extends Omeka_Record_Api_AbstractRecordAdapter
{
    
    /**
     * Set POST data to a location.
     * 
     * @param Location $record
     * @param mixed $data
     */
    public function setPostData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (isset($data->item->id)) {
            $record->item_id = $data->item->id;
        }
        if (isset($data->map_item_id)) {
            $record->map_item_id = $data->map_item_id;
        }
        if (isset($data->shape)) {
            $record->shape = $data->shape;
        }
        if (isset($data->coords)) {
            $record->coorde = $data->coords;
        }
        if (isset($data->alt)) {
            $record->title = $data->title;
        }
        if (isset($data->alt)) {
            $record->alt = $data->alt;
        }
    }
    
    /**
     * Set PUT data to a location.
     * 
     * @param Location $record
     * @param mixed $data
     */
    public function setPutData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (isset($data->map_item_id)) {
            $record->map_item_id = $data->map_item_id;
        }
        if (isset($data->shape)) {
            $record->shape = $data->shape;
        }
        if (isset($data->coords)) {
            $record->coorde = $data->coords;
        }
        if (isset($data->alt)) {
            $record->title = $data->title;
        }
        if (isset($data->alt)) {
            $record->alt = $data->alt;
        }
    }
}
