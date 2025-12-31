/**
 * jQuery is loaded from public/js in the HTML head before Vite for compatibility
 * with legacy scripts. We just need to ensure it's available in the module context.
 */
import jQuery from 'jquery';

// Use the global jQuery if it exists, otherwise use the imported one
window.$ = window.jQuery = window.jQuery || jQuery;

import _ from 'lodash';
import * as Popper from '@popperjs/core';
import * as bootstrap from 'bootstrap';

window._ = _;
window.Popper = Popper;
window.bootstrap = bootstrap;

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
