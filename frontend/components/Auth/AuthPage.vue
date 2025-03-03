<template>
  <div class="fixed-top">
    <div class="container">
      <div class="row justify-content-end">
        <div class="col-6 col-md-2">
          <select
            name=""
            id=""
            class="form-select form-select-sm"
            v-model="selectedLang"
            @change="setLang"
          >
            <option value="en">English</option>
            <option value="ru">Русский</option>
            <option value="by">Беларуская</option>
            <option value="ua">Українська</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row justify-content-center align-items-center" style="height: 100vh">
    <div class="col-12">
      <h4 class="text-center">Авторизация</h4>
      <input
        type="text"
        class="form-control my-1"
        v-model="authString"
        :disabled="step !== 'auth'"
        placeholder="Email, Телефон или Uuid"
      />
      <input
        type="password"
        class="form-control my-1"
        v-model="authPassword"
        v-if="step === 'password'"
        placeholder="Введите пароль"
      />
      <button class="btn btn-outline-primary w-100 my-1" @click="checkPhone" v-if="step === 'auth'">
        {{ store.Localization.next }}
      </button>
      <button
        class="btn btn-outline-primary w-100 my-1"
        @click="passwordAuth"
        v-if="step === 'password'"
      >
        {{ store.Localization.enter }}
      </button>
      <router-link to="/register" class="text-center btn btn-link w-100" v-if="step === 'auth'">{{
          store.Localization.register
      }}</router-link>
      <p class="text-danger text-center" v-if="authErrorCode !== null">
        {{ store.Localization.errorCodes[authErrorCode] }}
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AuthGateway from '../../gateway/AuthGateway'
import { appStore } from '@/stores/AppStore'
import Hashing from '@/security/Hashing'
import type { errorCodeList } from '@/localization/CustomInterfaces'
import Validation from '@/security/Validation'
import { useRouter } from 'vue-router'

const selectedLang = ref<'ru' | 'by' | 'ua' | 'en'>('ru')
const authString = ref('')
const authErrorCode = ref<errorCodeList | null>(null)
const step = ref<'password' | 'finger' | 'auth'>('auth')
const authPassword = ref<string>('')
const authSalt = ref<string>('')

const store = appStore()
const router = useRouter()

async function getUserNametype(): Promise<'phone' | 'uuid' | 'email'> {
  let type: 'phone' | 'uuid' | 'email' = 'phone'
  authErrorCode.value = null
  if (Validation.isUUID(authString.value)) {
    type = 'uuid'
  } else if (Validation.isEmail(authString.value)) {
    type = 'email'
    authString.value = await Hashing.digest(authString.value)
  } else if (Validation.isPhone(authString.value)) {
    authString.value = await Hashing.digest(authString.value)
  } else {
    authErrorCode.value = 5
    throw '5'
  }
  return type;
}

async function checkPhone() {
  const type = await getUserNametype()
  AuthGateway.preAuth(authString.value, type)
    .then(response => {
      if (response.success) {
        step.value = 'password'
        authSalt.value = window.atob(response.data.salt)
      } else {
        authErrorCode.value = response.code
      }
    })
    .catch(response => {
      console.log(response)
      authErrorCode.value = 0
    })
}
async function passwordAuth() {
  const info = await getUserNametype()
  const passwordHash = await Hashing.digest(authSalt.value + '' + authPassword.value)
  AuthGateway.passwordAuth(authString.value, info, passwordHash)
    .then(response => {
      if (response.success) {
        localStorage.setItem('authToken', response.data.hash)
        localStorage.setItem('salt', response.data.salt)
        localStorage.setItem('iv', response.data.iv)
        localStorage.setItem('password', authPassword.value)
        store.setNewToken(response.data.hash)
        router.push('/')
      } else {
        authErrorCode.value = response.code
      }
    })
    .catch(() => {
      authErrorCode.value = 0
    })
}

function setLang() {
  console.log('setNewLang')
  store.setNewLang(selectedLang.value)
}
</script>

<style scoped></style>
