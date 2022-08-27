/*=========================================================================================
  File Name: moduleAuthMutations.js
  Description: Auth Module Mutations
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

import axios from "../../http/axios/index.js";

export default {
    LOGIN_USER(state, accessToken) {
        //se manda el token para mostrar logueado al usuario
        state.isUserLoggedIn = accessToken;
        /**se agrega el token al header de axios */
        axios.defaults.headers.common["Authorization"] =
            "Bearer " + accessToken;
    },
    LOGOUT_USER(state) {
        /**se reinician los estados quitando toda credencial del sistema */
        axios.defaults.headers.common["Authorization"] = " ";
        state.isUserLoggedIn = "";
        localStorage.removeItem("accessToken");
        localStorage.removeItem("refreshToken"),
            localStorage.removeItem("userInfo");
        localStorage.removeItem("AuthMenu");
        localStorage.removeItem("AccessPermissions");
    }
};
