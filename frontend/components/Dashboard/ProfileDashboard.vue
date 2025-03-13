<template>
  <h4>{{ store.Localization.profile }}</h4>
  <ul class="list-group list-group-flush">
    <router-link to="/dashboard/profile/email" class="list-group-item">{{
      store.Localization.configure.email
    }}</router-link>
    <router-link to="/dashboard/profile/phones" class="list-group-item">{{
      store.Localization.configure.phone
    }}</router-link>
    <router-link to="/dashboard/profile/sessions" class="list-group-item">{{
      store.Localization.configure.session
    }}</router-link>
    <li class="list-group-item text-danger" @click="logOut">{{ store.Localization.logout }}</li>
  </ul>
</template>

<script setup lang="ts">
import AuthenticationMethods from '@/security/AuthenticationMethods'
import { useRouter } from 'vue-router'
import { appStore } from '@/stores/AppStore.ts'

const store = appStore()
const route = useRouter()

function logOut(): void {
  const token = localStorage.getItem('authToken') ?? ''
  store.DashboardGateway.killSession(token, false)
  AuthenticationMethods.logOut()
  route.push('/auth')
}
</script>

<style scoped></style>
