import { Calendar } from '@fullcalendar/core';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import bootstrapPlugin from '@fullcalendar/bootstrap';

import moment from "moment";

document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('calendar');

    let urlJson = document.getElementById('urlPrenotazioniJson');
    let urlJsonUser = document.getElementById('urlPrenotazioniJsonUser');

    let calendar = new Calendar(calendarEl, {
        locale: 'it',
        timeZone: 'Europe/Rome',
        contentHeight: 'auto',
        plugins: [ timeGridPlugin, interactionPlugin, bootstrapPlugin ],
        themeSystem: 'bootstrap',

        defaultView: 'timeGridWeek',
        header: {
            left: 'prev',
            center: 'title', //title
            right: 'next',
        },

        validRange: function(nowDate) {
            return {
                start: moment().add(-7, 'days').toDate(),
                end: moment().add(10,'days').toDate()
            };
        },

        allDaySlot: false,
        editable: false,
        slotDuration: '00:30:00',
        minTime: '09:00:00',
        maxTime: '22:00:00',
        eventSources: [
            {
                // url: 'http://tennis.locale/prenotazione/json',
                url: urlJson.getAttribute('href'),
                method: 'POST',
            },
            {
                // url: 'http://tennis.locale/prenotazione/jsonUser',
                url: urlJsonUser.getAttribute('href'),
                method: 'POST',
                color: 'blue'
            },
        ],

        dateClick: function(info) {
            // alert('Clicked on: ' + moment(info.date).hour());
            if (moment(info.date).hour() == 15 || moment(info.date).hour() == 16){
                alert('Non prenotare in queste ore. Meglio far silenzio.')
            } else {
                if (moment(info.date) > moment()) { // verifico che il click non sia sul passato
                    if (!giaPrenotato()) {
                        prenota(info.dateStr);
                    } else {
                        alert('Oggi hai già fatto una prenotazione.');
                    }
                }
            }

            //alert('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
            //alert('Current view: ' + info.view.type);
            // change the day's background color just for fun
            //info.dayEl.style.backgroundColor = 'red';
        },

        eventClick: function(info) {
            //alert('Event: ' + info.event.id);
            if (checkId(info.event.id)){
                elimina(info.event.id);
                info.event.remove();
            } else {
                alert('Non puoi cancellare questa prenotazione.');
            }


            // change the border color just for fun
            info.el.style.borderColor = 'red';
        }
    });

    calendar.render();
    calendar.updateSize();
});

require('../css/tabellone.css');