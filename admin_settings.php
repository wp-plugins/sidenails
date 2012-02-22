<?php
$source = $options['thumbnail_source'];
$have_image = $options['have_image'];
$cf_name = $options['cf_name'];
$show_credits = $options['show_credits'];
$sidenails_thumb_width = $options['sidenails_thumb_width'];
$sidenails_thumb_height = $options['sidenails_thumb_height'];
$ngg_thumbnail_width = $this->ngg_thumbnail_width;
$ngg_thumbnail_height = $this->ngg_thumbnail_height;
?>
<div class=wrap>
  <div class="icon32" id="icon-edit">
    <br />
  </div>
  <h2>SideNails <?php echo SIDENAILS_VERSION ?></h2>
  <br /> <br />
  <?php _e("<b>SideNails</b> est un plugin qui vous permet d'afficher une liste des derniers articles de votre blog sous forme de vignettes, dans la side bar.<br />Pour cela, <b>SideNails</b> utilise les images attachés à votre article (vignette, mise en avant).", 'sidenails') ?>
  <br /> <br />
  <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <div class="metabox-holder has-right-sidebar">
      <div class="inner-sidebar">
        <div class="meta-box-sortables ui-sortable">
          <div class="postbox">
            <h3 class="hndle">
              <?php _e('Info & Support', 'sidenails') ?>
            </h3>
              <div class="inside">
                <?php _e("<b>SideNails</b> est un plugin développé par <a href='http://www.tranchesdunet.com/'>Jean-Marc BIANCA</a>", "sidenails") ?>
                <br />
                <ul><li><a href="http://www.tranchesdunet.com/sidenails"><?php _e("Support", "sidenails")?></a></li></ul>
                <br /><br />
                
                <?php echo "<p><label><input type='checkbox' value='1' name='show_credits'";
                      if($show_credits)
                      {
                        echo " checked='checked'";
                      }
                      echo " /> ".__("Afficher un lien 'Powered by SideNails' en bas du Widget", "sidenails");?>
                <br /><br />
                <?php _e("Idée originale : <a href='http://www.tuxboard.com/'>Alexandre VAL</a>", "sidenails") ?>
              </div>
          </div>
        </div>
      </div>
      <div id="post-body-content">
        <div id="normal-sortables"
          class="meta-box-sortables ui-sortables">
          <div class="postbox">
            <h3 class="hndle">
            <?php _e('Images utilisées pour les vignettes', 'sidenails') ?>
            </h3>
            <div class="inside">
            <?php _e("Par défaut, <b>SideNails</b> utilise la miniature de la 1ere image attachée à l'article, classé par '<i>Ordre du menu</i>' dans la galerie d''image (voir l''image ci-dessous)","sidenails");
            echo "<br /><br />";
            echo "<img src='".WP_PLUGIN_URL."/sidenails/images/screenshot-ordre-menu-galerie.png' />";
            echo "<br /><br />";
            _e("Sélectionner la source des vignettes :","sidenails");
            echo "<br /><br />";

            echo "<p><label><input type='radio' value='attachment' name='thumbnail_source'";
            if($source == 'attachment')
            {
              echo " checked='checked'";
            }
            echo "> ".__("1ere image attachée à l'article","sidenails")."</label>";
            echo " ( ".__("Taille par défaut : ","sidenails").$this->default_thumbnail_width." x ".$this->default_thumbnail_height." )</p>";
            if(current_theme_supports('post-thumbnails'))
            {
              echo "<p><label><input type='radio' value='post-thumbnails' name='thumbnail_source'";
              if($source == 'post-thumbnails')
              {
                echo " checked='checked'";
              }
              echo "> ".__("Image mise en avant","sidenails")."</label></p>";
            }
            echo "<p><label><input type='radio' value='custom-field' name='thumbnail_source' ";
            if($source == 'custom-field')
            {
              echo " checked='checked'";
            }
            echo "> ".__("URL de l'image dans un Custom Field","sidenails")."</label>";
            echo " ( ".__("Nom du Custom Field ","sidenails").": <input type='text' name='cf_name' value='".$cf_name."' /> )</p>";
            if(class_exists('nggdb'))//seulement si NextGen Gallery est installé
            {
              echo "<p><label><input type='radio' value='ngg' name='thumbnail_source'";
              if($source == 'ngg')
              {
                echo " checked='checked'";
              }
              echo "> ".__("Image provenant d'une NextGen Gallery incluse dans l'article","sidenails")."</label>";
              echo " ( ".__("Taille par défaut : ","sidenails").$ngg_thumbnail_width." x ".$ngg_thumbnail_height." )</p>";
            }
            echo "<br />";
            echo "<p><label><input type='checkbox' value='1' name='have_image'";
            if($have_image)
            {
              echo " checked='checked'";
            }
            echo " /> ".__("N'afficher que des articles avec images", "sidenails")."</label></p>";
            ?>

            </div>
          </div>

          <div class="postbox ">
            <h3 class="hndle">
            <?php _e("Taille des vignettes", "sidenails");?>
            </h3>
            <div class="inside">
            <?php
            _e("La taille des vignettes est définie automatiquement :<br />- par vos réglages dans Wordpress pour les vignettes attachées à l'article et les images mise en avant, <br />- par vos réglages dans NextGen Gallery pour les vignettes de NextGen Gallery.<br />- les images venant de Custom Fields n'ont pas de taille par défaut.<br />Toutefois, vous pouvez modifier ici la taille des vignettes.","sidenails");
            echo " <b>".__("Attention, ces dimensions seront appliquées pour toutes les vignettes du plugin SideNails","sidenails")."</b>";
            echo "<br />";
            echo __("Largeur ","sidenails").": <input type='text' size=3 name='sidenails_thumb_width' value='".$sidenails_thumb_width."' />px<br />";
            echo __("Hauteur ","sidenails").": <input type='text' size=3 name='sidenails_thumb_height' value='".$sidenails_thumb_height."' />px<br />";
            ?>
            </div>
          </div>
          
          <div class="postbox ">
            <h3 class="hndle">
            <?php _e("YARPP : Yet Another Related Post Plugin", "sidenails");?>
            </h3>
            <div class="inside">
              <?php _e("En bonus, si vous utilisez le plugin YARPP, vous avez dans le répertoire du plugin <b>SideNails</b> un template pour ce plugin, afin d'afficher des images suivant le même principe que le Widget : yarpp-template-sidenails.php <br />Les réglages que vous effectuez sur cette page (source, taille des vignettes) sont valables également pour ce template.<br />Pour pouvoir utiliser ce template, vous devez recopier le fichier dans le repertoire de votre thème (et avoir <b>SideNails</b> activé, même si vous n'utilisez pas ses Widgets).<br />Il suffit ensuite de sélectionner ce template dans les réglages de YARPP.","sidenails")?>
            </div>
          </div>
          <!-- 
          <div class="postbox " id="postexcerpt">
            <h3 class="hndle">
              <span><?php _e('TODO', 'sidenails') ?> </span>
            </h3>
      
            TODO: images par defaut si pas d'image<br /> 
            option : changer l'orderby et l'order dans le cas de plusieurs images possibles (attachment, ngg)<br /> 
            option : changer l'apparence des vignettes (typo, padding, etc...) <br /> 
            option++ : attaché le resultat a un retour Yarpp<br /> 
            <br />
      
          </div> -->
          <div class="submit">
            <input type="submit" name="update_sidenailsSettings"
              value="<?php _e('Mettre à jour', 'sidenails') ?>" />
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
