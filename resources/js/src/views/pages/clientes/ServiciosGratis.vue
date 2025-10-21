<template>
    <div class="centerx">
        <vs-popup class="forms-popup popup-50" close="cancelar" :title="'CONTROL DE SERVICIOS GRATIS AL CLIENTE'"
            :active="localShow" :ref="this.$options.name">
            <div class="p-8">
                <div class="form-group">
                    <div class="title-form-group">
                        Datos del cliente
                    </div>
                    <div class="form-group-content">
                        <div class="flex flex-wrap">
                            <div class="w-full">
                                <div class="flex flex-wrap">
                                    <div class="w-full xl:w-12/12 px-2">
                                        <!--resumen-->
                                        <div class="flex flex-wrap">
                                            <div
                                                class="p-4 w-full mx-auto rounded-lg size-base border-gray-solid-1 rounded-lg">
                                                <div class="flex flex-wrap color-copy">
                                                    <div class="w-full">
                                                        <div class="flex flex-wrap">
                                                            <div
                                                                class="w-full xl:w-6/12 text-center font-medium color-black-900 uppercase">
                                                                Titular
                                                            </div>
                                                            <div
                                                                class="w-full xl:w-6/12 text-center font-medium color-copy">
                                                                <span>{{
                                                                    getDatosCliente.nombre
                                                                }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="w-full">
                                                        <div class="w-full xl:w-8/12 input-text px-2 mx-auto mt-5 ">
                                                            <label>Número de
                                                                servicios
                                                                GRATIS:</label>
                                                            <v-select :options="cantidades
                                                                " :clearable="false
                                                                    " v-model="form.servicios_gratis
                                                                        " :dir="$vs.rtl
                                                                            ? 'rtl'
                                                                            : 'ltr'
                                                                            " class="w-full" data-vv-as=" ">
                                                                <div slot="no-options">
                                                                    Seleccione 1
                                                                </div>
                                                            </v-select>
                                                            <span>{{
                                                                errors.first(
                                                                    "motivo"
                                                                )
                                                            }}</span>
                                                            <span v-if="
                                                                this
                                                                    .errores[
                                                                'motivo.value'
                                                                ]
                                                            ">{{
                                                                errores[
                                                                "motivo.value"
                                                                ][0]
                                                            }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--resumen-->
                                    </div>

                                    <div class="w-full input-text px-2 mt-6">
                                        <label>Nota u observación:</label>
                                        <vs-textarea class="w-full mt-3" label="Descripción..." height="170px"
                                            v-model="form.nota_servicios_gratis" ref="comentario" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom-buttons-section">
                <div class="text-advice">
                    <span class="ojo-advice">Ojo:</span>
                    Por favor revise la información ingresada, si todo es
                    correcto de click en el "Botón de Abajo”.
                </div>

                <div class="w-full">
                    <vs-button class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0" color="success"
                        @click="Guardar()">
                        <span>Guardar Cambios</span>
                    </vs-button>
                </div>
            </div>
        </vs-popup>
        <Password v-if="openStatus" :show="openStatus" :callback-on-success="callback"
            @closeVerificar="openStatus = false" :accion="'Entrega de Convenios'"></Password>
    </div>
</template>
<script>
import vSelect from "vue-select";
import Password from "@pages/confirmar_password";
import clientes from "@services/clientes";

export default {
    name: "ServiciosGratis",
    components: {
        "v-select": vSelect,
        Password
    },
    props: {
        show: {
            type: Boolean,
            required: true
        },
        datos: {
            type: Array,
            required: true,
            default: {}
        }
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newValue) {
                if (newValue) {
                    this.form.nota_servicios_gratis = this.getDatosCliente.nota_servicios_gratis;
                    if (this.getDatosCliente.servicios_gratis > 0) {
                        this.form.servicios_gratis = this.cantidades[
                            this.getDatosCliente.servicios_gratis
                        ];
                    } else {
                        this.form.servicios_gratis = this.cantidades[0];
                    }
                    this.$popupManager.register(
                        this,
                        this.cerrar,
                        "comentario"
                    );
                }
                this.localShow = newValue;
            },
        },
    },
    data() {
        return {
            localShow: false,
            cantidades: [
                {
                    label: "SIN SERVICIOS GRATIS",
                    value: 0
                },
                {
                    label: "1 Servicio",
                    value: 1
                },
                {
                    label: "2 Servicios",
                    value: 2
                },
                {
                    label: "3 Servicios",
                    value: 3
                },
                {
                    label: "4 Servicios",
                    value: 4
                },
                {
                    label: "5 Servicios",
                    value: 5
                }
            ],
            openStatus: false,
            callback: Function,

            form: {
                id: "",
                servicios_gratis: {
                    label: "SIN SERVICIOS GRATIS",
                    value: 0
                },
                nota_servicios_gratis: ""
            },
            errores: []
        };
    },
    computed: {
        getDatosCliente: {
            get() {
                return this.datos;
            },
            set(newValue) {
                return newValue;
            }
        },
        getInfo() {
            return 1;
        }
    },
    methods: {
        cerrar() {
            this.form.nota_servicios_gratis = "";
            this.form.servicios_gratis = this.cantidades[0];
            this.$emit("closeServiciosGratis");
        },
        Guardar() {
            //llamando a la function segun su cotnrolador
            this.form.id = this.getDatosCliente.id;
            (async () => {
                this.callback = await this.actualizar;
                this.openStatus = true;
            })();
        },
        async actualizar() {
            this.$vs.loading();
            try {
                let res = await clientes.servicios_gratis(this.form);
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Servicios Gratis",
                        text: "Se ha actualizado correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000
                    });
                    this.$emit("closeServiciosGratis");
                } else {
                    this.$vs.notify({
                        title: "Servicios Gratis",
                        text: "Error, por favor reintente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 4000
                    });
                }
                this.$vs.loading.close();
            } catch (err) {
                if (err.response) {
                    if (err.response.status == 403) {
                        /**FORBIDDEN ERROR */
                        this.$vs.notify({
                            title: "Permiso denegado",
                            text:
                                "Verifique sus permisos con el administrador del sistema.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 4000
                        });
                    } else if (err.response.status == 409) {
                        this.$vs.notify({
                            title: "Cancelar entrega de convenio",
                            text: err.response.data.error,
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 30000
                        });
                    }
                }
                this.$vs.loading.close();
            }
        }
    },
    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name);
    },
    beforeDestroy() {
        this.$popupManager.unregister(this.$options.name);
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
