<template>
    <div class="centerx">
        <vs-popup :class="['forms-popup popup-50', z_index]" title="Cancelar Cotizaciones" :active.sync="showVentana"
            ref="cancelar_cotizacion">
            <div class="flex flex-wrap">
                <div class="w-full alerta py-2 px-2">
                    <div class="danger">
                        <h3>¿Está seguro de querer cancelar esta cotización?</h3>
                        <p>
                            Una vez realizado el proceso de cancelación no habrá manera de
                            volver a habilitar la cotización. Es recomendable llevar a cabo este
                            proceso una vez esté seguro de que es necesario.
                        </p>
                    </div>
                </div>

                <div class="w-full py-2 px-2">
                    <div class="w-full text-center bg-data-table uppercase py-2">
                        Datos de la cotización
                    </div>
                </div>
                <div class="w-full flex flex-wrap color-copy py-2 px-2">
                    <div class="w-full xl:w-3/12 px-2 text-center">
                        <span class="font-medium color-black-700"> Núm. Cotización</span>
                        <div class="w-full uppercase">
                            {{ this.datos.id }}
                        </div>
                    </div>
                    <div class="w-full xl:w-3/12 px-2 text-center">
                        <span class="font-medium color-black-700">
                            Fecha de la cotización</span>
                        <div class="w-full uppercase">
                            {{ this.datos.fecha_texto }}
                        </div>
                    </div>
                    <div class="w-full xl:w-6/12 px-2 text-center">
                        <span class="font-medium color-black-700">
                            Nombre del Cliente</span>
                        <div class="w-full uppercase">
                            {{ this.datos.cliente_nombre }}
                        </div>
                    </div>
                </div>
                <div class="w-full px-2 py-2">
                    <NotasComponent :value="form.nota" @input="(val) => { this.form.nota = val; }" />
                </div>
                <div class="w-full py-4 px-2">
                    <vs-button class="w-full" color="danger" @click="cancelar">
                        <span>Cancelar Cotización</span>
                    </vs-button>
                </div>
                <Password :z_index="'z-index56k'" :show="openPassword" :callback-on-success="callback"
                    @closeVerificar="closePassword" :accion="'Modificar Cotización'"></Password>
            </div>
        </vs-popup>
    </div>
</template>
<script>
import NotasComponent from "@pages/NotasComponent";
import Password from "@pages/confirmar_password";
import cotizaciones from "@services/cotizaciones";
export default {
    components: {
        NotasComponent,
        Password
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index55k",
        },
        datos: {
            type: Object,
            required: true,
        }
    },
    watch: {
        show: function (newValue, oldValue) {
            if (newValue == true) {
            } else {
                //reinicias el form
            }
        },
    },
    computed: {
        showVentana: {
            get() {
                return this.show;
            },
            set(newValue) {
                return newValue;
            },
        }
    },
    data() {
        return {
            errores: [],
            form: {
                id: '',
                nota: ''
            },
            openPassword: false,
            callback: Function
        };
    },
    methods: {
        cancelar() {
            this.$validator
                .validateAll()
                .then((result) => {
                    if (!result) {
                        this.$vs.notify({
                            title: "Error",
                            text: "Verifique que todos los datos han sido capturados",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            position: "bottom-right",
                            time: "12000",
                        });
                        return;
                    } else {
                        this.form.id = this.datos.id;
                        //AL LLEGAR AQUI SE SABE QUE EL FORMULARIO PASO LA VALIDACION
                        (async () => {
                            this.callback = await this.cancelar_cotizacion;
                            this.openPassword = true;
                        })();
                    }
                })
                .catch(() => { });
        },
        async cancelar_cotizacion() {
            //aqui mando guardar los datos
            this.errores = [];
            this.$vs.loading();
            try {
                let res = await cotizaciones.cancelar_cotizacion(
                    this.form
                );
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Cotizaciones",
                        text: "Se ha cancelado la cotización correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                    this.$emit("ConsultarCotizacion", { id: res.data, cliente_email: '', cliente_nombre: '' });
                } else {
                    this.$vs.notify({
                        title: "Cotizaciones",
                        text: "Error al cancelar la cotización, por favor reintente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 6000,
                    });
                }
                this.$vs.loading.close();
            } catch (err) {
                if (err.response) {
                    if (err.response.status == 403) {
                        /**FORBIDDEN ERROR */
                        this.$vs.notify({
                            title: "Permiso denegado",
                            text: "Verifique sus permisos con el administrador del sistema.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 4000,
                        });
                    } else if (err.response.status == 422) {
                        //checo si existe cada error
                        this.errores = err.response.data.error;
                        this.$vs.notify({
                            title: "Cotizaciones",
                            text: "Verifique los errores encontrados en los datos.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 5000,
                        });
                    } else if (err.response.status == 409) {
                        //este error es por alguna condicion que el contrano no cumple para modificar
                        //la propiedad esa ya ha sido vendida
                        this.$vs.notify({
                            title: "Cotizaciones",
                            text: err.response.data.error,
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 8000,
                        });
                    }
                }
                this.$vs.loading.close();
            }
        },
        closePassword() {
            this.openPassword = false;
        },
        cerrar_ventana() {
            this.$emit("closeCancelarCotizacion");
            return;
        },
    },
    created() { },
    mounted() {
        this.$refs["cancelar_cotizacion"].$el.querySelector(".vs-icon").onclick =
            () => {
                this.cerrar_ventana();
            };
        //cerrando el confirmar con esc
        document.body.addEventListener("keyup", (e) => {
            if (e.keyCode === 27) {
                if (this.showVentana) {
                    //CIERRO EL CONFIRMAR AL PRESONAR ESC
                    this.cerrar_ventana();
                }
            }
        });
    },
};
</script>
