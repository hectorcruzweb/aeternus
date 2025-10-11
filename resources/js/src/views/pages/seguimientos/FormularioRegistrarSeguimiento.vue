<template>
    <div>
        <vs-popup :class="[
            'forms-popup',
            fueProgramado ? 'popup-95' : ' popup-70',
            z_index,
        ]" :fullscreen="false" close="cancelar" :title="popupTitle" :active="localShow" :ref="this.$options.name">
            <div class="flex flex-wrap pb-4">
                <div class="w-full">
                    {{ filters }}
                </div>
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
                ]" ref="seguimientoForm">
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
                                <div :class="[
                                    'w-full px-2 input-text',
                                    !fueProgramado
                                        ? 'md:w-4/12'
                                        : 'md:w-6/12',
                                ]">
                                    <label>Fecha y Hora de Contacto</label>
                                    <span>(*)</span>
                                    <flat-pickr ref="fechahora_seguimiento" name="fechahora_seguimiento"
                                        data-vv-as="Fecha del Seguimiento" v-validate="'required'"
                                        :config="configdateTimePickerWithTime" v-model="formData.fechahora_seguimiento"
                                        placeholder="Fecha de Contacto" class="w-full" @input="clearAllErrors"
                                        :disabled="isReadOnly" />
                                    <span v-show="errors.has('fechahora_seguimiento')
                                        " class="">
                                        {{
                                            errors.first(
                                                "fechahora_seguimiento"
                                            )
                                        }}
                                    </span>
                                    <span v-if="
                                        this.errores.fechahora_seguimiento
                                    " class="block">{{
                                        errores.fechahora_seguimiento[0]
                                    }}</span>
                                </div>
                                <div :class="[
                                    'w-full px-2 input-text',
                                    'md:w-4/12',
                                ]" v-if="!fueProgramado">
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
                                        errores["motivo.value"][0] }}</span>
                                </div>
                                <div :class="[
                                    'w-full px-2 input-text',
                                    !fueProgramado
                                        ? 'md:w-4/12'
                                        : 'md:w-6/12',
                                ]">
                                    <label>
                                        Resultado Obtenido
                                        <span>(*)</span>
                                    </label>
                                    <v-select :options="resultados" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                        v-model="formData.resultado" class="w-full" name="resultado"
                                        data-vv-as="resultado" v-validate="'required-select'" @input="clearAllErrors"
                                        :disabled="isReadOnly">
                                        <div slot="no-options">
                                            No Se Ha Seleccionado Ningún
                                            Resultado
                                        </div>
                                    </v-select>
                                    <span v-show="errors.has('resultado')" class="">
                                        {{ errors.first("resultado") }}
                                    </span>
                                    <span v-if="this.errores['resultado.value']" class="block">{{
                                        errores["resultado.value"][0]
                                        }}</span>
                                </div>
                                <div class="w-full md:w-6/12 px-2 input-text">
                                    <label>
                                        Medio de Contacto
                                        <span>(*)</span>
                                    </label>
                                    <v-select :options="medios" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                        v-model="formData.medio" class="w-full" name="medio"
                                        data-vv-as="Medio de Contacto" v-validate="'required-select'"
                                        @input="clearAllErrors" :disabled="isReadOnly">
                                        <div slot="no-options">
                                            No Se Ha Seleccionado Ningún Medio
                                        </div>
                                    </v-select>
                                    <span v-show="errors.has('medio')" class="">
                                        {{ errors.first("medio") }}
                                    </span>
                                    <span v-if="this.errores['medio.value']" class="block">{{ errores["medio.value"][0]
                                    }}</span>
                                </div>

                                <div class="w-full md:w-6/12 px-2 input-text">
                                    <label>
                                        Email
                                        <span></span>
                                    </label>
                                    <vs-input v-validate="formData.enviar_x_email
                                        ? 'required|email'
                                        : 'email'
                                        " name="email_seguimiento" type="email" class="w-full"
                                        placeholder="Ej. cliente@gmail.com" v-model="formData.email_seguimiento"
                                        maxlength="100" @input="clearAllErrors" :disabled="isReadOnly" />
                                    <span v-show="errors.has('email_seguimiento')" class="">
                                        {{ errors.first("email_seguimiento") }}
                                    </span>
                                    <span v-if="this.errores.email_seguimiento" class="block">{{
                                        errores.email_seguimiento[0]
                                        }}</span>
                                </div>
                                <div class="w-full px-2 pt-2 small-editor">
                                    <NotasComponent :readonly="isReadOnly" :value="formData.comentario_seguimiento"
                                        @input="
                                            (val) => {
                                                formData.comentario_seguimiento =
                                                    val;
                                            }
                                        " />
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
                            <ProgramarSeguimientoDatos v-model="formDataProgramado" :tipo="'consultar'">
                            </ProgramarSeguimientoDatos>
                        </div>
                    </div>
                </div>
                <!--Send Form-->
                <div v-if="!isReadOnly" class="w-full flex flex-wrap items-center justify-between pr-2 pt-4">
                    <vs-checkbox v-show="verEnviarEmailChk" color="success" class="size-small text-info"
                        v-model="formData.enviar_x_email" :vs-value="formData.enviar_x_email">¿Enviar por correo
                        electrónico?</vs-checkbox>
                    <vs-button ref="programar_seguimiento_button" class="ml-auto" :color="buttonColor"
                        @click="EnviarForm">
                        {{ buttonTitle }}
                    </vs-button>
                </div>
            </div>
        </vs-popup>
    </div>
