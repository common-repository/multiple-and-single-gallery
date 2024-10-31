<?php
/*
Plugin Name: Multiple And Single Gallery
Plugin URI:  http://www.webgensis.com/products.html
Description: Multiple And Single Gallery Plugin With Filter
Version:     1.0
Author:      Webgensis
Author URI:  http://www.webgensis.com
License:     GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wporg
*/

/* cuustom post type */

function wgsmg_create_post_gallery() { 
    register_post_type( 'Gallery',
        array(
            'labels' => array(
                'name' => __( 'Gallery' ),
                'singular_name' => __( 'Gallery' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'gallery'),
        )
    );
	register_taxonomy('Cgallery','gallery', array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'category' ),
    ));
	add_post_type_support( 'gallery', 'thumbnail' );
}
add_action( 'init', 'wgsmg_create_post_gallery' );

add_filter( 'manage_edit-gallery_columns', 'wgsmg_edit_gallery_columns' ) ;
function wgsmg_edit_gallery_columns( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'gallery' ),
		'shortcode' => __( 'Shortcode' ),
		'date' => __( 'Date' )
	);
	return $columns;
}

add_action( 'manage_gallery_posts_custom_column', 'wgsmg_manage_movie_columns', 10, 2 );
function wgsmg_manage_movie_columns( $column, $post_id ) {
	global $post;
    echo "[wgsmg_gallery_view gallery-id='".$post_id."']";
}


