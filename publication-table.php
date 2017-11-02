<?php

/*
Plugin Name: Publication Table
Plugin URI:  
Description: A plugin that uses Bootstrap and Jquery to create a stylish a professional looking table of publications.
Version:     0.1.0
Author:      Sebastien Georges
Author URI:  
License:     GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: publication_table
Domain Path: /languages
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

 

function pt_plugin_ressources() {

  wp_enqueue_style('ptcss', plugins_url('/css/style.css',__FILE__));
//  wp_enqueue_script( 'jqueryforme', plugins_url( '/js/jquery.js', __FILE__ ), array('jquery'));
  wp_enqueue_script( 'customjavascript', plugins_url( '/js/pt.js', __FILE__ ), array('jquery'));
  
}

add_action('wp_enqueue_scripts','pt_plugin_ressources'); 

/**
 * Used by hook: 'customize_preview_init'
 * 
 * @see add_action('customize_preview_init',$func)
 */
function pt_customizer_live_preview()
{
	wp_enqueue_script( 
		  'mytheme-themecustomizer',			//Give the script an ID
		   plugins_url('/js/theme-customizer.js',__FILE__),//Point to file
		  array( 'jquery','customize-preview' ),	//Define dependencies
		  '',						//Define a version (optional) 
		  true						//Put script in footer?
	);
}
add_action( 'customize_preview_init', 'pt_customizer_live_preview' );

function table_customise_register($wp_customize){ 
    
        $wp_customize-> add_section('table_colors', array(

			'title'=>__('Publication Table Customiser', 'publication_table'),
			'priority'=>30,    								

		));	 

		$wp_customize-> add_setting('header_color',array(		
				'transport'=>'postMessage',

		));	
        $wp_customize-> add_setting('even_color',array(		
                    'transport'=>'postMessage',
                    'default'=> '#dbdbdb'

            ));
        $wp_customize-> add_setting('header_text_color',array(	
                    'transport'=>'postMessage',

            ));	
    
    $wp_customize-> add_setting('form_color',array(	
                    'transport'=>'postMessage',

            ));	
											
		$wp_customize->add_control(new WP_Customize_Color_Control( $wp_customize, 'header_txt',array( 
				'label'=>__('Table Header Color', 'publication_table'),
				'section'=> 'table_colors',
				'settings'=>'header_color'  


		) ));
    $wp_customize->add_control(new WP_Customize_Color_Control( $wp_customize, 'form_colors',array( 
				'label'=>__('Form Color', 'publication_table'),
				'section'=> 'table_colors',
				'settings'=>'form_color'  


		) ));
    $wp_customize->add_control(new WP_Customize_Color_Control( $wp_customize, 'row',array( 
				'label'=>__('Even Row Color', 'publication_table'),
				'section'=> 'table_colors',
				'settings'=>'even_color'  


		) ));
    $wp_customize->add_control(new WP_Customize_Color_Control( $wp_customize, 'header',array( 
				'label'=>__('Header Text Color', 'publication_table'),
				'section'=> 'table_colors',
				'settings'=>'header_text_color'  


		) ));	

}

add_action('customize_register','table_customise_register');


$cell_spacing = '';
$header_color = '';
$even_color =  '';
$header_text_color = '';
$form_color = '';

$cell_spacing = '10';
$header_color = get_theme_mod('header_color');
$even_color =  get_theme_mod('even_color');
$header_text_color =  get_theme_mod('header_text_color');
$form_color = get_theme_mod('form_color');

function pt_customise_css(){ 
global $header_color;
global $even_color;
global $header_text_color;
global $form_color;
?>
	<style type="text/css">
        
		.pubtable-header{
    	background-color:<?php echo $header_color;  ?>;
        }
        
        .publication-table tr.pubrow:nth-child(2n+0){
        background-color: <?php echo $even_color ?>;
        }
        
        .pubtable-header > a:link,.pubtable-header > a:visited, .pubtable-header > a:hover{
   		color:<?php echo $header_text_color ?>;
        }
        .search-group{
            background-color:<?php echo $form_color;  ?>
        }
        
	</style>
<?php
	}

