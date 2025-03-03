import Security from './Security'

class Encryption {
  public async generateRSA(): Promise<CryptoKeyPair> {
    return await window.crypto.subtle.generateKey(
      {
        name: 'RSA-OAEP',
        modulusLength: 4096,
        publicExponent: new Uint8Array([1, 0, 1]),
        hash: 'SHA-256'
      },
      true,
      ['encrypt', 'decrypt']
    )
  }

  public async generateAESKey(): Promise<CryptoKey> {
    return await window.crypto.subtle.generateKey(
      {
        name: 'AES-CBC',
        length: 256
      },
      true,
      ['encrypt', 'decrypt']
    )
  }

  public async deriveKey(password: string, salt: ArrayBuffer): Promise<CryptoKey> {
    const importedKey = await window.crypto.subtle.importKey(
      'raw',
      Security.str2ab(password),
      'PBKDF2',
      false,
      ['deriveBits', 'deriveKey']
    )
    return await window.crypto.subtle.deriveKey(
      {
        name: 'PBKDF2',
        salt,
        iterations: 100000,
        hash: 'SHA-256'
      },
      importedKey,
      { name: 'AES-CBC', length: 256 },
      true,
      ['encrypt', 'decrypt']
    )
  }

  public async encryptAES(data: string, key: CryptoKey, iv: string): Promise<string> {
    return window.btoa(
      Security.ab2str(
        await this.encryptAESBytes(
          Security.utf8str2ab(data),
          key,
          new Uint8Array(Security.str2ab(window.atob(iv)))
        )
      )
    )
  }

  public async decryptAES(data: string, key: CryptoKey, iv: string): Promise<string> {
    const decodedBytes = await this.decryptAESBytes(
      Security.str2ab(window.atob(data)),
      key,
      new Uint8Array(Security.str2ab(window.atob(iv)))
    )
    return Security.ab2utf8str(decodedBytes)
  }

  public async encryptAESBytes(
    data: ArrayBuffer,
    encryptionKey: CryptoKey,
    iv: Uint8Array
  ): Promise<ArrayBuffer> {
    return await window.crypto.subtle.encrypt(
      {
        name: 'AES-CBC',
        iv: iv
      },
      encryptionKey,
      data
    )
  }

  public async decryptAESBytes(
    data: ArrayBuffer,
    encryptionKey: CryptoKey,
    iv: Uint8Array
  ): Promise<ArrayBuffer> {
    return await window.crypto.subtle.decrypt(
      {
        name: 'AES-CBC',
        iv: iv
      },
      encryptionKey,
      data
    )
  }

  public async exportPublicKey(publicKey: CryptoKey): Promise<string> {
    const exported = await window.crypto.subtle.exportKey('spki', publicKey)
    const exportedAsString = Security.ab2str(exported)
    return window.btoa(exportedAsString)
  }

  public async exportPrivateKey(privateKey: CryptoKey): Promise<ArrayBuffer> {
      return await window.crypto.subtle.exportKey('pkcs8', privateKey)
  }

  public async importPublicKey(publicKey: string): Promise<CryptoKey> {
      return await window.crypto.subtle.importKey(
        'spki',
        Security.str2ab(window.atob(publicKey)),
        {
            name: 'RSA-OAEP',
            hash: 'SHA-256'
        },
        true,
        ['encrypt']
    )
  }
}

export default new Encryption()
