import { Calendar } from '@fullcalendar/core';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import bootstrapPlugin from '@fullcalendar/bootstrap';

import moment from "moment";

/**
 * sends a request to the specified url from a form. this will change the window location.
 * @param {string} path the path to send the post request to
 * @param {object} params the paramiters to add to the url
 * @param {string} [method=post] the method to use on the form
 */
function post(path, params, method='post') {

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    const form = document.createElement('form');
    form.method = method;
    form.action = path;

    for (const key in params) {
        if (params.hasOwnProperty(key)) {
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = key;
            hiddenField.value = params[key];

            form.appendChild(hiddenField);
        }
    }

    document.body.appendChild(form);
    form.submit();
}

// window.location.replace('http://www.example.com')
btnPrenota = document.getElementById('btnPrenota');
function prenota(ora){
    //window.location.replace(btnPrenota.getAttribute('href').concat('?ora=').concat(ora))
    post(btnPrenota.getAttribute('href'), {ora: ora});
}

// window.location.replace('http://www.example.com')
btnElimina = document.getElementById('btnElimina');
let ids = document.querySelector('.prenotazioniUser').dataset.idsPrenotazioniGiocatore;
let prenotatoId = document.querySelector('.prenotazioniOggiUser').dataset.prenotazioniOggi;

console.log("ids prenotazioni USER", ids);
console.log("id prenotazione di oggi", prenotatoId);
console.log(Array.from(prenotatoId).length);
console.log(JSON.parse(prenotatoId).length);

export function giaPrenotato() {
    let prenotato = false;
    if (JSON.parse(prenotatoId).length > 0){
        prenotato = true;
    }
    return prenotato;
}

function checkId(id) {
    let trovato = false;
    console.log('Controllo se ho ID: ' + id);
    JSON.parse(ids).forEach(function (el) {
        console.log(el.id.toString(), id);
        if (el.id.toString() === id){
            trovato = true;
        }
    });
    console.log('fine controllo: ' + id);
    return trovato;
}

function elimina(id){
    //window.location.replace(btnElimina.getAttribute('href').concat('?id=').concat(id))
    post(btnElimina.getAttribute('href'), {id: id});
}

function getColonne() {
    let width = window.innerWidth
        || document.documentElement.clientWidth
        || document.body.clientWidth;

    let colonne = 7;

    if (width<960){
        colonne = 5
    }

    if (width<370){
        colonne = 3
    }

    return colonne;
}

document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('calendar');

    let urlJson = document.getElementById('urlPrenotazioniJson');
    let urlJsonUser = document.getElementById('urlPrenotazioniJsonUser');

    let calendar = new Calendar(calendarEl, {
        locale: 'it',
        timeZone: 'Europe/Rome',
        // aspectRatio: 1,
        contentHeight: 'auto',
        plugins: [ timeGridPlugin, interactionPlugin, bootstrapPlugin ],
        themeSystem: 'bootstrap',

        defaultView: 'timeGrid',
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
        duration: {days: getColonne()},
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
        },

    });

    calendar.render();
    calendar.updateSize();
});


require('../css/tabellone.css');