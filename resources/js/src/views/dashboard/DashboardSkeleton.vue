<template>
  <section class="dashboard-skeleton" :class="'dashboard-skeleton--' + variante"
    role="status" :aria-label="'Cargando ' + titulo" aria-busy="true">
    <span class="dashboard-skeleton__accesible">Cargando {{ titulo }}…</span>
    <article v-for="card in cantidadCards" :key="card" class="dashboard-skeleton__card">
      <header class="dashboard-skeleton__cabecera" aria-hidden="true">
        <span class="dashboard-skeleton__barra dashboard-skeleton__barra--titulo"></span>
        <span class="dashboard-skeleton__barra dashboard-skeleton__barra--accion"></span>
      </header>
      <div class="dashboard-skeleton__datos" aria-hidden="true">
        <div v-for="indicador in cantidadIndicadores" :key="indicador" class="dashboard-skeleton__indicador">
          <span class="dashboard-skeleton__icono"></span>
          <span class="dashboard-skeleton__barra dashboard-skeleton__barra--etiqueta"></span>
          <span class="dashboard-skeleton__barra dashboard-skeleton__barra--valor"></span>
          <span class="dashboard-skeleton__barra dashboard-skeleton__barra--detalle"></span>
        </div>
      </div>
      <div v-if="variante === 'grafica'" class="dashboard-skeleton__grafica" aria-hidden="true">
        <span v-for="columna in 10" :key="columna" :style="{ height: (25 + columna % 4 * 18) + '%' }"></span>
      </div>
      <span v-if="variante === 'inventario'" aria-hidden="true"
        class="dashboard-skeleton__barra dashboard-skeleton__barra--pie"></span>
    </article>
  </section>
</template>

<script>
// Estructura compartida: no contiene datos, importes, permisos ni acciones reales.
export default {
  name: "DashboardSkeleton",
  props: {
    variante: { type: String, default: "resumen" },
    titulo: { type: String, default: "información" },
  },
  computed: {
    cantidadCards() { return this.variante === "pendientes" ? 3 : this.variante === "seguimientos" ? 4 : 1; },
    cantidadIndicadores() {
      if (this.variante === "pendientes" || this.variante === "seguimientos") return 1;
      return this.variante === "servicios" || this.variante === "inventario" ? 4 : 3;
    },
  },
};
</script>
