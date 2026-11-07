<template>
  <div id="profile-page" class="aeternus-profile">
    <!-- Identidad de consulta: únicamente la contraseña se edita en esta vista. -->
    <section class="aeternus-profile__card" aria-labelledby="profile-heading">
      <!-- Recurso público directo: evita agregar dependencias de URL al chunk CSS del perfil. -->
      <div
        class="aeternus-profile__photo"
        style="background-image: linear-gradient(180deg,#292e35 0%,rgba(41,46,53,.92) 20%,rgba(41,46,53,.12) 52%,rgba(41,46,53,.08) 80%,#292e35 100%), url('/images/funeraria.jpg')"
        role="img"
        aria-label="Instalaciones de Aeternus Funerales"
      >
        <div class="aeternus-profile__brand-label"><span></span>Departamento de Sistemas</div>
        <div class="aeternus-profile__brand-footer"><strong>SIIGA</strong><p>Sistema Integral de Información y Gerencia Administrativa</p></div>
      </div>

      <section class="aeternus-profile__settings">
        <header class="aeternus-profile__heading">
          <h1 id="profile-heading">Mi perfil</h1>
          <p>Administra la contraseña de tu cuenta</p>
        </header>
      <aside
        class="aeternus-profile__identity"
        aria-label="Información del usuario"
      >
        <div class="aeternus-profile__identity-content">
          <vs-avatar
            class="aeternus-profile__avatar"
            :src="
              activeUserInfo.imagen ||
              require('@assets/images/portrait/small/default-user.jpg')
            "
            size="88px"
          />
          <h2>{{ activeUserInfo.nombre || "Usuario" }}</h2>
          <span class="aeternus-profile__role">{{
            activeUserInfo.rol || "No disponible"
          }}</span>
          <dl class="aeternus-profile__details">
            <div>
              <feather-icon icon="MailIcon" />
              <dt class="aeternus-profile__sr-only">Correo electrónico</dt>
              <dd>{{ activeUserInfo.email || "No disponible" }}</dd>
            </div>
            <div>
              <feather-icon icon="PhoneIcon" />
              <dt class="aeternus-profile__sr-only">Teléfono</dt>
              <dd>{{ activeUserInfo.telefono || "No disponible" }}</dd>
            </div>
            <div>
              <feather-icon icon="UserIcon" />
              <dt class="aeternus-profile__sr-only">Género</dt>
              <dd>{{ activeUserInfo.genero_des || "No disponible" }}</dd>
            </div>
            <!-- Puestos activos y área de trabajo, únicamente informativos. -->
            <div class="aeternus-profile__detail-labeled"><feather-icon icon="BriefcaseIcon" /><dt>Puesto(s)</dt><dd>{{ puestosPerfil }}</dd></div>
            <div class="aeternus-profile__detail-labeled"><feather-icon icon="MapPinIcon" /><dt>Área de trabajo</dt><dd>{{ activeUserInfo.area_des || 'No registrado' }}</dd></div>
          </dl>
        </div>
      </aside>

        <div class="aeternus-profile__password-panel">
        <div class="aeternus-profile__password">
          <h2>
            <feather-icon icon="LockIcon" /><span>Cambiar contraseña</span>
          </h2>
          <!-- Conservar modelos, nombres y reglas originales de VeeValidate.
               El botón mantiene la confirmación de autorización antes de guardar. -->
          <div class="aeternus-profile__field">
            <label for="profile-password"
              >Nueva contraseña <span aria-hidden="true">*</span></label
            >
            <vs-input
              id="profile-password"
              ref="password"
              name="password"
              data-vv-as=" "
              v-validate
              maxlength="75"
              type="password"
              class="aeternus-profile__input"
              placeholder="Ingresa tu nueva contraseña"
              v-model="form.password"
              autocomplete="new-password"
            />
            <span
              v-if="errors.first('password')"
              class="aeternus-profile__error"
              role="alert"
              >{{ errors.first("password") }}</span
            >
            <span
              v-if="errores.password"
              class="aeternus-profile__error"
              role="alert"
              >{{ errores.password[0] }}</span
            >
          </div>
          <div class="aeternus-profile__field">
            <label for="profile-password-confirm"
              >Confirmar nueva contraseña
              <span aria-hidden="true">*</span></label
            >
            <vs-input
              id="profile-password-confirm"
              name="repetirPassword"
              data-vv-as=" "
              v-validate="validar_confirmar"
              maxlength="75"
              type="password"
              class="aeternus-profile__input"
              placeholder="Confirma tu nueva contraseña"
              v-model="form.repetirPassword"
              autocomplete="new-password"
            />
            <span
              v-if="errors.first('repetirPassword')"
              class="aeternus-profile__error"
              role="alert"
              >{{ errors.first("repetirPassword") }}</span
            >
            <span
              v-if="errores.repetirPassword"
              class="aeternus-profile__error"
              role="alert"
              >{{ errores.repetirPassword[0] }}</span
            >
          </div>
        </div>
        <footer class="aeternus-profile__actions">
          <p>
            <feather-icon icon="InfoIcon" /><span
              >Para modificar tus datos personales, contacta a gerencia.</span
            >
          </p>
          <vs-button class="aeternus-profile__submit" @click="mandarModificar"
            ><feather-icon icon="CheckIcon" /><span
              >Actualizar contraseña</span
            ></vs-button
          >
        </footer>
        </div>
      </section>
    </section>
    <!-- Mismo diálogo y callback de autorización del componente original. -->
    <Password
      v-if="operConfirmar"
      :show="operConfirmar"
      :callback-on-success="ActualizarDatos"
      @closeVerificar="operConfirmar = false"
      :accion="'Actualizar perfil'"
    ></Password>
  </div>
