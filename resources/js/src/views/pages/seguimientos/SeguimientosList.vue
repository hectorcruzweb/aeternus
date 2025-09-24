<template>
    <div>
        <div class="text-right buttons-container-header">
            <vs-button
                class="w-full md:w-auto md:ml-2 md:mt-0"
                color="success"
                @click="OpenFormSeguimientos()"
            >
                <span>Registrar Seguimiento</span>
            </vs-button>
        </div>
        <FormularioSeguimientos
            :show="verFormSeguimientos"
            @closeVentana="reloadList"
        >
        </FormularioSeguimientos>
        <PopupDebug v-if="isDev"></PopupDebug>
    </div>
</template>
<script>
import FormularioSeguimientos from "@pages/seguimientos/FormularioSeguimientos";
import PopupDebug from "../../PopupDebug.vue";
export default {
    components: {
        FormularioSeguimientos,
        PopupDebug,
    },
    computed: {
        isDev() {
            return process.env.NODE_ENV === "development";
        },
    },
    watch: {},
    data() {
        return {
            verFormSeguimientos: false,
        };
    },
    methods: {
        OpenFormSeguimientos() {
            this.verFormSeguimientos = true;
        },
        reloadList() {
            (async () => {
                this.verFormSeguimientos = false;
            })();
        },
    },
    mounted() {
        this.OpenFormSeguimientos();
    },
    created() {},
};
</script>