</template>
<script>
import NotasComponent from "@pages/NotasComponent";
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
        NotasComponent,
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
                    await this._getResultadosMedios();
                    if (this.fueProgramado) {
                        //datos del seguimiento programado
                        await this._loadSeguimientosProgramadoDatos();
                    }
                    this.checkReady();

                    await this.$popupManager.register(
                        this,
                        this.cancelar,
                        "fechahora_seguimiento"
                    );
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
            return false;
        },
        verEnviarEmailChk() {
            if (this.tipo === "cancelar") {
                return this.formDataProgramado.email_seguimiento ? true : false;
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
                    return "Seguimiento registrado correctamente";
                case "modificar":
                    return "Seguimiento actualizado correctamente";
                case "cancelar":
                    return "Seguimiento cancelado correctamente";
                case "atender_seguimiento_programado":
                    return "Seguimiento atendido correctamente";
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
                    return "Registrar Seguimiento";
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
                loadMotivosResultadosMedios: false,
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
        /**
         * Filters
         * { "cliente_id": 4, "tipo_cliente_id": 1, "operacion_id": null, "seguimiento_id": null }
         * Tipo
         * agregar
         */
        async EnviarForm() {
            this.errores = [];
            // Run local validation first
            const isValid = await this.$validator.validateAll();
            if (!isValid) {
                this.$vs.notify({
                    title: "Error",
                    text: "Por favor corrige los errores antes de continuar.",
                    color: "danger",
                });
                return;
            }

            this.accionNombre = this.popupTitle;
            this.callback = await this.submitForm;
            if (this.tipo === "modificar" || this.tipo === "cancelar") {
                this.openPassword = true;
                return;
            }
            //manda sin confirmar password
            await this.submitForm();
        },
        async submitForm() {
            // ✅ Continue submit logic here
            this.$vs.loading();
            try {
                // Merge formData + filters into one object
                const payload = {
                    ...this.formData,
                    ...this.filters, // props object
                };
                this.$log("🚀 ~ submitForm ~ payload:", payload)
                const response = await seguimientos.registrarSeguimiento(
                    this.tipo, //tipo prompt
                    payload
                );
                this.$log("Success:", response);
                let success_text = this.successTextRespnse;
                this.$vs.notify({
                    title: "Éxito",
                    text: success_text,
                    color: "success",
                    time: 5000,
                });
                //close form and call the api to reload the form
                this.$emit("agregar_modificar_success_seguimiento");
            } catch (err) {
                this.$log("🚀 ~ submitForm ~ err:", err);
                const status = err.status || 500;
                if (status === 403) {
                    // Forbidden
                    this.$vs.notify({
                        title: "Permiso denegado",
                        text: "Verifique sus permisos con el administrador del sistema.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "warning",
                        time: 4000,
                    });
                } else if (status === 422) {
                    // Validation errors
                    this.errores =
                        err.data && err.data.error ? err.data.error : {};
                    this.$vs.notify({
                        title: "Validación",
                        text: "Verifique los errores encontrados en los datos.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 5000,
                    });
                } else if (status === 409) {
                    // Business rule conflict
                    this.$vs.notify({
                        title: "Programar seguimientos",
                        text:
                            (err.data && err.data.error) ||
                            "Conflicto en la operación.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 8000,
                    });
                } else {
                    // Fallback error
                    this.$vs.notify({
                        title: "Error inesperado",
                        text:
                            err.message || "Error desconocido en el servidor.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 5000,
                    });
                }
            } finally {
                this.$vs.loading.close();
            }
        },
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
            if (success) {
                this.ready.InfoOperacion = true;
                if (
                    this.tipo == "agregar" ||
                    this.tipo === "atender_seguimiento_programado"
                ) {
                    this.formData.email_seguimiento = email;
                }
            } else {
                this.$error("🚀 ~ resultado_datos_cliente ~ error:", success);
                this.cancelar();
            }
        },
        checkReady() {
            if (this.fueProgramado) {
                if (
                    this.ready.InfoOperacion &&
                    this.ready.loadSeguimientoProgramadoDatos &&
                    this.ready.loadMotivosResultadosMedios
                ) {
                    this.localShow = true;
                }
            } else {
                if (
                    this.ready.InfoOperacion &&
                    this.ready.loadMotivosResultadosMedios
                ) {
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
                let motivos = await seguimientos.getMotivos();
                // ✅ Validate responses
                if (!resultados || typeof resultados !== "object") {
                    throw new Error(
                        "Respuesta inválida en resultados de contacto"
                    );
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
                this.$log(
                    "🚀 ~ _getResultadosMedios ~ this.resultados:",
                    this.resultados
                );
                this.medios = [
                    { value: "", label: "Seleccione 1" }, // 👈 default blank
                    ...Object.entries(medios).map(([key, label]) => ({
                        value: key,
                        label,
                    })),
                ];
                this.$log(
                    "🚀 ~ _getResultadosMedios ~ this.medios:",
                    this.medios
                );

                this.motivos = [
                    { value: "", label: "Seleccione 1" }, // 👈 default blank
                    ...Object.entries(motivos).map(([key, label]) => ({
                        value: key,
                        label,
                    })),
                ];
                this.$log(
                    "🚀 ~ _getResultadosMedios ~ this.motivos:",
                    this.motivos
                );
                this.ready.loadMotivosResultadosMedios = true;
            } catch (error) {
                this.$error("🚀 ~ _getResultadosMedios ~ error:", error);
                this.cancelar();
                this.$vs.notify({
                    title: "Programar Seguimientos",
                    text: error,
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                });
            } finally {
                this.$vs.loading.close();
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
