export default {
    install(Vue) {
        // Define a read-only property globally
        Object.defineProperty(Vue.prototype, "$isDev", {
            get() {
                return process.env.NODE_ENV === "development";
            },
        });
    },
};
