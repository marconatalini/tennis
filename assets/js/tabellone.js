import { Calendar } from '@fullcalendar/core';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import moment from "moment";

document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('calendar');

    let calendar = new Calendar(calendarEl, {
        locale: 'it',
        timeZone: 'UTC',
        contentHeight: 'auto',
        plugins: [ timeGridPlugin, interactionPlugin ],

        defaultView: 'timeGridWeek',
        header: {
            left: 'prev',
            center: 'title',
            right: 'next',
        },

        validRange: function(nowDate) {
            return {
                start: nowDate,
                end: moment().add(10,'days').toDate()
            };
        },

        allDaySlot: false,
        editable: false,
        slotDuration: '00:30:00',
        minTime: '07:00:00',
        maxTime: '24:00:00',
        events: 'http://tennis.locale/prenotazione/json',

        dateClick: function(info) {
            alert('Clicked on: ' + info.dateStr);
            prenota(info.dateStr);
            //alert('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
            //alert('Current view: ' + info.view.type);
            // change the day's background color just for fun
            //info.dayEl.style.backgroundColor = 'red';
        },

        eventClick: function(info) {
            alert('Event: ' + info.event.id);
            elimina(info.event.id);
            info.event.remove();

            // change the border color just for fun
            info.el.style.borderColor = 'red';
        }
    });

    calendar.render();
    calendar.updateSize();
});

require('../css/tabellone.css');