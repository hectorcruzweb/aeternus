<template>
    <div class="centerx">
        <vs-popup :class="['forms-popup popup-50', z_index]" :title="formTitle" :active.sync="showVentana"
            ref="cotizaciones_predefinidas">
            <div class="flex flex-wrap">
                <div class="w-full lg:w-12/12 px-2 input-text large-size">
                    <label>
                        Cotizaciones Predefinidas
                        <span>(*)</span>
                    </label>
                    <v-select :options="cotizaciones" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                        v-model="form.cotizacion" class="w-full" name="cotizaciones" data-vv-as=" ">
                        <div slot="no-options">
                            No Se Ha Seleccionado Ninguna Cotización
                        </div>
                    </v-select>
                </div>


                <div class="w-full px-2 py-2 text-center">
                    <div v-show="seccion.conceptos.length > 0" :key="indexSeccion"
                        v-for="(seccion, indexSeccion) in form.cotizacion.secciones">
                        <div class="bg-dark-600 text-white h5 font-bold uppercase">
                            <span v-show="indexSeccion > 0" class="uppercase">solo en caso
                                de </span> {{ seccion.seccion }}
                        </div>
                        <div class="py-2">
                            <span class="uppercase" :key="indexConcepto"
                                v-for="(concepto, indexConcepto) in seccion.conceptos">
                                {{ concepto }} {{ (indexConcepto + 1) < seccion.conceptos.length ? ' | ' : '' }} </span>
                        </div>
                    </div>
                </div>
                <div class="w-full px-2 pb-2">
                    <vs-table noDataText="" class="tabla-datos tabla-datos-no-data"
                        :data="form.cotizacion.financiamientos">
                        <template slot="header">
                            <h3>Financiamientos</h3>
                        </template>
                        <template slot="thead">
                            <vs-th>#</vs-th>
                            <vs-th><span class="px-2">DESCRIPCIÓN</span></vs-th>
                            <vs-th><span class="px-2">COSTO NETO</span></vs-th>
                            <vs-th><span class="px-2">PAGO INICIAL</span></vs-th>
                            <vs-th><span class="px-2">ABONO MENSUAL</span></vs-th>
                        </template>
                        <template slot-scope="{ data }">
                            <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                <vs-td>
                                    <span class="font-semibold px-2">{{ indextr + 1 }}</span>
                                </vs-td>
                                <vs-td class="size-base padding-y-7">
                                    <span class="px-2">{{ form.cotizacion.financiamientos[indextr].financiamiento
                                        }}</span>
                                </vs-td>
                                <vs-td class="size-base padding-y-7">
                                    <span class="px-2">$ {{ form.cotizacion.financiamientos[indextr].costo_neto
                                        | numFormat("0,000.00") }}
                                        MXN.</span>
                                </vs-td>
                                <vs-td class="size-base padding-y-7">
                                    <span class="px-2">$ {{ form.cotizacion.financiamientos[indextr].pago_inicial
                                        | numFormat("0,000.00") }}
                                        MXN.</span>
                                </vs-td>
                                <vs-td class="size-base padding-y-7">
                                    <span class="px-2">$ {{ form.cotizacion.financiamientos[indextr].pago_mensual
                                        | numFormat("0,000.00") }}
                                        MXN.</span>
                                </vs-td>
                            </vs-tr>
                        </template>
                    </vs-table>
                </div>
                <div class="w-full py-4">
                    <vs-button v-show="tipoCotizacion == 'funeraria' || tipoCotizacion == 'cementerio'" class="w-full"
                        color="success" @click="seleccionarCotizacion()">
                        <span>Seleccionar Cotización</span>
                    </vs-button>
                </div>
            </div>
        </vs-popup>
    </div>
