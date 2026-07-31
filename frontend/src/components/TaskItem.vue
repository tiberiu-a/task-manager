<script setup>
import { ref } from 'vue'

defineProps({
    task: { type: Object, required: true}
})

const emit = defineEmits(['delete'])

const expanded = ref(false)
</script>

<template>
    <div>
        <h2>{{ task.title }}</h2>
        <p v-if="expanded">{{ task.description }}</p>
        <button @click="expanded=!expanded" style="margin-right: 10px;">Expand</button>
        <button @click="emit('delete', task.id)">Delete</button>

    </div>
</template>