</template>

<script>
import Password from "../pages/confirmar_password";
import usuarios from "@services/Usuarios";
import store from "@/store/store";
export default {
  components: {
    Password,
  },
  data() {
    return {
      activeUserInfo: {},
      errores: [],
      operConfirmar: false,
      form: {
        imagen: "",
        password: "",
        repetirPassword: "",
      },
    };
  },
  computed: {
    // El perfil recibe todos los puestos activos; no confundir el rol de permisos con el puesto laboral.
    puestosPerfil() {
      const puestos = this.activeUserInfo.puestos;
      return Array.isArray(puestos) && puestos.length
        ? puestos.map(puesto => puesto.puesto).filter(Boolean).join(', ') || 'No registrado'
        : 'No registrado';
    },
    validar_confirmar: function () {
      if (this.form.password != "") {
        return "required|confirmed:password";
      } else return "";
    },
  },
  methods: {
    imagen() {
      this.$refs.fileImage.click();
    },
    display: function (event) {
      // Reference to the DOM input element
      var input = event.target;
      // Ensure that you have a file before attempting to read it
      if (input.files && input.files[0]) {
        if (input.files[0].type != "image/jpeg") {
          this.$vs.notify({
            title: "Actualizar imagen",
            text: "Error, debe seleccionar una imagen .jpeg.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 4000,
          });
          return;
        }
        // create a new FileReader to read this image and convert to base64 format
        var reader = new FileReader();
        // Define a callback function to run, when FileReader finishes its job
        reader.onload = (e) => {
          // Note: arrow function used here, so that "this.imageData" refers to the imageData of Vue component
          // Read image as base64 and set to imageData
          this.form.imagen = e.target.result;
        };
        // Start the reader job - read file as a data url (base64 format)
        reader.readAsDataURL(input.files[0]);
      }
    },
    mandarModificar() {
      this.$validator
        .validateAll()
        .then((result) => {
          if (!result) {
            this.$vs.notify({
              title: "Actualizar Datos",
              text: "Error, verifique que ingresó todos los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 4000,
            });
            return;
          } else {
            //verificando que haya algo que modificar
            if (this.form.imagen != "" || this.form.password != "") {
              //manda modificar
              this.operConfirmar = true;
            } else {
              //no hay nada que modificar
              this.$vs.notify({
                title: "Actualizar Datos",
                text: "Error, no ha ingresado ningún dato para actualizar.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 4000,
              });
            }

            //se manda el emit al parent para guardar datps
            //this.$emit("actualizar", this.form);
          }
        })
        .catch(() => {});
    },
    ActualizarDatos() {
      this.$vs.loading();
      usuarios
        .actualizar_perfil(this.form)
        .then((res) => {
          if (res.data >= 0) {
            //success
            this.$vs.notify({
              title: "Actualizar Datos",
              text: "Se han guardado los cambios correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000,
            });

            setTimeout(function () {
              localStorage.removeItem("userInfo");
              location.reload();
            }, 300);
          } else {
            this.$vs.notify({
              title: "Actualizar Datos",
              text: "Error al guardar cambios, por favor reintente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 4000,
            });
          }

          this.$vs.loading.close();
        })
        .catch((err) => {
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
            } else if (err.response.status == 422) {
              //checo si existe cada error
              this.errores = err.response.data.error;
            }
          }
          this.$vs.loading.close();
        });
    },
  },
  mounted() {
    // Mostrar la ficha almacenada mientras se consulta la información laboral vigente.
    const guardado = localStorage.getItem("userInfo");
    if (guardado) {
      try { this.activeUserInfo = JSON.parse(guardado) || {}; } catch (error) { this.activeUserInfo = {}; }
    }
    this.$store.dispatch("auth/user_datos").then((perfil) => {
      this.activeUserInfo = perfil || this.activeUserInfo;
    }).catch(() => {
      // Si falla la consulta, conservar la ficha disponible sin redirigir ni inventar datos.
    });
  },
  beforeDestroy() {
    //this.$store.commit("TOGGLE_REDUCE_BUTTON", false);
  },
};
</script>

