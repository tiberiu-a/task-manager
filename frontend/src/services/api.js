const api = 'http://localhost:8000'

export async function request(path, options = {}) {
  if (options.body !== undefined) {
    options = {
      ...options,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(options.body),
    }
  }

  const response = await fetch(api + path, options)

  if (!response.ok) {
    throw new Error(`HTTP error! Status: ${response.status}`)
  }

  if (response.status === 204) {
    return null
  }

  return response.json()
}
