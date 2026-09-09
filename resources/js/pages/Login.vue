<script setup>
import { reactive } from 'vue'; import { useAuthStore } from '../stores/auth';
const emit = defineEmits(['logged-in']); const auth = useAuthStore(); const form = reactive({ email: 'admin@example.test', password: 'password' });
async function submit() { try { await auth.login(form); emit('logged-in'); } catch {} }
</script>
<template><v-main><v-container class="fill-height" style="max-width:480px"><v-card class="pa-8 w-100"><v-card-title class="text-h5 mb-4">Panel de administración</v-card-title><v-alert v-if="auth.error" type="error" class="mb-4">{{ auth.error }}</v-alert><v-form @submit.prevent="submit"><v-text-field v-model="form.email" label="Email" type="email" /><v-text-field v-model="form.password" label="Contraseña" type="password" /><v-btn type="submit" color="primary" block :loading="auth.loading">Entrar</v-btn></v-form></v-card></v-container></v-main></template>
