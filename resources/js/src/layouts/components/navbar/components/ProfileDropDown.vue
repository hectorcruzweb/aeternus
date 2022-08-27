<template>
  <div
    class="the-navbar__user-meta flex items-center"
    v-if="this.activeUserInfo"
  >
    <div class="text-right leading-tight hidden sm:block">
      <p class="size-base font-bold lowercase">
        {{ this.activeUserInfo.nombre }}
      </p>
      <small class="size-smaller color-copy">Disponible</small>
    </div>
    <vs-dropdown vs-custom-content vs-trigger-click:true class="cursor-pointer">
      <div class="con-img ml-3" v-if="this.activeUserInfo.imagen">
        <img
          key="onlineImg"
          :src="activeUserInfo.imagen"
          alt="user-img"
          width="40"
          height="40"
          class="rounded-full shadow-md cursor-pointer block"
        />
      </div>
      <div class="con-img ml-3" v-else>
        <img
          key="onlineImg"
          src="@assets/images/portrait/small/default-user.jpg"
          alt="user-img"
          width="40"
          height="40"
          class="rounded-full shadow-md cursor-pointer block"
        />
      </div>

      <vs-dropdown-menu class="vx-navbar-dropdown menu-dropdown-aeternus">
        <ul style="min-width: 9rem">
          <li
            class="flex py-2 px-4"
            @click="$router.push('/pages/profile').catch(() => {})"
          >
            <feather-icon icon="UserIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Perfil</span>
          </li>
          <!--
          <li
            class="flex py-2 px-4"
            @click="$router.push('/home').catch(() => {})"
          >
            <feather-icon icon="MailIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Soporte</span>
          </li>
          
          <li
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/apps/todo').catch(() => {})">
            <feather-icon icon="CheckSquareIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Tasks</span>
          </li>

          <li
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/apps/chat').catch(() => {})">
            <feather-icon icon="MessageSquareIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Chat</span>
          </li>

          <li
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/apps/eCommerce/wish-list').catch(() => {})">
            <feather-icon icon="HeartIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Wish List</span>
          </li>
          -->
          <vs-divider class="m-1" />

          <li class="flex py-2 px-4" @click="openConfirmarSinPassword = true">
            <feather-icon icon="LogOutIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Salir</span>
          </li>
        </ul>
      </vs-dropdown-menu>
    </vs-dropdown>
    <ConfirmarDanger
      :show="openConfirmarSinPassword"
      :callback-on-success="logout"
      @closeVerificar="openConfirmarSinPassword = false"
      :accion="'Esta operación lo sacará de sistema.'"
      :confirmarButton="'Confirmar y salir'"
    ></ConfirmarDanger>
  </div>
</template>

<script>
import ConfirmarDanger from "@pages/ConfirmarDanger";
export default {
  data() {
    return {
      activeUserInfo: {},
      openConfirmarSinPassword: false,
    };
  },
  components: {
    ConfirmarDanger,
  },
  methods: {
    logout() {
      this.$store.dispatch("auth/logout_user");
    },
  },
  mounted() {
    /**si no existe mando llamar los datos del usuario get_perfil de rutas api */
    if (!localStorage.getItem("userInfo")) {
      this.$store.dispatch("auth/user_datos").then((resp) => {
        this.activeUserInfo = JSON.parse(localStorage.getItem("userInfo"));
      });
    } else {
      this.activeUserInfo = JSON.parse(localStorage.getItem("userInfo"));
      if (!this.activeUserInfo.user_id) {
        this.$store.dispatch("auth/user_datos").then((resp) => {
          this.activeUserInfo = JSON.parse(localStorage.getItem("userInfo"));
        });
      }
    }
  },
  created() {
    this.activeUserInfo = JSON.parse(localStorage.getItem("userInfo"));
  },
};
</script>