</template>
<script>
import vSelect from "vue-select";
import planes from "@services/planes";
import cementerio from "@services/cementerio";
export default {
    components: {
        "v-select": vSelect,
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index55k",
        },
        tipo_cotizacion: {
            type: String,
            required: true,
        },
        cotizacionVer: {
            type: Object,
            required: false,
            default: {}
        }
    },
    watch: {
        show: function (newValue, oldValue) {
            if (newValue == true) {
                console.log(this.tipoCotizacion)
                if (this.tipoCotizacion == 'funeraria') {
                    (async () => {
                        await this.get_planes_funerarios();
                    })();
                } else if (this.tipoCotizacion == 'cementerio') {
                    (async () => {
                        await this.get_financiamientos();
                    })();
                } else if (this.tipoCotizacion == 'ver') {
                    this.cotizaciones.push(this.GetCotizacionVer);
                    this.form.cotizacion = this.cotizaciones[0];
                }
            } else {
                //reinicias el form
                this.form.cotizacion = {};
                this.cotizaciones = [];
            }
        },
    },
    computed: {
        formTitle() {
            let title = '';
            if (this.tipoCotizacion == 'funeraria' || this.tipoCotizacion == 'cementerio') {
                return 'seleccionar cotización de ' + this.tipoCotizacion;
            } else {
                return 'Visualizar cotización seleccionada';
            }
        },
        showVentana: {
            get() {
                return this.show;
            },
            set(newValue) {
                return newValue;
            },
        },
        tipoCotizacion: {
            get() {
                return this.tipo_cotizacion;
            },
            set(newValue) {
                return newValue;
            },
        },
        GetCotizacionVer: {
            get() {
                return this.cotizacionVer;
            },
            set(newValue) {
                return newValue;
            },
        }
    },
    data() {
        return {
            cotizaciones: [],
            form: {
                cotizacion: {}
            }
        };
    },
    methods: {
        async get_financiamientos() {
            try {
                this.$vs.loading();
                let res = await cementerio.get_financiamientos();
                //creamos las diferentes cotizaciones
                this.cotizaciones = [];
                res.data.forEach((element) => {
                    if (element.precios.length > 0) {
                        //creamos el objeto con la informacion de esta cotizacion
                        let financiamientos = [];
                        element.precios.forEach((financiamiento) => {
                            financiamientos.push({
                                financiamiento: financiamiento.tipo_financiamiento,
                                costo_neto: financiamiento.costo_neto,
                                pago_inicial: financiamiento.pago_inicial,
                                pago_mensual: financiamiento.pago_mensual
                            })
                        });
                        //se agrega al cotizaciones object
                        this.cotizaciones.push({
                            tipo: this.tipoCotizacion,
                            label: 'Espacio en cementerio tipo ' + element.tipo,
                            secciones: [{
                                //solo una seccion de incluye
                                seccion: 'incluye',
                                conceptos: [
                                    'espacio en cementerio tipo ' + element.tipo + ' con capacidad de ' + element.capacidad + ' Persona(s).'
                                ]
                            }],
                            financiamientos: financiamientos,
                        });
                    }
                });
                if (this.cotizaciones.length > 0) {
                    this.form.cotizacion = this.cotizaciones.length > 1 ? this.cotizaciones[1] : this.cotizaciones[0];
                    console.log(this.cotizaciones)
                } else {
                    this.$vs.notify({
                        title: "Error",
                        text: "Verifique los presupuestos predefinidos (Costos y Secciones de conceptos contenidos).",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        position: "bottom-right",
                        time: "9000",
                    });
                    this.cerrar_ventana();
                }
                this.$vs.loading.close();
            } catch (err) {
                this.$vs.loading.close();
                /**FORBIDDEN ERROR */
                this.$vs.notify({
                    title: "Error",
                    text: "Ha ocurrido un error al tratar de cargar el catálogo de cotizaciones predefinidas, por favor reintente.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "warning",
                    time: 4000,
                });
            }
        },
        async get_planes_funerarios() {
            try {
                this.$vs.loading();
                let res = await planes.get_planes(false, "");
                //creamos las diferentes cotizaciones
                this.cotizaciones = [];
                res.data.forEach((element) => {
                    if (element.precios.length > 0 && element.secciones.length > 0) {
                        //creamos el objeto con la informacion de esta cotizacion
                        let secciones = [];
                        element.secciones.forEach((seccion) => {
                            let conceptos = [];
                            seccion.conceptos.forEach((concepto) => {
                                conceptos.push(concepto.concepto)
                            });
                            secciones.push({
                                seccion: seccion.seccion,
                                conceptos: conceptos
                            });
                        });
                        let financiamientos = [];
                        element.precios.forEach((financiamiento) => {
                            financiamientos.push({
                                financiamiento: financiamiento.tipo_financiamiento,
                                costo_neto: financiamiento.costo_neto,
                                pago_inicial: financiamiento.pago_inicial,
                                pago_mensual: financiamiento.pago_mensual
                            })
                        });
                        //se agrega al cotizaciones object
                        this.cotizaciones.push({
                            tipo: this.tipoCotizacion,
                            label: element.plan,
                            secciones: secciones,
                            financiamientos: financiamientos,
                        });
                    }
                });
                this.$vs.loading.close();
                if (this.cotizaciones.length > 0) {
                    this.form.cotizacion = this.cotizaciones.length > 1 ? this.cotizaciones[1] : this.cotizaciones[0];
                } else {
                    this.$vs.notify({
                        title: "Error",
                        text: "Verifique los presupuestos predefinidos (Costos y Secciones de conceptos contenidos).",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        position: "bottom-right",
                        time: "9000",
                    });
                    this.cerrar_ventana();
                }
            } catch (error) {
                /**error al cargar vendedores */
                this.$vs.notify({
                    title: "Error",
                    text: "Ha ocurrido un error al tratar de cargar el catálogo de cotizaciones predefinidas, por favor reintente.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                    position: "bottom-right",
                    time: "9000",
                });
                this.$vs.loading.close();
                this.cerrar_ventana();
            }
        },
        cerrar_ventana() {
            this.$emit("closeCotizaciones");
            return;
        },
        seleccionarCotizacion() {
            //agregar
            //creamos el arreglo para cargar la cotización
            this.$emit("agregarCotizacion", this.form.cotizacion);
            this.$vs.notify({
                title: "Cotizaciones",
                text: ("Se agregó el " + this.form.cotizacion.label).toUpperCase(),
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "success",
                time: 8000,
            });
        }
    },
    created() { },
    mounted() {
        this.$refs["cotizaciones_predefinidas"].$el.querySelector(".vs-icon").onclick =
            () => {
                this.cerrar_ventana();
            };
        //cerrando el confirmar con esc
        document.body.addEventListener("keyup", (e) => {
            if (e.keyCode === 27) {
                if (this.showVentana) {
                    //CIERRO EL CONFIRMAR AL PRESONAR ESC
                    //this.cancelar();
                }
            }
        });
    },
};
</script>
