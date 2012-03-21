<?php
/*
 Plugin Name: SideNails
 Plugin URI: http://www.tranchesdunet.com/sidenails
 Description: SideNails allow you to display a list of the last posts with a thumbnail, in a widget. For this, SideNails use the images linked to your post (thumbnail, featured image, NextGen Gallery, etc.)
 Version: 0.2.1
 Author: <a href="http://www.tranchesdunet.com/">Jean-Marc BIANCA</a>
 Author URI: http://www.tranchesdunet.com/sidenails

 === RELEASE NOTES ===
 2012-02-21 - v0.1 - First public release
 2012-03-07 - v0.2 - Suppression du filtre pour les attachments et post-thumbnails : permet d'afficher une vignette meme si l'image liée au post n'est pas DANS le post,
 											 correction de bugs divers
 											 Possibilité d'afficher directement une liste d'articles par leur ID
 2012-03-21 - v0.2.1 - Correction du numero de version
 */

define("SIDENAILS_VERSION", "v0.2.1");

if (!class_exists("sidenails"))
{
  class sidenails extends WP_Widget
  {
    var $adminOptionsName = 'sidenailsAdminOptions';
    var $default_thumbnail_width = 0;//definies dans wordpress pour les thumbnails
    var $default_thumbnail_height = 0;
    var $ngg_thumbnail_width = 0;
    var $ngg_thumbnail_height = 0;

    /**
     * constructor
     */
    function sidenails()
    {
      parent::__construct("sidenails", "SideNails", array('description' => __("Afficher les derniers articles sous forme de vignettes dans un widget", "sidenails")));
      //recuperer les valeurs par defaut dans Wordpress
      $this->default_thumbnail_width = get_option("thumbnail_size_w");
      $this->default_thumbnail_height = get_option("thumbnail_size_h");
      if(class_exists("nggdb"))
      {
        $ngg_options = get_option("ngg_options");
        $this->ngg_thumbnail_width = $ngg_options['thumbwidth'];
        $this->ngg_thumbnail_height = $ngg_options['thumbheight'];
      }
    }

    function getAdminOptions()
    {
      $sidenailsAdminOptions = array(
            'sidenails_nb_article' => '10',
            'sidenails_cat' => '',
            'thumbnail_source' => 'attachment',
            'sidenails_widget_title' => __("Derniers articles"),
            'have_image' => 0,
            'cf_name' => 'sidenails_cf',
            'sidenails_thumb_width' => $this->default_thumbnail_width,
            'sidenails_thumb_height' => $this->default_thumbnail_height
      );
      $sidenailsOptions = get_option($this->adminOptionsName);
      if (!empty($sidenailsOptions))
      {
        foreach ($sidenailsOptions as $key => $option)
        $sidenailsAdminOptions[$key] = $option;
      }
      update_option($this->adminOptionsName, $sidenailsAdminOptions);
      
      return $sidenailsAdminOptions;
    }

    function init()
    {
      $this->getAdminOptions();
    }

    /**
     *
     * Affiche la page d'admin du plugin
     */
    function printAdminPage()
    {
      $options = $this->getAdminOptions();
      if (isset($_POST['update_sidenailsSettings']))
      {
        if (isset($_POST['thumbnail_source']))
        {
          $options['thumbnail_source'] = $_POST['thumbnail_source'];
        }

        if(isset($_POST['have_image']))
        {
          $options['have_image'] = $_POST['have_image'];
        }else{
          $options['have_image'] = 0;
        }
        
        if(isset($_POST['show_credits']))
        {
          $options['show_credits'] = $_POST['show_credits'];
        }else{
          $options['show_credits'] = 0;
        }
        
        if (isset($_POST['cf_name']))
        {
          $options['cf_name'] = $_POST['cf_name'];
        }else{
          $options['cf_name'] = "sidenails_cf";
        }
        
        if (isset($_POST['sidenails_thumb_width']))
        {
          $options['sidenails_thumb_width'] = $_POST['sidenails_thumb_width'];
        }else{
          $options['sidenails_thumb_width'] = $this->default_thumbnail_width;
        }
        
        if (isset($_POST['sidenails_thumb_height']))
        {
          $options['sidenails_thumb_height'] = $_POST['sidenails_thumb_height'];
        }else{
          $options['sidenails_thumb_height'] = $this->default_thumbnail_height;
        }

        update_option($this->adminOptionsName, $options);
        print '<div class="updated"><p><strong>';
        _e("Paramètres mis à jour", "sidenails");
        print '</strong></p></div>';
         
      }
      include('admin_settings.php'); // include du formulaire HTML
    }

    /**
     *
     * affichage du widget
     * @param unknown_type $args
     */
    function widget($args, $instance)
    {
      extract($args);
      $title = apply_filters( 'sidenails_widget_title', $instance['sidenails_widget_title'] );
      $options = get_option($this->adminOptionsName);
      $thumbnail_source = $options['thumbnail_source'];
      $show_credits = $options['show_credits'];
      //extract($args);
      print $before_widget;
      print $before_title;
      print $title;
      print $after_title;
      include('widget.php');
      print $after_widget;
    }

    /**
     *
     * affichage du panneau de controle du widget
     * @param unknown_type $args
     */
    function form($instance)
    {
      //$data = get_option($this->adminOptionsName);
      $instance = wp_parse_args( (array) $instance, array( 	'sidenails_nb_article' => '10',
                                                            'sidenails_cat' => '',
                                                            'sidenails_tag' => '',
                                                            'thumbnail_source' => 'attachment',
                                                            'sidenails_widget_title' => __("Derniers articles"),
                                                            'sidenails_posts' => '',
                                                            'title' => '' ) );
      //le parametre "title" sert uniquement pour mettre un titre customisé dans l'interface de gestion de widget. Il DOIT s'appeler title, sinon ca marche pas :(
      $sidenails_nb_article = strip_tags($instance['sidenails_nb_article']);
      $sidenails_cat = strip_tags($instance['sidenails_cat']);
      $thumbnail_source = strip_tags($instance['thumbnail_source']);
      $sidenails_widget_title = strip_tags($instance['sidenails_widget_title']);
      $sidenails_tag = strip_tags($instance['sidenails_tag']);
      $sidenails_posts = strip_tags($instance['sidenails_posts']);
      $title = $sidenails_widget_title;
      //retrouver la liste des categories
      $list_categorie = get_categories(array(	'type'                     => 'post',
                                             	'child_of'                 => 0,
                                            	'parent'                   => '',
                                            	'orderby'                  => 'name',
                                            	'order'                    => 'ASC',
                                            	'hide_empty'               => 1,
                                            	'hierarchical'             => 1,
                                            	'exclude'                  => '',
                                            	'include'                  => '',
                                            	'number'                   => '',
                                            	'taxonomy'                 => 'category',
                                            	'pad_counts'               => false));
      include('widget_control.php');
    }

    //save the widget
    function update($new_instance, $old_instance)
    {
      $instance = $old_instance;
      if(intval($new_instance['sidenails_nb_article']))
      {
        $instance['sidenails_nb_article'] = $new_instance['sidenails_nb_article'];
      }

      if(strlen(trim($new_instance['sidenails_widget_title'])) > 0)
      {
        $instance['sidenails_widget_title'] = strip_tags(trim($new_instance['sidenails_widget_title']));
      }
      $instance['sidenails_cat'] = $new_instance['sidenails_cat'];
      $instance['sidenails_tag'] = strip_tags(trim($new_instance['sidenails_tag']));
      $instance['sidenails_posts'] = strip_tags(trim($new_instance['sidenails_posts']));
      return $instance;
    }


    /**
     *
     * permet de retrouver une ng gallery lié a un post, en parsant le contenu du post
     * @param unknown_type $postid
     */
    function getGalleryFromPost($postid)
    {
      $post = get_post($postid);
      $content = $post->post_content;
      //var_dump($content);
      $pattern = '/\[nggallery\s+id=(\d+)\]/i';
      //var_dump($content);
      if(preg_match($pattern, $content, $matches))
      {
        $gal_id = $matches[1];
        return $gal_id;
      }
    }

    public function getImagesLink($postid=0, $size='thumbnail')
    {
      $options = get_option($this->adminOptionsName);
      $thumbnail_source = $options['thumbnail_source'];
      $cf_name = $options['cf_name'];
      $sortie = 0;
      //var_dump($thumbnail_source);// 'ngg'
      if ($postid<1){
        $postid = get_the_ID();
      }
      if($thumbnail_source == 'attachment')
      {
        if ($images = get_children(array(
         'post_parent' => $postid,
         'post_type' => $thumbnail_source,
         'order' => 'ASC',
         'orderby' => 'menu_order',
         'numberposts' => 1,
         'post_mime_type' => 'image',))) 
        {
          //   print '<pre>'; print_r($images);//exit;
          foreach($images as $image) 
          {
            $attachment = wp_get_attachment_image_src($image->ID, $size);
            //print '<pre>'; print_r($attachment);exit;
            $sortie = $attachment[0];
          }
        }
      }
      elseif($thumbnail_source == 'post-thumbnails')
      {
        if (has_post_thumbnail($postid))
        {
          $image = wp_get_attachment_image_src(get_post_thumbnail_id($postid), 'post-thumbnail');
          $sortie = $image[0];
        }
      }
      elseif($thumbnail_source == 'ngg')
      {
        if(class_exists('nggdb'))
        {
          //retrouver la galerie liée au post
          $gal_id = $this->getGalleryFromPost($postid);
          //var_dump($gal_id);
          if(intval($gal_id))
          {
            $picturelist = nggdb::get_gallery($gal_id, 'pid', 'ASC');
            if($picturelist && count($picturelist) > 0)
            {
              $thumb = current($picturelist);
              $sortie = $thumb->thumbURL;
            }
          }
        }
      }elseif($thumbnail_source == 'custom-field' && $cf_name)
      {
        $temp = get_post_custom_values($cf_name, $postid);
        $sortie = (is_array($temp) && count($temp) > 0) ? $temp[0] : null;
      }
      return $sortie;
    }



    /**
     * Permet de retrouver les dernieres images suivant les parametres donnés
     * @param unknown_type $instance instance du Widget
     * @return string : le code html qui sera affiché
     */    
    function LastPostsImages( $instance )
    {
      //variables globales
      $options = get_option($this->adminOptionsName);
      $have_image = $options['have_image'];
      $thumbnail_source = $options['thumbnail_source'];
      $cf_name = $options['cf_name'];
      $sidenails_width = $options['sidenails_thumb_width'];
      $sidenails_height = $options['sidenails_thumb_height'];
      
      //variables de l'instance du widget
      $nbimages = apply_filters( 'sidenails_nb_article', $instance['sidenails_nb_article'] );
      $cat = apply_filters( 'sidenails_cat', $instance['sidenails_cat'] );
      $tag = apply_filters( 'sidenails_tag', $instance['sidenails_tag'] );
      $posts = apply_filters( 'sidenails_posts', $instance['sidenails_posts']);
      if(trim($posts) && strlen(trim($posts)) > 0)
      {
        $posts = explode(",",$posts);
      }
      $template = '';
      $request = array(
      'showposts'        => $nbimages,
      'category_name'    => $cat,
      'cat'              => '',
      'tag'              => $tag,
      'post__in'				 => $posts,
      'author_name'      => '',
      'author'           => '',
      'orderby'          => 'post_date',
      'order'            => 'desc',
      'offset'           => '',
      'day'              => '',
      'monthnum'         => '',
      'year'             => '',
      'w'                => '',
      'tag__not_in'      => '',
      'caller_get_posts' => 1);
       
      if($have_image)//on va utiliser des filtres pour modifier le where, donc ne pas les supprimer
      {
        if($thumbnail_source == 'custom-field')
        {
          $request['meta_key'] = $cf_name;
        }elseif($thumbnail_source == 'post-thumbnail')
        {
          $request['meta_key'] = '_thumbnail_id';
        }else{
          $request['suppress_filters'] = false;
        }
      }
      $filter = null;
      //var_dump($thumbnail_source);exit;
      switch($thumbnail_source)
      {
        case 'attachment':
        case 'post-thumbnails':
          //$filter = 'sidenails_search_where_attachment';
          break;
        case 'ngg':
          $filter = 'sidenails_search_where_ngg';
          break;
      }
      if($have_image && $filter)
      {
        add_filter('posts_where', $filter );
      }
       
      $pageposts = get_posts($request);
      //var_dump($pageposts);exit;
      if($have_image && $filter)
      {
        remove_filter('posts_where', $filter);
      }
       
      if ($pageposts)
      {
        $com=0;
        
        
        if($sidenails_width && $sidenails_height)//on redefini les styles
        {
          $real_width = $sidenails_width;
          $real_height = $sidenails_height;
        }elseif(class_exists('nggdb') && $thumbnail_source == 'ngg')
        {
          $real_width = $this->ngg_thumbnail_width;
          $real_height = $this->ngg_thumbnail_height;
        }else{
          $real_width = $this->default_thumbnail_width;
          $real_height = $this->default_thumbnail_height;
        }
        $template .= "<style>";
        $template .= "
          	.sidenails a { height: ".$real_height."px; width: ".$real_width."px; }
            .sidenails strong { height: ".$real_height."px; width: ".$real_width."px; }";
        $template .= "</style>";
        
        foreach ($pageposts as $post)
        {
          $srcimg = $this->getImagesLink($post->ID);
          //var_dump($srcimg);
          //$local_img = str_replace("http://www.tranchesdunet.com",$_SERVER["DOCUMENT_ROOT"], $srcimg);
          //TODO: a enlever avant mise en prod
          $local_img = str_replace(get_bloginfo('url'),$_SERVER["DOCUMENT_ROOT"], $srcimg);
          if($srcimg && file_exists($local_img))
          {
            $srcimg = '<img src="'.$srcimg.'" width="'.$real_width.'" height="'.$real_height.'" alt="photos '.$post->post_title.'" title="'.$post->post_title.'" />';
            $template .= '<a href="'.get_permalink($post->ID).'" title="'.$post->post_title.'">'.$srcimg.'<span></span><strong>'.$post->post_title.'</strong></a>';
            $com++;
          }
        }
      }
      return $template;
    }
  }

  function add_script_config()
  {
    ?>
    <script type="text/javascript">
    // Function to add auto suggest
    function setSuggest(id) {
        jQuery('#' + id).suggest("<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php?action=ajax-tag-search&tax=post_tag");
    }
    </script>
    <?php
  }
}

