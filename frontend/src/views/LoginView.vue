<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br bg-gradient-to-r from-blue-500 to-indigo-600">
        <div class="w-full max-w-sm mx-auto">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-6 rounded-xl">
                <div class="text-center mb-6">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-white font-bold text-xl">L</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Bienvenido</h2>
                    <p class="text-gray-600 text-sm">Inicia sesión en tu cuenta</p>
                </div>
                <form @submit.prevent="submitLogin" class="space-y-4">
                    <div>
                        <label for="email1" class="text-sm font-medium leading-none text-gray-700">Email</label>
                        <input v-model="email" type="email" id="email1" placeholder="admin@gmail.com" required
                            class="flex w-full rounded-md border-input px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1 bg-white/80 border-0 focus:bg-white" />
                    </div>
                    <div>
                        <label for="password1" class="text-sm font-medium leading-none text-gray-700">Contraseña</label>
                        <input v-model="password" type="password" id="password1" placeholder="admin123" required
                            class="flex w-full rounded-md border-input px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1 bg-white/80 border-0 focus:bg-white" />
                    </div>
                
                    <div v-if="errorMessage" class="text-red-600 text-sm font-medium">{{ errorMessage }}</div>
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none text-white hover:bg-primary/90 h-10 px-4 py-2 w-full bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 cursor-pointer">Iniciar
                        Sesión</button>
                </form>
            </div>
        </div>
    </div>

</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../http.js';
import { useAuthStore } from '../stores/auth';

const email = ref('');
const password = ref('');
const remember = ref(false);
const errorMessage = ref('');
const router = useRouter();
const authStore = useAuthStore();

async function submitLogin() {
    errorMessage.value = '';
    try {
        const response = await api.post('/api/login', {
            email: email.value,
            password: password.value,
            remember: remember.value,
        });

        if (response.data.success) {
            authStore.setUser(response.data.user);
            authStore.setToken(response.data.token);
            router.push('/dashboard');
        } else {
            errorMessage.value = response.data.message || 'Error en la autenticación';
        }
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Error de conexión';
    }
}
</script>

<style scoped>
/* Puedes mantener o adaptar el estilo tailwind que utilizaste */
</style>
