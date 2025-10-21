export default {
    install(Vue) {
        // Add global property for development check
        Object.defineProperty(Vue.prototype, "$isDev", {
            get() {
                return process.env.NODE_ENV === "development";
            },
        });

        // Optional: also expose as a global variable for non-component use
        window.$isDev = process.env.NODE_ENV === "development";
    },
};
