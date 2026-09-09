<script setup>
import { ref } from 'vue';
import Login from './pages/Login.vue';
import RoomTypes from './pages/RoomTypes.vue';
const loggedIn = ref(Boolean(localStorage.getItem('admin_token')));
function loggedInNow() { loggedIn.value = true; }
</script>
<template><v-app><Login v-if="!loggedIn" @logged-in="loggedInNow" /><RoomTypes v-else /></v-app></template>
