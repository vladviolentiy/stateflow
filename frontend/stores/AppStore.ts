import { defineStore } from 'pinia'
import type { CountryCode, LocalizationInterface } from '../localization/LocalizationInterface'
import LocalizationRu from '../localization/LocalizationRu'
import DashboardGateway from '@/gateway/DashboardGateway'

export const appStore = defineStore('App', {
  state: () => {
    return {
      Localization: LocalizationRu as LocalizationInterface,
      CurrentLocalization: 'ru' as CountryCode,
      DashboardGateway: new DashboardGateway(
        localStorage.getItem('authToken') ?? ''
      ) as DashboardGateway
    }
  },
  actions: {
    async setNewLang(lang: CountryCode): Promise<void> {
      this.CurrentLocalization = lang
      switch (lang) {
        case 'ru':
          this.Localization = LocalizationRu
          break
        case 'en':
          this.Localization = (await import('../localization/LocalizationEn')).default
          break
        case 'by':
          this.Localization = (await import('../localization/LocalizationBy')).default
          break
        case 'ua':
          this.Localization = (await import('../localization/LocalizationUa')).default
          break
      }
    },
    setNewToken(token: string): void {
      this.DashboardGateway = new DashboardGateway(token)
    }
  }
})
