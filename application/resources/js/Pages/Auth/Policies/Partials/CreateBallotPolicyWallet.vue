<template>
    <div>
        <div class="mb-1">Here is your generated wallet seedphrase</div>
        <TextareaInput rows="4"  v-model="value" />
    </div>
</template>
<script lang="ts" setup>
import TextareaInput from "@/Components/TextareaInput.vue";
import { generateSeedPhrase } from "@lucid-cardano";
import { computed } from "vue";

const props = defineProps<{seedphrase: string}>();
const emit = defineEmits(['update:seedphrase'])

const value = computed({
  get() {
    return props.seedphrase
  },
  set(value) {
    emit('update:seedphrase', value)
  }
});

setTimeout(() => {
    value.value = generateSeedPhrase();
}, 100);
</script>
