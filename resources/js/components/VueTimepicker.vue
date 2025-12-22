<template>
    <VueDatePicker :format="format" placeholder="Choose Date" :model-value="date" @update:model-value="handleDate"
        auto-apply @cleared="clearData" :enable-time-picker="true" :week-start="0"/>
</template>
    
<script setup>
import { computed, onMounted, ref } from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import moment from 'moment';
import { usePage } from '@inertiajs/vue3';



const { modelValue } = defineProps(['modelValue'])
const emit = defineEmits(['update:modelValue']);


const date = ref();
date.value = modelValue;

const adminFormat = computed(() => usePage().props.dateFormat);
const format = ref('MM/dd/yyyy HH:mm');

onMounted(() => {
    if (adminFormat.value == 'DD/MM/YYYY HH:mm') {
        format.value = 'dd/MM/yyyy HH:mm';
    }
    if (adminFormat.value == 'MM/DD/YYYY HH:mm') {
        format.value = 'MM/dd/yyyy HH:mm';
    }
    if (adminFormat.value == 'YYYY/MM/DD HH:mm') {
        format.value = 'yyyy/MM/dd HH:mm';
    }

})

const handleDate = (modelData) => {
    // alert(date.value);
    let returnFormat = ''
    if (modelData) {
        date.value = moment(modelData).format('yyyy-MM-DD HH:mm');
        // date.value = moment(modelData).format(format.value.toUpperCase().replace(/\//g,'-'));
    }
    emit('update:modelValue', date.value)
    // do something else with the data
}

const clearData = () => {
    // alert('fff');
    emit('update:modelValue', '');
}

</script>
<style>
.dp__month_year_wrap .dp__month_year_select{
    color: #fff !important;
}
</style>