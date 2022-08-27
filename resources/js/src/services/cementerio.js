import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,

    registrar_precio_propiedad(datos) {
        let call = "/cementerio/registrar_precio_propiedad";
        return axios.post(call, datos);
    },

    update_precio_propiedad(datos) {
        let call = "/cementerio/update_precio_propiedad";
        return axios.post(call, datos);
    },

    /**enable / disable status precio */
    enable_disable(datos) {
        let call = "/cementerio/enable_disable";
        return axios.post(call, datos);
    },

    

    actualizar_status_convenio(datos) {
        let call = "/cementerio/actualizar_status_convenio";
        return axios.post(call, datos);
    },

    /**get financiamientos de las propiedades segun su tipo */
    get_financiamientos() {
        return axios.get("/cementerio/get_financiamientos");
    },
    /**obtiene los tipos de propiedades */
    get_tipo_propiedades() {
        return axios.get("/cementerio/get_tipo_propiedades");
    },
    /**busca un precio por id */
    get_precio_by_id(id) {
        return axios.get("/cementerio/get_precio_by_id", {
            params: {
                id_precio: id
            }
        });
    },

    //obtiene las propieades del cementerio
    get_cementerio() {
        let call = "/cementerio/get_cementerio";
        return axios.get(call);
    },
    //obtiene los usuarios para cargar los vendedores
    get_vendedores() {
        let call = "/cementerio/get_vendedores";
        return axios.get(call);
    },

    //obtiene las 3 tipos de venta segun la antiguedad
    get_antiguedades() {
        let call = "/inventarios/cementerio/get_antiguedades_venta";
        return new Promise((resolve, reject) => {
            axios
                .get(call)
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },

    //obtiene las formas de pago con la clave del sat segun las necesidades del sistema
    get_sat_formas_pago() {
        let call = "/inventarios/cementerio/get_sat_formas_pago";
        return new Promise((resolve, reject) => {
            axios
                .get(call)
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },

    guardar_venta(datos) {
        let call = "/cementerio/control_ventas/agregar";
        return axios.post(call, datos);
    },

    modificar_venta(datos) {
        let call = "/cementerio/control_ventas/modificar";
        return axios.post(call, datos);
    },

    cancelar_venta(datos) {
        let call = "/cementerio/cancelar_venta";
        return axios.post(call, datos);
    },

    //obtiene la distribucion del cementerio
    getDistribucion() {
        let call = "/inventarios/cementerio/get_cementerio";
        return new Promise((resolve, reject) => {
            axios
                .get(call)
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },

    getDistribucionById(id) {
        let call = "/inventarios/cementerio/propiedadesById";
        return new Promise((resolve, reject) => {
            axios
                .get(call, {
                    params: {
                        id_propiedad: id
                    }
                })
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },

    //obtengo todos los usuarios para poder aseginar cual es vendedor y cual no
    getUsuariosVendedores() {
        let self = this;
        return new Promise((resolve, reject) => {
            axios
                .get("/inventarios/cementerio/get_usuarios_para_vendedores")
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    if (axiosSuper.isCancel(error)) {
                        reject(error.message);
                    } else {
                        reject(error);
                    }
                });
        });
    },

    //obtengo los posibles tipos de venta para la propiedad
    get_ventas_referencias_propiedades() {
        let self = this;

        return axios.get(
            "/inventarios/cementerio/get_ventas_referencias_propiedades"
        );
    },

    //obtiene los tipos de propieades que hay
    tipoPropiedades() {
        let self = this;
        return new Promise((resolve, reject) => {
            axios
                .get("/inventarios/cementerio/tipoPropiedades")
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    if (axiosSuper.isCancel(error)) {
                        reject(error.message);
                    } else {
                        reject(error);
                    }
                });
        });
    },

    //obtiene las propieades segun su tipo
    get_propiedades_by_tipo(id) {
        let call = "/inventarios/cementerio/get_propiedades_by_tipo";
        return new Promise((resolve, reject) => {
            axios
                .get(call, {
                    params: {
                        id_propiedad_tipo: id
                    }
                })
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },

    //retorna los datos de columnas_filas para saber en que numero de lote inicia y acaba una fila de una terraza
    get_columna_fila_terraza(propiedades_id, fila) {
        let call = "/inventarios/cementerio/get_columna_fila_terraza";
        return new Promise((resolve, reject) => {
            axios
                .get(call, {
                    params: {
                        propiedades_id: propiedades_id,
                        fila: fila
                    }
                })
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },

    //retorna los precios de las diferenres propiedades
    precios_tarifas() {
        let call = "/inventarios/cementerio/precios_tarifas";
        return axios.get(call);
    },

    //update precios tarifas
    actualizar_precios_tarifas(datos) {
        let call = "/inventarios/cementerio/actualizar_precios_tarifas";
        return new Promise((resolve, reject) => {
            axios
                .post(call, datos)
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },
    /**get las ventas para el paginado de ventas */
    get_ventas(param) {
        let service = "/cementerio/get_ventas/all/paginated";
        if (param.filtro_especifico_opcion == 4) {
            /**es de tipo de id venta */
            /**se debe de cambiar la url */
            if (param.numero_control.trim() != "") {
                service =
                    "/cementerio/get_ventas/" +
                    param.numero_control.trim() +
                    "/paginated";
            }
        }
        return axios.get(service, {
            cancelToken: new CancelToken(c => {
                this.cancel = c;
            }),
            params: param
        });
    },

    get_cuotas(param) {
        let service = "/cementerio/get_cuotas/all/false";
        return axios.get(service, {
            cancelToken: new CancelToken(c => {
                this.cancel = c;
            }),
            params: param
        });
    },

    get_cuota_by_id(id_cuota) {
        let service = "/cementerio/get_cuotas/" + id_cuota;
        return axios.get(service);
    },

    //obtiene la venta por id
    consultar_venta_id(id_venta) {
        let service = "/cementerio/get_ventas/" + id_venta;
        return axios.get(service);
    },

    registrar_cuota(datos) {
        let call = "/cementerio/control_cuotas/agregar";
        return axios.post(call, datos);
    },

    update_cuota(datos) {
        let call = "/cementerio/control_cuotas/modificar";
        return axios.post(call, datos);
    },
    cancelar_cuota(datos) {
        let call = "/cementerio/cancelar_cuota";
        return axios.post(call, datos);
    },
    get_cuotas_simple() {
        let call = "/cementerio/get_cuotas_simple";
        return axios.get(call);
    }
};
