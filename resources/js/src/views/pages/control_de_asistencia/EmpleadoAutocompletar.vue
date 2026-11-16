<template>
    <div class="empleado-autocompletar">
        <label :for="idCampo">Nombre del empleado</label>
        <input :id="idCampo" :value="value.texto" type="search" maxlength="100"
            placeholder="Buscar por nombre" autocomplete="off" role="combobox" aria-autocomplete="list"
            :aria-expanded="abierto" :aria-controls="abierto ? idCampo + '-opciones' : null"
            :aria-activedescendant="abierto && indice >= 0 ? idCampo + '-' + indice : null"
            @input="escribir" @focus="buscar(value.texto)" @blur="cerrar" @keydown="navegar" />
        <div v-if="abierto" class="empleado-autocompletar__menu">
            <p v-if="mensaje" role="status">{{ mensaje }}</p>
            <ul :id="idCampo + '-opciones'" role="listbox" aria-label="Empleados">
                <li v-for="(empleado, i) in opciones" :id="idCampo + '-' + i" :key="empleado.id"
                    role="option" :aria-selected="i === indice" @mousedown.prevent @click="seleccionar(empleado)">
                    {{ empleado.nombre }} <small>· {{ empleado.area_des }} · #{{ empleado.id }}</small>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import axios from "@/axios.js";

export default {
    props: {
        value: { type: Object, required: true },
        idCampo: { type: String, required: true },
        area: { type: [String, Number], default: "" },
        estado: { type: String, default: "" },
    },
    data() {
        return { abierto: false, opciones: [], mensaje: "", indice: -1, secuencia: 0, temporizador: null };
    },
    watch: {
        value(valor) { if (!valor.texto || valor.id) this.cerrar(); },
        area: "cerrar",
        estado: "cerrar",
    },
    beforeDestroy() { this.cerrar(); },
    methods: {
        cerrar() {
            clearTimeout(this.temporizador);
            this.secuencia++;
            this.abierto = false;
        },
        escribir(evento) {
            const texto = evento.target.value;
            this.$emit("input", { texto, id: null });
            this.buscar(texto);
        },
        buscar(texto) {
            this.cerrar();
            const nombre = texto.trim();
            if (!nombre || (this.value.id && texto === this.value.texto)) return;
            this.abierto = true;
            this.indice = -1;
            this.opciones = [];
            this.mensaje = "Buscando empleados…";
            const secuencia = this.secuencia;
            this.temporizador = setTimeout(async () => {
                try {
                    const { data } = await axios.get("/checador/get_empleados_paginados", {
                        params: { nombre, area: this.area || 0, filtro_especifico_opcion: this.estado || undefined, per_page: 10 },
                    });
                    if (secuencia !== this.secuencia) return;
                    this.opciones = data.data;
                    this.mensaje = this.opciones.length ? "" : "No se encontraron empleados.";
                } catch (error) {
                    if (secuencia === this.secuencia) this.mensaje = "No se pudieron consultar los empleados.";
                }
            }, 300);
        },
        seleccionar(empleado) {
            this.cerrar();
            this.$emit("input", { texto: empleado.nombre, id: empleado.id });
        },
        navegar(evento) {
            if (!this.abierto) {
                if (evento.key === "ArrowDown") this.buscar(this.value.texto);
                return;
            }
            if (evento.key === "Escape") {
                evento.stopPropagation();
                this.cerrar();
                return;
            }
            if (evento.key === "Enter") {
                evento.preventDefault();
                if (this.indice >= 0) this.seleccionar(this.opciones[this.indice]);
            }
            if (!this.opciones.length || !["ArrowDown", "ArrowUp"].includes(evento.key)) return;
            evento.preventDefault();
            const total = this.opciones.length;
            this.indice = this.indice < 0 ? (evento.key === "ArrowDown" ? 0 : total - 1)
                : (this.indice + (evento.key === "ArrowDown" ? 1 : -1) + total) % total;
            this.$nextTick(() => {
                const opcion = document.getElementById(this.idCampo + "-" + this.indice);
                if (opcion) opcion.scrollIntoView({ block: "nearest" });
            });
        },
    },
};
</script>

<style scoped>
.empleado-autocompletar { position:relative; min-width:0; }
.empleado-autocompletar label { display:block; font-size:11px; line-height:1.5; color:#777; text-transform:uppercase; }
.empleado-autocompletar input { display:block; width:100%; height:38px; margin-top:5px; padding:8px 10px; border:1px solid #c9c9c9; border-radius:0; background:#fff; color:#666; font:13px Arial,sans-serif; }
.empleado-autocompletar input:focus { outline:none; border-color:#ab891d; }
.empleado-autocompletar__menu { position:absolute; z-index:20; top:calc(100% + 4px); width:100%; max-height:230px; overflow:auto; background:#fff; border:1px solid #d2d2d2; box-shadow:0 4px 12px #0002; }
.empleado-autocompletar__menu p, .empleado-autocompletar__menu li { padding:8px 10px; }
.empleado-autocompletar__menu li { cursor:pointer; }
.empleado-autocompletar__menu li:hover, .empleado-autocompletar__menu li[aria-selected="true"] { background:#faf8f0; color:#80641a; }
</style>
