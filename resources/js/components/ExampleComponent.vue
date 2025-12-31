<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">
                        <h5 class="mb-0">Vue 3 Example Component</h5>
                    </div>

                    <div class="card-body">
                        <p>{{ message }}</p>
                        
                        <div class="form-group">
                            <label for="name">Your Name:</label>
                            <input 
                                v-model="name" 
                                type="text" 
                                class="form-control" 
                                id="name"
                                placeholder="Enter your name"
                            >
                        </div>

                        <div v-if="name" class="alert alert-success">
                            Hello, <strong>{{ name }}</strong>! Welcome to Vue 3.
                        </div>

                        <button @click="incrementCounter" class="btn btn-primary">
                            Clicked {{ counter }} times
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// Reactive data
const message = ref("I'm a Vue 3 example component using Composition API!");
const name = ref('');
const counter = ref(0);

// Methods
const incrementCounter = () => {
    counter.value++;
};

// Lifecycle hook
onMounted(() => {
    console.log('Vue 3 Component mounted successfully!');
});
</script>

<style scoped>
.card {
    margin-top: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 15px;
}

.alert {
    margin-top: 15px;
}

button {
    margin-top: 10px;
}
</style>

