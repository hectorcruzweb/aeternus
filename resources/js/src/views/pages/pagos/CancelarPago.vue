<template>
    <div class="centerx">
        <vs-popup
            :class="['forms-popup popup-70 cancelar-pago-compacto', z_index]"
            title="Cancelar Movimientos de Cobranza"
            :active="localShow"
            :ref="this.$options.name"
        >
            <!-- Presentación de cancelación consistente con CancelarVenta.
                 Los datos, validaciones, referencias y callbacks se conservan. -->
            <div class="form-group">
                <div class="title-form-group">Formulario de Cancelación</div>
                <div class="form-group-content">
                    <div class="flex flex-wrap">
                        <div class="w-full alerta pt-4 pb-6 px-2">
                            <div class="danger pago-advertencia">
                                <div class="pago-advertencia-titulo">
                                <feather-icon icon="AlertTriangleIcon" aria-hidden="true" />
                                <h3>
                                    ¿Está seguro de querer cancelar este pago?
                                </h3>
                                </div>
                                <p>
                                    Una vez cancelado, no podrá volver a habilitarse.
                                    Continúe únicamente si está seguro de que es necesario.
                                </p>
                            </div>
                        </div>
                        <div class="w-full pago-panel-layout">
                            <section class="pago-resumen pago-panel-resumen">
                                <div class="pago-resumen-cabecera">
                                    <div class="pago-dato"><span><feather-icon icon="CalendarIcon" /> Fecha de pago</span><strong>{{ fechaPagoHumana }}</strong></div>
                                    <div class="pago-dato"><span><feather-icon icon="HashIcon" /> Referencia de pago</span><strong v-for="referencia in referenciasPago" :key="referencia" class="pago-referencia">{{ referencia }}</strong></div>
                                    <div class="pago-dato"><span><feather-icon icon="CreditCardIcon" /> Clave de pago</span><strong>{{ datosPago.id }}</strong></div>
                                </div>
                                <div class="pago-identidad">
                                <div class="pago-dato pago-registro"><span><feather-icon icon="UserCheckIcon" /> Registrado por</span><strong>{{ registradorPago }}</strong></div>
                                <div class="pago-dato"><span><feather-icon icon="BriefcaseIcon" /> Tipo de operación</span><strong>{{ datosPago.tipo_operacion_texto || 'No disponible' }}</strong></div>
                                <div class="pago-dato" v-for="(cliente, i) in clientesPago" :key="'cliente-' + i"><span><feather-icon icon="UserIcon" /> Cliente</span><strong>{{ cliente }}</strong></div>
                                </div>
                                <div class="pago-importes-horizontal">
                                <div class="pago-saldo"><span>Saldo actual de la operación</span><strong>$ {{ saldo_operacion | numFormat("0,000.00") }} <small>MXN</small></strong></div>
                                <div class="pago-movimiento"><span>Importe del pago</span><strong>$ {{ datosPago.monto_pago | numFormat("0,000.00") }} <small>MXN</small></strong></div>
                                <div class="pago-concepto"><span>Concepto</span><strong>{{ datosPago.movimientos_pagos_texto }}</strong></div>
                                <div class="pago-movimiento" v-for="(subpago, i) in datosPago.subpagos" :key="'subpago-' + i"><span>Clave {{ subpago.id }}</span><p>{{ subpago.movimientos_pagos_texto }}</p><strong>$ {{ subpago.monto_pago | numFormat("0,000.00") }} <small>MXN</small></strong></div>
                                </div>
                            </section>
                            <div class="pago-panel-captura">
                                <div class="w-full xl:w-12/12 pt-6">
                                    <div class="flex flex-wrap">
                                        <div class="w-full input-text px-2">
                                            <label><feather-icon icon="ListIcon" aria-hidden="true" /> Motivo de cancelación</label>
                                            <v-select
                                                :options="motivos"
                                                :clearable="false"
                                                v-model="form.motivo"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                class="pb-1 pt-1"
                                                v-validate:motivo_computed.immediate="
                                                    'required'
                                                "
                                                name="plan_venta"
                                                data-vv-as=" "
                                            >
                                                <div slot="no-options">
                                                    No Se Ha Seleccionado Ningún
                                                    Motivo
                                                </div>
                                            </v-select>
                                            <span class="text-danger text-sm">{{
                                                errors.first("motivo")
                                            }}</span>
                                            <span
                                                class="text-danger text-sm"
                                                v-if="
                                                    this.errores['motivo.value']
                                                "
                                                >{{
                                                    errores["motivo.value"][0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full input-text px-2">
                                    <label><feather-icon icon="MessageSquareIcon" aria-hidden="true" /> Comentario de la cancelación</label>
                                    <vs-textarea
                                        class="w-full pago-detalle"
                                        placeholder="Detalle de la cancelación…"
                                        aria-label="Detalle de la cancelación"
                                        height="120px"
                                        v-model="form.comentario"
                                        ref="comentario"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bottom-buttons-section">
                <div class="text-advice">
                    <feather-icon icon="InfoIcon" aria-hidden="true" />
                    Revise la información antes de continuar.
                </div>

                <div class="w-full">
                    <vs-button
                        v-if="!fueCancelada"
                        class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0"
                        color="danger"
                        @click="acceptAlert()"
                    >
                        <feather-icon icon="FileMinusIcon" aria-hidden="true" />
                        <span>Cancelar pago</span>
                    </vs-button>
                </div>
            </div>

            <Password
                v-if="openConfirmar"
                :show="openConfirmar"
                :callback-on-success="callback"
                @closeVerificar="openConfirmar = false"
                :accion="'Cancelar Movimiento de Cobranza'"
            ></Password>

            <ConfirmarDanger
                v-if="openConfirmarSinPassword"
                :z_index="'z-index59k'"
                :show="openConfirmarSinPassword"
                :callback-on-success="callBackConfirmar"
                @closeVerificar="openConfirmarSinPassword = false"
                :accion="accionConfirmarSinPassword"
                :confirmarButton="botonConfirmarSinPassword"
            ></ConfirmarDanger>
        </vs-popup>
    </div>
</template>
<script>
import Password from "@pages/confirmar_password";
import vSelect from "vue-select";
import pagos from "@services/pagos";
import ConfirmarDanger from "@pages/ConfirmarDanger";
export default {
    name: "CancelarPago",
    components: {
        Password,
        "v-select": vSelect,
        ConfirmarDanger,
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        id_pago: {
            type: Number,
            required: true,
            default: 0,
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index55k",
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newValue) {
                if (newValue) {
                    this.$vs.loading();
                    try {
                        let res = await pagos.get_pago_id(this.getPagoId);
                        this.datosPago = res.data[0];
                        this.saldo_operacion =
                            this.datosPago.referencias_cubiertas[0].operacion_del_pago.saldo;
                        this.$vs.loading.close();
                    } catch (err) {
                        this.$vs.loading.close();
                        if (err.response) {
                            if (err.response.status == 403) {
                                this.$vs.notify({
                                    title: "Permiso denegado",
                                    text: "Verifique sus permisos con el administrador del sistema.",
                                    iconPack: "feather",
                                    icon: "icon-alert-circle",
                                    color: "warning",
                                    time: 8000,
                                });
                            } else {
                                this.$vs.notify({
                                    title: "Error",
                                    text: "Ha ocurrido un error al tratar de cargar el catálogo de vendedores, por favor reintente.",
                                    iconPack: "feather",
                                    icon: "icon-alert-circle",
                                    color: "danger",
                                    position: "bottom-right",
                                    time: "9000",
                                });
                            }
                        }
                        this.cerrarVentana();
                    }
                    this.$popupManager.register(
                        this,
                        this.cancelar,
                        "comentario"
                    );
                    //verificamos el origen del form para determinar que haremos justo al abrir el form.
                    //obtener datos del cliente
                } else {
                    this.$popupManager.unregister(this.$options.name);
                }
                this.localShow = newValue;
            },
        },
    },
    computed: {
        /** Presenta al usuario de la relación registro cargada por get_pagos,
         * conservando el autor real del registro, independientemente de la sesión. */
        registradorPago() {
            return (this.datosPago.registro && this.datosPago.registro.nombre) || 'No disponible';
        },
        /** Presenta la fecha efectiva del pago sin conversión de zona horaria:
         * extrae año/mes/día del valor SQL y conserva el texto disponible como respaldo. */
        fechaPagoHumana() {
            const valor = this.datosPago.fecha_pago || '';
            const partes = String(valor).match(/^(\d{4})-(\d{2})-(\d{2})/);
            if (!partes) return this.datosPago.fecha_pago_texto || 'No disponible';
            const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            const mes = meses[Number(partes[2]) - 1];
            return mes ? `${Number(partes[3])} de ${mes} de ${partes[1]}` : 'No disponible';
        },
        /** Solo presentación: reúne clientes de las referencias cubiertas,
         * sin duplicarlos ni consultar nuevamente al servidor. */
        clientesPago() {
            const nombres = (this.datosPago.referencias_cubiertas || []).map(ref => {
                const operacion = ref.operacion_del_pago || {};
                return operacion.cliente && operacion.cliente.nombre;
            }).filter(Boolean);
            return nombres.length ? [...new Set(nombres)] : ['No disponible'];
        },
        /** Un pago puede cubrir varias referencias: mostrar todas, sin duplicar. */
        referenciasPago() {
            const referencias = (this.datosPago.referencias_cubiertas || [])
                .map(ref => ref.referencia_pago).filter(Boolean);
            return referencias.length ? [...new Set(referencias)] : ['No disponible'];
        },
        motivo_computed: function () {
            return this.form.motivo.value;
        },
        getPagoId: {
            get() {
                return this.id_pago;
            },
            set(newValue) {
                return newValue;
            },
        },
    },
    data() {
        return {
            localShow: false,
            botonConfirmarSinPassword: "",
            accionConfirmarSinPassword: "",
            callBackConfirmar: Function,
            openConfirmarSinPassword: false,
            callback: Function,
            openConfirmar: false,
            errores: [],
            datosPago: [],
            saldo_operacion: 0,
            motivos: [
                {
                    label: "A PETICIÓN DEL CLIENTE",
                    value: 2,
                },
                {
                    label: "ERROR AL CAPTURAR",
                    value: 3,
                },
            ],
            form: {
                pago_id: "",
                motivo: {
                    label: "ERROR AL CAPTURAR",
                    value: 3,
                },
                comentario: "",
            },
        };
    },
    methods: {
        acceptAlert() {
            this.$validator
                .validateAll()
                .then((result) => {
                    if (!result) {
                        this.$vs.notify({
                            title: "Error",
                            text: "Verifique que todos los datos han sido capturados",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            position: "bottom-right",
                            time: "8000",
                        });
                        return;
                    } else {
                        (async () => {
                            this.callback = await this.cancelar_pago;
                            this.openConfirmar = true;
                        })();
                    }
                })
                .catch(() => {});
        },
        cancelar() {
            /*this.botonConfirmarSinPassword = "Salir y limpiar";
      this.accionConfirmarSinPassword =
        "Esta acción limpiará los datos que capturó en el formulario.";
      this.openConfirmarSinPassword = true;
      this.callBackConfirmar = this.cerrarVentana;*/
            this.cerrarVentana();
        },
        cerrarVentana() {
            //this.openConfirmarSinPassword = false;
            this.limpiarVentana();
            this.$emit("closeCancelarPago");
            return;
        },
        async cancelar_pago() {
            this.errores = [];
            this.$vs.loading();
            this.form.pago_id = this.getPagoId;
            try {
                let res = await pagos.cancelar_pago(this.form);
                this.$vs.loading.close();
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Movimientos de Cobranza",
                        text: "Se ha cancelado el movimiento correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                    this.limpiarVentana();
                    this.$emit("retorno_pago", res.data);
                } else {
                    this.$vs.notify({
                        title: "Movimientos de Cobranza",
                        text: "Error al cancelar el movimiento, por favor reintente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 8000,
                    });
                }
            } catch (err) {
                if (err.response) {
                    if (err.response.status == 403) {
                        /**FORBIDDEN ERROR */
                        this.$vs.notify({
                            title: "Permiso denegado",
                            text: "Verifique sus permisos con el administrador del sistema.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 8000,
                        });
                    } else if (err.response.status == 422) {
                        //checo si existe cada error
                        this.errores = err.response.data.error;
                        if (this.errores.pago_id) {
                            //la propiedad esa ya ha sido vendida
                            this.$vs.notify({
                                title: "Movimientos de Cobranza",
                                text: "No se ha seleccionado un movimiento a cancelar",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "danger",
                                time: 5000,
                            });
                        }

                        this.$vs.notify({
                            title: "Movimientos de Cobranza",
                            text: "Verifique los errores encontrados en los datos.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 5000,
                        });
                    } else if (err.response.status == 409) {
                        //este error es por alguna condicion que el contrano no cumple para modificar
                        //la propiedad esa ya ha sido vendida
                        this.$vs.notify({
                            title: "Movimientos de Cobranza",
                            text: err.response.data.error,
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 30000,
                        });
                    }
                }
                this.$vs.loading.close();
            }
        },
        limpiarVentana() {
            this.form.comentario = "";
            this.form.motivo = {
                label: "ERROR AL CAPTURAR",
                value: 3,
            };
        },
    },
    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name);
    },
    beforeDestroy() {
        this.$popupManager.unregister(this.$options.name);
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
<style>
/* Estilos propios del modal de pagos; conservan el encabezado y contenedores
   originales. Flex limita el scroll al cuerpo sin desbordar la ventana. */
.cancelar-pago-compacto .vs-popup {
    background: #fff !important; display: flex; flex-direction: column;
    max-height: calc(100vh - 32px) !important; overflow: hidden;
}
.cancelar-pago-compacto .vs-popup--header { flex: 0 0 auto; }
.cancelar-pago-compacto .vs-popup--content {
    color: #555d64; flex: 1 1 auto; min-height: 0;
    height: auto !important; max-height: none !important;
    width: 100% !important; margin: 0 !important; overflow-y: auto; overflow-x: hidden;
}
.cancelar-pago-compacto .form-group { padding: 22px !important; margin-bottom: 14px; }
.cancelar-pago-compacto .form-group-content { padding: 10px 0 0 !important; }
.cancelar-pago-compacto .alerta { padding-top: 0 !important; padding-bottom: 18px !important; }
.cancelar-pago-compacto .pago-advertencia {
    padding: 15px 17px !important; background: #fcf4f4 !important;
    border-left: 2px solid #b94b50 !important; color: #a74449 !important;
}
.cancelar-pago-compacto .pago-advertencia-titulo { display: flex; align-items: flex-start; gap: 10px; }
.cancelar-pago-compacto .pago-advertencia-titulo > .feather-icon {
    position: static !important; transform: none !important;
    display: inline-flex; flex: 0 0 20px; margin-top: 1px;
}
.cancelar-pago-compacto .pago-advertencia-titulo svg { width: 20px; height: 20px; }
.cancelar-pago-compacto .pago-advertencia h3 { margin: 0 !important; padding: 0 !important; font-size: 13px !important; font-weight: 600; line-height: 1.5; }
.cancelar-pago-compacto .pago-advertencia p { margin: 6px 0 0 30px !important; padding: 0 !important; font-size: 12px !important; line-height: 1.6; color: #8c6568 !important; }
.cancelar-pago-compacto .pago-resumen { padding: 17px !important; }
.cancelar-pago-compacto .pago-resumen .uppercase { text-transform: none !important; }
.cancelar-pago-compacto .pago-resumen .font-medium { font-size: 13px; }
.cancelar-pago-compacto .pago-resumen .feather-icon { display: inline-flex; vertical-align: middle; margin-right: 5px; color: #7a838a; }
.cancelar-pago-compacto .pago-resumen .feather-icon svg { width: 15px; height: 15px; }
.cancelar-pago-compacto .pago-importes { background: #f7f7f5 !important; padding: 12px !important; gap: 10px; }
.cancelar-pago-compacto .pago-importes > div:first-child { color: #95771f !important; font-size: 15px; }
.cancelar-pago-compacto .pago-importes small { font-size: 10px; }
.cancelar-pago-compacto .input-text label {
    display: flex; align-items: center; gap: 7px; margin-bottom: 8px;
    font-size: 12px; font-weight: 400 !important;
    text-transform: none !important; color: #646c73 !important;
}
.cancelar-pago-compacto .input-text label .feather-icon { color: #7a838a !important; }
.cancelar-pago-compacto .input-text label svg { width: 16px; height: 16px; }
.cancelar-pago-compacto .vs__dropdown-toggle { border-radius: 0 !important; }
.cancelar-pago-compacto .vs__selected { font-size: 14px !important; font-weight: 400 !important; color: #555d64 !important; }
/* Placeholder real sin h4 de Vuesax, con tamaño fijo y sin resize. */
.cancelar-pago-compacto .pago-detalle {
    border: 1px solid #d2d7da !important; border-radius: 0 !important;
    background: #fff !important; padding: 0 !important; margin-bottom: 0;
    box-sizing: border-box; height: 147px !important; min-height: 147px;
}
.cancelar-pago-compacto .pago-detalle .vs-textarea {
    box-sizing: border-box; width: 100%; height: 145px !important; min-height: 145px;
    display: block; border: 0 !important;
    padding: 13px 14px !important; margin: 0; background: #fff;
    font-family: inherit; font-size: 14px !important; font-weight: 400 !important;
    line-height: 1.6; color: #555d64 !important;
    text-transform: none !important; resize: none !important;
}
.cancelar-pago-compacto .pago-detalle .vs-textarea::placeholder { color: #858b90; opacity: 1; font-weight: 400; font-size: 14px; }
.cancelar-pago-compacto .pago-detalle.focusx { border-color: #ae8b2a !important; box-shadow: 0 0 0 2px rgba(175,139,40,.12); }
.cancelar-pago-compacto .bottom-buttons-section {
    display: grid !important; grid-template-columns: minmax(0, 1fr) auto;
    align-items: center; gap: 14px; padding: 8px 8px 0 !important;
}
.cancelar-pago-compacto .bottom-buttons-section .text-advice {
    display: flex; align-items: center; gap: 7px; margin: 0 !important;
    padding: 0 !important; font-size: 11px !important;
}
.cancelar-pago-compacto .bottom-buttons-section .text-advice svg { width: 16px; height: 16px; }
.cancelar-pago-compacto .bottom-buttons-section > .w-full { width: auto !important; margin-left: auto; }
.cancelar-pago-compacto .bottom-buttons-section .vs-button { background: #b64c50 !important; border-radius: 0 !important; font-size: 13px !important; text-transform: none !important; }
.cancelar-pago-compacto .bottom-buttons-section .vs-button:hover { background: #963b3f !important; }
.cancelar-pago-compacto .bottom-buttons-section .vs-button-text { display: inline-flex; align-items: center; gap: 8px; }
.cancelar-pago-compacto .bottom-buttons-section .vs-button-text svg { width: 16px; height: 16px; }
@media (max-width: 600px) {
    .cancelar-pago-compacto .form-group { padding: 18px 8px !important; }
}
/* Ficha horizontal adaptable. El resumen conserva pago principal y subpagos;
   los campos de captura mantienen sus componentes y validaciones. */
.cancelar-pago-compacto .vs-popup { width: 760px !important; max-width: calc(100% - 24px) !important; height: auto !important; }
.cancelar-pago-compacto .pago-panel-layout {
    display: grid !important; grid-template-columns: minmax(0, 1fr);
    gap: 18px; padding: 0 8px;
}
.cancelar-pago-compacto .pago-panel-resumen {
    border: 1px solid #bdc8d1; border-radius: 4px; background: #fff;
    padding: 12px !important; min-width: 0;
}
.cancelar-pago-compacto .pago-panel-resumen h3 {
    font-size: 12px !important; font-weight: 500 !important;
    line-height: 1.5 !important; margin: 0 0 16px !important; color: #525b63;
}
.cancelar-pago-compacto .pago-panel-resumen .pago-dato { margin-bottom: 0; }
.cancelar-pago-compacto .pago-identidad {
    display: grid; grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
    gap: 8px 20px; justify-content: space-between;
}
.cancelar-pago-compacto .pago-identidad > .pago-dato:not(.pago-registro) > strong {
    font-size: 11px; line-height: 1.5;
    overflow-wrap: normal; word-break: normal;
}
.cancelar-pago-compacto .pago-identidad > .pago-dato:last-child { text-align: right; }
.cancelar-pago-compacto .pago-identidad > .pago-dato:last-child > span { justify-content: flex-end; }
.cancelar-pago-compacto .pago-identidad > .pago-dato { min-width: 0; }
.cancelar-pago-compacto .pago-identidad > .pago-registro {
    grid-column: 1 / -1; display: flex; flex-wrap: wrap;
    align-items: center; gap: 6px 12px;
    background: #f3f3f3; padding: 8px 10px;
}
.cancelar-pago-compacto .pago-identidad > .pago-registro > span { margin: 0; }
.cancelar-pago-compacto .pago-identidad > .pago-registro > strong { font-size: 12px; }
.cancelar-pago-compacto .pago-importes-horizontal {
    display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px;
    margin-top: 8px; padding-top: 8px; border-top: 1px solid #e8e9e9;
}
.cancelar-pago-compacto .pago-panel-resumen .pago-dato > span {
    display: flex; align-items: center; gap: 6px; margin-bottom: 4px;
    font-size: 11px; color: #7b8389;
}
.cancelar-pago-compacto .pago-panel-resumen strong { display: block; font-size: 13px; font-weight: 600; overflow-wrap: anywhere; }
.cancelar-pago-compacto .pago-panel-resumen .pago-referencia { font-size: 12px; margin-bottom: 5px; }
.cancelar-pago-compacto .pago-panel-resumen .pago-saldo { margin-top: 0; }
.cancelar-pago-compacto .pago-panel-resumen .pago-saldo span { font-size: 11px; color: #7b8389; }
.cancelar-pago-compacto .pago-panel-resumen .pago-saldo strong { margin-top: 0; font-size: 19px; color: #9b7b24; }
.cancelar-pago-compacto .pago-panel-resumen small { font-size: 10px; font-weight: 400; }
.cancelar-pago-compacto .pago-panel-resumen .pago-movimiento { border: 0; margin: 0; padding: 0; }
.cancelar-pago-compacto .pago-panel-resumen .pago-movimiento span { font-size: 11px; color: #75818a; }
.cancelar-pago-compacto .pago-panel-resumen .pago-movimiento p { font-size: 12px; margin: 8px 0; }
.cancelar-pago-compacto .pago-panel-resumen .pago-movimiento strong { font-size: 16px; color: #536d81; }
.cancelar-pago-compacto .pago-panel-captura {
    min-width: 0; padding-top: 0;
    display: flex; flex-direction: column; align-self: stretch;
}
.cancelar-pago-compacto .pago-panel-captura > .pt-6 { padding-top: 0 !important; }
.cancelar-pago-compacto .pago-panel-captura .input-text { padding-left: 0 !important; padding-right: 0 !important; }
/* En la ficha horizontal el comentario tiene altura compacta propia,
   independiente de la cantidad de referencias o subpagos del resumen. */
.cancelar-pago-compacto .pago-panel-captura > .input-text:last-child {
    flex: 1 1 auto; display: flex; flex-direction: column;
    min-height: 0; margin-bottom: 0 !important; padding-bottom: 0 !important;
}
.cancelar-pago-compacto .pago-panel-captura > .input-text:last-child > label { flex: 0 0 auto; }
.cancelar-pago-compacto .pago-panel-captura .pago-detalle {
    flex: 0 0 auto; display: flex; flex-direction: column;
    height: 102px !important; min-height: 102px; margin: 0 !important;
}
.cancelar-pago-compacto .pago-panel-captura .pago-detalle .vs-textarea {
    flex: 1 1 auto; height: 100px !important; min-height: 100px;
}
/* Tres columnas iguales para distribuir la cabecera. La descripción del
   movimiento ocupa una fila propia, sin desplazar el importe respecto al saldo. */
.cancelar-pago-compacto .pago-resumen-cabecera {
    display: grid; grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px; align-items: start; margin-bottom: 4px;
    padding-bottom: 0 !important;
}
.cancelar-pago-compacto .pago-resumen-cabecera h3 { margin: 0 !important; }
.cancelar-pago-compacto .pago-resumen-cabecera > :nth-child(2) { text-align: center; }
.cancelar-pago-compacto .pago-resumen-cabecera > :last-child { text-align: right; }
.cancelar-pago-compacto .pago-resumen-cabecera > :nth-child(2) > span { justify-content: center; }
.cancelar-pago-compacto .pago-resumen-cabecera > :last-child > span { justify-content: flex-end; }
.cancelar-pago-compacto .pago-importes-horizontal { align-items: start; }
.cancelar-pago-compacto .pago-concepto { display: grid; row-gap: 3px; min-width: 0; }
.cancelar-pago-compacto .pago-concepto > span { font-size: 11px; color: #75818a; line-height: 1.5; }
.cancelar-pago-compacto .pago-concepto > strong { font-size: 12px; line-height: 1.6; font-weight: 400; }
.cancelar-pago-compacto .pago-importes-horizontal .pago-saldo,
.cancelar-pago-compacto .pago-importes-horizontal .pago-movimiento {
    display: grid; grid-template-columns: minmax(0, 1fr); row-gap: 3px;
}
.cancelar-pago-compacto .pago-importes-horizontal .pago-saldo > span,
.cancelar-pago-compacto .pago-importes-horizontal .pago-movimiento > span { grid-row: 1; line-height: 1.5; }
.cancelar-pago-compacto .pago-importes-horizontal .pago-saldo > strong,
.cancelar-pago-compacto .pago-importes-horizontal .pago-movimiento > strong {
    grid-row: 2; font-size: 19px; line-height: 1.4; margin: 0;
}
.cancelar-pago-compacto .pago-importes-horizontal .pago-movimiento > p { grid-row: 3; margin: 0 !important; }
@media (max-width: 600px) {
    .cancelar-pago-compacto .pago-panel-layout { grid-template-columns: 1fr; }
    .cancelar-pago-compacto .bottom-buttons-section { grid-template-columns: 1fr; }
    .cancelar-pago-compacto .pago-identidad,
    .cancelar-pago-compacto .pago-importes-horizontal { grid-template-columns: 1fr; }
    .cancelar-pago-compacto .pago-resumen-cabecera { grid-template-columns: 1fr; }
    .cancelar-pago-compacto .pago-resumen-cabecera > :nth-child(2),
    .cancelar-pago-compacto .pago-resumen-cabecera > :last-child { text-align: left; }
    .cancelar-pago-compacto .pago-resumen-cabecera > :nth-child(2) > span,
    .cancelar-pago-compacto .pago-resumen-cabecera > :last-child > span { justify-content: flex-start; }
}
</style>
