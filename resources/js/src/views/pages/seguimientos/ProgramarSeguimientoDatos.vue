<template>
    <div>
        <div class="flex flex-wrap">
            <div class="w-full md:w-6/12 px-2 input-text">
                <label>Fecha y Hora a Contactar</label>
                <span>(*)</span>
                <flat-pickr
                    ref="fecha_a_contactar"
                    name="fecha_a_contactar"
                    data-vv-as="Fecha a Contactar"
                    v-validate="'required'"
                    :config="configdateTimePickerWithTime"
                    v-model="proxy.fecha_a_contactar"
                    placeholder="Fecha de Contacto"
                    class="w-full"
                    @input="clearAllErrors"
                    :disabled="isReadOnly"
                />
                <span v-show="errors.has('fecha_a_contactar')" class="">
                    {{ errors.first("fecha_a_contactar") }}
                </span>
                <span v-if="this.errores.fecha_a_contactar" class="block">{{
                    errores.fecha_a_contactar[0]
                }}</span>
            </div>
            <div class="w-full md:w-6/12 px-2 input-text">
                <label>
                    Motivo
                    <span>(*)</span>
                </label>
                <v-select
                    :options="motivos"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="proxy.motivo"
                    class="w-full"
                    name="motivo"
                    data-vv-as="Motivo"
                    v-validate="'required-select'"
                    @input="clearAllErrors"
                    :disabled="isReadOnly"
                >
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
            <div class="w-full md:w-6/12 px-2 input-text">
                <label>
                    Medio de Contacto
                    <span>(*)</span>
                </label>
                <v-select
                    :options="medios"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="proxy.medio"
                    class="w-full"
                    name="medio"
                    data-vv-as="Medio de Contacto"
                    v-validate="'required-select'"
                    @input="clearAllErrors"
                    :disabled="isReadOnly"
                >
                    <div slot="no-options">
                        No Se Ha Seleccionado Ningún Medio
                    </div>
                </v-select>
                <span v-show="errors.has('medio')" class="">
                    {{ errors.first("medio") }}
                </span>
                <span v-if="this.errores['medio.value']" class="block">{{
                    errores["medio.value"][0]
                }}</span>
            </div>
            <div class="w-full md:w-6/12 px-2 input-text">
                <label>
                    Email
                    <span></span>
                </label>
                <vs-input
                    v-validate="
                        proxy.enviar_x_email ? 'required|email' : 'email'
                    "
                    name="email"
                    type="email"
                    class="w-full"
                    placeholder="Ej. cliente@gmail.com"
                    v-model="proxy.email"
                    maxlength="100"
                    @input="clearAllErrors"
                    :disabled="isReadOnly"
                />
                <span v-show="errors.has('email')" class="">
                    {{ errors.first("email") }}
                </span>
                <span v-if="this.errores.email" class="block">{{
                    errores.email[0]
                }}</span>
            </div>
            <div class="w-full px-2 pt-2 small-editor">
                <NotasComponent
                    :readonly="isReadOnly"
                    :value="proxy.comentario_programado"
                    @input="
                        (val) => {
                            proxy.comentario_programado = val;
                        }
                    "
                />
            </div>

            <div v-if="tipo === 'cancelar'" class="w-full px-2 mt-2">
                <div
                    class="w-full flex flex-wrap highlighted-inputs-danger pt-2 pb-4"
                >
                    <div class="w-full py-2 px-2">
                        <div
                            class="w-full text-center uppercase py-2 bg-danger text-white"
                        >
                            Motivo de Cancelación
                        </div>
                    </div>
                    <div class="w-full md:w-6/12 px-2 input-text">
                        <label>
                            Motivo de Cancelación
                            <span>(*)</span>
                        </label>
                        <v-select
                            :options="motivos_de_cancelacion"
                            :clearable="false"
                            :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="proxy.motivo_cancelacion"
                            class="w-full"
                            name="motivo_cancelacion"
                            data-vv-as="Motivo de Cancelación"
                            v-validate="'required-select'"
                            @input="clearAllErrors"
                        >
                            <div slot="no-options">
                                No hay Motivos de Cancelación
                            </div>
                        </v-select>
                        <span
                            v-show="errors.has('motivo_cancelacion')"
                            class=""
                        >
                            {{ errors.first("motivo_cancelacion") }}
                        </span>
                        <span
                            v-if="this.errores['motivo_cancelacion.value']"
                            class="block"
                            >{{ errores["motivo_cancelacion.value"][0] }}</span
                        >
                    </div>
                    <div class="w-full md:w-6/12 px-2 input-text">
                        <label>
                            Comentario / Descripción
                            <span></span>
                        </label>
                        <vs-input
                            v-validate=""
                            name="comentario_cancelacion"
                            type="text"
                            class="w-full"
                            placeholder=""
                            v-model="proxy.comentario_cancelacion"
                            maxlength="250"
                            @input="clearAllErrors"
                        />
                        <span
                            v-show="errors.has('comentario_cancelacion')"
                            class=""
                        >
                            {{ errors.first("comentario_cancelacion") }}
                        </span>
                        <span
                            v-if="this.errores.comentario_cancelacion"
                            class="block"
                            >{{ errores.comentario_cancelacion[0] }}</span
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import seguimientos from "../../../services/seguimientos";
import NotasComponent from "@pages/NotasComponent";
import vSelect from "vue-select";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import { configdateTimePickerWithTime } from "@/VariablesGlobales";
export default {
    components: {
        "v-select": vSelect,
        flatPickr,
        NotasComponent,
    },
    // Name of the component (optional)
    name: "ProgramarSeguimientoDatos",
    // Props: data passed from parent
    props: {
        value: {
            type: Object,
            default: () => ({}),
        },
        errores: {
            type: Object,
            default: () => ({}),
        },
        tipo: {
            type: String,
            required: true,
            default: "agregar", //agregar, modificar, consulta
        },
    },
    emits: ["update:modelValue"],
    // Computed properties: derived reactive data
    computed: {
        proxy: {
            get() {
                return this.value;
            },
            set(val) {
                this.$emit("input", val);
            },
        },
        isReadOnly() {
            return this.tipo !== "agregar" && this.tipo !== "modificar";
        },
    },
    watch: {},
    // Data function returns the component's reactive state
    data() {
        return {
            configdateTimePickerWithTime: configdateTimePickerWithTime,
            // clone to avoid mutating prop directly
            localForm: Object.assign({}, this.modelValue),
            // alternatively: JSON.parse(JSON.stringify(this.value || {}))
            motivos: [],
            medios: [],
            motivos_de_cancelacion: [],
        };
    },
    // Methods: functions you can call in template or other hooks
    methods: {
        async validate() {
            const valid = await this.$validator.validateAll();
            return valid; // true or false
        },
        clearAllErrors() {
            this.errors.clear();
        },
        async _getMotivosandMedios() {
            this.$vs.loading();
            try {
                let motivos = await seguimientos.getMotivos();
                let medios = await seguimientos.getMedios();
                // ✅ Validate responses
                if (!motivos || typeof motivos !== "object") {
                    throw new Error("Respuesta inválida en motivos");
                }
                if (!medios || typeof medios !== "object") {
                    throw new Error("Respuesta inválida en medios");
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
                this.motivos = [
                    { value: "", label: "Seleccione 1" }, // 👈 default blank
                    ...Object.entries(motivos).map(([key, label]) => ({
                        value: key,
                        label,
                    })),
                ];
                this.medios = [
                    { value: "", label: "Seleccione 1" }, // 👈 default blank
                    ...Object.entries(medios).map(([key, label]) => ({
                        value: key,
                        label,
                    })),
                ];
                this.$emit("resultado", true);
            } catch (error) {
                this.$emit("resultado", false);
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
    async created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
        await this._getMotivosandMedios();
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name); // DOM is ready
    },
    beforeDestroy() {},
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
<style scoped></style>
