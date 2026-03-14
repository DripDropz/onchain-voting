<template>
    <div class="relative overflow-hidden rounded-xl border border-sky-500/20 bg-gradient-to-br from-gray-900 via-gray-900 to-sky-950/30 shadow-lg shadow-sky-900/10">
        <!-- Accent glow line at top -->
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-sky-500/60 to-transparent" />

        <!-- Verified badge header -->
        <div class="flex items-center gap-2.5 px-4 pt-4 pb-3 border-b border-gray-800/70">
            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-sky-500/15 ring-1 ring-sky-500/30">
                <ShieldCheckIcon class="w-4 h-4 text-sky-400" />
            </div>
            <span class="text-xs font-semibold uppercase tracking-widest text-sky-400">Verified Signature</span>
        </div>

        <!-- Fields -->
        <div class="px-4 py-2 space-y-0.5">
            <!-- Created -->
            <div class="flex items-center justify-between py-2.5 border-b border-gray-800/50">
                <div class="flex items-center gap-2 text-xs text-gray-500 uppercase tracking-wide">
                    <CalendarIcon class="w-3.5 h-3.5 shrink-0" />
                    Created
                </div>
                <div class="text-sm text-gray-200 font-medium tabular-nums">
                    {{ formatDate(signature.created_at) }}
                </div>
            </div>

            <!-- Type -->
            <div class="flex items-center justify-between py-2.5 border-b border-gray-800/50">
                <div class="flex items-center gap-2 text-xs text-gray-500 uppercase tracking-wide">
                    <TagIcon class="w-3.5 h-3.5 shrink-0" />
                    Type
                </div>
                <span
                    class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-semibold"
                    :class="signature.wallet_signature
                        ? 'bg-sky-500/15 text-sky-300 ring-1 ring-sky-500/25'
                        : 'bg-purple-500/15 text-purple-300 ring-1 ring-purple-500/25'"
                >
                    <WalletIcon v-if="signature.wallet_signature" class="w-3 h-3" />
                    <EnvelopeIcon v-else class="w-3 h-3" />
                    {{ getSignatureType() }}
                </span>
            </div>

            <!-- Email -->
            <div v-if="signature.email_signature" class="flex items-center justify-between py-2.5 border-b border-gray-800/50">
                <div class="flex items-center gap-2 text-xs text-gray-500 uppercase tracking-wide">
                    <EnvelopeIcon class="w-3.5 h-3.5 shrink-0" />
                    Email
                </div>
                <div class="text-sm text-gray-200 font-mono">
                    {{ signature.email_signature }}
                </div>
            </div>

            <!-- Address -->
            <div v-if="signature.stake_address" class="flex items-start justify-between py-2.5 border-b border-gray-800/50 gap-4">
                <div class="flex items-center gap-2 text-xs text-gray-500 uppercase tracking-wide shrink-0 pt-0.5">
                    <LinkIcon class="w-3.5 h-3.5 shrink-0" />
                    Address
                </div>
                <div class="text-xs text-gray-300 font-mono break-all text-right leading-relaxed">
                    {{ maskedAddress }}
                </div>
            </div>

            <!-- Signature hash -->
            <div v-if="signature.wallet_signature" class="flex items-start justify-between py-2.5 gap-4">
                <div class="flex items-center gap-2 text-xs text-gray-500 uppercase tracking-wide shrink-0 pt-0.5">
                    <FingerPrintIcon class="w-3.5 h-3.5 shrink-0" />
                    Sig
                </div>
                <div class="text-xs text-gray-400 font-mono break-all text-right leading-relaxed">
                    {{ maskedSignature }}
                </div>
            </div>
        </div>

        <!-- Footer branding -->
        <div class="flex items-center justify-end gap-1.5 px-4 py-2.5 border-t border-gray-800/60 bg-gray-900/40">
            <img :src="voteAppLogo" alt="ChainVote Logo" class="w-4 h-4 opacity-80" />
            <span class="text-xs font-bold tracking-tight text-gray-400">ChainVote</span>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { CalendarIcon, TagIcon, LinkIcon, FingerPrintIcon, ShieldCheckIcon } from '@heroicons/vue/20/solid';
import { WalletIcon, EnvelopeIcon } from '@heroicons/vue/24/outline';
import SignatureData = App.DataTransferObjects.SignatureData;
import voteAppLogo from '../../../../images/openchainvote.png';

const props = defineProps<{
    signature: SignatureData;
}>();

const formatDate = (dateString: string): string => {
    const options = { month: '2-digit', day: '2-digit', year: '2-digit' } as const;
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const getSignatureType = (): string => {
    if (props.signature.wallet_signature) {
        return 'Wallet';
    } else if (props.signature.email_signature) {
        return 'Email';
    } else {
        return 'Unknown';
    }
};

const maskedAddress = props.signature.stake_address?.slice(0, 12) + '****' + props.signature.stake_address?.slice(-6);
const maskedSignature = props.signature.wallet_signature?.slice(0, 12) + '****' + props.signature.wallet_signature?.slice(-6);
</script>