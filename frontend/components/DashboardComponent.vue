<template>
  <router-view></router-view>
  <div class="fixed-bottom">
    <div class="btn-group w-100">
      <router-link
        to="/dashboard/services"
        class="btn btn-outline-secondary"
        exact-active-class="active"
        ><i class="bi bi-list"></i
      ></router-link>
      <router-link to="/dashboard" class="btn btn-outline-secondary" exact-active-class="active"
        ><i class="bi bi-house"></i
      ></router-link>
      <router-link
        to="/dashboard/profile"
        class="btn btn-outline-secondary"
        exact-active-class="active"
        ><i class="bi bi-person"></i
      ></router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { appStore } from '@/stores/AppStore.ts'
import Encryption from '@/security/Encryption.ts'
import Security from '@/security/Security.ts'
import AuthenticationMethods from '@/security/AuthenticationMethods.ts'
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const store = appStore()
const router = useRouter()

onMounted(function () {
  const token = localStorage.getItem('authToken')
  const iv = localStorage.getItem('iv')
  if (token === null || iv === null) {
    AuthenticationMethods.logOut()
    router.push('/auth')
    return
  }
  store.DashboardGateway.checkAuth().then(response => {
    if (!response.success) {
      AuthenticationMethods.logOut()
      router.push('/auth')
    } else {
      store.setNewLang(response.data.lang)
      sendEncryptionInfo(
        response.data.ip,
        response.data.ua,
        response.data.acceptLang,
        response.data.acceptEncoding,
        iv ?? ''
      )
    }
  })
})

async function sendEncryptionInfo(ip: string, ua: string, al: string, ae: string, iv: string) {
  const cryptoKey = await Security.getDerivedKey()
  const date = new Date()
  store.DashboardGateway.insertMetaHashInfo(
    await Encryption.encryptAES(ip, cryptoKey, iv),
    await Encryption.encryptAES(ua, cryptoKey, iv),
    await Encryption.encryptAES(al, cryptoKey, iv),
    await Encryption.encryptAES(ae, cryptoKey, iv),
    await Encryption.encryptAES(date.toISOString(), cryptoKey, iv)
  )
  console.log(Date.now())
}
</script>

<style scoped></style>
