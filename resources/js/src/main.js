/*=========================================================================================
  File Name: main.js
  Description: main vue(js) file
  ----------------------------------------------------------------------------------------
  Item Name: Vuesax Admin - VueJS Dashboard Admin Template
  Author: Pixinvent
  Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/

import Vue from "vue";
import EnvPlugin from "./plugins/env";
Vue.use(EnvPlugin);
import App from "./App.vue";
import Logger from "./plugins/logger.js";

Vue.use(Logger);
import popupManager from "./plugins/popupManager.js";
Vue.use(popupManager);

// Vuesax Component Framework
import Vuesax from "vuesax";

Vue.use(Vuesax);

// axios
import axios from "./axios.js";
Vue.prototype.$http = axios;

// API Calls
import "./http/requests";

// Theme Configurations
import "../themeConfig.js";

// ACL
import acl from "./acl/acl";

// Globally Registered Components
import "./globalComponents.js";

// Vue Router
import router from "./router";

// Vuex Store
import store from "./store/store";

// Clipboard
import VueClipboard from "vue-clipboard2";
Vue.use(VueClipboard);

// VeeValidate
import VeeValidate, { Validator } from "vee-validate";
import es from "vee-validate/dist/locale/es";
Vue.use(VeeValidate);
Validator.localize("es", es);
// Register custom validator globally
VeeValidate.Validator.extend("required-select", {
    getMessage: (field) => `El dato(${field}) es obligatorio.`,
    validate: (value) => !!value && value.value !== "",
});

Vue.config.productionTip = false;
Vue.config.devtools = false;
Vue.config.errorHandler = function (err, vm, info) {
    //console.log(`Error: ${err.toString()}\nInfo: ${info}`);
    if (info == "v-on handler") {
        /**para manejar este tipo de errores comunes solo actualizo el navegador */
        window.location.reload(true);
    }
};

Vue.config.warnHandler = function (msg, vm, trace) {
    /**que no muestre warnings */
    //console.log(`Warn: ${msg}\nTrace: ${trace}`);
};
/**importo el formnateador de numeros a moneda */
import numeral from "numeral";
import numFormat from "vue-filter-number-format";

Vue.filter("numFormat", numFormat(numeral));
Vue.use(require("vue-moment"));
const moment = require("moment");
require("moment/locale/es");

Vue.use(require("vue-moment"), {
    moment,
});

import VueSignaturePad from "vue-signature-pad";
Vue.use(VueSignaturePad);
/**con esta funcion valido si el usuario tiene cierto permiso sobre algun modulo, tomand
 * con parametros la url del modulo y el id del permiso
 */
import { modulo } from "@/ModuloPermisos";
Vue.prototype.$modulo = modulo;

import "./FuncionesGlobales";
import "./VariablesGlobales";

new Vue({
    router,
    store,
    acl,
    render: (h) => h(App),
}).$mount("#app");
