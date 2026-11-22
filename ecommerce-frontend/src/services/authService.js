import api from '../api/axios'

export const authService = {
  // Enregistre un nouvel utilisateur et stocke le token et ses infos s'il réussit
  register: async (data) => {
    const response = await api.post('/auth/register', data)
    if (response.data.token) {
      localStorage.setItem('authToken', response.data.token)
      localStorage.setItem('user', JSON.stringify(response.data.user))
    }
    return response.data
  },

  // Connecte l'utilisateur avec email/password et sauvegarde sa session localement
  login: async (email, password) => {
    const response = await api.post('/auth/login', { email, password })
    if (response.data.token) {
      localStorage.setItem('authToken', response.data.token)
      localStorage.setItem('user', JSON.stringify(response.data.user))
    }
    return response.data
  },

  // Déconnecte l'utilisateur côté serveur et supprime ses données du localStorage
  logout: async () => {
    await api.post('/auth/logout')
    localStorage.removeItem('authToken')
    localStorage.removeItem('user')
  },


  // Récupère les données du profil de l'utilisateur connecté depuis le serveur
  getProfile: async () => {
    const response = await api.get('/auth/profile')
    return response.data
  },

  // Met à jour le profil sur le serveur et actualise les infos de l'utilisateur stockées localement
  updateProfile: async (data) => {
    const response = await api.put('/auth/profile', data)
    localStorage.setItem('user', JSON.stringify(response.data.user))
    return response.data
  },

  // Récupère et convertit en objet les infos de l'utilisateur actuellement stockées localement
  getCurrentUser: () => {
    const user = localStorage.getItem('user')
    return user ? JSON.parse(user) : null
  },
// Vérifie si l'utilisateur est connecté en inspectant la présence du token de session
  isAuthenticated: () => !!localStorage.getItem('authToken'),
}
