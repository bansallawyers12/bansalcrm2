/**
 * Auto-logout after a period of inactivity (default 30 minutes).
 * Resets on mouse, keyboard, touch, or scroll activity.
 * Uses localStorage for cross-tab synchronization.
 */
(function () {
	'use strict';

	var INACTIVITY_MINUTES = 30;
	var INACTIVITY_MS = INACTIVITY_MINUTES * 60 * 1000;
	var CHECK_INTERVAL_MS = 5000; // Check every 5 seconds
	var STORAGE_KEY = 'crm_last_activity_timestamp';
	
	var checkIntervalId = null;
	var isLoggingOut = false;

	/**
	 * Get the last activity timestamp from localStorage or current time
	 */
	function getLastActivity() {
		try {
			var stored = localStorage.getItem(STORAGE_KEY);
			return stored ? parseInt(stored, 10) : Date.now();
		} catch (e) {
			// localStorage might be disabled or throw errors
			return Date.now();
		}
	}

	/**
	 * Update the last activity timestamp in localStorage
	 */
	function updateLastActivity() {
		try {
			localStorage.setItem(STORAGE_KEY, Date.now().toString());
		} catch (e) {
			// Silently fail if localStorage is not available
		}
	}

	/**
	 * Perform logout by submitting the logout form
	 */
	function logout() {
		if (isLoggingOut) return; // Prevent multiple logout attempts
		isLoggingOut = true;

		var form = document.getElementById('logout-form');
		if (form && form.action) {
			// Clear the interval before logout
			if (checkIntervalId) {
				clearInterval(checkIntervalId);
				checkIntervalId = null;
			}
			form.submit();
		}
	}

	/**
	 * Check if session has expired based on last activity
	 */
	function checkInactivity() {
		if (isLoggingOut) return;

		var lastActivity = getLastActivity();
		var now = Date.now();
		var timeSinceLastActivity = now - lastActivity;

		if (timeSinceLastActivity >= INACTIVITY_MS) {
			logout();
		}
	}

	/**
	 * Handle user activity - update timestamp and reset across all tabs
	 */
	function handleActivity() {
		if (isLoggingOut) return;
		updateLastActivity();
	}

	/**
	 * Listen for storage events from other tabs
	 */
	function handleStorageEvent(event) {
		// When another tab updates the activity timestamp, we're automatically synced
		// No action needed here as checkInactivity will read the updated value
		if (event.key === STORAGE_KEY) {
			// Another tab had activity, we're still active
		}
	}

	/**
	 * Initialize the inactivity tracker
	 */
	function init() {
		var form = document.getElementById('logout-form');
		if (!form) return;

		// Set initial activity timestamp
		updateLastActivity();

		// Listen to user activity events
		var events = ['mousedown', 'mousemove', 'keydown', 'scroll', 'touchstart', 'click'];
		events.forEach(function (eventName) {
			document.addEventListener(eventName, handleActivity, { passive: true });
		});

		// Listen to storage events from other tabs
		window.addEventListener('storage', handleStorageEvent);

		// Start periodic inactivity check
		checkIntervalId = setInterval(checkInactivity, CHECK_INTERVAL_MS);

		// Also check immediately
		checkInactivity();
	}

	/**
	 * Cleanup on page unload (optional, for good practice)
	 */
	function cleanup() {
		if (checkIntervalId) {
			clearInterval(checkIntervalId);
			checkIntervalId = null;
		}
	}

	// Initialize when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	// Cleanup on page unload
	window.addEventListener('beforeunload', cleanup);
})();
