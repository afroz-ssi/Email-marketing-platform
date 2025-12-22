<template lang="">
    <footer class="page-footer cmn-gap">
        <div class="container">
            <div class="page-footer-copyright-info">
                <p>©{{ new Date().getFullYear() }} <a href="/"> {{usePage().props.appName }} </a>. All rights reserved.</p>
            </div>
        </div>
    </footer>
</template>
<script setup>
  import { usePage } from '@inertiajs/vue3'
</script>
<style lang="">
    
</style>