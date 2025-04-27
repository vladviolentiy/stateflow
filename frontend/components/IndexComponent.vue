<template>
  <h4>{{ store.Localization.welcome }}</h4>
</template>

<script setup lang="ts">
import { appStore } from '@/stores/AppStore'

const store = appStore()
</script>

<style scoped></style>
