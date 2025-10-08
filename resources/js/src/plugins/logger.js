// src/plugins/logger.js
export default {
    install(Vue) {
        const isDev = process.env.NODE_ENV === "development";

        Vue.prototype.$log = function (...args) {
            if (isDev) console.log(...args);
        };

        Vue.prototype.$warn = function (...args) {
            if (isDev) console.warn(...args);
        };

        Vue.prototype.$error = function (...args) {
            if (isDev) console.error(...args);
        };
    },
};
