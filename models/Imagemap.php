<?php

/**
 * Imagemap
 * @package: Omeka
 */
class Imagemap extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    public $item_id;
    public $map_item_id;
    public $shape;
    public $coords;
    public $title;
    public $alt;
    

    protected function _validate()
    {
        if (empty($this->item_id)) {
            $this->addError('item_id', __('Imagemap requires an item ID.'));
        }
        // An item must exist.
        if (!$this->getTable('Item')->exists($this->item_id)) {
            $this->addError('item_id', __('Imagemap requires a valid item ID.'));
        }
        
        if (!$this->exists() && $this->getTable()->findBy(array('item_id' => $this->item_id))) {
            $this->addError('map_item_id', __('An imagemap already exists for the provided item.'));
        }
        if (empty($this->map_item_id)) {
            $this->addError('map_item_id', __('Imagemap requires a map item ID.'));
        }
        // An item must exist.
        if (!$this->getTable('Item')->exists($this->map_item_id)) {
            $this->addError('map_item_id', __('Imagemap requires a valid map item ID. '.$this->map_item_id));
        }
    }
    
    public function getResourceId()
    {
        return 'Imagemaps';
    }

    /**
     * Get parent item.
     *
     * @return Omeka_Record The item.
     */
    public function getItem()
    {
        return get_record_by_id('Item', $this->item_id);
    }

    /**
     * Get parent item.
     *
     * @return Omeka_Record The item.
     */
    public function getMapItem()
    {
        return get_record_by_id('Item', $this->map_item_id);
    }
}