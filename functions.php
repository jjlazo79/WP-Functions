http://www.whatsapp.com/faq/es/android
<a class="whatsapp" href="whatsapp://send?text=<?php echo get_permalink(); ?>" data-action="share/whatsapp/share">WhatsApp</a>
@media handheld, only screen and (min-width: 640px) { .whatsapp{ display:none; } } @media only screen and (min-width: 640px) { .whatsapp{ display:none; } } 

<?php
/*
Plugin Name: Funciones
Plugin URI: http://mente-encendida.com/
Description: Plugin para liberar de funciones el fichero <code>functions.php</code> y activarlo a placer (o no) .
Version: 1.0
Author: Fernando Tellado y Jose Lazo
Author URI: http://joselazo.es
License: GPLv2 o posterior
*/

// TO-DO
// Añadir un map-route

/*
# LOGIN
	## Logo personalizado en login
	## Personalizar url logo acceso
	## Cambiar texto alt del logo de login
# 
# 
# 
# 
# 
# 
# 
# 
# 
# 
 */


// Logo personalizado en login *****************************************************************************

add_action("login_head", "my_login_head");
function my_login_head() {
	echo "
	<style>
	body.login #login h1 a {
		background: url('".get_bloginfo('template_url')."/images/loginlogo.png') no-repeat scroll center top transparent;
		height: 135px;
		width: 135px;
	}
	</style>
	";
}


// Personalizar url logo acceso ******************************************************************************
add_action( 'login_headerurl', 'my_custom_login_url' );
function my_custom_login_url() {
return 'http://mente-encendida.com';
}


// Cambiar texto alt del logo de login *****************************************************************
add_action("login_headertitle","my_custom_login_title");
function my_custom_login_title()
{
return 'Diseñado y creado para ti por Mente Encendida';
}


// Mostrar los 4 últimos post. Se pone en una plantilla, no en el fuctions.php
?>	
<div class="ultimas-entradas">
	<?php query_posts('showposts=4');?>
	<?php if (have_posts()) : while (have_posts()) : the_post();?>
    <ul>
        <li>
            <h2><a href="<?php the_permalink();?>" title="<?php the_title_attribute();?>"><?php the_title();?></a></h2>
        </li>
     <ul>
	<?php endwhile; endif;?>
</div>
<?php


// Añadir menu en la derecha
register_nav_menu( 'primary', __( 'Default', 'nombre_del_tema' ) );
register_nav_menu( 'secundary', __( 'Menu Derecha', 'nombre_del_tema' ) );
       // en el archivo de la plantilla donde quieres el menu, por ejmplo footer.php
wp_nav_menu( array( 'theme_location' => 'secundary' ) );



// Añadir campos sociales a los perfiles y elimina (unset) los inútiles *********************************
function add_redessociales_contactmethod( $contactmethods ) {
  // Añade Twitter
  $contactmethods['twitter'] = 'Twitter';
  // Añade Facebook
  $contactmethods['facebook'] = 'Facebook';
  // Quita Yahoo, IM, AIM y Jabber
  unset($contactmethods['yim']);
  unset($contactmethods['aim']);
  unset($contactmethods['jabber']);
  return $contactmethods;
}
add_filter('user_contactmethods','add_redessociales_contactmethod',10,1);


// Añadir nuevos tipos de archivo para subir **************************************************************************
 add_filter ( 'upload_mimes' , 'masMimes' ) ;
 function masMimes ( $mimes )
 {
	 $mimes = array_merge ( $mimes , array (
		 'pages|numbers|key' => 'application/octet-stream'
	 ) ) ;

	 return $mimes ;
 } 
 

// Cambiar texto de pie de página en el escritorio ********************************************************************
function remove_footer_admin () {
    echo "Sitio diseñado y creado por Mente Encendida";
} 

add_filter('admin_footer_text', 'remove_footer_admin'); 


// Quitar menu de admin ********************************************************************************

function my_remove_menu_pages() {
    $user_id = get_current_user_id();
    if (2 == $user_id) {

        remove_menu_page('edit.php?post_type=page'); // Páginas
        remove_menu_page('themes.php'); // Apariencia
        remove_menu_page('tools.php'); // Herramientas
        remove_menu_page('options-general.php'); // Ajustes
        remove_menu_page('link-manager.php'); // Enlaces
        remove_menu_page('plugins.php'); //Plugins
        remove_menu_page('revslider'); //Revolution Slider
        remove_menu_page('ig-pb-settings'); //IG PageBuilder
        remove_menu_page('wpcf7'); //Contact Form
        remove_menu_page('itsec'); //Security
        remove_menu_page('wangguard_conf'); //WangGuard
        remove_menu_page('wpacumbamail'); //Acumbamail
        remove_menu_page('bp-general-settings'); //BuddyPress
        remove_menu_page('wpseo_dashboard'); //SEO


    } else {
        remove_menu_page('admin.php'); //Opciones del tema
    }
}

