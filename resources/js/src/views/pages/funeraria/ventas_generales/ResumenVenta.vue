<template>
    <div class="flex flex-wrap">
        <div class="w-full">
            <div class="flex flex-wrap">
                <div class="w-full xl:w-12/12 px-2">
                    <div class="flex flex-wrap">
                        <div class="p-4 w-full mx-auto rounded-lg size-base border-gray-solid-1 rounded-lg">
                            <div class="flex flex-wrap color-copy">
                                <div class="w-full xl:w-2/12 px-2 text-center">
                                    <span class="font-medium color-black-700">
                                        Fecha de la venta</span>

                                    <div class="w-full uppercase">
                                        {{ datosVenta.fecha_operacion_texto }}
                                    </div>
                                </div>
                                <div class="w-full xl:w-7/12 px-2 text-center">
                                    <span class="font-medium color-black-700">
                                        Nombre del Cliente</span>
                                    <div class="w-full uppercase">
                                        {{ datosVenta.nombre }}
                                    </div>
                                </div>
                                <div class="w-full xl:w-3/12 px-2 text-center">
                                    <span class="font-medium color-black-700"> Vendedor</span>
                                    <div class="w-full uppercase" v-if="datosVenta.venta_general">
                                        {{ datosVenta.venta_general.vendedor.nombre }}
                                    </div>
                                </div>
                                <div class="w-full py-6 px-2 text-center">
                                    <vs-table class="tabla-datos" :data="datosVenta.conceptos_temporales"
                                        noDataText="No se han agregado Artículos ni Servicios">
                                        <template slot="header">
                                            <h3>Artículos que Incluye la Venta</h3>
                                        </template>
                                        <template slot="thead">
                                            <vs-th>#</vs-th>
                                            <vs-th>Descripción</vs-th>
                                            <vs-th>Cant.</vs-th>
                                            <vs-th>Costo Neto</vs-th>
                                            <vs-th>Descuento</vs-th>
                                            <vs-th>Costo Neto Con Descuento</vs-th>
                                            <vs-th>Importe</vs-th>
                                            <vs-th>Facturable</vs-th>
                                        </template>
                                        <template slot-scope="{ data }">
                                            <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                                <vs-td class="">
                                                    <div>
                                                        <span>{{ indextr + 1 }}</span>
                                                    </div>
                                                </vs-td>
                                                <vs-td class="">
                                                    <div class="uppercase">
                                                        {{ data[indextr].descripcion }}
                                                    </div>
                                                </vs-td>

                                                <vs-td class="">
                                                    {{ data[indextr].cantidad }}
                                                </vs-td>
                                                <vs-td class="">
                                                    $
                                                    {{
                                                        data[indextr].costo_neto_normal
                                                        | numFormat("0,000.00")
                                                    }}
                                                </vs-td>
                                                <vs-td class="">
                                                    <vs-switch disabled class="ml-auto mr-auto" color="success"
                                                        icon-pack="feather" v-model="data[indextr].descuento_b">
                                                        <span slot="on">SI</span>
                                                        <span slot="off">NO</span>
                                                    </vs-switch>
                                                </vs-td>
                                                <vs-td class="">
                                                    $
                                                    {{
                                                        data[indextr].costo_neto_descuento
                                                        | numFormat("0,000.00")
                                                    }}
                                                </vs-td>

                                                <vs-td class="">
                                                    <div v-if="data[indextr].descuento_b == 1">
                                                        $
                                                        {{
                                                            (data[indextr].costo_neto_descuento *
                                                                data[indextr].cantidad)
                                                            | numFormat("0,000.00")
                                                        }}
                                                    </div>
                                                    <div v-else>
                                                        $
                                                        {{
                                                            (data[indextr].costo_neto_normal *
                                                                data[indextr].cantidad)
                                                            | numFormat("0,000.00")
                                                        }}
                                                    </div>
                                                </vs-td>

                                                <vs-td class="">
                                                    <vs-switch class="ml-auto mr-auto" color="success" disabled
                                                        icon-pack="feather" v-model="data[indextr].facturable_b">
                                                        <span slot="on">SI</span>
                                                        <span slot="off">NO</span>
                                                    </vs-switch>
                                                </vs-td>
                                            </vs-tr>
                                        </template>
                                    </vs-table>
                                </div>
                                <div class="w-full px-2 pb-2 text-center">
                                    <span class="font-medium color-black-700"> Nota</span>
                                    <div class="w-full uppercase">
                                        {{ datosVenta.nota }}
                                    </div>
                                </div>
                                <div class="w-full md:w-3/12 px-2 text-center">
                                    <span class="font-medium color-black-700">
                                        $ Total de la Venta</span>
                                    <div class="w-full uppercase">
                                        $
                                        {{ datosVenta.total | numFormat("0,000.00") }}
                                    </div>
                                </div>
                                <div class="w-full md:w-3/12 px-2 text-center">
                                    <span class="font-medium color-black-700">
                                        $ Total Pagado</span>
                                    <div class="w-full uppercase">
                                        $
                                        {{ datosVenta.total_cubierto | numFormat("0,000.00") }}
                                    </div>
                                </div>

                                <div class="w-full md:w-3/12 px-2 text-center">
                                    <span class="font-medium color-black-700">
                                        $ Saldo Pendiente</span>
                                    <div class="w-full uppercase">
                                        $
                                        {{ datosVenta.saldo | numFormat("0,000.00") }}
                                    </div>
                                </div>
                                <div class="w-full md:w-3/12 px-2 text-center">
                                    <span class="font-medium color-black-700">
                                        Estatus Actual</span>
                                    <div class="w-full uppercase" v-if="datosVenta.venta_general">
                                        {{ datosVenta.venta_general.status_entregado_texto }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import vSelect from "vue-select";
import funeraria from "@services/funeraria";
export default {
    components: {
        "v-select": vSelect
    },
    props: {
        id_venta: {
            type: Number,
            required: true,
            default: 0,
        },
    },
    watch: {
        id_venta: function (newValue, oldValue) {
            (async () => {
                this.$vs.loading();
                try {
                    let res = await funeraria.get_ventas_gral(
                        null,
                        false,
                        newValue
                    );
                    this.datosVenta = res.data[0];
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
                                time: 4000,
                            });
                        } else {
                            this.$vs.notify({
                                title: "Error",
                                text: "Ha ocurrido un error al tratar de cargar la información solicitada, por favor reintente.",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "danger",
                                position: "bottom-right",
                                time: "9000",
                            });
                        }
                    }
                }
            })();
        },
    },
    computed: {
    },
    data() {
        return {
            datosVenta: [],
        };
    },
    methods: {
    },
    created() { },
    mounted() {
    },
};
</script>