<style scoped>
/* Distribución local inspirada en el login: fotografía a la izquierda y ficha
   informativa encima de la contraseña a la derecha. Sin cambios al menú ni al guardado. */
.aeternus-profile{font-family:Arial,sans-serif;color:#354047}
.aeternus-profile,.aeternus-profile *{box-sizing:border-box}
.aeternus-profile__card{display:grid;grid-template-columns:45% minmax(0,1fr);background:#fff;border:1px solid #e0e2e5;border-radius:8px;overflow:hidden;box-shadow:0 4px 16px rgba(29,35,40,.06)}
.aeternus-profile__photo{position:relative;min-height:620px;background-color:#292e35;background-size:100% 100%,auto 78%;background-position:center,center 70%;background-repeat:no-repeat;color:#fff}
.aeternus-profile__brand-label{display:flex;align-items:center;gap:14px;padding:34px 30px;font-size:11px;letter-spacing:1.8px;line-height:1.5;text-transform:uppercase;color:#eee}
.aeternus-profile__brand-label span{width:34px;height:2px;background:#a58828;flex:none}
.aeternus-profile__brand-footer{position:absolute;bottom:30px;left:30px;right:30px}
.aeternus-profile__brand-footer strong{font-size:25px;color:#fff}
.aeternus-profile__brand-footer p{margin:7px 0 0;color:#d4d9dc;font-size:12px;line-height:1.5}
.aeternus-profile__settings{min-width:0;padding:30px 34px;display:flex;flex-direction:column}
.aeternus-profile__heading h1{margin:0;color:#292e35;font-size:28px!important;line-height:1.25;font-weight:700}
.aeternus-profile__heading p{margin:7px 0 0;font-size:14px;line-height:1.5;color:#78818a}
.aeternus-profile__identity{margin-top:24px;padding-bottom:22px;border-bottom:1px solid #e4e6e8}
.aeternus-profile__identity-content{display:grid;grid-template-columns:64px minmax(0,1fr);column-gap:16px;align-items:center}
.aeternus-profile__avatar{grid-column:1;grid-row:1/3;width:64px!important;height:64px!important;margin:0!important;background:#eee!important;border:2px solid #f0f0f0}
.aeternus-profile__identity h2{grid-column:2;margin:0;color:#354047;font-size:16px!important;line-height:1.4;font-weight:600;overflow-wrap:anywhere}
.aeternus-profile__role{grid-column:2;justify-self:start;font-size:12px;line-height:1.5;color:#96791f;margin-top:4px}
.aeternus-profile__details{grid-column:1/-1;display:flex;flex-wrap:wrap;gap:12px 22px;margin:18px 0 0}
.aeternus-profile__details>div{display:flex;align-items:center;gap:9px;min-width:0}
.aeternus-profile__details>div:first-child{flex-basis:100%}
.aeternus-profile__details dd{margin:0;color:#68737c;font-size:13px;line-height:1.5;overflow-wrap:anywhere}
.aeternus-profile__details >>> svg{width:17px;height:17px;display:block;color:#90989e;flex:none}
.aeternus-profile__password{padding-top:23px}
.aeternus-profile__password h2{display:flex;align-items:center;gap:10px;margin:0 0 20px;color:#354047;font-size:18px!important;line-height:1.4;font-weight:600}
.aeternus-profile__password h2 >>> svg{display:block;width:22px;height:22px;color:#a58828}
.aeternus-profile__field+.aeternus-profile__field{margin-top:18px}
.aeternus-profile__field label{display:block;margin-bottom:7px;font-size:13px;font-weight:600;line-height:1.5}
.aeternus-profile__field label>span{color:#b14a4a}
.aeternus-profile__input,.aeternus-profile__input >>> .vs-con-input{width:100%}
.aeternus-profile__input >>> .vs-input--input{width:100%;min-height:44px;border-radius:3px!important;padding:12px 14px!important;font-size:14px!important;color:#354047;background:#fff}
.aeternus-profile__input >>> .vs-input--placeholder{padding:13px 14px!important;font-size:14px!important;color:#899198}
.aeternus-profile__error{display:block;margin-top:6px;font-size:12px;line-height:1.4;color:#b24646}
.aeternus-profile__actions{display:flex;flex-direction:column-reverse;gap:18px;margin-top:auto;padding-top:26px}
.aeternus-profile__actions p{display:flex;align-items:flex-start;gap:9px;margin:0;color:#7a838b;font-size:11px;line-height:1.6}
.aeternus-profile__actions p >>> svg{display:block;width:16px;height:16px;flex:none;margin-top:1px}
.aeternus-profile__submit{width:100%;padding:12px 18px!important;background:#a58828!important;color:#fff!important;border-radius:3px!important;font-size:14px!important;min-height:44px}
.aeternus-profile__submit >>> .vs-button-text{display:flex;align-items:center;justify-content:center;gap:9px;font-size:14px;color:#fff;line-height:20px}
.aeternus-profile__submit >>> svg{display:block;width:19px;height:19px}
.aeternus-profile__submit:hover{background:#8f741e!important}
.aeternus-profile__submit:focus-visible{outline:3px solid #cab875;outline-offset:3px}
.aeternus-profile__sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
@media(min-width:1600px){.aeternus-profile__settings{padding:34px 48px}.aeternus-profile__photo{min-height:650px}.aeternus-profile__brand-label{padding:40px}.aeternus-profile__brand-footer{left:40px;bottom:36px}}
@media(max-width:1000px){.aeternus-profile__card{grid-template-columns:38% minmax(0,1fr)}.aeternus-profile__settings{padding:26px}.aeternus-profile__brand-label{padding:28px 20px;font-size:10px}.aeternus-profile__brand-footer{left:20px;right:20px}}
@media(max-width:767px){.aeternus-profile__card{grid-template-columns:minmax(0,1fr)}.aeternus-profile__photo{min-height:180px;background-size:100% 100%,cover;background-position:center,center 60%}.aeternus-profile__brand-label{padding:20px 24px}.aeternus-profile__brand-footer{display:none}.aeternus-profile__settings{padding:26px 24px}.aeternus-profile__heading h1{font-size:25px!important}}
@media(max-width:420px){.aeternus-profile__settings{padding:24px 20px}.aeternus-profile__identity h2{font-size:14px!important}.aeternus-profile__identity-content{column-gap:12px}}

/* Panel blanco: ficha informativa izquierda y contraseña derecha.
   Las acciones pertenecen a la columna de contraseña; en pantallas estrechas se apilan. */
.aeternus-profile__settings{display:grid;grid-template-columns:minmax(210px,.85fr) minmax(0,1.15fr);grid-template-rows:auto auto 1fr;column-gap:24px;align-content:start}
.aeternus-profile__heading{grid-column:1/-1;margin-bottom:26px}
.aeternus-profile__identity{grid-column:1;grid-row:2/4;align-self:stretch;margin:0;padding:24px 18px;border:1px solid #e1e4e7;border-radius:5px;background:#f8f8f7}
.aeternus-profile__identity-content{display:flex;flex-direction:column;text-align:center;gap:0}
.aeternus-profile__avatar{margin:0 0 15px!important}
.aeternus-profile__identity h2{font-size:15px!important;line-height:1.5}
.aeternus-profile__role{margin-top:7px}
.aeternus-profile__details{display:block;width:100%;margin-top:20px;text-align:left}
.aeternus-profile__details>div{padding:12px 0;border-top:1px solid #e3e5e7}
.aeternus-profile__password{grid-column:2;grid-row:2;min-width:0;padding:0}
.aeternus-profile__actions{grid-column:2;grid-row:3;align-self:start;margin-top:0;padding-top:25px}
@media(max-width:1250px) and (min-width:768px){.aeternus-profile__card{grid-template-columns:32% minmax(0,1fr)}.aeternus-profile__settings{padding:26px 22px;column-gap:20px;grid-template-columns:minmax(185px,.85fr) minmax(0,1.15fr)}.aeternus-profile__identity{padding:22px 14px}}
@media(max-width:1000px){.aeternus-profile__settings{grid-template-columns:minmax(0,1fr)}.aeternus-profile__heading,.aeternus-profile__identity,.aeternus-profile__password,.aeternus-profile__actions{grid-column:1;grid-row:auto}.aeternus-profile__identity{margin-bottom:24px}.aeternus-profile__photo{min-height:620px}}
@media(max-width:767px){.aeternus-profile__photo{min-height:180px}.aeternus-profile__settings{padding:26px 24px}.aeternus-profile__identity-content{text-align:center}}
/* Campos adicionales: etiqueta y valor separados para facilitar la lectura. */
.aeternus-profile__details>.aeternus-profile__detail-labeled{display:grid;grid-template-columns:17px minmax(0,1fr);column-gap:9px;row-gap:3px;align-items:start}
.aeternus-profile__detail-labeled>dt{grid-column:2;margin:0;font-size:11px;color:#8b9399;line-height:1.4}
.aeternus-profile__detail-labeled>dd{grid-column:2;font-size:12px}
.aeternus-profile__detail-labeled>.feather-icon{grid-column:1;grid-row:1/3}
/* Ambos contenedores comparten la misma fila y se estiran a igual altura. */
.aeternus-profile__settings{grid-template-rows:auto 1fr}
.aeternus-profile__identity{grid-row:2;align-self:stretch}
.aeternus-profile__password-panel{grid-column:2;grid-row:2;display:flex;flex-direction:column;min-width:0;align-self:stretch;padding:22px;border:1px solid #e1e4e7;border-radius:5px;background:#fff}
.aeternus-profile__password-panel .aeternus-profile__actions{align-self:stretch;width:100%;margin-top:auto;padding-top:26px}
@media(max-width:1000px){.aeternus-profile__settings{grid-template-rows:auto}.aeternus-profile__identity,.aeternus-profile__password-panel{grid-column:1;grid-row:auto}.aeternus-profile__password-panel{padding:22px 18px}}
</style>
