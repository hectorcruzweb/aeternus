/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Cliente HTTP del JSON principal y la gráfica por mes/año.
 * Dónde ajustar: Mantener _fetchDashboard separado de graficaVentasSaldos para no recargar las demás
 * tarjetas al cambiar el periodo.
 * Relacionado: HomeInfo.vue; routes/api.php
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */
import axios from "@/axios.js";
import { error } from "@/plugins/logger";

export default {
    /**
     * Detalle del método: Devuelve la promesa Axios del agregado de operaciones para el año/mes indicado,
     * sin recargar el dashboard completo.
     * Entradas: periodo (objeto con año y mes solicitados).
     */
    graficaVentasSaldos(periodo) { return axios.get("/dashboard/ventas-saldos/grafica", { params: periodo }); },
    /**
     * Detalle del método: Devuelve la promesa Axios de GET /dashboard/build y propaga errores al
     * componente llamante; este cliente no aplica datos ni controla skeletons.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async _fetchDashboard() {
        try {
            return await axios.get("/dashboard/build"); // return only the data
        } catch (error) {
            throw error; // propagate the error so Vue can handle it
        }
    },
};