add_action('wp_head','pt_customise_css');

//Creates the publication post type

function pt_create_post_type() {
  register_post_type( 'publications', 
    array(
          'labels' => array(
        'name' => __( 'Publications' ),
        'singular_name' => __( 'Publication' )
      ),
      'public' => true,
      'has_archive' => true,
      'capability_type' => 'post',
      'show_in_rest'       => true,
      'taxonomies' => array ('category','tag')
    )
  );  

}

add_action( 'init', 'pt_create_post_type' );


// create two taxonomies, genres and writers for the post type "book"
function pt_create_publication_taxonomy() {
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name'              => _x( 'table_taxonomies', 'taxonomy general name', 'publication_table' ),
    'singular_name'     => _x( 'table_taxonomy', 'taxonomy singular name', 'publication_table' ),
    'search_items'      => __( 'Search table_taxonomies', 'publication_table' ),
    'all_items'         => __( 'All table_taxonomies', 'publication_table' ),
    'parent_item'       => __( 'Parent table_taxonomy', 'publication_table' ),
    'parent_item_colon' => __( 'Parent table_taxonomy:', 'publication_table' ),
    'edit_item'         => __( 'Edit table_taxonomy', 'publication_table' ),
    'update_item'       => __( 'Update table_taxonomy', 'publication_table' ),
    'add_new_item'      => __( 'Add New table_taxonomy', 'publication_table' ),
    'new_item_name'     => __( 'New table_taxonomy Name', 'publication_table' ),
    'menu_name'         => __( 'table_taxonomies', 'publication_table' ),
  );

  $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
   
  );

  register_taxonomy( 'table_taxonomies', array( 'publications' ), $args );

}

// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'pt_create_publication_taxonomy', 0 );



//Outputting the table rows

function pt_output_publications_as_table(){
  
$output = '';

global $post;
$cat_array = get_the_category();
$term_array = wp_get_object_terms($post->ID,'table_taxonomies');
    $termlist = '';
    $catlist = '';

  if($term_array){
      foreach ($term_array as $term) {
        $termlist .= '<a href="'. esc_url(get_term_link($term)).'">'.$term->name.'</a> / ';
      }
    }
  if($cat_array){
      foreach ($cat_array as $cat) {
        $catlist .= '<a href="'. esc_url(get_category_link($cat->cat_ID)).'">'.$cat->cat_name.'</a> / ';
      }
    }
      $combinedlist = $termlist.$catlist;
      $cleantermlist = trim($combinedlist,' /');

      $rawdate = get_field('publication_date');
  $output =  '<tr class="pubrow">';
    
        //Publication title cell
    
  $output .= '<td >';
  if (get_field('file_attachement')){

    $output .= "<a href=\"";
    $output .= get_field('file_attachement');
    $output .='">';
    $output .= get_field('pub_title');
    $output .='</a>';

  }else{
    $output .= get_field('pub_title');
  }
  $output .=  "</td>";
    
    //Authors cell
    
   $people = get_field('publication_authors');
    $author_list = '';
if($people){
    foreach ($people as $dude){
       $author_list .=  '<a href="'.$dude->guid.'">'.$dude->post_title.'</a>'.', ';
   }
    $clean_authors = trim($author_list,', ');
}
 $ext_authors = '';
if (get_field('external_authors')){
    $ext_authors = ', '.get_field('external_authors');
}
    
   $output .=  "<td>".$clean_authors.$ext_authors;

    // Publication date cell
    
  $output .=  "<td>";
  $output .= pt_format_the_date($rawdate,'publication_date',' M Y');
  $output .=  "</td>";
    
    //Published in cell
    
  $output .=  "<td>". get_field('published_in'). "</td>";
    
    //Category cell
    
  $output .=  "<td>";
  $output .=  $cleantermlist;
  $output .=  "</td>";
  $output .=  "</tr>";

  return $output;

}

