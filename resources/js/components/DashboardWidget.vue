<template>
    <div class="dashboard-widget card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ title }}</h5>
            <button 
                v-if="refreshable" 
                @click="refreshData" 
                class="btn btn-sm btn-link"
                :disabled="isLoading"
            >
                <i :class="['fas fa-sync-alt', { 'fa-spin': isLoading }]"></i>
            </button>
        </div>
        <div class="card-body">
            <div v-if="isLoading && !data" class="text-center py-5">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>

            <div v-else-if="error" class="alert alert-danger">
                {{ error }}
            </div>

            <div v-else>
                <slot :data="data" :loading="isLoading">
                    <!-- Default slot content -->
                    <pre>{{ data }}</pre>
                </slot>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Props
const props = defineProps({
    title: {
        type: String,
        required: true
    },
    apiEndpoint: {
        type: String,
        required: true
    },
    refreshable: {
        type: Boolean,
        default: true
    },
    autoRefresh: {
        type: Number,
        default: 0 // 0 means no auto-refresh, otherwise milliseconds
    }
});

// Emits
const emit = defineEmits(['data-loaded', 'error']);

// State
const data = ref(null);
const isLoading = ref(false);
const error = ref(null);

// Methods
const fetchData = async () => {
    isLoading.value = true;
    error.value = null;

    try {
        const response = await axios.get(props.apiEndpoint);
        data.value = response.data;
        emit('data-loaded', response.data);
    } catch (err) {
        error.value = err.message || 'Failed to load data';
        emit('error', err);
    } finally {
        isLoading.value = false;
    }
};

const refreshData = () => {
    fetchData();
};

// Lifecycle
onMounted(() => {
    fetchData();

    // Setup auto-refresh if enabled
    if (props.autoRefresh > 0) {
        setInterval(() => {
            fetchData();
        }, props.autoRefresh);
    }
});
</script>

<style scoped>
.dashboard-widget {
    height: 100%;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.btn-link {
    padding: 0;
    color: #6c757d;
}

.btn-link:hover {
    color: #007bff;
}
</style>

