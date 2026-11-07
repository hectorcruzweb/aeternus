<template>
  <div class="interface-clock" role="timer" aria-live="off">
    <!-- El footer aplica xl:block a la raíz; la fila interna mantiene su flex. -->
    <div class="interface-clock__line">
    <svg class="interface-clock__icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>
    <span class="interface-clock__date">{{ formattedDate }}</span>
    <span class="interface-clock__divider" aria-hidden="true"></span>
    <span class="interface-clock__time">{{ formattedTime }}<span class="interface-clock__period">{{ dayPeriod }}</span></span>
    </div>
  </div>
</template>

<script>
export default {
  name: "Clock",
  data() {
    return { time: new Date(), timer: null };
  },
  computed: {
    // Conservar la hora local del navegador, como el reloj original.
    formattedDate() {
      const date = this.time.toLocaleDateString("es-MX", {
        weekday: "short", day: "numeric", month: "long", year: "numeric",
      });
      return date.charAt(0).toUpperCase() + date.slice(1);
    },
    // Separar el período para darle menor tamaño sin interpretar cadenas por posición.
    timeParts() {
      return new Intl.DateTimeFormat("es-MX", {
        hour: "2-digit", minute: "2-digit", second: "2-digit", hourCycle: "h12",
      }).formatToParts(this.time);
    },
    formattedTime() {
      return this.timeParts.filter(part => ["hour", "minute", "second"].includes(part.type))
        .map(part => part.value).join(":");
    },
    dayPeriod() {
      const period = this.timeParts.find(part => part.type === "dayPeriod");
      return period ? period.value : "";
    },
  },
  mounted() {
    // Actualización cada segundo; liberar el intervalo al salir del componente.
    this.timer = setInterval(() => { this.time = new Date(); }, 1000);
  },
  beforeDestroy() { clearInterval(this.timer); },
};
</script>

<style scoped>
/* La fila interna evita conflictos con hidden/xl:block heredados del footer. */
.interface-clock{max-width:100%;font-family:Arial,sans-serif;line-height:1.4}
.interface-clock__line{display:flex;flex-wrap:nowrap;align-items:center;justify-content:flex-end;gap:12px;white-space:nowrap}
.interface-clock__icon{display:block;width:22px;height:22px;flex:none;color:#a58828}
.interface-clock__date{color:#707880;font-size:14px;line-height:22px}
.interface-clock__divider{display:block;height:21px;width:1px;flex:none;background:#ccd1d5;margin:0 3px}
.interface-clock__time{display:inline-flex;align-items:baseline;gap:7px;white-space:nowrap;color:#354047;font-size:16px;line-height:22px;font-weight:600;font-variant-numeric:tabular-nums}
.interface-clock__period{color:#a58828;font-size:12px;font-weight:500}
@media(max-width:575px){.interface-clock__line{gap:7px}.interface-clock__date{font-size:11px}.interface-clock__time{font-size:13px}.interface-clock__icon{width:18px;height:18px}.interface-clock__divider{margin:0 1px}}
</style>
