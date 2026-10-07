<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

const user = computed(() => page.props.auth.user);

import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import {
    Dialog,
    DialogPanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    TransitionChild,
    TransitionRoot,
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
} from "@headlessui/vue";
import {
    Bars3Icon,
    BellIcon,
    CalendarIcon,
    ChartBarIcon,
    ChartPieIcon,
    ChevronRightIcon,
    Cog6ToothIcon,
    DocumentChartBarIcon,
    DocumentDuplicateIcon,
    FaceSmileIcon,
    FolderIcon,
    GlobeAltIcon,
    HomeIcon,
    KeyIcon,
    ListBulletIcon,
    AcademicCapIcon,
    BookOpenIcon,
    ClockIcon,
    UserGroupIcon,
    UsersIcon,
    XMarkIcon,
} from "@heroicons/vue/24/outline";
import { ChevronDownIcon, MagnifyingGlassIcon } from "@heroicons/vue/20/solid";

const showingNavigationDropdown = ref(false);

// Get current URL path
const currentUrl = computed(() => {
    return page.url;
});

// Function to check if a route is active
const isActive = (href) => {
    if (href === '/') {
        return currentUrl.value === '/';
    }
    return currentUrl.value.startsWith(href);
};

// Function to check if any child is active
const hasActiveChild = (children) => {
    if (!children) return false;
    return children.some(child => isActive(child.href));
};

const navigation = computed(() => [
    { name: "Dashboard", href: "/", icon: HomeIcon },
    {
        name: "Student",
        href: "/",
        icon: UserGroupIcon,
        children: [
            {
                name: "Student List",
                href: "/students",
                icon: ListBulletIcon,
            },
            {
                name: "Handbooks",
                href: "/handbooks",
                icon: BookOpenIcon,
            },
            {
                name: "Student Result",
                href: "/student-result",
                icon: DocumentChartBarIcon,
            },
            {
                name: "Class Promotion",
                href: "/student-class-promotion",
                icon: AcademicCapIcon,
            },
            {
                name: "Retake Opportunities",
                href: "/students/retake-opportunities",
                icon: DocumentDuplicateIcon,
            },
            {
                name: "Delete Student Score",
                href: "/student-score/delete-scores",
                icon: DocumentDuplicateIcon,
            },
        ],
    },

    {
        name: "Attendance",
        href: "/",
        icon: ClockIcon,
        children: [
            {
                name: "Attendance List",
                href: "/student-attendance",
                icon: ListBulletIcon,
            },
            {
                name: "Attendance Log",
                href: "/student-attendance-log",
                icon: CalendarIcon,
            },
            // Monthly Attendance
            {
                name: "Monthly Attendance",
                href: "/monthly-attendance-logs",
                icon: DocumentChartBarIcon,
            },
            {
                name: "Attendance Shoqa",
                href: "/create-student-shoqa-score",
                icon: ChartBarIcon,
            },
        ],
    },

    {
        name: "Report",
        href: "/",
        icon: ChartBarIcon,
        children: [
            {
                name: "Student Results",
                href: "/reports/student-results",
                icon: DocumentChartBarIcon,
            },
            {
                name: "Subject Scores",
                href: "/reports/subject-scores",
                icon: DocumentChartBarIcon,
            },
            {
                name: "Subject Statistics",
                href: "/reports/subject-statistics",
                icon: ChartPieIcon,
            },
            {
                name: "Grade 9 Report",
                href: "/reports/grade-9",
                icon: DocumentChartBarIcon,
            },
            {
                name: "All Students Subject Scores",
                href: "/reports/all-students-subject-scores",
                icon: DocumentChartBarIcon,
            },
            {
                name: "Attendance Log",
                href: "/reports/attendance-log",
                icon: CalendarIcon,
            },
        ],
    },
    {
        name: "Student Score",
        href: "/student-score",
        icon: AcademicCapIcon,
    },

    { name: "Shoqa", href: "/shoqa", icon: ChartBarIcon },
]);

