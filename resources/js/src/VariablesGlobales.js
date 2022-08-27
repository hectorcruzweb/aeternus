import { Spanish } from "flatpickr/dist/l10n/es.js";
import { range } from "lodash";
export const configdateTimePickerWithTime = {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    locale: Spanish,
    shorthandCurrentMonth: false,
    time_24hr: false,
    maxDate: new Date(new Date().setDate(new Date().getDate() + 25))
};
export const configdateTimePicker = {
    enableTime: false,
    dateFormat: "Y-m-d",
    locale: Spanish,
    shorthandCurrentMonth: false,
    maxDate: new Date(new Date().setDate(new Date().getDate() + 25))
};

export const configdateTimePickerRange = {
    mode: "range",
    dateFormat: "Y-m-d",
    locale: Spanish,
    shorthandCurrentMonth: false,
    maxDate: new Date(new Date().setDate(new Date().getDate()))
};

export const configdateTimePickerFechasCaducidad = {
    enableTime: false,
    dateFormat: "Y-m-d",
    locale: Spanish,
    shorthandCurrentMonth: false,
    maxDate: new Date(new Date().setDate(new Date().getDate() + 2000))
};
/**SELECT OPTIONS */
export const mostrarOptions = [
    {
        label: "15",
        value: "15"
    },
    {
        label: "30",
        value: "30"
    },
    {
        label: "45",
        value: "45"
    },
    {
        label: "60",
        value: "60"
    },
    {
        label: "80",
        value: "80"
    },
    {
        label: "100",
        value: "100"
    },
    {
        label: "200",
        value: "200"
    }
];

export const estadosOptions = [
    {
        label: "Todos",
        value: ""
    },
    {
        label: "Activo",
        value: "1"
    },
    {
        label: "Sin Accceso",
        value: "0"
    }
];

export const generosOptions = [
    {
        label: "Hombre",
        value: "1"
    },
    {
        label: "Mujer",
        value: "2"
    }
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
    "AZ"
];

export const PermisosModulo = localStorage.getItem("AccessPermissions")
    ? JSON.parse(localStorage.getItem("AccessPermissions"))
    : null;
