import axios from "../../../axios/index.js";
import store from "../../../../store/store.js";

// Token Refresh
let isAlreadyFetchingAccessToken = false;
let subscribers = [];
let allowedErrorCodes = [422, 429, 500, 403, 404, 409, 414, 413];

/** VALIDATE TOKEN EXISTENCE BEFORE ANY REQUEST */
if (
    localStorage.getItem("accessToken") &&
    localStorage.getItem("refreshToken")
) {
    axios.defaults.headers.common["Authorization"] =
        "Bearer " + localStorage.getItem("accessToken");
} else {
    // Force logout if tokens are missing
    store.dispatch("auth/logout_force");
}

/** HELPER: Notify all subscribers once token is refreshed */
function onAccessTokenFetched(access_token) {
    subscribers = subscribers.filter((callback) => callback(access_token));
}

/** HELPER: Add subscribers waiting for new token */
function addSubscriber(callback) {
    subscribers.push(callback);
}

/** HELPER: Force logout and reload */
function forceLogout() {
    console.warn("⛔ Session expired or invalid. Forcing logout...");
    store.dispatch("auth/logout_force");
    location.reload();
}

export default {
    init() {
        axios.interceptors.response.use(
            function (response) {
                return response;
            },
            function (error) {
                const { config, response } = error;
                const originalRequest = config;

                // Handle token expiration or unauthorized errors
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
                                /** Update tokens in localStorage */
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

                                isAlreadyFetchingAccessToken = false;
                                onAccessTokenFetched(access_token);
                            })
                            .catch(() => {
                                // If refresh fails, force logout
                                forceLogout();
                            });
                    }

                    // Retry the original request once new token is fetched
                    const retryOriginalRequest = new Promise((resolve) => {
                        addSubscriber((access_token) => {
                            originalRequest.headers.Authorization =
                                "Bearer " + localStorage.getItem("accessToken");
                            resolve(axios(originalRequest));
                        });
                    });
                    return retryOriginalRequest;
                } else {
                    // Handle all other types of errors
                    if (response) {
                        const status = response.status;
                        const data = response.data || {};

                        // 🔹 Explicit API error response (backend format)
                        if (
                            (data.error && data.error === "No autenticado") ||
                            (data.code && data.code === 401)
                        ) {
                            forceLogout();
                            return;
                        }

                        // 🔹 Logout for unexpected status codes
                        if (
                            status &&
                            allowedErrorCodes.indexOf(status) === -1
                        ) {
                            forceLogout();
                            return;
                        }
                    } else {
                        // Network or unknown error
                        return Promise.reject(error);
                    }
                }

                return Promise.reject(error);
            }
        );
    },

    /** LOGIN */
    login(email, pwd) {
        return axios.post("/login_usuario", {
            username: email,
            password: pwd,
        });
    },

    /** REGISTER USER */
    registerUser(name, email, pwd) {
        return axios.post("/api/auth/register", {
            displayName: name,
            email: email,
            password: pwd,
        });
    },

    /** REFRESH TOKEN */
    refreshToken() {
        return axios.post("/refresh_token", {
            refresh_token: localStorage.getItem("refreshToken"),
        });
    },
};
