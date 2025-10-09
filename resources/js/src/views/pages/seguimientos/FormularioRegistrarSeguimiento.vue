<template>
    <div>
        <vs-popup :class="['forms-popup', fueProgramado ? 'popup-100' : ' popup-70', z_index]" :fullscreen="false"
            close="cancelar" :title="popupTitle" :active="localShow" :ref="this.$options.name">
            <div class="flex flex-wrap pb-4">
                <div class="w-full ">{{ filters }} / {{ tipo }}</div>
                <div class="px-2 w-full mb-2" v-if="fueProgramado">
                    <div class="w-full highlighted-inputs-primary">
                        <InfoOperacion ref="InfoOperacion" v-if="show" :filters="filters"
                            @resultado="resultado_datos_cliente">
                        </InfoOperacion>
                    </div>
                </div>
                <div :class="[
                    'w-full p-2 form-datos-registrar',
                    fueProgramado ? 'xl:w-6/12' : '',
                ]">
                    <div class="form-group">
                        <div class="title-form-group">
                            Datos del Seguimiento Realizado
                        </div>
                        <div class="form-group-content">
                            <div class="w-full highlighted-inputs-primary mb-2" v-if="!fueProgramado">
                                <InfoOperacion ref="InfoOperacion" v-if="show" :filters="filters"
                                    @resultado="resultado_datos_cliente">
                                </InfoOperacion>
                            </div>
                            <!--Contenido Form-->
                            <div class="flex flex-wrap">
                                <div class="w-full md:w-4/12 px-2 input-text">
                                    <label>Fecha y Hora de Contacto</label>
                                    <span>(*)</span>
                                    <flat-pickr ref="fechahora_seguimiento" name="fechahora_seguimiento"
                                        data-vv-as="Fecha del Seguimiento" v-validate="'required'"
                                        :config="configdateTimePickerWithTime" v-model="formData.fechahora_seguimiento"
                                        placeholder="Fecha de Contacto" class="w-full" @input="clearAllErrors"
                                        :disabled="isReadOnly" />
                                    <span v-show="errors.has('fechahora_seguimiento')" class="">
                                        {{ errors.first("fechahora_seguimiento") }}
                                    </span>
                                    <span v-if="this.errores.fechahora_seguimiento" class="block">{{
                                        errores.fechahora_seguimiento[0]
                                        }}</span>
                                </div>
                                <div class="w-full md:w-4/12 px-2 input-text">
                                    <label>
                                        Motivo
                                        <span>(*)</span>
                                    </label>
                                    <v-select :options="motivos" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                        v-model="formData.motivo" class="w-full" name="motivo" data-vv-as="Motivo"
                                        v-validate="'required-select'" @input="clearAllErrors" :disabled="isReadOnly">
                                        <div slot="no-options">
                                            No Se Ha Seleccionado Ningún Motivo
                                        </div>
                                    </v-select>
                                    <span v-show="errors.has('motivo')" class="">
                                        {{ errors.first("motivo") }}
                                    </span>
                                    <span v-if="this.errores['motivo.value']" class="block">{{
                                        errores["motivo.value"][0]
                                        }}</span>
                                </div>
                                <div class="w-full md:w-4/12 px-2 input-text">
                                    <label>
                                        Resultado Obtenido
                                        <span>(*)</span>
                                    </label>
                                    <v-select :options="resultados" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                        v-model="formData.resultado" class="w-full" name="resultado"
                                        data-vv-as="resultado" v-validate="'required-select'" @input="clearAllErrors"
                                        :disabled="isReadOnly">
                                        <div slot="no-options">
                                            No Se Ha Seleccionado Ningún Motivo
                                        </div>
                                    </v-select>
                                    <span v-show="errors.has('resultado')" class="">
                                        {{ errors.first("resultado") }}
                                    </span>
                                    <span v-if="this.errores['resultado.value']" class="block">{{
                                        errores["resultado.value"][0]
                                    }}</span>
                                </div>
                            </div>
                            <!--Fin Contenido Form-->
                        </div>
                    </div>
                </div>
                <div v-if="fueProgramado" :class="['w-full p-2 form-datos-programado', 'xl:w-6/12']">
                    <div class="form-group">
                        <div class="title-form-group">
                            Datos del Seguimiento Programado
                        </div>
                        <div class="form-group-content">
                            <!--Contenido Form-->
                            <ProgramarSeguimientoDatos ref="seguimientoForm" v-model="formDataProgramado"
                                :tipo="'consultar'">
                            </ProgramarSeguimientoDatos>
                        </div>
                    </div>
                </div>
            </div>
        </vs-popup>
    </div>
