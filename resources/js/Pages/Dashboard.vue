<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

const currentStep = defineModel({default: 1})
const currentPrompt = defineModel('')
const promptHistory = ref([])
const activeClass = ref('p-4 text-green-700 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:border-green-800 dark:text-green-400')
const regularClass = ref('text-gray-900 bg-gray-100 border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400" role="alert')
const has_error = ref(false)
const error_message = ref('')

const setActiveStep = (step) => {
    currentStep.value = step
}

const getStepClass = (step) => {
    return step === currentStep.value ? activeClass.value : regularClass
}

const submitPrompt = async () => {
    try {
        const response = await axios.post('/prompt', {
            prompt: currentPrompt.value
        })

        console.log(response.data)

        promptHistory.value[currentStep.value] = response.data

        if (currentStep.value === 1) {
            currentPrompt.value = response.data.prompt_answer
            currentStep.value = 2
        }
    } catch (error) {
        if (error?.response?.status === 400) {
            error_message.value = error?.response?.data.error_message
        } else {
            error_message.value = 'An error happened submitting user prompt!'
        }
        has_error.value = true
    }
}

const getUserPrompts = async () => {
    try {
        const response = await axios.get('/prompts', {
            prompt: currentPrompt.value
        })

        promptHistory.value = response.data?.prompts
    } catch (error) {
        if (error?.response?.status === 400) {
            error_message.value = error?.response?.data.error_message
        } else {
            error_message.value = 'An error happened while getting user last prompts!'
        }
        has_error.value = true
    }
}

const getCurrentPrompt = computed(() => {
    return promptHistory.value[promptHistory.value.length - currentStep.value] ?? {
        prompt: '',
        prompt_answer: ''
    }
})

onMounted(() => {
    getUserPrompts()
})

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div id="toast-top-right" class="fixed flex items-center w-full max-w-xs p-4 space-x-4 text-gray-500 bg-white divide-x rtl:divide-x-reverse divide-gray-200 rounded-lg shadow top-5 right-5 dark:text-gray-400 dark:divide-gray-700 dark:bg-gray-800" role="alert" v-if="has_error">
                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
                    </svg>
                    <span class="sr-only">Error icon</span>
                </div>
                <div class="ms-3 text-sm font-normal">{{ error_message }}</div>
                <button 
                    type="button"
                    class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
                    data-dismiss-target="#toast-danger"
                    aria-label="Close"
                    @click="dismissError"
                >
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
            <div class="max-w-7xl mx-auto mb-12 flex flex-row space-x-4">
                <ol class="space-y-4 w-72">
                    <li @click="setActiveStep(1)">
                        <div :class="`w-full p-4 ${getStepClass(1)}`">
                            <div class="flex items-center justify-between">
                                <span class="sr-only">First Prompt</span>
                                <h3 class="font-medium">First Prompt</h3>
                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12"  v-if="currentStep === 1">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                                </svg>
                            </div>
                        </div>
                    </li>
                    <li @click="setActiveStep(2)">
                        <div :class="`w-full p-4 ${getStepClass(2)}`">
                            <div class="flex items-center justify-between">
                                <span class="sr-only">Second Prompt</span>
                                <h3 class="font-medium">Second Prompt</h3>
                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12" v-if="currentStep === 2">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                                </svg>
                            </div>
                        </div>
                    </li>
                </ol>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col p-4 grow">    
                    <div class="py-4">
                        <h1 class="text-3xl text-gray-800 font-semibold">
                            {{getCurrentPrompt.prompt}}
                        </h1>
                        <p class="mt-3 text-gray-500 grow">
                            {{getCurrentPrompt.prompt_answer}}
                        </p>
                    </div>
                    <div>
                        <label for="chat" class="sr-only">Your prompt</label>
                        <div class="flex items-center px-2 py-2 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <textarea 
                                id="chat"
                                rows="1"
                                class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Your prompt..."
                                v-model="currentPrompt"
                            >
                            </textarea>
                                <button
                                    class="inline-flex justify-center p-2 text-blue-600 rounded-full cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600"
                                    @click="submitPrompt"
                                >
                                <svg class="w-5 h-5 rotate-90 rtl:-rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                    <path d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z"/>
                                </svg>
                                <span class="sr-only">Send prompt</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

