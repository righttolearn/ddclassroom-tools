<template>
    <Head title="Monthly Attendance Report" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Monthly Attendance Report
                    </h2>

                    <p class="text-sm text-gray-500">
                        Review monthly attendance before sending support
                        eligibility emails.
                    </p>
                </div>

                <div class="flex gap-2">
                    <a
                        :href="exportUrl"
                        class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-600"
                    >
                        Export Top-Up Excel
                    </a>

                    <Link
                        href="/monthly-attendance-logs/generate"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
                    >
                        Generate Attendance
                    </Link>

                    <button
                        type="button"
                        @click="sendEmails"
                        :disabled="selectedIds.length === 0"
                        class="rounded-md px-4 py-2 text-sm font-semibold text-white"
                        :class="
                            selectedIds.length === 0
                                ? 'bg-gray-300 cursor-not-allowed'
                                : 'bg-green-600 hover:bg-green-500'
                        "
                    >
                        Send Emails
                        <span v-if="selectedIds.length">
                            ({{ selectedIds.length }})
                        </span>
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-5">
                <!-- Year -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Year
                    </label>
                    <select
                        v-model="localFilters.year"
                        @change="getData"
                        class="block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Years</option>
                        <option
                            v-for="year in years"
                            :key="year.id"
                            :value="year.name"
                        >
                            {{ year.name }}
                        </option>
                    </select>
                </div>

                <!-- Month -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Month
                    </label>
                    <select
                        v-model="localFilters.month_id"
                        @change="getData"
                        class="block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Months</option>

                        <option
                            v-for="month in months"
                            :key="month.id"
                            :value="month.id"
                        >
                            {{ month.name }}
                        </option>
                    </select>
                </div>

                <!-- Grade -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Grade / Class
                    </label>
                    <select
                        v-model="localFilters.sub_grade_id"
                        @change="getData"
                        class="block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Grades</option>

                        <option
                            v-for="subGrade in subGrades"
                            :key="subGrade.id"
                            :value="subGrade.id"
                        >
                            {{ subGrade.full_name }}
                        </option>
                    </select>
                </div>

                <!-- Absence Percentage -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Minimum Absence %
                    </label>
                    <input
                        v-model="localFilters.absence_percentage"
                        @change="getData"
                        type="number"
                        min="0"
                        max="100"
                        step="0.01"
                        class="block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                </div>

                <!-- Support Type -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Support Type
                    </label>
                    <select
                        v-model="localFilters.support_type"
                        @change="getData"
                        class="block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Support Types</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="cash">Cash</option>
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <button
                    type="button"
                    @click="resetFilters"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Reset Filters
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="min-w-full overflow-hidden rounded-lg shadow">
            <div class="border-b border-gray-200 bg-white px-6 py-4">
                <h3 class="font-semibold text-gray-900">
                    Monthly Attendance Report — {{ reportPeriod }}
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <!-- Checkbox -->
                        <th class="px-4 py-3 text-left">
                            <input
                                type="checkbox"
                                :checked="allSelectableSelected"
                                @change="toggleSelectAll"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                        </th>
                        <th :class="headerClass">Name</th>
                        <th :class="headerClass">Father's Name</th>
                        <th :class="headerClass">Phone Number</th>
                        <th :class="headerClass">Grade / Class</th>
                        <th :class="headerClass">Eligibility</th>
                        <th :class="headerClass">Attendance</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr
                        v-for="item in monthlyAttendanceLogs.data"
                        :key="item.id"
                    >
                        <td class="px-4 py-4">
                            <input
                                v-if="canSelect(item)"
                                v-model="selectedIds"
                                type="checkbox"
                                :value="item.id"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <span v-else class="text-gray-300"> — </span>
                        </td>

                        <!-- Name -->
                        <td :class="cellClass">
                            {{ item.student?.name ?? "-" }}
                        </td>

                        <!-- Father -->
                        <td :class="cellClass">
                            {{ item.student?.father_name ?? "-" }}
                        </td>

                        <!-- Phone -->
                        <td :class="cellClass">
                            {{ item.student?.phone ?? "-" }}
                        </td>

                        <!-- Grade / Class -->
                        <td :class="cellClass">
                            {{ item.sub_grade?.full_name ?? "-" }}
                        </td>

                        <!-- Eligibility -->
                        <td :class="cellClass">
                            <span
                                class="inline-flex rounded-full px-2 py-1 text-xs font-medium"
                                :class="
                                    item.is_eligible_for_support
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-red-100 text-red-700'
                                "
                            >
                                {{
                                    item.is_eligible_for_support ? "Yes" : "No"
                                }}
                            </span>
                        </td>

                        <!-- Attendance -->
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                            <span class="font-semibold text-green-600">
                                {{ item.total_presents }} / {{ item.total_hours }}
                            </span>
                        </td>
                    </tr>
                </tbody>
                </table>
            </div>
        </div>

        <NoRecordFound v-if="monthlyAttendanceLogs.data.length === 0" />

        <!-- Pagination -->
        <div v-if="monthlyAttendanceLogs.links.length > 3" class="mt-3">
            <div class="-mb-1 flex flex-wrap">
                <template
                    v-for="(link, index) in monthlyAttendanceLogs.links"
                    :key="index"
                >
                    <div
                        v-if="link.url === null"
                        class="mb-1 mr-1 rounded border px-4 py-3 text-sm leading-4 text-gray-400"
                        v-html="link.label"
                    />

                    <Link
                        v-else
                        :href="link.url"
                        class="mb-1 mr-1 rounded border px-4 py-3 text-sm leading-4 hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
                        :class="{
                            'bg-blue-700 text-white': link.active,
                        }"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import NoRecordFound from "./../Partials/NoRecordFound.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, reactive, ref } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    monthlyAttendanceLogs: Object,
    subGrades: Array,
    months: Array,
    years: Array,
    filters: Object,
});

