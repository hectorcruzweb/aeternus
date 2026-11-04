<template>
    <div class="centerx">
        <vs-popup
            :class="['confirm-form', 'password-confirm-compact', z_index]"
            close="cancelar"
            title="contraseña"
            :active="localShow"
            :ref="$options.name"
        >
            <!-- Presentación compacta: las acciones conservan los métodos y
                 referencias originales usados por PopupManager. -->
            <div class="password-confirm-compact__heading">
                <span class="password-confirm-compact__badge" aria-hidden="true">
                    <feather-icon icon="KeyIcon" />
                </span>
                <div class="password-confirm-compact__intro">
                    <h2>Confirma tu identidad</h2>
                    <p>Autoriza la operación con tu contraseña.</p>
                </div>
                <button type="button" class="password-confirm-compact__close"
                    aria-label="Cerrar" @click.prevent.stop="cancel">
                    <feather-icon icon="XIcon" aria-hidden="true" />
                </button>
            </div>
            <div class="password-confirm-compact__body">
                <label for="password-confirm-auth">Contraseña</label>
                <div class="password-confirm-compact__field">
                    <feather-icon icon="LockIcon" aria-hidden="true" />
                <vs-input
                    id="password-confirm-auth"
                    maxlength="50"
                    autocomplete="one-time-code"
                    size="large"
                    inputmode="none"
                    name="auth_verification"
                    ref="confirmAuth"
                    type="password"
                    form="none"
                    class="w-full"
                    placeholder="Escribe tu contraseña"
                    v-model.trim="pass"
                    @keyup.enter="acceptAlert"
                />
                </div>
            </div>
            <div class="password-confirm-compact__actions">
                <button type="button"
                    @click.prevent.stop="cancel"
                    class="password-confirm-compact__cancel">
                    Cancelar <kbd>Esc</kbd>
                </button>
                <button type="button"
                    class="password-confirm-compact__continue"
                    @click.prevent="acceptAlert"
                >
                    <feather-icon icon="CheckIcon" aria-hidden="true" />
                    Continuar
                </button>
            </div>
        </vs-popup>
    </div>
