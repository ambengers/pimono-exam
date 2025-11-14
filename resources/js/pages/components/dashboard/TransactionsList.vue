<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue';
import Pagination from '@components/Pagination.vue';
import SendTransactionIcon from '@components/icons/SendTransactionIcon.vue';
import ReceiveTransactionIcon from '@components/icons/ReceiveTransactionIcon.vue';
import { useDate } from '@composables/useDate';
import { useCurrency } from '@composables/useCurrency';
import { useEcho } from '@composables/useEcho';
import axios from 'axios';
import type { Transaction } from '@/interfaces/transaction';
import type { User } from '@/interfaces/user';
import type { PaginatedResponse } from '@/interfaces/paginatedResponse';

interface Props {
    transactions: Transaction[];
    isLoading: boolean;
    user: User | null;
    pagination: PaginatedResponse<Transaction> | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'page-change': [page: number];
    'new-transaction': [transaction: Transaction];
    'refresh-transactions': [];
    'transaction-received': [transaction: Transaction];
}>();

const echo = useEcho();
const { privateChannel, leaveChannel } = echo;
const hasNewTransactions = ref(false);

const expandedTransactionId = ref<number | null>(null);
const { formatDate } = useDate();
const { formatCurrency } = useCurrency();

function getTransactionType(transaction: Transaction): 'sent' | 'received' {
    return transaction.sender_id === props.user?.id ? 'sent' : 'received';
}

function getTransactionCounterparty(transaction: Transaction): string {
    if (transaction.sender_id === props.user?.id) {
        return transaction.receiver?.name || `User #${transaction.receiver_id}`;
    }
    return transaction.sender?.name || `User #${transaction.sender_id}`;
}

function toggleTransaction(transactionId: number) {
    if (expandedTransactionId.value === transactionId) {
        expandedTransactionId.value = null;
    } else if (expandedTransactionId.value !== null) {
        expandedTransactionId.value = null;
        setTimeout(() => {
            expandedTransactionId.value = transactionId;
        }, 200);
    } else {
        expandedTransactionId.value = transactionId;
    }
}

function isTransactionExpanded(transactionId: number): boolean {
    return expandedTransactionId.value === transactionId;
}

function isOnFirstPage(): boolean {
    return props.pagination?.meta.current_page === 1;
}

async function fetchTransactionById(transactionId: number): Promise<Transaction | null> {
    try {
        const { data } = await axios.get(`/api/transactions/${transactionId}`);
        return data.data;
    } catch (error) {
        console.error('Failed to fetch transaction:', error);
        return null;
    }
}

function handleNewTransaction(transaction: Transaction) {
    if (isOnFirstPage()) {
        // Add transaction to the top of the list
        emit('new-transaction', transaction);
    } else {
        // Show notification that new transactions are available
        hasNewTransactions.value = true;
    }
}

function handleViewNewTransactions() {
    hasNewTransactions.value = false;
    emit('page-change', 1);
}

onMounted(() => {
    if (!props.user) {
        return;
    }

    // Listen to private transactions channel
    const channel = privateChannel('transactions-channel');
    
    // Listen for TransactionCreated event
    channel.listen('.TransactionCreated', (data: any) => {
        const { transactionId, senderId, receiverId } = data;
        
        // Check if the authenticated user is involved in this transaction
        if (props.user && (props.user.id === senderId || props.user.id === receiverId)) {
            // Fetch the full transaction details
            fetchTransactionById(transactionId).then((transaction) => {
                if (transaction) {
                    handleNewTransaction(transaction);
                    // Emit event to refresh user balance
                    emit('transaction-received', transaction);
                }
            });
        }
    });
});

// Clear notification when navigating to first page
watch(() => props.pagination?.meta.current_page, (newPage) => {
    if (newPage === 1) {
        hasNewTransactions.value = false;
    }
});

