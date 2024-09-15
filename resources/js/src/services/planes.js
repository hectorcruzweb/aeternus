import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,

    registrar_plan(datos) {
        let call = "/funeraria/control_planes/agregar";
        return axios.post(call, datos);
    },
    update_plan(datos) {
        let call = "/funeraria/control_planes/modificar";
        return axios.post(call, datos);
    },
    get_planes(solo_a_futuro = false, plan_id = 0) {
        return axios.get(
            "/funeraria/get_planes/" + solo_a_futuro + "/" + plan_id
        );
    },
    enable_disable_planes(datos) {
        let call = "/funeraria/enable_disable_planes";
        return axios.post(call, datos);
    },

    get_precio_by_id(id) {
        return axios.get("/funeraria/get_precio_by_id", {
            params: {
                id_precio: id
            }
        });
    },
    registrar_precio(datos) {
        let call = "/funeraria/registrar_precio";
        return axios.post(call, datos);
    },

    update_precio(datos) {
        let call = "/funeraria/update_precio";
        return axios.post(call, datos);
    },
    /**enable / disable status precio */
    enable_disable(datos) {
        let call = "/funeraria/enable_disable";
        return axios.post(call, datos);
    },

    /**get financiamientos de las propiedades segun su tipo */
    get_financiamientos() {
        return axios.get("/cementerio/get_financiamientos");
    },

    //obtiene los usuarios para cargar los vendedores
    get_vendedores() {
        let call = "/cementerio/get_vendedores";
        return axios.get(call);
    },

    guardar_venta(datos) {
        let call = "/funeraria/control_ventas/agregar";
        return axios.post(call, datos);
    },
    /**servicios del formulario */

    modificar_venta(datos) {
        let call = "/funeraria/control_ventas/modificar";
        return axios.post(call, datos);
    },

    cancelar_venta(datos) {
        let call = "/funeraria/cancelar_venta";
        return axios.post(call, datos);
    },

    /**get las ventas para el paginado de ventas */
    get_ventas(param, light = false) {
        let service = "/funeraria/get_ventas/all/paginated";
        if (param.filtro_especifico_opcion == 3) {
            /**es de tipo de id venta */
            /**se debe de cambiar la url */
            if (param.numero_control.trim() != "") {
                service =
                    "/funeraria/get_ventas/" +
                    param.numero_control.trim() +
                    "/paginated";
            }
        }
        if (light == true) {
            service += "?light=true";
        }
        return axios.get(service, {
            cancelToken: new CancelToken(c => {
                this.cancel = c;
            }),
            params: param
        });
    },

    //obtiene la venta por id
    consultar_venta_id(id_venta) {
        let service = "/funeraria/get_ventas/" + id_venta;
        return axios.get(service);
    }
};
