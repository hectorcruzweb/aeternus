<!-- =========================================================================================
  File Name: Profile.vue
  Description: Profile Page
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <div id="profile-page">
        <!-- PROFILE HEADER -->
        <div class="profile-header">
            <div class="relative">
                <div class="cover-container rounded-t-lg">
                    <div class="layer"></div>
                    <img src="@assets/images/profile/user-uploads/cover.jpg" alt="user-profile-cover"
                        class="responsive block" />
                </div>
                <div class="profile-img-container pointer-events-none" style="z-index: 1000 !important">
                    <input ref="fileImage" type="file" name="fileToUpload" id="fileToUpload" class="hidden"
                        accept="image/jpeg" @change="display" />

                    <div class="con-img ml-3" v-if="this.form.imagen">
                        <vs-avatar class="user-profile-img" :src="form.imagen" size="85px" />
                    </div>
                    <div class="con-img ml-3" v-else-if="this.activeUserInfo.imagen">
                        <vs-avatar class="user-profile-img" :src="activeUserInfo.imagen" size="85px" />
                    </div>
                    <div class="con-img ml-3" v-else>
                        <vs-avatar class="user-profile-img"
                            :src="require('@assets/images/portrait/small/default-user.jpg')" size="85px" />
                    </div>
                </div>
            </div>

            <!-- <vx-navbar> -->
            <!-- </vx-navbar> -->
        </div>

        <!-- COL AREA -->
        <div class="vx-row mt-10">
            <!-- COL 1 -->
            <div class="vx-col w-full lg:w-1/4">
                <!-- ABOUT CARD -->
                <vx-card title="Mi Perfíl" class="mt-base">
                    <!-- ACTION SLOT -->
                    <template slot="actions">
                        <span class="text-primary cursor-pointer mt-4" @click="imagen()" v-if="!this.form.imagen">
                            <feather-icon icon="CameraIcon" class="mr-2" svgClasses="w-5 h-5" />Seleccionar imagen
                            200x200px recomendado.
                        </span>

                        <span class="text-danger cursor-pointer" @click="form.imagen = ''" v-else>
                            <feather-icon icon="CameraOffIcon" class="mr-2" svgClasses="w-5 h-5" />Cancelar
                        </span>
                    </template>
                    <!-- OTEHR DATA -->

                    <div>
                        <h6>Rol de Usuario:</h6>
                        <p class="mt-1">{{ activeUserInfo.rol }}</p>
                    </div>

                    <div class="mt-5">
                        <h6>Nombre:</h6>
                        <p class="mt-1">{{ activeUserInfo.nombre }}</p>
                    </div>

                    <div class="mt-5">
                        <h6>Usuario:</h6>
                        <p class="mt-1">{{ activeUserInfo.email }}</p>
                    </div>

                    <div class="mt-5">
                        <h6>Género:</h6>
                        <p class="mt-1">{{ activeUserInfo.genero_des }}</p>
                    </div>

                    <div class="mt-5">
                        <h6>Teléfono:</h6>
                        <p class="mt-1">{{ activeUserInfo.telefono }}</p>
                    </div>
                </vx-card>

                <!-- PAGES SUGGESTION -->

                <!-- TWITER FEEDS -->
            </div>
            <!-- COL 2 -->
            <div class="vx-col w-full lg:w-3/4 mt-10">
                <div class="flex flex-wrap">
                    <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mb-4">
                        <vs-card class="cardx card-tarifas" fixedHeight>
                            <div slot="header">
                                <h3>Perfíl de Usuario</h3>
                            </div>
                            <div class="mt-3">
                                <div class="flex flex-wrap">
                                    <div class="w-full pb-5 px-2">
                                        <h3 class="text-xl">
                                            <feather-icon icon="EditIcon" class="mr-2" svgClasses="w-5 h-5" />Cambiar
                                            imagen de
                                            perfíl y contraseña
                                        </h3>
                                    </div>
                                    <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                                        <label class="text-sm opacity-75">
                                            Nueva contraseña
                                            <span class="text-danger text-sm">(*)</span>
                                        </label>
                                        <vs-input ref="password" name="password" data-vv-as=" " v-validate
                                            maxlength="75" type="password" class="w-full pb-1 pt-1"
                                            placeholder="Ingrese la nueva contraseña" v-model="form.password" />
                                        <div>
                                            <span class="text-danger text-sm">{{
                                                errors.first("password")
                                                }}</span>
                                        </div>
                                        <div class="mt-2">
                                            <span class="text-danger text-sm" v-if="this.errores.password">{{
                                                errores.password[0]
                                                }}</span>
                                        </div>
                                    </div>

                                    <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                                        <label class="text-sm opacity-75">
                                            Repetir nueva contraseña
                                            <span class="text-danger text-sm">(*)</span>
                                        </label>
                                        <vs-input name="repetirPassword" data-vv-as=" " v-validate="validar_confirmar"
                                            maxlength="75" type="password" class="w-full pb-1 pt-1"
                                            placeholder="Repita la contraseña" v-model="form.repetirPassword" />
                                        <div>
                                            <span class="text-danger text-sm">{{
                                                errors.first("repetirPassword")
                                                }}</span>
                                        </div>
                                        <div class="mt-2">
                                            <span class="text-danger text-sm" v-if="this.errores.repetirPassword">{{
                                                errores.repetirPassword[0] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="flex flex-wrap mt-12">
                                    <div class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 px-2">
                                        <p class="text-sm">
                                            <span class="text-danger font-medium">Ojo:</span>
                                            Desde este apartado solo puede actualizar su imagen de
                                            perfíl y contraseña. Si necesita actualizar información
                                            personal debe solicitarlo a la gerencia.
                                        </p>
                                    </div>
                                    <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                                        <vs-button size="small" class="ml-auto" color="success"
                                            icon="add_circle_outline" @click="mandarModificar">Actualizar</vs-button>
                                    </div>
                                </div>
                            </div>
                        </vs-card>
                    </div>
                </div>
            </div>
        </div>
        <Password :show="operConfirmar" :callback-on-success="ActualizarDatos" @closeVerificar="operConfirmar = false"
            :accion="'Actualizar perfil'"></Password>
    </div>
</template>

<script>
import Password from "../pages/confirmar_password";
import usuarios from "@services/Usuarios";
import store from "@/store/store";
export default {
    components: {
        Password,
    },
    data() {
        return {
            activeUserInfo: {},
            errores: [],
            operConfirmar: false,
            form: {
                imagen: "",
                password: "",
                repetirPassword: "",
            },
        };
    },
    computed: {
        validar_confirmar: function () {
            if (this.form.password != "") {
                return "required|confirmed:password";
            } else return "";
        },
    },
    methods: {
        imagen() {
            this.$refs.fileImage.click();
        },
        display: function (event) {
            // Reference to the DOM input element
            var input = event.target;
            // Ensure that you have a file before attempting to read it
            if (input.files && input.files[0]) {
                if (input.files[0].type != "image/jpeg") {
                    this.$vs.notify({
                        title: "Actualizar imagen",
                        text: "Error, debe seleccionar una imagen .jpeg.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 4000,
                    });
                    return;
                }
                // create a new FileReader to read this image and convert to base64 format
                var reader = new FileReader();
                // Define a callback function to run, when FileReader finishes its job
                reader.onload = (e) => {
                    // Note: arrow function used here, so that "this.imageData" refers to the imageData of Vue component
                    // Read image as base64 and set to imageData
                    this.form.imagen = e.target.result;
                };
                // Start the reader job - read file as a data url (base64 format)
                reader.readAsDataURL(input.files[0]);
            }
        },
        mandarModificar() {
            this.$validator
                .validateAll()
                .then((result) => {
                    if (!result) {
                        this.$vs.notify({
                            title: "Actualizar Datos",
                            text: "Error, verifique que ingresó todos los datos.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 4000,
                        });
                        return;
                    } else {
                        //verificando que haya algo que modificar
                        if (this.form.imagen != "" || this.form.password != "") {
                            //manda modificar
                            this.operConfirmar = true;
                        } else {
                            //no hay nada que modificar
                            this.$vs.notify({
                                title: "Actualizar Datos",
                                text: "Error, no ha ingresado ningún dato para actualizar.",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "danger",
                                time: 4000,
                            });
                        }

                        //se manda el emit al parent para guardar datps
                        //this.$emit("actualizar", this.form);
                    }
                })
                .catch(() => { });
        },
        ActualizarDatos() {
            this.$vs.loading();
            usuarios
                .actualizar_perfil(this.form)
                .then((res) => {
                    if (res.data >= 0) {
                        //success
                        this.$vs.notify({
                            title: "Actualizar Datos",
                            text: "Se han guardado los cambios correctamente.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "success",
                            time: 5000,
                        });

                        setTimeout(function () {
                            localStorage.removeItem("userInfo");
                            location.reload();
                        }, 300);
                    } else {
                        this.$vs.notify({
                            title: "Actualizar Datos",
                            text: "Error al guardar cambios, por favor reintente.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 4000,
                        });
                    }

                    this.$vs.loading.close();
                })
                .catch((err) => {
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
                            //checo si existe cada error
                            this.errores = err.response.data.error;
                        }
                    }
                    this.$vs.loading.close();
                });
        },
    },
    mounted() {
        //this.$store.commit("TOGGLE_REDUCE_BUTTON", true);
        if (!localStorage.getItem("userInfo")) {
            this.$store.dispatch("auth/user_datos").then((resp) => {
                this.activeUserInfo = JSON.parse(localStorage.getItem("userInfo"));
            });
        } else {
            this.activeUserInfo = JSON.parse(localStorage.getItem("userInfo"));
        }
    },
    beforeDestroy() {
        //this.$store.commit("TOGGLE_REDUCE_BUTTON", false);
    },
};
</script>


<style lang="scss">
@import "@sass/vuexy/pages/profile.scss";
</style>
