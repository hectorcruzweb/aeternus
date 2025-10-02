<template>
    <div>
        <div class="flex flex-wrap">
            <div class="w-full md:w-6/12 px-2 input-text">
                <label>Fecha y Hora a Contactar</label>
                <span>(*)</span>
                <flat-pickr name="fecha_a_contactar" data-vv-as="Fecha a Contactar" v-validate="'required'"
                    :config="configdateTimePickerWithTime" v-model="proxy.fecha_a_contactar"
                    placeholder="Fecha de Contacto" class="w-full" @input="clearAllErrors" />
                <span v-show="errors.has('fecha_a_contactar')" class="">
                    {{ errors.first('fecha_a_contactar') }}
                </span>
            </div>
            <div class="w-full md:w-6/12 px-2 input-text">
                <label>
                    Motivo
                    <span>(*)</span>
                </label>
                <v-select :options="motivos" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="proxy.motivo"
                    class="w-full" name="motivo" data-vv-as="Motivo" v-validate="'required-select'
                        " @input="clearAllErrors">
                    <div slot="no-options">
                        No Se Ha Seleccionado Ningún
                        Motivo
                    </div>
                </v-select>
                <span v-show="errors.has('motivo')" class="">
                    {{ errors.first('motivo') }}
                </span>
            </div>
            <div class="w-full md:w-6/12 px-2 input-text">
                <label>
                    Medio de Contacto
                    <span>(*)</span>
                </label>
                <v-select :options="medios" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="proxy.medio"
                    class="w-full" name="medio" data-vv-as="Medio de Contacto" v-validate="'required-select'
                        " @input="clearAllErrors">
                    <div slot="no-options">
                        No Se Ha Seleccionado Ningún
                        Medio
                    </div>
                </v-select>
                <span v-show="errors.has('medio')" class="">
                    {{ errors.first('medio') }}
                </span>
            </div>
            <div class="w-full md:w-6/12 px-2 input-text">
                <label>
                    Email
                    <span></span>
                </label>
                <vs-input v-validate="proxy.enviar_x_email ? 'required|email' : 'email'" name="email" type="email"
                    class="w-full" placeholder="Ej. cliente@gmail.com" v-model="proxy.email" maxlength="100"
                    @input="clearAllErrors" />
                <span v-show="errors.has('email')" class="">
                    {{ errors.first('email') }}
                </span>
            </div>
            <div class="w-full px-2 pt-2 small-editor">
                <NotasComponent :value="proxy.comentario_programado" @input="
                    val => {
                        proxy.comentario_programado = val;
                    }
                " />
            </div>
        </div>
    </div>
</template>
<script>
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
        NotasComponent
    },
    // Name of the component (optional)
    name: "ProgramarSeguimientoDatos",
    // Props: data passed from parent
    props: {
        value: {
            type: Object,
            default: () => ({})
        },
        errores: {
            type: Object,
            default: () => ({})
        }
    },
    emits: ["update:modelValue"],
    // Computed properties: derived reactive data
    computed: {
        proxy: {
            get() {
                return this.value
            },
            set(val) {
                this.$emit("input", val)
            }
        }
    },
    watch: {
    },
    // Data function returns the component's reactive state
    data() {
        return {
            configdateTimePickerWithTime: configdateTimePickerWithTime,
            // clone to avoid mutating prop directly
            localForm: Object.assign({}, this.modelValue),
            // alternatively: JSON.parse(JSON.stringify(this.value || {}))
            motivos: [
                {
                    "label": "Seleccione 1",
                    "value": ""
                },
                {
                    "label": "Recordatorio de pagos",
                    "value": 1
                },
                {
                    "label": "Recordatorio de mantenimiento anual de nicho o sepultura",
                    "value": 2
                },
                {
                    "label": "Renovación o actualización de plan funerario",
                    "value": 3
                },
                {
                    "label": "Entrega de documentación o certificados",
                    "value": 4
                },
                {
                    "label": "Seguimiento post-servicio funerario",
                    "value": 5
                },
                {
                    "label": "Promoción de planes futuros",
                    "value": 6
                },
                {
                    "label": "Entrega de cenizas, accesorios, etc.",
                    "value": 7
                },
                {
                    "label": "Cambio de ubicación o información de contacto",
                    "value": 8
                },
                {
                    "label": "Llamada de cortesía o atención personalizada",
                    "value": 9
                },
                {
                    "label": "Programación de servicios adicionales",
                    "value": 10
                }
            ]
            ,
            medios: [
                {
                    "label": "Seleccione 1",
                    "value": ""
                },
                {
                    "label": "Teléfono",
                    "value": 1
                },
                {
                    "label": "Correo electrónico",
                    "value": 2
                },
                {
                    "label": "WhatsApp",
                    "value": 3
                },
                {
                    "label": "Mensaje de texto (SMS)",
                    "value": 4
                },
                {
                    "label": "Visita presencial",
                    "value": 5
                },
                {
                    "label": "Redes sociales",
                    "value": 6
                }
            ],
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
        }
    },
    // Lifecycle hooks
    created() {
        console.log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        console.log("Component mounted! " + this.$options.name); // DOM is ready
    },
    beforeDestroy() { },
    destroyed() {
        console.log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
<style scoped></style>
