<template>
    <div class="centerx">
        <vs-prompt type="confirm" title="¿Desea continuar?" :class="['confirm-form confirmarDanger danger-confirm-compact', z_index]"
            :active="show" buttons-hidden :ref="$options.name">
            <!-- Encabezado compacto propio; conserva el prompt y PopupManager.
                 Mensaje, etiqueta y color de confirmación siguen siendo props. -->
            <div class="danger-confirm-compact__heading">
                <span class="danger-confirm-compact__badge" aria-hidden="true">
                    <feather-icon icon="AlertTriangleIcon" />
                </span>
                <h2>¿Seguro de continuar?</h2>
                <button type="button" class="danger-confirm-compact__close"
                    aria-label="Cerrar" @click="cancel">
                    <feather-icon icon="XIcon" aria-hidden="true" />
                </button>
            </div>

            <div class="danger-confirm-compact__body">
                {{ accion }}
            </div>

            <div class="danger-confirm-compact__actions">
                <button type="button" @click="cancel" class="danger-confirm-compact__cancel">
                    Cancelar <kbd>Esc</kbd>
                </button>

                <vs-button class="danger-confirm-compact__accept" ref="confirmar" :color="confirmarColor" @click="aceptar">
                    <feather-icon icon="CheckIcon" aria-hidden="true" />
                    <span>{{ confirmarButton }}</span>
                </vs-button>
            </div>
        </vs-prompt>
    </div>
</template>
<script>
export default {
    name: "ConfirmarDanger",
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
        confirmarButton: {
            type: String,
            default: "Aceptar",
        },
        confirmarColor: {
            type: String,
            default: "danger",
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index60k",
        },
    },

    data() {
        return {};
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newVal) {
                // Only listen when visible = true
                if (newVal) {
                    this.$popupManager.register(this, this.cancel);
                } else {
                    this.$popupManager.unregister(this.$options.name);
                }
            },
        },
    },
    computed: {},
    methods: {
        aceptar() {
            this.callbackOnSuccess();
            this.cancel();
        },
        cancel() {
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
/* Estilos exclusivos del confirmador de advertencia. El blanco es fijo,
   incluso con tema oscuro; no se cambia el color configurable del vs-button. */
.danger-confirm-compact .vs-dialog {
    width: 480px !important;
    max-width: calc(100% - 24px) !important;
    background: #fff !important;
    color: #50545a !important;
    border-radius: 4px !important;
}
.danger-confirm-compact .vs-dialog-text { padding: 0 !important; margin: 0 !important; }
.danger-confirm-compact__heading {
    display: flex; align-items: center; gap: 14px;
    padding: 24px 24px 18px; border-bottom: 1px solid #eceeed;
}
.danger-confirm-compact__badge {
    display: flex; align-items: center; justify-content: center;
    width: 40px; height: 40px; flex-shrink: 0;
    border-radius: 50%; background: #faeded; color: #b54e50;
}
.danger-confirm-compact__badge svg { width: 21px; height: 21px; }
.danger-confirm-compact__heading h2 {
    flex: 1; min-width: 0; margin: 0; font-size: 19px;
    font-weight: 600; line-height: 1.4; color: #373b40;
}
.danger-confirm-compact__close {
    display: flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; flex-shrink: 0;
    border: 0; background: transparent; color: #b33e3e;
}
.danger-confirm-compact__close svg { width: 21px; height: 21px; }
.danger-confirm-compact__body {
    padding: 22px 24px 28px; font-size: 14px; line-height: 1.6;
    color: #646a70; overflow-wrap: break-word;
}
.danger-confirm-compact__actions {
    display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
    gap: 12px; padding: 16px 24px; border-top: 1px solid #eceeed;
}
.danger-confirm-compact__cancel {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 9px; padding: 12px 17px; border: 0;
    background: transparent; color: #b44b4b; font-size: 13px; font-weight: 600;
}
.danger-confirm-compact__cancel kbd {
    border: 1px solid #e5cccc; padding: 2px 4px;
    font-family: inherit; font-size: 10px; font-weight: 400;
}
.danger-confirm-compact__close, .danger-confirm-compact__cancel {
    cursor: pointer; font-family: inherit;
    transition: background-color .18s, transform .18s;
}
.danger-confirm-compact__close:hover, .danger-confirm-compact__cancel:hover { background: #fff0f0; }
.danger-confirm-compact__close:active, .danger-confirm-compact__cancel:active { transform: translateY(1px); }
.danger-confirm-compact__close:focus-visible, .danger-confirm-compact__cancel:focus-visible {
    outline: 2px solid #b54e50; outline-offset: 3px;
}
.danger-confirm-compact__accept { margin: 0 !important; border-radius: 0 !important; }
.danger-confirm-compact__accept .vs-button-text {
    display: inline-flex; align-items: center; justify-content: center; gap: 9px;
}
.danger-confirm-compact__accept svg { width: 16px; height: 16px; }
@media (max-width: 420px) {
    .danger-confirm-compact__heading { padding: 20px 16px 16px; gap: 10px; }
    .danger-confirm-compact__heading h2 { font-size: 17px; }
    .danger-confirm-compact__body, .danger-confirm-compact__actions { padding: 18px 16px; }
}
@media (prefers-reduced-motion: reduce) {
    .danger-confirm-compact__close, .danger-confirm-compact__cancel { transition: none; }
}
</style>
