import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,
    get_registros(param) {
        let service = "/checador/get_registros_checador_frontend/all/";
        if (param.id_empleado != "") {
            service += param.id_empleado;
        } else {
            service += "all";
        }
        service += "/paginated";
        if (param.filtro_especifico_opcion != 2) {
            //por fechas
            param.fecha_inicio = "";
            param.fecha_fin = "";
        }
        service += "/" + param.area;
        return axios.get(service, {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    get_empleados() {
        return axios.get("checador/get_empleados");
    },

    cancelar_registro(datos) {
        let call = "/checador/cancelar_registro";
        return axios.post(call, datos);
    },
    habilitar_registro(datos) {
        let call = "/checador/habilitar_registro";
        return axios.post(call, datos);
    },

    get_dias_descanso_empleado(id) {
        let service = "checador/get_empleados_dias_descanso/" + id;
        return axios.get(service);
    },
    guardar_dias_descanso(datos) {
        let call = "/checador/guardar_dias_descanso";
        return axios.post(call, datos);
    },

    get_empleados_todos() {
        return axios.get("checador/get_empleados/si");
    },

    guardar_registro_administrativo(datos) {
        let call = "/checador/guardar_registro_administrativo";
        return axios.post(call, datos);
    },
    get_registro_by_id(id) {
        let service =
            "/checador/get_registros_checador_frontend/" +
            id +
            "/all/no_paginated";
        return axios.get(service);
    },

    modificar_registro_administrativo(datos) {
        let call = "/checador/modificar_registro_administrativo";
        return axios.post(call, datos);
    },

    get_empleados_paginados(param) {
        let service = "/checador/get_empleados_paginados";
        return axios.get(service, {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    }
};
