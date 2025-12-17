import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,
    get_tipos_comprobante() {
        return axios.get("/facturacion/get_tipos_comprobante");
    },
    get_sat_formas_pago() {
        return axios.get("/facturacion/get_sat_formas_pago");
    },
    get_metodos_pago() {
        return axios.get("/facturacion/get_metodos_pago");
    },
    get_tipos_relacion() {
        return axios.get("/facturacion/get_tipos_relacion");
    },
    get_claves_productos_sat() {
        return axios.get("/facturacion/get_claves_productos_sat");
    },
    get_sat_unidades() {
        return axios.get("/facturacion/get_sat_unidades");
    },
    get_usos_cfdi() {
        return axios.get("/facturacion/get_usos_cfdi");
    },
    get_sat_paises() {
        return axios.get("/facturacion/get_sat_paises");
    },
    get_empresa_tipo_operaciones() {
        return axios.get("/facturacion/get_empresa_tipo_operaciones");
    },
    get_operaciones(param) {
        return axios.get("/facturacion/get_operaciones/all/paginated", {
            cancelToken: new CancelToken((c) => {
                this.cancel = c;
            }),
            params: param,
        });
    },

    timbrar_cfdi(datos) {
        let call = "/facturacion/timbrar_cfdi";
        return axios.post(call, datos);
    },

    get_cfdis_timbrados(param) {
        return axios.get("/facturacion/get_cfdis_timbrados/all/paginated", {
            cancelToken: new CancelToken((c) => {
                this.cancel = c;
            }),
            params: param,
        });
    },

    get_cfdi_id(folio) {
        return axios.get("/facturacion/get_cfdis_timbrados/" + folio);
    },
    get_cfdi_download(folio) {
        return axios.get("/facturacion/get_cfdi_download/" + folio, {
            responseType: "blob",
        });
    },
    cancelar_cfdi_folio(datos) {
        let call = "/facturacion/cancelar_cfdi_folio";
        return axios.post(call, datos);
    },
    downloadExcelCfdisAdeudos() {
        return axios.get("/adeudos_cfdis", {
            responseType: "blob",
        });
    },

    reporteVentasFacturadas(year = "", mes = "") {
        return axios.get(`/ventas_x_mes_x_cfdis/${year}/${mes}`, {
            responseType: "blob",
        });
    },
};
