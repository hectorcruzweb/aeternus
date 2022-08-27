<template>
  <div>
    <div class="w-full text-right">
      <vs-button
        class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0"
        color="primary"
        @click="verFormulario('agregar')"
      >
        <span>Registrar Usuario</span>
      </vs-button>
    </div>

    <div class="pt-6 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
      <vx-card
        no-radius
        title="Filtros de selección"
        refresh-content-action
        @refresh="reset"
        :collapse-action="false"
      >
        <div class="flex flex-wrap">
          <div class="w-full md:w-6/12 lg:w-4/12 xl:w-4/12 px-2">
            <div class="w-full input-text">
              <label class="">Mostrar</label>
              <v-select
                :options="mostrarOptions"
                :clearable="false"
                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                v-model="mostrar"
                class="w-full"
              />
            </div>
          </div>

          <div class="w-full md:w-6/12 lg:w-4/12 xl:w-4/12 px-2">
            <div class="w-full input-text">
              <label class="">Estado</label>
              <v-select
                :options="estadosOptions"
                :clearable="false"
                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                v-model="estado"
                class="w-full"
              />
            </div>
          </div>

          <div class="w-full md:w-12/12 lg:w-4/12 xl:w-4/12 px-2">
            <div class="w-full input-text">
              <label class="">Roles</label>
              <v-select
                :options="rolesOptions"
                :clearable="false"
                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                v-model="roles"
                class="w-full"
              />
            </div>
          </div>
        </div>

        <div class="flex flex-wrap">
          <div class="w-full px-2 py-4">
            <h3 class="size-base">
              <feather-icon
                icon="UserIcon"
                class="mr-2"
                svgClasses="w-5 h-5"
              />Filtrar por Nombre del Usuario
            </h3>
          </div>
          <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
            <div class="w-full input-text">
              <label class="">Nombre del usuario</label>
              <vs-input
                class="w-full"
                icon="search"
                placeholder="Filtrar por nombre"
                v-model="nombre"
                v-on:keyup.enter="get_data(1)"
                v-on:blur="get_data(1, 'blur')"
              />
            </div>
          </div>
        </div>
      </vx-card>
    </div>

    <br />
    <vs-table
      :sst="true"
      @search="handleSearch"
      @change-page="handleChangePage"
      @sort="handleSort"
      :max-items="serverOptions.per_page.value"
      :data="users"
      noDataText="0 Resultados"
      class="tabla-datos"
    >
      <template slot="header">
        <h3>Listado de Usuarios registrados</h3>
      </template>
      <template slot="thead">
        <vs-th>Clave</vs-th>
        <vs-th>Nombre</vs-th>
        <vs-th>Usuario</vs-th>
        <vs-th>Género</vs-th>
        <vs-th>Estado</vs-th>
        <vs-th>Rol</vs-th>
        <vs-th>Acciones</vs-th>
      </template>
      <template slot-scope="{ data }">
        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
          <vs-td :data="data[indextr].id_user">
            <span class="font-bold">
              {{ data[indextr].id_user }}
            </span>
          </vs-td>
          <vs-td :data="data[indextr].nombre">{{ data[indextr].nombre }}</vs-td>
          <vs-td :data="data[indextr].email">{{ data[indextr].email }}</vs-td>
          <vs-td :data="data[indextr].genero">
            <p v-if="data[indextr].genero == 1">Hombre</p>
            <p v-else>Mujer</p>
          </vs-td>
          <vs-td :data="data[indextr].estado">
            <p v-if="data[indextr].estado == 1">
              Activo <span class="dot-success"></span>
            </p>
            <p v-else>Sin Acceso <span class="dot-warning"></span></p>
          </vs-td>
          <vs-td :data="data[indextr].rol">{{ data[indextr].rol }}</vs-td>
          <vs-td :data="data[indextr].id_user">
            <div class="flex justify-center">
              <img
                class="cursor-pointer img-btn-18 mx-2"
                src="@assets/images/edit.svg"
                title="Modificar"
                @click="openModificar(data[indextr].id_user)"
              />
              <img
                v-if="data[indextr].estado == 1"
                class="img-btn-24 mx-2"
                src="@assets/images/switchon.svg"
                title="Deshabilitar"
                @click="
                  deleteUsuario(data[indextr].id_user, data[indextr].nombre)
                "
              />

              <img
                v-else
                class="img-btn-24 mx-2"
                src="@assets/images/switchoff.svg"
                title="Habilitar"
                @click="
                  habilitarUsuario(data[indextr].id_user, data[indextr].nombre)
                "
              />
            </div>
          </vs-td>
        </vs-tr>
      </template>
    </vs-table>
    <div>
      <vs-pagination
        v-if="ver"
        :total="this.total"
        v-model="actual"
        class="mt-8"
      ></vs-pagination>
    </div>

    <formularioUsuarios
      :id_usuario="id_usuario_modificar"
      :tipo="tipoFormulario"
      :show="verFormularioUsuarios"
      @closeVentana="closeVentana"
      @get_data="get_data(actual)"
    ></formularioUsuarios>

    <Password
      :show="openStatus"
      :callback-on-success="callback"
      @closeVerificar="closeStatus"
      :accion="accionNombre"
    ></Password>
  </div>
</template>

<script>
import formularioUsuarios from "@pages/configuracion/usuarios/formularioUsuarios";

//componente de password
import Password from "@pages/confirmar_password";

import usuarios from "@services/Usuarios";
/**VARIABLES GLOBALES */
import {
  mostrarOptions,
  estadosOptions,
  rolesOptions,
} from "@/VariablesGlobales";
import vSelect from "vue-select";

