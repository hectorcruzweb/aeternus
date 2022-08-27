<template >
  <div class="centerx">
    <!--ordenando primero los tipode propiedad que son linales, tipo uniplex, nicho,duplex, triplex, cuqdriplex sin terraza-->
    <div v-if="getDatosAreas.tipo_propiedades_id!=4">
      <div class="flex flex-wrap">
        <div class="w-full px-2">
          <table class="distribucion tabla-otras-propiedades">
            <tr>
              <td class="bg-primary bold text-white">Módulo</td>
              <td class="bg-primary bold text-white">Títular</td>
              <td class="bg-primary bold text-white">Núm. Convenio</td>
            </tr>
            <tr :key="index_fila" v-for="(fila, index_fila) in getDatosAreas.filas">
              <td
                :class="[disposicionTerreno((getDatosAreas.filas-index_fila),1)[0]]"
                @click="cargarOpciones(
                  disposicionTerreno((getDatosAreas.filas-index_fila),1)
                )"
              >{{getDatosAreas.filas-index_fila}}</td>
              <td
                :class="[disposicionTerreno((getDatosAreas.filas-index_fila),1)[0]]"
                @click="cargarOpciones(
                  disposicionTerreno((getDatosAreas.filas-index_fila),1)
                )"
              >
                <span>{{get_titular((getDatosAreas.filas+1)-fila,1)[0]}}</span>
              </td>
              <td
                :class="[disposicionTerreno((getDatosAreas.filas-index_fila),1)[0]]"
                @click="cargarOpciones(
                  disposicionTerreno((getDatosAreas.filas-index_fila),1)
                )"
              >
                <span>{{get_titular((getDatosAreas.filas+1)-fila,1)[1]}}</span>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div v-else>
      <!--incia el dibujo de el area segun sus filas y lotes-->
      <div class="flex flex-wrap">
        <div class="w-full px-2">
          <table class="distribucion">
            <tr :key="index_fila" v-for="(fila, index_fila) in getDatosAreas.filas">
              <td
                class="bg-primary bold text-white letra-fila"
              >{{alfabeto[getDatosAreas.filas-fila]}}</td>
              <td
                :key="index_columna"
                v-for="(columna, index_columna) in getDatosAreas.columnas"
                :class="[disposicionTerreno((getDatosAreas.filas-index_fila),(getDatosAreas.columnas-index_columna))[0]]"
                @click="cargarOpciones(
                  disposicionTerreno((getDatosAreas.filas-index_fila),(getDatosAreas.columnas-index_columna))
                )"
              >
                <span
                  v-if="get_titular((getDatosAreas.filas+1)-fila,(getDatosAreas.columnas-index_columna))[0]!=''"
                >
                  <vx-tooltip
                    :text="'Titular: '+get_titular((getDatosAreas.filas+1)-fila,(getDatosAreas.columnas-index_columna))[0]"
                  >{{(getDatosAreas.columnas+1)-columna}}</vx-tooltip>
                </span>
                <span v-else>{{(getDatosAreas.columnas+1)-columna}}</span>
              </td>
              <td
                class="bg-primary bold text-white letra-fila"
              >{{alfabeto[getDatosAreas.filas-fila]}}</td>
            </tr>
          </table>
        </div>
      </div>
      <!--fin de dibujo de mapa-->
    </div>
    <ReportesVentas
      :show="openReportes"
      @closeListaReportes="openReportes=false;id_venta=0"
      :id_venta="id_venta"
    ></ReportesVentas>
  </div>
</template>
<script>
import ReportesVentas from "./ventas/ReportesVentas";
/**VARIABLES GLOBALES */
import { alfabeto } from "@/VariablesGlobales";
export default {
  components: {
    ReportesVentas
  },
  props: {
    datosAreas: {
      required: true
    }
  },
  watch: {},
  computed: {
    getDatosAreas: {
      get() {
        return this.datosAreas;
      },
      set(newValue) {
        return newValue;
      }
    }
  },
  data() {
    return {
      openReportes: false,
      id_venta: 0,
      datosVacios: [],
      alfabeto: alfabeto
    };
  },
  methods: {
    /**obtener el titular basado en la ubicacion */
    get_titular(fila, lote) {
      for (let index = 0; index < this.getDatosAreas.ventas.length; index++) {
        if (
          this.getDatosAreas.ventas[index].fila_raw == fila &&
          this.getDatosAreas.ventas[index].lote_raw == lote
        ) {
          return [
            this.getDatosAreas.ventas[index].cliente["nombre"],
            this.getDatosAreas.ventas[index].numero_convenio
          ];

          break;
        }
      }
      return ["", ""];
    },
    /**verificando y determinando los colores, segun disponibilidad en terrenos */
    disposicionTerreno: function(fila, lote) {
      /**verificar si es area valida, que no sea area no utilizada del area seleccionada */
      let existe = true;
      this.getDatosAreas.filas_columnas.forEach(element => {
        if (element.fila == fila) {
          existe = true;
          //aqui tengo los valores para saber en que columna empieza y acaba cada fila
          if (
            !(lote >= element.empieza_columna && lote <= element.fin_columna)
          ) {
            /**no existe este terreno */
            existe = false;
          }
        }
      });

      let id_venta = "";
      if (existe == true) {
        let esta = false;

        for (let index = 0; index < this.getDatosAreas.ventas.length; index++) {
          if (
            this.getDatosAreas.ventas[index].fila_raw == fila &&
            this.getDatosAreas.ventas[index].lote_raw == lote
          ) {
            id_venta = this.getDatosAreas.ventas[index].id;
            esta = true;
            break;
          }
        }

        if (esta == true) {
          /**vendido */
          return ["cursor-pointer bg-danger", 1, id_venta];
        } else {
          /**disponible */
          return ["cursor-pointer bg-success hover:bg-primary", 2, ""];
        }
      } else {
        /**espacio muerto */
        return ["bg-espacio-muerto", 0, ""];
      }
    },
    cargarOpciones(dato) {
      if (dato[2] != "") {
        this.openReportes = true;
        this.id_venta = dato[2];
      }
    }
  },
  created() {}
};
</script>