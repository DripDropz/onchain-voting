<template>
    <div class="flex">
        <template v-if="actions?.length === 1">
            <ul>
                <li>
                    <NavLink
                        type="button"
                        :href="actions[0].link"
                        v-if="!!actions[0].link"
                        class="px-3 py-0.5 text-xs font-semibold text-white rounded shadow-sm bg-sky-500 dark:text-white hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
                    >
                        {{ actions[0].label }}
                    </NavLink>
                    <PrimaryButton
                        :theme="'primary'"
                        @click="handleAction(actions[0])"
                        v-if="!!actions[0].clickAction"
                        :disabled="actions[0].disabled"
                        :otherClasses="actions[0].disabled ? 'cursor-not-allowed' :''"
                        class="px-2 py-1 text-xs font-semibold text-white rounded-lg shadow-sm bg-sky-500 dark:text-white hover:bg-primary-500 hover:text-black focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
                    >
                        {{ actions[0].label }}
                    </PrimaryButton>
                </li>
            </ul>
        </template>
        <template v-else>
            <div class="inline-flex rounded-md shadow-sm">
                <button
                    type="button"
                    class="relative inline-flex items-center rounded-l-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 dark:text-sky-500 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10"
                >
                    <span>Options</span>
                </button>
                <Menu as="div" class="relative -ml-px block">
                    <MenuButton
                        class="relative inline-flex items-center rounded-r-md bg-white px-2 py-2 text-gray-400 dark:text-sky-500 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10"
                    >
                        <span class="sr-only">Open options</span>
                        <ChevronDownIcon class="h-5 w-5" aria-hidden="true" />
                    </MenuButton>
                    <transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <MenuItems
                            class="absolute right-0 z-20 -mr-1 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                        >
                            <div class="py-1">
                                <MenuItem v-for="item in actions" :key="item.label" v-slot="{ active }">
                                    <a
                                        :href="item.link"
                                        @click="handleAction(item)"
                                        :class="[active ? 'bg-gray-100 text-sky-500 dark:text-gray-900' : 'text-gray-700 dark:text-sky-500', 'block px-4 py-2 text-sm', { 'cursor-not-allowed': item.disabled, ...item.otherClasses }]"
                                        :disabled="item.disabled"
                                    >
                                        {{ item.label }}
                                    </a>
                                </MenuItem>
                            </div>
                        </MenuItems>
                    </transition>
                </Menu>
            </div>
        </template>
    </div>
</template>
  
  <script setup lang="ts">
  import NavLink from "@/Components/NavLink.vue";
  import PrimaryButton from "./PrimaryButton.vue";
  import { useConfigStore } from "@/stores/config-store";
  import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
  import { ChevronDownIcon } from '@heroicons/vue/20/solid';
  
  let configStore = useConfigStore();
  
  defineProps<{
      actions?: any[],
  }>();

  const handleAction = (action: any) => {
    if (action.clickAction) {
        if (!action.disabled) {
            configStore.toggleModal();
        }
    }
  };
  
</script>
