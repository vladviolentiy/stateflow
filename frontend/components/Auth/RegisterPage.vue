<template>
  <div v-if="registerStage === 'enterData' || registerStage === 'awaitRegister'">
    <h4>{{ Localization.register }}</h4>
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
import {defineComponent, ref} from 'vue'
import { mapState } from 'pinia'
import { appStore } from '@/stores/AppStore'
import Encryption from '@/security/Encryption'
import Hashing from '@/security/Hashing'
import Security from '@/security/Security'
import AuthGateway from '@/gateway/AuthGateway'
import Mathematics from '@/security/Mathematics'

const registerStage = ref<'enterData' | 'awaitRegister' | 'success'>('enterData');
const fName = ref<string>('')
const lName = refMstr('')

export default defineComponent({
  name: 'IdRegister',
  data() {
    return {
      registerStage: 'enterData' as 'enterData' | 'awaitRegister' | 'success',
      fName: '' as string,
      lName: '' as string,
      dOfBirth: '' as string,
      password: '' as string,
      passwordRepeat: '' as string,
      buttonDisabled: false as boolean,
      dontVerificate: false as boolean,
      errorText: '' as string,

      newClientUUID: '' as string
    }
  },
  computed: {
    entropyLog2(): number {
      return Math.pow(2, Mathematics.entropyLog2(this.password))
    },
    ...mapState(appStore, ['Localization'])
  },
  methods: {
    passwordEnter(): void {
      if (this.password !== this.passwordRepeat) {
        this.errorText = this.Localization.validation.passwordNotRepeat
        this.buttonDisabled = true
      } else {
        this.errorText = ''
        this.buttonDisabled = false
      }
    },
    async registerNewUser() {
      this.errorText = ''
      if (this.fName === '') {
        this.errorText = this.Localization.validation.fNameNull
        return
      }
      if (this.lName === '') {
        this.errorText = this.Localization.validation.lNameNull
        return
      }
      if (this.dOfBirth === '') {
        this.errorText = this.Localization.validation.dobNull
        return
      }
      if (this.password === '') {
        this.errorText = this.Localization.validation.passwordNull
        return
      }
      if (this.password !== this.passwordRepeat) {
        this.errorText = this.Localization.validation.passwordNotRepeat
        return
      }
      this.buttonDisabled = true
      this.registerStage = 'awaitRegister'
      const iv = await Security.getRandom(16)
      const salt = await Security.getRandom(16)

      const pbkdf2Key = await Encryption.deriveKey(this.password, salt)
      const passwordHash = await Hashing.digest(Security.ab2str(salt) + '' + this.password)
      const rsaKey = await Encryption.generateRSA()
      const basePublic = await Encryption.exportPublicKey(rsaKey.publicKey)
      const basePrivate = await Encryption.exportPrivateKey(rsaKey.privateKey)

      const encryptedPrivateKey = await Encryption.encryptAESBytes(basePrivate, pbkdf2Key, iv)
      console.log(
        this.fName +
          '-' +
          this.lName +
          '-' +
          this.dOfBirth +
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
        await Encryption.encryptAES(this.fName, pbkdf2Key, ivString),
        await Encryption.encryptAES(this.lName, pbkdf2Key, ivString),
        await Encryption.encryptAES(this.dOfBirth, pbkdf2Key, ivString),
        await Hashing.digest(
          this.fName +
            '-' +
            this.lName +
            '-' +
            this.dOfBirth +
            '-' +
            window.btoa(Security.ab2str(salt))
        )
      )
        .then(response => {
          this.buttonDisabled = false
          if (response.success) {
            this.registerStage = 'success'
            this.newClientUUID = response.data.uuid
          } else {
            this.registerStage = 'enterData'
            this.errorText = response.text
          }
        })
        .catch(() => {
          this.buttonDisabled = false
          this.registerStage = 'enterData'
          this.errorText = 'Ошибка запроса'
        })

      console.log('iv - ' + window.btoa(Security.ab2str(iv)))
      console.log('salt - ' + window.btoa(Security.ab2str(salt)))
      console.log('passwordHash - ' + passwordHash)
      console.log('publicKey - ' + basePublic)
      console.log('privateKey - ' + encryptedPrivateKey)
    }
  }
})
</script>

<style scoped></style>
