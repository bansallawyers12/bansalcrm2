/**
 * FullCalendar v6 Initialization
 * 
 * This file loads FullCalendar v6 and exposes it globally for legacy scripts.
 */

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

// Expose FullCalendar globally
window.FullCalendar = {
    Calendar: Calendar,
    dayGridPlugin: dayGridPlugin,
    timeGridPlugin: timeGridPlugin,
    listPlugin: listPlugin,
    interactionPlugin: interactionPlugin
};

console.log('FullCalendar v6 loaded via Vite');