onUnmounted(() => {
    // Leave the channel when component is unmounted
    leaveChannel('transactions-channel');
});
</script>
<template>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Transaction History
                </h3>
                <!-- New Transactions Notification -->
                <Transition name="fade">
                    <button
                        v-if="hasNewTransactions && !isOnFirstPage()"
                        @click="handleViewNewTransactions"
                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors cursor-pointer"
                    >
                        <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New transactions available
                    </button>
                </Transition>
            </div>
        </div>

        <!-- Loading State (only show on initial load when there are no transactions) -->
        <div v-if="isLoading && transactions.length === 0" class="px-6 py-12 flex items-center justify-center">
            <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <!-- Empty State -->
        <div v-else-if="!isLoading && transactions.length === 0" class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No transactions</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by making your first transaction.</p>
        </div>

        <!-- Transactions List -->
        <div v-if="transactions.length > 0" class="divide-y divide-gray-200 relative">
            <!-- Loading Overlay -->
            <div v-if="isLoading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
                <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div
                v-for="transaction in transactions"
                :key="transaction.id"
                class="px-6 py-4 hover:bg-gray-50 transition-colors cursor-pointer"
                @click="toggleTransaction(transaction.id)"
            >
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-3">
                            <div
                                :class="[
                                    'flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center',
                                    getTransactionType(transaction) === 'sent'
                                        ? 'bg-red-100'
                                        : 'bg-green-100'
                                ]"
                            >
                                <SendTransactionIcon v-if="getTransactionType(transaction) === 'sent'" />
                                <ReceiveTransactionIcon v-else />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ getTransactionType(transaction) === 'sent' ? 'Sent to' : 'Received from' }}
                                            <span class="text-gray-600">{{ getTransactionCounterparty(transaction) }}</span>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ formatDate(transaction.created_at) }}
                                        </p>
                                    </div>
                                    <svg
                                        class="h-5 w-5 text-gray-400 transition-transform duration-200 flex-shrink-0 ml-2"
                                        :class="{ 'rotate-180': isTransactionExpanded(transaction.id) }"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                                
                                <!-- Transaction Details (Accordion Content) -->
                                <Transition name="accordion">
                                    <div v-if="isTransactionExpanded(transaction.id)" class="mt-3 space-y-1">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-500">Amount:</span>
                                            <span class="font-medium text-gray-900">{{ formatCurrency(transaction.amount) }}</span>
                                        </div>
                                        
                                        <div v-if="getTransactionType(transaction) === 'sent'" class="flex items-center justify-between text-xs">
                                            <span class="text-gray-500">Commission Fee ({{ transaction.commission_fee_percentage  * 100 }}%):</span>
                                            <span class="font-medium text-gray-900">{{ formatCurrency(transaction.commission_fee) }}</span>
                                        </div>
                                        
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-500">Total:</span>
                                            <span class="font-medium text-gray-900">{{ formatCurrency(transaction.total) }}</span>
                                        </div>
                                        
                                        <!-- Sender Balance (only if user is sender) -->
                                        <template v-if="getTransactionType(transaction) === 'sent'">
                                            <div class="pt-2 mt-2 border-t border-gray-100">
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="text-gray-500">Balance Before:</span>
                                                    <span class="font-medium text-gray-900">{{ formatCurrency(transaction.sender_balance_before) }}</span>
                                                </div>
                                                <div class="flex items-center justify-between text-xs mt-1">
                                                    <span class="text-gray-500">Balance After:</span>
                                                    <span class="font-medium text-gray-900">{{ formatCurrency(transaction.sender_balance_after) }}</span>
                                                </div>
                                            </div>
                                        </template>
                                        
                                        <!-- Receiver Balance (only if user is receiver) -->
                                        <template v-if="getTransactionType(transaction) === 'received'">
                                            <div class="pt-2 mt-2 border-t border-gray-100">
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="text-gray-500">Balance Before:</span>
                                                    <span class="font-medium text-gray-900">{{ formatCurrency(transaction.receiver_balance_before) }}</span>
                                                </div>
                                                <div class="flex items-center justify-between text-xs mt-1">
                                                    <span class="text-gray-500">Balance After:</span>
                                                    <span class="font-medium text-gray-900">{{ formatCurrency(transaction.receiver_balance_after) }}</span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </Transition>
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-right ml-4">
                        <p
                            :class="[
                                'text-sm font-semibold',
                                getTransactionType(transaction) === 'sent' ? 'text-red-600' : 'text-green-600'
                            ]"
                        >
                            {{ getTransactionType(transaction) === 'sent' ? '-' : '+' }}{{ formatCurrency(transaction.amount) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <Pagination
            v-if="pagination"
            :current-page="pagination.meta.current_page"
            :last-page="pagination.meta.last_page"
            @page-change="(page: number) => emit('page-change', page)"
        />
    </div>
</template>

<style scoped>
.accordion-enter-active,
.accordion-leave-active {
    transition: all 0.3s ease;
    overflow: hidden;
}

.accordion-enter-from,
.accordion-leave-to {
    opacity: 0;
    max-height: 0;
    transform: translateY(-10px);
}

.accordion-enter-to,
.accordion-leave-from {
    opacity: 1;
    max-height: 500px;
    transform: translateY(0);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