const selectedIds = ref([]);

const localFilters = reactive({
    year: props.filters?.year || "",
    month_id: props.filters?.month_id || "",
    sub_grade_id: props.filters?.sub_grade_id || "",
    absence_percentage: props.filters?.absence_percentage ?? 30,
    support_type: props.filters?.support_type || "",
});

const headerClass =
    "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider";

const cellClass = "px-6 py-4 whitespace-nowrap text-sm text-gray-700";

const reportPeriod = computed(() => {
    const month = props.months.find(
        (item) => String(item.id) === String(localFilters.month_id),
    );

    return `${month?.name || "All Months"} ${localFilters.year || "All Years"}`;
});

function getData() {
    selectedIds.value = [];

    router.get("/monthly-attendance-logs", localFilters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function resetFilters() {
    localFilters.year = "";
    localFilters.month_id = "";
    localFilters.sub_grade_id = "";
    localFilters.absence_percentage = "";
    localFilters.support_type = "";

    getData();
}

function canSelect(item) {
    return (
        !item.is_sent &&
        Number(item.absence_percentage) > 30
    );
}

const selectableIds = computed(() => {
    return props.monthlyAttendanceLogs.data
        .filter(canSelect)
        .map((item) => item.id);
});

const allSelectableSelected = computed(() => {
    if (selectableIds.value.length === 0) {
        return false;
    }

    return selectableIds.value.every((id) => selectedIds.value.includes(id));
});

const exportUrl = computed(() => {
    const params = new URLSearchParams();

    Object.entries(localFilters).forEach(([key, value]) => {
        if (value !== "" && value !== null && value !== undefined) {
            params.set(key, value);
        }
    });

    const queryString = params.toString();

    return queryString
        ? `/monthly-attendance-logs/export?${queryString}`
        : "/monthly-attendance-logs/export";
});

function toggleSelectAll() {
    if (allSelectableSelected.value) {
        selectedIds.value = [];
        return;
    }

    selectedIds.value = [...selectableIds.value];
}

function sendEmails() {
    if (selectedIds.value.length === 0) {
        return;
    }

    Swal.fire({
        title: "Send attendance emails?",
        text:
            `${selectedIds.value.length} student(s) ` +
            "will receive individual emails. Each homeroom teacher " +
            "will receive one summary email.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, Send Emails",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        router.post(
            "/monthly-attendance-logs/send-emails",
            {
                ids: selectedIds.value,
            },
            {
                preserveScroll: true,

                onSuccess: () => {
                    selectedIds.value = [];

                    Swal.fire(
                        "Queued",
                        "The student emails and homeroom teacher summaries were added to the queue.",
                        "success",
                    );
                },
            },
        );
    });
}

</script>
