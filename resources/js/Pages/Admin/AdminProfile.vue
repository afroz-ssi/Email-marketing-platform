<template>
    <div>

        <Head title="My profile" />
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body">
                <h2>Profile</h2>
                <form @submit.prevent="submit">
                    <div class="form-group validated row">
                        <div class="form-group col-lg-6">
                            <label for="first_name">First name</label>
                            <input type="text" id="first_name" v-model="form.first_name"
                                class="form-control border-gray-200" placeholder="First name">
                            <span class="text-danger" v-if="form.errors.first_name">{{ form.errors.first_name }}</span>
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="last_name">Last name</label>
                            <input type="text" id="last_name" v-model="form.last_name"
                                class="form-control border-gray-200" placeholder="Last name">
                            <span class="text-danger" v-if="form.errors.last_name">{{ form.errors.last_name }}</span>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="email">Email</label>
                            <input type="email" id="email" v-model="form.email" class="form-control border-gray-200"
                                placeholder="Email">
                            <span class="text-danger" v-if="form.errors.email">{{ form.errors.email }}</span>
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="profile_photo">Profile Photo</label>
                            <input type="file" class="form-control border-gray-200" @change="handleProfilePhoto($event)"
                                accept="image/*">
                            <span class="text-danger" v-if="form.errors.profile_photo">{{ form.errors.profile_photo
                                }}</span>
                        </div>

                        <div class="col-lg-4 profile-edit-form-col" v-if="uploadedImage != ''">
                            <div class="bnrprv-wrpr">
                                <figure class="jb-lstbng-filimgbx">
                                    <cropper class="cropper" :src="uploadedImage" :stencil-props="{
                                        handlers: {},
                                        movable: false,
                                        resizable: false,
                                    }" :stencil-size="{
                                        width: 200,
                                        height: 200
                                    }" image-restriction="stencil" @change="onPositionChange" />
                                </figure>
                            </div>
                        </div>
                        <div class="col-lg-3 profile-edit-form-col">
                            <div class="preview-image-wrap">
                                <img class="preview-image" :src="previewImage" alt="" id="banner_add_image"
                                    style="height: 200px; width: 200px;">
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__foot">
                        <div class="kt-form__actions">
                            <div class="row">
                                <div class="pr-2">
                                    <submit-button :disabled="form.processing"
                                        :isLoading="form.processing">Submit</submit-button>
                                </div>
                                <div>
                                    <Link href="/admin/dashboard" class="btn btn-secondary">Cancel</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <admin-change-password />
    </div>
</template>

<script setup>

import { onMounted, ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import SubmitButton from '../../components/SubmitButton.vue'
import AdminChangePassword from './AdminChangePassword.vue'
import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css';

const props = defineProps({
    errors: Object,
    user: Object
});
const uploadedImage = ref('');
const previewImage = ref('');

const form = useForm({
    first_name: props.user?.first_name || null,
    last_name: props.user?.last_name || null,
    email: props.user?.email || null,
    profile_photo: null,
})

onMounted(() => {
    previewImage.value = props.user?.profile_photo_url || '';
    emit.emit('pageName', 'Profile Management', [{ title: "Profile", routeName: "admin.profile" }]);
});

const handleProfilePhoto = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            uploadedImage.value = e.target.result;
        };
        reader.onerror = () => {
            alert("Error reading file");
        };
        // Ensure the file type is supported
        if (file.type.startsWith("image/")) {
            reader.readAsDataURL(file);
        } else {
            console.error("Unsupported file type");
            alert("Unsupported file type");
        }
    }
};

const onPositionChange = ({ coordinates, canvas }) => {
    const newPreviewImage = canvas.toDataURL();
    if (previewImage.value !== newPreviewImage) {
        previewImage.value = newPreviewImage;
        form.profile_photo = newPreviewImage;
    }
};

function submit() {
    form.post(route('admin.profile'));
}

</script>