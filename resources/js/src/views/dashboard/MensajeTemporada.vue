<template>
  <div class="dashboard-temporada-contenedor">
    <!-- Control de pruebas: activado explícitamente por el programador mediante una prop. -->
    <aside
      v-if="controlesDesarrollo && esEscritorio"
      class="dashboard-temporada-dev"
      aria-label="Pruebas de temporada"
    >
      <label for="dashboard-temporada-selector">Temporada · Desarrollo</label>
      <select
        id="dashboard-temporada-selector"
        v-model="temporadaSeleccionada"
        :disabled="!habilitado"
      >
        <option value="" disabled>Selecciona una temporada</option>
        <option
          v-for="temporada in temporadas"
          :key="temporada.id"
          :value="temporada.id"
        >
          {{ temporada.titulo
          }}{{ temporada.activa === false ? " (desactivada)" : "" }}
        </option>
      </select>
      <button
        type="button"
        :disabled="!habilitado || !temporadaSeleccionada"
        @click="previsualizarTemporada"
      >
        Ver prueba
      </button>
      <small>Vista manual sin cierre automático ni registro en sesión.</small>
    </aside>
    <!-- Presentación breve con identidad de marca; no modifica las cards del dashboard. -->
    <transition
      name="dashboard-aviso-temporada"
      @after-enter="enfocarCierre"
      @after-leave="restaurarFoco"
    >
      <div
        v-if="visible && esEscritorio && temporadaActual"
        class="dashboard-aviso-temporada-capa"
        @click.self="cerrar"
        @keydown.esc.stop.prevent="cerrar"
        @keydown.tab.prevent="enfocarCierre"
      >
        <section
          class="dashboard-aviso-temporada"
          :class="'dashboard-aviso-temporada--' + decoracionActual"
          role="dialog"
          aria-modal="true"
          :aria-label="temporadaActual.titulo"
          aria-describedby="dashboard-temporada-mensaje"
        >
          <!-- Decoración ligera, sin eventos ni temporizadores adicionales. -->
          <div class="dashboard-aviso-temporada__decoracion" aria-hidden="true">
            <span
              v-for="indice in 8"
              :key="indice"
              class="dashboard-aviso-temporada__particula"
              :style="estiloParticula(indice - 1)"
            >
              <span
                v-if="decoracionActual === 'flores'"
                class="dashboard-aviso-temporada__flor"
              >
                <i
                  v-for="petalo in 8"
                  :key="petalo"
                  :style="{
                    transform:
                      'rotate(' + (petalo - 1) * 45 + 'deg) translateY(-7px)',
                  }"
                ></i
                ><b></b>
              </span>
              <span
                v-else-if="decoracionActual === 'nieve'"
                class="dashboard-aviso-temporada__copo"
                ><i></i><i></i><i></i
              ></span>
              <HeartIcon
                v-else-if="decoracionActual === 'corazones'"
                size="22"
              />
              <span
                v-else-if="['hojas', 'ramo'].includes(decoracionActual)"
                class="dashboard-aviso-temporada__hoja"
              ></span>
              <span
                v-else-if="['patria', 'papalotes'].includes(decoracionActual)"
                class="dashboard-aviso-temporada__banderin"
                :class="{
                  'dashboard-aviso-temporada__banderin--patrio':
                    decoracionActual === 'patria',
                }"
              ></span>
              <svg
                v-else-if="decoracionActual === 'huellas'"
                viewBox="0 0 120 180"
                width="24"
                height="30"
                fill="currentColor"
                focusable="false"
              >
                <ellipse cx="60" cy="112" rx="26" ry="22" />
                <ellipse
                  cx="24"
                  cy="77"
                  rx="11"
                  ry="16"
                  transform="rotate(-25 24 77)"
                />
                <ellipse
                  cx="47"
                  cy="60"
                  rx="11"
                  ry="16"
                  transform="rotate(-8 47 60)"
                />
                <ellipse
                  cx="74"
                  cy="60"
                  rx="11"
                  ry="16"
                  transform="rotate(8 74 60)"
                />
                <ellipse
                  cx="97"
                  cy="77"
                  rx="11"
                  ry="16"
                  transform="rotate(25 97 77)"
                />
              </svg>
              <StarIcon v-else size="22" />
            </span>
          </div>
          <!-- Ilustraciones vectoriales locales: cambian con la temporada. -->
          <div
            class="dashboard-aviso-temporada__ilustraciones"
            aria-hidden="true"
          >
            <svg
              v-for="lado in 2"
              :key="lado"
              :class="'dashboard-aviso-temporada__escena--' + lado"
              viewBox="0 0 120 180"
              focusable="false"
            >
              <g v-if="decoracionActual === 'flores'">
                <path
                  d="M30 160 Q70 120 66 64 M65 110 Q28 115 22 88 Q55 84 65 110 M64 133 Q100 133 105 103 Q73 105 64 133"
                  fill="#7f9359"
                  stroke="#7f9359"
                  stroke-width="3"
                />
                <g transform="translate(66 55)">
                  <ellipse
                    v-for="n in 12"
                    :key="n"
                    cy="-18"
                    rx="10"
                    ry="20"
                    :transform="'rotate(' + n * 30 + ')'"
                    fill="#e7a12b"
                    opacity=".9"
                  />
                  <circle r="13" fill="#b97424" />
                  <circle r="7" fill="#f5c052" />
                </g>
                <path d="M14 164 V125 H42 V164" fill="#eee0bd" />
                <path
                  d="M28 121 C9 108 31 93 28 84 C48 109 40 119 28 121"
                  fill="#e9a132"
                />
                <path
                  d="M28 116 Q20 108 29 100 Q36 110 28 116"
                  fill="#ffe6a0"
                />
              </g>
              <g v-else-if="decoracionActual === 'nieve'">
                <path
                  d="M60 27 L29 76 H43 L19 111 H36 L10 149 H110 L84 111 H101 L77 76 H91 Z"
                  fill="#678774"
                />
                <path d="M54 149 H66 V171 H54Z" fill="#947a52" />
                <circle cx="44" cy="90" r="5" fill="#c29b3c" />
                <circle cx="72" cy="119" r="6" fill="#b86f62" />
                <circle cx="42" cy="137" r="5" fill="#e4d8ad" />
                <path
                  d="M60 10 L64 20 L75 20 L66 27 L69 38 L60 31 L51 38 L54 27 L45 20 L56 20Z"
                  fill="#c4a24b"
                />
              </g>
              <g v-else-if="decoracionActual === 'corazones'" fill="#bb7c83">
                <path d="M60 125 C-10 80 22 30 60 65 C98 30 130 80 60 125Z" />
                <path d="M60 125 V165" stroke="#bba46a" stroke-width="3" />
              </g>
              <g v-else-if="decoracionActual === 'papalotes'">
                <path d="M60 30 L95 75 L60 115 L25 75Z" fill="#d3ad51" />
                <path
                  d="M60 30 V115 M25 75 H95 M60 115 Q25 140 60 170"
                  stroke="#99834e"
                  stroke-width="2"
                  fill="none"
                />
                <path d="M43 140 L63 133 L60 150Z" fill="#8aaca7" />
              </g>
              <g v-else-if="decoracionActual === 'patria'">
                <path d="M14 35 V165" stroke="#bca064" stroke-width="3" />
                <path d="M16 40 H45 V110 H16Z" fill="#77947d" />
                <path d="M45 40 H74 V110 H45Z" fill="#f4eedc" />
                <path d="M74 40 H103 V110 H74Z" fill="#ba7d76" />
                <circle cx="59" cy="75" r="6" fill="#b89b57" />
              </g>
              <g v-else-if="['hojas', 'ramo'].includes(decoracionActual)">
                <path
                  d="M60 165 V50 M60 115 Q20 115 16 75 Q55 75 60 115 M60 140 Q102 140 106 100 Q65 100 60 140 M60 85 Q28 85 30 49 Q59 55 60 85"
                  fill="#8a9b73"
                  stroke="#8a9b73"
                  stroke-width="3"
                />
                <g
                  v-if="decoracionActual === 'ramo'"
                  transform="translate(64 45)"
                  fill="#c98c96"
                >
                  <ellipse
                    v-for="n in 6"
                    :key="n"
                    cy="-14"
                    rx="10"
                    ry="16"
                    :transform="'rotate(' + n * 60 + ')'"
                  />
                  <circle r="9" fill="#e6c16b" />
                </g>
              </g>
              <g v-else-if="decoracionActual === 'huellas'" fill="#a88a56">
                <ellipse cx="60" cy="112" rx="26" ry="22" />
                <ellipse
                  cx="24"
                  cy="77"
                  rx="11"
                  ry="16"
                  transform="rotate(-25 24 77)"
                />
                <ellipse
                  cx="47"
                  cy="60"
                  rx="11"
                  ry="16"
                  transform="rotate(-8 47 60)"
                />
                <ellipse
                  cx="74"
                  cy="60"
                  rx="11"
                  ry="16"
                  transform="rotate(8 74 60)"
                />
                <ellipse
                  cx="97"
                  cy="77"
                  rx="11"
                  ry="16"
                  transform="rotate(25 97 77)"
                />
                <path
                  d="M60 158 C35 144 43 132 60 142 C77 132 85 144 60 158Z"
                  fill="#c8988c"
                />
              </g>
              <g
                v-else
                fill="none"
                stroke="#c3a044"
                stroke-width="3"
                stroke-linecap="round"
              >
                <path
                  d="M60 24 V48 M60 88 V112 M16 68 H40 M80 68 H104 M28 36 L45 53 M75 83 L92 100 M28 100 L45 83 M75 53 L92 36"
                />
                <circle cx="60" cy="68" r="9" fill="#e4ca77" />
                <path
                  d="M34 130 L39 143 L53 144 L42 153 L45 167 L34 159 L22 167 L25 153 L14 144 L28 143Z"
                  fill="#f0dfab"
                />
                <circle cx="90" cy="143" r="7" />
              </g>
            </svg>
          </div>
          <button
            ref="botonCerrar"
            type="button"
            class="dashboard-aviso-temporada__cerrar"
            aria-label="Cerrar mensaje de temporada"
            @click="cerrar"
          >
            <XIcon size="16" aria-hidden="true" />
          </button>
          <img
            class="dashboard-aviso-temporada__logotipo"
            :src="logotipo"
            alt="Aeternus Funerales"
            width="231"
            height="83"
          />
          <div class="dashboard-aviso-temporada__titulo">
            <span class="dashboard-aviso-temporada__icono" aria-hidden="true"
              ><component :is="temporadaActual.icono" size="21"
            /></span>
            <h2>{{ temporadaActual.titulo }}</h2>
          </div>
          <p class="dashboard-aviso-temporada__fecha">{{ fechaCelebracion }}</p>
          <p
            id="dashboard-temporada-mensaje"
            class="dashboard-aviso-temporada__mensaje"
          >
            {{ temporadaActual.mensaje }}
          </p>
          <small class="dashboard-aviso-temporada__nota">{{
            vistaPrevia
              ? "Vista de prueba · Cierra con la X o Escape"
              : "Este mensaje se cerrará automáticamente"
          }}</small>
        </section>
      </div>
    </transition>
  </div>