add_action( 'admin_menu', 'my_remove_menu_pages' );


// añade enlaces/menús a la barra de admin *****************************************************************
function mytheme_admin_bar_render() {
	global $wp_admin_bar;
	if ( !is_super_admin() || !is_admin_bar_showing() )
        return;
	$wp_admin_bar->add_menu( array(
		'parent' => 'comments', // usa 'false' para que sea un menú superior o sino indica el ID del menú superior
		'id' => 'false', // ID del enlace, por defecto debe ser un valor de título
		'title' => __('Disqus'), // título del enlace
		'href' => admin_url( 'edit-comments.php?page=disqus') // hombre del archivo al que enlaza, en mi caso disqus
	));
}
add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );


//permalinks canónicos *************************************************************************************
function set_canonical() {
  if ( is_single() ) {
	global $wp_query;
	echo '<link rel="canonical" href="'.get_permalink($wp_query->post->ID).'"/>';
  }
}
add_action('wp_head', 'set_canonical');


//Ejecutar PHP en widgets de texto
function ejecutar_php($html){
    if(strpos($html,"<"."?php")!==false){
        ob_start();
        eval("?".">".$html);
        $html=ob_get_contents();
        ob_end_clean();
        }
    return $html;
}

add_filter('widget_text','ejecutar_php',100);


//soporte de Twitter oEmbed *************************************************************************************
add_filter('oembed_providers','twitter_oembed');
function twitter_oembed($a) {
	$a['#http(s)?://(www\.)?twitter.com/.+?/status(es)?/.*#i'] = array( 'http://api.twitter.com/1/statuses/oembed.{format}', true);
	return $a;
}