export default {
  components: {
    "v-select": vSelect,
    Password,
    formularioUsuarios,
  },
  watch: {
    actual: function (newValue, oldValue) {
      this.get_data(this.actual);
    },
    mostrar: function (newValue, oldValue) {
      this.get_data(1);
    },
    estado: function (newVal, previousVal) {
      this.get_data(1);
    },
    roles: function (newValue, oldValue) {
      this.get_data(1);
    },
  },
  data() {
    return {
      tipoFormulario: "",
      id_usuario_modificar: 0,
      verPdf: false,
      pdfLink: "",
      openStatus: false,
      callback: Function,
      accionNombre: "",
      verModificar: false,
      datosModifcar: {},
      verFormularioUsuarios: false,
      activeTab: 0,
      ver: true,
      total: 0,
      actual: 1,
      mostrarOptions: mostrarOptions,
      estadosOptions: estadosOptions,
      rolesOptions: [],
      mostrar: { label: "15", value: "15" },
      estado: { label: "Todos", value: "" },
      roles: { label: "Todos", value: "" },
      nombre: "",
      selected: [],
      users: [],
      /**opciones para filtrar la peticion del server */
      serverOptions: {
        page: "",
        per_page: "",
        status: "",
        rol_id: "",
        nombre: "",
      },
      /**user id para bajas y altas */
      user_id: "",
    };
  },
  methods: {
    reset(card) {
      card.removeRefreshAnimation(500);
      this.mostrar = { label: "15", value: "15" };
      this.estado = { label: "Todos", value: "" };
      this.roles = { label: "Todos", value: "" };
      this.nombre = "";
      this.get_data(this.actual);
    },

    get_data(page, evento = "") {
      if (evento == "blur") {
        if (this.nombre != "") {
          //la funcion no avanza

          return false;
        }
      }
      let self = this;
      if (usuarios.cancel) {
        usuarios.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.ver = false;
      this.serverOptions.page = page;
      this.serverOptions.per_page = this.mostrar.value;
      this.serverOptions.rol_id = this.roles.value;
      this.serverOptions.status = this.estado.value;
      this.serverOptions.nombre = this.nombre;
      usuarios
        .getUsuarios(this.serverOptions)
        .then((res) => {
          this.users = res.data.data;
          this.total = res.data.last_page;
          this.actual = res.data.current_page;
          this.ver = true;
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.loading.close();
          this.ver = true;
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
                time: 8000,
              });
            }
          }
        });
    },
    get_roles() {
      usuarios
        .getRoles()
        .then((res) => {
          this.rolesOptions = [];
          //le agrego todos los roles
          this.rolesOptions.push({ label: "Todos", value: "" });
          res.data.data.forEach((element) => {
            /**AGREGO LOS DEMAS ROLES */
            this.rolesOptions.push(element);
          });
          this.roles = { label: "Todos", value: "" };
        })
        .catch((err) => {});
    },
    handleSearch(searching) {},
    handleChangePage(page) {},
    handleSort(key, active) {},
    closeVentana() {
      this.verFormularioUsuarios = false;
    },
    openModificar(id_user) {
      this.id_usuario_modificar = id_user;
      this.verFormulario("modificar");
    },
    //eliminar usuario logicamente
    deleteUsuario(id_user, nombre) {
      this.accionNombre = "deshabilitar usuario " + nombre;
      this.user_id = id_user;
      this.openStatus = true;
      this.callback = this.delete_usuario;
    },
    delete_usuario() {
      this.$vs.loading();
      let datos = {
        user_id: this.user_id,
      };
      usuarios
        .delete_usuario(datos)
        .then((res) => {
          this.$vs.loading.close();
          this.get_data(this.actual);
          if (res.data == 1) {
            this.$vs.notify({
              title: "Deshabilitar Usuario",
              text: "Se ha deshabilitado el usuario exitosamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 8000,
            });
          } else {
            this.$vs.notify({
              title: "Deshabilitar Usuario",
              text: "No se realizaron cambios.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "primary",
              time: 8000,
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
                time: 8000,
              });
            } else if (err.response.status == 422) {
              /**error de validacion */
              this.errores = err.response.data.error;
            }
          }
        });
    },

    //eliminar usuario logicamente
    habilitarUsuario(id_user, nombre) {
      this.accionNombre = "habilitar usuario " + nombre;
      this.user_id = id_user;
      this.openStatus = true;
      this.callback = this.habilitar_usuario;
    },
    habilitar_usuario() {
      this.$vs.loading();
      let datos = {
        user_id: this.user_id,
      };
      usuarios
        .habilitar_usuario(datos)
        .then((res) => {
          this.get_data(this.actual);
          this.$vs.loading.close();
          if (res.data == 1) {
            this.$vs.notify({
              title: "Habilitar Usuario",
              text: "Se ha habilitado el usuario exitosamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 8000,
            });
          } else {
            this.$vs.notify({
              title: "Habilitar Usuario",
              text: "No se realizaron cambios.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "primary",
              time: 8000,
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
                time: 8000,
              });
            } else if (err.response.status == 422) {
              /**error de validacion */
              this.errores = err.response.data.error;
            }
          }
        });
    },
    closeModificar() {
      this.verModificar = false;
    },

    closeStatus() {
      this.openStatus = false;
    },

    verFormulario(tipo) {
      this.tipoFormulario = tipo;
      this.verFormularioUsuarios = true;
    },
  },
  created() {
    this.get_roles();
    this.get_data(this.actual);
  },
};
</script>
