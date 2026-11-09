<?php 
/**
 *
 * @package  Unity 3D Viewer
 */

class ImageMapPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array ('install',
        'uninstall',
        'after_save_item',
        'public_items_show',
        'public_head');
    protected $_filters = array('exhibit_layouts', 'admin_items_form_tabs' );

    
    public function hookInstall()
    {
        $db = get_db();
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->Imagemap` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `item_id` BIGINT UNSIGNED NOT NULL ,
        `map_item_id` BIGINT UNSIGNED NOT NULL ,
        `shape` VARCHAR( 255 ) NOT NULL ,
        `coords` VARCHAR( 255 ) NOT NULL ,
        `title` VARCHAR( 255 ) NOT NULL ,
        `alt` VARCHAR( 255 ) NOT NULL ,
        INDEX (`item_id`)) ENGINE = InnoDB";
        $db->query($sql);
    }

    public function hookUninstall()
    {
        // Drop the Imagemap table
        $db = get_db();
        $db->query("DROP TABLE IF EXISTS `$db->Imagemap`");
    }

    public function filterExhibitLayouts($layouts)
    {
         $layouts['image_map'] = array(
            'name' => __('Image Map'),
            'description' => __('Show a Iamge Map.')
        );
        return $layouts;
    }

    public function hookPublicHead() 
    {
        queue_css_file('popup');
        queue_js_file('popup');
        queue_js_file('jquery.rwdImageMaps.min');
    }

    public function hookPublicItemsShow($args)
    {
        $view = $args['view'];
        $item = $args['item'];
        if(is_null($item->getItemType())) return;
        $itemType = $item->getItemType()->name;
        
        if ($itemType == 'ImageMap') {
            $image = '';
            $imageTitle = '';
            foreach ($item->Files as $file):
                if($file->getExtension() == "png" || $file->getExtension() == "jpg") {
                        $image = $file->getWebPath('original');
                        $imageTitle = $file->original_filename;
                }
            endforeach;
            if($image == '') return;
            $areas = $this->_db->getTable('Imagemap')->findImagemapByMapItem($item, false);
            
            $html = '<div>';
            $html .= '<h2>Image Map</h2>';
            foreach ($areas as $area):
                $areaItem = $area->getItem();
                $html .= '<div id="'.$area->item_id.'Popup" class="popup" onclick ="div_hide(\''.$area->item_id.'Popup\')">';
                $html .= '<div class="popupContent" >';
                $html .= '<h2 style="text-align: center;">'.metadata($areaItem, array('Dublin Core', 'Title')).'</h2>';
                $html .= '<table cellspacing="0" cellpadding="0"><tr><td>';
                $html .= files_for_item(array('linkToFile'=>false, 'imageSize'=>'thumbnail'), array(), $area->getItem());
                $html .= '</td><td>';
                $html .= metadata($areaItem, array('Dublin Core', 'Description'));
                $html .= '</td></tr></table>';
                
                $html .= '</div>';
                $html .= '</div>';
            endforeach;
            $html .= '<img src="'.$image.'" alt="" class="contain" usemap="#'.$item->id.'">';
            $html .= '<map name="'.$item->id.'">';
            foreach ($areas as $area):
                $html .= '<area shape="'.$area->shape.'" title="'.$area->title.'" onclick="div_show( \''.$area->item_id.'Popup\' ); return false;" href="#" coords="'.$area->coords.'" alt="'.$area->alt.'" />';
            endforeach;
            $html .= '</map>';

            $html .= "</div>";
            echo $html;
        }
    }

    public function filterAdminItemsFormTabs($tabs, $args)
    {
        // insert the map tab before the Miscellaneous tab
        $item = $args['item'];
        $tabs['ImageMap'] = $this->_imagemapForm($item);

        return $tabs;
    }

    public function hookAfterSaveItem($args)
    {
        if (!($post = $args['post'])) {
            return;
        }

        $item = $args['record'];
        if (!isset($post['imagemap'])) {
            return;
        }

        $imagemap = $this->_db->getTable('Imagemap')->findImagemapByItem($item, true);

        $imagemapPost = $post['imagemap'];
        if (!empty($imagemapPost)
            && $imagemapPost['map_item_id'] != ''
        ) {
            if (!$imagemap) {
                $imagemap = new Imagemap;
                $imagemap->item_id = $item->id;
            }
            $imagemap->setPostData($imagemapPost);
            $imagemap->save();
        } else {
            if ($imagemap) {
                $imagemap->delete();
            }
        }
    }


    protected function _imagemapForm($item, $label = 'Find a by Address:', $confirmChange = true,  $post = null)
    {
        $itemtype = $this->_db->getTable('ItemType')->findByName('ImageMap');
        $no_of_items = $itemtype->totalItems();
        //$items = $itemtype->getItems($no_of_items);
        $params = array('type'=>$itemtype->id);
        $items = $this->_db->getTable('Item')->findBy($params, $no_of_items);
        $html = '';
        
        $area = $this->_db->getTable('Imagemap')->findImagemapByItem($item, true);
        $html .= '<div class="field">';
        $html .= '<select name="imagemap[map_item_id]" id="imagemap_map_item_id">';
        $html .= '<option value="">Select Below </option>';
        foreach($items as $mapitem):
	    if(is_object($area) && !is_null($area) && $mapitem->id == $area->map_item_id)
                $html .= '<option value="'.$mapitem->id.'" selected="selected">'.metadata($mapitem, array('Dublin Core', 'Title')).'</option>';
            else
                $html .= '<option value="'.$mapitem->id.'">'.metadata($mapitem, array('Dublin Core', 'Title')).'</option>';
        endforeach;
        $html .= '</select>';
        if(is_object($area) && !is_null($area))
        {
            $html .= '<select name="imagemap[shape]" id="imagemap_shape">';
            if($area->shape == 'default')
                $html .= '<option value="default" selected="selected">default</option>';
            else
                $html .= '<option value="default">default</option>';
            if($area->shape == 'rect')
                $html .= '<option value="rect" selected="selected">rect</option>';
            else
                $html .= '<option value="rect">rect</option>';
            if($area->shape == 'circle')
                $html .= '<option value="circle" selected="selected">circle</option>';
            else
                $html .= '<option value="circle">circle</option>';
            if($area->shape == 'poly')
                $html .= '<option value="poly" selected="selected">poly</option>';
            else
                $html .= '<option value="poly">poly</option>';
            $html .= '</select>';
            $html .= '<input type="text" name="imagemap[coords]" value="'.$area->coords.'">';
            $html .= '<input type="text" name="imagemap[title]" value="'.$area->title.'">';
            $html .= '<input type="text" name="imagemap[alt]" value="'.$area->alt.'">';
        }
        else
        {
            $html .= '<select name="imagemap[shape]" id="imagemap_shape">';
            $html .= '<option value="default">default</option>';
            $html .= '<option value="rect">rect</option>';
            $html .= '<option value="circle">circle</option>';
            $html .= '<option value="poly">poly</option>';
            $html .= '</select>';
            $html .= '<input type="text" name="imagemap[coords]">';
            $html .= '<input type="text" name="imagemap[title]">';
            $html .= '<input type="text" name="imagemap[alt]">';
        }
        $html .= '</div>';

        //$html = '';
        return $html;
    }
}
