<template>
  <h4>{{ Localization.configure.phone }}</h4>

  <button class="btn btn-outline-success w-100 my-1" @click="addNewShow">
    {{ Localization.phone.add }}
  </button>
  <p class="text-center my-1" v-if="list.length === 0">{{ Localization.phone.notAdded }}</p>
  <ul class="list-group my-1" v-if="list.length > 0">
    <li
      class="list-group-item"
      :key="item.id"
      v-for="item in list"
      @click="editItemGetInfo(item.id)"
    >
      {{ item.phone }}
    </li>
  </ul>

  <div
    class="offcanvas offcanvas-end"
    tabindex="-1"
    id="addEditPhone"
    aria-labelledby="offcanvasLabel"
  >
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="offcanvasLabel">
        {{ newEditId === null ? 'Добавление нового телефона' : 'Редактирование телефона' }}
      </h5>
      <button
        type="button"
        class="btn-close"
        data-bs-dismiss="offcanvas"
        aria-label="Close"
      ></button>
    </div>
    <div class="offcanvas-body">
      <label for="newEditPhone">{{ Localization.phone.generic }}:</label>
      <input type="tel" class="form-control" id="newEditPhone" v-model="newEditPhone" />
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
      <button class="btn btn-primary w-100 my-1" v-if="newEditId === null" @click="addNewPhone">
        {{ Localization.add }}
      </button>
      <button class="btn btn-primary w-100 my-1" v-if="newEditId !== null" @click="editPhone">
        {{ Localization.edit }}
      </button>
      <button class="btn btn-danger w-100 my-1" v-if="newEditId !== null" @click="deletePhone">
        {{ Localization.delete }}
      </button>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { Offcanvas } from 'bootstrap'
import { mapState } from 'pinia'
import { appStore } from '@/stores/AppStore'
import type { phoneListResponseItem } from '@/gateway/Interfaces/DashboardGatewayIntefaces'
import Validation from '@/security/Validation'
import Hashing from '@/security/Hashing'
import Encryption from '@/security/Encryption'
import Security from '@/security/Security'
export default defineComponent({
  name: 'PhonesConfigure',
  data() {
    return {
      list: [] as phoneListResponseItem[],

      newEditId: null as number | null,
      newEditPhone: '' as string,
      newEditAllowAuth: false as boolean,
      errorText: '' as string,

      cryptoKey: null as null | CryptoKey
    }
  },
  async mounted() {
    this.cryptoKey = await Security.getDerivedKey()

    const response = await this.DashboardGateway.getPhoneList()
    if (response.success) {
      await this.remapListElements(response.data)
    }
  },
  computed: {
    ...mapState(appStore, ['DashboardGateway', 'Localization'])
  },
  methods: {
    async remapListElements(response: phoneListResponseItem[]) {
      const iv = localStorage.getItem('iv') ?? ''
      this.list = await Promise.all(
        response.map(async item => {
          if (this.cryptoKey !== null) {
            item.phone = await Encryption.decryptAES(item.phone, this.cryptoKey, iv)
          }
          return item
        })
      )
    },
    addNewShow(): void {
      this.newEditId = null
      this.newEditPhone = ''
      this.newEditAllowAuth = false
      const item = Offcanvas.getOrCreateInstance('#addEditPhone')
      item.show()
    },

    async editPhone() {
      if (this.newEditPhone === '') {
        this.errorText = this.Localization.validation.phoneNull
        return
      }
      if (!Validation.isPhone(this.newEditPhone)) {
        this.errorText = this.Localization.validation.phoneIncorrect
        return
      } else {
        this.errorText = ''
      }
      if (this.cryptoKey === null || this.newEditId === null) return
      const phoneHash = await Hashing.digest(this.newEditPhone)
      const phoneEncrypted = await Encryption.encryptAES(
        this.newEditPhone,
        this.cryptoKey,
        localStorage.getItem('iv') ?? ''
      )
      this.DashboardGateway.editPhoneItem(
        this.newEditId,
        phoneEncrypted,
        phoneHash,
        this.newEditAllowAuth
      ).then(response => {
        if (response.success) {
          const item = Offcanvas.getOrCreateInstance('#addEditPhone')
          item.hide()
          this.remapListElements(response.data)
        } else {
          this.errorText = response.text
        }
      })
    },
    editItemGetInfo(itemId: number): void {
      this.newEditId = itemId
      this.DashboardGateway.getPhoneItem(itemId).then(async response => {
        if (response.success) {
          if (this.cryptoKey !== null) {
            this.newEditPhone = await Encryption.decryptAES(
              response.data.phoneEncrypted,
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
    async addNewPhone() {
      if (this.newEditPhone === '') {
        this.errorText = this.Localization.validation.phoneNull
        return
      }
      if (!Validation.isPhone(this.newEditPhone)) {
        this.errorText = 'Ошибка ввода телефона'
        return
      } else {
        this.errorText = ''
      }
      if (this.cryptoKey === null) return
      const emailHash = await Hashing.digest(this.newEditPhone)
      const encryptedEmail = await Encryption.encryptAES(
        this.newEditPhone,
        this.cryptoKey,
        localStorage.getItem('iv') ?? ''
      )
      this.DashboardGateway.addNewPhone(encryptedEmail, emailHash, this.newEditAllowAuth).then(
        response => {
          if (response.success) {
            const item = Offcanvas.getOrCreateInstance('#addEditPhone')
            item.hide()
            this.remapListElements(response.data)
          } else {
            this.errorText = response.text
          }
        }
      )
    },
    deletePhone(): void {
      if (this.newEditId === null) return
      this.DashboardGateway.deletePhone(this.newEditId).then(response => {
        if (response.success) {
          const item = Offcanvas.getOrCreateInstance('#addEditPhone')
          item.hide()
          this.remapListElements(response.data)
        }
      })
    }
  }
})
</script>

<style scoped></style>
