<template>
  <h4>{{ store.Localization.welcome }}</h4>
</template>

<script setup lang="ts">
import {appStore} from "@/stores/AppStore.ts";

const store = appStore()

</script>

<style scoped></style>
