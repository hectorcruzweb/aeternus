<template>
  <div class="centerx">
    <vs-popup class="forms-popup popup-80" close="cancel" :title="title" :active="localShow" :ref="this.$options.name">
      <!--Nombre del rol-->
      <div class="form-group">
        <div class="title-form-group">
          <span>Rol de usuario</span>
        </div>
        <div class="form-group-content">
          <div class="flex flex-wrap">
            <div class="w-full px-2 input-text">
              <label class="">
                Nombre del Rol
                <span class="">(*)</span>
              </label>
              <vs-input data-vv-as=" " ref="nombre" name="nombre_rol" data-vv-validate-on="blur" v-validate="'required'"
                type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el nombre del rol" v-model="form.rol" />
              <span class="">{{ errors.first("nombre_rol") }}</span>
              <span class="" v-if="this.errores.rol">{{ errores.rol[0] }}</span>
            </div>
          </div>
        </div>
      </div>
      <!--Nombre del rol-->

      <!--Permisos del rol-->
      <div class="form-group">
        <div class="title-form-group">
          <span>Permisos en el sistema</span>
        </div>
        <div class="form-group-content">
          <div class="flex flex-wrap">
            <div v-for="seccion in modulos" :key="seccion.id" class="w-full">
              <div class="size-small uppercase font-medium color-black-900 px-2 py-1">
                {{ seccion.seccion }}
                <vs-divider />
              </div>
              <div class="w-full" v-for="modulo in seccion.modulos" :key="modulo.id">
                <div v-if="es_agrupador(modulo) == false"
                  class="flex flex-wrap theme-background mb-4 py-4 px-4 rounded-lg">
                  <div class="md:w-3/12">
                    <div class="pb-2 size-base font-medium color-copy">
                      {{ modulo.modulo }}
                    </div>
                    <vs-checkbox ref="permisos_modulo" color="success" class="size-small font-medium color-copy py-2"
                      v-model="form.modulos" :vs-value="modulo.id">Todos los permisos</vs-checkbox>
                  </div>
                  <div class="w-full md:w-9/12">
                    <div class="flex flex-wrap">
                      <!--Permisos del modulo-->
                      <ul v-for="permiso in modulo.permisos" :key="permiso.id"
                        class="w-full sm:w-6/12 md:w-6/12 lg:w-4/12 text-center">
                        <li class="px-3 pt-2">
                          <label class="text-center size-small font-medium color-copy">{{ permiso.permiso }}</label>
                          <vs-switch ref="permiso" color="primary" v-model="form.permisos" :vs-value="permiso.id"
                            class="mx-auto" />
                        </li>
                      </ul>
                    </div>
                  </div>

                  <!--Permisos del modulo-->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!--Permisos del rol-->

      <div class="bottom-buttons-section">
        <div class="text-advice">
          <span class="ojo-advice">Ojo:</span>
          Por favor revise la información ingresada, si todo es correcto de
          click en el "Botón de Abajo”.
        </div>

        <div class="w-full">
          <vs-button class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0" color="primary" @click="acceptAlert()">
            <span class="" v-if="this.getTipoformulario == 'agregar'">Crear Rol</span>
            <span class="" v-else>Modificar Rol</span>
          </vs-button>
        </div>
      </div>
    </vs-popup>
    <Password v-if="operConfirmar" :show="operConfirmar" :callback-on-success="callback" @closeVerificar="closeChecker"
      :accion="accionNombre"></Password>
    <ConfirmarDanger v-if="openConfirmarDanger" :z_index="'z-index58k'" :show="openConfirmarDanger"
      :callback-on-success="callBackConfirmar" @closeVerificar="openConfirmarDanger = false"
      :accion="accionConfirmarDanger" :confirmarButton="botonConfirmarDanger"></ConfirmarDanger>
    <ConfirmarAceptar :z_index="'z-index58k'" :show="openConfirmarAceptar" :callback-on-success="callback"
      @closeVerificar="openConfirmarAceptar = false" :accion="'He revisado la información y quiero registrar este rol'"
      :confirmarButton="'Crear Rol'"></ConfirmarAceptar>
  </div>
