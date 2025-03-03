class Mathematics {
  private freqTable(data: string): Record<string, number> {
    const frequency: Record<string, number> = {}

    // Count the frequency of each character in the data
    for (let i = 0; i < data.length; i++) {
      const character = data[i]
      if (frequency[character]) {
        frequency[character]++
      } else {
        frequency[character] = 1
      }
    }
    return frequency
  }
  public entropy(data: string): number {
    let entropy = 0
    const frequency: Record<string, number> = this.freqTable(data)

    // Calculate the entropy
    for (const char in frequency) {
      const probability = frequency[char] / data.length
      entropy -= probability * Math.log(probability)
    }

    return entropy
  }
  public entropyLog2(data: string): number {
    let entropy = 0
    const frequency: Record<string, number> = this.freqTable(data)

    // Calculate the entropy
    for (const char in frequency) {
      const probability = frequency[char] / data.length
      entropy -= probability * Math.log2(probability)
    }

    return entropy
  }
  public entropyLog10(data: string): number {
    let entropy = 0
    const frequency: Record<string, number> = this.freqTable(data)

    // Calculate the entropy
    for (const char in frequency) {
      const probability = frequency[char] / data.length
      entropy -= probability * Math.log10(probability)
    }

    return entropy
  }
}

export default new Mathematics()
