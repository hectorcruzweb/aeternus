<template>
    <div class="centerx">
        <vs-popup class="forms-popup popup-80 bg-content-theme" close="cancel" :title="title" :ref="this.$options.name"
            :active="localShow" ref="usuarios">
            <div>
                <vs-tabs alignment="left" position="top" v-model="activeTab">
                    <vs-tab label="DATOS DEL EMPLEADO / USUARIO" class=""></vs-tab>
                    <vs-tab label="REGISTRO DE FIRMA"></vs-tab>
                    <!--<vs-tab label="FACTURACIÓN" icon="fingerprint"></vs-tab>-->
                </vs-tabs>
                <div class="tab-content" v-show="activeTab == 0">
                    <div class="flex flex-wrap">
                        <div class="w-full py-4">
                            <!--Datos del usuario-->
                            <div class="form-group">
                                <div class="title-form-group">
                                    <span>Información del Usuario</span>
                                </div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div class="w-full xl:w-4/12 px-2 input-text">
                                            <label class="">
                                                Nombre
                                                <span class="">(*)</span>
                                            </label>
                                            <vs-input ref="nombre" name="Nombre" data-vv-validate-on="blur"
                                                v-validate="'required'" type="text" class="w-full"
                                                placeholder="Ingrese el nombre del usuario" v-model="form.nombre" />
                                            <span class="">{{ errors.first("Nombre") }}</span>
                                            <span class="" v-if="this.errores.nombre">{{
                                                errores.nombre[0]
                                                }}</span>
                                        </div>
                                        <div class="w-full md:w-6/12 xl:w-4/12 px-2 input-text">
                                            <label class="">
                                                Rol
                                                <span class="">(*)</span>
                                            </label>
                                            <v-select :options="rolesOptions" :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="roles" class="w-full" />
                                            <span class="" v-if="this.errores.rol_id">{{
                                                errores.rol_id[0]
                                                }}</span>
                                        </div>
                                        <div class="w-full md:w-6/12 xl:w-2/12 px-2 input-text">
                                            <label class="">
                                                Género
                                                <span class="">(*)</span>
                                            </label>
                                            <v-select :options="generosOptions" :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="genero" class="w-full" />
                                            <span class="" v-if="this.errores.genero">{{
                                                errores.genero[0]
                                                }}</span>
                                        </div>
                                        <div class="w-full md:w-6/12 xl:w-2/12 px-2 input-text">
                                            <label class="">
                                                Área
                                                <span class="">(*)</span>
                                            </label>
                                            <v-select :options="areasOptions" :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="area" class="w-full" />
                                            <span class="" v-if="this.errores.area">{{
                                                errores.area[0]
                                                }}</span>
                                        </div>

                                        <div class="w-full lg:w-12/12 xl:w-4/12 px-2 input-text">
                                            <label class="">
                                                Usuario (email)
                                                <span class="">(*)</span>
                                            </label>
                                            <vs-input name="Usuario (email)" data-vv-validate-on="blur"
                                                v-validate="'required|email'" type="email" class="w-full"
                                                placeholder="Correo electrónico del usuario" v-model="form.usuario" />

                                            <span class="">{{
                                                errors.first("Usuario (email)")
                                                }}</span>

                                            <span class="" v-if="this.errores.usuario">{{
                                                errores.usuario[0]
                                                }}</span>
                                        </div>
                                        <div class="w-full md:w-6/12 lg:w-6/12 xl:w-4/12 px-2 input-text">
                                            <label class="">
                                                Password
                                                <span class="">(*)</span>
                                            </label>
                                            <vs-input data-vv-validate-on="blur" v-validate="'required'" name="Password"
                                                type="password" class="w-full" placeholder="Contraseña del usuario"
                                                v-model="form.password" />

                                            <span class="">{{ errors.first("Password") }}</span>

                                            <span class="" v-if="this.errores.password">{{
                                                errores.password[0]
                                                }}</span>
                                        </div>
                                        <div class="w-full md:w-6/12 lg:w-6/12 xl:w-4/12 px-2 input-text">
                                            <label class="">
                                                Repetir Password
                                                <span class="">(*)</span>
                                            </label>
                                            <vs-input data-vv-validate-on="blur" v-validate="'required'"
                                                name="Repetir Password" type="password" class="w-full"
                                                placeholder="Repita la contraseña" v-model="form.repetir" />

                                            <span class="">{{
                                                errors.first("Repetir Password")
                                                }}</span>

                                            <span class="" v-if="this.errores.repetir">{{
                                                errores.repetir[0]
                                                }}</span>
                                        </div>

                                        <div class="w-full lg:w-12/12 xl:w-4/12 px-2 input-text">
                                            <label class="">Dirección</label>
                                            <vs-input type="text" class="w-full"
                                                placeholder="Ingrese el nombre del usuario" v-model="form.direccion" />
                                        </div>
                                        <div class="w-full md:w-6/12 lg:w-6/12 xl:w-4/12 px-2 input-text">
                                            <label class="">Teléfono</label>
                                            <vs-input type="text" class="w-full"
                                                placeholder="Ingrese el nombre del usuario" v-model="form.telefono" />
                                        </div>
                                        <div class="w-full md:w-6/12 lg:w-6/12 xl:w-4/12 px-2 input-text">
                                            <label class="">Celular</label>
                                            <vs-input type="text" class="w-full"
                                                placeholder="Ingrese el nombre del usuario" v-model="form.celular" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Datos del usuario-->

                            <!--Datos del area de trabajo-->
                            <div class="form-group">
                                <div class="title-form-group">
                                    <span>Responsabilidades de este usuario</span>
                                </div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div class="w-full md:w-4/12 lg:w-4/12 xl:w-4/12 px-2 input-text"
                                            v-for="puesto in puestos" :key="puesto.id">
                                            <vs-checkbox class="theme-background p-2" v-model="form.puestos"
                                                :vs-value="puesto.id">
                                                <label class="size-small color-copy font-medium">{{
                                                    puesto.puesto
                                                    }}</label>
                                            </vs-checkbox>
                                        </div>
                                        <div class="w-full px-2 mt-2">
                                            <span class="" v-if="this.errores.puestos">{{
                                                errores.puestos[0]
                                                }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Datos del area de trabajo-->

                            <!--Datos del contacto-->
                            <div class="form-group">
                                <div class="title-form-group">
                                    <span>Información de un Contacto</span>
                                </div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div class="w-full lg:w-12/12 xl:w-4/12 px-2 input-text">
                                            <label class="">Nombre de un contacto</label>
                                            <vs-input type="text" class="w-full"
                                                placeholder="Ingrese el nombre del usuario"
                                                v-model="form.nombre_contacto" />
                                        </div>
                                        <div class="w-full md:w-6/12 lg:w-6/12 xl:w-4/12 px-2 input-text">
                                            <label class="">Teléfono del contacto</label>
                                            <vs-input type="text" class="w-full"
                                                placeholder="Ingrese el nombre del usuario"
                                                v-model="form.tel_contacto" />
                                        </div>
                                        <div class="w-full md:w-6/12 lg:w-6/12 xl:w-4/12 px-2 input-text">
                                            <label class="">Parentesco</label>
                                            <vs-input type="text" class="w-full"
                                                placeholder="Ingrese el nombre del usuario"
                                                v-model="form.parentesco_contacto" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Datos del contacto-->
                        </div>
                    </div>
                </div>
                <div class="tab-content" v-show="activeTab == 1">
                    <div class="flex flex-wrap">
                        <div class="w-full py-4">
                            <!--Datos del usuario-->
                            <div class="form-group">
                                <div class="title-form-group">
                                    <span>Digitalización de Firma Manuscrita</span>
                                </div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div class="w-full">
                                            <div class="signature">
                                                <h3 class="mt-6">Registro de Firma Manuscrita</h3>
                                                <div class="firma" v-show="!form.firma_registrada">
                                                    <VueSignaturePad ref="signaturePad" width="400px" height="200px" />
                                                </div>
                                                <div v-show="form.firma_registrada" class="firma">
                                                    <img :src="form.firma_path" width="400px" height="200px" alt="" />
                                                </div>
                                                <p :class="['color-danger-900']" v-if="form.firma_registrada">
                                                    La firma de este usuario ya ha sido registrada
                                                </p>
                                                <p :class="['color-copy']" v-else>
                                                    Firme dentro de esta área
                                                </p>
                                            </div>
                                        </div>
                                        <div class="w-full text-center mt-6" v-if="!form.firma_registrada">
                                            <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
                                                color="danger" @click="undo">
                                                <span>Limpiar</span>
                                            </vs-button>
                                            <vs-button
                                                class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0 hidden"
                                                color="success">
                                                <span>Guardar Firma</span>
                                            </vs-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Datos del usuario-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom-buttons-section">
                <div class="text-advice">
                    <span class="ojo-advice">Ojo:</span>
                    Por favor revise la información ingresada, si todo es correcto de
                    click en el "Botón de Abajo”.
                </div>

                <div class="w-full">
                    <vs-button class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0" color="primary"
                        @click="acceptAlert()">
                        <span class="" v-if="this.getTipoformulario == 'agregar'">Crear Usuario</span>
                        <span class="" v-else>Modificar Usuario</span>
                    </vs-button>
                </div>
            </div>
        </vs-popup>
        <Password v-if="operConfirmar" :show="operConfirmar" :callback-on-success="callback"
            @closeVerificar="closeChecker" :accion="accionNombre"></Password>
        <ConfirmarDanger v-if="openConfirmarDanger" :show="openConfirmarDanger" :callback-on-success="callBackConfirmar"
            @closeVerificar="openConfirmarDanger = false" :accion="accionConfirmarDanger"
            :confirmarButton="botonConfirmarDanger"></ConfirmarDanger>
        <ConfirmarAceptar v-if="openConfirmarAceptar" :show="openConfirmarAceptar" :callback-on-success="callback"
            @closeVerificar="openConfirmarAceptar = false"
            :accion="'He revisado la información y quiero registrar a este usuario'"
            :confirmarButton="'Guardar Usuario'"></ConfirmarAceptar>
    </div>
</template>
<script>
//componente de password
import Password from "@pages/confirmar_password";
import usuarios from "@services/Usuarios";
import vSelect from "vue-select";
import ConfirmarDanger from "@pages/ConfirmarDanger";
/**VARIABLES GLOBALES */
import { generosOptions, areasOptions } from "@/VariablesGlobales";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";

export default {
    name: "formularioUsuarios",
    components: {
        "v-select": vSelect,
        Password,
        ConfirmarDanger,
        ConfirmarAceptar,
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        tipo: {
            type: String,
            required: true,
        },
        id_usuario: {
            type: Number,
            required: false,
            default: 0,
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newValue) {
                if (newValue) {
                    // Only listen when visible = true
                    this.get_roles();
                    /**get puestos de trabajo */
                    this.get_puestos();
                    if (this.getTipoformulario == "agregar") {
                        this.title = "Registrar Nuevo Usuario";
                    } else {
                        this.title = "Modificar Usuario";
                        /**se cargan los datos del usuario */
                        this.get_usuarioById(this.get_usuario_id);
                    }
                    this.$popupManager.register(
                        this,
                        this.cancel,
                        "nombre"
                    );
                    //verificamos el origen del form para determinar que haremos justo al abrir el form.
                    //obtener datos del cliente
                }
                this.localShow = newValue;
            },
        },
    },
    data() {
        return {
            localShow: false,
            error_message: false,
            openConfirmarAceptar: false,
            title: "",
            botonConfirmarDanger: "",
            openConfirmarDanger: false,
            callBackConfirmar: Function,
            accionConfirmarDanger: "",
            activeTab: 0,
            generosOptions: generosOptions,
            areasOptions: areasOptions,
            operConfirmar: false,
            callback: Function,
            accionNombre: "",
            roles: { label: "Seleccione 1", value: "" },
            rolesOptions: [],
            genero: generosOptions[0],
            area: areasOptions[0],
            puestos: [],
            form: {
                user_id: "",
                puestos: [],
                rol_id: "",
                nombre: "",
                usuario: "",
                password: "",
                repetir: "",
                genero: "",
                area: "",
                direccion: "",
                telefono: "",
                celular: "",
                nombre_contacto: "",
                tel_contacto: "",
                parentesco_contacto: "",
                firma_path: "",
                firma_registrada: false,
                firma: "",
            },
            errores: [],
        };
    },
    computed: {
        showVentana: {
            get() {
                return this.show;
            },
            set(newValue) {
                return newValue;
            },
        },
        getTipoformulario: {
            get() {
                return this.tipo;
            },
            set(newValue) {
                return newValue;
            },
        },
        get_usuario_id: {
            get() {
                return this.id_usuario;
            },
            set(newValue) {
                return newValue;
            },
        },
    },
    methods: {
        undo() {
            this.$refs.signaturePad.clearSignature();
        },
        save() {
            const { isEmpty, data } = this.$refs.signaturePad.saveSignature();
            if (!isEmpty) {
                if (!this.form.firma_registrada) {
                    this.form.firma = data;
                }
            }
            // this.$refs.signaturePad.lockSignaturePad();
        },
        resizeCanvas() {
            let canvas = this.$refs.signaturePad.$el.firstChild;

            var ratio = Math.max(window.devicePixelRatio || 1, 1);

            if (canvas.offsetWidth > 0) {
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                //canvas.getContext("2d").scale(ratio, ratio);
            } else {
                canvas.width = 400;
                canvas.height = 200;
            }
            //this.$refs.signaturePad.clearSignature(); // otherwise isEmpty() might return incorrect value
        },

        get_usuarioById(id_user) {
            this.$vs.loading();
            usuarios
                .get_usuarioById(id_user)
                .then((res) => {
                    this.$vs.loading.close();
                    this.roles = {
                        label: res.data[0].rol,
                        value: res.data[0].roles_id,
                    };
                    this.genero = {
                        label: res.data[0].genero_des,
                        value: res.data[0].genero,
                    };
                    this.area = {
                        label: res.data[0].area_des,
                        value: res.data[0].cementerio_funeraria_filtro,
                    };
                    this.form.user_id = res.data[0].id_user;
                    this.form.nombre = res.data[0].nombre;
                    this.form.usuario = res.data[0].email;
                    this.form.password = "nochanges";
                    this.form.repetir = "nochanges";
                    this.form.direccion = res.data[0].domicilio;
                    this.form.telefono = res.data[0].telefono;
                    this.form.celular = res.data[0].celular;
                    this.form.firma_path = res.data[0].firma_path;
                    this.form.firma_registrada = res.data[0].firma_registrada;

                    /*this.$refs.signaturePad.fromDataURL(this.form.firma_path);
                    const data = this.$refs.signaturePad.toData();
                    this.$refs.signaturePad.fromData(data);
                    */

                    this.form.nombre_contacto = res.data[0].nombre_contacto;
                    this.form.tel_contacto = res.data[0].tel_contacto;
                    this.form.parentesco_contacto = res.data[0].parentesco;
                    /**llenando los puestos */
                    res.data[0].puestos.forEach((element) => {
                        this.form.puestos.push(element.id);
                    });
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },

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
                            time: "4000",
                        });
                        return;
                    } else {
                        //se confirma la cntraseña
                        /**genero el base64 de la firma */
                        this.save();
                        if (this.getTipoformulario == "agregar") {
                            /**se manda llamar la funcion de agregar usuario */
                            this.accionNombre = "Registrar Nuevo Usuario";
                            this.openConfirmarAceptar = true;
                            this.callback = this.saveUsuario;
                        } else {
                            this.accionNombre = "Modificar Usuario";
                            this.callback = this.updateUsuario;
                            /**se manda agregar  la funcion de modificar */
                            this.operConfirmar = true;
                        }
                    }
                })
                .catch(() => { });
        },
        cancel() {
            if (this.getTipoformulario !== "agregar") {
                this.botonConfirmarDanger = "Salir y limpiar";
                this.accionConfirmarDanger =
                    "Esta acción limpiará los datos que capturó en el formulario.";
                this.openConfirmarDanger = true;
                this.callBackConfirmar = this.cerrarVentana;
            } else
                this.cerrarVentana();
        },
        cerrarVentana() {
            /*this.roles = { label: "Seleccione 1", value: "" };
            this.genero = { label: "Seleccione 1", value: "" };
            this.form.rol_id = "";
            this.form.nombre = "";
            this.form.usuario = "";
            this.form.password = "";
            this.form.repetir = "";
            this.form.direccion = "";
            this.form.telefono = "";
            this.form.celular = "";
            this.form.nombre_contacto = "";
            this.form.tel_contacto = "";
            this.form.parentesco_contacto = "";
            this.form.puestos = [];
            this.form.firma_path = "";
            this.form.firma = "";
            this.form.firma_registrada = false;
            this.undo();
            */
            this.$emit("closeVentana");
        },
        get_roles() {
            this.$vs.loading();
            usuarios
                .getRoles()
                .then((res) => {
                    //le agrego todos los roles
                    this.rolesOptions = [];
                    this.rolesOptions.push({ label: "Seleccione 1", value: "" });
                    res.data.data.forEach((element) => {
                        /**AGREGO LOS DEMAS ROLES */
                        this.rolesOptions.push(element);
                    });
                    this.$vs.loading.close();
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },
        get_puestos() {
            usuarios
                .get_puestos()
                .then((res) => {
                    //le agrego todos los roles
                    this.puestos = res.data;
                })
                .catch((err) => { });
        },
        //funcion que inserta el nuevo rol
        saveUsuario() {
            this.save();
            this.$vs.loading();
            //limpiando errores
            this.errores = [];
            this.form.rol_id = this.roles.value;
            this.form.genero = this.genero.value;
            this.form.area = this.area.value;
            usuarios
                .add_usuario(this.form)
                .then((res) => {
                    this.$vs.loading.close();
                    this.$vs.notify({
                        title: "Agregar Usuarios",
                        text: "Se ha creado el nuevo usuario exitosamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 4000,
                    });
                    this.$emit("get_data");
                    this.cerrarVentana();
                })
                .catch((err) => {
                    this.$vs.loading.close();
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
                                time: 4000,
                            });
                        } else if (err.response.status == 422) {
                            this.$vs.notify({
                                title: "Error",
                                text: "Verifique que todos los datos han sido capturados",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "danger",
                                position: "bottom-right",
                                time: "4000",
                            });
                            /**error de validacion */
                            this.errores = err.response.data.error;
                        }
                    }
                });
        },

        updateUsuario() {
            this.$vs.loading();
            //limpiando errores
            this.errores = [];
            this.form.rol_id = this.roles.value;
            this.form.genero = this.genero.value;
            this.form.area = this.area.value;
            usuarios
                .update_usuario(this.form)
                .then((res) => {
                    this.$vs.loading();
                    this.$vs.notify({
                        title: "Modificar Usuarios",
                        text: "Se ha modificado el usuario exitosamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 4000,
                    });
                    this.$emit("get_data");
                    this.cerrarVentana();
                })
                .catch((err) => {
                    this.$vs.loading.close();
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
                                time: 4000,
                            });
                        } else if (err.response.status == 422) {
                            /**error de validacion */
                            this.$vs.notify({
                                title: "Error",
                                text: "Verifique que todos los datos han sido capturados",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "danger",
                                position: "bottom-right",
                                time: "4000",
                            });
                            this.errores = err.response.data.error;
                        }
                    }
                });
        },

        closeChecker() {
            this.operConfirmar = false;
        },
    },

    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name);
        this.resizeCanvas();
    },
    beforeDestroy() {
        this.$popupManager.unregister(this.$options.name);
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
