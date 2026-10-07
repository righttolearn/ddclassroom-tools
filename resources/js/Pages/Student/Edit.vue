<template>

    <Head title="Edit student" />
    <AuthenticatedLayout>

        <!-- student list btn -->
        <div class="my-2">
            <Link href="/students"
                class="pointer-events-auto  mb-2 rounded-md bg-indigo-600 px-3 py-2 text-[0.8125rem] font-semibold leading-5 text-white hover:bg-indigo-500">
            Student list
            </Link>
        </div>
        <div class="py-4 bg-sky-30 rounded-lg border">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class=" overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Update Student -->
                    <form @submit.prevent="submit">
                        <div class="space-y-12">

                            <div class="border-b border-gray-900/10 pb-12">
                                <h2 class="text-base font-semibold leading-7 text-gray-900">Create student</h2>
                                <p class="mt-1 text-sm leading-6 text-gray-600">You can create a new student.
                                </p>

                                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                    <!-- Name -->
                                    <div class="sm:col-span-3">
                                        <label for="first-name"
                                            class="block text-sm font-medium leading-6 text-gray-900">First
                                            name</label>
                                        <div class="mt-2">
                                            <input type="text" v-model="form.name" name="first-name" id="first-name"
                                                autocomplete="given-name"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.name">{{ errors . name }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Last Name -->
                                    <div class="sm:col-span-3">
                                        <label for="last_name"
                                            class="block text-sm font-medium leading-6 text-gray-900">
                                            Last Name</label>
                                        <div class="mt-2">
                                            <input id="last_name" v-model="form.last_name" name="last_name"
                                                type="text" autocomplete="last_name"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.last_name">
                                                {{ errors . last_name }}</p>
                                        </div>
                                    </div>


                                    <!-- Father Name -->
                                    <div class="sm:col-span-3">
                                        <label for="father_name"
                                            class="block text-sm font-medium leading-6 text-gray-900">
                                            Father Name</label>
                                        <div class="mt-2">
                                            <input id="father_name" v-model="form.father_name" name="father_name"
                                                type="text" autocomplete="father_name"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.father_name">
                                                {{ errors . father_name }}</p>
                                        </div>
                                    </div>

                                    <!-- Farsi Name -->
                                    <div class="sm:col-span-3">
                                        <label for="fa-first-name"
                                            class="block text-sm font-medium leading-6 text-gray-900">Farsi First
                                            Name</label>
                                        <div class="mt-2">
                                            <input type="text" v-model="form.fa_name" name="fa-first-name" id="fa-first-name"
                                                autocomplete="given-name"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.fa_name">{{ errors . fa_name }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Farsi Last Name -->
                                    <div class="sm:col-span-3">
                                        <label for="fa_last_name"
                                            class="block text-sm font-medium leading-6 text-gray-900">
                                            Farsi Last Name</label>
                                        <div class="mt-2">
                                            <input id="fa_last_name" v-model="form.fa_last_name" name="fa_last_name"
                                                type="text" autocomplete="fa_last_name"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.fa_last_name">
                                                {{ errors . fa_last_name }}</p>
                                        </div>
                                    </div>


                                    <!-- Farsi Father Name -->
                                    <div class="sm:col-span-3">
                                        <label for="fa_father_name"
                                            class="block text-sm font-medium leading-6 text-gray-900">
                                            Farsi Father Name</label>

                                        <div class="mt-2">
                                            <input id="fa_father_name" v-model="form.fa_father_name" name="fa_father_name"
                                                type="text" autocomplete="fa_father_name"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.fa_father_name">
                                                {{ errors . fa_father_name }}</p>
                                        </div>
                                    </div>

                                    <!-- Moodle ID -->
                                    <div class="sm:col-span-3">
                                        <label for="id_number"
                                            class="block text-sm font-medium leading-6 text-gray-900">
                                            Moodle ID</label>
                                        <div class="mt-2">
                                            <input id="id_number" v-model="form.id_number"
                                                name="id_number" type="text" autocomplete="id_number"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.id_number">
                                                {{ errors . id_number }}</p>
                                        </div>
                                    </div>

                                    <!-- Name In System -->
                                    <div class="sm:col-span-3">
                                        <label for="name_in_system"
                                            class="block text-sm font-medium leading-6 text-gray-900">
                                            Name In System</label>
                                        <div class="mt-2">
                                            <input id="name_in_system" v-model="form.name_in_system"
                                                name="name_in_system" type="text" autocomplete="name_in_system"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.name_in_system">
                                                {{ errors . name_in_system }}</p>
                                        </div>
                                    </div>

                                    <!-- Email address -->
                                    <div class="sm:col-span-3">
                                        <label for="email"
                                            class="block text-sm font-medium leading-6 text-gray-900">Email
                                            address</label>
                                        <div class="mt-2">
                                            <input id="email" v-model="form.email" name="email" type="email"
                                                autocomplete="email"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.email">
                                                {{ errors . email }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Username -->
                                    <div class="sm:col-span-3">
                                        <label for="username" class="block text-sm font-medium leading-6 text-gray-900">
                                            Username</label>
                                        <div class="mt-2">
                                            <input id="username" v-model="form.username" name="username" type="text"
                                                autocomplete="username"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.username">
                                                {{ errors . username }}</p>
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    <div class="sm:col-span-3">
                                        <label for="phone" class="block text-sm font-medium leading-6 text-gray-900">
                                            Phone</label>
                                        <div class="mt-2">
                                            <input id="phone" v-model="form.phone" name="phone" type="text"
                                                autocomplete="phone"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.phone">
                                                {{ errors . phone }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Support Type -->
                                    <div v-if="canManageSupportType" class="sm:col-span-3">
                                        <label for="support_type"
                                            class="block text-sm font-medium leading-6 text-gray-900">
                                            Support Type
                                        </label>
                                        <div class="mt-2">
                                            <select id="support_type" v-model="form.support_type" name="support_type"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                <option value="">Not Set</option>
                                                <option value="credit_card">Credit Card</option>
                                                <option value="cash">Cash</option>
                                            </select>
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.support_type">
                                                {{ errors.support_type }}
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Father Phone -->
                                    <div class="sm:col-span-3">
                                        <label for="father_phone" class="block text-sm font-medium leading-6 text-gray-900">
                                            Father Phone</label>
                                        <div class="mt-2">
                                            <input id="father_phone" v-model="form.father_phone" name="father_phone" type="text"
                                                autocomplete="father_phone"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.father_phone">
                                                {{ errors . father_phone }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Country -->
                                    <div class="sm:col-span-3">
                                        <label for="country_id"
                                            class="block text-sm font-medium leading-6 text-gray-900">Country
                                        </label>
                                        <div class="mt-2">
                                            <select v-model="form.country_id" name="country" id="country"
                                                autocomplete="country"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                <option value="">...</option>
                                                <option v-for="country in countries" :value="country.id"
                                                    :key="country">{{ country . name }}</option>
                                            </select>
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.country_id">
                                                {{ errors . country_id }}</p>
                                        </div>
                                    </div>
                                    <!-- Province -->
                                    <div class="sm:col-span-3">
                                        <label for="province" class="block text-sm font-medium leading-6 text-gray-900">
                                            Province</label>
                                        <div class="mt-2">
                                            <input id="province" v-model="form.province" name="province" type="text"
                                                autocomplete="province"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.province">
                                                {{ errors . province }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- School -->
                                    <div class="sm:col-span-3">
                                        <label for="school"
                                            class="block text-sm font-medium leading-6 text-gray-900">
                                            School</label>
                                        <div class="mt-2">
                                            <input id="school" v-model="form.school" name="school"
                                                type="text" autocomplete="school"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.school">
                                                {{ errors . school }}</p>
                                        </div>
                                    </div>

                                    <!-- Grade -->
                                    <div class="sm:col-span-3">
                                        <label for="grade_id"
                                            class="block text-sm font-medium leading-6 text-gray-900">Grade
                                        </label>
                                        <div class="mt-2">
                                            <select v-model="form.grade_id" name="grade" id="grade"
                                                autocomplete="grade"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                <option value="">...</option>
                                                <option v-for="grade in grades" :value="grade.id"
                                                    :key="grade">{{ grade . full_name }}</option>
                                            </select>
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.grade_id">
                                                {{ errors . grade_id }}</p>
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="sm:col-span-3">
                                        <label for="password"
                                            class="block text-sm font-medium leading-6 text-gray-900">
                                            Password</label>
                                        <div class="mt-2">
                                            <input type="text" v-model="form.password" name="password"
                                                id="password" autocomplete="password"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.password">
                                                {{ errors . password }}</p>
                                        </div>
                                    </div>

                                    <!-- Father Email -->
                                    <div class="sm:col-span-3">
                                        <label for="father_email"
                                            class="block text-sm font-medium leading-6 text-gray-900">
                                            Father Email</label>
                                        <div class="mt-2">
                                            <input type="text" v-model="form.father_email" name="father_email"
                                                id="father_email" autocomplete="father_email"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                            <p class="mt-2 text-sm text-red-500" v-if="errors.father_email">
                                                {{ errors . father_email }}</p>
                                        </div>
                                    </div>

                                    <!-- Is Active -->
                                    <div class="sm:col-span-4">
                                        <label for="postal-code"
                                            class="block text-sm font-medium leading-6 text-gray-900">Is Active
                                        </label>
                                        <div class="mt-2">
                                            <Switch @click="form.is_active = !form.is_active, isActive= true"
                                                :class="[form.is_active ? 'bg-indigo-600' : 'bg-gray-200',
                                                    'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'
                                                ]">
                                                <span class="sr-only">Is Active</span>
                                                <span
                                                    :class="[form.is_active ? 'translate-x-5' : 'translate-x-0',
                                                        'pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200'
                                                    ]">
                                                    <span
                                                        :class="[form.is_active ? 'opacity-0 ease-out duration-100' :
                                                            'opacity-100 ease-in duration-200',
                                                            'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity'
                                                        ]"
                                                        aria-hidden="true">
                                                        <svg class="h-3 w-3 text-gray-400" fill="none"
                                                            viewBox="0 0 12 12">
                                                            <path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>
                                                    <span
                                                        :class="[form.is_active ? 'opacity-100 ease-in duration-200' :
                                                            'opacity-0 ease-out duration-100',
                                                            'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity'
                                                        ]"
                                                        aria-hidden="true">
                                                        <svg class="h-3 w-3 text-indigo-600" fill="currentColor"
                                                            viewBox="0 0 12 12">
                                                            <path
                                                                d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" />
                                                        </svg>
                                                    </span>
                                                </span>
                                            </Switch>

                                        </div>
                                    </div>

                                    <div class="sm:col-span-6">
                                        <label for="photo" class="block text-sm font-medium text-gray-700">
                                            Photo
                                        </label>
                                        <div class="flex-shrink-0 h-16 w-16 my-4" v-if="props.student.photo">
                                            <img class="h-16 w-16 rounded-full" :src="props.student.photo"
                                                alt="" />
                                        </div>
                                        <div
                                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                    fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="file-upload"
                                                        class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                        <span>Upload a photo</span>
                                                        <input id="file-upload" @change="selectPhoto"
                                                            name="file-upload" type="file" class="sr-only" />
                                                    </label>
                                                    <p class="pl-1"></p>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    PNG, JPG, GIF up to 5MB
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit button -->
                        <div class="mt-6 flex items-center justify-end gap-x-6">
                            <button type="button"
                                class="text-sm font-semibold leading-6 text-gray-900">Cancel</button>
                            <button type="submit"
                                class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import ErrorsAndMessages from "../Partials/ErrorsAndMessages.vue";
    import NoRecordFound from "../Partials/NoRecordFound.vue";
    import Swal from 'sweetalert2'
    import {
        Inertia
    } from '@inertiajs/inertia';
    import {
        Switch
    } from '@headlessui/vue';
    import {
        computed,
        defineComponent,
        reactive,
        ref
    } from 'vue'
    import {
        Head,
        Link
    } from '@inertiajs/vue3';
    import {
        router
    } from '@inertiajs/vue3';

    const props = defineProps(['student', 'countries', 'grades', 'errors', 'canManageSupportType']);

    const canManageSupportType = props.canManageSupportType;

    const form = reactive({
        id: props.student.id,
        name: props.student.name,
        fa_name: props.student.fa_name,
        last_name: props.student.last_name,
        fa_last_name: props.student.fa_last_name,
        father_name: props.student.father_name,
        fa_father_name: props.student.fa_father_name,
        phone: props.student.phone,
        support_type: props.student.support_type || '',
        father_phone: props.student.father_phone,
        father_email: props.student.father_email,
        province: props.student.province,
        reg_date: props.student.reg_date,
        photo: props.student.photo,
        username: props.student.username,
        email: props.student.email,
        country_id: props.student.country_id,
        grade_id: props.student.sub_grade_id,
        password: props.student.password,
        name_in_system: props.student.name_in_system,
        id_number: props.student.id_number,
        school: props.student.school,
        password: props.student.password,
        is_active: props.student.is_active,
        _method: 'put'
    })

    function submit() {
        router.post(route(`students.update`, {
            'id': form.id
        }), form, {
            forceFormData: true,
            onFinish: () => {
                Swal.fire(`Edited`,
                    `Student has been successfully updated.`)
            },
        })
    }

    function selectPhoto($event) {
        form.photo = $event.target.files[0];
    }
</script>
