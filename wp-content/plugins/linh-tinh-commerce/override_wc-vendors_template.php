<?php
function myplugin_plugin_path() {
 
  // gets the absolute path to this plugin directory
 
  return untrailingslashit( plugin_dir_path( __FILE__ ) );
 
}

function sinh_override_wc_vendors_template() {
	$plugin_path = myplugin_plugin_path() . '/templates/wc-vendors';
	// Look within passed path within the theme - this is priority
 
  $template = locate_template(
 
    array(
 
      $template_path . $template_name,
 
      $template_name
 
    )
 
  );
 
 
 
  // Modification: Get the template from this plugin, if it exists
 
  if ( ! $template && file_exists( $plugin_path . $template_name ) )
 
    $template = $plugin_path . $template_name;
 
 
 
  // Use default template
 
  if ( ! $template )
 
    $template = $_template;
 
 
 
  // Return what we found
 
  return $template;
 
}
add_action( 'template_include', 'sinh_override_wc_vendors_template' );