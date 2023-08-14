<template>
    <div>
        <div class="mb-1">Paste your seedphrase below</div>
        <TextareaInput rows="4" v-model="value" />
    </div>
</template>
<script lang="ts" setup>
import { ref } from 'vue';
import TextareaInput from "@/Components/TextareaInput.vue";



const value = ref('')
</script>