//distinto color segun estado de entrada **********************************************************************************
function posts_status_color() {
?>
  <style>
  .status-draft { background: #FCE3F2 !important; }
  .status-pending { background: #87C5D6 !important; }
  .status-publish { /* por defecto */ }
  .status-future { background: #C6EBF5 !important; }
  .status-private { background: #F2D46F; }
  </style>
<?php
}
add_action('admin_footer','posts_status_color');


//cargar traducciones en el childtheme *************************************************************************************
function weg_localisation() {
    unload_textdomain( 'woothemes' );
    load_textdomain('woothemes', get_stylesheet_directory() . '/lang/es_ES.mo');
    unload_textdomain('woocommerce');
    load_textdomain('woocommerce', get_stylesheet_directory() . '/woocommerce/i18n/languages/woocommerce-es_ES.mo');
}
add_action('init', 'weg_localisation');


// Añadir shortcodes en el widget de texto de la sidebar
if (function_exists ('shortcode_unautop')) {
	add_filter ('widget_text', 'shortcode_unautop');
}
add_filter ('widget_text', 'do_shortcode');


// Condicional para mostrar distintos elementos a usuarios registrados y visitantes ****************************************

	if ( is_user_logged_in() ) {
		?>
	  
	  	<a class="tile tile-members" href="<?php echo home_url(); ?>/<?php _e('members', 'Cinematix'); ?>\"><span class="tile-title"><?php _e('MEMBERS', 'Cinematix'); ?></span></a>

		<a class="tile tile-groups" href="<?php echo home_url(); ?>/<?php _e('groups', 'Cinematix'); ?>\"><span class="tile-title"><?php _e('GROUPS', 'Cinematix'); ?></span></a>

		<a class="tile tile-activity" href="<?php echo home_url(); ?>/<?php _e('activity', 'Cinematix'); ?>\"><span class=\"tile-title\"><?php _e('ACTIVITY', 'Cinematix'); ?></span></a>
		<?php
	 } else {
	 	?>
		    
		<?php
	 }



?>


<!-- CODIGO PARA PONER LAS 3 ULTIMAS NOTICIAS DONDE QUIERAS *****************************************************-->

<div id="widgetnoticias">
	
	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('NoticiasHeader') ) : ?>;
	<?php endif; ?>

</div>

	<!-- Y EN EL FUNCTIONS.PHP (cambiando 'NoticiasHeader' por el nombre que 
	quieras ponerle a la zona de widget, 'theme5820' por el nombre del 
	theme en cuestión y 'Noticias-Header' por el ID que le quieras poner.) -->

<?php

	register_sidebar( array(
			'name' => __( 'NoticiasHeader', 'theme5820' ),
			'id' => 'Noticias-Header',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h1 class="widget-title">',
			'after_title' => '</h1>',
		) );
?>

<?php 
// Theme the TinyMCE editor
// You should create custom-editor-style.css in your theme folder
add_editor_style('custom-editor-style.css');


// Enable thumbnails
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size(200, 200, true); // Normal post thumbnails


// Custom CSS for the login page
// Create wp-login.css in your theme folder
function wpfme_loginCSS() {
	echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('template_directory').'/wp-login.css"/>';
}
add_action('login_head', 'wpfme_loginCSS');


// Enable widgetable sidebar
// You may need to tweak your theme files, more info here - http://codex.wordpress.org/Widgetizing_Themes
if ( function_exists('register_sidebar') )
	register_sidebar(array(
	'before_widget' => '<aside>',
	'after_widget' => '</aside>',
	'before_title' => '<h1>',
	'after_title' => '</h1>',
));


// Remove the admin bar from the front end
add_filter( 'show_admin_bar', '__return_false' );


// Customise the footer in admin area
function wpfme_footer_admin () {
	echo 'Theme designed and developed by <a href="#" target="_blank">YourNameHere</a> and powered by <a href="http://wordpress.org" target="_blank">WordPress</a>.';
}
add_filter('admin_footer_text', 'wpfme_footer_admin');


// Set a maximum width for Oembedded objects
if ( ! isset( $content_width ) )
$content_width = 660;


// Add default posts and comments RSS feed links to head
add_theme_support( 'automatic-feed-links' );


// Put post thumbnails into rss feed
function wpfme_feed_post_thumbnail($content) {
	global $post;
	if(has_post_thumbnail($post->ID)) {
		$content = '' . $content;
	}
	return $content;
}
add_filter('the_excerpt_rss', 'wpfme_feed_post_thumbnail');
add_filter('the_content_feed', 'wpfme_feed_post_thumbnail');


// Add custom menus
register_nav_menus( array(
	'primary' => __( 'Primary Navigation', 'wpfme' ),
	//'example' => __( 'Example Navigation', 'wpfme' ),
) );


// Custom CSS for the whole admin area
// Create wp-admin.css in your theme folder
function wpfme_adminCSS() {
	echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('template_directory').'/wp-admin.css"/>';
}
add_action('admin_head', 'wpfme_adminCSS');


// Enable admin to set custom background images in 'appearance > background'
add_custom_background();


// Randomly chosen placeholder text for post/page edit screen
function wpfme_writing_encouragement( $content ) {
	global $post_type;
	if($post_type == "post"){
	$encArray = array(
		// Placeholders for the posts editor
		"Test post message one.",
		"Test post message two.",
		"<h1>Test post heading!</h1>"
		);
		return $encArray[array_rand($encArray)];
	}
	else{ $encArray = array(
		// Placeholders for the pages editor
		"Test page message one.",
		"Test page message two.",
		"<h1>Test Page Heading</h1>"
		);
		return $encArray[array_rand($encArray)];
	}
}
add_filter( 'default_content', 'wpfme_writing_encouragement' );


//change amount of posts on the search page - set here to 100
function wpfme_search_results_per_page( $query ) {
	global $wp_the_query;
	if ( ( ! is_admin() ) && ( $query === $wp_the_query ) && ( $query->is_search() ) ) {
	$query->set( 'wpfme_search_results_per_page', 100 );
	}
	return $query;
}
add_action( 'pre_get_posts',  'wpfme_search_results_per_page'  );


//create a permalink after the excerpt
function wpfme_replace_excerpt($content) {
	return str_replace('[...]',
		'<a class="readmore" href="'. get_permalink() .'">Continue Reading</a>',
		$content
	);
}
add_filter('the_excerpt', 'wpfme_replace_excerpt');


function wpfme_has_sidebar($classes) {
    if (is_active_sidebar('sidebar')) {
        // add 'class-name' to the $classes array
        $classes[] = 'has_sidebar';
    }
    // return the $classes array
    return $classes;
}
add_filter('body_class','wpfme_has_sidebar');


// Create custom sizes
// This is then pulled through to your theme useing the_post_thumbnail('custombig');
if ( function_exists( 'add_image_size' ) ) {
	add_image_size('customsmall', 300, 200, true); //narrow column
	add_image_size('custombig', 400, 500, true); //wide column
}


// Stop images getting wrapped up in p tags when they get dumped out with the_content() for easier theme styling
function wpfme_remove_img_ptags($content){
	return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}
add_filter('the_content', 'wpfme_remove_img_ptags');


// Call the google CDN version of jQuery for the frontend
// Make sure you use this with wp_enqueue_script('jquery'); in your header
function wpfme_jquery_enqueue() {
	wp_deregister_script('jquery');
	wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, null);
	wp_enqueue_script('jquery');
}
if (!is_admin()) add_action("wp_enqueue_scripts", "wpfme_jquery_enqueue", 11);


//custom excerpt length
function wpfme_custom_excerpt_length( $length ) {
	//the amount of words to return
	return 20;
}
add_filter( 'excerpt_length', 'wpfme_custom_excerpt_length');


// Call Googles HTML5 Shim, but only for users on old versions of IE
function wpfme_IEhtml5_shim () {
	global $is_IE;
	if ($is_IE)
	echo '<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->';
}
add_action('wp_head', 'wpfme_IEhtml5_shim');


// Remove the version number of WP
// Warning - this info is also available in the readme.html file in your root directory - delete this file!
remove_action('wp_head', 'wp_generator');


// Obscure login screen error messages
function wpfme_login_obscure(){ return '<strong>Sorry</strong>: Think you have gone wrong somwhere!';}
add_filter( 'login_errors', 'wpfme_login_obscure' );


// Disable the theme / plugin text editor in Admin
define('DISALLOW_FILE_EDIT', true);


// Lista de artículos pendientes de publicar (insertar codigo en la pagina que se desee)
?> <!-- esto no -->

<div id="proximos">
    <div id="proximos_header">
          <p>Próximos artículos</p>
   </div>
    <?php query_posts('showposts=10&post_status=future'); ?>
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <div>
            <p class><b><?php the_title(); ?></b><?php edit_post_link('e',' (',')'); ?><br />
 
            <span class="datetime"><?php the_time('j. F Y'); ?></span></p>
        </div>
    <?php endwhile; else: ?><p>No hay artículos programados.</p><?php endif; ?>
</div>

<?php //esto no


// Artículos relacionados. Para usar en el loop, muestra 5 artículos relacionados con el primer tag del artículo actual

$tags = wp_get_post_tags($post->ID);
if ($tags) {
  echo 'Artículos Relacionados';
  $first_tag = $tags[0]->term_id;
  $args=array(
    'tag__in' => array($first_tag),
    'post__not_in' => array($post->ID),
    'showposts'=>5,
    'caller_get_posts'=>1
   );
  $my_query = new WP_Query($args);
  if( $my_query->have_posts() ) {
    while ($my_query->have_posts()) : $my_query->the_post(); ?>
      <p><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to < ?php the_title_attribute(); ?>"><?php the_title(); ?></a></p>
      <?php
    endwhile;
  }
}


// Artículos más vistos (insertar codigo en la pagina que se desee)
?> <!-- esto no -->

<h2>Artículos Más Vistos</h2>
<ul>
	<?php $result = $wpdb->get_results("SELECT comment_count,ID,post_title FROM $wpdb->posts ORDER BY comment_count DESC LIMIT 0 , 5");
	foreach ($result as $post) {
	setup_postdata($post);
	$postid = $post->ID;
	$title = $post->post_title;
	$commentcount = $post->comment_count;
		if ($commentcount != 0) { ?>
		 
			<li><a href="<?php echo get_permalink($postid); ?>" title="< ?php echo $title ?>">
			<?php echo $title ?></a> {<?php echo $commentcount ?>}</li>
		<?php 
		} 
	} ?>
 
</ul>

<?php //esto no


// Mostrar contenido a usuarios registrados (insertar este trozo de código en el functions.php)
add_shortcode( 'registrados', 'member_check_shortcode' );
 
function member_check_shortcode( $atts, $content = null ) {
	 if ( is_user_logged_in() && !is_null( $content ) && !is_feed() )
		return $content;
	return '';
}


// Muestra una imágen aleatoria en tu cabecera. Incluye las imágenes que quieres mostrar en una carpeta y llámalas 1.jpg, 2.jpg… e incluye esto en tu header.php:
?> <!-- esto no -->

$num = rand(1,10); //Modifica el 10 si tienes otro número de imágenes
	<div id="header" style="background:transparent url(carpetaImagenes/.jpg) no-repeat top left;">
	</div>

<?php //esto no


// Muestra tus últimos N tweets (insertar este trozo de código en el functions.php)

function get_tweets($usuario,$tweets) {
		$feed = "http://search.twitter.com/search.atom?q=from:" . $usuario . "&rpp=" . $tweets;
 	$xml = simplexml_load_file($feed);
		$boleano = 0;
	foreach($xml->children() as $child) {
		foreach ($child as $value) {
			if($value->getName() == "content") {
				$content = $value . "";
				echo "<p class='twit".$boleano."'>".$content."</p>";
			}
		}
		if($boleano == 0){
			$boleano = 1;
		}
		else if($boleano ==1){
			$boleano = 0;
		}
	}
}

// Y esto donde quieras mostrarlos
?>//esto no

<?php get_tweets('USUARIO,NumeroTWEETS'); ?>

<?php //esto no

// en el CSS puedes hacer que los tweets pares e impapres se muestren diferentes
.twit1{
	background-color:#000;
	color:#fff;
}
.twit2{
	background-color:#FFF;
	color:#000;
}

// Otra funcion
 ?>