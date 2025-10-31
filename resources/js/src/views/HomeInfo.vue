<template>
  <div class="flex flex-col flex-1 gap-2 bg-white">
    <div class="bg-golden-elegant hidden lg:flex hidden">
      <h3 class="text-copy h5">
        Accesos Directos
      </h3>
      <div class="accesos items-center justify-start lg:justify-between">
        <a class="quick-access-btn" href="/clientes/control-de-seguimientos">
          <img src="@assets/images/dashboard/accesos/seguimientos.svg" alt="Control de Seguimientos" class="btn-icon" />
          <span class="btn-label">Control de Seguimientos</span>
        </a>
        <a class="quick-access-btn" href="/funeraria/cotizaciones">
          <img src="@assets/images/dashboard/accesos/cotizaciones.svg" alt="Cotizaciones" class="btn-icon" />
          <span class="btn-label">Hacer Cotizaciones</span>
        </a>
        <a class="quick-access-btn" href="/cementerio/ventas">
          <img src="@assets/images/dashboard/accesos/ventas_cementerio.svg" alt="Ventas en Cementerio"
            class="btn-icon" />
          <span class="btn-label">Ventas en Cementerio</span>
        </a>
        <a class="quick-access-btn" href="/funeraria/ventas_planes">
          <img src="@assets/images/dashboard/accesos/ventas_planes.svg" alt="Venta a Futuro" class="btn-icon" />
          <span class="btn-label">Venta a Futuro</span>
        </a>
        <a class="quick-access-btn" href="/funeraria/servicios">
          <img src="@assets/images/dashboard/accesos/servicios_funerarios.svg" alt="Servicio Funerario"
            class="btn-icon" />
          <span class="btn-label">Servicio Funerario</span>
        </a>
        <a class="quick-access-btn" href="/funeraria/ventas_generales">
          <img src="@assets/images/dashboard/accesos/ventas_gral.svg" alt="Ventas en General" class="btn-icon" />
          <span class="btn-label">Ventas en General</span>
        </a>
        <a class="quick-access-btn" href="/cobranza/facturacion">
          <img src="@assets/images/dashboard/accesos/facturacion.svg" alt="Portal de Facturación" class="btn-icon" />
          <span class="btn-label">Portal de Facturación</span>
        </a>
        <a class="quick-access-btn" href="/control-de-asistencia">
          <img src="@assets/images/dashboard/accesos/checador.svg" alt="Reloj Checador" class="btn-icon" />
          <span class="btn-label">Reloj Checador</span>
        </a>
      </div>
    </div>
    <div class="dashboard flex-1">
      <div class="top">
        <div class="bg-success card">
          1
        </div>
        <div class="bg-success card">
          2
        </div>
        <div class="bg-success card">
          3
        </div>
      </div>
      <div class="bottom bg-danger">
        <div class="bg-success flex-1">
          Bottom Card
        </div>
      </div>
    </div>
  </div>
  <!--
      <vue-slick-carousel class="flex flex-col flex-1 bg-danger" v-bind="settings">
        <div class="bg-success flex-1">
          <div class="bg-success flex-1">
            Slide 1
          </div>
        </div>
        <div class="bg-success flex flex-col flex-1">Slide 2</div>
        <div class="bg-success flex flex-col flex-1">Slide 3</div>
      </vue-slick-carousel>
    -->
</template>
<script>
export default {
  name: "Dashboard",
  data() {
    return {
      settings: {
        draggable: true,      // Make the slider draggable
        swipe: true,          // Enable swipe on touch devices
        arrows: true,
        dots: false,
        autoplay: true,
        infinite: true,
        slidesToShow: 1,
        adaptiveHeight: true,
        autoplaySpeed: 5000,
        //pauseOnDotsHover: true,
        //pauseOnFocus: true,
        //pauseOnHover: true
      },
    };
  },
  methods: {
  },
  components: {},
  created() { }
};
</script>
<style lang="scss" scoped>
.dashboard {
  display: grid;
  /* two equal rows */
  grid-template-rows: 1fr;
  gap: .5rem;
}

/**
flex: 1 1 0% is shorthand for: 
flex-grow: 1; → the item grows to fill available space.
flex-shrink: 1; → it shrinks when necessary.
flex-basis: 0%; → its initial size before growing/shrinking is 0.
*/
/* Extra small (default): no min-width, applies to all */
.top {
  display: grid;
  gap: .5rem;

  .card {
    min-height: 200px;
  }
}

.bottom {
  display: flex;
  flex-direction: column;
  flex: 1 1 0%;
}


/* Small (sm) — min-width: 640px */
@media (min-width: 640px) {}

/* Medium (md) — min-width: 768px */
@media (min-width: 768px) {
  .dashboard {
    grid-template-rows: 200px 1fr;
  }

  .top {
    grid-template-columns: repeat(3, 1fr);
  }

}

/* Large (lg) — min-width: 1024px */
@media (min-width: 1024px) {}

/* Extra Large (xl) — min-width: 1280px */
@media (min-width: 1280px) {}

/* 2XL — min-width: 1536px */
@media (min-width: 1536px) {}





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
