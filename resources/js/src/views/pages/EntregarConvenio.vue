<template>
    <div class="centerx">
        <vs-popup :class="['forms-popup popup-50', z_index]" close="cancelar" :title="'ENTREGA DE CONVENIOS'"
            :active="localShow" :ref="this.$options.name">
            <div class="p-8">
                <div class="form-group">
                    <div class="title-form-group">Datos del convenio</div>
                    <div class="form-group-content">
                        <div class="flex flex-wrap">
                            <div class="w-full">
                                <div class="flex flex-wrap">
                                    <div class="w-full xl:w-12/12 px-2">
                                        <!--resumen-->
                                        <div class="flex flex-wrap">
                                            <div
                                                class="p-4 w-full mx-auto rounded-lg size-base border-gray-solid-1 rounded-lg">
                                                <div
                                                    class="size-base font-bold color-black-900 uppercase pb-6 text-center">
                                                    Resumen del Convenio
                                                </div>

                                                <div class="flex flex-wrap color-copy">
                                                    <div class="w-full">
                                                        <div class="flex flex-wrap pb-6">
                                                            <div
                                                                class="w-full text-center font-medium color-black-900 uppercase">
                                                                <span v-if="
                                                                    getDatosConvenio.tipo ==
                                                                    'terreno'
                                                                ">Ubicación
                                                                    de la
                                                                    Propiedad</span>
                                                                <span v-else>Paquete
                                                                    Funerario</span>
                                                            </div>
                                                            <div class="w-full text-center font-medium color-copy pt-2">
                                                                <span class="capitalize">
                                                                    <span>{{
                                                                        getDatosConvenio.producto
                                                                        }}</span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="w-full">
                                                        <div class="flex flex-wrap">
                                                            <div
                                                                class="w-full xl:w-6/12 text-center font-medium color-black-900 uppercase">
                                                                Titular
                                                            </div>
                                                            <div
                                                                class="w-full xl:w-6/12 text-center font-medium color-copy">
                                                                <span>{{
                                                                    getDatosConvenio.titular
                                                                    }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="w-full">
                                                        <div class="w-full xl:w-8/12 input-text px-2 mx-auto mt-5">
                                                            <label>Estatus del
                                                                covenio:</label>
                                                            <v-select :options="estados
                                                                " :clearable="false
                                                                    " v-model="form.estado
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
                                                    <div class="w-full" v-if="
                                                        getDatosConvenio.status ==
                                                        1
                                                    ">
                                                        <div class="w-full text-center mx-auto mt-5">
                                                            Registrado el día:
                                                            {{
                                                                getDatosConvenio.fecha_entrega
                                                            }}, por :
                                                            {{
                                                                getDatosConvenio.entregado_por
                                                            }}
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
                                            v-model="form.nota" ref="comentario" />
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
import cementerio from "@services/cementerio";
export default {
    name: "EntregarConvenio",
    components: {
        "v-select": vSelect,
        Password,
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        datos: {
            type: Array,
            required: true,
            default: {},
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index58k",
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newValue) {
                if (newValue) {
                    this.form.nota = this.getDatosConvenio.nota;
                    this.form.estado = this.estados[0];
                    this.$popupManager.register(this, this.cerrar, "comentario");
                } else {
                    //limpiar
                    this.form.nota = "";
                    this.$popupManager.unregister(this.$options.name);
                }
                this.localShow = newValue;
            },
        },
    },
    data() {
        return {
            localShow: false,
            estados: [
                {
                    label: "ENTREGADO",
                    value: 1,
                },
                {
                    label: "NO ENTREGADO",
                    value: 0,
                },
            ],
            openStatus: false,
            callback: Function,
            form: {
                id: "",
                tipo: "",
                nota: "",
                action: "",
                estado: {},
            },
            errores: [],
        };
    },
    computed: {
        getDatosConvenio: {
            get() {
                return this.datos;
            },
            set(newValue) {
                return newValue;
            },
        },
        getInfo() {
            return 1;
        },
    },
    methods: {
        cerrar() {
            this.$emit("closeEntregarConvenio");
        },
        Guardar() {
            this.form.tipo = this.getDatosConvenio.tipo;
            this.form.id = this.getDatosConvenio.id;
            this.form.action = this.form.estado.value;
            //llamando a la function segun su cotnrolador
            (async () => {
                this.callback = await this.actualizar;
                this.openStatus = true;
            })();
        },
        async actualizar() {
            this.$vs.loading();
            try {
                let res = await cementerio.actualizar_status_convenio(
                    this.form
                );
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Entrega de convenios",
                        text: "Se ha actulizado la entrega del convenio correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                    this.$emit("closeEntregarConvenio");
                } else {
                    this.$vs.notify({
                        title: "Entrega de convenios",
                        text: "Error, por favor reintente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 4000,
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
                    } else if (err.response.status == 409) {
                        this.$vs.notify({
                            title: "Cancelar entrega de convenio",
                            text: err.response.data.error,
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 30000,
                        });
                    }
                }
                this.$vs.loading.close();
            }
        },
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
