/*=========================================================================================
  File Name: router.js
  Description: Routes for vue-router. Lazy loading is enabled.
  Object Strucutre:
                    path => router path
                    name => router name
                    component(lazy loading) => component to load
                    meta : {
                      rule => which user can have access (ACL)
                      breadcrumb => Add breadcrumb to specific page
                      pageTitle => Display title besides breadcrumb
                    }
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

import Vue from "vue";
import Router from "vue-router";
import store from "../src/store/store";

Vue.use(Router);

const router = new Router({
    mode: "history",
    base: process.env.BASE_URL,
    scrollBehavior() {
        return {
            x: 0,
            y: 0
        };
    },
    routes: [
        {
            // =============================================================================
            // MAIN LAYOUT ROUTES
            // =============================================================================
            path: "",
            component: () => import("./layouts/main/Main.vue"),
            children: [
                // =============================================================================
                // Theme Routes
                // =============================================================================
                {
                    path: "/",
                    redirect: "/home",
                    meta: {
                        authRequired: true
                    }
                },
                {
                    path: "/home",
                    name: "home",
                    component: () => import("./views/HomeInfo.vue"),
                    meta: {
                        rule: "editor",
                        authRequired: true
                    }
                },
                /**RUTAS PARA EL MODULO DE USUARIOS */
                {
                    path: "/configuracion/roles",
                    name: "Lista de Roles de Usuario",
                    component: () =>
                        import(
                            "@/views/pages/configuracion/roles/RolesList.vue"
                        ),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Roles de Usuario"
                            },
                            {
                                title: "Lista de roles registrados",
                                active: true
                            }
                        ],
                        pageTitle: "Roles de Usuario",
                        rule: "editor",
                        authRequired: true
                    }
                },
                {
                    path: "/configuracion/usuarios",
                    name: "Lista de Usuarios",
                    component: () =>
                        import(
                            "@/views/pages/configuracion/usuarios/UsuariosList.vue"
                        ),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Usuarios"
                            },
                            {
                                title: "Lista de usuarios registrados",
                                active: true
                            }
                        ],
                        pageTitle: "Usuarios",
                        rule: "editor",
                        authRequired: true
                    }
                },
                /**FIN DE RUTAS PARA EL MODULO DE USUARIOS */
                /**RUTAS PARA EL MODULO DE EMPRESA */
                {
                    path: "/configuracion/empresa",
                    name: "Informacion de empresa",
                    component: () =>
                        import(
                            "@/views/pages/empresa/configuracion/empresa.vue"
                        ),
                    //component: () => import('@/views/pages/empresa/Empresa.vue'),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Configuración",
                                active: true
                            }
                        ],
                        pageTitle: "Empresa",
                        rule: "editor",
                        authRequired: true
                    }
                },
                /**FIN DE RUTAS PARA EL MODULO DE EMPRESAS */

                /*Modulo de checador */
                {
                    path: "/control-de-asistencia",
                    name: "Control de asistencia",
                    component: () =>
                        import(
                            "@/views/pages/control_de_asistencia/checador.vue"
                        ),
                    //component: () => import('@/views/pages/empresa/Empresa.vue'),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Control de Asistencia",
                                active: true
                            }
                        ],
                        pageTitle: "Checador",
                        rule: "editor",
                        authRequired: true
                    }
                },
                /*fin del modulo de checador */

                {
                    path: "/cementerio/ventas",
                    name: "cementerio_ventas",
                    component: () =>
                        import(
                            "@/views/pages/cementerio/ventas/VentasList.vue"
                        ),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Control del cementerio"
                            },
                            {
                                title: "Ventas de Terrenos",
                                active: true
                            }
                        ],
                        pageTitle: "Cementerio",
                        rule: "editor",
                        authRequired: true
                    }
                },
                {
                    path: "/funeraria/ventas_planes",
                    name: "ventas_planes",
                    component: () =>
                        import("@/views/pages/funeraria/ventas/VentasList.vue"),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Control de Planes Funerarios"
                            },
                            {
                                title: "Ventas",
                                active: true
                            }
                        ],
                        pageTitle: "Funeraria",
                        rule: "editor",
                        authRequired: true
                    }
                },
                {
                    path: "/cobranza/pagos",
                    name: "cobranza_pagos",
                    component: () =>
                        import("@/views/pages/pagos/PagosLista.vue"),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Control del pagos"
                            },
                            {
                                title: "Módulo de cobranza",
                                active: true
                            }
                        ],
                        pageTitle: "Cobranza",
                        rule: "editor",
                        authRequired: true
                    }
                },
                {
                    path: "/cobranza/facturacion",
                    name: "cobranza_facturacion",
                    component: () =>
                        import("@/views/pages/facturacion/ListaFacturas.vue"),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Control de Facturación"
                            },
                            {
                                title: "Módulo de Facturación",
                                active: true
                            }
                        ],
                        pageTitle: "Facturación Electrónica",
                        rule: "editor",
                        authRequired: true
                    }
                },
                // =============================================================================
                // Application Routes
                // =============================================================================

                {
                    path: "/clientes",
                    name: "catalogo_clientes",
                    component: () =>
                        import("@/views/pages/clientes/ClientesList.vue"),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Catálogos"
                            },
                            {
                                title: "Control de clientes",
                                active: true
                            }
                        ],
                        pageTitle: "Clientes",
                        rule: "editor",
                        authRequired: true
                    }
                },

                {
                    path: "/inventarios/funeraria/proveedores",
                    name: "catalogo_proveedores",
                    component: () =>
                        import(
                            "@/views/pages/inventarios/proveedores/ProveedoresList.vue"
                        ),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Catálogos"
                            },
                            {
                                title: "Control de proveedores",
                                active: true
                            }
                        ],
                        pageTitle: "Proveedores",
                        rule: "editor",
                        authRequired: true
                    }
                },
                {
                    path: "/inventarios/funeraria/articulos",
                    name: "catalogo_articulos",
                    component: () =>
                        import(
                            "@/views/pages/inventarios/articulos/ArticulosList.vue"
                        ),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Catálogos"
                            },
                            {
                                title: "Control de artículos y servicios",
                                active: true
                            }
                        ],
                        pageTitle: "Artículos / Servicios",
                        rule: "editor",
                        authRequired: true
                    }
                },
                {
                    path: "/inventarios/funeraria/ajustes",
                    name: "ajuste_inventario",
                    component: () =>
                        import(
                            "@/views/pages/inventarios/ajustes/AjustesList.vue"
                        ),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Catálogos"
                            },
                            {
                                title: "Ajustes de inventario",
                                active: true
                            }
                        ],
                        pageTitle: "Ajustar Inventario",
                        rule: "editor",
                        authRequired: true
                    }
                },
                {
                    path: "/inventarios/funeraria/compras",
                    name: "compras_inventario",
                    component: () =>
                        import(
                            "@/views/pages/inventarios/compras/ComprasList.vue"
                        ),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Control de Compras a Proveedores"
                            },
                            {
                                title: "Inventario",
                                active: true
                            }
                        ],
                        pageTitle: "Compras",
                        rule: "editor",
                        authRequired: true
                    }
                },

                {
                    path: "/funeraria/servicios",
                    name: "servicios_funerarios",
                    component: () =>
                        import(
                            "@/views/pages/funeraria/servicios_funerarios/ServiciosList.vue"
                        ),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Control de Servicios Funerarios"
                            },
                            {
                                title: "Servicios Funerarios",
                                active: true
                            }
                        ],
                        pageTitle: "Funeraria",
                        rule: "editor",
                        authRequired: true
                    }
                },

                {
                    path: "/reportes",
                    name: "reportes",
                    component: () =>
                        import("@/views/pages/reportes/GeneradorReportes"),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Listado de reportes disponibles"
                            },
                            {
                                title: "Generar reportes",
                                active: true
                            }
                        ],
                        pageTitle: "Reportes",
                        rule: "editor",
                        authRequired: true
                    }
                },

                // =============================================================================
                // Pages Routes
                // =============================================================================
                {
                    path: "/pages/profile",
                    name: "page-profile",
                    component: () => import("@/views/pages/Profile.vue"),
                    meta: {
                        authRequired: true,
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Usuario"
                            },
                            {
                                title: "Cambiar mi Perfíl",
                                active: true
                            }
                        ],
                        pageTitle: "Perfíl",
                        rule: "editor",
                        authRequired: true
                    }
                },

                {
                    path: "/pages/faq",
                    name: "page-faq",
                    component: () => import("@/views/pages/Faq.vue"),
                    meta: {
                        breadcrumb: [
                            {
                                title: "Home",
                                url: "/"
                            },
                            {
                                title: "Pages"
                            },
                            {
                                title: "FAQ",
                                active: true
                            }
                        ],
                        pageTitle: "FAQ",
                        rule: "editor"
                    }
                }
            ]
        },
        // =============================================================================
        // FULL PAGE LAYOUTS
        // =============================================================================
        {
            path: "",
            component: () => import("@/layouts/full-page/FullPage.vue"),
            children: [
                // =============================================================================
                // PAGES
                // =============================================================================
                {
                    path: "/pages/login",
                    name: "page-login",
                    component: () => import("@/views/pages/login/Login.vue"),
                    meta: {
                        rule: "editor"
                    }
                },

                {
                    path: "/pages/forgot-password",
                    name: "page-forgot-password",
                    component: () => import("@/views/pages/ForgotPassword.vue"),
                    meta: {
                        rule: "editor"
                    }
                },
                {
                    path: "/pages/reset-password/:token",
                    name: "page-reset-password",
                    component: () => import("@/views/pages/ResetPassword.vue"),
                    meta: {
                        rule: "editor"
                    }
                },
                {
                    path: "/pages/lock-screen",
                    name: "page-lock-screen",
                    component: () => import("@/views/pages/LockScreen.vue"),
                    meta: {
                        rule: "editor"
                    }
                },
                {
                    path: "/pages/comingsoon",
                    name: "page-coming-soon",
                    component: () => import("@/views/pages/ComingSoon.vue"),
                    meta: {
                        rule: "editor"
                    }
                },
                {
                    path: "/pages/error-404",
                    name: "page-error-404",
                    component: () => import("@/views/pages/Error404.vue"),
                    meta: {
                        rule: "editor"
                    }
                },
                {
                    path: "/pages/error-500",
                    name: "page-error-500",
                    component: () => import("@/views/pages/Error500.vue"),
                    meta: {
                        rule: "editor"
                    }
                },
                {
                    path: "/pages/not-authorized",
                    name: "page-not-authorized",
                    component: () => import("@/views/pages/NotAuthorized.vue"),
                    meta: {
                        rule: "editor"
                    }
                },
                {
                    path: "/pages/maintenance",
                    name: "page-maintenance",
                    component: () => import("@/views/pages/Maintenance.vue"),
                    meta: {
                        rule: "editor"
                    }
                }
            ]
        },
        // Redirect to 404 page, if no match found
        {
            path: "*",
            redirect: "/pages/error-404"
        }
    ]
});

