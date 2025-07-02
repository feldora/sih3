import './bootstrap';
import $ from 'jquery';
import Alpine from 'alpinejs';
import "choices.js/src/styles/choices";
import "choices.js/public/assets/styles/choices.css";

import '@fortawesome/fontawesome-free/css/all.min.css';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import 'leaflet.awesome-markers';
import tinymce from 'tinymce';
import 'tinymce/models/dom';

import 'tinymce/themes/silver';
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/preview';
import 'tinymce/icons/default';
import 'tinymce/skins/ui/oxide/skin.min.css';

import 'tinymce/skins/ui/oxide/content.css';
import 'tinymce/skins/ui/oxide/content.min.css';

import '../css/pages/geospasial-map.css';
import './pages/geospasial-map.js';

import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css';

// Configure TinyMCE to use the correct skin and content CSS paths
window.tinymce = tinymce;
window.tinymceInitOptions = {
	skin_url: '/node_modules/tinymce/skins/ui/oxide',
	content_css: '/node_modules/tinymce/skins/content/default/content.min.css'
};

window.L = L;
window.$ = $;
window.jQuery = $;

window.Alpine = Alpine;
window.DataTable = DataTable;
Alpine.start();
