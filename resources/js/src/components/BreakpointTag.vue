<template>
    <div class="breakpoint-tag">
        {{ breakpoint }}
    </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    name: "BreakpointTag",
    computed: {
        ...mapGetters(["windowBreakPoint"]),
        breakpoint() {
            return this.windowBreakPoint;
        }
    }
};
</script>

<style scoped>
.breakpoint-tag {
    position: fixed;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    background: #b18b1e;
    /* Elegant gold tone */
    color: white;
    font-weight: 600;
    font-size: 14px;
    padding: 5px 12px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    z-index: 100000;
    opacity: 0.85;
    transition: all 0.3s ease-in-out;
}
</style>
