import Vue from "vue";

const state = Vue.observable({
    stack: [],
});

const PopupManager = {
    state,

    async register(popupInstance, closeFn, focusRef = null) {
        if (!popupInstance || !popupInstance.$options) {
            console.warn("PopupManager.register: invalid component instance");
            return;
        }

        const popupId = popupInstance.$options.name;

        await Vue.nextTick();

        // --- Find popup element
        let popupEl = null;
        if (
            popupInstance.$refs &&
            popupInstance.$refs[popupId] &&
            popupInstance.$refs[popupId].$el
        ) {
            popupEl = popupInstance.$refs[popupId].$el;
        } else {
            popupEl = popupInstance.$el;
        }

        if (!popupEl) {
            console.warn(`PopupManager: no popupEl found for ${popupId}`);
            return;
        }

        // --- Add to stack if new
        const exists = state.stack.find((p) => p.popupId === popupId);
        if (!exists) {
            state.stack.push({ popupId, closeFn, popupEl });
            this.updateFocusAndScroll();
        }

        // --- Automatically attach .vs-icon click handler to call closeFn
        Vue.nextTick(() => {
            const icon = popupEl.querySelector(".vs-icon");
            if (icon && typeof closeFn === "function") {
                // Save handler reference for cleanup
                const handler = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    try {
                        closeFn();
                    } catch (err) {
                        console.error(
                            `PopupManager: closeFn error in ${popupId}`,
                            err
                        );
                    }
                };
                icon.addEventListener("click", handler);

                // store handler for later cleanup
                const popup = state.stack.find((p) => p.popupId === popupId);
                if (popup) popup.iconHandler = handler;
            }
        });

        // --- Focus management (unchanged)
        Vue.nextTick(() => {
            setTimeout(() => {
                let el = null;

                if (focusRef && popupInstance.$refs[focusRef]) {
                    el =
                        popupInstance.$refs[focusRef].$el ||
                        popupInstance.$refs[focusRef];
                } else {
                    const content =
                        popupEl.querySelector(".vs-popup--content") || popupEl;
                    const focusables = content.querySelectorAll(
                        'a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])'
                    );
                    el = Array.from(focusables).find(
                        (f) => !f.disabled && f.offsetParent !== null
                    );
                }

                if (!el) return;
                const inputOrButton =
                    el.querySelector("input, textarea, select, button") || el;
                if (
                    inputOrButton &&
                    typeof inputOrButton.focus === "function"
                ) {
                    inputOrButton.focus();
                }
            }, 50);
        });
    },

    unregister(popupId) {
        Vue.nextTick(() => {
            setTimeout(() => {
                const index = state.stack.findIndex(
                    (p) => p.popupId === popupId
                );
                if (index !== -1) {
                    const popup = state.stack[index];

                    // Safe query for the icon without optional chaining
                    const icon =
                        popup && popup.popupEl
                            ? popup.popupEl.querySelector(".vs-icon")
                            : null;
                    if (icon && popup.iconHandler) {
                        icon.removeEventListener("click", popup.iconHandler);
                    }

                    state.stack.splice(index, 1);
                    this.updateFocusAndScroll();
                }
            }, 50);
        });
    },
    handleEsc(e) {
        if ((e.key === "Escape" || e.key === "Esc") && state.stack.length > 0) {
            const topPopup = state.stack[state.stack.length - 1];
            if (topPopup && typeof topPopup.closeFn === "function") {
                try {
                    topPopup.closeFn();
                } catch (err) {
                    console.error("Error closing popup:", err);
                }
            }
        }
    },

    updateFocusAndScroll() {
        // --- Manejo de Scroll ---
        if (state.stack.length > 0) {
            document.body.style.overflow = "hidden";
        } else {
            document.body.style.overflow = "";
        }

        // --- Manejo de Tab ---
        document.removeEventListener("keydown", this.handleTab, true);
        if (state.stack.length > 0) {
            document.addEventListener("keydown", this.handleTab, true);
        }
    },

    handleTab(e) {
        if (e.key !== "Tab") return;
        const top = state.stack[state.stack.length - 1];
        if (!top || !top.popupEl) return;

        const content =
            top.popupEl.querySelector(".vs-popup--content") || top.popupEl;
        const focusables = content.querySelectorAll(
            'a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])'
        );
        const visibleFocusables = Array.from(focusables).filter(
            (el) => !el.disabled && el.offsetParent !== null
        );

        if (visibleFocusables.length === 0) {
            e.preventDefault();
            return;
        }

        const first = visibleFocusables[0];
        const last = visibleFocusables[visibleFocusables.length - 1];

        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    },

    init() {
        if (!this._initialized) {
            window.addEventListener("keydown", this.handleEsc.bind(this));
            this._initialized = true;
        }
    },
};

PopupManager.init();

export default PopupManager;
