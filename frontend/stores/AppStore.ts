import { defineStore } from 'pinia'
import type { LocalizationInterface } from '../localization/LocalizationInterface'
import LocalizationRu from '../localization/LocalizationRu'
import LocalizationUa from '../localization/LocalizationUa'
import LocalizationBy from '../localization/LocalizationBy'
import DashboardGateway from '@/gateway/DashboardGateway'
import LocalizationEn from '@/localization/LocalizationEn'

export const appStore = defineStore('App', {
  state: () => {
    return {
      Localization: LocalizationRu as LocalizationInterface,
      CurrentLocalization: 'ru' as 'ru' | 'ua' | 'by' | 'en',
      DashboardGateway: new DashboardGateway(
        localStorage.getItem('authToken') ?? ''
      ) as DashboardGateway
    }
  },
  actions: {
    setNewLang(lang: 'ru' | 'ua' | 'by' | 'en'): void {
      this.CurrentLocalization = lang
      switch (lang) {
        case 'ru':
          this.Localization = LocalizationRu
          break
        case 'en':
          this.Localization = LocalizationEn
          break
        case 'by':
          this.Localization = LocalizationBy
          break
        case 'ua':
          this.Localization = LocalizationUa
          break
      }
    },
    setNewToken(token: string): void {
      this.DashboardGateway = new DashboardGateway(token)
    }
  }
})
