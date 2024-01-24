<template>
    <form class="flex flex-col gap-4" @submit.prevent="submitForm">
        <div>
            <h2 class="text-2xl font-bold mb-4 mt-8">Sign this petition</h2>
        </div>
        <div class="flex flex-col gap-3">
            <input
                v-model="firstName"
                type="text"
                placeholder="First Name"
                class="border-2 border-sky-400 rounded-lg"
            />
            <input
                v-model="lastName"
                type="text"
                placeholder="Last Name"
                class="border-2 border-sky-400 rounded-lg"
            />
            <input
                v-model="email"
                type="email"
                placeholder="Email"
                class="border-2 border-sky-400 rounded-lg"
            />
        </div>
        <div>
            <button type="submit" class="button bg-sky-400 text-white rounded-lg py-2 px-4 text-lg font-semibold mt-8">
                Sign
                this petition
            </button>
        </div>
    </form>
</template>

<script setup lang="ts">
import {ref} from 'vue';

const firstName = ref('');
const lastName = ref('');
const email = ref('');

const submitForm = () => {
    console.log('Form submitted:', {firstName: firstName.value, lastName: lastName.value, email: email.value});
};
</script>
