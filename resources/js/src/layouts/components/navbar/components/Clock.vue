<template>
    <div>
        <div class="clock-container">
            <p class="material-icons">schedule</p>
            <p class="clock-timer">{{ formattedTime }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "Clock",
    data() {
        return {
            time: new Date(),
        };
    },
    computed: {
        formattedTime() {
            const options = {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
                second: '2-digit'
            };
            return this.time.toLocaleString("es-MX", options);
        },
    },
    mounted() {
        // Update every second
        this.timer = setInterval(() => {
            this.time = new Date();
        }, 1000);
    },
    beforeDestroy() {
        clearInterval(this.timer);
    },
};
</script>

<style lang="scss" scoped>
@import "../../../../../../sass/vuexy/variables";

.clock-container {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
    justify-content: space-between;
    font-weight: 500;
    color: $primary;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;

    .clock-timer {
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
}
</style>
