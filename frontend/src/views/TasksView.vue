<script setup>
import { ref, onMounted } from 'vue'
import TaskItem from '@/components/TaskItem.vue'

const tasks = ref([])
const api = "http://localhost:8000"
const loading = ref(true)
const pending = ref(false)
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
        loading.value = tasks.value.length === 0
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
        pending.value = true
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
    } finally {
        pending.value = false
    }

}

async function deleteTask(id) {
    try {
        actionError.value = null
        pending.value = true
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
    } finally {
        pending.value = false
    }

}

async function updateTask(data) {
    try {
        actionError.value = null
        pending.value = true
        const response = await fetch(api + '/tasks/' + data.id, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                title: data.title,
                description: data.description
            })
        })
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`)
        }
        tasks.value = tasks.value.map(t => t.id === data.id ? { ...t, title: data.title, description: data.description } : t)
    } catch (e) {
        actionError.value = "An error occurred while updating a task!"
        console.error('Error updating task: ', e)
    } finally {
        pending.value = false
    }
}
</script>

<template>
    <div class="root-tasks">
        <h1>My tasks</h1>
        <p v-if="actionError" class="error">{{ actionError }}</p>
        <p v-if="pending" class="state">Pending...</p>
        <p v-if="loading" class="state">Is Loading...</p>
        <p v-else-if="loadError" class="error">{{ loadError }}
            <button @click="loadTasks" class="secondary">Retry</button>
        </p>
        <p v-else-if="tasks.length === 0" class="state">No task to display!</p>
        <div v-else class="task-list">
            <TaskItem v-for="task in tasks" :key="task.id" :task="task" @delete="deleteTask" @update="updateTask" />
        </div>
        <form @submit.prevent="createTask" class="card task-form">
            <label>
                Title
                <input v-model.trim="title" />
            </label>
            <label>
                Description
                <textarea v-model="description"></textarea>
            </label>
            <button type="submit" class="primary">Submit</button>
        </form>
    </div>
</template>

<style scoped>
.root-tasks {
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-width: 720px;
    margin: 0 auto;
}

.task-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.task-form {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.state {
    color: var(--text-muted)
}

.error {
    color: var(--danger);
    background-color: var(--danger-bg);
    padding: 8px 12px;
    border-radius: 4px;
}
</style>