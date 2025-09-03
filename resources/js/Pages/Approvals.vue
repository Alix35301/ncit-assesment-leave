<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';


const showModal = ref(false);


const props = defineProps({
    leaveBalances: {
        type: Object,
        required: true,
    },
    leaves: {
        type: Object,
        required: true,
    },

    user: {
        type: Object,
        required: true,
    },
});

const leaveBalances = ref(props.leaveBalances);
const leaves = ref(props.leaves);

const submit = () => {
    router.post(route('leaves.store'), {
        start_date: start_date.value,
        end_date: end_date.value,
        type: leave_type.value,
        reason: reason.value,
    }, {
        onSuccess: () => {
            document.location.reload();
        }
    });
};

const approve = (leave) => {
    router.visit(route('leave-approvals.approve', { leave: leave.id }));
    document.location.reload();
};

const reject = (leave) => {
    router.visit(route('leave-approvals.reject', { leave: leave.id }));
    document.location.reload();
};


const getLeaveBalance = (leave) => {
    if(leave.type === 'annual') {
        return leave.leave_balance?.annual_leave;
    }
    if(leave.type === 'sick') {
        return leave.leave_balance.sick_leave;
    }
    if(leave.type === 'other') {
        return leave.leave_balance.other_leave;
    }
};


</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Leave Approvals</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">


                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Staff Name
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Leave Type</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Start Date</th>
                                        <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        End Date</th>

                                        <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Leave Balance</th>





                                        <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                        <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="leave in leaves" :key="leave.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ leave.user.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ leave.type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ leave.start_date }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ leave.end_date }}</td>

                                    <td class="px-6 py-4 whitespace-nowrap">{{ getLeaveBalance(leave) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="{
                                                'inline-flex px-2 py-1 rounded-full text-xs font-semibold': true,
                                                'bg-yellow-100 text-yellow-800': leave.status === 'pending',
                                                'bg-green-100 text-green-800': leave.status === 'approved',
                                                'bg-red-100 text-red-800': leave.status === 'rejected'
                                            }"
                                        >
                                            {{ leave.status.charAt(0).toUpperCase() + leave.status.slice(1) }}
                                        </span>
                                    </td>
                                    <td v-if="leave.status === 'pending'" class="px-6 gap-2 flex  py-4 whitespace-nowrap">
                                        <button class="bg-green-500 text-white px-4 py-2 rounded-md" @click="approve(leave)">Approve</button>
                                        <button class="bg-red-500 text-white px-4 py-2 rounded-md" @click="reject(leave)">Reject</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showModal" @close="showModal = false">
            <div class="p-4">
                <h2 class="text-lg font-medium text-gray-900">Apply for leave</h2>

                <form class="space-y-4 p-4" @submit.prevent="submit">
                    <div class="flex flex-col gap-2">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="leave_type">Leave Type</label>
                        <select id="leave_type" name="leave_type">
                            <option value="annual">Annual</option>
                            <option value="sick">Sick</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="reason">Reason</label>
                        <input type="text" id="reason" name="reason" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="type">Type</label>
                        <input type="text" id="type" name="type" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <button class="bg-gray-500 text-white px-4 py-2 rounded-md" type="button" @click="showModal = false">Cancel</button>
                        <button class="bg-blue-500 text-white px-4 py-2 rounded-md" type="submit">Submit</button>

                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
