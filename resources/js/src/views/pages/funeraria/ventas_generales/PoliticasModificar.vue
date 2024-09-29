<template>
    <div class="centerx">
        <vs-popup :class="['confirm-form width-auto popup-40', z_index]" title="Modificar Ventas en General"
            :active.sync="showChecker" ref="politicas">
            <div class="flex flex-col justify-evenly text-center">
                <h5 class="text-primary uppercase title">Políticas de Modificación</h5>
                <p class="content-text">Para poder modificar una venta en general, debe de estar <span
                        class="text-primary">Vigente</span> y no haber sido
                    marcada como <span class="text-primary">Entregada</span>. <span v-if="!permisoModificar">Esta venta
                        <span class="text-danger" v-if="!permisoModificar">NO</span> cumple con estas
                        características.</span></p>
                <div class="items-center justify-center">
                    <vs-button class="sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="success" @click="aceptar()">
                        <span>Entendido</span>
                    </vs-button>
                </div>
            </div>
        </vs-popup>
    </div>
</template>
<script>

export default {
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        permiso_modificar: {
            type: Boolean,
            required: true,
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index100k",
        },
    },

    data() {
        return {};
    },
    watch: {
        show: function (newValue, oldValue) {
            if (newValue == true) {
                this.$refs["politicas"].$el.querySelector(".vs-icon").onclick = () => {
                    this.aceptar();
                };
            }
        },
    },
    computed: {
        showChecker: {
            get() {
                return this.show;
            },
            set(newValue) {
                return newValue;
            },
        },
        permisoModificar: {
            get() {
                return this.permiso_modificar;
            },
            set(newValue) {
                return newValue;
            },
        },
    },
    methods: {
        aceptar() {
            this.$emit("closeForm", this.permisoModificar);
        },
    },
    mounted() {
        //cerrando el confirmar con esc
        document.body.addEventListener("keyup", (e) => {
            if (e.keyCode === 27) {
                if (this.showChecker) {
                    //CIERRO EL CONFIRMAR AL PRESONAR ESC
                    this.aceptar();
                }
            }
        });

        document.body.addEventListener("keyup", (e) => {
            if (e.keyCode === 13) {
                if (this.showChecker) {
                    //CIERRO EL CONFIRMAR AL PRESONAR ESC
                    this.aceptar();
                }
            }
        });
    },
};
</script>
