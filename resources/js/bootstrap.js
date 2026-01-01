/**
 * jQuery is now loaded via jquery-init.js as a separate entry point
 * This ensures it's available before any legacy scripts execute
 */

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
