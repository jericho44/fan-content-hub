<template>
    <div class="vue-loader-fan-content-hub"
        :style="{ border: '5px solid', borderColor: props.color ? props.color : '#EE7B33' }" />
</template>

<script setup lang="ts">
interface LoaderProps {
    color: string,
}
const props = withDefaults(defineProps<LoaderProps>(), {
    color: '#EE7B33', // Default value for removable
});

</script>

<style scoped>
.vue-loader-fan-content-hub {
    border-radius: 50%;
    border-top: 5px solid #f2f2f2 !important;
    width: 40px;
    height: 40px;
    -webkit-animation: spinLoaderFanContentHub 2s linear infinite;
    animation: spinLoaderFanContentHub 2s linear infinite;
}

/* Safari */
@-webkit-keyframes spinLoaderFanContentHub {
    0% {
        -webkit-transform: rotate(0deg);
    }

    100% {
        -webkit-transform: rotate(360deg);
    }
}

@keyframes spinLoaderFanContentHub {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>
