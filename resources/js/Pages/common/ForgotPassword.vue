<template lang="">
    <div>
        <Head title="Forgot Password" />

        <div class="welcome_scr">
            <div class="container">
                <div class="scr_body">
                    <div class="src_logo_area">
                        <!-- <a href="/admin/login" class=""><img :src="'/admin_assets/logo/logo.png'" alt=""></a> -->
                    <h2>{{ $page.props.appName }}</h2>

                    </div>
                    <h1 class="card-title text-center">Forgot Password</h1>
        
                    <form @submit.prevent="submit">
                        <div class="form-group">
                        <label for="email">Email</label>
                            <input type="email" id="email" v-model="form.email" class="form-control" placeholder="Email" >
                            <span class="text-danger" v-if="form.errors.email">{{ form.errors.email }}</span>
                        </div>
                        <div class="scr_footer">
                            <Link href="/admin/login" class="btn float-right">Back</Link>
                            <button type="submit" class="btn btn-primary"  :disabled="form.processing">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
   
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import SubmitButton from '../../components/SubmitButton.vue'

const form = useForm({
    email: '',
})

defineProps({
    errors: Object
})

function submit() {
    form.post(route('admin.forgotPassword'));
}
</script>
<style>
@import "/public/admin_assets/custom.css";
</style>
