import './bootstrap';

import Alpine from 'alpinejs';

import "choices.js/public/assets/styles/choices.css";

import '@fortawesome/fontawesome-free/css/all.min.css';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

window.L = L;

window.Alpine = Alpine;

Alpine.start();
