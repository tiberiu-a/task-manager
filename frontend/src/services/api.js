const api = import.meta.env.VITE_API_URL

class HTTPError extends Error {
  constructor(status, message) {
    super(message)
    this.status = status
  }
}

export async function request(path, options = {}) {
  if (options.body !== undefined) {
    options = {
      ...options,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(options.body),
    }
  }
  options = {
    ...options,
    credentials: 'include',
  }

  const response = await fetch(api + path, options)

  if (!response.ok) {
    throw new HTTPError(response.status, `HTTP error! Status: ${response.status}`)
  }

  if (response.status === 204) {
    return null
  }

  return response.json()
}