</template>

<script>
import { SunIcon, HeartIcon, StarIcon, XIcon } from "vue-feather-icons";

// Calendario: ventanas amplias para fechas clave; el resto, solo el día.
// fechaCelebracion conserva la fecha real aunque el aviso empiece antes.
const TEMPORADAS = [
  // El recuerdo de mascotas tiene prioridad sobre el aviso general de Muertos el 27/oct.
  {
    id: "recuerdo-mascotas",
    desde: { mes: 10, dia: 27 },
    hasta: { mes: 10, dia: 27 },
    titulo: "En memoria de nuestras mascotas",
    mensaje:
      "Sus huellas permanecen en la familia. Honramos a los animales de compañía que partieron y el amor que nos dejaron.",
    icono: "HeartIcon",
    decoracion: "huellas",
    prioridad: 100,
  },
  {
    id: "dia-perro",
    desde: { mes: 7, dia: 21 },
    hasta: { mes: 7, dia: 21 },
    titulo: "Día Mundial del Perro",
    mensaje:
      "Su lealtad deja recuerdos que nos acompañan toda la vida. Honramos el vínculo con nuestros compañeros de cuatro patas.",
    icono: "HeartIcon",
    decoracion: "huellas",
    prioridad: 0,
  },
  {
    id: "dia-gato",
    desde: { mes: 8, dia: 8 },
    hasta: { mes: 8, dia: 8 },
    titulo: "Día Internacional del Gato",
    mensaje:
      "Su compañía forma parte de nuestra historia. Recordamos con cariño a los gatos que dejaron huella en nuestras familias.",
    icono: "HeartIcon",
    decoracion: "huellas",
    prioridad: 0,
  },
  {
    id: "dia-animales",
    desde: { mes: 10, dia: 4 },
    hasta: { mes: 10, dia: 4 },
    titulo: "Día Mundial de los Animales",
    mensaje:
      "Respetar la vida también es cuidar a los animales. Reconocemos su compañía y el lugar que ocupan en nuestros recuerdos.",
    icono: "HeartIcon",
    decoracion: "huellas",
    prioridad: 0,
  },
  {
    id: "dia-muertos",
    desde: { mes: 10, dia: 24 },
    hasta: { mes: 11, dia: 2 },
    titulo: "Día de Muertos",
    mensaje:
      "Acompañamos a las familias a honrar la vida y la memoria de quienes siguen presentes en su corazón.",
    icono: "SunIcon",
    decoracion: "flores",
  },
  {
    id: "navidad",
    desde: { mes: 12, dia: 18 },
    hasta: { mes: 12, dia: 25 },
    titulo: "Navidad",
    mensaje:
      "En estas fechas, acompañamos con respeto a quienes extrañan a un ser querido. Que encuentren paz y cercanía.",
    icono: "HeartIcon",
    decoracion: "nieve",
  },
  {
    id: "anio-nuevo",
    desde: { mes: 12, dia: 28 },
    hasta: { mes: 1, dia: 5 },
    titulo: "Año Nuevo",
    mensaje:
      "Renovamos nuestro compromiso de acompañar a cada familia con dignidad, respeto y calidez.",
    icono: "StarIcon",
    decoracion: "estrellas",
  },
  {
    id: "reyes",
    desde: { mes: 1, dia: 6 },
    hasta: { mes: 1, dia: 6 },
    titulo: "Día de Reyes",
    mensaje:
      "Los recuerdos compartidos en familia son un legado que permanece. Hoy acompañamos con cercanía y esperanza.",
    icono: "StarIcon",
    decoracion: "estrellas",
  },
  {
    id: "amor-amistad",
    desde: { mes: 2, dia: 14 },
    hasta: { mes: 2, dia: 14 },
    titulo: "Día del Amor y la Amistad",
    mensaje:
      "El amor y la amistad dejan huellas que permanecen. Acompañamos a las familias a honrar esos vínculos.",
    icono: "HeartIcon",
    decoracion: "corazones",
  },
  {
    id: "infancia",
    desde: { mes: 4, dia: 30 },
    hasta: { mes: 4, dia: 30 },
    titulo: "Día del Niño y de la Niña",
    mensaje:
      "Acompañar a una familia también es escuchar y cuidar a su infancia con sensibilidad, respeto y cariño.",
    icono: "HeartIcon",
    decoracion: "papalotes",
  },
  {
    id: "madres",
    desde: { mes: 5, dia: 4 },
    hasta: { mes: 5, dia: 10 },
    titulo: "Día de las Madres",
    mensaje:
      "Honramos el amor de las madres y acompañamos con especial cercanía a quienes hoy viven su ausencia.",
    icono: "HeartIcon",
    decoracion: "ramo",
    fechaCelebracion: "10 de mayo",
  },
  {
    id: "padres",
    desde: { mes: 6, dia: 1 },
    hasta: { mes: 6, dia: 1 },
    titulo: "Día del Padre",
    mensaje:
      "Honramos la vida y el legado de los padres, y acompañamos a las familias que conservan su recuerdo.",
    icono: "HeartIcon",
    decoracion: "hojas",
    regla: "tercer-domingo-junio",
    diasPrevios: 6,
  },
  {
    id: "personas-mayores",
    desde: { mes: 8, dia: 22 },
    hasta: { mes: 8, dia: 28 },
    titulo: "Día de las Personas Adultas Mayores",
    mensaje:
      "Escuchar su historia y cuidar su dignidad es honrar su legado. Acompañamos a cada generación con respeto.",
    icono: "HeartIcon",
    decoracion: "hojas",
    fechaCelebracion: "28 de agosto",
  },
  {
    id: "patrias",
    desde: { mes: 9, dia: 16 },
    hasta: { mes: 9, dia: 16 },
    titulo: "Fiestas Patrias",
    mensaje:
      "Honramos nuestras raíces y la tradición de acompañarnos como comunidad, con respeto a cada familia.",
    icono: "StarIcon",
    decoracion: "patria",
    fechaCelebracion: "16 de septiembre",
  },
  {
    id: "semana-santa",
    desde: { mes: 3, dia: 1 },
    hasta: { mes: 3, dia: 1 },
    titulo: "Semana Santa",
    mensaje:
      "Un tiempo de reflexión para honrar la vida y acompañar con respeto las creencias de cada familia.",
    icono: "SunIcon",
    decoracion: "ramo",
    regla: "domingo-pascua",
    activa: false,
  },
  {
    id: "guadalupe",
    desde: { mes: 12, dia: 12 },
    hasta: { mes: 12, dia: 12 },
    titulo: "Día de la Virgen de Guadalupe",
    mensaje:
      "Acompañamos con respeto a las familias que encuentran consuelo y esperanza en su fe y sus tradiciones.",
    icono: "HeartIcon",
    decoracion: "ramo",
    activa: false,
  },
];
// Respaldo en memoria para navegadores que no permitan sessionStorage.
const mostradosEnMemoria = new Set();

