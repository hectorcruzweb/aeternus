<!-- =========================================================================================
    File Name: ForgotPassword.vue
    Description: FOrgot Password Page
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->

<template>
    <div class="h-screen flex w-full bg-login-password-recovery">
        <div class="layer"></div>
        <div
            class="w-4/5 sm:w-4/5 md:w-4/5 lg:w-3/5 xl:w-3/5 mx-auto self-center"
        >
            <vx-card class="py-20 md:bg-danger-600">
                <div slot="no-body">
                    <div class="flex flex-wrap">
                        <div
                            class="w-full hidden sm:hidden md:hidden xl:block xl:w-5/12"
                        >
                            <div class="w-full">
                                <img
                                    src="@assets/images/pages/forgot-password.png"
                                    alt="login"
                                    class="mx-auto"
                                />
                            </div>
                        </div>
                        <div
                            class="w-full sm:w-full md:w-12/12 lg:w-12/12 xl:w-7/12"
                        >
                            <div class="px-8">
                                <div class="vx-card__title mb-4">
                                    <h4
                                        class="color-dark-800 h1 font-medium pb-2"
                                    >
                                        Recuperar su contraseña
                                    </h4>
                                    <p
                                        class="size-base font-normal normal-line"
                                    >
                                        Por favor ingrese su correo electrónico
                                        a donde enviaremos las instrucciones
                                        para recuperar su cuenta.
                                    </p>
                                </div>
                                <div class="sm:w-full pb-6">
                                    <form @submit.prevent="submitForm">
                                        <div class="w-full input-text">
                                            <label
                                                >Correo electrónico
                                                <span>(*)</span></label
                                            >
                                            <vs-input
                                                v-validate="
                                                    'required|email|min:3|max:50'
                                                "
                                                name="email"
                                                maxlength="50"
                                                icon-no-border
                                                data-vv-as=" "
                                                icon="icon icon-user"
                                                icon-pack="feather"
                                                placeholder="Correo electrónico"
                                                v-model="email"
                                                class="w-full py-1"
                                                ref="email"
                                            />
                                            <span>{{
                                                errors.first("email")
                                            }}</span>
                                        </div>
                                    </form>
                                </div>

                                <vs-button
                                    type="border"
                                    to="/pages/login"
                                    class="px-4 w-full md:w-auto"
                                    >Regresar</vs-button
                                >
                                <vs-button
                                    class="float-right px-4 w-full md:w-auto mt-4 mb-8 md:mt-0 md:mb-0"
                                    @click.prevent="submitForm"
                                    >Recuperar Contraseña</vs-button
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </vx-card>
        </div>
    </div>
</template>

<script>
import axios from "../../http/axios/index";
export default {
    data() {
        return {
            email: "",
        };
    },
    methods: {
        submitForm() {
            this.$validator.validateAll().then((result) => {
                if (result) {
                    const payload = {
                        email: this.email,
                    };
                    this.$vs.loading();

                    axios
                        .post("/password/email", payload)
                        .then((resp) => {
                            // Success
                            this.$vs.notify({
                                time: 6000,
                                title: "Recuperar contraseña",
                                text: resp.data,
                                color: "success",
                            });
                            this.email = "";
                            this.$validator.reset(); // ✅ Reset all validation errors
                            this.$vs.loading.close();
                            this.$router.push("/");
                        })
                        .catch((error) => {
                            // Extract server message if exists
                            let message =
                                "Ocurrió un error, intente nuevamente.";
                            if (
                                error.response &&
                                error.response.data &&
                                error.response.data.error
                            ) {
                                message = error.response.data.error;
                            }
                            this.$vs.notify({
                                time: 6000,
                                title: "Recuperar contraseña",
                                text: message,
                                color: "danger",
                            });
                            this.$vs.loading.close();
                        });
                } else {
                    // Validation failed
                    this.$vs.notify({
                        time: 6000,
                        title: "Recuperar contraseña",
                        text: "Por favor corrija los errores de validación.",
                        color: "warning",
                    });
                }
            });

            return false;
        },
    },
};
</script>
