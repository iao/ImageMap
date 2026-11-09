<?php
foreach ($attachments as $attachment):
$item = $attachment->getItem();
$db = get_db();
$image = '';
            $imageTitle = '';
            foreach ($item->Files as $file):
                if($file->getExtension() == "png" || $file->getExtension() == "jpg") {
                        $image = $file->getWebPath('original');
                        $imageTitle = $file->original_filename;
                }
            endforeach;
            if($image == '') return;
            $areas = $db->getTable('Imagemap')->findImagemapByMapItem($item, false);
            
            $html = '<div>';
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
            $html .= '<img src="'.$image.'" alt="" class="contain" usemap="#'.$item->id.'" style="width:100%;">';
            $html .= '<map name="'.$item->id.'">';
            foreach ($areas as $area):
                $html .= '<area shape="'.$area->shape.'" title="'.$area->title.'" onclick="div_show( \''.$area->item_id.'Popup\' ); return false;" href="#" coords="'.$area->coords.'" alt="'.$area->alt.'" />';
            endforeach;
            $html .= '</map>';

            $html .= "</div>";
echo $html;
endforeach;
?>

<script type="text/javascript">
jQuery(document).ready(function(e) {
        jQuery('img[usemap]').rwdImageMaps();
});
</script>
