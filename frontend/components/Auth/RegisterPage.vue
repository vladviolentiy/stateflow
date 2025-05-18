<template>
  <div v-if="registerStage === 'enterData' || registerStage === 'awaitRegister'">
    <h4>{{ store.Localization.register }}</h4>
    <label for="lName">Фамилия (на латинице):</label>
    <input type="text" class="form-control" v-model="lName" id="lName" />
    <label for="fName">Имя (на латинице):</label>
    <input type="text" class="form-control" v-model="fName" id="fName" />
    <label for="dOfBirth">Дата рождения:</label>
    <input type="date" class="form-control" v-model="dOfBirth" id="dOfBirth" />
    <label for="password">Введите пароль:</label>
    <input
      type="password"
      class="form-control"
      @input="passwordEnter"
      v-model="password"
      id="password"
    />
    <label for="passwordRepeat">Повторите пароль:</label>
    <input
      type="password"
      class="form-control"
      @input="passwordEnter"
      v-model="passwordRepeat"
      id="passwordRepeat"
    />
    <p class="text-primary m-0">Энтропия Log2 - {{ entropyLog2 }}</p>

    <div class="form-check">
      <input
        class="form-check-input"
        type="checkbox"
        v-model="dontVerificate"
        id="dontVerificate"
      />
      <label class="form-check-label" for="dontVerificate"
        >Я отказываюсь проходить верификацию</label
      >
    </div>

    <button class="btn btn-primary w-100" @click="registerNewUser" :disabled="buttonDisabled">
      {{ registerStage === 'awaitRegister' ? 'Выполняется регистрация' : 'Создать пользователя' }}
    </button>
    <p class="m-0 text-center text-danger">{{ errorText }}</p>
  </div>

  <div v-if="registerStage === 'success'">
    <p class="m-0 text-center">Регистрация завершена. Ваш UUID:</p>
    <input type="text" class="form-control" readonly v-model="newClientUUID" />
    <p class="m-0 text-center">Используйте его для авторизации</p>
    <router-link to="/auth">Вернутся на страницу авторизации</router-link>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import Encryption from '@/security/Encryption'
import Hashing from '@/security/Hashing'
import Security from '@/security/Security'
import AuthGateway from '@/gateway/AuthGateway'
import Mathematics from '@/security/Mathematics'
import { appStore } from '@/stores/AppStore.ts'

const store = appStore()

const registerStage = ref<'enterData' | 'awaitRegister' | 'success'>('enterData')
const fName = ref<string>('')
const lName = ref<string>('')
const dOfBirth = ref<string>('')
const password = ref<string>('')
const passwordRepeat = ref<string>('')
const errorText = ref<string>('')
const newClientUUID = ref<string>('')
const buttonDisabled = ref<boolean>(false)
const dontVerificate = ref<boolean>(false)

function passwordEnter(): void {
  if (password.value !== passwordRepeat.value) {
    errorText.value = store.Localization.validation.passwordNotRepeat
    buttonDisabled.value = true
  } else {
    errorText.value = ''
    buttonDisabled.value = false
  }
}

const entropyLog2 = computed(function () {
  return Math.pow(2, Mathematics.entropyLog2(password.value))
})

async function registerNewUser() {
  errorText.value = ''
  if (fName.value === '') {
    errorText.value = store.Localization.validation.fNameNull
    return
  }
  if (lName.value === '') {
    errorText.value = store.Localization.validation.lNameNull
    return
  }
  if (dOfBirth.value === '') {
    errorText.value = store.Localization.validation.dobNull
    return
  }
  if (password.value === '') {
    errorText.value = store.Localization.validation.passwordNull
    return
  }
  if (password.value !== passwordRepeat.value) {
    errorText.value = store.Localization.validation.passwordNotRepeat
    return
  }
  buttonDisabled.value = true
  registerStage.value = 'awaitRegister'
  const iv = Security.getRandom(16)
  const salt = Security.getRandom(16)

  const pbkdf2Key = await Encryption.deriveKey(password.value, salt)
  const passwordHash = await Hashing.digest(Security.ab2str(salt) + '' + password.value)
  const rsaKey = await Encryption.generateRSA()
  const basePublic = await Encryption.exportPublicKey(rsaKey.publicKey)
  const basePrivate = await Encryption.exportPrivateKey(rsaKey.privateKey)

  const encryptedPrivateKey = await Encryption.encryptAESBytes(basePrivate, pbkdf2Key, iv)
  console.log(
    fName.value +
      '-' +
      lName.value +
      '-' +
      dOfBirth.value +
      '-' +
      window.btoa(Security.ab2str(salt))
  )
  const ivString = window.btoa(Security.ab2str(iv))
  AuthGateway.registerNewUser(
    passwordHash,
    ivString,
    window.btoa(Security.ab2str(salt)),
    basePublic,
    window.btoa(Security.ab2str(encryptedPrivateKey)),
    await Encryption.encryptAES(fName.value, pbkdf2Key, ivString),
    await Encryption.encryptAES(lName.value, pbkdf2Key, ivString),
    await Encryption.encryptAES(dOfBirth.value, pbkdf2Key, ivString),
    await Hashing.digest(
      fName.value +
        '-' +
        lName.value +
        '-' +
        dOfBirth.value +
        '-' +
        window.btoa(Security.ab2str(salt))
    )
  )
    .then(response => {
      buttonDisabled.value = false
      if (response.success) {
        registerStage.value = 'success'
        newClientUUID.value = response.data.uuid
      } else {
        registerStage.value = 'enterData'
        errorText.value = response.text
      }
    })
    .catch(() => {
      buttonDisabled.value = false
      registerStage.value = 'enterData'
      errorText.value = 'Ошибка запроса'
    })

  console.log('iv - ' + window.btoa(Security.ab2str(iv)))
  console.log('salt - ' + window.btoa(Security.ab2str(salt)))
  console.log('passwordHash - ' + passwordHash)
  console.log('publicKey - ' + basePublic)
  console.log('privateKey - ' + encryptedPrivateKey)
}
</script>

<style scoped></style>
