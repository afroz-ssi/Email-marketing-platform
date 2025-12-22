<template lang="">
  <div>
    <div class="up_file_wrapper">
      <input
        type="file"
        v-bind="$attrs"
        @change="onFileChange"
        class="form-control border-gray-200"
        id="product_img"
        accept="image/jpeg,image/jpg,image/png,image/heif"
        multiple
      />
      <label for="product_img" class="file_up_btn"><i class="la la-upload"></i> upload image </label>
    </div>
    <output v-if="previewUrl || props.imageurl">
      <div class="img-outer-wrap">
        <figure v-for="(imgDB,index) in props.imageurl">
          <img :src="imgDB.image_url" height="100" width="100" />
          <a href="javascript:void(0);" class="img-cross-icon" @click="removeDBImg(index)"><img :src="'/admin_assets/close.png'" alt=""></a>
        </figure>
        <figure v-for="(imgurl,index) in previewUrl">
          <img :src="imgurl" height="100" width="100" />
          <a href="javascript:void(0);" class="img-cross-icon" @click="removeImg(index)"><img :src="'/admin_assets/close.png'" alt=""></a>
        </figure>
      </div>
    </output>
  </div>
</template>
<script setup>
import { ref } from "vue";

const props = defineProps(["imageurl"]);
const previewUrl = ref([]);

const removeImg = (index) => {
    emit.emit('imageRemoveLocal', index);
    previewUrl.value.splice(index, 1);    
}

const removeDBImg = (index) => {
    emit.emit('imageRemove', props.imageurl[index]);
    props.imageurl.splice(index, 1);    
}

function onFileChange(event) {
  const files = event.target.files;
  if (!files[0]) {
    return false;
  }
  for (let i = 0; i < files.length; i++) {
    const reader = new FileReader();
    reader.onload = function (e) {
      previewUrl.value[i] = e.target.result;
    };
    reader.readAsDataURL(files[i]);
  }
}
</script>
<style>
.img-outer-wrap {
  display: flex;
  flex-wrap: wrap;
  margin:-8px;
  padding-top: 25px;
}
.img-outer-wrap figure {
    padding:8px;
    margin-bottom: 0;
    position: relative;
}
.img-cross-icon {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 20px;
    height: 20px;
    background-color: #000080;
    border-radius: 50%;
    padding: 5px;
    position: absolute;
    top: 0;
    right: 0;
    border: none;
    outline: none;
    z-index: 3;
    transition: all .3s ease-in-out;
}
.img-cross-icon img {
    width: 100%;
    filter: brightness(0) invert(1);
}


.up_file_wrapper [type="file"] {
  display: none;
}

.file_up_btn {
  cursor: pointer;
  margin: 0;
  display: inline-flex;
  align-items: center;
  background: var(--LightTheme_color1);
  color: #ffffff;
  text-transform: capitalize;
  padding: 10px 18px;
  border-radius: 4px;
  font-size: 13px!important;
  font-weight: 500!important;
  transition: all .4s ease-in-out;
}

.file_up_btn i {
  display: inline-block;
  font-size: 15px;
  font-weight: 600;
  margin-right: 5px;
  position: relative;
}

.file_up_btn:hover {
  color: var(--LightTheme_color3) !important;
  background-color: var(--LightTheme_color1) !important;
  box-shadow: 0 0 0 1px var(--LightTheme_color1) !important;
  border: 0 !important;
}

</style>
