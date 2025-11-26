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
      <div class="servicios-funerarios">
        <vue-slick-carousel class="" v-bind="settings">
          <div v-for="n in 4" :class="['slide-servicio', n % 2 > 0 ? 'servicio-no-contratado' : 'servicio-contratado']"
            :key="n">
            <div class="slide-header">
              <div class="fallecido">
                <h2>
                  SUSANA DEL ROCIO GOMEZ NEGRETE
                </h2>
              </div>
              <div class="velacion">
                <p v-if="n % 2 == 0"><strong>Servicio por definir.</strong></p>
                <p v-else><strong>Velación: </strong> <span class="">Sala La Piedad.</span></p>
                <div class="hidden">
                  <img class="cursor-pointer img-btn-19" src="@assets/images/folder.svg" title="Ver Expediente" />
                  <img class="cursor-pointer img-btn-21" src="@assets/images/whatsapp.svg" title="Compartir Enlace" />
                </div>
                <div class="">
                  <span class="">Ver Expediente</span>
                </div>
              </div>
            </div>
            <div class="slide-content">
              <div class="contenido" v-if="n % 2 > 0">
                <p>Datos del Servicio</p>
                <div class="datos-contenido">
                  <div class="row-dato">
                    <div>
                      <p class="dato bg-primary">si</p>
                      <p class="texto-dato">Cremación</p>
                    </div>
                    <div>
                      <p class="dato bg-primary">
                        si
                      </p>
                      <p class="texto-dato">
                        Inhumación
                      </p>
                    </div>
                  </div>
                  <div class="row-dato">
                    <div>
                      <p class="dato bg-primary">no</p>
                      <p class="texto-dato">Ceremonía Religiosa</p>
                    </div>
                    <div>
                      <p class="dato bg-danger">si</p>
                      <p class="texto-dato">
                        $ Saldo Pendiente
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="contenido-null" v-else>
                <p>En proceso de contratación...</p>
              </div>
              <div class="imagen">
                <img src="@assets/images/luto.svg" />
              </div>
            </div>
          </div>
        </vue-slick-carousel>
      </div>
      <div class="inventario">
        <div class="seccion-title">
          <h2>
            Estado del Inventario
          </h2>
          <div class="">
            <span class="text-primary">Reporte General</span>
          </div>
        </div>
        <div class="reportes">
          <div class="lista">
            <div class="reporte">
              <div class="imagen bg-primary-400">
                <img class="" src="@assets/images/coffin.svg" />
              </div>
              <div>
                <p class="cantidad">150</p>
                <p class="descripcion">Conceptos</p>
              </div>
            </div>
            <div class="reporte">
              <div class="imagen bg-danger-400">
                <img class="" src="@assets/images/low.svg" />
              </div>
              <div>
                <p class="cantidad">150</p>
                <p class="descripcion">Desabastecido</p>
              </div>
            </div>
            <div class="reporte">
              <div class="imagen bg-warning-400">
                <img class="" src="@assets/images/alto.svg" />
              </div>
              <div>
                <p class="cantidad">150</p>
                <p class="descripcion">Sobrestock</p>
              </div>
            </div>
            <div class="reporte">
              <div class="imagen bg-success-400">
                <img class="" src="@assets/images/dollar_bill.svg" />
              </div>
              <div>
                <p class="cantidad">$150</p>
                <p class="descripcion">$Costo Total</p>
              </div>
            </div>
          </div>
          <div class="imagen">
            <img class="" src="@assets/images/ataud.svg" />
          </div>
        </div>
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
    -->
  </div>
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
        dots: true,
        //autoplay: true,
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
  methods: {},
  components: {},
  created() { },
};
</script>
<style lang="scss" scoped>
@import "../../../sass/vuexy/variables";
@import "../../../sass/vuexy/typography";

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

