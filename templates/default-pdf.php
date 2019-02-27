<?php

 if ( ! defined( 'ABSPATH' ) ) {
 	exit;
 }

 $formid = $_REQUEST['formid'];
 $entryid = $_REQUEST['entryid'];

 if(empty($formid) || empty($entryid))
 {
?>
  <h1>ERROR PARAMETER MISSING</h1>
<?php
}else{
?>
<?php

  $args = array(
			'id'             => $entryid,
			'entry'          => false,
			'fields'         => false,
			'plain_text'     => false,
			'user_info'      => false,
			'include_blank'  => false,
			'default_email'  => false,
			'form_id'        => $formid,
			'format'         => 'text',
			'array_key'      => 'key',
			'direction'      => 'ltr',
			'font_size'      => '',
			'text_color'     => '',
			'border_width'   => '',
			'border_color'   => '',
			'bg_color'       => '',
			'alt_bg_color'   => '',
			'clickable'      => true,
			'exclude_fields' => '',
			'include_fields' => '',
			'include_extras' => '',
			'inline_style'   => 1,
			'child_array'    => false, // return embedded fields as nested array
		);
  // $fields = FrmField::get_all_for_form($formid);
  // echo '<pre>';
  // print_r($fields);
  // echo '</pre>';

  if ( $args['format'] == 'array' ) {
    $entry = FrmProEntriesController::show_entry_shortcode( $args );
    echo '<pre>';
    print_r($entry);
    echo '</pre>';
  }else{
    echo FrmProEntriesController::show_entry_shortcode( $args );
  }

?>
<?php } ?>
