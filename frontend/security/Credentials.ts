class Credentials {
  public async create(
    randomString: Uint8Array,
    userId: string,
    name: string,
    displayName: string
  ): Promise<Credential> {
    const cred = await navigator.credentials.create({
      publicKey: {
        challenge: randomString,
        rp: {
          name: 'StateFlow'
        },
        user: {
          id: Uint8Array.from(userId, c => c.charCodeAt(0)),
          name: name,
          displayName: displayName
        },
        pubKeyCredParams: [
          { alg: -7, type: 'public-key' },
          { type: 'public-key', alg: -257 }
        ],
        authenticatorSelection: {
          authenticatorAttachment: 'cross-platform'
        },
        timeout: 60000,
        attestation: 'direct'
      }
    })
    if (cred === null) throw 'Credential Error'
    return cred
  }

  public async get(randomString: string): Promise<Credential> {
    const cred = await navigator.credentials.get({
      publicKey: {
        challenge: Uint8Array.from(randomString, c => c.charCodeAt(0))
      }
    })
    if (cred === null) throw 'Credential Error'
    return cred
  }
}

export default new Credentials()
