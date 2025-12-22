<template lang="">
<loading v-model:active="isLoading" :can-cancel="true" :is-full-page="true" />
    <Header/>
        <slot/>
    <Footer/>
        
</template>
<script setup>
import Footer from "./Footer.vue";
import Header from "./Header.vue";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
import { computed, ref } from "vue";
import { usePage, router } from "@inertiajs/vue3";

const isLoading = ref(false);

router.on("start", () => {
    isLoading.value = true;
});

router.on("finish", () => {
    isLoading.value = false;
});

const flash = computed(() => {
    return usePage().props.flash;
});

const props = defineProps(["flash"]);
</script>

<style></style>