import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,

    get_legistas_embalsamadores() {
        return axios.get("/funeraria/get_legistas_embalsamadores/");
    },
    get_personal_recoger() {
        return axios.get("/funeraria/get_personal_recoger/");
    },
    guardar_solicitud(param) {
        let call = "/funeraria/control_solicitud/agregar";
        return axios.post(call, param);
    },

    modificar_solicitud(param) {
        let call = "/funeraria/control_solicitud/modificar";
        return axios.post(call, param);
    },

    modificar_contrato(param, tipo_servicio) {
        let call = "/funeraria/control_contratos/" + tipo_servicio;
        return axios.post(call, param);
    },

    get_solicitudes_servicios(param, light = false) {
        let service = "/funeraria/get_solicitudes_servicios/all/paginated";
        if (light == true) {
            service += "?light=true";
        }
        return axios.get(service, {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },
    get_solicitudes_servicios_id_uso_plan_funerario_futuro(param) {
        return axios.get("/funeraria/get_solicitudes_servicios/all/false/" + param, {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },
    get_solicitudes_servicios_id_uso_terreno(param) {
        return axios.get("/funeraria/get_solicitudes_servicios/all/false/0/" + param, {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },
    get_solicitudes_servicios_id(param) {
        return axios.get("/funeraria/get_solicitudes_servicios/" + param + '/0/0/0/1', {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    get_estados_civiles() {
        let call = "/funeraria/get_estados_civiles";
        return axios.get(call);
    },

    get_escolaridades() {
        let call = "/funeraria/get_escolaridades";
        return axios.get(call);
    },

    get_afiliaciones() {
        let call = "/funeraria/get_afiliaciones";
        return axios.get(call);
    },

    get_sitios_muerte() {
        let call = "/funeraria/get_sitios_muerte";
        return axios.get(call);
    },
    get_titulos() {
        let call = "/funeraria/get_titulos";
        return axios.get(call);
    },
    get_estados_afectado() {
        let call = "/funeraria/get_estados_afectado";
        return axios.get(call);
    },

    get_lugares_velacion() {
        let call = "/funeraria/get_lugares_velacion";
        return axios.get(call);
    },

    get_lugares_inhumacion() {
        let call = "/funeraria/get_lugares_inhumacion";
        return axios.get(call);
    },

    get_material_velacion(param) {
        return axios.get("/funeraria/get_material_velacion/all/false/3/0/0/0", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },
    get_tipos_contratante() {
        let call = "/funeraria/get_tipos_contratante";
        return axios.get(call);
    },

    get_terrenos(param) {
        return axios.get("/cementerio/get_ventas/all/paginated", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    get_planes_a_futuro(param) {
        return axios.get("/funeraria/get_ventas/all/paginated", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    get_planes() {
        return axios.get("/funeraria/get_planes");
    },

    get_categorias_servicio() {
        return axios.get("/funeraria/get_categorias_servicio");
    },

    get_inventario_servicios(param) {
        return axios.get("/funeraria/get_inventario/all/paginated/0/0", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    get_inventario_servicios_codigos(param) {
        return axios.get("/funeraria/get_inventario/all/0/0/0", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    cancelar_solicitud(datos) {
        let call = "/funeraria/cancelar_solicitud";
        return axios.post(call, datos);
    },

    /**serviios del modulo */

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

    modificar_articulo(param) {
        let call = "/inventario/control_articulos/modificar";
        return axios.post(call, param);
    },

    get_ajustes(param) {
        return axios.get("/inventario/get_ajustes/all/paginated", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    get_inventariable(param) {
        return axios.get("/inventario/get_inventario/all/paginated/0/0/0/1", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
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
    //ventas en gral
    control_ventas_gral(param, tipo_servicio) {
        let call = "/funeraria/control_ventas_gral/" + tipo_servicio;
        return axios.post(call, param);
    },
    get_ventas_gral(param, light = false, id = "") {
        let service = "/funeraria/get_ventas_gral/";
        if (id == "") {
            service += "all/paginated";
        } else {
            service += id;
        }
        if (light == true) {
            service += "?light=true";
        }
        return axios.get(service, {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },
    entregar_venta_gral(datos) {
        let call = "/funeraria/entregar_venta_gral";
        return axios.post(call, datos);
    },
    cancelar_venta_gral(datos) {
        let call = "/funeraria/cancelar_venta_gral";
        return axios.post(call, datos);
    },
};