export default {
  name: "MensajeTemporada",
  components: { SunIcon, HeartIcon, StarIcon, XIcon },
  props: {
    modoDesarrollador: { type: Boolean, default: false },
    habilitado: { type: Boolean, default: true },
    logotipo: { type: String, default: "/images/logo/aeternus.jpg" },
    duracion: {
      type: Number,
      default: 4000,
      validator: (valor) => Number.isFinite(valor) && valor > 0,
    },
    unaVezPorSesion: { type: Boolean, default: true },
    fechaReferencia: { type: Date, default: () => new Date() },
    temporadas: {
      type: Array,
      default: () =>
        TEMPORADAS.map((t) => ({
          ...t,
          desde: { ...t.desde },
          hasta: { ...t.hasta },
        })),
    },
  },
  data() {
    return {
      visible: false,
      vistaPrevia: false,
      temporadaSeleccionada: "",
      esEscritorio: false,
      estaMontado: false,
      mostradoEnInstancia: false,
    };
  },
  computed: {
    // La fecha celebrada es distinta de la ventana en que se muestra el aviso.
    fechaCelebracion() {
      const temporada = this.temporadaActual;
      if (!temporada) return "";
      const fechas = {
        "dia-muertos": "1 y 2 de noviembre",
        navidad: "24 y 25 de diciembre",
        "anio-nuevo": "31 de diciembre y 1 de enero",
        patrias: "15 y 16 de septiembre",
      };
      if (temporada.fechaCelebracion) return temporada.fechaCelebracion;
      if (fechas[temporada.id]) return fechas[temporada.id];
      const meses = [
        "enero",
        "febrero",
        "marzo",
        "abril",
        "mayo",
        "junio",
        "julio",
        "agosto",
        "septiembre",
        "octubre",
        "noviembre",
        "diciembre",
      ];
      const inicio = temporada.desde,
        fin = temporada.hasta;
      if (!inicio || !fin) return "";
      if (inicio.mes === fin.mes && inicio.dia === fin.dia)
        return inicio.dia + " de " + meses[inicio.mes - 1];
      if (inicio.mes === fin.mes)
        return inicio.dia + " al " + fin.dia + " de " + meses[inicio.mes - 1];
      return (
        inicio.dia +
        " de " +
        meses[inicio.mes - 1] +
        " al " +
        fin.dia +
        " de " +
        meses[fin.mes - 1]
      );
    },
    controlesDesarrollo() {
      return this.modoDesarrollador;
    },
    decoracionActual() {
      const decoracion =
        this.temporadaActual && this.temporadaActual.decoracion;
      return [
        "flores",
        "nieve",
        "estrellas",
        "corazones",
        "papalotes",
        "ramo",
        "hojas",
        "patria",
        "huellas",
      ].includes(decoracion)
        ? decoracion
        : "estrellas";
    },
    temporadaActual() {
      if (this.vistaPrevia && this.controlesDesarrollo) {
        return (
          this.resolverTemporada(
            this.temporadas.find((t) => t.id === this.temporadaSeleccionada)
          ) || null
        );
      }
      if (Number.isNaN(this.fechaReferencia.getTime())) return null;
      const hoy =
        (this.fechaReferencia.getMonth() + 1) * 100 +
        this.fechaReferencia.getDate();
      return (
        this.temporadas
          .map(this.resolverTemporada)
          .sort((a, b) => ((b && b.prioridad) || 0) - ((a && a.prioridad) || 0))
          .find((t) => {
            if (!t || t.activa === false || !t.desde || !t.hasta) return false;
            const inicio = t.desde.mes * 100 + t.desde.dia;
            const fin = t.hasta.mes * 100 + t.hasta.dia;
            return inicio <= fin
              ? hoy >= inicio && hoy <= fin
              : hoy >= inicio || hoy <= fin;
          }) || null
      );
    },
    claveSesion() {
      const t = this.temporadaActual;
      if (!t) return null;
      const hoy =
        (this.fechaReferencia.getMonth() + 1) * 100 +
        this.fechaReferencia.getDate();
      const inicio = t.desde.mes * 100 + t.desde.dia;
      const fin = t.hasta.mes * 100 + t.hasta.dia;
      // Los rangos que cruzan diciembre/enero pertenecen a una misma temporada.
      const anio =
        this.fechaReferencia.getFullYear() -
        (inicio > fin && hoy <= fin ? 1 : 0);
      return "aeternus:dashboard:temporada:" + t.id + ":" + anio;
    },
  },
  watch: {
    controlesDesarrollo(valor) {
      if (!valor && this.vistaPrevia) this.cerrar();
    },
    habilitado(valor) {
      if (valor) this.intentarMostrar();
      else this.cerrar();
    },
  },
  created() {
    this._temporizadorTemporada = null;
    this._focoAnteriorTemporada = null;
    this._consultaEscritorio = null;
  },
  mounted() {
    // Un ancho de 1024px es el umbral de escritorio; móvil no consume la sesión.
    this._consultaEscritorio = window.matchMedia("(min-width: 1024px)");
    this.esEscritorio = this._consultaEscritorio.matches;
    if (this._consultaEscritorio.addEventListener)
      this._consultaEscritorio.addEventListener(
        "change",
        this.actualizarEscritorio
      );
    else this._consultaEscritorio.addListener(this.actualizarEscritorio);
    this.estaMontado = true;
    this.intentarMostrar();
  },
  beforeDestroy() {
    if (this._consultaEscritorio) {
      if (this._consultaEscritorio.removeEventListener)
        this._consultaEscritorio.removeEventListener(
          "change",
          this.actualizarEscritorio
        );
      else this._consultaEscritorio.removeListener(this.actualizarEscritorio);
    }
    this.limpiarTemporizador();
    this.restaurarFoco();
  },
  methods: {
    // La prueba no marca ni borra sessionStorage y permite fechas desactivadas.
    previsualizarTemporada() {
      if (
        !this.controlesDesarrollo ||
        !this.estaMontado ||
        !this.esEscritorio ||
        !this.habilitado
      )
        return;
      if (!this.temporadas.some((t) => t.id === this.temporadaSeleccionada))
        return;
      this.limpiarTemporizador();
      if (typeof document !== "undefined")
        this._focoAnteriorTemporada = document.activeElement;
      this.vistaPrevia = true;
      this.visible = true;
    },
    // Normaliza las fechas móviles sin alterar el calendario recibido por props.
    resolverTemporada(temporada) {
      if (!temporada || !temporada.regla) return temporada;
      const anio = this.fechaReferencia.getFullYear();
      if (temporada.regla === "tercer-domingo-junio") {
        const dia = 1 + ((7 - new Date(anio, 5, 1).getDay()) % 7) + 14;
        return {
          ...temporada,
          desde: { mes: 6, dia: dia - (temporada.diasPrevios || 0) },
          hasta: { mes: 6, dia },
          fechaCelebracion: dia + " de junio",
        };
      }
      if (["semana-santa", "domingo-pascua"].includes(temporada.regla)) {
        // Cómputo gregoriano de Pascua; intervalo de Jueves Santo a Domingo.
        const a = anio % 19,
          b = Math.floor(anio / 100),
          c = anio % 100;
        const d = Math.floor(b / 4),
          e = b % 4,
          f = Math.floor((b + 8) / 25);
        const g = Math.floor((b - f + 1) / 3),
          h = (19 * a + b - d - g + 15) % 30;
        const i = Math.floor(c / 4),
          k = c % 4,
          l = (32 + 2 * e + 2 * i - h - k) % 7;
        const m = Math.floor((a + 11 * h + 22 * l) / 451),
          base = h + l - 7 * m + 114;
        const fin = new Date(anio, Math.floor(base / 31) - 1, (base % 31) + 1);
        const inicio = new Date(fin);
        if (temporada.regla === "semana-santa")
          inicio.setDate(inicio.getDate() - 3);
        return {
          ...temporada,
          desde: { mes: inicio.getMonth() + 1, dia: inicio.getDate() },
          hasta: { mes: fin.getMonth() + 1, dia: fin.getDate() },
        };
      }
      return temporada;
    },
    actualizarEscritorio(evento) {
      this.esEscritorio = evento.matches;
      if (this.esEscritorio) this.intentarMostrar();
      else this.cerrar();
    },
    // Posiciones deterministas: las partículas no cambian de lugar con cada render.
    estiloParticula(indice) {
      const posiciones = [6, 94, 20, 80, 34, 66, 44, 56];
      return {
        left: posiciones[indice] + "%",
        animationDelay: -(indice * 0.27) + "s",
        animationDuration: 2.1 + indice * 0.13 + "s",
      };
    },
    intentarMostrar() {
      // Espera a que termine el loading del dashboard. Sin temporada, no muestra nada.
      if (
        !this.estaMontado ||
        !this.esEscritorio ||
        !this.habilitado ||
        !this.temporadaActual ||
        this.mostradoEnInstancia
      )
        return;
      const clave = this.claveSesion;
      if (this.unaVezPorSesion) {
        if (mostradosEnMemoria.has(clave)) return;
        try {
          if (window.sessionStorage.getItem(clave)) return;
        } catch (error) {
          /* Usar el respaldo en memoria. */
        }
        mostradosEnMemoria.add(clave);
        try {
          window.sessionStorage.setItem(clave, "1");
        } catch (error) {
          /* No bloquear el aviso. */
        }
      }
      this.mostradoEnInstancia = true;
      if (typeof document !== "undefined")
        this._focoAnteriorTemporada = document.activeElement;
      this.visible = true;
      this.limpiarTemporizador();
      this._temporizadorTemporada = setTimeout(this.cerrar, this.duracion);
    },
    // El foco queda en el cierre mientras el aviso es visible y vuelve al terminar.
    enfocarCierre() {
      const boton = this.$refs.botonCerrar;
      if (this.visible && boton) boton.focus();
    },
    restaurarFoco() {
      const elemento = this._focoAnteriorTemporada;
      this._focoAnteriorTemporada = null;
      if (
        typeof document !== "undefined" &&
        elemento &&
        typeof elemento.focus === "function" &&
        document.documentElement.contains(elemento)
      )
        elemento.focus();
    },
    cerrar() {
      this.visible = false;
      this.vistaPrevia = false;
      this.limpiarTemporizador();
    },
    limpiarTemporizador() {
      if (this._temporizadorTemporada !== null) {
        clearTimeout(this._temporizadorTemporada);
        this._temporizadorTemporada = null;
      }
    },
  },
};
</script>
