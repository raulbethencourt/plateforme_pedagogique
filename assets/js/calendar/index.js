import { Calendar } from "@fullcalendar/core";
import interactionPlugin from "@fullcalendar/interaction";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import bootstrapPlugin from '@fullcalendar/bootstrap';

// import "@fullcalendar/core/main.css";
import "@fullcalendar/daygrid/main.css";
import "@fullcalendar/timegrid/main.css";

import "./index.css"; // this will create a calendar.css file reachable to 'encore_entry_link_tags'


document.addEventListener("DOMContentLoaded", () => {
    var calendarEl = document.getElementById("calendar-holder");

    var eventsUrl = calendarEl.dataset.eventsUrl;

    var calendar = new Calendar(calendarEl, {
        defaultView: "dayGridMonth",
        editable: true,
        eventSources: [
            {
                url: eventsUrl,
                method: "POST",
                extraParams: {
                    filters: JSON.stringify({})
                },
                failure: () => {
                    alert("There was an error while fetching FullCalendar!");
                },
            },
        ],
        header: {
            left: "prev,next,today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay",
        },
        themeSystem: 'bootstrap',
        plugins: [interactionPlugin, dayGridPlugin, timeGridPlugin, bootstrapPlugin], // https://fullcalendar.io/docs/plugin-index
        timeZone: "UTC",
    });
    calendar.render();
});