<!-- src/components/PopupDebug.vue -->
<template>
    <div class="debug-box">
        <h3>PopupManager Debug</h3>
        <ul>
            <li v-for="(popup, index) in popupStack" :key="index">
                {{ index + 1 }}. {{ popup.popupId }}
            </li>
        </ul>
        456
    </div>
</template>

<script>
import PopupManager from '../utils/PopupManager';

export default {
    computed: {
        popupStack() {
            return PopupManager.state.stack
        }
    }
}
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
    z-index: 100000 !important;
}
</style>
