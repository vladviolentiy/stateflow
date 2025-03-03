<template>
  <h4>{{ Localization.configure.email }}</h4>

  <button class="btn btn-outline-success w-100 my-1" @click="addNewShow">
    {{ Localization.email.add }}
  </button>
  <p class="text-center my-1" v-if="list.length === 0">{{ Localization.email.notAdded }}</p>
  <ul class="list-group my-1" v-if="list.length > 0">
    <li
      class="list-group-item"
      :key="item.id"
      v-for="item in list"
      @click="editItemGetInfo(item.id)"
    >
      {{ item.email }}
    </li>
  </ul>

  <div
    class="offcanvas offcanvas-end"
    tabindex="-1"
    id="addEditEmail"
    aria-labelledby="offcanvasLabel"
  >
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="offcanvasLabel">
        {{ newEditId === null ? 'Добавление нового email' : 'Редактирование email' }}
      </h5>
      <button
        type="button"
        class="btn-close"
        data-bs-dismiss="offcanvas"
        aria-label="Close"
      ></button>
    </div>
    <div class="offcanvas-body">
      <label for="newEditEmail">Email:</label>
      <input type="email" class="form-control" id="newEditEmail" v-model="newEditEmail" />
      <div class="form-check">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="newEditAllowAuth"
          id="newEditAllowAuth"
        />
        <label class="form-check-label" for="newEditAllowAuth">
          {{ Localization.allowAuth }}
        </label>
      </div>
      <p class="m-0 text-danger text-center">{{ errorText }}</p>
      <button class="btn btn-primary w-100 my-1" v-if="newEditId === null" @click="addNewEmail">
        {{ Localization.add }}
      </button>
      <button class="btn btn-primary w-100 my-1" v-if="newEditId !== null" @click="editEmailItem">
        {{ Localization.edit }}
      </button>
      <button class="btn btn-danger w-100 my-1" v-if="newEditId !== null" @click="deleteEmail">
        {{ Localization.delete }}
      </button>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { mapState } from 'pinia'
import { appStore } from '@/stores/AppStore'
import type { emailListResponseItem } from '@/gateway/Interfaces/DashboardGatewayIntefaces'
import { Offcanvas } from 'bootstrap'
import Security from '@/security/Security'
import Encryption from '@/security/Encryption'
import Hashing from '@/security/Hashing'
import Validation from '@/security/Validation'

export default defineComponent({
  name: 'EmailConfigure',
  data() {
    return {
      list: [] as emailListResponseItem[],

      newEditId: null as number | null,
      newEditEmail: '' as string,
      newEditAllowAuth: false as boolean,
      errorText: '' as string,

      cryptoKey: null as null | CryptoKey
    }
  },
  computed: {
    ...mapState(appStore, ['DashboardGateway', 'Localization'])
  },
  async mounted() {
    this.cryptoKey = await Security.getDerivedKey()

    const response = await this.DashboardGateway.getEmailList()
    if (response.success) {
      await this.remapListElements(response.data)
    }
  },
  methods: {
    async remapListElements(response: emailListResponseItem[]) {
      const iv = localStorage.getItem('iv') ?? ''
      this.list = await Promise.all(
        response.map(async item => {
          if (this.cryptoKey !== null) {
            item.email = await Encryption.decryptAES(item.email, this.cryptoKey, iv)
          }
          return item
        })
      )
    },
    addNewShow(): void {
      this.newEditId = null
      this.newEditEmail = ''
      this.newEditAllowAuth = false
      const item = Offcanvas.getOrCreateInstance('#addEditEmail')
      item.show()
    },
    editItemGetInfo(itemId: number): void {
      this.newEditId = itemId
      this.DashboardGateway.getEmailItem(itemId).then(async response => {
        if (response.success) {
          if (this.cryptoKey !== null) {
            this.newEditEmail = await Encryption.decryptAES(
              response.data.emailEncrypted,
              this.cryptoKey,
              localStorage.getItem('iv') ?? ''
            )
            this.newEditAllowAuth = response.data.allowAuth
            const item = Offcanvas.getOrCreateInstance('#addEditEmail')
            item.show()
          }
        }
      })
    },
    deleteEmail(): void {
      if (this.newEditId === null) return
      this.DashboardGateway.deleteEmail(this.newEditId).then(response => {
        if (response.success) {
          const item = Offcanvas.getOrCreateInstance('#addEditEmail')
          item.hide()
          this.remapListElements(response.data)
        }
      })
    },
    async editEmailItem() {
      if (this.newEditEmail === '') {
        this.errorText = this.Localization.validation.emailNull
        return
      }
      if (!Validation.isEmail(this.newEditEmail)) {
        this.errorText = this.Localization.validation.emailIncorrect
        return
      } else {
        this.errorText = ''
      }
      if (this.cryptoKey === null || this.newEditId === null) return
      const emailHash = await Hashing.digest(this.newEditEmail)
      const encryptedEmail = await Encryption.encryptAES(
        this.newEditEmail,
        this.cryptoKey,
        localStorage.getItem('iv') ?? ''
      )
      this.DashboardGateway.editEmailItem(
        this.newEditId,
        encryptedEmail,
        emailHash,
        this.newEditAllowAuth
      ).then(response => {
        if (response.success) {
          const item = Offcanvas.getOrCreateInstance('#addEditEmail')
          item.hide()
          this.remapListElements(response.data)
        } else {
          this.errorText = response.text
        }
      })
    },
    async addNewEmail() {
      if (this.newEditEmail === '') {
        this.errorText = this.Localization.validation.emailNull
        return
      }
      if (!Validation.isEmail(this.newEditEmail)) {
        this.errorText = 'Ошибка ввода email'
        return
      } else {
        this.errorText = ''
      }
      if (this.cryptoKey === null) return
      const emailHash = await Hashing.digest(this.newEditEmail)
      const encryptedEmail = await Encryption.encryptAES(
        this.newEditEmail,
        this.cryptoKey,
        localStorage.getItem('iv') ?? ''
      )
      this.DashboardGateway.addNewEmail(encryptedEmail, emailHash, this.newEditAllowAuth).then(
        response => {
          if (response.success) {
            const item = Offcanvas.getOrCreateInstance('#addEditEmail')
            item.hide()
            this.remapListElements(response.data)
          } else {
            this.errorText = response.text
          }
        }
      )
    }
  }
})
</script>

<style scoped></style>
