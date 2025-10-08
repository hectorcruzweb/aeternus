// src/plugins/logger.js

const isDev = process.env.NODE_ENV === "development";

// Vue plugin
export default {
    install(Vue) {
        Vue.prototype.$log = (...args) => {
            if (isDev) console.log(...args);
        };
        Vue.prototype.$warn = (...args) => {
            if (isDev) console.warn(...args);
        };
        Vue.prototype.$error = (...args) => {
            console.error(...args);
        }; // always active
    },
};

// Standalone exports for non-Vue code
export const log = (...args) => {
    if (isDev) console.log(...args);
};
export const warn = (...args) => {
    if (isDev) console.warn(...args);
};
export const error = (...args) => {
    console.error(...args);
};