</template>
<script>
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import Password from "@pages/confirmar_password";
import seguimientos from "../../../services/seguimientos";
import ProgramarSeguimientoDatos from "./ProgramarSeguimientoDatos.vue";
import InfoOperacion from "./InfoOperacion.vue";
import vSelect from "vue-select";
import { configdateTimePickerWithTime } from "@/VariablesGlobales";
export default {
    // Name of the component (optional)
    name: "FormularioRegistrarSeguimiento",
    components: {
        "v-select": vSelect,
        ProgramarSeguimientoDatos,
        InfoOperacion,
        Password,
        flatPickr,
    },
    // Props: data passed from parent
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
        filters: {
            type: Object,
            required: false,
            default: {},
        },
        tipo: {
            type: String,
            required: true,
            default: "agregar", //agregar, modificar, consultar, cancelar, atender_seguimiento_programado
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newVal) {
                // Only listen when visible = true
                if (newVal) {
                    this.$popupManager.register(this, this.cancelar);

                    await this._getResultadosMedios();
                    if (this.fueProgramado) {
                        //datos del seguimiento programado
                        await this._loadSeguimientosProgramadoDatos();
                    }
                }
            },
        },
    },
    // Computed properties: derived reactive data
    computed: {
        fueProgramado() {
            return this.tipo === "atender_seguimiento_programado";
        },
        isReadOnly() {
            return (
                this.tipo === "consultar" &&
                this.tipo !== "cancelar"
            );
        },
        verEnviarEmailChk() {
            if (this.tipo === "cancelar") {
                return this.formDataProgramado.email ? true : false;
            } else return true;
        },
        popupTitle() {
            switch (this.tipo) {
                case "agregar":
                    return "Registrar Seguimiento";
                case "modificar":
                    return "Modificar Seguimiento";
                case "consultar":
                    return "Consultar Seguimiento";
                case "cancelar":
                    return "Cancelar Seguimiento";
                case "atender_seguimiento_programado":
                    return "Atender Seguimiento Programado";
                default:
                    return "N/A";
            }
        },
        successTextRespnse() {
            switch (this.tipo) {
                case "agregar":
                    return "Seguimiento programado correctamente";
                case "modificar":
                    return "Seguimiento actualizado correctamente";
                case "cancelar":
                    return "Seguimiento cancelado correctamente";
                default:
                    return "N/A";
            }
        },
        buttonTitle() {
            switch (this.tipo) {
                case "modificar":
                    return "Modificar Seguimiento";
                case "cancelar":
                    return "Cancelar Seguimiento";
                default:
                    return "Programar Seguimiento";
            }
        },
        buttonColor() {
            switch (this.tipo) {
                case "cancelar":
                    return "danger";
                default:
                    return "success";
            }
        },
    },
    // Data function returns the component's reactive state
    data() {
        return {
            localShow: false, // controls popup visibility
            configdateTimePickerWithTime: configdateTimePickerWithTime,
            //datos del form de registro de seguimientos y atendidos
            resultados: [],
            medios: [],
            motivos: [],
            motivos_de_cancelacion: [],
            formData: {
                seguimiento_id: null,
                fechahora_seguimiento: "",
                enviar_x_email: false,
                resultado: { label: "Seleccione 1", value: "" },
                medio: { label: "Seleccione 1", value: "" },
                email_seguimiento: "",
                motivo_cancelacion: { label: "Seleccione 1", value: "" },
                motivo: { label: "Seleccione 1", value: "" },
                comentario_seguimiento: "",
                comentario_cancelacion: "",
            },
            errores: [], //from backend
            ready: {
                InfoOperacion: false,
                loadSeguimientoProgramadoDatos: false,
                loadMotivosResultadosMedios: false
            },
            //Form Data de Seguimientos Programados
            formDataProgramado: {
                fecha_a_contactar: "",
                motivo: { label: "Seleccione 1", value: "" },
                medio: { label: "Seleccione 1", value: "" },
                email: "",
                comentario_programado: "",
            },
        };
    },
    // Methods: functions you can call in template or other hooks
    methods: {
        clearAllErrors() {
            this.errors.clear();
        },
        cancelar() {
            this.limpiarVentana();
            this.$emit("closeVentana");
        },
        limpiarVentana() { },
        // Called when InfoOperacion is ready
        resultado_datos_cliente(success, email = null) {
            this.$log("🚀 ~ resultado_datos_cliente ~ success:", success);
            if (success) {
                this.ready.InfoOperacion = true;
                this.checkReady();
            } else {
                this.cancelar();
            }
        },

        checkReady() {
            if (this.fueProgramado) {
                if (
                    this.ready.InfoOperacion &&
                    this.ready.loadSeguimientoProgramadoDatos
                    && this.ready.loadMotivosResultadosMedios
                ) {
                    this.localShow = true;
                }
            } else {
                if (this.ready.InfoOperacion && this.ready.loadMotivosResultadosMedios) {
                    this.localShow = true;
                }
            }
        },
        async _loadSeguimientosProgramadoDatos() {
            if (!this.show) return; // stop here if not visible
            const params = {
                cliente_id: this.filters.cliente_id,
                tipo_cliente_id: this.filters.tipo_cliente_id,
                programado_b: 1,
                status: 1,
                id: this.filters.seguimiento_id,
            };
            this.$log("🚀 ~ _getSeguimientosProgramados ~ params:", params);
            //this.$vs.loading();
            try {
                // Call the API from seguimientos service
                const result = await seguimientos.getSeguimientosProgramados(
                    params
                );
                this.$log("🚀 ~ _getSeguimientosProgramados ~ result:", result);
                if (!result || typeof result !== "object") {
                    throw new Error(
                        "Respuesta inválida en _getSeguimientosProgramados"
                    );
                }
                this.formDataProgramado.email = result[0].email_programado;
                this.formDataProgramado.comentario_programado =
                    result[0].comentario_programado;
                this.formDataProgramado.motivo = {
                    label: result[0].motivo_texto,
                    value: result[0].motivo_id,
                };
                this.formDataProgramado.medio = {
                    label: result[0].medio_texto,
                    value: result[0].medio_preferido_programado_id,
                };
                let fecha_contacto = result[0].fecha_programada.split("-");
                let hora_contacto = result[0].hora_programada.split(":");
                //yyyy-mm-dd hh:mm
                this.formDataProgramado.fecha_a_contactar = new Date(
                    fecha_contacto[0],
                    fecha_contacto[1] - 1,
                    fecha_contacto[2],
                    hora_contacto[0],
                    hora_contacto[1]
                );
                this.ready.loadSeguimientoProgramadoDatos = true;
                this.checkReady();
            } catch (error) {
                this.$error("Error fetching seguimientos:", error);
                this.cancelar();
            } finally {
                //this.$vs.loading.close();
            }
        },
        async _getResultadosMedios() {
            this.$vs.loading();
            try {
                let medios = await seguimientos.getMedios();
                let resultados = await seguimientos.getResultadosContacto();
                let motivos = await seguimientos.getResultadosContacto();
                // ✅ Validate responses
                if (!resultados || typeof resultados !== "object") {
                    throw new Error("Respuesta inválida en resultados de contacto");
                }
                if (!medios || typeof medios !== "object") {
                    throw new Error("Respuesta inválida en medios");
                }
                if (!motivos || typeof motivos !== "object") {
                    throw new Error("Respuesta inválida en motivos");
                }

                if (this.tipo === "cancelar") {
                    let motivosCancelar =
                        await seguimientos.getMotivosCancelacion();
                    // ✅ Validate responses
                    if (
                        !motivosCancelar ||
                        typeof motivosCancelar !== "object"
                    ) {
                        throw new Error(
                            "Respuesta inválida en motivos de cancelación"
                        );
                    }
                    this.motivos_de_cancelacion = [
                        { value: "", label: "Seleccione 1" }, // 👈 default blank
                        ...Object.entries(motivosCancelar).map(
                            ([key, label]) => ({
                                value: key,
                                label,
                            })
                        ),
                    ];
                }
                // build array with default + API values
                this.resultados = [
                    { value: "", label: "Seleccione 1" }, // 👈 default blank
                    ...Object.entries(resultados).map(([key, label]) => ({
                        value: key,
                        label,
                    })),
                ];
                this.$log("🚀 ~ _getResultadosMedios ~ this.resultados:", this.resultados)
                this.medios = [
                    { value: "", label: "Seleccione 1" }, // 👈 default blank
                    ...Object.entries(medios).map(([key, label]) => ({
                        value: key,
                        label,
                    })),
                ];
                this.$log("🚀 ~ _getResultadosMedios ~ this.medios:", this.medios)

                this.motivos = [
                    { value: "", label: "Seleccione 1" }, // 👈 default blank
                    ...Object.entries(motivos).map(([key, label]) => ({
                        value: key,
                        label,
                    })),
                ];
                this.$log("🚀 ~ _getResultadosMedios ~ this.motivos:", this.motivos)




                this.ready.loadMotivosResultadosMedios = true
            } catch (error) {
                this.$vs.notify({
                    title: "Programar Seguimientos",
                    text: error,
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                });
            } finally {
                this.$vs.loading.close();
                this.checkReady();
            }
        },
    },
    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name); // DOM is ready
        const icon =
            this.$refs[this.$options.name].$el.querySelector(".vs-icon");
        if (icon) {
            icon.addEventListener("click", (e) => {
                e.preventDefault(); // stop form submission / page reload
                e.stopPropagation(); // stop bubbling if needed
                this.cancelar();
            });
        }
    },
    beforeDestroy() {
        this.$popupManager.unregister(this.$options.name);
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
