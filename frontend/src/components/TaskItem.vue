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
    <form v-if="editing" @submit.prevent="saveEdit">
        <input v-model.trim="draftTitle" @keyup.esc="cancelEdit" />
        <br />
        <textarea v-model="draftDescription" @keyup.esc="cancelEdit"></textarea>
        <br />
        <button type="submit" style="margin-right: 50px;">Save</button>
        <button type="button" @click="cancelEdit">Cancel</button>
    </form>
    <div v-else>
        <h2>{{ task.title }}</h2>
        <p v-if="expanded">{{ task.description }}</p>
        <button @click="expanded = !expanded" style="margin-right: 10px;">Expand</button>
        <button @click="emit('delete', task.id)" style="margin-right: 10px;">Delete</button>
        <button @click="startEdit">Edit</button>
    </div>
</template>