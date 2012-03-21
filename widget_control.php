<input type="hidden" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo attribute_escape($title);?>" />
<p><label><?php _e("Titre", "sidenails") ?> : <input class="widefat" name="<?php echo $this->get_field_name('sidenails_widget_title'); ?>" type="text" value="<?php echo attribute_escape($sidenails_widget_title);?>" /></label></p>
<p><label><?php _e("Nombre d'articles affichés", "sidenails") ?> : <input class="widefat" name="<?php echo $this->get_field_name('sidenails_nb_article'); ?>" type="text" value="<?php echo attribute_escape($sidenails_nb_article);?>" size="2" /></label></p>
<p><label><?php _e("Catégorie (facultatif)", "sidenails") ?> : 
<?php 
  if(isset($list_categorie) && is_array($list_categorie) && count($list_categorie) > 0)
  {
    echo "<select class='widefat' name='".$this->get_field_name('sidenails_cat')."'>";
    echo "<option value=''> ---------- </option>";
    foreach($list_categorie as $cat)
    {
      echo "<option value='".$cat->cat_name."' ".selected($cat->cat_name, attribute_escape($sidenails_cat), false).">".$cat->cat_name."</option>";
    }
    echo "</select>";
  }else{
    echo _e("Aucune catégorie trouvée", "sidenails");
  }
?>
</label></p>
<p><?php _e("et/ou","sidenails");?></p>
<p><label><?php _e("Tag (facultatif)", "sidenails") ?> : <input class="widefat" name="<?php echo $this->get_field_name('sidenails_tag'); ?>" type="text" value="<?php echo attribute_escape($sidenails_tag);?>" id="<?php echo $this->get_field_id('sidenails_tag'); ?>" onFocus="setSuggest('<?php echo $this->get_field_id('sidenails_tag'); ?>');" /></label></p>
<p><?php _e("et/ou","sidenails");?></p>
<p><label><?php _e("ID de billets séparés par une virgule (ex: 8,12,324)","sidenails")?> : <input class="widefat" name="<?php echo $this->get_field_name('sidenails_posts'); ?>" type="text" value="<?php echo attribute_escape($sidenails_posts);?>" /></label></p>