/**** add meta box ****/
function wgsmg_gallery_meta_box_add()
{
    add_meta_box( 'gallery-meta-box-id', 'More Gallery Images', 'wgsmg_meta_box', 'gallery', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'wgsmg_gallery_meta_box_add' );
function wgsmg_scripts_method() {	
	wp_enqueue_style('custom-front', plugin_dir_url( __FILE__ ) . 'css/custom-front.css' );
	wp_enqueue_style('custom-ad-min', plugin_dir_url( __FILE__ ) . 'css/custom-admin.css' );
	wp_enqueue_style('font-awesome-custom', plugin_dir_url( __FILE__ ) . 'font/css/font-awesome.css' );
	wp_enqueue_style('light-box-min', plugin_dir_url( __FILE__ ) . 'css/flavor-lightbox.css' );
	wp_enqueue_style('custom-front-style', plugin_dir_url( __FILE__ ) . 'Animate/css/style.css' );
	wp_enqueue_style('custom-front-animated', plugin_dir_url( __FILE__ ) . 'Animate/css/animated-masonry-gallery.css' );
	
	wp_enqueue_script( 'custom-isotope', plugin_dir_url( __FILE__ ) . 'Animate/js/jquery.isotope.min.js', array(), '1.0.0', true );
	wp_enqueue_script( 'custom-masonry', plugin_dir_url( __FILE__ ) . 'Animate/js/animated-masonry-gallery.js', array(), '1.0.0', true );	
}
add_action('wp_enqueue_scripts', 'wgsmg_scripts_method');

function wgsmg_popup() {
 	wp_enqueue_script( 'custom-flavor', plugin_dir_url( __FILE__ ) . 'js/jquery.flavor.js', array(), '1.0.0', true );	
 	wp_enqueue_script( 'custom-script', plugin_dir_url( __FILE__ ) . 'js/script.js', array(), '1.0.0', true );		
}
add_action('wp_footer', 'wgsmg_popup');


function wgsmg_admin_scripts() {
        wp_enqueue_script('ad-min-custom-script', plugins_url('/js/admin_script.js', __FILE__ ),array('jquery'), '', true );
        wp_enqueue_style( 'custom-ad-min', plugins_url('/css/custom-admin.css', __FILE__ ) ); 	
        wp_enqueue_style( 'font-awesome-custom', plugins_url('/font/css/font-awesome.css', __FILE__ ) ); 		
}
add_action('admin_enqueue_scripts', 'wgsmg_admin_scripts');
/**** display  meta box ****/
function wgsmg_meta_box(){
$post_id = get_the_ID(); 	
                wp_nonce_field( basename( __FILE__ ), 'prfx_nonce' );
                $prfx_stored_meta = get_post_meta($post_id);		
                ?>
                <div class="wrap">
                <input type="button" id="add-input" value="Add Image">
				<?php 
				$get_gallery_img = get_post_meta($post_id, 'gallery-image',true);
				if( !empty( $get_gallery_img) ){
				foreach($get_gallery_img as $k => $v){
					echo '<div><span class="image_label"><img src ="'.wp_get_attachment_url( $v ).'" style="width:100px;height:100px;"></span><input type="hidden" name="meta-image-id[]" class="meta-image-id" value="'.$v.'"><input type="button" class="meta-image-button button" value="Upload Image"><span class="button remove-row">Remove</span></div>';
				}
				}
				?>
                <div id="images-container">
                </div><!-- end images container -->
                </div>               									
<?php				
}
/***  Save galley ***/
function wgsmg_save_gallery_fields_meta( $post_id ) {   
global $wpdb;
$post_id = get_the_ID(); 
        $is_autosave = wp_is_post_autosave( $post_id );
        $is_revision = wp_is_post_revision( $post_id );
        $is_valid_nonce = ( isset( $_POST[ 'prfx_nonce' ] ) && wp_verify_nonce( $_POST[ 'prfx_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
        if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
            return;
        }
		$get_gallery_img = get_post_meta($post_id, 'gallery-image',true);
		if( !empty( $get_gallery_img) ){
			add_post_meta($post_id, 'gallery-image',true);
		}
       if( isset( $_POST[ 'meta-image-id' ] ) ) { 
		$meta_images_id01 = array();
		foreach($_POST['meta-image-id'] as $meta_images_id){
			$meta_images_id01[] = $meta_images_id;			
		}   		      
		update_post_meta($post_id, 'gallery-image', $meta_images_id01);	
       }
}
add_action( 'save_post', 'wgsmg_save_gallery_fields_meta' );

/***  Single Gallery View Short code ***/
function wgsmg_view_shortcode( $atts ) {   
global $wpdb;
$post_id = $atts['gallery-id']; 
$get_gallery_img = get_post_meta($post_id, 'gallery-image',true);
$simple_and_3d_condition = get_option('simple_and_3d_condition');
if($simple_and_3d_condition == 1){
?>
    <div class="gallery-header_popup"><h3><?php get_the_title($post_id) ?></h3></div>
    <div class="container" data-flavor="myGallery3">
	    <?php if( !empty( $get_gallery_img) ){ ?>
    	<div id="carousel">
		    <?php foreach($get_gallery_img as $k => $v){ ?> 
			<figure><div class="example-image-link11" data-flavor-src="<?php echo wp_get_attachment_url($v); ?>" data-lightbox="example-set" ><img src="<?php echo wp_get_attachment_url($v); ?>" style="width:186px;height:116px;"></div></figure>
            <?php } ?>			
		</div>
		<?php } ?>	
	</div>
<?php } else { ?>	
	<div id="gallery-content-single">
	<div id="gallery-content-center-single" class="gallery-content-center-manage" data-flavor="myGallery">
		<?php if( !empty( $get_gallery_img) ){ ?>
			<?php foreach($get_gallery_img as $k => $v){ ?> 
				<div class="gallery_images_animated" data-flavor-src="<?php echo wp_get_attachment_url($v); ?>"><img src="<?php echo wp_get_attachment_url($v); ?>" class="all test1  isotope-item" style="position: absolute; left: 0px; top: 0px; transform: translate3d(0px, 0px, 0px);"></div>
			<?php } ?>
		<?php } ?>
	</div>
	</div>
<?php } 
}
add_shortcode('wgsmg_gallery_view', 'wgsmg_view_shortcode');
/***  All Gallery View Short code ***/
function wgsmg_gallery_view_shortcode( $atts ) {   
global $wpdb;
  $Allcategories = get_terms( 'Cgallery', 'orderby=count&hide_empty=0' );
    $all_gallery_pic = get_posts( array('post_type' => 'gallery') ); 
  ?> 
<script>
jQuery(window).load(function () {
var button = 1;
var button_class = "gallery-header-center-right-links-current";
var jQuerycontainer = jQuery('#gallery-content-center');   
jQuerycontainer.isotope({itemSelector : 'img'});
function check_button(){
	jQuery('.gallery-header-center-right-links').removeClass(button_class);
	<?php if( !empty( $Allcategories) ){ ?>
	        if(button==1){
			  jQuery("#filter-all").addClass(button_class);
			}
		    <?php 
			$i = 2;
			foreach($Allcategories as $k => $v){ ?> 
			if(button==<?php echo $i; ?>){
			  jQuery("#filter-<?php echo $v->slug; ?>").addClass(button_class);
			}
            <?php $i++; } ?>			
	<?php } ?>		
}	
	<?php if( !empty( $Allcategories) ){ ?>
	        jQuery("#filter-all").click(function() { jQuerycontainer.isotope({ filter: '.all' }); button = 1; check_button(); });
		    <?php 
			$i = 2;
			foreach($Allcategories as $k => $v){ ?> 
			  jQuery("#filter-<?php echo $v->slug; ?>").click(function() {  jQuerycontainer.isotope({ filter: '.<?php echo $v->slug; ?>' }); button = <?php echo $i; ?>; check_button();  });
            <?php $i++; } ?>			
	<?php } ?>
check_button();
});	
</script>

<div id="gallery">
<div id="gallery-header">
<div id="gallery-header-center">

<div id="gallery-header-center-right">
    <?php if( !empty( $Allcategories) ){ ?>
    	<div id="gallery_view">
		    <div class="gallery-header-center-right-links" id="filter-all">All</div>
		    <?php foreach($Allcategories as $k => $v){ ?> 
			<div class="gallery-header-center-right-links" id="filter-<?php echo $v->slug; ?>"><?php echo $v->name; ?></div>
            <?php } ?>			
		</div>
	<?php } ?>  
</div>
</div>
</div>
<div id="gallery-content">
<div id="gallery-content-center" data-flavor="myGallery2">

    <?php 
	if ( !empty( $all_gallery_pic ) ){
		foreach($all_gallery_pic as $all_gallery_pics){
			$terms = get_the_terms($all_gallery_pics->ID, 'Cgallery');			
			if ( !empty( $terms ) ){
			$slug_class = '';	
			foreach($terms as $termss){
					$slug_class .= $termss->slug;
					$slug_class .= ' ';
			}
			}	
			$feat_image = wp_get_attachment_url( get_post_thumbnail_id($all_gallery_pics->ID) );
			echo '<div class="gallery_images_animated"  data-flavor-src="'.$feat_image.'" data-lightbox="example-set" ><img src="'.$feat_image.'" class="all '.$slug_class.'"></div>';		
	}
	}
	?>
</div>
</div>
</div>	
<?php	
}
add_shortcode('wgsmg_all_gallery_view', 'wgsmg_gallery_view_shortcode');
add_action( 'admin_menu', 'wgsmg_register_settings' );
function wgsmg_register_settings()
{	
    add_options_page('Gallery Settings', 'Gallery Settings', 'manage_options', gallery_settings, 'wgsmg_settings' );
	//=================//
	add_option('simple_and_3d_condition', '0', '', 'yes' );
    /************************/
	register_setting( 'wgsmg_three_d_or_simple_setting', 'simple_and_3d_condition');
}

function wgsmg_settings()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
	<form action="options.php" method="post">
	    <?php 
		settings_fields('wgsmg_three_d_or_simple_setting');
	    $simple_and_3d_condition = get_option('simple_and_3d_condition');
		?>
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <div class="custum_gallery_setting">
		   <h3>1. All Gallery View Short Code: <b>[wgsmg_all_gallery_view]</b></h3>
		   <h3>2. For Separate Gallery View Short Code: <b>After create gallery you will find short code in column.</b></h3>
			<div class="show_hide_simple_and_3d">
			<b>Gallery Types</b>
			  <input type="radio" name="simple_and_3d_condition" value="0" <?php if($simple_and_3d_condition == 0){ ?> checked="checked" <?php } ?>>Animate Gallery    <input type="radio" name="simple_and_3d_condition" value="1" <?php if($simple_and_3d_condition == 1){ ?> checked="checked" <?php } ?>>3d Gallery					
			</div>
		</div>
		<?php  
		submit_button(); ?>
    </form>
    </div>
    <?php
}