if(class_exists("sidenails"))
{
  $inst_sidenails = new sidenails();
}
if (!function_exists("sidenails_ap"))
{
  function sidenails_ap() {
    global $inst_sidenails;
    if (!isset($inst_sidenails)) {
      return;
    }
    if (function_exists('add_options_page'))
    {
      add_options_page('SideNails', 'SideNails', 9, basename(__FILE__), array(&$inst_sidenails, 'printAdminPage'));
    }
  }
}
add_action('admin_menu', 'sidenails_ap');
function register_sidenails(){
  register_widget('sidenails');
  wp_enqueue_style("sidenails", WP_PLUGIN_URL."/sidenails/css/sidenails.css");
}
add_action('init', 'register_sidenails', 1);
// Register hooks
add_action('admin_print_scripts', 'add_suggest_script');
add_action('admin_head', 'add_script_config');
load_plugin_textdomain('sidenails', null, '/sidenails/lang/');
/**
 * Add suggest script to admin page
 */
function add_suggest_script() {
  // Build in tag auto complete script
  wp_enqueue_script( 'suggest' );
}

function sidenails_search_where_ngg( $where )
{
  $where .= " AND post_content LIKE '%[nggallery%' ";
  return $where;
}

function sidenails_search_where_attachment( $where )
{
  $where .= " AND post_content LIKE '%<img%' AND post_content LIKE '%/wp-content/uploads/%' ";
  return $where;
}