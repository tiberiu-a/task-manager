<script setup>
import { ref } from 'vue'

defineProps({
    task: { type: Object, required: true}
})

const expanded = ref(false)
</script>

<template>
    <div>
        <h2>{{ task.title }}</h2>
        <p v-if="expanded">{{ task.description }}</p>
        <button @click="expanded=!expanded">Expand</button>
    </div>
</template>