<template>
  <h4>Сессии</h4>
  <div class="row" v-if="list.length > 0">
    <div class="col-md-4" v-for="item in list">
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

<script lang="ts">
import { defineComponent } from 'vue'
import Encryption from '@/security/Encryption'
import Security from '@/security/Security'
import { mapState } from 'pinia'
import { appStore } from '@/stores/AppStore'
import type {
  sessionListResponseItem
} from '@/gateway/Interfaces/DashboardGatewayIntefaces'
export default defineComponent({
  name: 'SessionConfigure',
  data() {
    return {
      list: [] as sessionListResponseItem[],
      cryptoKey: null as null | CryptoKey
    }
  },
  async mounted() {
    this.cryptoKey = await Security.getDerivedKey()

    const response = await this.DashboardGateway.getSessionsList()
    if (response.success) {
      await this.remapListElements(response.data)
    }
  },
  computed: {
    currentSession(): string {
      return localStorage.getItem('authToken') ?? ''.toLowerCase()
    },
    ...mapState(appStore, ['DashboardGateway', 'Localization'])
  },
  methods: {
    async remapListElements(response: sessionListResponseItem[]) {
      const iv = localStorage.getItem('iv') ?? ''
      console.log(response)
      this.list = await Promise.all(
        response.map(async item => {
          if (this.cryptoKey !== null) {
            item.uas = await Promise.all(
              item.uas.map(async itemUa => {
                if (this.cryptoKey !== null) {
                  itemUa = await Encryption.decryptAES(itemUa, this.cryptoKey, iv)
                }
                return itemUa
              })
            )
            item.ips = await Promise.all(
              item.ips.map(async itemIp => {
                if (this.cryptoKey !== null) {
                  itemIp = await Encryption.decryptAES(itemIp, this.cryptoKey, iv)
                }
                return itemIp
              })
            )
          }

          return item
        })
      )
      console.log(this.list)
    },
    killSession(hash: string): void {
      this.DashboardGateway.killSession(hash, true).then(response => {
        if (response.success) {
          this.remapListElements(response.data)
        }
      })
    }
  }
})
</script>

<style scoped></style>
