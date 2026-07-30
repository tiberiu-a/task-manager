<script setup>
import { ref, onMounted } from 'vue'
import TaskItem from '@/components/TaskItem.vue'

const tasks = ref([])
const api = "http://localhost:8000"
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
    loadTasks(api + "/tasks")
})

async function loadTasks(url) {
    try {
        const response = await fetch(url)
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`)
        }
        tasks.value = await response.json()
    } catch (e) {
        error.value = "An error occurred while retrieving the tasks!"
        console.error('Error fetching tasks: ', e)
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <h1>My tasks</h1>
    <br/>
    <p v-if="loading">Is Loading...</p>
    <p v-else-if="error">{{ error }}</p>
    <p v-else-if="tasks.length === 0">No task to display!</p>
    <template v-else>
        <TaskItem 
            v-for="task in tasks"
            :key="task.id"
            :task="task"
        />    
    </template>   
    
</template>