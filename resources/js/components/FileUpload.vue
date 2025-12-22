<template lang="">
    <div>
        <input type="file" v-bind="$attrs" @change="onFileChange" class="form-control border-gray-200" />
        <div v-if="fileType == 'image'">
            <output v-if="previewUrl">  
            <img :src="previewUrl" v-if="previewUrl" height="100" width="100" >   
            </output>
        </div>
        <div v-if="backendMsg != ''">
            <p class="text-danger">{{ backendMsg }}</p>
        </div>        
    </div> 
</template>

<script setup>
import { onMounted, ref } from "vue";
const previewUrl = ref('');
const fileType = ref('');
const backendMsg = ref('');

const props = defineProps(['imageurl']);
setTimeout(() => {
    previewUrl.value = props.imageurl;
}, "100");

onMounted(() => {
    emit.on('fileuploadmessage', function (error) {
        backendMsg.value = error;
    });
    setTimeout(() => {
        if(props.imageurl != ""){
            fileType.value = 'image';
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
    }

    if (file.type.match('video.*')) {
        fileType.value = 'video';
        backendMsg.value = "File not supported.Please upload valid file format";
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