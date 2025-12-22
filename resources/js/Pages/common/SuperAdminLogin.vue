<template lang="">
    <div>

        <Head title="Admin Login" />
        <div class="welcome_scr">
            <div class="container">
                <div class="scr_body">
                    <div class="src_logo_areaa">
                         <h1>{{ $page.props.appName }}</h1>
                    </div>
                    <h1 class="card-title text-center">Admin Login</h1>

                    <form @submit.prevent="handleSubmit">
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" id="email" v-model="form.email" class="form-control" placeholder="Email">
                            <span class="text-danger" v-if="form.errors.email">{{ form.errors.email }}</span>
                        </div>

                        <div class="form-group">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <div class="password_box">
                                <input :type="showPassword ? 'text' : 'password'" id="password" v-model="form.password" class="form-control" placeholder="Password">
                                <div class="control">
                                    <span class="icon is-small is-right">
                                        <i @click="toggleShow" class="fas" :class="{ 'fa-eye-slash': showPassword, 'fa-eye': !showPassword }"></i>
                                    </span>
                                </div>
                            </div>
                            <span class="text-danger" v-if="form.errors.password">{{ form.errors.password }}</span>
                        </div>
                        <div class="scr_footer">
                            <Link :href="route('admin.forgotPassword')">Forgot Password?</Link>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card d-flex justify-content-center">
        <Dialog
            v-model:visible="visible"
            modal
            header="Enter Verification Code"
            :style="{ width: '42rem' }"
            :draggable="false">
            <!-- Form Body -->
            <form @submit.prevent="submitOtp" class="px-3 py-2">
                <div class="form-group mb-3">
                    <label for="otp" class="form-label fw-semibold">Verification Code</label>
                    <InputText id="otp" v-model="form.otp" placeholder="Enter 6-digit code" class="form-control" autocomplete="one-time-code" maxlength="6" inputmode="numeric" required oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')" />
                    <small v-if="form.otp && !/^\d{6}$/.test(form.otp)" class="text-danger">
                        Please enter a valid 6-digit code.
                    </small>
                    <span class="text-danger" v-if="form.errors.otp">{{ form.errors.otp }}</span>
                </div>

                <p class="text-muted small mb-4">
                    Enter the verification code sent to your email.
                    If you didn't receive the code or lost access to your email, please contact support.
                </p>

                <!-- Footer Buttons -->
                <div class="d-flex justify-content-end gap-2">
                    <Button
                        type="button"
                        label="Close"
                        class="mr-2"
                        severity="secondary"
                        @click="visible = false" />
                    <Button
                        type="submit"
                        label="Verify"
                        class="btn btn-primary" />
                </div>
            </form>
        </Dialog>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    email: "",
    password: "",
    otp: "",
});

const visible = ref(false);

defineProps({
    errors: Object
})


function handleSubmit() {
    form.post(route("admin.login-submit"), {
        preserveScroll: true,
        onSuccess: (response) => {
            if (response?.props?.props_data == 'opt_send') {
                visible.value = true;
            }
        },
        onError: () => {
            visible.value = false;
        }
    });
}

function submitOtp() {
    form.post(route("admin.verify-otp"));
}

const showPassword = ref(false);

const toggleShow = async () => {
    showPassword.value = !showPassword.value;
}
</script>

<style>
@import "/public/admin_assets/custom.css";
</style>