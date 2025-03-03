<template>
  <h4>Предварительная верификация</h4>
  <label for="selectCountry">Гражданство:</label>
  <select name="" id="selectCountry" v-model="selectCountry" class="form-select">
    <option value="rus">Россия</option>
    <option value="bel">Беларусь</option>
    <option value="ukr">Украина</option>
    <option value="oth">Другое</option>
  </select>
  <label for="lName">Фамилия (на латинице):</label>
  <input type="text" class="form-control" v-model="lName" id="lName" />
  <label for="fName">Имя (на латинице):</label>
  <input type="text" class="form-control" v-model="fName" id="fName" />
  <label for="dOfBirth">Дата рождения:</label>
  <input type="date" class="form-control" v-model="dOfBirth" id="dOfBirth" />
  <label for="selectDocumentType">Документ:</label>
  <select name="" id="selectDocumentType" v-model="selectDocumentType" class="form-select">
    <option value="passport">Паспорт</option>
    <option value="drivingLicense">Водительское удостоверение</option>
  </select>
  <div class="mb-3">
    <label for="formFile" class="form-label">Фотография документа</label>
    <input class="form-control" type="file" id="formFile" />
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import dayjs from 'dayjs'
export default defineComponent({
  name: 'IdVerification',
  data() {
    return {
      lName: '' as string,
      fName: '' as string,
      dOfBirth: dayjs().format('YYYY-MM-DD') as string,
      selectDocumentType: 'passport' as string,
      selectCountry: 'rus' as string
    }
  }
})
</script>

<style scoped></style>
