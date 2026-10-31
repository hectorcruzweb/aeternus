/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Formateador monetario compartido por el dashboard.
 * Dónde ajustar: moneda convierte números a formato es-MX. El tamaño de MXN se controla en el marcado y
 * SCSS de cada indicador, no en este formateador.
 * Relacionado: dashboard/*.vue; _dashboard.scss
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */
// Un solo formateador para todas las cifras del resumen; conserva los centavos.
const formatoMoneda = new Intl.NumberFormat("es-MX", {
  style: "currency", currency: "MXN", minimumFractionDigits: 0, maximumFractionDigits: 2,
});
export /**
   * Detalle del método: Devuelve formato monetario es-MX con hasta dos decimales o marcador de dato
   * ausente; el tamaño de MXN se controla en HTML/SCSS.
   * Entradas: valor (valor recibido del control o propiedad observada).
   */
  function moneda(valor) {
  if (valor === null || valor === undefined || valor === "" || !Number.isFinite(Number(valor))) return "—";
  return formatoMoneda.format(Number(valor));
}
