interface checkAuth {
  type: 'password' | 'finger'
  iv: string
  salt: string
}

interface randomString {
  random: string
}

interface successRegister {
  uuid: string
}

interface successAuth {
  hash: string
  iv: string
  salt: string
}

export type { checkAuth, randomString, successRegister, successAuth }