</template>
<script>
//componente de password
import Password from "@pages/confirmar_password";
import roles from "@services/Roles";
import vSelect from "vue-select";
import ConfirmarDanger from "@pages/ConfirmarDanger";
/**VARIABLES GLOBALES */
import { generosOptions } from "@/VariablesGlobales";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
export default {
  name: "formularioRoles",
  components: {
    "v-select": vSelect,
    Password,
    ConfirmarDanger,
    ConfirmarAceptar,
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    tipo: {
      type: String,
      required: true,
    },
    id_rol: {
      type: Number,
      required: false,
      default: 0,
    },
  },
  watch: {
    show: {
      immediate: true, // runs when component is mounted too
      async handler(newValue) {
        if (newValue) {
          // Only listen when visible = true
          /**obtengo los datos para llenar el form con los permisos segun su modulo */
          await this.get_modulos_permisos();
          if (this.getTipoformulario == "agregar") {
            this.title = "Registrar Rol de Usuario";
          } else {
            this.title = "Modificar Rol de Usuario";
          }
          this.$popupManager.register(
            this,
            this.cancel,
            "nombre"
          );
          //verificamos el origen del form para determinar que haremos justo al abrir el form.
          //obtener datos del cliente
        }
        this.localShow = newValue;
      },
    },
  },
  data() {
    return {
      localShow: false,
      openConfirmarAceptar: false,
      title: "",
      botonConfirmarDanger: "",
      openConfirmarDanger: false,
      callBackConfirmar: Function,
      accionConfirmarDanger: "",
      generosOptions: generosOptions,
      operConfirmar: false,
      callback: Function,
      accionNombre: "Modificar un Rol",
      modulos: [],
      form: {
        modulos: [],
        permisos: [],
        rol: "",
        rol_id: "",
      },
      errores: [],
    };
  },
  computed: {
    getTipoformulario: {
      get() {
        return this.tipo;
      },
      set(newValue) {
        return newValue;
      },
    },
    get_rol_id_modificar: {
      get() {
        return this.id_rol;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  methods: {
    get_rol_id(id_rol) {
      this.$vs.loading();
      roles
        .get_rol_id(id_rol)
        .then((res) => {
          this.form.rol = res.data.data.rol;
          /**llenando los valores de permisos haciendo clicks */
          this.$nextTick(() => {
            let index_permiso = 0;
            this.modulos.forEach((secciones) => {
              secciones.modulos.forEach((modulo) => {
                modulo.permisos.forEach((permiso) => {
                  res.data.data.permisos.forEach((element) => {
                    if (permiso.id == element.permisos_id) {
                      /**se remueve el permiso del arreglo y se da el click */
                      this.form.permisos.push(element.permisos_id);

                      this.$refs["permiso"][index_permiso].$el.querySelector(
                        "input"
                      ).checked = true;

                      var event = new Event("change");
                      // Dispatch it.
                      this.$refs["permiso"][index_permiso].$el
                        .querySelector("input")
                        .dispatchEvent(event);

                      /**verificando cual aplica activar el selector de modulos completo */
                      return false;
                    }
                  });

                  index_permiso++;
                });
                /**checando el seleccionar todos de caca modulo si aplica */
              });
            });
          });

          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.loading.close();
        });
    },

    es_agrupador(dato) {
      if (dato.parent_modulo_id == 0 && dato.url == "") {
        return true;
      } else return false;
    },
    acceptAlert() {
      this.$validator
        .validateAll()
        .then((result) => {
          if (!result) {
            this.$vs.notify({
              title: "Error",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000",
            });
            return;
          } else {
            //se confirma la cntraseña

            if (this.getTipoformulario == "agregar") {
              /**se manda llamar la funcion de agregar rol */
              this.openConfirmarAceptar = true;
              this.callback = this.saveRol;
            } else {
              this.callback = this.update_rol;
              /**se manda agregar  la funcion de modificar */
              this.operConfirmar = true;
            }
          }
        })
        .catch(() => { });
    },
    cancel() {
      if (this.getTipoformulario !== "agregar") {
        this.botonConfirmarDanger = "Salir y limpiar";
        this.accionConfirmarDanger =
          "Esta acción limpiará los datos que capturó en el formulario.";
        this.openConfirmarDanger = true;
        this.callBackConfirmar = this.cerrarVentana;
      } else
        this.cerrarVentana();
    },
    cerrarVentana() {
      /*
      this.form.rol_id = "";
      this.form.rol = "";
      this.form.modulos = [];
      this.form.permisos = [];
      this.$nextTick(() => {
        let index_permiso = 0;
        let index_modulo = 0;
        this.modulos.forEach((secciones) => {
          secciones.modulos.forEach((modulo) => {
            //apagados todos los select all de cada modulo
            if (modulo.url != "") {
              this.$refs["permisos_modulo"][0].$el.querySelector(
                "input"
              ).checked = false;
            }
            modulo.permisos.forEach((permiso) => {
              this.$refs["permiso"][0].$el.querySelector(
                "input"
              ).checked = false;
              index_permiso++;
            });
            //checando el seleccionar todos de caca modulo si aplica
            index_modulo++;
          });
        });
      });
*/
      this.$emit("closeVentana");
    },
    async get_modulos_permisos() {
      this.$vs.loading();
      try {
        // Call the API 
        this.modulos = await roles.get_modulos_permisos();
        if (!this.modulos || typeof this.modulos !== "object") {
          throw new Error(
            "Respuesta inválida en get_modulos_permisos"
          );
        }
        this.$nextTick(() => {
          let index_modulo = 0;
          let permiso_index = 0;
          this.modulos.forEach((secciones) => {
            secciones.modulos.forEach((modulo) => {
              if (modulo.url != "") {
                this.$refs["permisos_modulo"][index_modulo].$el.querySelector(
                  "input"
                ).onchange = ($event) => {
                  /**revisar si se activo el check */
                  this.checarTodosModulo($event, modulo.id);
                };
                index_modulo++;
              }
              modulo.permisos.forEach((permiso) => {
                this.$refs["permiso"][permiso_index].$el.querySelector(
                  "input"
                ).onchange = ($event) => {
                  this.updateChecarTodosModulo($event, modulo.id, permiso.id);
                };
                permiso_index++;
              });
            });
          });
          if (this.getTipoformulario == "modificar") {
            this.get_rol_id(this.get_rol_id_modificar);
          }
        });

      } catch (error) {
        this.$error("Error fetching modulos permisos:", error);
        this.cancel();
      } finally {
        this.$vs.loading.close();
      }
    },

    updateChecarTodosModulo(event, modulo_id, permiso_id) {
      this.$nextTick(() => {
        let total_permisos_modulo = 0; //total de permisos del modulo seleccionadp
        let total_permisos_activados = 0; //total de permisos que ha activado
        let index_permiso = 0; //index en el que se encuenta el permiso clickeado
        let index_modulo = 0;
        if (event.target.checked == true) {
          /**checar si se debe de prender el boton de encender todos setgun si estan todos los permisos de ese modulo activado */
          this.modulos.forEach((secciones) => {
            secciones.modulos.forEach((modulo) => {
              modulo.permisos.forEach((permiso) => {
                if (permiso.modulos_id == modulo_id) {
                  if (
                    this.$refs["permiso"][index_permiso].$el.querySelector(
                      "input"
                    ).checked == true
                  ) {
                    total_permisos_activados++;
                  }
                  //si ya estamos en el modulo que buscamos prender se debe reccorer el total de permisos para sacar el total
                  //de activados
                  if (total_permisos_activados == modulo.permisos.length) {
                    //aqui al ya coincidir se debe de activar el modulo al que pertenece el permiso activado
                    this.removeA(this.form.modulos, modulo_id);
                    this.form.modulos.push(modulo_id);
                    this.$refs["permisos_modulo"][
                      index_modulo
                    ].$el.querySelector("input").onclick = ($event) => {
                      /**revisar si se activo el check */
                      this.checarTodosModulo($event, modulo.id);
                    };
                  }
                }

                //se aumenta el index del permiso

                index_permiso++;
              });
            });
          });
        } else {
          /**aqui se apaga el checar modulo a fuerzas este o no prendido*/
          this.modulos.forEach((secciones) => {
            secciones.modulos.forEach((modulo) => {
              if (modulo.url != "") {
                if (modulo.id == modulo_id) {
                  this.removeA(this.form.modulos, modulo_id);
                  this.$refs["permisos_modulo"][index_modulo].$el.querySelector(
                    "input"
                  ).checked = false;
                  return false;
                }
                index_modulo++;
              }
            });
          });
        }
      });
    },
    checarTodosModulo(event, modulo_id) {
      /**prendiendo todos los del modulo */
      this.$nextTick(() => {
        let index_permiso = 0;
        this.modulos.forEach((secciones) => {
          secciones.modulos.forEach((modulo) => {
            modulo.permisos.forEach((permiso) => {
              if (modulo_id == permiso.modulos_id) {
                this.removeA(this.form.permisos, permiso.id);
                /**verificando la casilla */
                if (event.target.checked == true) {
                  this.form.permisos.push(permiso.id);
                  /**se remueve el permiso del arreglo y se da el click */
                  this.$refs["permiso"][index_permiso].$el.querySelector(
                    "input"
                  ).checked = true;
                } else {
                  this.$refs["permiso"][index_permiso].$el.querySelector(
                    "input"
                  ).checked = false;
                }
              }
              index_permiso++;
            });
          });
        });
      });
    },

    removeA(arr) {
      var what,
        a = arguments,
        L = a.length,
        ax;
      while (L > 1 && arr.length) {
        what = a[--L];
        while ((ax = arr.indexOf(what)) !== -1) {
          arr.splice(ax, 1);
        }
      }
      return arr;
    },

    //funcion que inserta el nuevo rol
    saveRol() {
      this.$vs.loading();
      //limpiando errores
      this.errores = [];
      roles
        .add_roles(this.form)
        .then((res) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: "Crear Roles de Usuario",
            text: "Se ha creado el nuevo rol exitosamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 4000,
          });
          this.$emit("get_data");
          this.cerrarVentana();
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
              this.$vs.notify({
                title: "Error",
                text:
                  "Verifique que todos los datos han sido capturados, ingrese un nombre para este rol y seleccione al menos un permiso.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                position: "bottom-right",
                time: "12000",
              });
              /**error de validacion */
              this.errores = err.response.data.error;
            }
          }
        });
    },

    update_rol() {
      this.$vs.loading();
      //limpiando errores
      this.errores = [];
      this.form.rol_id = this.get_rol_id_modificar;
      roles
        .update_rol(this.form)
        .then((res) => {
          this.$vs.loading();
          this.$vs.notify({
            title: "Modificar Roles",
            text: "Se ha modificado el rol exitosamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 4000,
          });
          this.$emit("get_data");
          this.cerrarVentana();
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
              this.$vs.notify({
                title: "Error",
                text: "Verifique que todos los datos han sido capturados",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                position: "bottom-right",
                time: "4000",
              });
              this.errores = err.response.data.error;
            }
          }
        });
    },

    closeChecker() {
      this.operConfirmar = false;
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