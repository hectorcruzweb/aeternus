<template>
    <div class="centerx">
        <vs-popup class="forms-popup popup-85" fullscreen title="listado de planes funerarios" :active="localShow"
            :ref="this.$options.name">
            <div class="form-group">
                <div class="title-form-group">Planes Funerarios Registrados</div>
                <div class="form-group-content">
                    <div class="flex flex-wrap">
                        <!-- Barra visual: conserva los mismos modelos y acciones del módulo. -->
                        <div class="planes-toolbar">
                            <label class="planes-toolbar__filtro">
                                <span>Filtrar por plan funerario</span>
                                <v-select :options="tipo_planes" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="plan_tipo" class="w-full">
                                    <div slot="no-options">Seleccione un Plan Funerario</div>
                                </v-select>
                            </label>
                            <div class="planes-toolbar__acciones">
                                <IdiomaReporte v-model="idiomaReporte" />
                                <button type="button" class="planes-accion planes-accion--borde" @click="openReporte('Todos los Planes', '/funeraria/planes_funerarios', '')">
                                    <feather-icon icon="CopyIcon" aria-hidden="true" /><span>Ver lista de planes</span>
                                </button>
                                <button type="button" class="planes-accion planes-accion--oro" @click="agregarPrecios()">
                                    <feather-icon icon="TagIcon" aria-hidden="true" /><span>Agregar precios</span>
                                </button>
                                <button type="button" class="planes-accion planes-accion--verde" @click="agregarPlan()">
                                    <feather-icon icon="PlusIcon" aria-hidden="true" /><span>Crear plan</span>
                                </button>
                            </div>
                        </div>

                        <div v-if="planes.length > 0" class="w-full pt-6">
                            <div v-for="(plan, index) in planes" :key="index"
                                :class="[indexPrecios(index, plan.id), 'w-full']">
                                <vs-table :data="plan.precios" noDataText="0 Resultados" class="tabla-datos">
                                    <template slot="header">
                                        <!-- Solo el encabezado del plan cambia de presentación.
                                             Las columnas y filas de precios permanecen intactas. -->
                                        <div class="planes-cabecera">
                                            <div class="planes-cabecera__identidad">
                                                <feather-icon icon="LayersIcon" class="planes-cabecera__icono" aria-hidden="true" />
                                                <span class="planes-cabecera__nombre">{{ plan.plan }}</span>
                                                <span class="planes-estado" :class="{ 'planes-estado--inactivo': plan.status != 1 }">{{ plan.status == 1 ? 'Activo' : 'Inactivo' }}</span>
                                            </div>
                                            <div class="planes-cabecera__acciones">
                                                <button type="button" class="planes-enlace" @click="openModificarPlan(plan.id)"><feather-icon icon="Edit2Icon" aria-hidden="true" /><span>Editar</span></button>
                                                <button v-if="plan.status == 1" type="button" class="planes-enlace planes-enlace--rojo" @click="enable_disable_plan(plan.id, plan.plan, 'deshabilitar')"><feather-icon icon="PowerIcon" aria-hidden="true" /><span>Desactivar</span></button>
                                                <button v-else type="button" class="planes-enlace planes-enlace--verde" @click="enable_disable_plan(plan.id, plan.plan, 'habilitar')"><feather-icon icon="PowerIcon" aria-hidden="true" /><span>Habilitar</span></button>
                                                <button type="button" class="planes-accion planes-accion--documento" @click="openReportePlan('Precios x Plan', '/funeraria/pdf_plan_funerario', plan.id)"><feather-icon icon="FileTextIcon" aria-hidden="true" /><span>Ver plan</span></button>
                                            </div>
                                        </div>
                                    </template>
            <template slot="thead">
              <vs-th>#</vs-th>
              <vs-th>Pago Inicial ($)</vs-th>
              <vs-th>Costo Neto ($)</vs-th>
              <vs-th>Costo a Pronto Pago ($)</vs-th>
              <vs-th>Abono Mensual ($)</vs-th>
              <vs-th>Desc. x Pago ($)</vs-th>
              <vs-th>Financiamiento</vs-th>
              <vs-th>Descripción</vs-th>
              <vs-th>Acciones</vs-th>
            </template>
            <template slot-scope="{ data }">
              <vs-tr
                :data="precio"
                :key="index_precio"
                v-for="(precio, index_precio) in data"
              >
                <vs-td>
                  <span class="font-semibold">{{ index_precio + 1 }}</span>
                </vs-td>
                <vs-td
                  >$ {{ precio.pago_inicial | numFormat("0,000.00") }}</vs-td
                >
                <vs-td>$ {{ precio.costo_neto | numFormat("0,000.00") }}</vs-td>
                <vs-td
                  >$
                  {{
                    precio.costo_neto_pronto_pago | numFormat("0,000.00")
                  }}</vs-td
                >
                <vs-td :data="data[index_precio].pago_mensual"
                  >$ {{ precio.pago_mensual | numFormat("0,000.00") }}</vs-td
                >
                <vs-td
                  >$
                  {{ precio.descuento_x_pago | numFormat("0,000.00") }}</vs-td
                >
                <vs-td>{{ precio.tipo_financiamiento }}</vs-td>
                <vs-td>{{ precio.descripcion }}</vs-td>

                <vs-td>
                  <img
                    class="cursor-pointer img-btn ml-auto mb-1"
                    src="@assets/images/edit.svg"
                    title="Modificar"
                    @click="openModificar(data[index_precio].id)"
                  />
                  <img
                    v-if="data[index_precio].status == 1"
                    class="cursor-pointer img-btn-32 ml-3"
                    src="@assets/images/switchon.svg"
                    title="Deshabilitar"
                    @click="
                      enable_disable_precio(
                        data[index_precio].id,
                        data[index_precio].descripcion,
                        'deshabilitar'
                      )
                    "
                  />

                  <img
                    v-else
                    class="cursor-pointer img-btn-32 mr-auto ml-3"
                    src="@assets/images/switchoff.svg"
                    title="Habilitar"
                    @click="
                      enable_disable_precio(
                        data[index_precio].id,
                        data[index_precio].descripcion,
                        'habilitar'
                      )
                    "
                  />
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
                            </div></div></div></div></div>
            <ConfirmarDanger v-if="operConfirmar" :show="operConfirmar" :callback-on-success="callback" @closeVerificar="operConfirmar = false" :accion="'¿Desea eliminar este plan de mensualidades? Los datos quedarán eliminados del sistema.'" :confirmarButton="'Eliminar'" />
            <Password v-if="openPassword" :show="openPassword" :callback-on-success="callbackPassword" @closeVerificar="openPassword = false" :accion="accionPassword" />
            <FormularioPrecios v-if="verFormularioPrecios" :id_precio="id_precio_modificar" :tipo="tipoFormulario" :show="verFormularioPrecios" @closeVentana="verFormularioPrecios = false" @retornar_id="retorno_plan" />
            <FormularioPlanes v-if="verFormularioPlan" :id_plan="id_plan_modificar" :tipo="tipoFormularioPlan" :show="verFormularioPlan" @closeVentana="verFormularioPlan = false" @retornar="retorno_plan" />
            <Reporteador v-if="openReportesLista" :header="'Consultar precios x propiedad'" :show="openReportesLista" :listadereportes="ListaReportes" :request="request" @closeReportes="openReportesLista = false" />
        </vs-popup>
        <VisorReportes v-if="mostrarCatalogoVisor" :show="mostrarCatalogoVisor" :cargar="cargarCatalogoVisor" :descargar-pdf="descargarCatalogoPdf" :enviar-correo="enviarCatalogoCorreo" @close="mostrarCatalogoVisor = false" />
    </div>