const teams = computed(() => [
    {
        id: 1,
        name: "Country",
        href: "/countries",
        icon: GlobeAltIcon,
    },
    {
        id: 2,
        name: "Grades",
        href: "/sub-grades",
        icon: AcademicCapIcon,
    },
    { id: 3, name: "Subject", href: "/subjects", icon: BookOpenIcon },
    { id: 7, name: "Semester Subjects", href: "/sub-grade-subject-semesters", icon: BookOpenIcon },
    { id: 4, name: "Years", href: "/years", icon: CalendarIcon },
    { id: 5, name: "Homeroom Teacher", href: "/class-responsible", icon: UsersIcon },
    { id: 8, name: "Skipped Subjects",href: "/skipped-grade-subjects",icon: DocumentDuplicateIcon},
    { id: 6, name: "2FA", href: "/2fa/index", icon: KeyIcon },
]);
const userNavigation = [{ name: "Your profile", href: "#" }];

const sidebarOpen = ref(false);
</script>

<template>
    <div>
        <TransitionRoot as="template" :show="sidebarOpen">
            <Dialog
                as="div"
                class="relative z-50 lg:hidden"
                @close="sidebarOpen = false"
            >
                <TransitionChild
                    as="template"
                    enter="transition-opacity ease-linear duration-300"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="transition-opacity ease-linear duration-300"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-gray-900/80" />
                </TransitionChild>

                <div class="fixed inset-0 flex">
                    <TransitionChild
                        as="template"
                        enter="transition ease-in-out duration-300 transform"
                        enter-from="-translate-x-full"
                        enter-to="translate-x-0"
                        leave="transition ease-in-out duration-300 transform"
                        leave-from="translate-x-0"
                        leave-to="-translate-x-full"
                    >
                        <DialogPanel
                            class="relative mr-16 flex w-full max-w-xs flex-1"
                        >
                            <TransitionChild
                                as="template"
                                enter="ease-in-out duration-300"
                                enter-from="opacity-0"
                                enter-to="opacity-100"
                                leave="ease-in-out duration-300"
                                leave-from="opacity-100"
                                leave-to="opacity-0"
                            >
                                <div
                                    class="absolute left-full top-0 flex w-16 justify-center pt-5"
                                >
                                    <button
                                        type="button"
                                        class="-m-2.5 p-2.5"
                                        @click="sidebarOpen = false"
                                    >
                                        <span class="sr-only"
                                            >Close sidebar</span
                                        >
                                        <XMarkIcon
                                            class="h-6 w-6 text-white"
                                            aria-hidden="true"
                                        />
                                    </button>
                                </div>
                            </TransitionChild>
                            <!-- Sidebar component, swap this element with another sidebar if you like -->
                            <div
                                class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4 ring-1 ring-white/10"
                            >
                                <div class="flex h-16 shrink-0 items-center">
                                    <img
                                        class="h-16 w-auto"
                                        src="../../../public/images/logo.webp"
                                        alt="Your Company"
                                    />
                                </div>
                                <nav class="flex flex-1 flex-col">
                                    <ul
                                        role="list"
                                        class="flex flex-1 flex-col gap-y-7"
                                    >
                                        <li>
                                            <ul
                                                role="list"
                                                class="-mx-2 space-y-1"
                                            >
                                                <li
                                                    v-for="item in navigation"
                                                    :key="item.name"
                                                >
                                                    <div v-if="item.children" class="space-y-1">
                                                        <div
                                                            :class="[
                                                                hasActiveChild(item.children)
                                                                    ? 'bg-gray-700 text-white'
                                                                    : 'text-gray-400 hover:text-white hover:bg-gray-800',
                                                                'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                                                            ]"
                                                        >
                                                            <component
                                                                :is="item.icon"
                                                                class="h-6 w-6 shrink-0"
                                                                aria-hidden="true"
                                                            />
                                                            {{ item.name }}
                                                        </div>
                                                        <ul class="ml-4 space-y-1">
                                                            <li
                                                                v-for="child in item.children"
                                                                :key="child.name"
                                                            >
                                                                <Link
                                                                    :href="child.href"
                                                                    :class="[
                                                                        isActive(child.href)
                                                                            ? 'bg-gray-700 text-white'
                                                                            : 'text-gray-400 hover:text-white hover:bg-gray-800',
                                                                        'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                                                                    ]"
                                                                >
                                                                    <component
                                                                        :is="child.icon"
                                                                        class="h-6 w-6 shrink-0"
                                                                        aria-hidden="true"
                                                                    />
                                                                    {{ child.name }}
                                                                </Link>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <Link
                                                        v-else
                                                        :href="item.href"
                                                        :class="[
                                                            isActive(item.href)
                                                                ? 'bg-gray-700 text-white'
                                                                : 'text-gray-400 hover:text-white hover:bg-gray-800',
                                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                                                        ]"
                                                    >
                                                        <component
                                                            :is="item.icon"
                                                            class="h-6 w-6 shrink-0"
                                                            aria-hidden="true"
                                                        />
                                                        {{ item.name }}
                                                    </Link>
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <div
                                                class="text-xs font-semibold leading-6 text-gray-400"
                                            >
                                                General
                                            </div>
                                            <ul
                                                role="list"
                                                class="-mx-2 mt-2 space-y-1"
                                            >
                                                <li
                                                    v-for="team in teams"
                                                    :key="team.name"
                                                >
                                                    <Link
                                                        :href="team.href"
                                                        :class="[
                                                            isActive(team.href)
                                                                ? 'bg-gray-700 text-white'
                                                                : 'text-gray-400 hover:text-white hover:bg-gray-800',
                                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                                                        ]"
                                                    >
                                                        <component
                                                            :is="team.icon"
                                                            class="h-6 w-6 shrink-0"
                                                            aria-hidden="true"
                                                        />
                                                        <span
                                                            class="truncate"
                                                            >{{
                                                                team.name
                                                            }}</span
                                                        >
                                                    </Link>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="mt-auto">
                                            <Link
                                                href="/users"
                                                :class="[
                                                    isActive('/users')
                                                        ? 'bg-gray-700 text-white'
                                                        : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                                                    'group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6',
                                                ]"
                                            >
                                                <Cog6ToothIcon
                                                    class="h-6 w-6 shrink-0"
                                                    aria-hidden="true"
                                                />
                                                Users
                                            </Link>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>

        <!-- Static sidebar for desktop -->
        <div
            class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col"
        >
            <!-- Sidebar component, swap this element with another sidebar if you like -->
            <div
                class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4"
            >
                <div class="flex h-16 shrink-0 items-center">
                    <img
                        class="h-16 w-auto"
                        src="../../../public/images/logo.webp"
                        alt="Your Company"
                    />
                </div>
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <div v-for="item in navigation" :key="item.name">
                                <div v-if="!item.children">
                                    <Link
                                        :href="item.href"
                                        :class="[
                                            isActive(item.href)
                                                ? 'bg-gray-700 text-white'
                                                : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                                            'group flex items-center px-2 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400',
                                        ]"
                                    >
                                        <component
                                            :is="item.icon"
                                            :class="[
                                                isActive(item.href)
                                                    ? 'text-gray-300'
                                                    : 'text-gray-400 group-hover:text-gray-300',
                                                'mr-3 flex-shrink-0 h-6 w-6',
                                            ]"
                                            aria-hidden="true"
                                        />
                                        {{ item.name }}
                                    </Link>
                                </div>
                                <Disclosure
                                    v-else
                                    as="div"
                                    class="space-y-1"
                                    v-slot="{ open }"
                                    :default-open="hasActiveChild(item.children)"
                                >
                                    <DisclosureButton
                                        :class="[
                                            hasActiveChild(item.children)
                                                ? 'menu-parent-active'
                                                : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                                            'group w-full flex items-center pl-2 pr-1 py-2 text-left text-sm font-medium rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400',
                                        ]"
                                    >
                                            <component
                                                :is="item.icon"
                                                :class="[
                                                    hasActiveChild(item.children)
                                                        ? 'text-gray-300'
                                                        : 'text-gray-400 group-hover:text-gray-500',
                                                    'mr-3 h-6 w-6 flex-shrink-0',
                                                ]"
                                                aria-hidden="true"
                                            />
                                            <span class="flex-1">{{
                                                item.name
                                            }}</span>
                                            <ChevronRightIcon
                                                :class="[
                                                    open
                                                        ? 'text-gray-400 !rotate-90'
                                                        : hasActiveChild(item.children)
                                                        ? 'text-gray-300'
                                                        : 'text-gray-300',
                                                    'ml-3 h-5 w-5 flex-shrink-0 transform transition-all duration-150 ease-in-out group-hover:text-gray-400',
                                                ]"
                                            />
                                        </DisclosureButton>
                                    <DisclosurePanel class="space-y-1">
                                        <DisclosureButton
                                            v-for="subItem in item.children"
                                            :key="subItem.name"
                                            as="div"
                                            :href="subItem.href"
                                        >
                                            <Link
                                                :href="subItem.href"
                                                :class="[
                                                    isActive(subItem.href)
                                                        ? 'bg-gray-700 text-white'
                                                        : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                                                    'group flex w-full items-center rounded-md py-2 pl-11 pr-2 text-sm font-medium focus:outline-none focus:ring-1 focus:ring-gray-400',
                                                ]"
                                            >
                                                <component
                                                    :is="subItem.icon"
                                                    :class="[
                                                        isActive(subItem.href)
                                                            ? 'text-gray-300'
                                                            : 'text-gray-400 group-hover:text-gray-500',
                                                        'mr-3 h-6 w-6 flex-shrink-0',
                                                    ]"
                                                    aria-hidden="true"
                                                />
                                                {{ subItem.name }}
                                            </Link>
                                        </DisclosureButton>
                                    </DisclosurePanel>
                                </Disclosure>
                            </div>
                        </li>
                        <li>
                            <div
                                class="text-sm pl-2 mb-4 font-semibold leading-6 text-gray-400"
                            >
                                General
                            </div>
                            <div v-for="item in teams" :key="item.name">
                                <div v-if="!item.children">
                                    <Link
                                        :href="item.href"
                                        :class="[
                                            isActive(item.href)
                                                ? 'bg-gray-700 text-white'
                                                : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                                            'group flex items-center px-2 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400',
                                        ]"
                                    >
                                        <component
                                            :is="item.icon"
                                            :class="[
                                                isActive(item.href)
                                                    ? 'text-gray-300'
                                                    : 'text-gray-400 group-hover:text-gray-300',
                                                'mr-3 flex-shrink-0 h-6 w-6',
                                            ]"
                                            aria-hidden="true"
                                        />
                                        {{ item.name }}
                                    </Link>
                                </div>
                                <Disclosure
                                    v-else
                                    as="div"
                                    class="space-y-1"
                                    v-slot="{ open }"
                                >
                                    <DisclosureButton
                                        :class="[
                                            item.current
                                                ? 'bg-gray-900 text-white'
                                                : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                                            'group w-full flex items-center pl-2 pr-1 py-2 text-left text-sm font-medium rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400',
                                        ]"
                                    >
                                        <component
                                            :is="item.icon"
                                            class="mr-3 h-6 w-6 flex-shrink-0 text-gray-400 group-hover:text-gray-500"
                                            aria-hidden="true"
                                        />
                                        <span class="flex-1">{{
                                            item.name
                                        }}</span>
                                        <ChevronRightIcon
                                            :class="[
                                                open
                                                    ? 'text-gray-400 !rotate-90'
                                                    : 'text-gray-300',
                                                'ml-3 h-5 w-5 flex-shrink-0 transform transition-all duration-150 ease-in-out group-hover:text-gray-400',
                                            ]"
                                        />
                                    </DisclosureButton>
                                    <DisclosurePanel class="space-y-1">
                                        <DisclosureButton
                                            v-for="subItem in item.children"
                                            :key="subItem.name"
                                            as="div"
                                            :href="subItem.href"
                                        >
                                            <Link
                                                :href="subItem.href"
                                                class="group flex w-full items-center rounded-md py-2 pl-11 pr-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-1 focus:ring-gray-400"
                                            >
                                                <component
                                                    :is="subItem.icon"
                                                    class="mr-3 h-6 w-6 flex-shrink-0 text-gray-400 group-hover:text-gray-500"
                                                    aria-hidden="true"
                                                />
                                                {{ subItem.name }}
                                            </Link>
                                        </DisclosureButton>
                                    </DisclosurePanel>
                                </Disclosure>
                            </div>
                        </li>
                        <li class="mt-auto">
                            <Link
                                :href="'/users'"
                                :class="[
                                    isActive('/users')
                                        ? 'bg-gray-800 text-white'
                                        : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                                    'group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6',
                                ]"
                            >
                                <Cog6ToothIcon
                                    class="h-6 w-6 shrink-0"
                                    aria-hidden="true"
                                />
                                Users
                            </Link>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="lg:pl-72">
            <div
                class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8"
            >
                <button
                    type="button"
                    class="-m-2.5 p-2.5 text-gray-700 lg:hidden"
                    @click="sidebarOpen = true"
                >
                    <span class="sr-only">Open sidebar</span>
                    <Bars3Icon class="h-6 w-6" aria-hidden="true" />
                </button>

                <!-- Separator -->
                <div
                    class="h-6 w-px bg-gray-900/10 lg:hidden"
                    aria-hidden="true"
                />

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <form class="relative flex flex-1" action="#" method="GET">
                        <label for="search-field" class="sr-only">Search</label>
                        <MagnifyingGlassIcon
                            class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400"
                            aria-hidden="true"
                        />
                        <input
                            id="search-field"
                            class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                            placeholder="Search..."
                            type="search"
                            name="search"
                        />
                    </form>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <button
                            type="button"
                            class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500"
                        >
                            <span class="sr-only">View notifications</span>
                            <BellIcon class="h-6 w-6" aria-hidden="true" />
                        </button>

                        <!-- Separator -->
                        <div
                            class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-900/10"
                            aria-hidden="true"
                        />

                        <!-- Profile dropdown -->
                        <Menu as="div" class="relative">
                            <MenuButton class="-m-1.5 flex items-center p-1.5">
                                <span class="sr-only">Open user menu</span>
                                <img
                                    class="h-8 w-8 rounded-full bg-gray-50"
                                    :src="
                                        user.photo
                                            ? user.photo
                                            : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'
                                    "
                                    alt=""
                                />
                                <span class="hidden lg:flex lg:items-center">
                                    <span
                                        class="ml-4 text-sm font-semibold leading-6 text-gray-900"
                                        aria-hidden="true"
                                        >{{ user.name }}</span
                                    >
                                    <ChevronDownIcon
                                        class="ml-2 h-5 w-5 text-gray-400"
                                        aria-hidden="true"
                                    />
                                </span>
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
                                    class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
                                >
                                    <MenuItem
                                        v-for="item in userNavigation"
                                        :key="item.name"
                                        v-slot="{ active }"
                                    >
                                        <Link
                                            :href="item.href"
                                            :class="[
                                                active ? 'bg-gray-50' : '',
                                                'block px-3 py-1 text-sm leading-6 text-gray-900',
                                            ]"
                                            >{{ item.name }}</Link
                                        >
                                    </MenuItem>
                                    <Link
                                        :href="route('logout')"
                                        class="block px-3 py-1 text-sm leading-6 text-gray-900"
                                        method="post"
                                        as="button"
                                        >Log out</Link
                                    >
                                </MenuItems>
                            </transition>
                        </Menu>
                    </div>
                </div>
            </div>

            <main class="py-4 sm:py-6">
                <div class="px-4 sm:px-6">
                    <div
                        v-if="page.props.flash?.success"
                        class="mb-4 rounded-md border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800"
                        role="status"
                    >
                        {{ page.props.flash.success }}
                    </div>

                    <div
                        v-if="page.props.flash?.error"
                        class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800"
                        role="alert"
                    >
                        {{ page.props.flash.error }}
                    </div>

                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
