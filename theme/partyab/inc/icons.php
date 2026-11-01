<?php
/**
 * Icon paths (SVG) — shared with the static prototype.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * Raw SVG inner markup per icon.
 *
 * @return array
 */
function partyab_icon_paths() {
	return array(
		'search' => '<circle cx="11" cy="11" r="7"/><path d="m20.2 20.2-3.8-3.8"/>',
		'user' => '<circle cx="12" cy="8" r="3.6"/><path d="M4.8 20.4c0-3.6 3.2-5.9 7.2-5.9s7.2 2.3 7.2 5.9"/>',
		'cart' => '<path d="M3 4h2.1l2.3 10.6a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 1.95-1.55L20.8 8H6"/><circle cx="9.5" cy="20" r="1.3"/><circle cx="17.5" cy="20" r="1.3"/>',
		'phone' => '<path d="M6.2 3h2.9l1.4 3.7-2 1.4a12.4 12.4 0 0 0 6.4 6.4l1.4-2L20 13.9v2.9a2 2 0 0 1-2.2 2A17 17 0 0 1 4.2 5.2 2 2 0 0 1 6.2 3Z"/>',
		'clock' => '<circle cx="12" cy="12" r="8.4"/><path d="M12 7.6V12l3 1.8"/>',
		'truck' => '<path d="M2.8 7h9.4v9.4H2.8z"/><path d="M12.2 10.6h3.6l3.4 3.2v2.6h-7"/><circle cx="6.3" cy="18.4" r="1.5"/><circle cx="16.6" cy="18.4" r="1.5"/>',
		'shield' => '<path d="M12 3.2 18.6 6v5.2c0 4.1-2.8 7.2-6.6 9-3.8-1.8-6.6-4.9-6.6-9V6L12 3.2Z"/><path d="m9.2 12 2 2 3.6-3.8"/>',
		'refresh' => '<path d="M20 12a8 8 0 1 1-2.4-5.7"/><path d="M20 4.4v4.2h-4.2"/>',
		'headset' => '<path d="M4.4 13.2v-1a7.6 7.6 0 0 1 15.2 0v1"/><path d="M4.4 13.2h2.8v5.6H6A1.6 1.6 0 0 1 4.4 17.2Z"/><path d="M19.6 13.2h-2.8v5.6h1.2a1.6 1.6 0 0 0 1.6-1.6Z"/>',
		'check' => '<path d="m5 12.6 4.2 4.2L19 6.4"/>',
		'x' => '<path d="M6 6l12 12M18 6 6 18"/>',
		'chevron-down' => '<path d="m6 9.5 6 6 6-6"/>',
		'chevron-left' => '<path d="m14 6-6 6 6 6"/>',
		'chevron-right' => '<path d="M10 6l6 6-6 6"/>',
		'arrow-left' => '<path d="M19 12H5"/><path d="m11 6-6 6 6 6"/>',
		'car' => '<path d="M4.4 16.4h15.2"/><path d="M4 16.4v-3.6l2-4.8h12l2 4.8v3.6"/><path d="M6.2 12.4h11.6"/><circle cx="7.6" cy="16.6" r="1.5"/><circle cx="16.4" cy="16.6" r="1.5"/>',
		'garage' => '<path d="M3.4 20.2V9.6L12 4.2l8.6 5.4v10.6"/><path d="M7.4 20.2v-6.4h9.2v6.4"/><path d="M9.4 16.8h5.2"/>',
		'filter' => '<path d="M3.6 5.4h16.8l-6.4 7.2v5.6l-4 2.2v-7.8L3.6 5.4Z"/>',
		'spark' => '<path d="M13 2.6 5.4 13.8h5.4l-.8 7.6 7.8-11.4h-5.4l.6-7.4Z"/>',
		'brake' => '<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="2.6"/><path d="M12 4.6v1.8M12 17.6v1.8M4.6 12h1.8M17.6 12h1.8M7.2 7.2l1.3 1.3M15.5 15.5l1.3 1.3M16.8 7.2l-1.3 1.3M8.5 15.5l-1.3 1.3"/>',
		'oil' => '<path d="M12 3s5.8 6.2 5.8 9.6A5.8 5.8 0 0 1 12 18.4a5.8 5.8 0 0 1-5.8-5.8C6.2 9.2 12 3 12 3Z"/><path d="M9.5 12.6a2.5 2.5 0 0 0 2.5 2.5"/>',
		'shock' => '<path d="M12 3.2v3"/><path d="M7.8 6.4h8.4M7.8 9.6h8.4M7.8 12.8h8.4M7.8 16h8.4"/><path d="M12 16v4.8"/>',
		'engine' => '<path d="M7.4 9.2V6.8h5.4v2.4"/><path d="M4.8 9.2h9.8v8H4.8z"/><path d="M14.6 11.6h2.6l2.2 2v3.6h-4.8"/><path d="M7.6 17.2v2M11.8 17.2v2M2.4 12.4h2.4"/>',
		'light' => '<path d="M3.6 8.4h3.6l8.4-4v15.2l-8.4-4H3.6z"/><path d="M18.4 9.2 21 7.4M18.4 14.8 21 16.6M17.6 12h3.6"/>',
		'tire' => '<circle cx="12" cy="12" r="8.4"/><circle cx="12" cy="12" r="3.2"/><path d="M12 3.6v5.2M12 15.2v5.2M3.6 12h5.2M15.2 12h5.2"/>',
		'accessory' => '<path d="M14.5 6.2a3.6 3.6 0 0 0 4.6 4.6l-8.2 8.2a2.6 2.6 0 0 1-3.7-3.7l8.2-8.2Z"/><path d="M9.6 8.6 6.4 5.4a2 2 0 0 0-2.8 2.8l3.2 3.2"/>',
		'sliders' => '<path d="M4 7h9M17.5 7H20M4 17h4M12 17h8"/><circle cx="15" cy="7" r="2.2"/><circle cx="9.5" cy="17" r="2.2"/>',
		'plus' => '<path d="M12 5.5v13M5.5 12h13"/>',
		'minus' => '<path d="M5.5 12h13"/>',
		'star' => '<path d="m12 2.6 2.9 5.9 6.5.9-4.7 4.6 1.1 6.4L12 17.4l-5.8 3 1.1-6.4L2.6 9.4l6.5-.9L12 2.6Z"/>',
		'info' => '<circle cx="12" cy="12" r="8.6"/><path d="M12 11v5.2M12 7.9v.2"/>',
		'alert' => '<path d="M12 3.6 21 19.4H3L12 3.6Z"/><path d="M12 9.6v4M12 16.6v.2"/>',
		'box' => '<path d="m12 3.2 8 4.2v9.2l-8 4.2-8-4.2V7.4l8-4.2Z"/><path d="m4 7.4 8 4.2 8-4.2M12 11.6v9.2"/>',
		'wrench' => '<path d="M20 5.6a4.6 4.6 0 0 1-6.1 6.1L7.3 18.3a2.3 2.3 0 1 1-3.2-3.2l6.6-6.6A4.6 4.6 0 0 1 17.4 2.4l-3 3 1.2 2.8 2.8 1.2 3-3Z"/>',
		'instagram' => '<rect x="3.4" y="3.4" width="17.2" height="17.2" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r=".9" fill="currentColor" stroke="none"/>',
		'telegram' => '<path d="m20.6 4.2-2.8 15.2-5.6-3.8-2.8 2.7.2-4.3 8.6-7.5-10 6.6-4.2-1.6 16.6-7.3Z"/>',
		'whatsapp' => '<path d="M4.2 20.4l1.2-4.2a7.8 7.8 0 1 1 3 3l-4.2 1.2Z"/><path d="M9 8.6c1 3 2.6 4.6 5.4 5.4"/>',
		'map-pin' => '<path d="M12 21s6.4-5.6 6.4-10.4A6.4 6.4 0 0 0 5.6 10.6C5.6 15.4 12 21 12 21Z"/><circle cx="12" cy="10.4" r="2.4"/>',
		'mail' => '<rect x="3" y="5.4" width="18" height="13.2" rx="2.4"/><path d="m3.8 7 8.2 6 8.2-6"/>',
		'file' => '<path d="M13.6 3.4H6.8a2 2 0 0 0-2 2v13.2a2 2 0 0 0 2 2h10.4a2 2 0 0 0 2-2V8.8l-5.6-5.4Z"/><path d="M13.4 3.6v5.4h5.4"/>',
		'grid' => '<rect x="4" y="4" width="6.4" height="6.4" rx="1.6"/><rect x="13.6" y="4" width="6.4" height="6.4" rx="1.6"/><rect x="4" y="13.6" width="6.4" height="6.4" rx="1.6"/><rect x="13.6" y="13.6" width="6.4" height="6.4" rx="1.6"/>',
		'zoom' => '<circle cx="11" cy="11" r="6.6"/><path d="m16 16 4.4 4.4M11 8.6v4.8M8.6 11h4.8"/>',
		'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'copy' => '<rect x="8.6" y="8.6" width="11.4" height="11.4" rx="2.4"/><path d="M15.4 5.4H6a2 2 0 0 0-2 2v9.4"/>',
		'send' => '<path d="m21 3-9.4 18-2.2-7-7-2.2L21 3Z"/><path d="m9.4 14 5.2-5.2"/>',
		'key' => '<circle cx="8.2" cy="15.8" r="3.4"/><path d="m10.8 13.2 8-8M16.4 5.2l2.4 2.4M14.4 7.2l2.4 2.4"/>',
		'check-shield' => '<path d="M12 3.2 18.6 6v5.2c0 4.1-2.8 7.2-6.6 9-3.8-1.8-6.6-4.9-6.6-9V6L12 3.2Z"/><path d="m9 12 2 2 4-4"/>',
		'layers' => '<path d="m12 3.4 8.4 4.4-8.4 4.4-8.4-4.4 8.4-4.4Z"/><path d="m3.6 12 8.4 4.4 8.4-4.4M3.6 16.4 12 20.8l8.4-4.4"/>',
	);
}
