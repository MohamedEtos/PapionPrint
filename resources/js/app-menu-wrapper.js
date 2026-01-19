// This wrapper ensures jQuery is available for app-menu.js
import $ from 'jquery';
window.$ = window.jQuery = $;

// Now import app-menu.js which depends on jQuery
import '../core/js/core/app-menu.js';