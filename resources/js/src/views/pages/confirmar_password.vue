<template>
    <div class="centerx">
        <vs-popup :class="['confirm-form', z_index]" close="cancelar" title="contraseña" :active="localShow"
            :ref="$options.name">
            <div class="text-center password_icono hidden"></div>
            <div class="w-full text-center mt-3 h2 color-copy font-medium capitalize px-2">
                confirmar contraseña
            </div>
            <div class="mt-3 text-center hidden">
                <span class="color-primary-900 size-smaller uppercase">{{
                    accion
                }}</span>
            </div>
            <div class="w-full text-center mt-3 color-copy size-small px-2">
                Para mayor seguridad debe ingresar su contraseña para confirmar
                que es un usuario autorizado para realizar esta operación.
            </div>
            <div class="w-full px-2 mt-6 mx-auto">
                <vs-input maxlength="50" autocomplete="one-time-code" size="large" inputmode="none"
                    name="auth_verification" ref="confirmAuth" type="password" form="none" class="w-full"
                    placeholder="Contraseña" v-model.trim="pass" @keyup.enter="acceptAlert" />
            </div>
            <div class="w-full text-right px-2 mt-6">
                <span @click.prevent.stop="cancel" class="color-danger-900 my-2 mr-8 cursor-pointer">(Esc) Cerrar
                    Ventana</span>
                <vs-button class="w-auto md:ml-2 my-2 md:mt-0" color="success" @click.prevent="acceptAlert">
                    <span>Continuar</span>
                </vs-button>
            </div>
        </vs-popup>
    </div>
</template>
<script>
import usuarios from "@services/Usuarios";
export default {
    name: "confirmar_password",
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        callbackOnSuccess: {
            type: Function,
            required: true,
        },
        accion: {
            type: String,
            required: true,
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index60k",
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            handler(newVal) {
                // Only listen when visible = true
                if (newVal) {
                    this.$popupManager.register(this, this.cancel);
                } else {
                    this.$popupManager.unregister(this.$options.name);
                }
                this.localShow = newVal;
            },
        },
    },
    data() {
        return {
            pass: "",
            localShow: false, // controls popup visibility
        };
    },
    computed: {
        validPassword() {
            return !!this.pass;
        },
    },
    methods: {
        async acceptAlert() {
            if (!this.validPassword) {
                this.pass = "";
                return;
            }
            //se verificq que exista una contraseña y se procede a realizar la confirmacion al servidor
            try {
                const res = await usuarios.confirmPassword(this.pass);
                if (res.status === 200) {
                    this.callbackOnSuccess();
                    this.cancel();
                }
            } catch (err) {
                this.$vs.notify({
                    title: "Permiso denegado",
                    text: "Contraseña incorrecta. Por favor reintente.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                    position: "top-center",
                    time: "40000",
                });
                this.pass = "";
                this.$nextTick(() =>
                    this.$refs["confirmAuth"].$el.querySelector("input").focus()
                );
            }
        },
        cancel(event) {
            if (event) event.stopPropagation();
            this.pass = "";
            this.$emit("closeVerificar");
        },
    },
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name); // DOM is ready
    },
    beforeDestroy() {
        this.$popupManager.unregister(this.$options.name);
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
