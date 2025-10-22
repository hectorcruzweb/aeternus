<template>
  <div class="centerx">
    <vs-popup :class="['forms-popup popup-80', z_index]" title="expediente de servicio funerario" :active="localShow"
      :ref="this.$options.name">
      <div class="pb-6">
        <div class="flex flex-wrap">
          <div class="w-full">
            <vs-table :data="documentos" noDataText="0 Resultados" class="tabla-datos">
              <template slot="header">
                <h3>Documentos de la Compra</h3>
              </template>
              <template slot="thead">
                <vs-th>#</vs-th>
                <vs-th>Documento</vs-th>
                <vs-th>Seleccionar Documento</vs-th>
              </template>
              <template>
                <vs-tr v-for="(documento, index_documento) in documentos" v-bind:key="documento.id">
                  <vs-td>
                    <span class="font-bold">{{ index_documento + 1 }}</span>
                  </vs-td>
                  <vs-td>
                    <span class="">{{ documento.documento }}</span>
                  </vs-td>
                  <vs-td>
                    <img v-if="documento.tipo == 'pdf'" class="cursor-pointer img-btn-24 mx-2"
                      src="@assets/images/pdf.svg" title="Consultar Documento" @click="
                        openReporte(documento.documento, documento.url, '', '')
                        " />
                  </vs-td>
                </vs-tr>
              </template>
            </vs-table>
          </div>
        </div>
      </div>

      <Reporteador v-if="openReportesLista" :header="'consultar documentos de venta de propiedad'"
        :show="openReportesLista" :listadereportes="ListaReportes" :request="request"
        @closeReportes="openReportesLista = false"></Reporteador>
    </vs-popup>
  </div>
</template>
<script>
import Reporteador from "@pages/Reporteador";
import funeraria from "@services/funeraria";
export default {
  name: "ReportesServicio",
  components: {
    Reporteador,
  },
  props: {
    verAcuse: {
      type: Boolean,
      required: false,
      default: false,
    },
    show: {
      type: Boolean,
      required: true,
    },
    id_compra: {
      type: Number,
      required: true,
    },
    z_index: {
      type: String,
      required: false,
      default: "z-index58k",
    },
  },
  watch: {
    show: {
      immediate: true, // runs when component is mounted too
      async handler(newValue) {
        if (newValue) {
          this.$popupManager.register(this, this.cancelar, "input");
        } else {
          this.$popupManager.unregister(this.$options.name);
        }
        this.localShow = newValue;
      },
    },
  },
  computed: {
    get_compra_id: {
      get() {
        return this.id_compra;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  data() {
    return {
      localShow: false,
      referencia: "",
      documentos: [
        {
          documento: "Nota de Compra",
          url: "/inventario/pdf_nota_compra",
          tipo: "pdf",
        },
      ],
      total: 0 /**rows que se van a remplazar el click en el evento de las tablas para modificar el expand */,
      funcion_reemplazada: [],
      ListaReportes: [],
      request: {
        id_pago: "",
        id_compra: "",
        email: "",
        destinatario: "",
      },
      openReportesLista: false,
    };
  },
  methods: {
    cancelar() {
      this.$emit("closeListaReportes");
      return;
    },
    openReporte(nombre_reporte = "", link = "", parametro = "", tipo = "") {
      this.ListaReportes = [];
      this.ListaReportes.push({
        nombre: nombre_reporte,
        url: link,
      });
      //estado de cuenta
      this.request.email = "";
      this.request.id_compra = this.get_compra_id;
      this.request.destinatario = "";
      this.openReportesLista = true;
      this.$vs.loading.close();
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

<style lang="stylus">
.con-expand-users {
  width: 100%;

  .con-btns-user {
    display: flex;
    padding: 10px;
    padding-bottom: 0px;
    align-items: center;
    justify-content: space-between;

    .con-userx {
      display: flex;
      align-items: center;
      justify-content: flex-start;
    }
  }

  .list-icon {
    i {
      font-size: 0.9rem !important;
    }
  }
}
</style>