</template>
<script>
import VisorReportes from "@pages/VisorReportes.vue";
import IdiomaReporte from "@pages/IdiomaReporte.vue";
import reportesDocumentosPlanes from "@services/reportesDocumentosPlanes";
import Reporteador from "@pages/Reporteador";
import ConfirmarDanger from "@pages/ConfirmarDanger";
import Password from "@pages/confirmar_password";
import planes from "@services/planes";
import FormularioPrecios from "@pages/funeraria/ventas/FormularioPrecios";
import FormularioPlanes from "@pages/funeraria/ventas/FormularioPlanes";
import vSelect from "vue-select";
export default {
 name: "PlanesVentas",
 props: {show: {type:Boolean,required:true}},
 watch: {show: {immediate:true, async handler(newValue) {
  if(newValue){await this.get_planes();this.$popupManager.register(this,this.cancelar,null);}
  else {this.datosVenta=[];this.total=0;this.$popupManager.unregister(this.$options.name);}
  this.localShow=newValue;
 }}},
 components: {VisorReportes,IdiomaReporte,Reporteador,ConfirmarDanger,Password,FormularioPrecios,FormularioPlanes,"v-select":vSelect},
 data(){return {
  localShow:false,idiomaReporte:"es",idiomaCatalogoVisor:"es",mostrarCatalogoVisor:false,documentoCatalogoVisor:"catalogo",planDocumentoVisorId:null,
  plan_tipo:{},tipo_planes:[],tipoFormularioPlan:"",verFormularioPlan:false,id_plan_modificar:0,
  openReportesLista:false,ListaReportes:[],request:{destinatario:"",id_tipo_propiedad:"",email:""},
  tipoFormularioPrecio:"",selected:[],planes:[],planes_tipos:[],propiedad_tipo:{},verFormularioPrecios:false,tipoFormulario:"",id_precio_modificar:0,
  operConfirmar:false,openPassword:false,accionPassword:"",callback:Function,callbackPassword:Function
 };},
 computed:{},
 methods:{
    async get_planes() {
      try {
        this.$vs.loading();
        let res = await planes.get_planes(false, "");
        this.planes = res.data;
        /**llenando los tipos de propiedad para el select */
        this.tipo_planes = [];
        this.tipo_planes.push({
          label: "Todos los Planes",
          value: "",
        });
        res.data.forEach((element) => {
          this.tipo_planes.push({
            label: element.plan.charAt(0).toUpperCase() + element.plan.slice(1),
            value: element.id,
          });
          //la primero opcion
          this.plan_tipo = this.tipo_planes[0];
        });
        this.$vs.loading.close();
      } catch (err) {
        this.$vs.loading.close();
        this.ver = true;
        if (err.response) {
          if (err.response.status == 403) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Permiso denegado",
              text: "Verifique sus permisos con el administrador del sistema.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 4000,
            });
          }
        }
      }
    },
    agregarPlan() {
      this.tipoFormularioPlan = "agregar";
      this.verFormularioPlan = true;
    },
    openModificarPlan(id_plan) {
      this.tipoFormularioPlan = "modificar";
      this.id_plan_modificar = id_plan;
      this.verFormularioPlan = true;
    },
    enable_disable_plan(id_plan, plan, accion) {
      this.accionPassword = accion + " plan " + plan;
      this.id_plan_modificar = id_plan;
      this.openPassword = true;
      this.callbackPassword = this.enable_disable_planes;
    },
    enable_disable_planes() {
      this.$vs.loading();
      let datos = {
        id_plan: this.id_plan_modificar,
      };
      planes
        .enable_disable_planes(datos)
        .then((res) => {
          this.$vs.loading.close();
          (async () => {
            await this.get_planes();
          })();
          if (res.data >= 1) {
            this.$vs.notify({
              title: "Cambiar estatus del plan funerario",
              text: "Se ha actualizado el estatus exitosamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 4000,
            });
          } else {
            this.$vs.notify({
              title: "Cambiar estatus del plan funerario",
              text: "No se realizaron cambios.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "primary",
              time: 4000,
            });
          }
        })
        .catch((err) => {
          this.$vs.loading.close();
          if (err.response) {
            if (err.response.status == 403) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Permiso denegado",
                text:
                  "Verifique sus permisos con el administrador del sistema.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "warning",
                time: 4000,
              });
            } else if (err.response.status == 422) {
              /**error de validacion */
              this.errores = err.response.data.error;
            } else if (err.response.status == 409) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Cambiar estatus del precio",
                text: err.response.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 8000,
              });
            }
          }
        });
    },

    agregarPrecios() {
      this.tipoFormulario = "agregar";
      this.verFormularioPrecios = true;
    },
        openReportePlan(nombre_reporte = "", link = "", parametro = "") {
            this.documentoCatalogoVisor = "plan";
            this.planDocumentoVisorId = Number(parametro);
            this.idiomaCatalogoVisor = this.idiomaReporte;
            this.mostrarCatalogoVisor = true;
            this.$vs.loading.close();
        },
        /** El catálogo usa el visor común. Fijar el idioma al abrir conserva la
         * misma consulta al actualizar, descargar o enviar por correo. */
        openReporte(nombre_reporte = "", link = "", parametro = "") {
            this.documentoCatalogoVisor = "catalogo";
            this.planDocumentoVisorId = null;
            this.idiomaCatalogoVisor = this.idiomaReporte;
            this.mostrarCatalogoVisor = true;
            this.$vs.loading.close();
        },
        async cargarCatalogoVisor() {
            return (await reportesDocumentosPlanes.visualizar(this.documentoCatalogoVisor, { idioma: this.idiomaCatalogoVisor, id_plan: this.planDocumentoVisorId })).data;
        },
        async descargarCatalogoPdf(token) {
            return (await reportesDocumentosPlanes.pdf(this.documentoCatalogoVisor, token)).data;
        },
        async enviarCatalogoCorreo(token, datos) {
            return (await reportesDocumentosPlanes.correo(this.documentoCatalogoVisor, token, datos)).data;
        },
        openModificar(id_precio) {
            this.tipoFormulario = "modificar";
            this.id_precio_modificar = id_precio;
            this.verFormularioPrecios = true;
        },
        agregar() {
            this.tipoFormulario = "agregar";
            this.verFormularioPrecios = true;
        },
        indexPrecios(index, plan_id) {
            if (this.plan_tipo.value != "") {
                if (plan_id != this.plan_tipo.value) {
                    return "hidden";
                }
            } else {
                if (index == 0) {
                    return "";
                } else {
                    return "pt-16";
                }
            }
        },
        async get_financiamientos() {
            try {
                this.$vs.loading();
                let res = await planes.get_financiamientos();
                this.planes = res.data;
                /**llenando los tipos de propiedad para el select */
                this.planes_tipos = [];
                this.planes_tipos.push({
                    label: "Todas las Propiedades",
                    value: "",
                });
                this.planes.forEach((element) => {
                    this.planes_tipos.push({
                        label: "Tipo " + element.tipo,
                        value: element.id,
                    });
                    //la primero opcion
                    this.propiedad_tipo = this.planes_tipos[0];
                });
                this.$vs.loading.close();
            } catch (err) {
                this.$vs.loading.close();
                this.ver = true;
                if (err.response) {
                    if (err.response.status == 403) {
                        /**FORBIDDEN ERROR */
                        this.$vs.notify({
                            title: "Permiso denegado",
                            text: "Verifique sus permisos con el administrador del sistema.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 4000,
                        });
                    }
                }
            }
        },
        cancelar() {
            this.$emit("closePlanesFuneraria");
            return;
        },
        retorno_plan(dato) {
            (async () => {
                await this.get_planes();
            })();
        },
        enable_disable_precio(id_precio, precio, accion) {
            this.accionPassword = accion + " precio " + precio;
            this.id_precio_modificar = id_precio;
            this.openPassword = true;
            this.callbackPassword = this.enable_disable;
        },
        enable_disable() {
            this.$vs.loading();
            let datos = {
                id_precio: this.id_precio_modificar,
            };
            planes
                .enable_disable(datos)
                .then((res) => {
                    this.$vs.loading.close();
                    (async () => {
                        await this.get_planes();
                    })();
                    if (res.data >= 1) {
                        this.$vs.notify({
                            title: "Cambiar estatus del precio",
                            text: "Se ha actualizado el estatus exitosamente.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "success",
                            time: 4000,
                        });
                    } else {
                        this.$vs.notify({
                            title: "Cambiar estatus del precio",
                            text: "No se realizaron cambios.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "primary",
                            time: 4000,
                        });
                    }
                })
                .catch((err) => {
                    this.$vs.loading.close();
                    if (err.response) {
                        if (err.response.status == 403) {
                            /**FORBIDDEN ERROR */
                            this.$vs.notify({
                                title: "Permiso denegado",
                                text:
                                    "Verifique sus permisos con el administrador del sistema.",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "warning",
                                time: 4000,
                            });
                        } else if (err.response.status == 422) {
                            /**error de validacion */
                            this.errores = err.response.data.error;
                        } else if (err.response.status == 409) {
                            /**FORBIDDEN ERROR */
                            this.$vs.notify({
                                title: "Cambiar estatus del precio",
                                text: err.response.data.error,
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "danger",
                                time: 8000,
                            });
                        }
                    }
                });
        },
    },
    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name);
    },
    beforeDestroy() {
        this.$popupManager.unregister(this.$options.name);
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>

<style scoped>
/* Estilos limitados a la barra y al encabezado aprobados. No se aplican reglas
   sobre vs-table, thead, tbody, filas ni celdas, ni se modifican sus acciones. */
.planes-toolbar{display:flex;align-items:flex-end;gap:16px;width:100%;flex-wrap:wrap}
.planes-toolbar__filtro{flex:1 1 320px;min-width:220px;margin:0}
.planes-toolbar__filtro>span{display:block;font-size:12px;color:#778188;margin-bottom:6px}
.planes-toolbar__acciones{display:flex;align-items:center;justify-content:flex-end;gap:10px;flex-wrap:wrap}
.planes-toolbar__acciones .reporte-idioma{margin:0;height:38px}
.planes-accion{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:8px 13px;border:1px solid #d6dce0;border-radius:0;background:#fff;color:#69767e;font:inherit;font-size:12px;line-height:1.4;white-space:nowrap;cursor:pointer;transition:background-color .15s,box-shadow .15s}
.planes-accion:hover{box-shadow:0 2px 5px rgba(0,0,0,.08)}
.planes-accion--borde{border-color:#a58627;color:#987820}
.planes-accion--borde:hover{background:#faf7ed}
.planes-accion--oro{background:#a58627;border-color:#a58627;color:#fff}
.planes-accion--oro:hover{background:#947621}
.planes-accion--verde{background:#28bd78;border-color:#28bd78;color:#fff}
.planes-accion--verde:hover{background:#22aa6c}
.planes-cabecera{display:flex;align-items:center;justify-content:space-between;gap:14px;width:100%;padding:11px 13px;background:#f0f1f2;flex-wrap:wrap;box-sizing:border-box}
.planes-cabecera__identidad{display:flex;align-items:center;gap:10px;flex:1 1 300px;min-width:0}
.planes-cabecera__nombre{text-transform:uppercase;font-weight:500;font-size:13px;color:#54626b;overflow-wrap:anywhere}
.planes-cabecera__icono{color:#6b7a84;flex-shrink:0}
.planes-estado{font-size:11px;padding:3px 8px;background:#edf5ef;color:#5e886b;white-space:nowrap}
.planes-estado--inactivo{background:#faeded;color:#ac5c60}
.planes-cabecera__acciones{display:flex;align-items:center;gap:9px;flex-wrap:wrap}
.planes-enlace{display:inline-flex;align-items:center;gap:6px;min-height:36px;border:0;background:transparent;padding:6px 8px;color:#a18429;font:inherit;font-size:12px;cursor:pointer}
.planes-enlace:hover{background:#e8e9e9}
.planes-enlace--rojo{color:#bb565d}
.planes-enlace--verde{color:#388862}
.planes-accion--documento{min-height:36px;padding:6px 10px}
.planes-accion .feather-icon,.planes-enlace .feather-icon,.planes-cabecera__icono{width:17px;height:17px}
@media(max-width:900px){.planes-toolbar__acciones{justify-content:flex-start}.planes-cabecera__acciones{margin-left:auto}}
@media(max-width:575px){.planes-toolbar__filtro{flex-basis:100%;min-width:0}.planes-toolbar__acciones{width:100%;gap:8px}.planes-toolbar__acciones .planes-accion{flex:1 1 auto}.planes-cabecera__identidad{flex-basis:100%}.planes-cabecera__acciones{width:100%;justify-content:flex-end;gap:4px}.planes-estado{margin-left:auto}}
@media(prefers-reduced-motion:reduce){.planes-accion{transition:none}}
</style>
