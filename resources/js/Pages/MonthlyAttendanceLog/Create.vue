<template>
    <Head title="Generate Monthly Attendance" />

    <AuthenticatedLayout>
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Generate Monthly Attendance
                </h2>
                <p class="text-sm text-gray-500">
                    Preview attendance results before saving the monthly report.
                </p>
            </div>

            <Link
                href="/monthly-attendance-logs"
                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Back to Report
            </Link>
        </div>

        <div class="rounded-lg bg-white p-4 shadow">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Year</label>
                    <select v-model="form.year" :class="inputClass">
                        <option value="">Select Year</option>
                        <option v-for="year in years" :key="year.id" :value="year.name">
                            {{ year.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Month</label>
                    <select v-model="form.month_id" :class="inputClass">
                        <option value="">Select Month</option>
                        <option v-for="month in months" :key="month.id" :value="month.id">
                            {{ month.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Grade / Class</label>
                    <select v-model="form.sub_grade_id" :class="inputClass">
                        <option value="">All Grades</option>
                        <option v-for="subGrade in subGrades" :key="subGrade.id" :value="subGrade.id">
                            {{ subGrade.full_name }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button
                    type="button"
                    @click="previewResults"
                    class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-600"
                >
                    Get Results
                </button>
            </div>
        </div>

        <div v-if="hasPreview && previewIsCurrent" class="mt-5">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="font-semibold text-gray-900">
                        Monthly Attendance Report — {{ reportPeriod }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        {{ results.length }} student(s) found in attendance logs.
                    </p>
                </div>

                <button
                    v-if="results.length"
                    type="button"
                    @click="generateReport"
                    :disabled="isGenerating"
                    class="rounded-md px-4 py-2 text-sm font-semibold text-white"
                    :class="
                        isGenerating
                            ? 'cursor-not-allowed bg-gray-400'
                            : 'bg-indigo-600 hover:bg-indigo-500'
                    "
                >
                    {{ isGenerating ? "Generating..." : "Generate Report" }}
                </button>
            </div>

            <div v-if="results.length" class="overflow-x-auto overflow-hidden rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th :class="headerClass">Name</th>
                            <th :class="headerClass">Father's Name</th>
                            <th :class="headerClass">Phone Number</th>
                            <th :class="headerClass">Grade / Class</th>
                            <th :class="headerClass">Eligibility</th>
                            <th :class="headerClass">Attendance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="result in results" :key="result.student_id">
                            <td :class="cellClass">{{ result.student_name }}</td>
                            <td :class="cellClass">{{ result.father_name || '-' }}</td>
                            <td :class="cellClass">{{ result.phone || '-' }}</td>
                            <td :class="cellClass">{{ result.sub_grade_name || '-' }}</td>
                            <td :class="cellClass">
                                {{ result.is_eligible_for_support ? 'Yes' : 'No' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-green-600">
                                {{ result.total_presents }} / {{ result.total_hours }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <NoRecordFound v-else />
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
    results: Array,
    subGrades: Array,
    months: Array,
    years: Array,
    filters: Object,
    hasPreview: Boolean,
});

const form = reactive({
    year: props.filters?.year || "",
    month_id: props.filters?.month_id || "",
    sub_grade_id: props.filters?.sub_grade_id || "",
});

const isGenerating = ref(false);

const inputClass = "block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500";
const headerClass = "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider";
const cellClass = "px-6 py-4 whitespace-nowrap text-sm text-gray-700";

const previewIsCurrent = computed(() => {
    return String(form.year) === String(props.filters?.year || "")
        && String(form.month_id) === String(props.filters?.month_id || "")
        && String(form.sub_grade_id || "") === String(props.filters?.sub_grade_id || "");
});

const reportPeriod = computed(() => {
    const month = props.months.find(
        (item) => String(item.id) === String(form.month_id),
    );

    return `${month?.name || ""} ${form.year || ""}`.trim();
});

function validateSelection() {
    if (form.year && form.month_id) {
        return true;
    }

    Swal.fire("Missing Information", "Please select Year and Month first.", "warning");
    return false;
}

function previewResults() {
    if (!validateSelection()) {
        return;
    }

    router.get("/monthly-attendance-logs/generate", {
        year: form.year,
        month_id: form.month_id,
        sub_grade_id: form.sub_grade_id || null,
    }, {
        preserveState: true,
        replace: true,
    });
}

function generateReport() {
    if (!validateSelection()) {
        return;
    }

    Swal.fire({
        title: "Generate this report?",
        text: `${props.results.length} monthly record(s) will be created or updated.`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Generate",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        router.post(
            route("monthly-attendance-logs.generate"),
            {
                year: form.year,
                month_id: form.month_id,
                sub_grade_id: form.sub_grade_id || null,
            },
            {
                onStart: () => {
                    isGenerating.value = true;
                },
                onFinish: () => {
                    isGenerating.value = false;
                },
            },
        );
    });
}
</script>
