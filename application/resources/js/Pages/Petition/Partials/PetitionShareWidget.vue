<template>
    <div>
        <!-- Copy link row -->
        <div class="flex items-center gap-2 mb-4">
            <div class="flex items-center flex-1 gap-2 px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-sm text-gray-400 min-w-0 overflow-hidden">
                <LinkIcon class="w-4 h-4 shrink-0 text-gray-500" />
                <span class="truncate text-xs">{{ link }}</span>
            </div>
            <button
                @click="copyLink"
                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2.5 rounded-lg text-sm font-medium bg-gray-800 border border-gray-700 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
            >
                <ClipboardDocumentIcon class="w-4 h-4" />
                {{ copied ? 'Copied!' : 'Copy' }}
            </button>
        </div>

        <!-- Social buttons -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            <button
                v-for="s in socialPlatforms"
                :key="s.id"
                @click="shareOn(s.id)"
                class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold border transition-colors"
                :class="s.cls"
            >
                <component :is="s.icon" class="w-4 h-4 shrink-0" />
                {{ s.label }}
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { LinkIcon, ClipboardDocumentIcon, EnvelopeIcon } from '@heroicons/vue/20/solid';
import AlertService from '@/shared/Services/alert-service';

const props = defineProps<{
    link: string;
    title?: string;
}>();

const copied = ref(false);

const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(props.link);
        copied.value = true;
        AlertService.show(['Link copied to clipboard'], 'success');
        setTimeout(() => { copied.value = false; }, 2000);
    } catch {
        AlertService.show(['Could not copy link'], 'error');
    }
};

const shareOn = (platform: string) => {
    const url = encodeURIComponent(props.link);
    const title = encodeURIComponent(props.title ?? 'Sign this petition');
    const links: Record<string, string> = {
        twitter:  `https://twitter.com/intent/tweet?text=${title}&url=${url}&hashtags=petition`,
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${url}`,
        whatsapp: `https://wa.me/?text=${title}%20${url}`,
        email:    `mailto:?subject=${title}&body=${encodeURIComponent('I thought you might be interested in this petition: ' + props.link)}`,
    };
    if (links[platform]) window.open(links[platform], '_blank');
};

const TwitterIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.743l7.73-8.835L1.254 2.25H8.08l4.259 5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>`,
};
const FacebookIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M24 12.073C24 5.404 18.627 0 12 0 5.372 0 0 5.404 0 12.073c0 6.021 4.388 11.01 10.125 11.927V15.57H7.079v-3.497h3.046V9.41c0-3.025 1.791-4.697 4.532-4.697 1.313 0 2.686.235 2.686.235v2.97h-1.513c-1.491 0-1.955.931-1.955 1.887v2.267h3.328l-.532 3.497h-2.796v8.43C19.612 23.083 24 18.094 24 12.073z"/></svg>`,
};
const WhatsAppIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>`,
};

const socialPlatforms = [
    { id: 'twitter',  label: 'X / Twitter', icon: TwitterIcon,  cls: 'bg-gray-800 border-gray-700 text-gray-300 hover:bg-gray-700 hover:text-white' },
    { id: 'facebook', label: 'Facebook',    icon: FacebookIcon, cls: 'bg-blue-900/30 border-blue-700/30 text-blue-300 hover:bg-blue-900/60' },
    { id: 'whatsapp', label: 'WhatsApp',    icon: WhatsAppIcon, cls: 'bg-emerald-900/30 border-emerald-700/30 text-emerald-300 hover:bg-emerald-900/60' },
    { id: 'email',    label: 'Email',       icon: EnvelopeIcon, cls: 'bg-gray-800 border-gray-700 text-gray-300 hover:bg-gray-700 hover:text-white' },
];
</script>
