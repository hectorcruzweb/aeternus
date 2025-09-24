import PopupManager from "../utils/PopupManager";

export default {
    install(Vue) {
        Vue.prototype.$popupManager = PopupManager;
    },
};
