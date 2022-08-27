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
    // JWT
    loginJWT({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            jwt.login(payload.userDetails.email, payload.userDetails.password)
                .then(response => {
                    // If there's user data in response
                    if (response.data) {
                        // Navigate User to homepage
                        // Set accessToken
                        localStorage.setItem(
                            "accessToken",
                            response.data.access_token
                        );
                        localStorage.setItem(
                            "refreshToken",
                            response.data.refresh_token
                        );
                        // Set bearer token in axios
                        commit("LOGIN_USER", response.data.access_token);
                        dispatch("crear_menu");
                        dispatch("user_datos");

                        /**lista de urls y permisos para el usuarios logueado */
                        dispatch("user_modulos_permisos");
                        // Update user details(perfil del usuario)
                        commit("UPDATE_USER_INFO", payload.userDetails.email, {
                            root: true
                        });
                        resolve(response);
                    } else {
                        reject({
                            message: "Error de usuario y/o contraseña."
                        });
                    }
                })
                .catch(error => {
                    reject(error);
                });
        });
    },

    crear_menu({ commit }) {
        if (localStorage.getItem("accessToken")) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/get_permisos")
                    .then(resp => {
                        if (resp.code) {
                            reject({
                                message: "Error"
                            });
                        } else {
                            localStorage.setItem(
                                "AuthMenu",
                                JSON.stringify(resp.data)
                            );
                            router
                                .push(router.currentRoute.query.to || "/")
                                .catch(err => {});
                            resolve(resp);
                        }
                    })
                    .catch(err => {
                        reject(err);
                    });
            });
        }
    },

    //OBTENER INFO DEL USUARIOS
    user_datos({ commit }) {
        return new Promise((resolve, reject) => {
            axios
                .get("/get_perfil")
                .then(resp => {
                    if (resp.status == 200) {
                        localStorage.setItem(
                            "userInfo",
                            JSON.stringify(resp.data[0])
                        );
                        resolve(resp.data[0]);
                    } else {
                        reject();
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
