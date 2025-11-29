<template>
    <div class="dashboard">
        <div class="parte-1">
            <ServiciosFunerarios />
            <div class="inventario">
                <div class="seccion-title">
                    <h2 class="">Estado del Inventario</h2>
                    <div class="">
                        <span class="text-white">Reporte General</span>
                    </div>
                </div>
                <div class="reportes">
                    <div class="lista">
                        <div class="reporte">
                            <div class="imagen bg-primary-400">
                                <img class="" src="@assets/images/coffin.svg" />
                            </div>
                            <div>
                                <p class="cantidad">150</p>
                                <p class="descripcion">Conceptos</p>
                            </div>
                        </div>
                        <div class="reporte">
                            <div class="imagen bg-danger-400">
                                <img class="" src="@assets/images/low.svg" />
                            </div>
                            <div>
                                <p class="cantidad">150</p>
                                <p class="descripcion">Desabastecido</p>
                            </div>
                        </div>
                        <div class="reporte">
                            <div class="imagen bg-warning-400">
                                <img class="" src="@assets/images/alto.svg" />
                            </div>
                            <div>
                                <p class="cantidad">150</p>
                                <p class="descripcion">Sobrestock</p>
                            </div>
                        </div>
                        <div class="reporte">
                            <div class="imagen bg-success-400">
                                <img
                                    class=""
                                    src="@assets/images/dollar_bill.svg"
                                />
                            </div>
                            <div>
                                <p class="cantidad">$150</p>
                                <p class="descripcion">$Costo Total</p>
                            </div>
                        </div>
                    </div>
                    <div class="imagen">
                        <img
                            class=""
                            src="@assets/images/conserviciosfunerarios.svg"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="parte-2">
            <div class="reporte-venta reporte-select">
                <div class="seccion-title w-full flex flex-col gap-2">
                    <div
                        class="w-full flex flex-wrap items-center justify-between"
                    >
                        <h2 class="h4 font-medium one-line-text">
                            Desgloce de Ventas por Año
                        </h2>
                        <div class="">
                            <span class="">Descargar Reporte</span>
                        </div>
                    </div>
                    <div class="selectores">
                        <div class="tipo_operacion">
                            <button
                                class="operacion"
                                :class="{
                                    'is-active':
                                        filtro_ventas_operaciones ===
                                        'cementerio',
                                }"
                                @click="cambiarGraficaVentas('cementerio')"
                            >
                                <span>Cementerio</span>
                            </button>
                            <button
                                class="operacion"
                                :class="{
                                    'is-active':
                                        filtro_ventas_operaciones === 'cuotas',
                                }"
                                @click="cambiarGraficaVentas('cuotas')"
                            >
                                <span>Cuotas</span>
                            </button>
                            <button
                                class="operacion"
                                :class="{
                                    'is-active':
                                        filtro_ventas_operaciones === 'planes',
                                }"
                                @click="cambiarGraficaVentas('planes')"
                            >
                                <span>Planes</span>
                            </button>
                            <button
                                class="operacion"
                                :class="{
                                    'is-active':
                                        filtro_ventas_operaciones ===
                                        'servicios',
                                }"
                                @click="cambiarGraficaVentas('servicios')"
                            >
                                <span>Servicios</span>
                            </button>
                            <button
                                class="operacion"
                                :class="{
                                    'is-active':
                                        filtro_ventas_operaciones ===
                                        'ventas_gral',
                                }"
                                @click="cambiarGraficaVentas('ventas_gral')"
                            >
                                <span>Ventas en Gral.</span>
                            </button>
                        </div>
                        <div class="flex justify-end w-full">
                            <v-select
                                :options="years"
                                :clearable="false"
                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                v-model="year"
                                class="ajustar-width"
                            >
                            </v-select>
                        </div>
                    </div>
                </div>
                <div class="graficas">
                    <div class="grafica">
                        <apexchart
                            type="bar"
                            height="400"
                            :options="chartOptions"
                            :series="series"
                        ></apexchart>
                    </div>
                </div>
            </div>
            <div class="reporte-venta reporte-select">
                <div class="seccion-title w-full flex flex-col gap-2">
                    <div
                        class="w-full flex flex-wrap items-center justify-between"
                    >
                        <h2 class="h4 font-medium one-line-text">
                            Desgloce de Adeudos
                        </h2>
                        <div class="">
                            <span class="">Descargar Reporte</span>
                        </div>
                    </div>
                    <div class="selectores">
                        <div class="tipo_operacion">
                            <button class="operacion">
                                <span>Cementerio</span>
                            </button>
                            <button class="operacion">
                                <span>Cuotas</span>
                            </button>
                            <button class="operacion">
                                <span>Planes</span>
                            </button>
                            <button class="operacion">
                                <span>Servicios</span>
                            </button>
                            <button class="operacion">
                                <span>Ventas en Gral.</span>
                            </button>
                        </div>
                        <div class="flex justify-end w-full">
                            <v-select
                                :options="years"
                                :clearable="false"
                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                v-model="year"
                                class="ajustar-width"
                            >
                            </v-select>
                        </div>
                    </div>
                </div>
                <div class="graficas">
                    <div class="grafica">
                        <apexchart
                            type="bar"
                            height="400"
                            :options="chartOptions"
                            :series="series"
                        ></apexchart>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import vSelect from "vue-select";
