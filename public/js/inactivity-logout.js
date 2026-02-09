/**
 * Auto-logout after a period of inactivity (default 15 minutes).
 * Resets on mouse, keyboard, touch, or scroll activity.
 */
(function () {
	'use strict';

	var INACTIVITY_MINUTES =30;
	var MS = INACTIVITY_MINUTES * 60 * 1000;
	var timer = null;

	function logout() {
		var form = document.getElementById('logout-form');
		if (form && form.action) {
			form.submit();
		}
	}

	function resetTimer() {
		if (timer) clearTimeout(timer);
		timer = setTimeout(logout, MS);
	}

	function init() {
		var form = document.getElementById('logout-form');
		if (!form) return;

		var events = ['mousedown', 'mousemove', 'keydown', 'scroll', 'touchstart', 'click'];
		events.forEach(function (eventName) {
			document.addEventListener(eventName, resetTimer);
		});

		resetTimer();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
