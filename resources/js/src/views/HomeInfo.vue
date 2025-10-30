<template>
  <div>
    <div class="bg-golden-elegant hidden lg:flex">
      <h3 class="text-copy h5">
        Accesos Directos
      </h3>
      <div class="accesos items-center justify-start lg:justify-between">
        <div class="quick-access-btn" @click="$router.push('/clientes/control-de-seguimientos')">
          <img src="@assets/images/dashboard/accesos/seguimientos.svg" alt="Control de Seguimientos" class="btn-icon" />
          <span class="btn-label">Control de Seguimientos</span>
        </div>
        <div class="quick-access-btn" @click="$router.push('/funeraria/cotizaciones')">
          <img src="@assets/images/dashboard/accesos/cotizaciones.svg" alt="Cotizaciones" class="btn-icon" />
          <span class="btn-label">Hacer Cotizaciones</span>
        </div>
        <div class="quick-access-btn" @click="$router.push('/cementerio/ventas')">
          <img src="@assets/images/dashboard/accesos/ventas_cementerio.svg" alt="Ventas en Cementerio"
            class="btn-icon" />
          <span class="btn-label">Ventas en Cementerio</span>
        </div>
        <div class="quick-access-btn" @click="$router.push('/funeraria/ventas_planes')">
          <img src="@assets/images/dashboard/accesos/ventas_planes.svg" alt="Venta a Futuro" class="btn-icon" />
          <span class="btn-label">Venta a Futuro</span>
        </div>
        <div class="quick-access-btn" @click="$router.push('/funeraria/servicios')">
          <img src="@assets/images/dashboard/accesos/servicios_funerarios.svg" alt="Servicio Funerario"
            class="btn-icon" />
          <span class="btn-label">Servicio Funerario</span>
        </div>
        <div class="quick-access-btn" @click="$router.push('/funeraria/ventas_generales')">
          <img src="@assets/images/dashboard/accesos/ventas_gral.svg" alt="Ventas en General" class="btn-icon" />
          <span class="btn-label">Ventas en General</span>
        </div>
        <div class="quick-access-btn" @click="$router.push('/cobranza/facturacion')">
          <img src="@assets/images/dashboard/accesos/facturacion.svg" alt="Portal de Facturación" class="btn-icon" />
          <span class="btn-label">Portal de Facturación</span>
        </div>
        <div class="quick-access-btn" @click="$router.push('/control-de-asistencia')">
          <img src="@assets/images/dashboard/accesos/checador.svg" alt="Reloj Checador" class="btn-icon" />
          <span class="btn-label">Reloj Checador</span>
        </div>
      </div>
      <!-- Add more buttons as needed -->
    </div>
    <div class="mt-5 flex flex-col flex-1 bg-white">

    </div>
    <!--
    <div class="flex flex-wrap">
      <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
        <div class="text-center mt-32">
          <img src="@assets/images/logo/aeternus-siiga.png" alt="login"
            class="w-6/12 sm:w-12/12 md:w-1/12 lg:w-1/12 xl:w-4/12" />

          <h1 class="text-textos_theme sistema-title uppercase">
            SIIGA | Aeternus Funerales
          </h1>
          <h2 class="mt-3 text-lg text-textos_theme font-light text-center sistema-descripcion">
            Sistema Integral de Información y Gerencia Administrativa
          </h2>
          <p class="mt-3 text-sm text-textos_theme text-center">
            ©2020 Todos los Derechos Reservados
          </p>
        </div>
      </div>
    </div>-->
  </div>
</template>
<script>
export default {
  data() {
    return {};
  },
  components: {},
  created() { }
};
</script>
<style lang="scss" scoped>
@import "../../../sass/vuexy/variables";

/* Center the container on the page */
/* Golden container */
.bg-golden-elegant {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  padding: .5rem 1rem 1rem 1rem;
  background: #ffffff;
  /* clean white background */
  box-shadow:
    0 2px 4px rgba(0, 0, 0, 0.06),
    0 8px 18px rgba(0, 0, 0, 0.08);
  /* elegant, subtle shadow */
  gap: 0.75rem;
}

.accesos {
  display: flex;
  flex-wrap: wrap;
  gap: .5rem;
}


.quick-access-btn:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.12);
}

/* Individual button styling */
.quick-access-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  /* centers image inside the button */
  background: #fff;
  border: 1px solid #e0e0e0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  /* slightly translucent */
  padding: 0.5rem;
  border-radius: 0.75rem;
  cursor: pointer;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  width: 86px;
  height: 86px;
  max-width: 86px;
  max-height: 86px;
  text-align: center;
  box-sizing: content-box;
}

/* Button image */
.btn-icon {
  width: 32px;
  height: 36px;
  object-fit: contain;
  margin-bottom: 0.25rem;
  /* leave space for label */
  flex-shrink: 0;
}

/* Button label */
.btn-label {
  font-size: .90rem;
  font-weight: 500;
  color: $content-color;
  /* readable over golden background */
  line-height: 1.25rem;
  white-space: normal;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

/* Hover effect */
.quick-access-btn:hover {
  background: #fef3c7;
  /* soft golden highlight */
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}

/* Active / click effect */
.quick-access-btn:active {
  background: #fde68a;
  /* slightly darker golden */
  transform: translateY(0);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}
</style>
