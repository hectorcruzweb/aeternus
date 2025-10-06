<template>
    <div class="centerx">
        <vs-prompt type="confirm" title="¿Desea continuar?" :class="['confirm-form confirmarDanger', z_index]"
            :active="show" buttons-hidden>
            <div class="text-center icono"></div>
            <div class="w-full text-center mt-3 h2 color-copy font-medium capitalize px-2">
                ¿Seguro de continuar?
            </div>

            <div class="w-full text-center mt-3 color-copy size-small px-2">
                {{ accion }}
            </div>

            <div class="w-full text-right px-2 mt-6 pb-3">
                <span @click="cancel" class="color-primary-900 my-2 mr-8 cursor-pointer">(Esc) Cancelar</span>

                <vs-button class="w-auto md:ml-2 my-2 md:mt-0" :color="confirmarColor" @click="aceptar">
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
            default: "z-index55k",
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
                    this.$popupManager.register(
                        this.$options.name,
                        this.cancel
                    );
                } else {
                    this.$popupManager.unregister(this.$options.name);
                }
            },
        }
    },
    computed: {
    },
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
        console.log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        console.log("Component mounted! " + this.$options.name); // DOM is ready
        //cerrando el confirmar con esc
        /*document.body.addEventListener("keyup", e => {
          if (e.keyCode === 13) {
            if (this.showChecker) {
              //CIERRO EL CONFIRMAR AL PRESONAR ESC
              this.aceptar();
            }
          }
        });*/
    },
    beforeDestroy() {
        this.$popupManager.unregister(this.$options.name);
    },
    destroyed() {
        console.log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
