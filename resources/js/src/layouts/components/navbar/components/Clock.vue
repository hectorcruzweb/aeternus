<template>
    <div class="clock-timer">
        {{ formattedTime }}
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

.clock-timer {
    text-transform: uppercase;
    font-weight: 400;
    color: $primary;
    border: 1px solid #e8e8e8;
    padding: 7px 14px;
    text-align: center;
    font-family: "Poppins", "Segoe UI", sans-serif;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    width: fit-content;
    transition: all 0.3s ease;
}

.clock-timer:hover {
    box-shadow: 0 4px 18px rgba(177, 139, 30, 0.25);
    transform: translateY(-2px);
}

.hora {
    font-size: 1.4rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: capitalize;
}
</style>
