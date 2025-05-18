<template>
  <h4>Сессии</h4>
  <div class="row" v-if="list.length > 0">
    <div class="col-md-4" v-for="item in list" :key="item.authHash">
      <div class="card w-100">
        <div class="card-body">
          <h5 class="card-title">
            Сессия от {{ item.createdAt }}
            <span class="badge bg-success" v-if="item.authHash.toLowerCase() === currentSession"
              >Текущая</span
            >
          </h5>
          <p class="card-text">Ip адрес - {{ item.ips[0] }}</p>
          <p class="card-text">User-agent - {{ item.uas[0] }}</p>
          <button
            class="btn btn-link"
            @click="killSession(item.authHash)"
            v-if="item.authHash.toLowerCase() !== currentSession"
          >
            Закрыть
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { appStore } from '@/stores/AppStore.ts'
import type { sessionListResponseItem } from '@/gateway/Interfaces/DashboardGatewayIntefaces.ts'
import { computed, onMounted, ref } from 'vue'
import Security from '@/security/Security.ts'
import Encryption from '@/security/Encryption.ts'

const store = appStore()
const list = ref<sessionListResponseItem[]>([])
const cryptoKey = ref<CryptoKey | null>(null)

onMounted(async function () {
  cryptoKey.value = await Security.getDerivedKey()

  const response = await store.DashboardGateway.getSessionsList()
  if (response.success) {
    remapListElements(response.data)
  }
})

const currentSession = computed(() => {
  return localStorage.getItem('authToken') ?? ''.toLowerCase()
})

async function remapListElements(response: sessionListResponseItem[]) {
  const iv = localStorage.getItem('iv') ?? ''
  console.log(response)
  list.value = await Promise.all(
    response.map(async item => {
      if (cryptoKey.value !== null) {
        item.uas = await Promise.all(
          item.uas.map(async itemUa => {
            if (cryptoKey.value !== null) {
              itemUa = await Encryption.decryptAES(itemUa, cryptoKey.value, iv)
            }
            return itemUa
          })
        )
        item.ips = await Promise.all(
          item.ips.map(async itemIp => {
            if (cryptoKey.value !== null) {
              itemIp = await Encryption.decryptAES(itemIp, cryptoKey.value, iv)
            }
            return itemIp
          })
        )
      }

      return item
    })
  )
  console.log(list.value)
}

function killSession(hash: string): void {
  store.DashboardGateway.killSession(hash, true).then(response => {
    if (response.success) {
      remapListElements(response.data)
    }
  })
}
</script>

<style scoped></style>
