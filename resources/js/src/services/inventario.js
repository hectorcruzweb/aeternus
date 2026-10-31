/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Cliente HTTP de inventario; incluye visualizarExcel y descargas/envío para el nuevo visor.
 * Dónde ajustar: Conservar contratos de funciones usadas por otros módulos; el nombre visualizarExcel es
 * histórico y devuelve una vista HTML de reporte.
 * Relacionado: InventarioController; HomeInfo.vue; VisorReportes.vue
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */
import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,

    get_tipo_articulos() {
        return axios.get("/inventario/get_tipo_articulos/");
    },

    get_categorias() {
        return axios.get("/inventario/get_categorias/");
    },

    get_unidades() {
        return axios.get("/inventario/get_unidades/");
    },

    get_sat_unidades() {
        return axios.get("/inventario/get_sat_unidades/");
    },

    guardar_articulo(param) {
        let call = "/inventario/control_articulos/agregar";
        return axios.post(call, param);
    },

    control_compras(param) {
        let call = "/inventario/control_compras/agregar";
        return axios.post(call, param);
    },
    cancelar_compra(param) {
        let call = "/inventario/cancelar_compra";
        return axios.post(call, param);
    },

    modificar_articulo(param) {
        let call = "/inventario/control_articulos/modificar";
        return axios.post(call, param);
    },

    get_compras(param) {
        return axios.get("/inventario/get_compras/all/paginated", {
            cancelToken: new CancelToken((c) => {
                self.cancel = c;
            }),
            params: param,
        });
    },

    get_inventario(param) {
        return axios.get("/inventario/get_inventario/all/paginated", {
            cancelToken: new CancelToken((c) => {
                self.cancel = c;
            }),
            params: param,
        });
    },

    get_ajustes(param) {
        return axios.get("/inventario/get_ajustes/all/paginated", {
            cancelToken: new CancelToken((c) => {
                self.cancel = c;
            }),
            params: param,
        });
    },

    get_inventariable(param) {
        return axios.get("/inventario/get_inventario/all/paginated/0/0/0/1", {
            cancelToken: new CancelToken((c) => {
                self.cancel = c;
            }),
            params: param,
        });
    },

    get_articulo_by_id(param) {
        return axios.get("/inventario/get_inventario/" + param, {
            cancelToken: new CancelToken((c) => {
                self.cancel = c;
            }),
            params: param,
        });
    },

    enable_disable(param) {
        let call = "/inventario/enable_disable/enable";
        return axios.post(call, param);
    },
    delete_articulo(param) {
        let call = "/inventario/enable_disable/disable";
        return axios.post(call, param);
    },

    ajustar_inventario(param) {
        let call = "/inventario/ajustar_inventario";
        return axios.post(call, param);
    },

    get_inventariable_etiquetado() {
        return axios.get("/inventario/get_inventario/all/false/0/0/0/1", {
            cancelToken: new CancelToken((c) => {
                self.cancel = c;
            }),
        });
    },
    // Vista HTML directa y token privado para descargar la misma selección.
    /**
     * Detalle del método: Solicita HTML/metadatos/token de inventario por filtro; devuelve promesa Axios y
     * no genera XLSX pese al nombre histórico.
     * Entradas: filtro = "all" (filtro de inventario; all usa el listado general).
     */
    visualizarExcel(filtro = "all") {
        return axios.get("/inventario/dashboard/reporte-preview", { params: { filtro } });
    },
    /**
     * Detalle del método: Envía token y datos confirmados del correo de inventario al backend; devuelve la
     * promesa del envío.
     * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
     * datos confirmados del envío, según el método).
     */
    enviarExcelPreview(token, datos) {
        return axios.post("/inventario/dashboard/reporte-preview/" + encodeURIComponent(token) + "/correo", datos);
    },
    /**
     * Detalle del método: Devuelve la promesa Axios con Blob PDF del reporte de inventario autorizado por
     * token.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    descargarPdfPreview(token) {
        return axios.get("/inventario/dashboard/reporte-preview/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" });
    },
    /**
     * Detalle del método: Devuelve la promesa Axios con Blob XLSX del reporte de inventario autorizado por
     * token.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    descargarExcelPreview(token) {
        return axios.get("/inventario/dashboard/reporte-preview/" + encodeURIComponent(token) + "/excel", { responseType: "blob" });
    },
    /**
     * Detalle del método: Conserva la descarga directa anterior del Excel de inventario por filtro; no
     * exige abrir el visor.
     * Entradas: filtro = "all" (filtro de inventario; all usa el listado general).
     */
    descargarExcel(filtro = "all") {
        return axios.get("/listar_inventario/" + filtro + "/1", {
            responseType: "blob",
        });
    },
};
