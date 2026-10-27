<template>
  <!-- No se reciben datos gerenciales para dibujar las áreas restringidas. -->
  <section
    class="dashboard-vista-limitada"
    :style="{
      minHeight: altoDisponible === null ? null : altoDisponible + 'px',
    }"
    aria-label="Vista operativa del dashboard"
  >
    <div class="dashboard__operacion dashboard-vista-limitada__operacion">
      <slot />
      <aside class="dashboard-vista-limitada__aviso">
        <ShieldIcon size="54" aria-hidden="true" />
        <h2>Vista operativa</h2>
        <p>Consulta los servicios en proceso<br />y sus expedientes.</p>
      </aside>
    </div>
    <h2 class="dashboard-vista-limitada__titulo">
      Información disponible según tu perfil
    </h2>
    <div class="dashboard-vista-limitada__secciones">
      <article
        v-for="seccion in secciones"
        :key="seccion.id"
        class="dashboard-vista-limitada__seccion"
        aria-disabled="true"
      >
        <component
          :is="seccion.icono"
          size="32"
          class="dashboard-vista-limitada__icono"
          aria-hidden="true"
        />
        <div>
          <h3>{{ seccion.titulo }}</h3>
          <p>Acceso de gerencia</p>
        </div>
        <LockIcon
          size="15"
          class="dashboard-vista-limitada__candado"
          aria-hidden="true"
        />
      </article>
    </div>
  </section>
</template>
<script>
import {
  ShieldIcon,
  LockIcon,
  PackageIcon,
  DollarSignIcon,
  FileTextIcon,
  UsersIcon,
  BarChart2Icon,
  PieChartIcon,
} from "vue-feather-icons";
export default {
  name: "VistaLimitada",
  components: {
    ShieldIcon,
    LockIcon,
    PackageIcon,
    DollarSignIcon,
    FileTextIcon,
    UsersIcon,
    BarChart2Icon,
    PieChartIcon,
  },
  // Solo etiquetas estáticas: sin cantidades, enlaces ni peticiones adicionales.
  data() {
    return {
      altoDisponible: null,
      secciones: [
        { id: "inventario", titulo: "Inventario", icono: "PackageIcon" },
        { id: "ventas", titulo: "Ventas y cobranza", icono: "DollarSignIcon" },
        { id: "facturacion", titulo: "Facturación", icono: "FileTextIcon" },
        { id: "seguimientos", titulo: "Seguimientos", icono: "UsersIcon" },
        {
          id: "reportes",
          titulo: "Reportes y gráficas",
          icono: "BarChart2Icon",
        },
        { id: "resumen", titulo: "Resumen gerencial", icono: "PieChartIcon" },
      ],
    };
  },
  mounted() {
    this._ajusteAltura = null;
    this.programarAjusteAltura();
    window.addEventListener("resize", this.programarAjusteAltura);
    // Observa el layout y el footer sin cambiar sus estilos ni crear otro scroll.
    if (typeof ResizeObserver !== "undefined") {
      this._observadorAltura = new ResizeObserver(this.programarAjusteAltura);
      this._observadorAltura.observe(this.$el.parentElement);
      const footer = document.querySelector(".the-footer");
      if (footer) this._observadorAltura.observe(footer);
    }
  },
  beforeDestroy() {
    window.removeEventListener("resize", this.programarAjusteAltura);
    if (this._ajusteAltura !== null)
      window.cancelAnimationFrame(this._ajusteAltura);
    if (this._observadorAltura) this._observadorAltura.disconnect();
  },
  methods: {
    programarAjusteAltura() {
      if (this._ajusteAltura !== null) return;
      this._ajusteAltura = window.requestAnimationFrame(() => {
        this._ajusteAltura = null;
        this.ajustarAltura();
      });
    },
    ajustarAltura() {
      if (!this.$el || !this.$el.getBoundingClientRect) return;
      const top =
        this.$el.getBoundingClientRect().top + (window.pageYOffset || 0);
      let espacioInferior = 0;
      let padre = this.$el.parentElement;
      while (padre && padre.id !== "content-area" && padre !== document.body) {
        const estilo = window.getComputedStyle(padre);
        espacioInferior +=
          (parseFloat(estilo.paddingBottom) || 0) +
          (parseFloat(estilo.marginBottom) || 0);
        padre = padre.parentElement;
      }
      const footer = document.querySelector(".the-footer");
      if (footer) {
        const alto = footer.getBoundingClientRect().height;
        espacioInferior =
          window.getComputedStyle(footer).position === "fixed"
            ? Math.max(espacioInferior, alto)
            : espacioInferior + alto;
      }
      const disponible = Math.max(
        0,
        Math.floor(window.innerHeight - top - espacioInferior)
      );
      if (this.altoDisponible !== disponible) this.altoDisponible = disponible;
    },
  },
};
</script>
