<?php
class Table_Imagemap extends Omeka_Db_Table
{

    public function findImagemapByItem($item, $findOnlyOne = false)
    {
        $db = get_db();
        
        if (($item instanceof Item) && !$item->exists()) {
            return array();
        } else if (is_array($item) && !count($item)) {
            return array();
        }
        $alias = $this->getTableAlias();

        $select = $db->select()->from(array($alias => $db->Imagemap), "$alias.*");
        
        
        if (is_array($item)) {
            $itemIds = array();
            foreach ($item as $it) {
                $itemIds[] = (int)(($it instanceof Item) ? $it->id : $it);
            }
            $select->where("$alias.item_id IN (?)", $itemIds);
        } else {
            $itemId = (int)(($item instanceof Item) ? $item->id : $item);
            $select->where("$alias.item_id = ?", $itemId);
        }

        
        if ($findOnlyOne) {
            $imagemap = $this->fetchObject($select);
            return $imagemap;
        }

        
        $imagemaps = $this->fetchObjects($select);

        $indexedImagemaps = array();
        foreach ($imagemaps as $k => $loc) {
            $indexedImagemaps[$loc['item_id']] = $loc;
        }
        return $indexedImagemaps;
    }

    public function findImagemapByMapItem($item, $findOnlyOne = false)
    {
        $db = get_db();
        
        if (($item instanceof Item) && !$item->exists()) {
            return array();
        } else if (is_array($item) && !count($item)) {
            return array();
        }
        $alias = $this->getTableAlias();

        $select = $db->select()->from(array($alias => $db->Imagemap), "$alias.*");
        
        
        if (is_array($item)) {
            $itemIds = array();
            foreach ($item as $it) {
                $itemIds[] = (int)(($it instanceof Item) ? $it->id : $it);
            }
            $select->where("$alias.map_item_id IN (?)", $itemIds);
        } else {
            $itemId = (int)(($item instanceof Item) ? $item->id : $item);
            $select->where("$alias.map_item_id = ?", $itemId);
        }

        
        if ($findOnlyOne) {
            $imagemap = $this->fetchObject($select);
            return $imagemap;
        }

        
        $imagemaps = $this->fetchObjects($select);

        $indexedImagemaps = array();
        foreach ($imagemaps as $k => $loc) {
            $indexedImagemaps[$loc['item_id']] = $loc;
        }
        return $indexedImagemaps;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        $select->join(array('items' => $this->_db->Item), 'items.id = imagemaps.item_id', array());
        $permissions = new Omeka_Db_Select_PublicPermissions('Items');
        $permissions->apply($select, 'items');
        return $select;
    }
}