// adding functionality to sort the table rows by clicking on the table titles

function pt_custom_query_sort_on_header_click(){

    $sort= ''; //default sort order
    $termsortby = "";
    $catsortby = "";
    $taxquery = "";
    if (isset($_GET['sort'])){        //retrieving get data for the sort effect
      $sort = urlencode($_GET['sort']);
    }
    if(isset($_POST['termsortby'])){
      //   $raw = '';
      // foreach ($_POST['termsortby'] as $key => $value) {
      //         $raw .= $value;
      // }
      //    $termsortby  = trim($raw, ',');
        $termsortby = $_POST['termsortby'];
        $taxquery = array(
                'taxonomy' => 'table_taxonomies',
                'field'    => 'term_id',
                'terms'    => $termsortby
          );
    }

    if(isset($_POST['catsortby'])){
      //   $raw = '';
      // foreach ($_POST['catsortby'] as $key => $value) {
      //   $raw .= $value.','; 
      // }
      // $catsortby = trim($raw, ',');
       $catsortby = $_POST['catsortby'];
        $taxquery = array(
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => $catsortby
                );
    }

    if( isset($_POST['catsortby']) && isset($_POST['termsortby']) ){
         $termsortby = $_POST['termsortby'];
         $catsortby = $_POST['catsortby'];
         $taxquery = array(
    'relation' => 'OR',
    array(
      'taxonomy' => 'category',
      'field'    => 'term_id',
      'terms'    => $catsortby
    ),
    array(
      'taxonomy' => 'table_taxonomies',
      'field'    => 'term_id',
      'terms'    =>  $termsortby,
    ),
  );


}


    if ($sort == 'publication_date'){   //when ordering by date, order from most recent to older 
      $order = 'ASC';
      }else{
      $order = 'DESC';
    }

    //setting up a new query,this is to avoid interference with the previous query
    $args = array( 
      'post_type' => 'publications',
      'meta_key'  => $sort,       // using the _GET value to sort by clicking on table headers
      'orderby' => 'meta_value',
      'order'   => $order,
      'tax_query' => array($taxquery),           
            
        );

return $args;
      
    }

     

      


function pt_format_the_date($rawdate,$acf_name,$format){
  $rawdate = get_field($acf_name,false,false);
    $dateo = new DateTime($rawdate);
    $output = $dateo->format($format);
    return  $output;
}


//function to re-order an array by value of specific key

function pt_prep_array_for_multisort($array_to_sort,$keyvalue_to_sort_by){
 
  $neworder = array();              //1.set up a new array
  foreach ($array_to_sort as $key => $row){   //2. loop through the original array
  $neworder[$key] = $row[$keyvalue_to_sort_by]; //3. assign the same keys as the original array to the new array and give values of the multidimentional array we want to sort by as value of the new array                  //4. sort by key value using the php function array_multisort($neworder, SORT_ASC, $fields) after using the custom function in the document
  } 
  return $neworder;
}


$mycategory = 'Category';


