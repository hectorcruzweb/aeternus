// src/utils/PopupManager.js
import Vue from "vue";
const state = Vue.observable({
    stack: []
});
const PopupManager = {
    state,
    register(popupId, closeFn) {
        state.stack.push({ popupId, closeFn });
    },
    unregister(popupId) {
        state.stack = state.stack.filter(p => p.popupId !== popupId);
    },
    handleEsc(e) {
        if (e.key === "Escape" && state.stack.length > 0) {
            const topPopup = state.stack[state.stack.length - 1];
            if (topPopup && typeof topPopup.closeFn === "function") {
                topPopup.closeFn();
            }
        }
    },
    init() {
        if (!this._initialized) {
            window.addEventListener("keydown", this.handleEsc.bind(this));
            this._initialized = true;
        }
    }
};

PopupManager.init();

export default PopupManager;
