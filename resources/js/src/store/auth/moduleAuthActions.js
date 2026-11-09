/*=========================================================================================
  File Name: moduleAuthActions.js
  Description: Auth Module Actions
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

import jwt from "../../http/requests/auth/jwt/index.js";
import axios from "../../http/axios/index";

import router from "@/router";

export default {
    /** Autentica y prepara la sesión completa antes de entrar al layout.
     * Esperar las tres consultas evita montar el menú/perfil con datos ausentes.
     * Si alguna falla, esperar a las demás antes de limpiar sus datos parciales. */
    async loginJWT({ commit, dispatch }, payload) {
        const response = await jwt.login(payload.userDetails.email, payload.userDetails.password);
        if (!response.data || !response.data.access_token)
            throw new Error("Error de usuario y/o contraseña.");

        localStorage.setItem("accessToken", response.data.access_token);
        localStorage.setItem("refreshToken", response.data.refresh_token);
        commit("LOGIN_USER", response.data.access_token);
        try {
            const resultados = await Promise.all(
                ["crear_menu", "user_datos", "user_modulos_permisos"].map(accion =>
                    dispatch(accion).then(() => ({ correcto: true }), error => ({ correcto: false, error }))
                )
            );
            const fallo = resultados.find(resultado => !resultado.correcto);
            if (fallo) throw fallo.error || new Error("No fue posible cargar los datos de la sesión.");
        } catch (error) {
            commit("LOGOUT_USER");
            throw error;
        }
        await router.push(router.currentRoute.query.to || "/").catch(() => {});
        return response;
    },

    /** Carga el menú autorizado. No navega: también se usa para recuperarlo
     * desde Main.vue cuando falta en el almacenamiento de una sesión vigente. */
    async crear_menu() {
        if (!localStorage.getItem("accessToken"))
            throw new Error("Inicie sesión para cargar el menú.");
        const resp = await axios.get("/get_permisos");
        if (resp.code || !Array.isArray(resp.data))
            throw new Error("No fue posible cargar el menú de navegación.");
        localStorage.setItem("AuthMenu", JSON.stringify(resp.data));
        return resp;
    },

    //OBTENER INFO DEL USUARIOS
    user_datos({ commit }) {
        return new Promise((resolve, reject) => {
            axios
                .get("/get_perfil")
                .then(resp => {
                    const perfil = resp.data && resp.data[0];
                    if (resp.status == 200 && perfil && typeof perfil === "object" && !Array.isArray(perfil)) {
                        // Guardar el objeto completo; el email solo no contiene nombre ni imagen.
                        commit("UPDATE_USER_INFO", perfil, { root: true });
                        resolve(perfil);
                    } else {
                        reject(new Error("No fue posible cargar el perfil del usuario."));
                    }
                })
                .catch(err => {
                    reject(err);
                });
        });
    },

    user_modulos_permisos({ commit }) {
        return new Promise((resolve, reject) => {
            axios
                .get("/get_modulos_urls_permisos")
                .then(resp => {
                    if (resp.status == 200) {
                        localStorage.setItem(
                            "AccessPermissions",
                            JSON.stringify(resp.data)
                        );
                        resolve(resp.data);
                    } else {
                        reject();
                    }
                })
                .catch(err => {
                    reject(err);
                });
        });
    },

    logout_user({ commit }) {
        if (localStorage.getItem("accessToken")) {
            return new Promise((resolve, reject) => {
                commit("LOGIN_USER", localStorage.getItem("accessToken"));
                axios
                    .post("/logout_usuario")
                    .then(resp => {
                        if (resp.code) {
                            reject({
                                message: "Error al cerrar sesion"
                            });
                        } else {
                            commit("LOGOUT_USER");
                            // Navigate User to login
                            router.push("/pages/login").catch(() => {});
                            resolve(resp);
                        }
                    })
                    .catch(err => {
                        reject(err);
                    });
            });
        } else {
            router.push("/pages/login").catch(() => {});
        }
    },

    /**FORZAR EL LOGOUT DEL USUARIO */
    logout_force({ commit }) {
        commit("LOGOUT_USER");
        router.push("/pages/login").catch(() => {});
    },

    fetchAccessToken() {
        return new Promise(resolve => {
            jwt.refreshToken().then(response => {
                resolve(response);
            });
        });
    }
};
