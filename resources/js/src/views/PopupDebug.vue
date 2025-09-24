<!-- src/components/PopupDebug.vue -->
<template>
    <div class="debug-box" v-if="$popupManager.state.stack.length > 0">
        <h3>PopupManager Debug</h3>
        <ul>
            <li
                v-for="(popup, index) in $popupManager.state.stack"
                :key="index"
            >
                {{ index + 1 }}. {{ popup.popupId }}
            </li>
        </ul>
    </div>
</template>

<script>
export default {};
</script>

<style scoped>
.debug-box {
    position: fixed;
    bottom: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.7);
    color: #fff;
    padding: 10px;
    font-size: 12px;
    border-radius: 5px;
    z-index: 1000000 !important;
}
</style>
