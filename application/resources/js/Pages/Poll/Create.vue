<template>
    <VoterLayout>
      <div class="w-full">
        <div class="max-w-6xl mx-auto space-y-6 sm:px-6 lg:px-8">
          <div class="flex flex-col gap-8 p-4 bg-white sm:p-8 dark:bg-gray-800 sm:rounded-lg dark:text-white">
            <div class="flex justify-center w-full py-3">
              <p class="text-xl font-bold leading-tight xl:text-2xl">
                Create Your Poll
              </p>
            </div>
            <div class="flex flex-col w-full lg:flex-row gap-y-4">
              <div class="flex flex-col w-full lg:w-2/3 lg:px-12">
                <div class="flex items-center justify-center w-full mb-6">
                  <div class="flex flex-col w-full gap-4 p-4 border-2 border-gray-600 rounded-lg">
                    <input
                      type="textarea"
                      v-model="form.question"
                      placeholder="What's on your mind?"
                      class="text-lg italic font-bold outline-0 "
                      required
                    />
                    <div class="flex-auto" v-for="(option, index) in form.options" :key="index">
                      <div class="flex items-center ">
                        <div class="flex items-center w-5/6">
                          <span class="mr-2 text-sm font-bold leading-6 text-sky-500">
                            {{ index + 1 }}.
                          </span>
                          <input
        type="textarea"
        :value="getOption(index)"
        @input="setOption(index, ($event.target as HTMLInputElement).value)"
        class="w-full text-lg font-bold outline-0"
        required
      />
                        </div>
                        <button @click.prevent="removeOption(index)" style="color: rgb(14, 165, 233)">
                          <XMarkIcon aria class="w-5 h-5" />
                        </button>
                      </div>
                    </div>
                    <input
                    type="textarea"
                    v-model="newOption"
                    @keydown.enter.prevent="addOption"
                    placeholder="What else?"
                    class="w-full text-lg font-bold outline-0"
                  />
                  </div>
                </div>
  
                <div>
                  <div class="flex flex-col mt-8">
                    <div class="flex items-center">
                      <span class="text-2xl font-bold xl:text-3xl">
                        Publish results onchain?
                      </span>
                      <label class="relative inline-flex items-center ml-auto cursor-pointer">
                        <input type="checkbox" v-model="form.publishOnchain" class="sr-only peer" />
                        <div
                          class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-sky-300 dark:peer-focus:ring-sky-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-sky-400"
                        ></div>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
  
  
                <div class="bg-white rounded-lg dark:bg-gray-900 ml-20">
                    <div class="flex flex-col gap-4 mb-6">
                  <h3 class="text-xl font-bold xl:text-x2l">Poll Criteria </h3>
                  <p class="text-sm text-black/60">Users will need these items in their wallet in order to answer this poll</p>
                </div>
                  <div class="w-full">
                    <Criteria />
                  </div>
                </div>
            </div>
          </div>
        </div>
        <div class="bottom-0 flex justify-end gap-5 px-16 bg-white sm:p-8 dark:bg-gray-800 sm:rounded-lg dark:text-white">
          <Link
            :href="route('polls.index')"
            class="inline-flex items-center gap-x-2 rounded-md bg-white px-8 py-2.5 font-semibold text-sky-400 shadow-sm hover:bg-sky-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 border border-sky-400"
          >
            Back
          </Link>
          <button
            @click="submit"
            class="inline-flex items-center gap-x-2 rounded-md bg-sky-400 px-8 py-2.5 font-semibold text-white shadow-sm hover:bg-sky-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600"
          >
            Create Poll
          </button>
        </div>
      </div>
    </VoterLayout>
  </template>
  
  <script setup lang="ts">
  import VoterLayout from "@/Layouts/VoterLayout.vue";
  import PollData = App.DataTransferObjects.PollData;
  import Criteria from "@/shared/components/Criteria.vue";
  import { XMarkIcon } from "@heroicons/vue/20/solid";
  import { Link, useForm } from '@inertiajs/vue3';
  import AlertService from "@/shared/Services/alert-service";
  import { ref } from "vue";
  
  let form = useForm({
    question: '',
    options: [],
    publishOnchain: false,
  });

  let getOption = (index) => {
  return form.options[index];
};

let setOption = (index, value) => {
  form.options[index] = value;
};
  
  let newOption = ref('');

let addOption = () => {
  if (newOption.value) {
    form.options.push(newOption.value);
    newOption.value = '';
  }
};
  
  let removeOption = (index) => {
    form.options.splice(index, 1);
  };
  
  let submit = () => {
    if (form.question && form.options.every((option) => option !== '')) {
      AlertService.show(['Poll Created Successfully'], 'success');
      form.post(route('polls.store'));
    } else {
      AlertService.show(['Please fill in all required fields'], 'error');
    }
  };
  </script>
  