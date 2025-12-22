<template>
    <div>
        <loading v-model:active="isLoading" :can-cancel="true" :is-full-page="true" />
        <div class="kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed"
            :class="{ 'kt-aside--minimize': menuHideShow, 'kt-aside--on': mobileMenuHideShow, 'kt-header__topbar--mobile-on': mobileProfileMenuHideShow }">
            <mobile-nav />
            <div class="kt-grid kt-grid--hor kt-grid--root">
                <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
                    <Nav />
                    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
                        <Header />
                        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
                            <sub-header />
                            <div class="kt-content kt-grid__item kt-grid__item--fluid">
                                <slot />
                            </div>
                        </div>
                        <Footer />
                    </div>
                </div>
            </div>
            <div id="kt_scrolltop" class="kt-scrolltop">
                <i class="fa fa-arrow-up"></i>
            </div>
        </div>
    </div>
</template>
<script setup>
import '/public/admin_assets/vendors/general/bootstrap/dist/js/bootstrap.min.js';
import Nav from './Nav.vue';
import Footer from './Footer.vue';
import Header from './Header.vue';
import SubHeader from './SubHeader.vue';
import MobileNav from './MobileNav.vue';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
import { onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const isLoading = ref(false);
const menuHideShow = ref(false);
const mobileMenuHideShow = ref(false);
const mobileProfileMenuHideShow = ref(false);

router.on("start", () => {
    isLoading.value = true;
});

router.on("finish", () => {
    isLoading.value = false;
});

onMounted(() => {
    isLoading.value = false;
    emit.on('toggleSideMenu', function (arg1) {
        menuHideShow.value = arg1;
    });
    emit.on('toggleMobileMenu', function (arg1) {
        mobileMenuHideShow.value = arg1;
    });
    emit.on('toggleProfileMobileMenu', function (arg1) {
        mobileProfileMenuHideShow.value = arg1;
    });
});
</script>

<style>
@import "/public/admin_assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.min.rtl.css";
@import "/public/admin_assets/vendors/general/bootstrap-select/dist/css/bootstrap-select.min.css";
@import "/public/admin_assets/vendors/general/socicon/css/socicon.css";
@import "/public/admin_assets/vendors/custom/vendors/line-awesome/css/line-awesome.css";
@import "/public/admin_assets/vendors/custom/vendors/flaticon/flaticon.css";
@import "/public/admin_assets/vendors/custom/vendors/flaticon2/flaticon.css";
@import "/public/admin_assets/vendors/custom/vendors/fontawesome5/css/all.min.css";
@import "/public/admin_assets/demo/default/base/style.bundle.min.css";
@import "/public/admin_assets/demo/default/skins/header/base/light.css";
@import "/public/admin_assets/demo/default/skins/header/menu/light.css";
@import "/public/admin_assets/demo/default/skins/brand/dark.css";
@import "/public/admin_assets/demo/default/skins/aside/dark.css";
@import "/public/admin_assets/vendors/custom/datatables/datatables.bundle.css";
@import "/public/admin_assets/custom.css";
</style>