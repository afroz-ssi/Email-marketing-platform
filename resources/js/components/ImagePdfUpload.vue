<template>
  <div class="form-input">
    <div class="file-upload">
      <div class="file-uoload-style">
        <input
          type="file"
          id="imag"
          accept="image/*,application/pdf"
          @change="handleFileChange"
          ref="fileInput"
        />
      </div>

      <div v-if="imageUrl && fileType === 'image'" class="uploaded img-outer-wrap">
        <div class="image-wrap">
          <img :src="imageUrl" alt="Image Preview" id="ImgPreview" />
          <button class="btn-rmv1" v-if="isFileUploaded" @click="removeImage">X</button>
        </div>
      </div>

      <div v-else-if="fileType === 'pdf'" class="preview1 uploaded">
        <div class="mt-3 image-wrap_pdf">
          <i class="fa fa-file-pdf-o fa-4x"></i>
          <a :href="imageUrl" :download="fileName" class="ml-3">{{ fileName }}</a>
          <button class="btn-rmv1_pdf text-white ml-2" v-if="isFileUploaded" @click="removeImage">X</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'

const props = defineProps({
  initialImageUrl: String,
  form: Object,
  title: String
})

const imageUrl = ref(props.initialImageUrl || '')
const fileName = ref('');
const artFile = ref(null);
const fileType = ref('');
const errorMessage = ref('');
const isFileUploaded = ref(false);

const fileInput = ref(null);

function handleFileChange(event) {
  const file = event.target.files[0]
  if (!file) return

  const isImage = file.type.startsWith('image/')
  const isPdf = file.type === 'application/pdf'

  if (!isImage && !isPdf) {
    alert('Only image or PDF files are allowed!')
    clearFileInput()
    return
  }

  fileName.value = file.name;
  fileType.value = isPdf ? 'pdf' : 'image'

  const reader = new FileReader()
  reader.onload = (e) => {
    imageUrl.value = e.target.result
  }

  isFileUploaded.value = true
  reader.readAsDataURL(file)
}

function removeImage(event) {
  event.preventDefault()
  clearFileInput()
}

const removeDBImg = (index) => {
    emit.emit('imageRemove', props.imageurl[index]);
    props.imageurl.splice(index, 1);    
}


function clearFileInput() {
  artFile.value = null
  imageUrl.value = ''
  fileName.value = ''
  fileType.value = ''
  errorMessage.value = ''
  if (fileInput.value) fileInput.value.value = ''
}


function detectFileTypeFromUrl(url) {
  const extension = url.split('.').pop()?.toLowerCase()
  if (!extension) return ''

  if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension)) {
    return 'image'
  } else if (extension === 'pdf') {
    return 'pdf'
  }
  return ''
}


onMounted(() => {
  if (typeof imageUrl.value === 'string' && imageUrl.value.trim() !== '') {
    fileType.value = detectFileTypeFromUrl(imageUrl.value)
    fileName.value = imageUrl.value?.split('/').pop() || ''
    isFileUploaded.value = false;
  }
})
</script>

<style scoped>
.form-input {
  margin: 10px 0;
}

.img-outer-wrap {
  display: flex;
  flex-wrap: wrap;
  margin: -8px;
  padding-top: 25px;
}

.image-wrap {
  position: relative;
  display: inline-block;
  margin: 8px;
  width: 100px;
  height: 100px;
}

.image-wrap img#ImgPreview {
  width: 100px;
  height: 100px;
}

.btn-rmv1 {
  position: absolute;
  top: -5px;
  right: -5px;
  width: 20px;
  height: 20px;
  background-color: #e44242;
  color: white;
  font-size: 12px;
  font-weight: bold;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

.btn-rmv1_pdf {
  background-color: #e44242;
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  font-size: 12px;
  font-weight: bold;
  color: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.image-wrap_pdf {
  display: flex;
  align-items: center;
}
</style>
