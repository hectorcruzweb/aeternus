<template>
    <div>
        <div v-if="aviso" class="px-2 py-2 text-warning" role="status">
            {{ aviso }}
            <button v-if="errorRecuperable" type="button" class="ml-2 text-primary underline"
                :disabled="cargando" @click="_fetchData()">{{ cargando ? "Cargando…" : "Reintentar" }}</button>
        </div>
        <!-- Datos del Cliente y Operación Seleccionada -->
        <div class="flex flex-wrap">
            <div class="w-full md:w-6/12 px-2 input-text">
                <label> Cliente </label>
                <vs-input ref="nombre" name="nombre" type="text" class="w-full" placeholder="" maxlength="100"
                    :readonly="true" v-model="cliente.nombre" />
            </div>
            <div class="w-full md:w-6/12 px-2 input-text">
                <label> Operación Seleccionada </label>
                <vs-input ref="operacion" name="operacion" type="text" class="w-full" placeholder="" maxlength="100"
                    :readonly="true" v-model="operacion_descripcion" />
            </div>
            <div class="w-full px-2 input-text">
                <label> Dirección </label>
                <vs-input ref="direccion" name="direccion" type="text" class="w-full" placeholder="" maxlength="100"
                    :readonly="true" v-model="cliente.direccion_completa" />
            </div>
        </div>
    </div>
</template>
<script>
import clientes from "../../../services/clientes";
export default {
    name: "InfoOperacion",
    props: {
        filters: { type: Object, required: true },
        soloConsulta: { type: Boolean, default: false },
    },
    data() {
        return { cliente: {}, operacion_descripcion: null, aviso: "", errorRecuperable: false, cargando: false, solicitudActual: 0 };
    },
    watch: {
        filters: { deep: true, immediate: true, handler(valor) { if (valor && valor.cliente_id) this._fetchData(); } },
        soloConsulta() { if (this.filters.cliente_id) this._fetchData(); },
    },
    methods: {
        // Normalizar como texto evita diferencias número/string y pérdida de precisión en BIGINT.
        normalizarId(valor) {
            if (valor === null || valor === undefined || typeof valor === "boolean") return null;
            const texto = String(valor).trim();
            if (!/^\d+$/.test(texto)) return null;
            const id = texto.replace(/^0+(?=\d)/, "");
            return id === "0" ? null : id;
        },
        async _fetchData() {
            const solicitud = ++this.solicitudActual;
            this.cargando = true; this.aviso = ""; this.errorRecuperable = false;
            this.cliente = {}; this.operacion_descripcion = null;
            try {
                const params = { id: this.filters.cliente_id, tipo_cliente_id: this.filters.tipo_cliente_id, filtrar_x_operaciones: 1 };
                if (this.soloConsulta && this.normalizarId(this.filters.seguimiento_id)) {
                    params.seguimiento_historico_id = this.normalizarId(this.filters.seguimiento_id);
                }
                const result = await clientes.fetchClientes(params);
                if (solicitud !== this.solicitudActual) return;
                const data = Array.isArray(result) ? result[0] : result;
                if (!data || typeof data !== "object" || !this.normalizarId(data.id)) throw new Error("No se recibieron los datos del cliente.");
                this.cliente = { id: data.id, nombre: data.nombre, direccion_completa: data.direccion_completa };
                const operacionId = this.normalizarId(this.filters.operacion_id);
                if (operacionId) {
                    const lista = Array.isArray(data.operaciones) ? data.operaciones : [];
                    const match = lista.find(op => this.normalizarId(op.operacion_id) === operacionId);
                    if (match) {
                        this.operacion_descripcion = match.descripcion + (match.saldo ? " Saldo: " + match.saldo : "");
                        if (this.soloConsulta && Number(match.status) === 0) {
                            this.aviso = "La operación relacionada está cancelada. Se muestra únicamente para consultar este seguimiento.";
                        }
                    } else if (this.soloConsulta) {
                        this.operacion_descripcion = "Operación relacionada no disponible";
                        this.aviso = "La operación relacionada ya no está disponible para este cliente. Puedes consultar los datos del seguimiento.";
                    } else {
                        throw new Error("Error, Operación no válida.");
                    }
                } else {
                    this.operacion_descripcion = "No se seleccionó ninguna operación";
                }
                if (data.cliente_disponible === false) this.aviso = "Los datos del cliente ya no están disponibles. Se conservan los datos del seguimiento.";
                this.$emit("resultado", true, data.email);
            } catch (error) {
                if (solicitud !== this.solicitudActual) return;
                const status = Number((error.response && error.response.status) || error.status || 0);
                this.errorRecuperable = this.soloConsulta && ![401,403,404,422].includes(status);
                this.aviso = this.errorRecuperable ? "No fue posible cargar los datos del cliente. Puedes reintentar sin cerrar esta consulta." : (error.message || "No fue posible consultar los datos.");
                if (!this.errorRecuperable) this.$vs.notify({ title: "Datos del Cliente", text: this.aviso, iconPack: "feather", icon: "icon-alert-circle", color: "danger" });
                this.$emit("resultado", false, "", { recuperable: this.errorRecuperable });
            } finally {
                if (solicitud === this.solicitudActual) this.cargando = false;
            }
        },
    },
    beforeDestroy() { this.solicitudActual++; },
};
</script>
