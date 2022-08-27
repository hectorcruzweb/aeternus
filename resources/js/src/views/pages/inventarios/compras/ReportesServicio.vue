<template >
  <div class="centerx">
    <vs-popup
      class="forms-popup popup-80"
      title="expediente de servicio funerario"
      :active.sync="showVentana"
      ref="lista_reportes"
    >
      <div class="pb-6">
        <div class="flex flex-wrap">
          <div class="w-full">
            <vs-table
              :data="documentos"
              noDataText="0 Resultados"
              class="tabla-datos"
            >
              <template slot="header">
                <h3>Documentos de la Compra</h3>
              </template>
              <template slot="thead">
                <vs-th>#</vs-th>
                <vs-th>Documento</vs-th>
                <vs-th>Seleccionar Documento</vs-th>
              </template>
              <template>
                <vs-tr
                  v-for="(documento, index_documento) in documentos"
                  v-bind:key="documento.id"
                >
                  <vs-td>
                    <span class="font-bold">{{ index_documento + 1 }}</span>
                  </vs-td>
                  <vs-td>
                    <span class="">{{ documento.documento }}</span>
                  </vs-td>
                  <vs-td>
                    <img
                      v-if="documento.tipo == 'pdf'"
                      class="cursor-pointer img-btn-24 mx-2"
                      src="@assets/images/pdf.svg"
                      title="Consultar Documento"
                      @click="
                        openReporte(documento.documento, documento.url, '', '')
                      "
                    />
                  </vs-td>
                </vs-tr>
              </template>
            </vs-table>
          </div>
        </div>
      </div>

      <Reporteador
        :header="'consultar documentos de venta de propiedad'"
        :show="openReportesLista"
        :listadereportes="ListaReportes"
        :request="request"
        @closeReportes="openReportesLista = false"
      ></Reporteador>
    </vs-popup>
  </div>
</template>
<script>
import Reporteador from "@pages/Reporteador";
import funeraria from "@services/funeraria";
export default {
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
  },
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.$refs["lista_reportes"].$el.querySelector(".vs-icon").onclick =
          () => {
            this.cancelar();
          };
      } else {
        /**cerrar ventana */
      }
    },
  },
  computed: {
    showVentana: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      },
    },
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
  mounted() {
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