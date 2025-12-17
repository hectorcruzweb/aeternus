import { Spanish } from "flatpickr/dist/l10n/es.js";
import { range } from "lodash";
export const configdateTimePickerWithTime = {
    altInput: true,
    altFormat: "j M Y h:i K", // 👈 "K" adds AM/PM
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    locale: Spanish,
    shorthandCurrentMonth: false,
    time_24hr: false,
    maxDate: new Date(new Date().setDate(new Date().getDate() + 25)),
};
export const configdateTimePicker = {
    altInput: true,
    altFormat: "j M Y",
    enableTime: false,
    dateFormat: "Y-m-d",
    locale: Spanish,
    shorthandCurrentMonth: false,
    maxDate: new Date(new Date().setDate(new Date().getDate() + 25)),
};

export const configdateTimePickerFullMonth = {
    altInput: true,
    altFormat: "j F Y",
    enableTime: false,
    dateFormat: "Y-m-d",
    locale: Spanish,
    shorthandCurrentMonth: false,
    maxDate: new Date(new Date().setDate(new Date().getDate() + 25)),
};

export const configdateTimePickerRange = {
    altInput: true,
    altFormat: "j M Y",
    mode: "range",
    dateFormat: "Y-m-d",
    locale: Spanish,
    shorthandCurrentMonth: false,
    maxDate: new Date(new Date().setDate(new Date().getDate())),
};

export const configdateTimePickerFechasCaducidad = {
    altInput: true,
    altFormat: "j M Y",
    enableTime: false,
    dateFormat: "Y-m-d",
    locale: Spanish,
    shorthandCurrentMonth: false,
    maxDate: new Date(new Date().setDate(new Date().getDate() + 2000)),
};
/**SELECT OPTIONS */
export const mostrarOptions = [
    {
        label: "15",
        value: "15",
    },
    {
        label: "30",
        value: "30",
    },
    {
        label: "45",
        value: "45",
    },
    {
        label: "60",
        value: "60",
    },
    {
        label: "80",
        value: "80",
    },
    {
        label: "100",
        value: "100",
    },
    {
        label: "200",
        value: "200",
    },
];

export const estadosOptions = [
    {
        label: "Todos",
        value: "",
    },
    {
        label: "Activo",
        value: "1",
    },
    {
        label: "Sin Accceso",
        value: "0",
    },
];

export const generosOptions = [
    {
        label: "Hombre",
        value: "1",
    },
    {
        label: "Mujer",
        value: "2",
    },
];

export const areasOptions = [
    {
        label: "Funeraria",
        value: "1",
    },
    {
        label: "Cementerio",
        value: "2",
    },
];

export const areasOptionsTodas = [
    {
        label: "Ambas",
        value: "",
    },
    {
        label: "Funeraria",
        value: "1",
    },
    {
        label: "Cementerio",
        value: "2",
    },
];

//variables para poder crear las filas de las terrazas por nombre de letra
export const alfabeto = [
    "A",
    "B",
    "C",
    "D",
    "E",
    "F",
    "G",
    "H",
    "I",
    "J",
    "K",
    "L",
    "M",
    "N",
    "Ñ",
    "O",
    "P",
    "Q",
    "R",
    "S",
    "T",
    "U",
    "V",
    "X",
    "Y",
    "Z",
    "AA",
    "AB",
    "AC",
    "AD",
    "AE",
    "AF",
    "AG",
    "AH",
    "AI",
    "AJ",
    "AK",
    "AL",
    "AM",
    "AN",
    "AÑ",
    "AO",
    "AP",
    "AQ",
    "AR",
    "AS",
    "AT",
    "AU",
    "AV",
    "AX",
    "AY",
    "AZ",
];

export const PermisosModulo = localStorage.getItem("AccessPermissions")
    ? JSON.parse(localStorage.getItem("AccessPermissions"))
    : null;

import Vue from "vue";

// Global date helper (from before)
Vue.prototype.$fechaHora = () => {
    const months = [
        "Ene",
        "Feb",
        "Mar",
        "Abr",
        "May",
        "Jun",
        "Jul",
        "Ago",
        "Sep",
        "Oct",
        "Nov",
        "Dic",
    ];
    const now = new Date();
    const day = now.getDate();
    const month = months[now.getMonth()];
    const year = now.getFullYear();
    let hours = now.getHours();
    const minutes = now.getMinutes().toString().padStart(2, "0");
    const ampm = hours >= 12 ? "PM" : "AM";
    hours = hours % 12 || 12;

    return `${month} ${day} ${year} ${hours}-${minutes} ${ampm}`;
};

Vue.prototype.$hora_completa = () => {
    const now = new Date();
    let hours = now.getHours();
    const minutes = now.getMinutes().toString().padStart(2, "0");
    const seconds = now.getSeconds().toString().padStart(2, "0");
    const ampm = hours >= 12 ? "PM" : "AM";
    hours = hours % 12 || 12;

    return `${hours}-${minutes}-${seconds} ${ampm}`;
};

// Global date helper (from before)
Vue.prototype.$meses_array = [
    {
        value: "1",
        label: "Enero",
    },
    {
        value: "2",
        label: "Febrero",
    },
    {
        value: "3",
        label: "Marzo",
    },
    {
        value: "4",
        label: "Abril",
    },
    {
        value: "5",
        label: "Mayo",
    },
    {
        value: "6",
        label: "Junio",
    },
    {
        value: "7",
        label: "Julio",
    },
    {
        value: "8",
        label: "Agosto",
    },
    {
        value: "9",
        label: "Septiembre",
    },
    {
        value: "10",
        label: "Octubre",
    },
    {
        value: "11",
        label: "noviembre",
    },
    {
        value: "12",
        label: "Diciembre",
    },
];
