<template lang="">
    <div>   
        <input type="file" v-bind="$attrs" @change="onFileChange" class="form-control border-gray-200" />
        <div v-if="fileType == 'video'" class="mt-5">
            <output v-if="previewUrl">  
            <video :src="previewUrl" v-if="previewUrl" height="200" width="400" controls/>  
            </output>
        </div>
        <div v-if="backendMsg">
            <p class="text-danger">{{ backendMsg }}</p>
        </div>        
      </div> 
    </template>
<script setup>
import { onMounted, ref } from "vue";
const previewUrl = ref('');
const fileType = ref('');
const backendMsg = ref('');

const props = defineProps(['videoUrl']);
setTimeout(() => {
    previewUrl.value = props.videoUrl;
}, "100");


onMounted(() => {
    emit.on('videoUploadMsg', function (error) {
        backendMsg.value = error;
    });
    setTimeout(() => {
        if(props.videoUrl != ""){
            fileType.value = 'video';
        }
    }, "100");
})

function onFileChange(event) {  
    const file = event.target.files[0];
    if (!file) {
        return false
    }

    if (file.type.match('audio.*')) {
        fileType.value = 'audio';
        backendMsg.value = "File not supported.Please upload valid file format";
    }

    if (file.type.match('application.*')) {
        fileType.value = 'application';
        backendMsg.value = "File not supported.Please upload valid file format";
    }

    if (file.type.match('image.*')) {
        fileType.value = 'image';
        backendMsg.value = "File not supported.Please upload valid file format";
    }

    if (file.type.match('video.*')) {
        fileType.value = 'video';
        backendMsg.value = "";
    }

    const reader = new FileReader()
    reader.onload = function (e) {
        previewUrl.value = e.target.result
    }
    reader.readAsDataURL(file)
}

</script>
<style lang="">
        
</style>