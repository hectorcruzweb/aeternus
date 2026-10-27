// Un solo formateador para todas las cifras del resumen; conserva los centavos.
const formatoMoneda = new Intl.NumberFormat("es-MX", {
  style: "currency", currency: "MXN", minimumFractionDigits: 0, maximumFractionDigits: 2,
});
export function moneda(valor) {
  if (valor === null || valor === undefined || valor === "" || !Number.isFinite(Number(valor))) return "—";
  return formatoMoneda.format(Number(valor));
}
