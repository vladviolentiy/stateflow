import Encryption from '@/security/Encryption'

class Security {
  private textEncoder: TextEncoder
  private textDecoder: TextDecoder
  constructor() {
    this.textEncoder = new TextEncoder()
    this.textDecoder = new TextDecoder()
  }

  public ab2utf8str(buf: ArrayBuffer): string {
    return this.textDecoder.decode(new Uint8Array(buf))
  }
  public utf8str2ab(string: string): ArrayBuffer {
    return this.textEncoder.encode(string)
  }

  public ab2str(buf: ArrayBuffer): string {
    return String.fromCharCode(...new Uint8Array(buf))
  }

  public str2ab(string: string): ArrayBuffer {
    return Uint8Array.from(string, c => c.charCodeAt(0))
  }

  public buf2hex(buffer: ArrayBuffer) {
    // buffer is an ArrayBuffer
    let s = ''
    const h = '0123456789abcdef'
    new Uint8Array(buffer).forEach(v => {
      s += h[v >> 4] + h[v & 15]
    })
    return s
  }

  public getRandom(length: number): Uint8Array {
    return window.crypto.getRandomValues(new Uint8Array(length))
  }

  public async getDerivedKey(): Promise<CryptoKey> {
    return await Encryption.deriveKey(
      localStorage.getItem('password') ?? '',
      this.str2ab(window.atob(localStorage.getItem('salt') ?? ''))
    )
  }
}

export default new Security()