.no-scrollbar {
  overflow-y: hidden;
  overflow-x: hidden;
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

.border {
  border: 0.3px solid black;
}

.one-line-text {
  white-space: nowrap;
  /* forces one single line */
  overflow: hidden;
  /* hides the extra text */
  text-overflow: ellipsis;
  /* adds the … */
}

.servicio-contratado {
  background: linear-gradient(200deg, #CB9F22, #1f1f1f);
}

.servicio-no-contratado {
  background: linear-gradient(200deg, #1f1f1f, #6b7280);
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
  //background-color: red;

  >div {
    //background-color: yellow;
    display: grid;
    gap: 1rem;

    >div {
      @extend .scroll-styles, .effects;
      padding: 21px;
    }
  }

  .parte-1 {
    grid-template-rows: 1fr 1fr;

    >div {
      min-height: 238.625px;
      max-height: 238.625px;
    }

    .servicios-funerarios {
      padding: 0;
      display: block;
      @extend .no-scrollbar;
      position: relative;

      .slide-servicio {
        padding: 21px;
        display: grid !important;
        height: 100%;
        /* required for "1fr" to work */
        grid-template-rows: 48px 135px;
        /* first row fixed, second row fills the rest */
        align-content: space-between;
      }

      .slide-header {
        overflow-x: hidden;
        /* optional styles */
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        color: white;

        .fallecido {
          h2 {
            @extend .h4, .font-medium, .one-line-text;
          }

          width: 100%;
        }

        .velacion {
          width: 100%;
          display: grid;
          grid-template-columns: 1fr auto;
          align-items: center;
          justify-content: space-between;

          >div {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
          }
        }
      }

      .slide-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        height: 100%;
        /* 🔥 REQUIRED */
        gap: 1rem;

        >div {
          display: flex;
          flex-direction: column;
          height: 100%;
        }

        .contenido-null {
          display: flex;
          align-items: flex-start;
          justify-content: center;

          p {
            @extend .h4, .font-bold;
            color: white;
          }
        }

        .contenido {
          display: grid;
          grid-template-rows: 21px 1fr;
          gap: 1rem;

          p {
            @extend .h5, .font-bold;
            color: white;
          }

          .datos-contenido {
            display: grid;
            grid-template-rows: 1fr 1fr;
            @extend .scroll-styles;

            .row-dato {
              display: grid;
              grid-template-columns: 1fr 1fr;
              gap: 1rem;

              >div {
                display: grid;
                grid-template-columns: 36px 1fr;
                gap: 0.5rem;
                align-items: center;

                .dato {
                  padding: 0.1rem 0.2rem;
                  border-radius: 4px;
                  text-align: center;
                  text-transform: capitalize
                }

                .texto-dato {
                  @extend .font-normal, .one-line-text;

                }
              }
            }
          }
        }

        .imagen {
          display: flex;
          align-items: flex-end;
          justify-content: center;

          img {
            height: 60%;
            width: auto;
            object-fit: contain;
          }
        }
      }
    }

    .inventario {
      @extend .no-scrollbar;
      display: flex;
      flex-direction: column;
      gap: 1rem;
      justify-content: space-between;

      .seccion-title {
        display: flex;
        align-items: center;
        justify-content: space-between;

        h2 {
          @extend .h4, .font-medium, .one-line-text;
        }
      }

      .reportes {
        display: grid;
        grid-template-columns: 1fr;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;

        .lista {
          display: grid;
          grid-template-columns: repeat(2, 1fr);
          gap: 1rem;

          .reporte {
            display: flex;
            gap: 1rem;
            align-items: center;

            .imagen {
              display: flex;
              flex-direction: column;
              align-items: center;
              border-radius: 4px;
              padding: 7px;

              img {
                width: 36px;
              }
            }

            .cantidad {
              @extend .h4, .font-medium;
            }

            .descripcion {
              @extend .size-small;
            }
          }
        }

        >.imagen {
          height: 100%;
          display: flex;
          justify-content: flex-end;

          img {
            width: 130px;
            display: none;
          }
        }
      }
    }
  }
}

::v-deep .slick-slider,
::v-deep .slick-list,
::v-deep .slick-track,
::v-deep .slick-slide {
  height: 100% !important;
  /* required for "1fr" to work */
}

::v-deep .slick-slide>div {
  height: 100% !important;
  /* required for "1fr" to work */
}

/* make the slider a positioning context */
::v-deep .slick-slider {
  position: relative;
  /* important: so absolute children are positioned relative to the slider */
}

/* move the dots to top-right */
::v-deep .slick-dots {
  position: absolute !important;
  top: 4px;
  /* distance from top of slider */
  right: 7px;
  /* distance from right of slider */
  margin: 0;
  transform: none !important;
  /* remove any translate Slick might apply */
  z-index: 30;
  /* above slides */
  width: auto;
  height: 0;
}

/* inline the li items and remove margins */
::v-deep .slick-dots li {
  display: inline-block;
  margin: 0;
  padding: 0;
}

/* Estilos de los DOTS */
:deep(.slick-dots li button) {
  width: 8px;
  /* dot size */
  height: 8px;
  padding: 0;
  border-radius: 50%;
  /* make it round */
  background: rgba(255, 255, 255, 0.3);
  /* Not selected */
  border: none;

  &::before {
    content: "";
  }
}

/* Punto ACTIVO */
:deep(.slick-dots li.slick-active button) {
  background: white;
  opacity: 1;
  transform: scale(1.3);
  /* opcional: más grande */
}

/* (optional) make dots larger on hover */
.slick-dots li button:hover {
  transform: scale(1.1);
}

/* Small (sm) — min-width: 576px */
@media (min-width: 576px) {}

/* Medium (md) — min-width: 768px */
@media (min-width: 768px) {

  .inventario {
    .reportes {
      grid-template-columns: 1fr 130px !important;

      >.imagen {
        img {
          display: block !important;
        }
      }
    }
  }
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
@media (min-width: 1200px) {}

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
