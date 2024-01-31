<template>
    <VoterLayout page="Share Petition">
        <div class="mx-auto flex flex-col items-center justify-center bg-white dark:bg-gray-900 dark:text-white">
            <div class="flex flex-col px-6 sm:px-0">
                <h1 class="text-2xl md:text-5xl font-bold mb-4">Share it to get your first x signatures!</h1>
                <p class="mb-4">Maximize your chances to get signatures by sharing to as many different places as possible.</p>
                <button @click="copy(link)"
                    class="font-bold border-2 border-black dark:border-white py-2 rounded-md w-[90%] flex flex-row items-center justify-center mt-8">
                    <LinkIcon class="w-4 h-4" />
                    <span class=" ml-3">
                        Copy link
                    </span>
                </button>
                <div class="grid grid-cols-2 gap-4 my-8 w-[90%]">
                    <button @click="shareOnPlatform('facebook')"
                        class="justify-center font-bold border-2 border-black dark:border-white py-2 rounded-md flex flex-row items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 48 48">
                            <linearGradient id="Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1" x1="9.993" x2="40.615" y1="9.993" y2="40.615" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#2aa4f4"></stop><stop offset="1" stop-color="#007ad9"></stop></linearGradient><path fill="url(#Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1)" d="M24,4C12.954,4,4,12.954,4,24s8.954,20,20,20s20-8.954,20-20S35.046,4,24,4z"></path><path fill="#fff" d="M26.707,29.301h5.176l0.813-5.258h-5.989v-2.874c0-2.184,0.714-4.121,2.757-4.121h3.283V12.46 c-0.577-0.078-1.797-0.248-4.102-0.248c-4.814,0-7.636,2.542-7.636,8.334v3.498H16.06v5.258h4.948v14.452 C21.988,43.9,22.981,44,24,44c0.921,0,1.82-0.084,2.707-0.204V29.301z"></path>
                        </svg>
                        <span class="mr-2 ml-3">Facebook</span>
                    </button>
  
                    <button @click="shareOnPlatform('discord')"
                        class="justify-center font-bold border-2 border-black dark:border-white py-2 rounded-md flex flex-row items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="22" height="22" viewBox="0 0 40 40">
                            <path fill="#98ccfd" d="M10,37.5c-4.136,0-7.5-3.364-7.5-7.5V10c0-4.136,3.364-7.5,7.5-7.5h20c4.136,0,7.5,3.364,7.5,7.5v20	c0,4.136-3.364,7.5-7.5,7.5H10z"></path><path fill="#4788c7" d="M30,3c3.86,0,7,3.14,7,7v20c0,3.86-3.14,7-7,7H10c-3.86,0-7-3.14-7-7V10c0-3.86,3.14-7,7-7H30 M30,2	H10c-4.418,0-8,3.582-8,8v20c0,4.418,3.582,8,8,8h20c4.418,0,8-3.582,8-8V10C38,5.582,34.418,2,30,2L30,2z"></path><path fill="#fff" d="M30.909,11.909c0,0-3.126-2.446-6.818-2.727l-0.779,1.46c-2.73-0.39-3.941-0.403-6.752-0.067 l-0.651-1.392c-3.873,0.366-6.818,2.727-6.818,2.727S5.599,16.972,5,26.91C8.52,30.968,13.864,31,13.864,31l1.118-1.49 c-1.112-0.456-2.563-1.642-2.99-2.27C13.991,28.191,16.596,29,20,29c3.356,0,6.016-0.737,8.003-1.668 c-0.809,1.011-1.941,1.779-2.984,2.178L26.136,31c0,0,5.344-0.032,8.864-4.091C34.401,16.972,30.909,11.909,30.909,11.909z M15.486,24.3c-1.348,0-2.487-1.325-2.487-3.11s1.139-3.363,2.487-3.363s2.487,1.578,2.487,3.363S16.833,24.3,15.486,24.3z M24.489,24.3c-1.362,0-2.513-1.326-2.513-3.113c0-1.787,1.151-3.367,2.513-3.367c1.362,0,2.513,1.58,2.513,3.367 C27.002,22.974,25.851,24.3,24.489,24.3z"></path>
                        </svg>
                        <span class="mr-2 ml-3">Discord</span>
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-4 w-[90%]">
                    <button @click="shareOnPlatform('twitter')"
                        class="justify-center font-bold border-2 border-black dark:border-white py-2 rounded-md flex flex-row items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="21" height="21" viewBox="0 0 30 30">
                            <path d="M26.37,26l-8.795-12.822l0.015,0.012L25.52,4h-2.65l-6.46,7.48L11.28,4H4.33l8.211,11.971L12.54,15.97L3.88,26h2.65 l7.182-8.322L19.42,26H26.37z M10.23,6l12.34,18h-2.1L8.12,6H10.23z"></path>
                        </svg>
                        <span class="mr-2 ml-3">Twitter</span>
                    </button>
  
                    <button @click="shareOnPlatform('email')"
                        class="justify-center font-bold border-2 border-black dark:border-white py-2 rounded-md flex flex-row items-center justify-center">
                        <EnvelopeIcon class="w-5 h-5" />
                        <span class="mr-2 ml-3">Email</span>
                    </button>
                </div>
            </div>
            <div class="mt-8">
                <a :href="route('petitions.index', { petition: props.petition.hash })" class="border-b-2 border-sky-400 dark:border-sky-300 font-bold text-sky-400 dark:text-sky-300 text-center">Skip to petition dashboard</a>
            </div>
        </div>
    </VoterLayout>
</template>

<script lang="ts" setup>
import VoterLayout from "@/Layouts/VoterLayout.vue";
import { LinkIcon, EnvelopeIcon } from "@heroicons/vue/20/solid";
import PetitionData = App.DataTransferObjects.PetitionData;
import AlertService from "@/shared/Services/alert-service";
import { defineProps, ref } from "vue";

const props = defineProps<{
    petition: PetitionData;
    crumbs?: [];
}>();

let copy = (link) => {
    try {
        navigator.clipboard.writeText(link);
        AlertService.show(['Copied'], 'success');
    } catch (error) {
        AlertService.show(['Not Copied'], 'error');
    }
};

const link = ref(route('petitions.view', { petition: props.petition.hash }));

const shareOnPlatform = (platform) => {
    switch (platform) {
        case 'facebook':
            const facebookUrl = `https://www.facebook.com/sharer/sharer.php?u=${link.value}`;
            openSharingWindow(facebookUrl);
        break;
        case 'discord':
            // Discord sharing logic
        break;
        case 'twitter':
            const twitterText = encodeURIComponent('Check out this petition!');
            const twitterUrl = `https://twitter.com/intent/tweet?text=${twitterText}&url=${link.value}&hashtags=petition`;
            openSharingWindow(twitterUrl);
        break;
        case 'email':
            const emailSubject = encodeURIComponent('Check out this petition!');
            const emailBody = encodeURIComponent(`I thought you might be interested in this petition: ${link.value}`);
            const emailUrl = `mailto:?subject=${emailSubject}&body=${emailBody}`;
            openSharingWindow(emailUrl);
        break;
    }
};
  
const openSharingWindow = (url) => {
    window.open(url, '_blank');
};
</script>
  