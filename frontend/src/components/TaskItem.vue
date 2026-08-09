<script setup>
import { ref } from 'vue'

const props = defineProps({
    task: { type: Object, required: true }
})

const emit = defineEmits(['delete', 'update'])

const expanded = ref(false)
const editing = ref(false)
const draftTitle = ref("")
const draftDescription = ref("")

function startEdit() {
    draftTitle.value = props.task.title
    draftDescription.value = props.task.description
    editing.value = true
}

function cancelEdit() {
    editing.value = false
}

function saveEdit() {
    emit('update', {
        id: props.task.id,
        title: draftTitle.value,
        description: draftDescription.value
    })
    editing.value = false
}


</script>

<template>
    <div class="card">
        <form v-if="editing" @submit.prevent="saveEdit" class="task-edit">
            <label>
                Title
                <input v-model.trim="draftTitle" @keyup.esc="cancelEdit" />
            </label>
            <label>
                Description
                <textarea v-model="draftDescription" @keyup.esc="cancelEdit"></textarea>
            </label>
            <div class="actions">
                <button type="submit" class="primary">Save</button>
                <button type="button" @click="cancelEdit" class="secondary">Cancel</button>
            </div>
        </form>
        <div v-else class="task">
            <div class="task-text">
                <h2 class="title">{{ task.title }}</h2>
                <p v-if="expanded" class="description">{{ task.description }}</p>
            </div>
            <div class="actions">
                <button @click="expanded = !expanded" class="secondary">Expand</button>
                <button @click="startEdit" class="secondary">Edit</button>
                <button @click="emit('delete', task.id)" class="danger">Delete</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.actions {
    display: flex;
    gap: 8px;
}

.task {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.task-text {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.title {
    font-size: 16px;
    font-weight: 600;
}

.description {
    color: var(--text-muted);
}

.task-edit {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
</style>