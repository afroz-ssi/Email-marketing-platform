<template lang="">

<div>
<!-- begin:: Header Mobile -->
<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
    <div class="kt-header-mobile__logo">
        <!-- <a href="index.html">
     <img alt="Logo" src="../assets/media/logos/logo-light.png" />
    </a> -->
    <Link :href="route('admin.dashboard')" class="logo_text_custom">
                 <!-- <span>Laravel</span> -->
                <!-- <img :src="'/admin_assets/logo/logo.png'" alt="" width="100%"
                    style="padding: 10px; width:100%;"> -->
            </Link>
    </div>
    <div class="kt-header-mobile__toolbar">
        <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" :class="hideShow ?'kt-header-mobile__toolbar-toggler--active': ''"
            id="kt_aside_mobile_toggler" @click="toggleMobileMenu"><span></span></button>
        <!-- <button class="kt-header-mobile__toggler" id="kt_header_mobile_toggler"><span></span></button> -->
        <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler" @click="toggleProfileMobileMenu"><i
                class="flaticon-more"></i></button>
    </div>
</div>
</div>
<!-- end:: Header Mobile -->



</template>
<script setup>

import { onMounted, ref } from "vue";

const hideShow = ref(false);
const profileMenu = ref(false);
onMounted(()=>{
    emit.on('closeMobileSideBar', function (arg) {
        hideShow.value = arg
    });
})
const toggleMobileMenu = () => {
    hideShow.value = !hideShow.value;
    emit.emit('toggleMobileMenu', hideShow.value);
}

const toggleProfileMobileMenu = () => {
    profileMenu.value = !profileMenu.value;
    emit.emit('toggleProfileMobileMenu', profileMenu.value);
}

</script>
<style lang="">

</style>