function pt_get_table_publication(){

global $mycategory;
global $cell_spacing;
$output = '';
$args = array('post_type' => 'publications');//set the query for post type 'publications'
$loop1 = new WP_Query( $args );
if($loop1->have_posts()) : $loop1->the_post();

/**field objects are not necessarily ordered by the order_no parmater(Field Order in the admin menu). 
*Here we ensure that they are, so that they can be re-ordered from the admin panel by dragging and dropping
*/
    $fields = get_field_objects();
    $neworder = pt_prep_array_for_multisort($fields,'order_no');
    array_multisort($neworder, SORT_ASC, $fields);

/**
*$fields is now ordered by order_no (Field Order in the admin menu)
*/
    
    $output = '';
    $output .= '<table cellpadding="'.$cell_spacing.'"  class="publication-table">';
    $output .= '<tr class="pub-row-header">';

  if ($fields){ //testing that we have custom fields
    foreach( $fields as $field_name => $field ){

      if (($field['name'] == 'file_attachement') || ($field['name'] == '') || ($field['name'] == 'external_authors')) {  // we skip the file attachement custom field using continue
        continue;
      }
      
      $output .= '<th class="pubtable-header"><a href="';
//      $output .= get_permalink(30);
      $output .= "?sort=".urlencode($field['name']);
      $output .= "\">";
      $output .= $field['label'];
      $output .= "</a></th>";         
    }
}
    $output.= '<th class="pubtable-header"><a href="#">'.$mycategory.'</a></th>';

    $output .= '</tr>'; 
    $output .= '<tbody>';

endif;wp_reset_postdata();
    
$loop = new WP_Query( pt_custom_query_sort_on_header_click() ); //custom function see function.php

    if ( $loop->have_posts() ): while ( $loop->have_posts() ): $loop->the_post();
            
    $output .= pt_output_publications_as_table();

    endwhile; 
    
    else :
           echo '<div class="message">Create publications from the Admin panel to display them here instead of this message</div>';
    endif; wp_reset_postdata(); 
            
    $output .= '</tbody>';
    $output .= '</table>';

    return $output;

}

function pt_get_the_table_form(){
 $output = '';
$args = array('post_type' => 'publications');
$loop1 = new WP_Query( $args );
$loop2 = new WP_Query( $args );
if ( $loop2->have_posts() && $loop1->have_posts() ): $loop1->the_post();
  $output = '';
  $output .= '<form  method="post">';
  $type = get_post_type_object(get_post_type());
  $output .= '<div class="search-group"><label>Quick Search '. $type->labels->name.':&nbsp;</label><br/>';
  $output .= '<input placeholder="filter the table by populating this field..."'; 
  $output .= 'class="search-field" type="Search" id="pubsearch">';
      $output .=  '<img src="';
    $output .= plugins_url('/assets/search.svg',__FILE__);
    $output .='"/> ';  
   $output .= '</div>';
    

  $output .= '<div class="search-group filter-section">';
          
  $label = '';
  $labels = get_field_objects();
    if ($labels){
      foreach($labels as $key => $val){
          if($val['order_no'] == 5){
            $label = $val['label'];
          }
        }
    }
  $output .= '<label>Filter by ';
  $output .=  $label; 
  $output .= ' :<br/> </label><br/> <div class="filters">';
           
    while($loop2->have_posts()): $loop2->the_post();

    global $post;
$cat_array = get_the_category();
$term_array = wp_get_object_terms($post->ID,'table_taxonomies');
    $termlist = '';
    $catlist = '';

  if($term_array){
      foreach ($term_array as $term) {
        $termlist .= '<div><input type="checkbox" name="termsortby[]" value="'.$term->term_id.'"> '.$term->name. '</div>';
      }
    }
  if($cat_array){
      foreach ($cat_array as $cat) {
        $catlist .= '<div><input type="checkbox" name="catsortby[]" value="'.$cat->cat_ID.'"> '.$cat->cat_name.'</div>';
      }
    }
      $combinedlist = $termlist.$catlist;
  
    $output .= $combinedlist;
        endwhile;
    $output .= '</div>';
    $output .= '<br/>
              <button type="submit" name="submit" class="submit-btn">Apply</button>
          </div>
        </form>';
 endif;

          return $output;

}