import ServiciosFunerarios from "./dashboard/ServiciosFunerarios.vue";
export default {
    name: "Dashboard",
    components: {
        "v-select": vSelect,
        ServiciosFunerarios,
    },
    data() {
        return {
            years: [],
            year: null,
            filtro_ventas_operaciones: "",
            seriesOri: [
                {
                    name: "Ventas",
                    data: [15, 22, 28, 35, 40, 38, 45, 50, 48, 42, 39, 55],
                },
                {
                    name: "Ingresos",
                    data: [
                        12000, 15000, 18000, 20000, 22000, 21000, 24000, 26000,
                        25000, 23000, 22500, 28000,
                    ],
                },
            ],
            series: [],
            chartOptions: {
                title: {
                    text: "Ventas e Ingresos Anuales",
                    align: "center",
                    floating: false,
                    offsetY: 10, // mueve el título hacia abajo
                    style: {
                        fontSize: "16px",
                        fontWeight: "bold",
                        color: "#222",
                    },
                },
                chart: {
                    toolbar: { show: false },
                },
                colors: ["#B18B1E", "#222"],
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ["#fff"],
                },
                plotOptions: {
                    bar: {
                        borderRadius: 0,
                        columnWidth: "50%",
                    },
                },
                xaxis: {
                    categories: [
                        "Ene",
                        "Feb",
                        "Mar",
                        "Abr",
                        "May",
                        "Jun",
                        "Jul",
                        "Ago",
                        "Sep",
                        "Oct",
                        "Nov",
                        "Dic",
                    ],
                },
                yaxis: [
                    {
                        title: { text: "Ventas" },
                    },
                    {
                        opposite: true,
                        title: { text: "Ingresos ($) MXN" },
                        labels: {
                            formatter: (value) => "$" + value.toLocaleString(),
                        },
                    },
                ],
                tooltip: {
                    //shared: true,
                    y: {
                        formatter: (value) => {
                            // Si es Venta (número pequeño) solo devuélvelo
                            if (value < 1000) return value;
                            // Si es Ingreso formatear como dinero
                            return "$" + value.toLocaleString() + " MXN";
                        },
                    },
                },
                legend: {
                    position: "bottom",
                },
            },
        };
    },
    methods: {
        cambiarGraficaVentas(tipo) {
            this.series = this.seriesOri;
            this.filtro_ventas_operaciones = tipo;
            this.series = [];
            if (tipo === "cementerio") {
                this.series.push(this.seriesOri[0]);
            } else if (tipo === "cuotas") {
                this.series.push(this.seriesOri[1]);
            } else {
                // Show all
                this.series = this.seriesOri;
            }
        },
    },

    created() {
        const currentYear = new Date().getFullYear();
        for (let i = 0; i <= 10; i++) {
            this.years.push(currentYear - i);
        }
        this.year = currentYear;
    },
    mounted() {},
};
</script>
