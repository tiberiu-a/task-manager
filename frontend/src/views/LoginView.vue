<script setup>
import { request } from '@/services/api';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const email = ref("")
const password = ref("")
const submitting = ref(false)
const error = ref(null)
const router = useRouter()

async function login() {
    submitting.value = true
    error.value = null
    try {
        await request("/login", {
            method: "POST",
            body: {
                email: email.value,
                password: password.value
            }
        })
        router.replace('/')
    } catch (e) {
        password.value = ""
        requestError(e.status)
        console.error("Login error: ", e.message)
    } finally {
        submitting.value = false
    }
}

function requestError(status) {
    if (status == 401) {
        error.value = "The email or password are wrong!"
    } else {
        error.value = "An error occurred during login"
    }
}
</script>

<template>
    <div class="root-login">
        <h1>Login in Task Manager</h1>
        <p v-if="error" class="error">{{ error }}</p>
        <p v-if="submitting">Is Loading...</p>
        <form @submit.prevent="login" class="login-form">
            <label>
                Email
                <input type="email" v-model.trim="email" required autocomplete="email">
            </label>
            <label>
                Password
                <input type="password" v-model="password" required autocomplete="current-password">
            </label>
            <button type="submit" class="primary" :disabled="submitting">Login</button>
        </form>
    </div>
</template>

<style scoped>
.root-login {
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-width: 320px;
    margin: 0 auto;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
</style>