</template>
<script>
import usuarios from "@services/Usuarios";
export default {
    name: "confirmar_password",
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        callbackOnSuccess: {
            type: Function,
            required: true,
        },
        accion: {
            type: String,
            required: true,
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index60k",
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            handler(newVal) {
                // Only listen when visible = true
                if (newVal) {
                    this.$popupManager.register(
                        this,
                        this.cancel,
                        "confirmAuth"
                    );
                } else {
                    this.$popupManager.unregister(this.$options.name);
                }
                this.localShow = newVal;
            },
        },
    },
    data() {
        return {
            pass: "",
            localShow: false, // controls popup visibility
        };
    },
    computed: {
        validPassword() {
            return !!this.pass;
        },
    },
    methods: {
        async acceptAlert() {
            if (!this.validPassword) {
                this.pass = "";
                return;
            }
            //se verificq que exista una contraseña y se procede a realizar la confirmacion al servidor
            try {
                const res = await usuarios.confirmPassword(this.pass);
                if (res.status === 200) {
                    this.callbackOnSuccess();
                    this.cancel();
                }
            } catch (err) {
                this.$vs.notify({
                    title: "Permiso denegado",
                    text: "Contraseña incorrecta. Por favor reintente.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                    position: "top-center",
                    time: "40000",
                });
                this.pass = "";
                this.$nextTick(() =>
                    this.$refs["confirmAuth"].$el.querySelector("input").focus()
                );
            }
        },
        cancel(event) {
            if (event) event.stopPropagation();
            this.pass = "";
            this.$emit("closeVerificar");
        },
    },
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name); // DOM is ready
    },
    beforeDestroy() {
        this.$popupManager.unregister(this.$options.name);
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
<style>
/* Reglas limitadas a este popup: no alteran los demás confirmadores.
   El encabezado nativo sigue oculto por confirm-form; se dibuja uno propio. */
.password-confirm-compact .vs-popup {
    width: 480px !important;
    max-width: calc(100% - 24px) !important;
    max-height: calc(100% - 32px) !important;
    padding: 0 !important;
    border-radius: 4px !important;
}
.password-confirm-compact .vs-popup--content {
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
}
.password-confirm-compact__heading {
    display: flex; align-items: center; gap: 14px;
    padding: 24px 24px 18px; border-bottom: 1px solid #eceeed;
}
.password-confirm-compact__badge {
    display: flex; align-items: center; justify-content: center;
    width: 40px; height: 40px; flex-shrink: 0;
    border-radius: 50%; color: #44816d; background: #edf5f1;
}
.password-confirm-compact__badge svg { width: 21px; height: 21px; }
.password-confirm-compact__intro { flex: 1; min-width: 0; }
.password-confirm-compact__intro h2 {
    margin: 0; font-size: 19px; font-weight: 600; color: #373b40;
}
.password-confirm-compact__intro p {
    margin: 6px 0 0; font-size: 12px; line-height: 1.5; color: #72777d;
}
.password-confirm-compact button {
    font-family: inherit; cursor: pointer;
    transition: background-color .18s, transform .18s;
}
.password-confirm-compact button:active { transform: translateY(1px); }
.password-confirm-compact button:focus-visible {
    outline: 2px solid #44816d; outline-offset: 3px;
}
.password-confirm-compact__close {
    display: flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; flex-shrink: 0;
    border: 0; background: transparent; color: #b33e3e;
}
.password-confirm-compact__close svg { width: 21px; height: 21px; }
.password-confirm-compact__close:hover { background: #fff0f0; }
.password-confirm-compact__body { padding: 22px 24px 24px; }
.password-confirm-compact__body label { font-size: 12px; font-weight: 600; color: #5e6266; }
.password-confirm-compact__field { position: relative; margin-top: 9px; }
.password-confirm-compact__field > .feather-icon {
    position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
    z-index: 1; pointer-events: none; color: #929996;
}
.password-confirm-compact__field > .feather-icon svg { width: 18px; height: 18px; }
.password-confirm-compact__field .vs-input--input {
    padding-left: 42px !important; font-size: 14px !important;
    border-radius: 0 !important;
}
.password-confirm-compact__field .vs-input--placeholder { padding-left: 42px !important; }
.password-confirm-compact__field:focus-within .vs-input--input {
    border-color: #44816d !important; box-shadow: 0 0 0 3px rgba(68,129,109,.09);
}
.password-confirm-compact__actions {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; padding: 16px 24px; border-top: 1px solid #eceeed;
}
.password-confirm-compact__cancel, .password-confirm-compact__continue {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 9px; padding: 12px 17px; border: 0; font-size: 13px; font-weight: 600;
}
.password-confirm-compact__cancel { background: transparent; color: #b44b4b; }
.password-confirm-compact__cancel:hover { background: #fff0f0; }
.password-confirm-compact__cancel kbd {
    border: 1px solid #e5cccc; padding: 2px 4px;
    font-family: inherit; font-size: 10px; font-weight: 400;
}
.password-confirm-compact__continue { background: #44816d; color: #fff; }
.password-confirm-compact__continue:hover { background: #326352; }
.password-confirm-compact__continue svg { width: 16px; height: 16px; }
@media (max-width: 420px) {
    .password-confirm-compact__heading { gap: 10px; padding: 20px 16px 16px; }
    .password-confirm-compact__badge { width: 32px; height: 32px; }
    .password-confirm-compact__intro h2 { font-size: 17px; }
    .password-confirm-compact__body, .password-confirm-compact__actions { padding: 18px 16px; }
}
@media (prefers-reduced-motion: reduce) {
    .password-confirm-compact button { transition: none; }
}
</style>
