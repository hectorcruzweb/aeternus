<template>
    <div>
        <div class="text-right buttons-container-header">
            <vs-button class="w-full md:w-auto   md:ml-2 md:mt-0" color="success"
                @click="OpenFormularioCotizaciones('agregar')">
                <span>Hacer Cotización</span>
            </vs-button>
        </div>
        <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
            <vx-card no-radius title="Filtros de selección" refresh-content-action @refresh="reset"
                :collapse-action="false">
                <div class="flex flex-wrap">
                    <div class="w-full sm:w-6/12 lg:w-3/12 px-2 input-text">
                        <label class="">Mostrar</label>
                        <v-select :options="mostrarOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="mostrar" class="w-full" />
                    </div>
                    <div class="w-full sm:w-6/12 lg:w-3/12 input-text px-2">
                        <label class="">Número de Control</label>
                        <vs-input class="w-full" icon="search" maxlength="14"
                            placeholder="Filtrar por Número de Control" v-model="serverOptions.numero_control"
                            v-on:keyup.enter="get_data(1)" v-on:blur="get_data(1, 'blur')" />
                    </div>
                    <div class="w-full  lg:w-6/12 input-text px-2">
                        <label class="">Nombre del Cliente</label>
                        <vs-input class="w-full" icon="search" placeholder="Filtrar por Nombre del Cliente"
                            v-model="serverOptions.cliente" v-on:keyup.enter="get_data(1)"
                            v-on:blur="get_data(1, 'blur')" maxlength="75" />
                    </div>
                </div>
            </vx-card>
        </div>
        <br>
        <vs-table :sst="true" :max-items="serverOptions.per_page.value" :data="ventas" noDataText="0 Resultados"
            class="tabla-datos">
            <template slot="header">
                <h3>Listado de Cotizaciones</h3>
            </template>
            <template slot="thead">
                <vs-th>Núm. Cotización</vs-th>
                <vs-th>Cliente</vs-th>
                <vs-th>Fecha Elaboración</vs-th>
                <vs-th>Fecha de Vencimiento</vs-th>
                <vs-th>Tel. / Celular</vs-th>
                <vs-th>Estatus</vs-th>
                <vs-th>Acciones</vs-th>
            </template>
            <template slot-scope="{ data }">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td :data="data[indextr].ventas_generales_id">
                        <span class="font-semibold">{{
                            data[indextr].ventas_generales_id
                        }}</span>
                    </vs-td>
                    <vs-td :data="data[indextr].nombre">
                        {{ data[indextr].nombre }}
                    </vs-td>
                    <vs-td :data="data[indextr].fecha_operacion_texto">
                        <span class="font-medium">
                            {{ data[indextr].fecha_operacion_texto }}
                        </span>
                    </vs-td>
                    <vs-td :data="data[indextr].total">
                        <span class="font-medium">
                            $
                            {{ data[indextr].total | numFormat("0,000.00") }}
                        </span>
                    </vs-td>
                    <vs-td :data="data[indextr].saldo">
                        <span class="font-medium">
                            $
                            {{ data[indextr].saldo | numFormat("0,000.00") }}
                        </span>
                    </vs-td>
                    <vs-td>
                        <p v-if="data[indextr].status_texto == 'Cancelada'">
                            {{ data[indextr].status_texto }}
                            <span class="dot-danger"></span>
                        </p>
                        <p v-else-if="data[indextr].status_texto == 'Por Pagar'">
                            {{ data[indextr].status_texto }}
                            <span class="dot-warning"></span>
                        </p>
                        <p v-else-if="data[indextr].status_texto == 'Pagada'">
                            {{ data[indextr].status_texto }}
                            <span class="dot-success"></span>
                        </p>
                    </vs-td>
                    <vs-td :data="data[indextr].id">
                        <div class="flex justify-center">
                            <img v-if="data[indextr].operacion_status == 0" class="img-btn-22 mx-3"
                                src="@assets/images/deliver-disabled.svg" title="Hacer Entrega de Venta"
                                @click="openEntregarVenta(data[indextr])" />
                            <img v-else-if="
                                data[indextr].operacion_status >= 1 &&
                                data[indextr].venta_general.entregado_b == 0
                            " class="img-btn-22 mx-3" src="@assets/images/deliver-yes.svg"
                                title="Hacer Entrega de Venta" @click="openEntregarVenta(data[indextr])" />
                            <img v-else class="img-btn-22 mx-3" src="@assets/images/deliver-no.svg"
                                title="Esta venta ya fue entregada." @click="openEntregarVenta(data[indextr])" />
                            <img class="cursor-pointer img-btn-20 mx-3" src="@assets/images/folder.svg"
                                title="Expediente" @click="ConsultarVenta(data[indextr].ventas_generales_id)" />
                            <img class=" img-btn-22 mx-3" src="@assets/images/edit.svg" title="Modificar Venta"
                                @click="openModificar(data[indextr])" />
                            <img v-if="data[indextr].operacion_status >= 1" class="img-btn-22 mx-3"
                                src="@assets/images/trash.svg" title="Cancelar Venta"
                                @click="cancelarVenta(data[indextr].ventas_generales_id)" />
                            <img v-else class="img-btn-22 mx-3" src="@assets/images/trash-open.svg"
                                title="Esta venta ya fue cancelada, puede hacer click aquí para consultar"
                                @click="ConsultarVentaAcuse(data[indextr].ventas_generales_id)" />
                        </div>
                    </vs-td>
                    <template class="expand-user" slot="expand"></template>
                </vs-tr>
            </template>
        </vs-table>
        <div>
            <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual" class="mt-8"></vs-pagination>
        </div>
        <!--Componentes-->
        <FormularioCotizaciones :id_venta="id_venta" :tipo="tipoFormulario" :show="verFormularioCotizaciones"
            @closeVentana="reloadList">
        </FormularioCotizaciones>
    </div>
</template>
<script>
import FormularioCotizaciones from "@pages/cotizaciones/FormularioCotizaciones";
import { mostrarOptions } from "@/VariablesGlobales";
import vSelect from "vue-select";
export default {
    components: {
        "v-select": vSelect,
        FormularioCotizaciones
    },
    watch: {
    },
    data() {
        return {
            mostrarOptions: mostrarOptions,
            serverOptions: {
                page: "",
                per_page: "",
                status: "",
                filtro_especifico_opcion: "",
                numero_control: "",
                cliente: "",
            },
            tipoFormulario: "",
            verFormularioCotizaciones: false
        }
    },
    methods: {
        reset(card) {
            card.removeRefreshAnimation(500);
        },
        OpenFormularioCotizaciones(tipo) {
            this.tipoFormulario = tipo;
            this.verFormularioCotizaciones = true;
        },
    },
    created(
    ) {
        this.OpenFormularioCotizaciones("agregar");
    }
};
</script>
