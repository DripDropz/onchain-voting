<template>
    <Head title="Petition" />

    <AdminLayout>
        <template #header>
            <Nav :crumbs="props.crumbs" />
        </template>
              <div class="py-12">
                <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex gap-8 p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                     test
                    </div>
                </div>
            </div>
    </AdminLayout>

</template>

<script lang="ts" setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Nav from '../Breadcrumbs.vue';
import PetitionData = App.DataTransferObjects.PetitionData;


const props = defineProps<{
    petition: PetitionData;
    crumbs: []
}>();

</script>
