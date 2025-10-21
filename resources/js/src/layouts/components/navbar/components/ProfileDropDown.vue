<template>
  <div class="the-navbar__user-meta flex items-center" v-if="activeUserInfo">
    <!-- User info -->
    <div class="text-right leading-tight hidden sm:block">
      <p class="size-base font-bold lowercase">{{ activeUserInfo.nombre }}</p>
      <small class="size-smaller color-copy">Disponible</small>
    </div>

    <!-- Avatar and dropdown -->
    <vs-dropdown vs-custom-content vs-trigger-click class="cursor-pointer" ref="userDropdown">
      <!-- Avatar -->
      <div class="con-img ml-3">
        <img :src="activeUserInfo.imagen || require('@assets/images/portrait/small/default-user.jpg')" alt="user-img"
          width="40" height="40" class="rounded-full shadow-md cursor-pointer block" />
      </div>

      <!-- Dropdown Menu -->
      <vs-dropdown-menu class="vx-navbar-dropdown menu-dropdown-aeternus">
        <ul style="min-width: 9rem">
          <li class="flex py-2 px-4" @click="goToProfile">
            <feather-icon icon="UserIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Perfil</span>
          </li>

          <vs-divider class="m-1" />

          <li class="flex py-2 px-4" @click="openConfirmarSinPassword = true">
            <feather-icon icon="LogOutIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Salir</span>
          </li>
        </ul>
      </vs-dropdown-menu>
    </vs-dropdown>

    <!-- Confirm logout -->
    <ConfirmarDanger v-if="openConfirmarSinPassword" :show="openConfirmarSinPassword" :callback-on-success="logout"
      @closeVerificar="openConfirmarSinPassword = false" :accion="'Esta operación lo sacará del sistema.'"
      :confirmarButton="'Confirmar y salir'" />
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
  components: { ConfirmarDanger },
  methods: {
    closeDropdown() {
      this.$nextTick(() => {
        if (this.$refs.userDropdown) {
          this.$refs.userDropdown.closeDropdown();
        }
      });
    },
    goToProfile() {
      this.$router.push("/pages/profile").catch(() => { });
      this.closeDropdown();
    },
    logout() {
      this.$store.dispatch("auth/logout_user");
    },
    handleClickOutside(event) {
      const dropdownRef = this.$refs.userDropdown;
      if (dropdownRef && dropdownRef.$el && !dropdownRef.$el.contains(event.target)) {
        this.closeDropdown();
      }
    }

  },
  mounted() {
    // Load user info
    const user = localStorage.getItem("userInfo");
    if (user) {
      this.activeUserInfo = JSON.parse(user);
    } else {
      this.$store.dispatch("auth/user_datos").then(() => {
        this.activeUserInfo = JSON.parse(localStorage.getItem("userInfo"));
      });
    }

    // Close dropdown if clicking outside
    document.addEventListener("click", this.handleClickOutside);

    // Close dropdown on **any route change**
    this.unwatch = this.$router.afterEach(() => {
      this.closeDropdown();
    });
  },
  beforeDestroy() {
    document.removeEventListener("click", this.handleClickOutside);
    if (this.unwatch) this.unwatch(); // remove afterEach listener
  },
};
</script>
