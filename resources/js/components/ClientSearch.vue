<template>
    <div class="client-search-component">
        <div class="input-group">
            <input 
                v-model="searchQuery"
                @input="handleSearch"
                type="text" 
                class="form-control" 
                placeholder="Search clients by name, email, or phone..."
            >
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" @click="clearSearch">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div v-if="isLoading" class="text-center mt-3">
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <div v-if="results.length > 0 && searchQuery" class="search-results mt-2">
            <div class="list-group">
                <a 
                    v-for="client in results" 
                    :key="client.id"
                    :href="`/admin/clients/${client.id}`"
                    class="list-group-item list-group-item-action"
                >
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">{{ client.name }}</h6>
                        <small class="text-muted">ID: {{ client.id }}</small>
                    </div>
                    <p class="mb-1 small">{{ client.email }}</p>
                    <small class="text-muted">{{ client.phone }}</small>
                </a>
            </div>
        </div>

        <div v-if="searchQuery && !isLoading && results.length === 0" class="alert alert-info mt-2">
            No clients found matching "{{ searchQuery }}"
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';

// Props
const props = defineProps({
    apiEndpoint: {
        type: String,
        default: '/admin/api/clients/search'
    },
    minCharacters: {
        type: Number,
        default: 2
    }
});

// Reactive state
const searchQuery = ref('');
const results = ref([]);
const isLoading = ref(false);
let searchTimeout = null;

// Methods
const handleSearch = () => {
    // Clear previous timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    // Don't search if query is too short
    if (searchQuery.value.length < props.minCharacters) {
        results.value = [];
        return;
    }

    // Debounce search
    searchTimeout = setTimeout(async () => {
        isLoading.value = true;
        
        try {
            const response = await axios.get(props.apiEndpoint, {
                params: { q: searchQuery.value }
            });
            results.value = response.data.data || [];
        } catch (error) {
            console.error('Search error:', error);
            results.value = [];
        } finally {
            isLoading.value = false;
        }
    }, 300); // 300ms debounce
};

const clearSearch = () => {
    searchQuery.value = '';
    results.value = [];
};
</script>

<style scoped>
.client-search-component {
    position: relative;
}

.search-results {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.list-group-item {
    cursor: pointer;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}
</style>

