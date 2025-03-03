import Security from '@/security/Security'

class Hashing {
  public async digest(value: string): Promise<string> {
    const data = Security.utf8str2ab(value)
    const hash = await window.crypto.subtle.digest('SHA-384', data)
    return Security.buf2hex(hash)
  }
}

export default new Hashing()
