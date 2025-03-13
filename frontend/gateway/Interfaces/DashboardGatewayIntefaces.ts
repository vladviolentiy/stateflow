import type { CountryCode } from '@/localization/LocalizationInterface.ts'

interface checkAuthResponse {
  userId: number
  lang: CountryCode
  ip: string
  ua: string
  acceptEncoding: string
  acceptLang: string
}

interface emailListResponseItem {
  id: number
  email: string
}

interface phoneListResponseItem {
  id: number
  phone: string
}

interface sessionListResponseItem {
  authHash: string
  createdAt: string
  uas: string[]
  ips: string[]
}

interface editItemGlobal {
  allowAuth: boolean
  csrf: string
}

interface emailEditItem extends editItemGlobal {
  emailEncrypted: string
}
interface phoneEditItem extends editItemGlobal {
  phoneEncrypted: string
}

export type {
  checkAuthResponse,
  sessionListResponseItem,
  emailListResponseItem,
  emailEditItem,
  phoneListResponseItem,
  phoneEditItem
}
