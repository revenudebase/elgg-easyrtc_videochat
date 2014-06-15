<?php
/**
 * elgg-easyrtc_videochat plugin
 * easyRTC video chat
 *
 * @package elgg-easyrtc_videochat
 */

elgg_register_event_handler('init', 'system', 'elgg_easyrtc_videochat');


function elgg_easyrtc_videochat() {

	// Extend view
	elgg_extend_view('css/elgg', 'elgg_easyrtc_videochat/css');
	elgg_extend_view('js/elgg', 'elgg_easyrtc_videochat/js_chat');
	elgg_extend_view('page/default', 'elgg_easyrtc_videochat/html_chat');

	/**
	 * Register external javascript for require.js
	 */
	elgg_define_js('fuse', array(
		'src' => '/mod/elgg-easyrtc_videochat/vendors/Fuse/src/fuse.min'
	));

}
