<template>
    <div class="dashboard">
        <!--<div class="bg-golden-elegant hidden lg:flex hidden">
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
  -->
        <div class="parte-1">
            <div class="servicios-funerarios">Servicios Funerarios</div>
            <div class="inventario">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolores
                sequi dolore quae ipsam laborum aliquid unde quia fuga, dolorum
                voluptatibus consectetur necessitatibus illum nobis possimus
                nesciunt quis eaque aut. Non
            </div>
        </div>
        <div class="hidden">
            <div>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolores
                sequi dolore quae ipsam laborum aliquid unde quia fuga, dolorum
                voluptatibus consectetur necessitatibus illum nobis possimus
                nesciunt quis eaque aut. Non?
            </div>
            <div>
                Eveniet est placeat nihil. Molestiae eius officia corrupti? Quia
                quos est qui aliquam atque eveniet, sed consectetur nihil iusto
                accusamus provident expedita voluptate fugit recusandae labore
                eum quis placeat repudiandae!
            </div>
        </div>
        <div class="hidden">
            <div>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolores
                sequi dolore quae ipsam laborum aliquid unde quia fuga, dolorum
                voluptatibus consectetur necessitatibus illum nobis possimus
                nesciunt quis eaque aut. Non?
            </div>
            <div>
                Eveniet est placeat nihil. Molestiae eius officia corrupti? Quia
                quos est qui aliquam atque eveniet, sed consectetur nihil iusto
                accusamus provident expedita voluptate fugit recusandae labore
                eum quis placeat repudiandae!
            </div>
        </div>
        <div class="hidden">
            <div>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolores
                sequi dolore quae ipsam laborum aliquid unde quia fuga, dolorum
                voluptatibus consectetur necessitatibus illum nobis possimus
                nesciunt quis eaque aut. Non?
            </div>
            <div>
                Eveniet est placeat nihil. Molestiae eius officia corrupti? Quia
                quos est qui aliquam atque eveniet, sed consectetur nihil iusto
                accusamus provident expedita voluptate fugit recusandae labore
                eum quis placeat repudiandae!
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
    --></div>
</template>
<script>
export default {
    name: "Dashboard",
    data() {
        return {
            settings: {
                draggable: true, // Make the slider draggable
                swipe: true, // Enable swipe on touch devices
                arrows: false,
                dots: false,
                autoplay: true,
                infinite: true,
                slidesToShow: 1,
                adaptiveHeight: false,
                autoplaySpeed: 5000,
                //pauseOnDotsHover: true,
                //pauseOnFocus: true,
                //pauseOnHover: true
            },
        };
    },
    methods: {},
    components: {},
    created() {},
};
</script>
<style lang="scss" scoped>
@import "../../../sass/vuexy/variables";

.scroll-styles {
    overflow-y: auto;
    /* 👈 enables vertical scrolling */
    overflow-x: auto;
    /* avoid horizontal scrollbars */
    scrollbar-width: thin;
    /* for Firefox */
    scrollbar-color: $primary #f1f1f1;
    /* gold thumb on light background */
}

.effects {
    background-color: white;
    border: 1px solid #e5e7eb;
    /* Tailwind gray-200 equivalent */
    border-radius: 0.35rem;
    padding: 1rem;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);

    &:hover {
        cursor: pointer;
        box-shadow: 1px 2px 2px 1px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
}

/**
flex: 1 1 0% is shorthand for: 
flex-grow: 1; → the item grows to fill available space.
flex-shrink: 1; → it shrinks when necessary.
flex-basis: 0%; → its initial size before growing/shrinking is 0.
*/
/* Extra small (xs): default — no media query needed */
.dashboard {
    display: grid;
    grid-template-rows: repeat(auto, auto);
    gap: 1rem;
    align-items: start;
    /* 🔥 items inside each cell stick to the TOP */
    @extend .scroll-styles;
    background-color: red;

    > div {
        background-color: yellow;
        display: grid;
        gap: 1rem;

        > div {
            @extend .scroll-styles, .effects;
            padding: 7px;
        }
    }

    .parte-1 {
        grid-template-rows: 1fr 1fr;

        div {
            min-height: 218.625px;
            max-height: 218.625px;
            background-color: green;
            color: white;
            display: flex;
            justify-content: center;
            /* centers horizontally */
            align-items: center;
            /* centers vertically */
        }
    }
}

/* Small (sm) — min-width: 576px */
@media (min-width: 576px) {
}

/* Medium (md) — min-width: 768px */
@media (min-width: 768px) {
}

/* Large (lg) — min-width: 992px */
@media (min-width: 992px) {
    .dashboard {
        grid-template-rows: 218.625px repeat(2, auto);

        .parte-1 {
            grid-template-rows: auto;
            grid-template-columns: 1fr 1fr;
        }
    }
}

/* Extra large (xl) — min-width: 1200px */
@media (min-width: 1200px) {
}

/* Optional: 2XL — Tailwind’s modern default (min-width: 1536px) */
@media (min-width: 1536px) {
    /* Huge screens */
}

/* Center the container on the page */
/* Golden container */
.bg-golden-elegant {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 0.5rem 1rem 1rem 1rem;
    background: #ffffff;
    /* clean white background */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06), 0 8px 18px rgba(0, 0, 0, 0.08);
    /* elegant, subtle shadow */
    gap: 0.75rem;
}

.accesos {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
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
    font-size: 0.9rem;
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