router.afterEach(() => {
    // Remove initial loading
    const appLoading = document.getElementById("loading-bg");
    if (appLoading) {
        appLoading.style.display = "none";
    }
});

router.beforeEach((to, from, next) => {
    /**validando paginas especiales */
    if (
        to.path === "/pages/login" ||
        to.path === "/pages/forgot-password" ||
        to.path === "/callback"
    ) {
        if (localStorage.getItem("accessToken")) {
            return next("/");
        }
    }

    //validar si ya esta logueado y redigir al dashboard

    // If auth required, check login. If login fails redirect to login page
    //router.push({ path: '/pages/login', query: { to: to.path } }).catch(err=>{})
    /** verificar que este logueado */
    if (to.matched.some(record => record.meta.authRequired)) {
        if (localStorage.getItem("accessToken")) {
            if (to.path === "/home" || to.path === "/pages/profile") {
                return next();
            } else {
                /**verificando si el usuario tiene permiso para acceder a esa ruta solicitdad */
                if (localStorage.getItem("AccessPermissions")) {
                    var modulos_permisos = JSON.parse(
                        localStorage.getItem("AccessPermissions")
                    );
                    /**verificando que la ruta ala que quiere acceder esta en la lista
                     * de permisos de el clienre que esta logueado
                     */
                    for (
                        let index = 0;
                        index < modulos_permisos.length;
                        index++
                    ) {
                        if (modulos_permisos[index].url === to.path) {
                            next();
                            return false;
                        }
                    }

                    /**no esntro en niguna ruta */
                    return next("/");
                } else {
                    /**se deberia mandar llamar el dispatch del crear user_modulos_permisos en moduleAuthActions */
                    /**verificando que las urls y los permisos esten siempre disponibles */
                    if (!localStorage.getItem("AccessPermissions")) {
                        store
                            .dispatch("auth/user_modulos_permisos")
                            .then(() => {
                                return next("/");
                            });
                    }
                }
            }
        } else return next("/pages/login");
    } else {
        return next();
    }
    //
    //router.push({ path: '/pages/login' }).catch(err=>{})
    // Specify the current path as the customState parameter, meaning it
    // will be returned to the application after auth
    // auth.login({ target: to.path })
});

export default router;
