<template>
    <div class="centerx">
        <vs-popup
            :class="['forms-popup popup-80', z_index]"
            close="cancelar"
            fullscreen
            :title="
                getTipoformulario == 'modificar'
                    ? 'Modificar Solicitud de Servicio Funerario'
                    : 'Solicitud de Servicio Funerario'
            "
            :active="localShow"
            :ref="this.$options.name"
        >
            <div class="flex flex-wrap">
                <div class="w-full">
                    <!--Contenido del plan-->
                    <div class="form-group">
                        <div class="title-form-group">
                            Datos del Fallecido y Traslado
                        </div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <div
                                    class="w-full xl:w-6/12 px-2 text-center input-text"
                                >
                                    <label class="">Origen de Solicitud</label>
                                    <div class="mt-3">
                                        <vs-radio
                                            vs-name="llamada_b"
                                            v-model="form.llamada_b"
                                            :vs-value="1"
                                            class="mr-4"
                                            >Por Llamada</vs-radio
                                        >
                                        <vs-radio
                                            vs-name="llamada_b"
                                            v-model="form.llamada_b"
                                            :vs-value="0"
                                            class="mr-4"
                                            >En Sucursal</vs-radio
                                        >
                                    </div>
                                </div>

                                <div class="w-full xl:w-6/12 px-2 input-text">
                                    <label>
                                        Fecha y Hora de Solicitud
                                        <span>(*)</span>
                                    </label>
                                    <flat-pickr
                                        name="fecha_solicitud"
                                        data-vv-as=" "
                                        v-validate:fecha_solicitud_validacion_computed.immediate="
                                            'required'
                                        "
                                        :config="configdateTimePickerWithTime"
                                        v-model="form.fecha_solicitud"
                                        placeholder="Fecha y Hora de Solicitud"
                                        class="w-full"
                                    />
                                    <span>{{
                                        errors.first("fecha_solicitud")
                                    }}</span>
                                    <span v-if="this.errores.fecha_solicitud">{{
                                        errores.fecha_solicitud[0]
                                    }}</span>
                                </div>

                                <div class="w-full px-2 input-text">
                                    <label>
                                        Nombre del Fallecido
                                        <span>(*)</span>
                                    </label>
                                    <vs-input
                                        v-validate.disabled="'required'"
                                        name="nombre_afectado"
                                        data-vv-as=" "
                                        type="text"
                                        class="w-full"
                                        placeholder="Nombre del Fallecido"
                                        v-model="form.nombre_afectado"
                                        maxlength="100"
                                        ref="fallecido"
                                    />
                                    <span>{{
                                        errors.first("nombre_afectado")
                                    }}</span>
                                    <span v-if="this.errores.nombre_afectado">{{
                                        errores.nombre_afectado[0]
                                    }}</span>
                                </div>

                                <div class="w-full xl:w-6/12 px-2 input-text">
                                    <label>
                                        Ubicación del Fallecido
                                        <span>(*)</span>
                                    </label>
                                    <vs-input
                                        v-validate.disabled="'required'"
                                        name="ubicacion_recoger"
                                        data-vv-as=" "
                                        type="text"
                                        class="w-full"
                                        placeholder="Ubicación del Fallecido"
                                        v-model="form.ubicacion_recoger"
                                        maxlength="100"
                                    />
                                    <span>{{
                                        errors.first("ubicacion_recoger")
                                    }}</span>
                                    <span
                                        v-if="this.errores.ubicacion_recoger"
                                        >{{
                                            errores.ubicacion_recoger[0]
                                        }}</span
                                    >
                                </div>
                                <div class="w-full xl:w-6/12 px-2 input-text">
                                    <label>
                                        Encargado de Recoger
                                        <span>(*)</span>
                                    </label>
                                    <v-select
                                        :options="recogioOpciones"
                                        :clearable="false"
                                        :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                        v-model="form.recogio"
                                        class="w-full"
                                        v-validate:recogio_validacion_computed.immediate="
                                            'required'
                                        "
                                        name="recogio"
                                        data-vv-as=" "
                                    >
                                        <div slot="no-options">
                                            Seleccione 1
                                        </div>
                                    </v-select>
                                    <span>{{ errors.first("recogio") }}</span>
                                    <span
                                        v-if="this.errores['recogio.value']"
                                        >{{ errores["recogio.value"][0] }}</span
                                    >
                                </div>

                                <div class="w-full xl:w-6/12 input-text px-2">
                                    <label>
                                        Nombre del Informante
                                        <span>(*)</span>
                                    </label>
                                    <vs-input
                                        v-validate.disabled="'required'"
                                        name="nombre_informante"
                                        data-vv-as=" "
                                        type="text"
                                        class="w-full"
                                        placeholder="Nombre del Informante"
                                        v-model="form.nombre_informante"
                                        maxlength="100"
                                    />
                                    <span>{{
                                        errors.first("nombre_informante")
                                    }}</span>
                                    <span
                                        v-if="this.errores.nombre_informante"
                                        >{{
                                            errores.nombre_informante[0]
                                        }}</span
                                    >
                                </div>

                                <div
                                    class="w-full sm:w-6/12 xl:w-3/12 input-text px-2"
                                >
                                    <label> Teléfono del Informante </label>
                                    <vs-input
                                        name="telefono_informante"
                                        type="text"
                                        class="w-full"
                                        placeholder="Teléfono del Informante"
                                        v-model="form.telefono_informante"
                                        :disabled="fueCancelada"
                                        maxlength="15"
                                    />
                                    <span>{{
                                        errors.first("telefono_informante")
                                    }}</span>
                                    <span
                                        v-if="this.errores.telefono_informante"
                                        >{{
                                            errores.telefono_informante[0]
                                        }}</span
                                    >
                                </div>

                                <div
                                    class="w-full sm:w-6/12 xl:w-3/12 input-text px-2"
                                >
                                    <label> Parentesco con el Fallecido </label>
                                    <vs-input
                                        name="parentesco_informante"
                                        type="text"
                                        class="w-full"
                                        placeholder="Parentesco con el Fallecido"
                                        v-model="form.parentesco_informante"
                                        maxlength="100"
                                    />
                                    <span>{{
                                        errors.first("parentesco_informante")
                                    }}</span>
                                    <span
                                        v-if="
                                            this.errores.parentesco_informante
                                        "
                                        >{{
                                            errores.parentesco_informante[0]
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="title-form-group">
                            Datos del Contratante
                        </div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap pb-6">
                                <div class="w-full xl:w-6/12 input-text px-2">
                                    <label>
                                        Nombre del Contratante
                                        <span>(*)</span>
                                    </label>
                                    <vs-input
                                        v-validate.disabled="'required'"
                                        name="nombre_contratante_temp"
                                        data-vv-as=" "
                                        type="text"
                                        class="w-full"
                                        placeholder="Nombre del Contratante"
                                        v-model="form.nombre_contratante_temp"
                                        maxlength="100"
                                    />
                                    <span>{{
                                        errors.first("nombre_contratante_temp")
                                    }}</span>
                                    <span
                                        v-if="
                                            this.errores.nombre_contratante_temp
                                        "
                                        >{{
                                            errores.nombre_contratante_temp[0]
                                        }}</span
                                    >
                                </div>

                                <div
                                    class="w-full sm:w-6/12 xl:w-3/12 input-text px-2"
                                >
                                    <label> Teléfono del Contratante </label>
                                    <vs-input
                                        name="telefono_contratante_temp"
                                        type="text"
                                        class="w-full"
                                        placeholder="Teléfono del Informante"
                                        v-model="form.telefono_contratante_temp"
                                        :disabled="fueCancelada"
                                        maxlength="15"
                                    />
                                    <span>{{
                                        errors.first(
                                            "telefono_contratante_temp"
                                        )
                                    }}</span>
                                    <span
                                        v-if="
                                            this.errores
                                                .telefono_contratante_temp
                                        "
                                        >{{
                                            errores.telefono_contratante_temp[0]
                                        }}</span
                                    >
                                </div>

                                <div
                                    class="w-full sm:w-6/12 xl:w-3/12 input-text px-2"
                                >
                                    <label> Parentesco con el Fallecido </label>
                                    <vs-input
                                        name="parentesco_contratante_temp"
                                        type="text"
                                        class="w-full"
                                        placeholder="Parentesco con el Fallecido"
                                        v-model="
                                            form.parentesco_contratante_temp
                                        "
                                        maxlength="100"
                                    />
                                    <span>{{
                                        errors.first(
                                            "parentesco_contratante_temp"
                                        )
                                    }}</span>
                                    <span
                                        v-if="
                                            this.errores
                                                .parentesco_contratante_temp
                                        "
                                        >{{
                                            errores
                                                .parentesco_contratante_temp[0]
                                        }}</span
                                    >
                                </div>
                                <div class="w-full input-text px-2">
                                    <label> Dirección del contratante </label>
                                    <vs-input
                                        name="direccion_contratante_temp"
                                        type="text"
                                        class="w-full"
                                        placeholder="Dirección del contratante"
                                        v-model="
                                            form.direccion_contratante_temp
                                        "
                                        maxlength="100"
                                    />
                                    <span>{{
                                        errors.first(
                                            "direccion_contratante_temp"
                                        )
                                    }}</span>
                                    <span
                                        v-if="
                                            this.errores
                                                .direccion_contratante_temp
                                        "
                                        >{{
                                            errores
                                                .direccion_contratante_temp[0]
                                        }}</span
                                    >
                                </div>

                                <div class="w-full input-text py-6 px-2">
                                    <label>
                                        Ingrese alguna nota o comentario
                                    </label>
                                    <vs-textarea
                                        :rows="5"
                                        ref="nota_al_recoger"
                                        type="text"
                                        class="w-full"
                                        v-model.trim="form.nota_al_recoger"
                                    />
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
                    <vs-button
                        v-if="!fueCancelada"
                        class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0"
                        color="primary"
                        @click="acceptAlert()"
                    >
                        <span
                            class=""
                            v-if="this.getTipoformulario == 'agregar'"
                            >Guardar Solicitud</span
                        >
                        <span class="" v-else>Modificar Solicitud</span>
                    </vs-button>
                </div>
            </div>

            <!--fin solicitud-->
        </vs-popup>
        <Password
            v-if="openPassword"
            :show="openPassword"
            :z_index="'z-index59k'"
            :callback-on-success="callback"
            @closeVerificar="closePassword"
            :accion="accionNombre"
        ></Password>
        <ConfirmarDanger
            v-if="openConfirmarSinPassword"
            :z_index="'z-index59k'"
            :show="openConfirmarSinPassword"
            :callback-on-success="callBackConfirmar"
            @closeVerificar="openConfirmarSinPassword = false"
            :accion="accionConfirmarSinPassword"
            :confirmarButton="botonConfirmarSinPassword"
        ></ConfirmarDanger>

        <ConfirmarAceptar
            v-if="openConfirmarAceptar"
            :z_index="'z-index58k'"
            :show="openConfirmarAceptar"
            :callback-on-success="callBackConfirmarAceptar"
            @closeVerificar="openConfirmarAceptar = false"
            :accion="'He revisado la información y quiero guardar la solicitud'"
            :confirmarButton="'Guardar Solicitud'"
        >
        </ConfirmarAceptar>
    </div>
</template>
<script>
/**date picker */
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import funeraria from "@services/funeraria";
import vSelect from "vue-select";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
import ClientesBuscador from "@pages/clientes/searcher.vue";

/**VARIABLES GLOBALES */
import { alfabeto, configdateTimePickerWithTime } from "@/VariablesGlobales";

export default {
    name: "FormularioSolicitud",
    components: {
        "v-select": vSelect,
        flatPickr,
        Password,
        ConfirmarDanger,
        ConfirmarAceptar,
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        //para saber que tipo de formulario es
        tipo: {
            type: String,
            required: true,
        },
        id_solicitud: {
            type: Number,
            required: false,
            default: 0,
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
                    await this.get_personal_recoger();
                    if (this.getTipoformulario == "agregar") {
                        /**acciones cuando el formulario es de agregar */
                    } else {
                        /**es modificar */
                        /**pasando el valor de la solicitud id */
                        this.form.id_solicitud = this.get_solicitud_id;
                        /**se cargan los datos al formulario */
                        await this.get_solicitudes_servicios();
                    }
                    this.$popupManager.register(
                        this,
                        this.cancelar,
                        "fallecido"
                    );
                } else {
                    this.$popupManager.unregister(this.$options.name);
                }
                this.localShow = newValue;
            },
        },
    },
    computed: {
        /**validar si es modificar el formulario */
        getTipoformulario: {
            get() {
                return this.tipo;
            },
            set(newValue) {
                return newValue;
            },
        },
        get_solicitud_id: {
            get() {
                return this.id_solicitud;
            },
            set(newValue) {
                return newValue;
            },
        },
        recogio_validacion_computed: function () {
            return this.form.recogio.value;
        },
        fecha_solicitud_validacion_computed: function () {
            return this.form.fecha_solicitud;
        },
        //fin de validaciones calculadas
    },
    data() {
        return {
            localShow: false,
            /**variables dle modulo */
            configdateTimePickerWithTime: configdateTimePickerWithTime,
            verDisponibilidad: false,
            accionConfirmarSinPassword: "",
            botonConfirmarSinPassword: "",
            alfabeto: alfabeto,
            openPassword: false,
            openConfirmarSinPassword: false,
            callback: Function,
            callBackConfirmar: Function,
            openConfirmarAceptar: false,
            callBackConfirmarAceptar: Function,
            accionNombre: "Modificar Solicitud",
            opciones: [
                {
                    label: "SI",
                    value: 1,
                },
                {
                    label: "NO",
                    value: 0,
                },
            ],
            recogioOpciones: [],
            /**para modificar, se traen los datos aqui */
            datos_solicitud: [],
            //fin var con mapa
            form: {
                /**varaibles del modulo */
                llamada_b: 1,
                nombre_afectado: "",
                fecha_solicitud: "",
                nombre_informante: "",
                telefono_informante: "",
                parentesco_informante: "",
                ubicacion_recoger: "",
                recogio: {
                    label: "Seleccione 1",
                    value: "",
                },

                nombre_contratante_temp: "",
                telefono_contratante_temp: "",
                parentesco_contratante_temp: "",
                direccion_contratante_temp: "",
                nota_al_recoger: "",
                /**en caso de modificar*/
                id_solicitud: "",
            },
            errores: [],
        };
    },
    methods: {
        acceptAlert() {
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
                        /**aqui se hace la validacion en los totales de la solicitud */
                        //se confirma la cntraseña
                        /**actualizando los valores de total de solicitud */
                        //fin de actualizar datos de ubicacion
                        (async () => {
                            if (this.getTipoformulario == "agregar") {
                                this.callBackConfirmarAceptar = await this
                                    .guardar_solicitud;
                                this.openConfirmarAceptar = true;
                            } else {
                                /**es modificacion */
                                this.callback = await this.modificar_solicitud;
                                this.openPassword = true;
                            }
                        })();
                    }
                })
                .catch(() => {});
        },

        async guardar_solicitud() {
            //aqui mando guardar los datos
            this.errores = [];
            this.$vs.loading();
            try {
                let res = await funeraria.guardar_solicitud(this.form);
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Servicios Funerarios",
                        text: "Se ha guardado la solicitud correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                    this.$emit("ver_pdfs_nueva_solicitud", res.data);
                    this.cerrarVentana();
                } else {
                    this.$vs.notify({
                        title: "Servicios Funerarios",
                        text: "Error al guardar la solicitud, por favor reintente.",
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
                    } else if (err.response.status == 422) {
                        //checo si existe cada error
                        this.errores = err.response.data.error;
                        this.$vs.notify({
                            title: "Guardar Solicitud",
                            text: "Verifique los errores encontrados en los datos.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 5000,
                        });
                    } else if (err.response.status == 409) {
                        /**FORBIDDEN ERROR */
                        this.$vs.notify({
                            title: "Guardar Solicitud",
                            text: err.response.data.error,
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 15000,
                        });
                    }
                }
                this.$vs.loading.close();
            }
        },

        async modificar_solicitud() {
            //aqui mando guardar los datos
            this.errores = [];
            this.$vs.loading();
            try {
                let res = await funeraria.modificar_solicitud(this.form);
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Servicios Funerarios",
                        text: "Se ha modificado la solicitud correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                    this.$emit("ver_pdfs_nueva_solicitud", res.data);
                    this.cerrarVentana();
                } else {
                    this.$vs.notify({
                        title: "Servicios Funerarios",
                        text: "Error al modificar la solicitud, por favor reintente.",
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
                    } else if (err.response.status == 422) {
                        //checo si existe cada error
                        this.errores = err.response.data.error;

                        this.$vs.notify({
                            title: "Modificar Solicitud",
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
                            title: "Modificar información de la solicitud",
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
        cancel() {
            this.$emit("closeVentana");
        },

        //get vendedores
        async get_personal_recoger() {
            try {
                let res = await funeraria.get_personal_recoger();
                //le agrego todos los usuarios vendedores
                this.recogioOpciones = [];
                this.recogioOpciones.push({ label: "Seleccione 1", value: "" });
                if (this.getTipoformulario == "agregar") {
                    this.form.recogio = this.recogioOpciones[0];
                }
                res.data.forEach((element) => {
                    this.recogioOpciones.push({
                        label: element.nombre,
                        value: element.id,
                    });
                });
            } catch (error) {
                /**error al cargar vendedores */
                this.$vs.notify({
                    title: "Error",
                    text: "Ha ocurrido un error al tratar de cargar el catálogo de personal, por favor reintente.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                    position: "bottom-right",
                    time: "9000",
                });
                this.cerrarVentana();
            }
        },

        cancelar() {
            if (this.getTipoformulario !== "agregar") {
                this.botonConfirmarSinPassword = "Salir y limpiar";
                this.accionConfirmarSinPassword =
                    "Esta acción limpiará los datos que capturó en el formulario.";
                this.openConfirmarSinPassword = true;
                this.callBackConfirmar = this.cerrarVentana;
            } else {
                this.$emit("closeVentana");
            }
        },

        cerrarVentana() {
            this.openConfirmarSinPassword = false;
            this.limpiarVentana();
            this.$emit("closeVentana");
        },
        //regresa los datos a su estado inicial
        limpiarVentana() {
            this.form.llamada_b = 1;
            this.form.nombre_afectado = "";
            this.form.fecha_solicitud = "";
            this.form.nombre_informante = "";
            this.form.telefono_informante = "";
            this.form.parentesco_informante = "";
            this.form.ubicacion_recoger = "";
            this.form.recogio = {
                label: "Seleccione 1",
                value: "",
            };
            this.form.nombre_contratante_temp = "";
            this.form.telefono_contratante_temp = "";
            this.form.parentesco_contratante_temp = "";
            this.form.direccion_contratante_temp = "";
            this.form.nota_al_recoger = "";
            /**en caso de modificar*/
            this.form.id_solicitud = "";
        },

        closePassword() {
            this.openPassword = false;
        },

        async get_solicitudes_servicios() {
            try {
                this.$vs.loading();
                let res = await funeraria.get_solicitudes_servicios_id(
                    this.form.id_solicitud
                );
                this.datos_solicitud = res.data[0];
                /**llenando datos */
                this.form.llamada_b = this.datos_solicitud.llamada_b;
                this.form.nombre_afectado =
                    this.datos_solicitud.nombre_afectado;
                /**fecha de la solicitud */
                var partes_fecha =
                    this.datos_solicitud.fecha_solicitud.split("-");
                var partes_hora =
                    this.datos_solicitud.hora_solicitud.split(":");
                //yyyy-mm-dd
                this.form.fecha_solicitud = new Date(
                    partes_fecha[0],
                    partes_fecha[1] - 1,
                    partes_fecha[2],
                    partes_hora[0],
                    partes_hora[1]
                );

                this.form.nombre_informante =
                    this.datos_solicitud.nombre_informante;
                this.form.telefono_informante =
                    this.datos_solicitud.telefono_informante;
                this.form.parentesco_informante =
                    this.datos_solicitud.parentesco_informante;

                this.form.direccion_contratante_temp =
                    this.datos_solicitud.direccion_contratante_temp;
                this.form.parentesco_contratante_temp =
                    this.datos_solicitud.parentesco_contratante_temp;
                this.form.telefono_contratante_temp =
                    this.datos_solicitud.telefono_contratante_temp;
                this.form.nombre_contratante_temp =
                    this.datos_solicitud.nombre_contratante_temp;

                this.form.ubicacion_recoger =
                    this.datos_solicitud.ubicacion_recoger;
                this.form.nota_al_recoger =
                    this.datos_solicitud.nota_al_recoger;
                this.recogioOpciones.forEach((element) => {
                    if (element.value == this.datos_solicitud.recogio_id) {
                        this.form.recogio = element;
                        return;
                    }
                });

                this.$vs.loading.close();
            } catch (err) {
                this.$vs.loading.close();
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
                    }
                }
            }
        },

        limpiarValidation() {
            this.$validator.pause();
            this.$nextTick(() => {
                this.$validator.errors.clear();
                this.$validator.fields.items.forEach((field) => field.reset());
                this.$validator.fields.items.forEach((field) =>
                    this.errors.remove(field)
                );
                this.$validator.resume();
            });
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
