<template>
    <div>


        <!-- begin:: Aside -->
        <button class="kt-aside-close " v-if="mobileMenuHideShow"
            @click="mobileMenuHideShow = !mobileMenuHideShow, closeMobileSidebar()" id="kt_aside_close_btn"><i
                class="la la-close"></i></button>
        <div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop"
            :class="{ 'kt-aside--on': mobileMenuHideShow }" id="kt_aside">
            <!-- begin:: Aside -->
            <div class="kt-aside__brand kt-grid__item " id="kt_aside_brand">
                <div class="kt-aside__brand-logoo">
                    <Link :href="route('admin.dashboard')" class="logo_text_custom" style="padding: 10px; ">
                    <!-- <img :src="'/admin_assets/logo.png'" alt="" width="100%" style="padding: 5px; width:100%;"> -->
                    <span style="color:white; padding:10px;font-weight:bold">{{ $page.props.appName }}</span>
                    </Link>
                </div>
                <div class="kt-aside__brand-tools">
                    <button class="kt-aside__brand-aside-toggler" id="kt_aside_toggler" @click="toggleSideMenu">
                        <span>
                        </span>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon id="Shape" points="0 0 24 0 24 24 0 24" />
                                    <path
                                        d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z"
                                        id="Path-94" fill="#000000" fill-rule="nonzero" />
                                    <path
                                        d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z"
                                        id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3"
                                        transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) " />
                                </g>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>

            <!-- end:: Aside -->

            <!-- begin:: Aside Menu -->
            <perfect-scrollbar>
                <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
                    <div id="kt_aside_menu" class="kt-aside-menu" data-ktmenu-vertical="1" data-ktmenu-scroll="1"
                        data-ktmenu-dropdown-timeout="500">

                        <ul class="kt-menu__nav ">
                            <li class="kt-menu__item  "
                                :class="{ 'kt-menu__item--active': $page.url === '/admin/dashboard' }"
                                aria-haspopup="true">
                                <Link :href="route('admin.dashboard')" class="kt-menu__link ">
                                <Icon icon="bx:home" color="white" width="20" height="20"
                                    class="kt-menu__link-icon mr-3" />
                                <span class="kt-menu__link-text">Dashboard</span>
                                </Link>
                            </li>

                            <li class="kt-menu__section" v-if="$page.props.permissions.includes('Lead')">
                                <h4 class="kt-menu__section-text">Lead Management</h4>
                                <i class="kt-menu__section-icon flaticon-more-v2">
                                </i>
                            </li>

                            <li class="kt-menu__item"
                                :class="{ 'kt-menu__item--active': $page.component.startsWith('Admin/lead') }"
                                aria-haspopup="true" v-if="$page.props.permissions.includes('Lead')">
                                <Link :href="route('admin.leads')" class="kt-menu__link">
                                  <Icon icon="carbon:user-multiple" width="20" height="20" class="kt-menu__link-icon mr-3" />
                                  <span class="kt-menu__link-text">Leads</span>
                                </Link>
                            </li>

                            <li class="kt-menu__section" v-if="$page.props.permissions.includes('Campaigns')">
                                <h4 class="kt-menu__section-text">Campaign Management</h4>
                                <i class="kt-menu__section-icon flaticon-more-v2">
                                </i>
                            </li>

                            <li class="kt-menu__item"
                                :class="{ 'kt-menu__item--active': $page.component.startsWith('Admin/campaigns') }"
                                aria-haspopup="true" v-if="$page.props.permissions.includes('Campaigns')">
                                <Link :href="route('admin.campaigns')" class="kt-menu__link">
                                    <Icon icon="carbon:bullhorn" color="white" width="20" height="20"
                                    class="kt-menu__link-icon mr-3" />
                                <span class="kt-menu__link-text">Campaign</span>
                                </Link>
                            </li>

                            <li class="kt-menu__section" v-if="$page.props.permissions.includes('OutlookAccount')">
                                <h4 class="kt-menu__section-text">Email Management</h4>
                                <i class="kt-menu__section-icon flaticon-more-v2">
                                </i>
                            </li>

                            <li class="kt-menu__item"
                                :class="{ 'kt-menu__item--active': $page.component.startsWith('Admin/outlook') }"
                                aria-haspopup="true" v-if="$page.props.permissions.includes('OutlookAccount')">
                                <Link :href="route('admin.outlookAccounts')" class="kt-menu__link">
                                    <Icon icon="mdi:email-outline" color="white" width="20" height="20"
                                    class="kt-menu__link-icon mr-3" />
                                <span class="kt-menu__link-text">Outlook Accounts</span>
                                </Link>
                            </li>
                            
                        </ul>
                    </div>
                </div>
            </perfect-scrollbar>
            <!-- end:: Aside Menu -->
        </div>
        <!-- end:: Aside -->
    </div>
</template>
<script setup>

import { onMounted, ref } from "vue";
import { route } from "ziggy-js";

const hideShow = ref(false);
const mobileMenuHideShow = ref(false);

onMounted(() => {
    document.querySelectorAll('.kt-menu__link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 1024) {
                mobileMenuHideShow.value = false;
                closeMobileSidebar();
            }
        });
    });
});
const closeMobileSidebar = () => {
    emit.emit('closeMobileSideBar', false);
};


const toggleSideMenu = () => {
    hideShow.value = !hideShow.value;
    emit.emit('toggleSideMenu', hideShow.value);
}

emit.on('toggleMobileMenu', function (arg1) {
    mobileMenuHideShow.value = arg1;
});

</script>