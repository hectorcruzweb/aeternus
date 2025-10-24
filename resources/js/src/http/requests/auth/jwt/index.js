import axios from "../../../axios/index.js";
import store from "../../../../store/store.js";

// Token Refresh
let isAlreadyFetchingAccessToken = false;
let subscribers = [];
let allowedErrorCodes = [422, 429, 500, 403, 404, 409, 414, 413];
/**VALIDO SI EXISTEN ESTOS DATOS PARA DEJAR PASAR LA PETICION */
if (
    localStorage.getItem("accessToken") &&
    localStorage.getItem("refreshToken")
) {
    axios.defaults.headers.common["Authorization"] =
        "Bearer " + localStorage.getItem("accessToken");
} else {
    //forzar el logout
    store.dispatch("auth/logout_force");
}

function onAccessTokenFetched(access_token) {
    subscribers = subscribers.filter((callback) => callback(access_token));
}

function addSubscriber(callback) {
    subscribers.push(callback);
}

export default {
    init() {
        axios.interceptors.response.use(
            function (response) {
                return response;
            },
            function (error) {
                console.log("🚀 ~ error:", error);
                return;
                // const { config, response: { status } } = error
                const { config, response } = error;
                const originalRequest = config;
                // if (status === 401) {
                /**AQUI VALIDO QUE LA PETICION NO APLIQUE PARA EL REFREH TOKEN
            PUESTO QUE SI ESTE TAMBIEN FALLA EL SISTEMA DEBERIA OBLIGAR AL 
            USUARIO A LOGUEARSE NUEVAMENTE */
                if (
                    response &&
                    (response.status === 401 || response.status === 500) &&
                    originalRequest.url != "/refresh_token"
                ) {
                    if (!isAlreadyFetchingAccessToken) {
                        isAlreadyFetchingAccessToken = true;
                        store
                            .dispatch("auth/fetchAccessToken")
                            .then((access_token) => {
                                /**ACTUALIZO EL LOCAL STORAGE CON LOS NUEVO VALORES DEL TOKEN Y EL REFRSH */
                                localStorage.setItem(
                                    "accessToken",
                                    access_token.data.access_token
                                );
                                localStorage.setItem(
                                    "refreshToken",
                                    access_token.data.refresh_token
                                );
                                axios.defaults.headers.common["Authorization"] =
                                    "Bearer " +
                                    localStorage.getItem("accessToken");
                                /**FIN DE ACTUALIZAR EL LOCAL STORAGE */
                                isAlreadyFetchingAccessToken = false;
                                onAccessTokenFetched(access_token);
                            });
                    }

                    const retryOriginalRequest = new Promise((resolve) => {
                        addSubscriber((access_token) => {
                            /**PASANDO EL NUEVO TOKEN A LA ULTIMA PETICION QUE MANDO EL 401 */
                            originalRequest.headers.Authorization =
                                "Bearer " + localStorage.getItem("accessToken");
                            resolve(axios(originalRequest));
                        });
                    });
                    return retryOriginalRequest;
                } else {
                    /**VALIDO QUE EL ERROR NO SEA EL 422 DE ERROR PROCESING "CAUSADO POR EL RESET PASSWORD" */
                    /**ERROR 429 TOO MANY REQUEST */
                    /**ERROR 500 INTERNAL SERVER */
                    if (response) {
                        //si el error tiene status
                        if (response.status) {
                            if (
                                allowedErrorCodes.indexOf(response.status) ===
                                -1
                            ) {
                                store.dispatch("auth/logout_force");
                                location.reload();
                            }
                        }
                    } else {
                        return Promise.reject(error);
                    }
                }
                return Promise.reject(error);
            }
        );
    },
    login(email, pwd) {
        return axios.post("/login_usuario", {
            username: email,
            password: pwd,
        });
    },
    registerUser(name, email, pwd) {
        return axios.post("/api/auth/register", {
            displayName: name,
            email: email,
            password: pwd,
        });
    },
    refreshToken() {
        //PASAR EL REFRESH TOKEN PARA SOLICITAR UN NUEVO TOKEN
        return axios.post("/refresh_token", {
            refresh_token: localStorage.getItem("refreshToken"),
        });
    },
};
