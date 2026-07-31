<script setup>
import { ref, onMounted } from 'vue'
import TaskItem from '@/components/TaskItem.vue'

const tasks = ref([])
const api = "http://localhost:8000"
const loading = ref(true)
const loadError = ref(null)
const actionError = ref(null)
const title = ref("")
const description = ref("")

const temp_creator_id = 5

onMounted(async () => {
    await loadTasks()
})

async function loadTasks() {
    try {
        loadError.value = null
        loading.value = true
        const response = await fetch(api + "/tasks")
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`)
        }
        tasks.value = await response.json()
    } catch (e) {
        loadError.value = "An error occurred while retrieving the tasks!"
        console.error('Error fetching tasks: ', e)
    } finally {
        loading.value = false
    }
}

async function createTask() {
    try {
        actionError.value = null
        const response = await fetch(api + "/tasks", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                title: title.value,
                description: description.value,
                creator_id: temp_creator_id
            })
        })
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`)
        }
        title.value = ""
        description.value = ""
        loadTasks()
    } catch (e) {
        actionError.value = "An error occurred while creating a task!"
        console.error('Error creating task: ', e)
    }

}

async function deleteTask(id) {
    try {
        actionError.value = null
        const response = await fetch(api + '/tasks/' + id, {
            method: "DELETE"
        })
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`)
        }        
        tasks.value = tasks.value.filter(task => task.id !== id)
    } catch (e) {
        actionError.value = "An error occurred while deleting a task!"
        console.error('Error deleting task: ', e)
    }
    
}
</script>

<template>
    <h1>My tasks</h1>
    <br />
    <p v-if="actionError" style="color: red;">{{ actionError }}</p>
    <br />
    <p v-if="loading">Is Loading...</p>
    <p v-else-if="loadError" style="color: red;">{{ loadError }}
        <button @click="loadTasks">Retry</button>
    </p>
    <p v-else-if="tasks.length === 0">No task to display!</p>
    <template v-else>
        <TaskItem v-for="task in tasks" :key="task.id" :task="task" @delete="deleteTask" />
    </template>
    <br />
    <form @submit.prevent="createTask">
        <input :value="title" @input="title = $event.target.value" />
        <br />
        <input :value="description" @input="description = $event.target.value" />
        <br />
        <button type="submit">Submit</button>
    </form>
</template>