function the_publication_table(){

  /**
 * Detect plugin.
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// check for plugin using plugin name
if ( is_plugin_active('advanced-custom-fields/acf.php')) {

  echo pt_get_table_publication();

} else{

      echo '<div class="message">Publication table requires the Advanced Custom Fields plugin. Please install Advanced Custom Fields to output your publication table</div>';
    }

}

function the_table_form(){
    /**
 * Detect plugin.
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// check for plugin using plugin name
if ( is_plugin_active('advanced-custom-fields/acf.php')) {

  echo pt_get_the_table_form();

} else{

      echo '<div class="message">the publication form requires the Advanced Custom Fields plugin. Please install Advanced Custom Fields to output your publication table</div>';
    }

}

function publication_form_shortcodes_init()
{
    function publication_form_shortcode($atts = [], $content = null)
    {
        $content = the_table_form();
 
        // always return
        return $content;
    }
    add_shortcode('publication-form', 'publication_form_shortcode');
}
add_action('init', 'publication_form_shortcodes_init');





function publication_table_shortcodes_init()
{
    function publication_table_shortcode($atts = [], $content = null)
    {
        $content = the_publication_table();
 
        // always return
        return $content;
    }
    add_shortcode('publication-table', 'publication_table_shortcode');
}
add_action('init', 'publication_table_shortcodes_init');



if(function_exists("register_field_group"))
{
  register_field_group(array (
    'id' => 'acf_publications',
    'title' => 'publications',
    'fields' => array (
      array (
        'key' => 'field_58526be4a8fa4',
        'label' => 'File attachement',
        'name' => 'file_attachement',
        'type' => 'file',
        'instructions' => 'Upload publication pdf here',
        'save_format' => 'url',
        'library' => 'uploadedTo',
      ),
      array (
        'key' => 'field_58525ea616c53',
        'label' => 'Publication Title',
        'name' => 'pub_title',
        'type' => 'text',
        'order_no' => 1,
        'instructions' => 'Please enter the title of the publication ',
        'required' => 1,
        'default_value' => '',
        'placeholder' => '',
        'prepend' => '',
        'append' => '',
        'formatting' => 'none',
        'maxlength' => 200,
      ),
      array (
        'key' => 'field_58525faf16c54',
'label' => 'Publication Authors',
				'name' => 'publication_authors',
				'type' => 'post_object',
                'order_no' => 2,
				'instructions' => 'Select the authors of the paper by holding the "Ctrl" key and clicking on the author names. These are the authors belonging to the project. To add authors external to the project enter their name in the External Author field.',
				'required' => 1,
				'post_type' => array (
					0 => 'people',
				),
				'taxonomy' => array (
					0 => 'all',
				),
				'allow_null' => 1,
				'multiple' => 1,
      ),
        array (
				'key' => 'field_58c4232cac6d2',
				'label' => 'External Authors',
				'name' => 'external_authors',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => 30,
			),
      array (
        'key' => 'field_585277e6752c5',
        'label' => 'Publication date',
        'name' => 'publication_date',
        'order_no' => 3,
        'type' => 'date_picker',
        'instructions' => 'Please enter the date of publication. Thie is the month followed by the year',
        'date_format' => 'yymmdd',
        'display_format' => 'yymmdd',
        'first_day' => 1,
      ),
      array (
        'key' => 'field_5852605d16c57',
        'label' => 'Published in',
        'name' => 'published_in',
        'order_no' => 4,
        'type' => 'text',
        'instructions' => 'Please enter the conference or journal where thispublication was presented/published',
        'default_value' => '',
        'placeholder' => '',
        'prepend' => '',
        'append' => '',
        'formatting' => 'none',
        'maxlength' => 100,
      ),

    ),
    'location' => array (
      array (
        array (
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'publications',
          'order_no' => 0,
          'group_no' => 0,
        ),
        array (
          'param' => 'user_type',
          'operator' => '==',
          'value' => 'administrator',
          'order_no' => 1,
          'group_no' => 0,
        ),
      ),
    ),
    'options' => array (
      'position' => 'normal',
      'layout' => 'no_box',
      'hide_on_screen' => array (
        0 => 'permalink',
        1 => 'the_content',
        2 => 'excerpt',
        3 => 'custom_fields',
        4 => 'discussion',
        5 => 'comments',
        6 => 'revisions',
        7 => 'slug',
        8 => 'author',
        9 => 'format',
        10 => 'featured_image',
        11 => 'tags',
        12 => 'send-trackbacks',
      ),

    ),
    'menu_order' => 0,
  ));
}
